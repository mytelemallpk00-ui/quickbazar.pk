<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Support\Courier\CourierPlatforms;

class CourierBookingService
{
    public function __construct(protected CourierRateService $rates) {}

    public function bookOrder(Order $order): Order
    {
        $platform = $order->courier_code ?: CourierPlatforms::platformFromMethodId((string) $order->shipping_method_id);
        if ($platform === null) {
            $order->forceFill([
                'courier_booking_status' => 'skipped',
                'courier_error' => null,
            ])->save();

            return $order;
        }

        $client = $this->rates->clientForPlatform($platform);
        if ($client === null) {
            $order->forceFill([
                'courier_booking_status' => 'failed',
                'courier_error' => 'Unknown courier.',
            ])->save();

            return $order;
        }

        $order->loadMissing('items');
        $result = $client->book($order);

        if ($result['success']) {
            $order->forceFill([
                'courier_code' => $platform,
                'courier_label' => CourierPlatforms::label($platform),
                'tracking_number' => $result['tracking_number'],
                'courier_booking_ref' => $result['booking_ref'],
                'courier_booking_status' => 'booked',
                'courier_error' => null,
            ])->save();

            return $order;
        }

        $order->forceFill([
            'courier_booking_status' => 'failed',
            'courier_error' => $result['error'],
        ])->save();

        return $order;
    }
}
