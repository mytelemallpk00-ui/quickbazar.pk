<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Courier\CourierBookingService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class BookCourierShipmentJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function handle(CourierBookingService $booking): void
    {
        $booking->bookOrder($this->order->fresh());
    }
}
