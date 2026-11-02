<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Services\Courier\Concerns\InteractsWithCourierCredentials;
use App\Services\Courier\Contracts\CourierClient;
use App\Support\Courier\PakistanZoneRates;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class AbstractCourierClient implements CourierClient
{
    use InteractsWithCourierCredentials;

    public function quote(string $destinationCity, float $weightKg, float $codAmount): ?array
    {
        if (! $this->courierCredentials()->isConfigured($this->platform())) {
            return $this->zoneFallback($destinationCity);
        }

        $url = config('courier.platforms.'.$this->platform().'.rate_url');
        if (! filled($url)) {
            return $this->zoneFallback($destinationCity);
        }

        try {
            $response = Http::timeout(12)
                ->acceptJson()
                ->post($url, $this->ratePayload($destinationCity, $weightKg, $codAmount));

            if (! $response->successful()) {
                return $this->zoneFallback($destinationCity);
            }

            $parsed = $this->parseRateResponse($response->json());
            if ($parsed !== null) {
                $parsed['source'] = 'api';

                return $parsed;
            }
        } catch (\Throwable $e) {
            Log::warning('Courier rate API failed', [
                'courier' => $this->platform(),
                'message' => $e->getMessage(),
            ]);
        }

        return $this->zoneFallback($destinationCity);
    }

    /** @return array{price: float, eta: string, source: string} */
    protected function zoneFallback(string $destinationCity): array
    {
        $zone = PakistanZoneRates::quote($destinationCity, $this->platform());

        return [
            'price' => $zone['price'],
            'eta' => $zone['eta'],
            'source' => 'zone',
        ];
    }

    public function book(Order $order): array
    {
        if (! $this->courierCredentials()->isConfigured($this->platform())) {
            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => null,
                'error' => 'Courier API not configured in admin.',
            ];
        }

        $url = config('courier.platforms.'.$this->platform().'.book_url');
        if (! filled($url)) {
            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => null,
                'error' => 'Booking URL missing — set in .env or use admin courier settings.',
            ];
        }

        try {
            $response = Http::timeout(20)
                ->acceptJson()
                ->post($url, $this->bookingPayload($order));

            if (! $response->successful()) {
                return [
                    'success' => false,
                    'tracking_number' => null,
                    'booking_ref' => null,
                    'error' => 'Courier rejected booking (HTTP '.$response->status().').',
                ];
            }

            return $this->parseBookingResponse($response->json());
        } catch (\Throwable $e) {
            Log::error('Courier booking failed', [
                'courier' => $this->platform(),
                'order' => $order->order_number,
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'tracking_number' => null,
                'booking_ref' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /** @return array<string, mixed> */
    abstract protected function ratePayload(string $destinationCity, float $weightKg, float $codAmount): array;

    /** @return array<string, mixed> */
    abstract protected function bookingPayload(Order $order): array;

    /**
     * @param  array<string, mixed>|null  $json
     * @return array{price: float, eta: string}|null
     */
    abstract protected function parseRateResponse(?array $json): ?array;

    /**
     * @param  array<string, mixed>|null  $json
     * @return array{success: bool, tracking_number: ?string, booking_ref: ?string, error: ?string}
     */
    abstract protected function parseBookingResponse(?array $json): array;

    protected function originCity(): string
    {
        return (string) config('courier.origin_city', 'Lahore');
    }
}
