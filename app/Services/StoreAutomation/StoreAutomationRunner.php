<?php

namespace App\Services\StoreAutomation;

use App\Mail\StoreAutomationMail;
use App\Models\AbandonedCart;
use App\Models\Order;
use App\Models\Product;
use App\Models\ReorderReminderSchedule;
use App\Models\StoreAutomationDispatch;
use App\Support\StoreAutomationSettings;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class StoreAutomationRunner
{
    public function runAll(): array
    {
        if (! StoreAutomationSettings::isActive() || ! Schema::hasTable('abandoned_carts')) {
            return ['abandoned' => 0, 'post_purchase' => 0, 'reorder' => 0, 'low_stock' => 0];
        }

        return [
            'abandoned' => $this->processAbandonedCarts(),
            'post_purchase' => $this->processPostPurchaseUpsells(),
            'reorder' => $this->processReorderReminders(),
            'low_stock' => $this->processLowStockAlerts(),
        ];
    }

    public function processAbandonedCarts(): int
    {
        $config = StoreAutomationSettings::section('abandoned_cart');
        if (empty($config['enabled'])) {
            return 0;
        }

        $steps = $config['steps'] ?? [];
        if (! is_array($steps) || $steps === []) {
            return 0;
        }

        $minSubtotal = (float) ($config['min_subtotal'] ?? 0);
        $sent = 0;

        AbandonedCart::query()
            ->whereNull('recovered_at')
            ->whereNull('unsubscribed_at')
            ->where('subtotal', '>=', $minSubtotal)
            ->where('last_activity_at', '<', now()->subMinutes(20))
            ->orderBy('id')
            ->chunkById(50, function ($carts) use ($steps, &$sent, $config): void {
                foreach ($carts as $cart) {
                    $stepIndex = (int) $cart->reminders_sent;
                    if (! isset($steps[$stepIndex])) {
                        continue;
                    }

                    $step = $steps[$stepIndex];
                    $delayHours = (float) ($step['delay_hours'] ?? 1);
                    $baseTime = $stepIndex === 0
                        ? $cart->last_activity_at
                        : ($cart->last_reminder_at ?? $cart->last_activity_at);

                    if ($baseTime->copy()->addHours($delayHours)->isFuture()) {
                        continue;
                    }

                    $refKey = 'cart:'.$cart->id;
                    if (StoreAutomationDispatch::wasSent('abandoned_cart', $refKey, $stepIndex)) {
                        continue;
                    }

                    $checkoutUrl = url($config['checkout_url'] ?? '/checkout').'?recover='.$cart->session_token;

                    Mail::to($cart->email)->send(new StoreAutomationMail(
                        type: 'abandoned_cart',
                            subjectLine: (string) ($step['subject'] ?? 'Your Beautage bag'),
                        badge: (string) ($step['badge'] ?? 'CART'),
                        headline: (string) ($step['headline'] ?? 'Complete your order'),
                        bodyHtml: (string) ($step['body_html'] ?? ''),
                        ctaLabel: (string) ($step['cta_label'] ?? 'Checkout'),
                        ctaUrl: $checkoutUrl,
                        cartItems: $cart->cart_items ?? [],
                        discountCode: (string) ($step['discount_code'] ?? ''),
                    ));

                    StoreAutomationDispatch::record(
                        'abandoned_cart',
                        $refKey,
                        $stepIndex,
                        $cart->email,
                        (string) ($step['subject'] ?? ''),
                    );

                    $cart->update([
                        'reminders_sent' => $stepIndex + 1,
                        'last_reminder_at' => now(),
                    ]);
                    $sent++;
                }
            });

        return $sent;
    }

    public function processPostPurchaseUpsells(): int
    {
        $config = StoreAutomationSettings::section('post_purchase');
        if (empty($config['enabled'])) {
            return 0;
        }

        $steps = $config['steps'] ?? [];
        if (! is_array($steps)) {
            return 0;
        }

        $sent = 0;
        $since = now()->subDays(14);

        Order::query()
            ->where('created_at', '>=', $since)
            ->whereNotIn('status', [Order::STATUS_CANCELLED])
            ->with('items')
            ->orderBy('id')
            ->chunkById(30, function ($orders) use ($steps, &$sent): void {
                foreach ($orders as $order) {
                    foreach ($steps as $index => $step) {
                        $delayHours = (float) ($step['delay_hours'] ?? 0);
                        if ($order->created_at->copy()->addHours($delayHours)->isFuture()) {
                            continue;
                        }

                        $refKey = 'order:'.$order->id;
                        if (StoreAutomationDispatch::wasSent('post_purchase', $refKey, $index)) {
                            continue;
                        }

                        $products = $this->pickUpsellProducts($order, (string) ($step['pick_products'] ?? 'same_category'));

                        Mail::to($order->customer_email)->send(new StoreAutomationMail(
                            type: 'post_purchase',
                            subjectLine: (string) ($step['subject'] ?? 'Thank you — Beautage'),
                            badge: (string) ($step['badge'] ?? 'ORDER'),
                            headline: (string) ($step['headline'] ?? 'Thank you'),
                            bodyHtml: (string) ($step['body_html'] ?? ''),
                            ctaLabel: (string) ($step['cta_label'] ?? 'Shop now'),
                            ctaUrl: route('account.index'),
                            products: $products,
                            orderNumber: $order->order_number,
                        ));

                        StoreAutomationDispatch::record(
                            'post_purchase',
                            $refKey,
                            $index,
                            $order->customer_email,
                            (string) ($step['subject'] ?? ''),
                        );
                        $sent++;
                    }
                }
            });

        return $sent;
    }

    /** @return list<array<string, mixed>> */
    protected function pickUpsellProducts(Order $order, string $mode): array
    {
        if ($mode === 'none') {
            return [];
        }

        $categoryIds = $order->items->pluck('product_id')->filter()->map(
            fn ($id) => Product::query()->whereKey($id)->value('category_id')
        )->filter()->unique();

        $query = Product::query()->where('in_stock', true);

        if ($mode === 'bundles') {
            return $query->where('is_flash', true)->orderByDesc('rating')->limit(3)->get()->map(function (Product $p) {
                return [
                    'name' => $p->name,
                    'price' => $p->price,
                    'old_price' => $p->old_price,
                    'badge' => $p->badge ?: 'Bundle pick',
                    'image_url' => self::productImageUrl($p),
                    'url' => url('/exclusive-offers'),
                ];
            })->all();
        }

        if ($mode === 'same_category' && $categoryIds->isNotEmpty()) {
            $query->whereIn('category_id', $categoryIds);
        }

        return $query->orderByDesc('is_flash')->limit(3)->get()->map(function (Product $p) {
            return [
                'name' => $p->name,
                'price' => $p->price,
                'old_price' => $p->old_price,
                'badge' => $p->badge,
                'image_url' => self::productImageUrl($p),
                'url' => url('/product/'.$p->slug),
            ];
        })->all();
    }

    public function processReorderReminders(): int
    {
        $config = StoreAutomationSettings::section('reorder');
        if (empty($config['enabled']) || ! Schema::hasTable('reorder_reminder_schedules')) {
            return 0;
        }

        $sent = 0;

        ReorderReminderSchedule::query()
            ->whereNull('sent_at')
            ->where('due_at', '<=', now())
            ->with(['orderItem', 'product'])
            ->orderBy('id')
            ->chunkById(50, function ($rows) use ($config, &$sent): void {
                foreach ($rows as $row) {
                    $refKey = 'reorder:'.$row->id;
                    if (StoreAutomationDispatch::wasSent('reorder', $refKey, 0)) {
                        continue;
                    }

                    $product = $row->product;
                    $products = $product ? [[
                        'name' => $product->name,
                        'price' => $product->price,
                        'old_price' => $product->old_price,
                        'badge' => $product->badge,
                        'image_url' => self::productImageUrl($product),
                        'url' => url('/product/'.$product->slug),
                    ]] : [];

                    Mail::to($row->customer_email)->send(new StoreAutomationMail(
                        type: 'reorder',
                        subjectLine: (string) ($config['subject'] ?? 'Reorder reminder'),
                        badge: (string) ($config['badge'] ?? 'REORDER'),
                        headline: (string) ($config['headline'] ?? 'Refill reminder'),
                        bodyHtml: (string) ($config['body_html'] ?? ''),
                        ctaLabel: (string) ($config['cta_label'] ?? 'Shop again'),
                        ctaUrl: url($config['shop_url'] ?? '/collections'),
                        products: $products,
                    ));

                    StoreAutomationDispatch::record('reorder', $refKey, 0, $row->customer_email, (string) ($config['subject'] ?? ''));
                    $row->update(['sent_at' => now()]);
                    $sent++;
                }
            });

        return $sent;
    }

    public function processLowStockAlerts(): int
    {
        $config = StoreAutomationSettings::section('low_stock');
        if (empty($config['enabled']) || ! Schema::hasColumn('products', 'stock_quantity')) {
            return 0;
        }

        $threshold = (int) ($config['threshold'] ?? 10);
        $emails = collect(preg_split('/[\s,;]+/', (string) ($config['admin_emails'] ?? ''), -1, PREG_SPLIT_NO_EMPTY))
            ->filter(fn ($e) => filter_var($e, FILTER_VALIDATE_EMAIL))
            ->values();

        if ($emails->isEmpty()) {
            return 0;
        }

        $refKey = 'low_stock:'.now()->toDateString();
        if (StoreAutomationDispatch::wasSent('low_stock', $refKey, 0)) {
            return 0;
        }

        $products = Product::query()
            ->whereNotNull('stock_quantity')
            ->where('stock_quantity', '<=', $threshold)
            ->orderBy('stock_quantity')
            ->limit(25)
            ->get();

        if ($products->isEmpty()) {
            return 0;
        }

        $lines = $products->map(fn (Product $p) => $p->name.' — Qty '.$p->stock_quantity)->all();

        foreach ($emails as $email) {
            Mail::to($email)->send(new StoreAutomationMail(
                type: 'low_stock',
                subjectLine: (string) ($config['subject'] ?? 'Low stock alert'),
                badge: (string) ($config['badge'] ?? 'INVENTORY'),
                headline: (string) ($config['headline'] ?? 'Low stock'),
                bodyHtml: (string) ($config['body_html'] ?? '').'<ul><li>'.implode('</li><li>', array_map('e', $lines)).'</li></ul>',
                ctaLabel: 'Open admin catalog',
                ctaUrl: url('/admin/products'),
                products: [],
            ));
        }

        StoreAutomationDispatch::record('low_stock', $refKey, 0, $emails->implode(','), (string) ($config['subject'] ?? ''));

        return 1;
    }

    protected static function productImageUrl(Product $product): ?string
    {
        $image = trim((string) ($product->image ?? ''));
        if ($image === '') {
            return null;
        }

        return str_starts_with($image, 'http') ? $image : url($image);
    }
}
