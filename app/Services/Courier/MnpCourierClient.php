<?php

namespace App\Services\Courier;

use App\Models\Order;

class MnpCourierClient extends AbstractCourierClient
{
    public function platform(): string
    {
        return 'mnp';
    }

    protected function ratePayload(string $destinationCity, float $weightKg, float $codAmount): array
    {
        return [
            'username' => $this->credential('username'),
            'password' => $this->credential('password'),
            'destinationCityName' => $destinationCity,
            'weight' => max(0.5, $weightKg),
            'codAmount' => $codAmount,
        ];
    }

    protected function bookingPayload(Order $order): array
    {
        $isCod = str_contains(strtolower((string) $order->payment_method), 'cash');
        $weight = max(0.5, (float) config('courier.default_weight_kg', 0.5));
        $productSummary = $order->items->pluck('name')->take(3)->implode(', ');
        if ($productSummary === '') {
            $productSummary = 'Beautage order';
        }

        return [
            'username' => $this->credential('username'),
            'password' => $this->credential('password'),
            'AccountNo' => $this->credential('account_no'),
            'locationID' => (string) $this->credential('location_id'),
            'InsertType' => (int) ($this->credential('insert_type') ?: config('courier.platforms.mnp.default_insert_type', 19)),
            'ReturnLocation' => (int) ($this->credential('return_location') ?: $this->credential('location_id') ?: 0),
            'subAccountId' => (int) ($this->credential('sub_account_id') ?: 0),
            'consigneeName' => $order->customerName() ?: 'Customer',
            'consigneeAddress' => $order->shipping_address_line,
            'consigneeMobNo' => preg_replace('/\D+/', '', (string) $order->customer_phone) ?: '03000000000',
            'consigneeEmail' => $order->customer_email ?: 'orders@beautage.pk',
            'destinationCityName' => strtolower(trim((string) $order->shipping_city)),
            'pieces' => max(1, (int) $order->items->sum('quantity')),
            'weight' => $weight,
            'codAmount' => $isCod ? (float) $order->total : 0,
            'custRefNo' => substr((string) $order->order_number, 0, 50),
            'productDetails' => substr($productSummary, 0, 50),
            'fragile' => 'NO',
            'service' => $this->credential('service') ?: 'Overnight',
            'remarks' => 'Beautage storefront booking',
            'insuranceValue' => '0',
        ];
    }

    protected function parseRateResponse(?array $json): ?array
    {
        if (! is_array($json)) {
            return null;
        }

        $price = $json['rate'] ?? $json['charges'] ?? $json['total_charges'] ?? null;
        if (! is_numeric($price)) {
            return null;
        }

        return [
            'price' => (float) $price,
            'eta' => (string) ($json['eta'] ?? '1–3 business days'),
        ];
    }

    protected function parseBookingResponse(?array $json): array
    {
        $row = $json;
        if (is_array($json) && isset($json[0]) && is_array($json[0])) {
            $row = $json[0];
        }

        if (! is_array($row)) {
            return ['success' => false, 'tracking_number' => null, 'booking_ref' => null, 'error' => 'Empty response'];
        }

        $successFlag = strtolower((string) ($row['isSuccess'] ?? ''));
        $success = in_array($successFlag, ['true', '1', 'yes', 'success'], true);

        $tracking = $row['orderReferenceId'] ?? $row['consignmentNo'] ?? $row['cn'] ?? null;

        if ($success && filled($tracking)) {
            return [
                'success' => true,
                'tracking_number' => (string) $tracking,
                'booking_ref' => (string) $tracking,
                'error' => null,
            ];
        }

        return [
            'success' => false,
            'tracking_number' => null,
            'booking_ref' => null,
            'error' => (string) ($row['message'] ?? 'M&P booking failed'),
        ];
    }
}
