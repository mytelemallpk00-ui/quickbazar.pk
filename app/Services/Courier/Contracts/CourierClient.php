<?php

namespace App\Services\Courier\Contracts;

use App\Models\Order;

interface CourierClient
{
    public function platform(): string;

    /**
     * @return array{price: float, eta: string, source: string}|null
     */
    public function quote(string $destinationCity, float $weightKg, float $codAmount): ?array;

    /**
     * @return array{success: bool, tracking_number: ?string, booking_ref: ?string, error: ?string}
     */
    public function book(Order $order): array;
}
