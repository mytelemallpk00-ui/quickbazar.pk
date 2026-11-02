<?php

namespace App\Services\Courier;

use App\Models\Order;

class LeopardsCourierClient extends AbstractCourierClient
{
    public function platform(): string
    {
        return 'leopards';
    }

    protected function ratePayload(string $destinationCity, float $weightKg, float $codAmount): array
    {
        return [
            'api_key' => $this->credential('api_key'),
            'api_password' => $this->credential('api_password'),
            'origin_city' => $this->originCity(),
            'destination_city' => $destinationCity,
            'weight' => max(0.1, $weightKg),
            'cod_amount' => $codAmount,
        ];
    }

    protected function bookingPayload(Order $order): array
    {
        return [
            'api_key' => $this->credential('api_key'),
            'api_password' => $this->credential('api_password'),
            'shipper_id' => $this->credential('shipper_id'),
            'consignee_name' => $order->customerName(),
            'consignee_phone' => $order->customer_phone,
            'consignee_address' => $order->shipping_address_line,
            'consignee_city' => $order->shipping_city,
            'origin_city' => $this->originCity(),
            'weight' => config('courier.default_weight_kg'),
            'cod_amount' => str_contains(strtolower((string) $order->payment_method), 'cash') ? $order->total : 0,
            'order_id' => $order->order_number,
        ];
    }

    protected function parseRateResponse(?array $json): ?array
    {
        if (! is_array($json)) {
            return null;
        }

        $data = is_array($json['data'] ?? null) ? $json['data'] : $json;
        $price = $data['rate'] ?? $data['charges'] ?? $data['net_amount'] ?? null;
        if (! is_numeric($price)) {
            return null;
        }

        return [
            'price' => (float) $price,
            'eta' => (string) ($data['eta'] ?? '1–3 business days'),
        ];
    }

    protected function parseBookingResponse(?array $json): array
    {
        if (! is_array($json)) {
            return ['success' => false, 'tracking_number' => null, 'booking_ref' => null, 'error' => 'Empty response'];
        }

        $data = is_array($json['data'] ?? null) ? $json['data'] : $json;
        $tracking = $data['track_number'] ?? $data['tracking_number'] ?? $data['cn_number'] ?? null;

        if (! filled($tracking)) {
            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => null,
                'error' => (string) ($json['message'] ?? 'No tracking number returned'),
            ];
        }

        return [
            'success' => true,
            'tracking_number' => (string) $tracking,
            'booking_ref' => isset($data['packet_id']) ? (string) $data['packet_id'] : null,
            'error' => null,
        ];
    }
}
