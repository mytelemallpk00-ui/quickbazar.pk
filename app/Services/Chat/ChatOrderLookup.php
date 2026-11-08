<?php

namespace App\Services\Chat;

use App\Models\Order;
use App\Services\Courier\CourierTrackingService;
use Illuminate\Support\Str;

class ChatOrderLookup
{
    public function __construct(
        protected CourierTrackingService $courierTracking,
    ) {}

    public function extractReference(string $message): ?string
    {
        $trimmed = trim($message);
        if ($trimmed === '') {
            return null;
        }

        if (preg_match('/\b(BTG[\s-]*\d{4,8})\b/i', $trimmed, $m)) {
            return $this->normalizeOrderNumber($m[1]);
        }

        if (preg_match('/(?:order|tracking|track|parcel|cn)\s*(?:no|number|#)?\s*[:#-]?\s*([A-Z0-9-]{4,32})/i', $trimmed, $m)) {
            return $this->normalizeReference(trim($m[1]));
        }

        if (preg_match('/^[A-Z0-9-]{4,32}$/i', $trimmed)) {
            return $this->normalizeReference($trimmed);
        }

        return null;
    }

    public function findByReference(string $reference): ?Order
    {
        $reference = $this->normalizeReference($reference);
        $orderNumber = $this->normalizeOrderNumber($reference);

        return Order::query()
            ->with(['items'])
            ->where(function ($builder) use ($reference, $orderNumber): void {
                $builder->where('order_number', $orderNumber)
                    ->orWhere('order_number', $reference)
                    ->orWhere('tracking_number', $reference)
                    ->orWhere('courier_booking_ref', $reference);

                if (ctype_digit($reference)) {
                    $builder->orWhere('tracking_number', 'like', '%'.$reference.'%');
                }
            })
            ->latest('id')
            ->first();
    }

    public function formatStatusBlock(Order $order): string
    {
        $tz = config('app.timezone');
        $labels = Order::statusLabels();
        $payLabels = Order::paymentStatusLabels();
        $fulfillLabels = Order::fulfillmentStatusLabels();

        $statusKey = (string) $order->status;
        $paymentKey = (string) $order->payment_status;
        $fulfillKey = (string) $order->fulfillment_status;

        $lines = [
            'Shop Pakistan order (database):',
            '',
            'Order #: '.($order->order_number ?? '—'),
            'Placed: '.($order->created_at?->timezone($tz)->format('d M Y, h:i A') ?? '—'),
            'Order status: '.($labels[$statusKey] ?? $statusKey ?: '—'),
            'Payment: '.($payLabels[$paymentKey] ?? $paymentKey ?: '—'),
        ];

        if (filled($order->payment_method)) {
            $lines[] = 'Payment method: '.$order->payment_method;
        }

        $lines[] = 'Dispatch: '.($fulfillLabels[$fulfillKey] ?? $fulfillKey ?: '—');

        if ($order->fulfilled_at) {
            $lines[] = 'Shipped on: '.$order->fulfilled_at->timezone($tz)->format('d M Y, h:i A');
        }

        if ($order->delivered_at) {
            $lines[] = 'Delivered on: '.$order->delivered_at->timezone($tz)->format('d M Y, h:i A');
        } elseif ($order->cancelled_at) {
            $lines[] = 'Cancelled on: '.$order->cancelled_at->timezone($tz)->format('d M Y, h:i A');
        } elseif ($order->returned_at) {
            $lines[] = 'Returned on: '.$order->returned_at->timezone($tz)->format('d M Y, h:i A');
        }

        if (filled($order->courier_label) || filled($order->courier_code)) {
            $lines[] = 'Courier: '.trim($order->courier_label ?: strtoupper((string) $order->courier_code));
        }

        if (filled($order->tracking_number)) {
            $lines[] = 'Tracking #: '.$order->tracking_number;
        }

        if (filled($order->courier_booking_status)) {
            $lines[] = 'Courier booking: '.$order->courier_booking_status;
        }

        if (filled($order->courier_booking_ref)) {
            $lines[] = 'Booking ref: '.$order->courier_booking_ref;
        }

        if (filled($order->shipping_city)) {
            $lines[] = 'Ship to city: '.$order->shipping_city;
        }

        if (filled($order->courier_error)) {
            $lines[] = 'Courier note: '.$order->courier_error;
        }

        $lines[] = '';
        $lines[] = $this->summarizeItems($order);
        $lines[] = '';
        $lines[] = $this->summarizeDeliveryPhase($order);

        $courierLine = $this->courierTracking->formatForChat($order);
        if ($courierLine !== null) {
            $lines[] = '';
            $lines[] = $courierLine;
        }

        $lines[] = '';
        $lines[] = 'Source: Shop Pakistan orders database (updated when your order is processed).';

        return implode("\n", $lines);
    }

    protected function summarizeItems(Order $order): string
    {
        $items = $order->items;
        if ($items->isEmpty()) {
            return 'Items: (no line items in database)';
        }

        $rows = ['Items in this order:'];
        foreach ($items as $item) {
            $qty = (int) $item->quantity;
            $name = trim((string) $item->name);
            $rows[] = '• '.$qty.' × '.$name;
        }

        return implode("\n", $rows);
    }

    protected function summarizeDeliveryPhase(Order $order): string
    {
        if ($order->status === Order::STATUS_CANCELLED) {
            return 'Delivery: Cancelled — parcel dispatch nahi hua.';
        }

        if ($order->delivered_at) {
            return 'Delivery: Delivered (record database mein mark hai).';
        }

        if ($order->fulfillment_status === Order::FULFILLMENT_FULFILLED && filled($order->tracking_number)) {
            return 'Delivery: Shipped — courier ke paas hai. Tracking # upar hai.';
        }

        if ($order->fulfillment_status === Order::FULFILLMENT_FULFILLED) {
            return 'Delivery: Dispatched — tracking number abhi database mein nahi.';
        }

        return 'Delivery: Processing — abhi ship / assign ho raha hai.';
    }

    protected function normalizeReference(string $value): string
    {
        return strtoupper(trim($value));
    }

    protected function normalizeOrderNumber(string $value): string
    {
        $v = strtoupper(preg_replace('/\s+/', '', trim($value)) ?? trim($value));
        if (preg_match('/^BTG(\d+)$/', $v, $m)) {
            return 'BTG-'.$m[1];
        }

        return $v;
    }
}
