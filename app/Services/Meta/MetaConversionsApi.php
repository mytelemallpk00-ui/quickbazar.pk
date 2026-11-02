<?php

namespace App\Services\Meta;

use App\Models\Order;
use App\Support\MetaPixel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaConversionsApi
{
    public function sendPurchase(Order $order, ?string $clientIp = null, ?string $userAgent = null): bool
    {
        if (! MetaPixel::isConversionsApiEnabled()) {
            return false;
        }

        $pixelId = MetaPixel::pixelId();
        $token = MetaPixel::accessToken();
        if ($pixelId === null || $token === null) {
            return false;
        }

        $order->loadMissing('items');

        $contents = $order->items->map(fn ($item): array => [
            'id' => (string) ($item->product_id ?: $item->sku ?: $item->id),
            'quantity' => (int) $item->quantity,
            'item_price' => (float) $item->unit_price,
        ])->values()->all();

        $userData = array_filter([
            'em' => $this->hashData($order->customer_email),
            'ph' => $this->hashData($this->normalizePhone((string) $order->customer_phone)),
            'fn' => $this->hashData(strtolower(trim((string) $order->customer_first_name))),
            'ln' => $this->hashData(strtolower(trim((string) $order->customer_last_name))),
            'client_ip_address' => $clientIp,
            'client_user_agent' => $userAgent,
        ]);

        $payload = [
            'data' => [[
                'event_name' => 'Purchase',
                'event_time' => $order->created_at?->timestamp ?? time(),
                'event_id' => (string) $order->order_number,
                'action_source' => 'website',
                'event_source_url' => url('/checkout'),
                'user_data' => $userData,
                'custom_data' => [
                    'currency' => strtoupper((string) ($order->currency ?: 'PKR')),
                    'value' => (float) $order->total,
                    'content_type' => 'product',
                    'contents' => $contents,
                    'order_id' => (string) $order->order_number,
                ],
            ]],
            'access_token' => $token,
        ];

        $testCode = MetaPixel::testEventCode();
        if ($testCode !== null) {
            $payload['test_event_code'] = $testCode;
        }

        try {
            $response = Http::timeout(15)
                ->post("https://graph.facebook.com/v21.0/{$pixelId}/events", $payload);

            if (! $response->successful()) {
                Log::warning('Meta CAPI purchase failed', [
                    'order' => $order->order_number,
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Meta CAPI exception', [
                'order' => $order->order_number,
                'message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    protected function hashData(?string $value): ?string
    {
        $value = trim(strtolower((string) $value));
        if ($value === '') {
            return null;
        }

        return hash('sha256', $value);
    }

    protected function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';
        if (str_starts_with($digits, '92')) {
            return $digits;
        }
        if (str_starts_with($digits, '0')) {
            return '92'.substr($digits, 1);
        }

        return $digits;
    }
}
