<?php

namespace App\Services\StoreAutomation;

use App\Models\AbandonedCart;
use App\Support\CheckoutSettings;
use App\Support\StoreAutomationSettings;
use Illuminate\Support\Facades\Schema;

class AbandonedCartSyncService
{
    /** @param  list<array<string, mixed>>  $items */
    public function sync(string $email, array $items, ?int $userId = null): void
    {
        $email = strtolower(trim($email));
        if ($email === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        if (! StoreAutomationSettings::isActive()) {
            return;
        }

        $section = StoreAutomationSettings::section('abandoned_cart');
        if (empty($section['enabled']) || ! Schema::hasTable('abandoned_carts')) {
            return;
        }

        if ($items === []) {
            return;
        }

        $subtotal = collect($items)->sum(fn ($item) => (float) ($item['price'] ?? 0) * (int) ($item['qty'] ?? 1));
        $min = (float) ($section['min_subtotal'] ?? 0);
        if ($subtotal < $min) {
            return;
        }

        $currency = (string) (CheckoutSettings::all()['currency'] ?? 'PKR');

        $cart = AbandonedCart::query()
            ->where('email', $email)
            ->whereNull('recovered_at')
            ->latest('last_activity_at')
            ->first();

        if (! $cart) {
            $cart = new AbandonedCart([
                'email' => $email,
                'session_token' => AbandonedCart::issueToken(),
                'reminders_sent' => 0,
            ]);
        }

        $cart->fill([
            'user_id' => $userId,
            'cart_items' => $items,
            'subtotal' => $subtotal,
            'currency' => $currency,
            'last_activity_at' => now(),
        ]);
        $cart->save();
    }
}
