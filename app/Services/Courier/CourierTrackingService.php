<?php

namespace App\Services\Courier;

use App\Models\Order;

/**
 * Live courier scan — TCS / Leopards / M&P APIs yahan connect hongi.
 * Abhi sirf order record se; API ready hone par is class mein implement karein.
 */
class CourierTrackingService
{
    public function formatForChat(Order $order): ?string
    {
        if (! filled($order->tracking_number)) {
            return null;
        }

        $live = $this->fetchLiveStatus($order);
        if ($live !== null) {
            return 'Courier live tracking: '.$live;
        }

        return 'Courier live scan: jald add ho rahi hai — abhi status Beautage order database se hai (upar).';
    }

    /**
     * @return null|string Human-readable latest scan line
     */
    protected function fetchLiveStatus(Order $order): ?string
    {
        // TODO: dispatch to TcsCourierClient::track(), LeopardsCourierClient::track(), etc.
        return null;
    }
}
