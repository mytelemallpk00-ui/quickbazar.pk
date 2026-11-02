<?php

namespace App\Services\Courier;

use App\Models\Order;

class TcsCourierClient extends AbstractCourierClient
{
    public function platform(): string
    {
        return 'tcs';
    }

    protected function ratePayload(string $destinationCity, float $weightKg, float $codAmount): array
    {
        return [
            'client_id' => $this->credential('client_id'),
            'api_key' => $this->credential('api_key'),
            'origin' => $this->originCity(),
            'destination' => $destinationCity,
            'weight' => max(0.1, $weightKg),
            'cod_amount' => $codAmount,
        ];
    }

    protected function bookingPayload(Order $order): array
    {
        return [
            'client_id' => $this->credential('client_id'),
            'api_key' => $this->credential('api_key'),
            'account_no' => $this->credential('account_no'),
            'consignee_name' => $order->customerName(),
            'consignee_phone' => $order->customer_phone,
            'consignee_address' => $order->shipping_address_line,
            'consignee_city' => $order->shipping_city,
            'origin' => $this->originCity(),
            'weight' => config('courier.default_weight_kg'),
            'cod_amount' => $order->payment_method && str_contains(strtolower($order->payment_method), 'cash') ? $order->total : 0,
            'reference' => $order->order_number,
        ];
    }

    protected function parseRateResponse(?array $json): ?array
    {
        if (! is_array($json)) {
            return null;
        }

        $price = $json['rate'] ?? $json['price'] ?? $json['shipping_charges'] ?? null;
        if (! is_numeric($price)) {
            return null;
        }

        $eta = (string) ($json['eta'] ?? $json['delivery_days'] ?? '2–4 business days');

        return ['price' => (float) $price, 'eta' => $eta];
    }

    protected function parseBookingResponse(?array $json): array
    {
        if (! is_array($json)) {
            return ['success' => false, 'tracking_number' => null, 'booking_ref' => null, 'error' => 'Empty response'];
        }

        $tracking = $json['tracking_number'] ?? $json['consignment_no'] ?? $json['cn'] ?? null;
        $ref = $json['booking_id'] ?? $json['reference'] ?? null;

        if (! filled($tracking)) {
            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => is_string($ref) ? $ref : null,
                'error' => (string) ($json['message'] ?? $json['error'] ?? 'No tracking number returned'),
            ];
        }

        return [
            'success' => true,
            'tracking_number' => (string) $tracking,
            'booking_ref' => is_string($ref) ? $ref : null,
            'error' => null,
        ];
    }
}
