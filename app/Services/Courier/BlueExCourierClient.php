<?php

namespace App\Services\Courier;

use App\Models\Order;

class BlueExCourierClient extends AbstractCourierClient
{
    public function platform(): string
    {
        return 'blueex';
    }

    protected function ratePayload(string $destinationCity, float $weightKg, float $codAmount): array
    {
        return [
            'username' => $this->credential('username'),
            'password' => $this->credential('password'),
            'account_code' => $this->credential('account_code'),
            'origin' => $this->originCity(),
            'destination' => $destinationCity,
            'weight' => max(0.1, $weightKg),
            'cod' => $codAmount,
        ];
    }

    protected function bookingPayload(Order $order): array
    {
        return [
            'username' => $this->credential('username'),
            'password' => $this->credential('password'),
            'account_code' => $this->credential('account_code'),
            'recipient_name' => $order->customerName(),
            'recipient_phone' => $order->customer_phone,
            'recipient_address' => $order->shipping_address_line,
            'recipient_city' => $order->shipping_city,
            'origin' => $this->originCity(),
            'weight' => config('courier.default_weight_kg'),
            'cod' => str_contains(strtolower((string) $order->payment_method), 'cash') ? $order->total : 0,
            'customer_reference' => $order->order_number,
        ];
    }

    protected function parseRateResponse(?array $json): ?array
    {
        if (! is_array($json)) {
            return null;
        }

        $price = $json['total_charges'] ?? $json['rate'] ?? $json['amount'] ?? null;
        if (! is_numeric($price)) {
            return null;
        }

        return [
            'price' => (float) $price,
            'eta' => (string) ($json['service_days'] ?? '2–4 business days'),
        ];
    }

    protected function parseBookingResponse(?array $json): array
    {
        if (! is_array($json)) {
            return ['success' => false, 'tracking_number' => null, 'booking_ref' => null, 'error' => 'Empty response'];
        }

        $tracking = $json['awb'] ?? $json['tracking_no'] ?? $json['consignment_number'] ?? null;

        if (! filled($tracking)) {
            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => null,
                'error' => (string) ($json['status_message'] ?? 'No tracking number returned'),
            ];
        }

        return [
            'success' => true,
            'tracking_number' => (string) $tracking,
            'booking_ref' => isset($json['booking_id']) ? (string) $json['booking_id'] : null,
            'error' => null,
        ];
    }
}
