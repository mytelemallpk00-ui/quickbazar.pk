<?php

namespace App\Http\Controllers;

use App\Http\Requests\ShippingRatesRequest;
use App\Services\Courier\CourierRateService;
use App\Support\CheckoutSettings;
use App\Support\PromoCodes;
use Illuminate\Http\JsonResponse;

class CheckoutShippingController extends Controller
{
    public function __invoke(ShippingRatesRequest $request, CourierRateService $rates): JsonResponse
    {
        $data = $request->validated();
        $items = $data['items'] ?? [];
        $subtotal = collect($items)->sum(fn (array $item): float => (float) $item['price'] * (int) $item['qty']);

        $promo = PromoCodes::resolve($data['promo_code'] ?? null);
        $discountPercent = $promo['percent'] ?? (float) ($data['discount_percent'] ?? 0);
        $discounted = max(0, $subtotal - round($subtotal * ($discountPercent / 100), 2));

        $weight = $rates->estimateWeightKg(collect($items)->sum('qty'));
        $city = (string) $data['city'];

        $methods = $rates->shippingMethodsFor($city, $discounted, $weight);

        return response()->json([
            'success' => true,
            'shipping_methods' => $methods,
            'free_shipping_threshold' => (float) CheckoutSettings::all()['free_shipping_threshold'],
            'discounted_subtotal' => $discounted,
        ]);
    }
}
