<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Jobs\BookCourierShipmentJob;
use App\Services\Meta\MetaConversionsApi;
use App\Models\Order;
use App\Models\Product;
use App\Services\Courier\CourierRateService;
use App\Services\StoreAutomation\OrderAutomationPlanner;
use App\Support\CheckoutSettings;
use App\Support\PromoCodes;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class OrderController extends Controller
{
    public function store(StoreOrderRequest $request, CourierRateService $courierRates): JsonResponse
    {
        $data = $request->validated();
        $items = $data['items'];
        $settings = CheckoutSettings::all();
        $shippingMethodId = (string) ($data['shipping_method_id'] ?? 'standard');

        $subtotal = collect($items)->sum(fn (array $item): float => (float) $item['price'] * (int) $item['qty']);

        $promo = PromoCodes::resolve($data['promo_code'] ?? null);
        $discountPercent = $promo['percent'] ?? (float) ($data['discount_percent'] ?? 0);
        if ($promo === null && $discountPercent > 0 && ! filled($data['promo_code'] ?? null)) {
            $discountPercent = 0;
        }

        $discountTotal = round($subtotal * ($discountPercent / 100), 2);
        $discounted = max(0, $subtotal - $discountTotal);

        $city = (string) $data['shipping']['city'];
        $weight = $courierRates->estimateWeightKg(collect($items)->sum('qty'));
        $shippingTotal = $courierRates->priceForMethod($shippingMethodId, $city, $discounted, $weight);
        $courierMeta = $courierRates->courierMetaForMethod($shippingMethodId);

        $taxRate = (float) ($settings['tax_rate'] ?? 0);
        $taxTotal = round($discounted * $taxRate, 2);
        $grandTotal = round($discounted + $taxTotal + $shippingTotal, 2);
        $currency = (string) ($settings['currency'] ?? 'PKR');

        $userId = Auth::id();

        $order = DB::transaction(function () use (
            $data,
            $items,
            $subtotal,
            $discountTotal,
            $taxTotal,
            $shippingTotal,
            $grandTotal,
            $currency,
            $shippingMethodId,
            $courierMeta,
            $userId,
            $promo,
        ): Order {
            $order = Order::query()->create([
                'user_id' => $userId,
                'order_number' => Order::generateOrderNumber(),
                'customer_email' => $data['customer']['email'],
                'customer_phone' => $data['customer']['phone'] ?? null,
                'customer_first_name' => $data['customer']['first_name'],
                'customer_last_name' => $data['customer']['last_name'] ?? null,
                'shipping_address_line' => $data['shipping']['address'],
                'shipping_city' => $data['shipping']['city'],
                'shipping_postal' => $data['shipping']['postal'] ?? null,
                'shipping_country' => $data['shipping']['country'] ?? 'PK',
                'shipping_method_id' => $shippingMethodId,
                'courier_code' => $courierMeta['courier_code'],
                'courier_label' => $courierMeta['courier_label'],
                'courier_booking_status' => $courierMeta['courier_code'] ? 'pending' : 'skipped',
                'subtotal' => $subtotal,
                'discount_total' => $discountTotal,
                'tax_total' => $taxTotal,
                'shipping_total' => $shippingTotal,
                'total' => $grandTotal,
                'currency' => $currency,
                'payment_method' => $data['payment_method'],
                'payment_status' => Order::PAYMENT_PENDING,
                'fulfillment_status' => Order::FULFILLMENT_UNFULFILLED,
                'status' => Order::STATUS_OPEN,
                'promo_code' => $promo['label'] ?? ($data['promo_code'] ?? null),
            ]);

            foreach ($items as $item) {
                $productId = isset($item['id']) ? (int) $item['id'] : null;
                $sku = null;
                if ($productId) {
                    $sku = Product::query()->whereKey($productId)->value('external_sku')
                        ?? Product::query()->whereKey($productId)->value('slug');
                }

                $qty = (int) $item['qty'];
                $unit = (float) $item['price'];

                $order->items()->create([
                    'product_id' => $productId ?: null,
                    'name' => $item['name'],
                    'sku' => $sku,
                    'image' => $item['image'] ?? null,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => round($unit * $qty, 2),
                ]);

                if ($productId && Schema::hasColumn('products', 'stock_quantity')) {
                    Product::query()
                        ->whereKey($productId)
                        ->whereNotNull('stock_quantity')
                        ->decrement('stock_quantity', $qty);
                }
            }

            return $order->load('items');
        });

        if ($order->courier_code) {
            BookCourierShipmentJob::dispatchSync($order);
            $order->refresh();
        }

        app(MetaConversionsApi::class)->sendPurchase(
            $order,
            $request->ip(),
            (string) $request->userAgent(),
        );

        app(OrderAutomationPlanner::class)->afterOrderPlaced($order);

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'total' => $order->total,
            'tracking_number' => $order->tracking_number,
            'account_url' => Auth::check() ? route('account.index') : route('register'),
        ]);
    }
}
