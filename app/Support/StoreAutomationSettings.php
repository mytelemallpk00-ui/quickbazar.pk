<?php

namespace App\Support;

class StoreAutomationSettings
{
    public const GROUP = 'store_automation';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'master_enabled' => true,
            'abandoned_cart' => [
                'enabled' => true,
                'min_subtotal' => 2000,
                'checkout_url' => '/checkout',
                'steps' => [
                    [
                        'delay_hours' => 1,
                        'subject' => 'You left something in your Quick Bazar bag',
                        'badge' => 'CART · REMINDER',
                        'headline' => 'Your favourites are waiting',
                        'body_html' => '<p>We saved your bag — complete checkout before items sell out.</p>',
                        'cta_label' => 'Return to checkout',
                        'discount_code' => '',
                    ],
                    [
                        'delay_hours' => 24,
                        'subject' => 'Still thinking? Your Quick Bazar bag is reserved',
                        'badge' => 'CART · 24H',
                        'headline' => 'Luxury skincare, one tap away',
                        'body_html' => '<p>Your selected products are still in your bag. Need help choosing? Reply to this email.</p>',
                        'cta_label' => 'Complete my order',
                        'discount_code' => 'COMEBACK5',
                    ],
                    [
                        'delay_hours' => 72,
                        'subject' => 'Last call — your Quick Bazar cart expires soon',
                        'badge' => 'CART · FINAL',
                        'headline' => 'Final reminder for your bag',
                        'body_html' => '<p>This is our last nudge — after this, you may need to re-add items.</p>',
                        'cta_label' => 'Checkout now',
                        'discount_code' => '',
                    ],
                ],
            ],
            'post_purchase' => [
                'enabled' => true,
                'steps' => [
                    [
                        'delay_hours' => 0,
                        'subject' => 'Thank you for your Quick Bazar order',
                        'badge' => 'ORDER · CONFIRMED',
                        'headline' => 'We received your order',
                        'body_html' => '<p>Our team is preparing your parcel. You will get tracking details when it ships.</p>',
                        'cta_label' => 'View order',
                        'pick_products' => 'none',
                    ],
                    [
                        'delay_hours' => 48,
                        'subject' => 'Pairs perfectly with your recent purchase',
                        'badge' => 'UPSELL · CURATED',
                        'headline' => 'Complete your routine',
                        'body_html' => '<p>Customers who bought from your order also love these additions.</p>',
                        'cta_label' => 'Shop recommendations',
                        'pick_products' => 'same_category',
                    ],
                    [
                        'delay_hours' => 120,
                        'subject' => 'Exclusive bundle for returning clients',
                        'badge' => 'UPSELL · BUNDLE',
                        'headline' => 'Bundle & save',
                        'body_html' => '<p>Unlock a limited bundle offer tailored to your last order category.</p>',
                        'cta_label' => 'See bundle deals',
                        'pick_products' => 'bundles',
                    ],
                ],
            ],
            'reorder' => [
                'enabled' => true,
                'default_cycle_days' => 42,
                'send_days_before' => 5,
                'subject' => 'Time to replenish your Quick Bazar essentials?',
                'badge' => 'REORDER · SMART',
                'headline' => 'Your routine refill is due soon',
                'body_html' => '<p>Based on your last purchase, you may be running low. Re-order in one click.</p>',
                'cta_label' => 'Reorder now',
                'shop_url' => '/collections',
            ],
            'low_stock' => [
                'enabled' => true,
                'threshold' => 10,
                'admin_emails' => 'admin@beautage.com.pk',
                'subject' => 'Low stock alert — Quick Bazar catalog',
                'badge' => 'OPS · INVENTORY',
                'headline' => 'Products need restocking',
                'body_html' => '<p>The following SKUs are at or below your alert threshold.</p>',
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        $stored = SiteSettings::get('store_automation_settings', []);
        if (! is_array($stored)) {
            $stored = [];
        }

        return array_replace_recursive(static::defaults(), $stored);
    }

    public static function isActive(): bool
    {
        $all = static::all();

        return (bool) ($all['master_enabled'] ?? true);
    }

    /** @return array<string, mixed> */
    public static function section(string $key): array
    {
        $section = static::all()[$key] ?? [];

        return is_array($section) ? $section : [];
    }

    /** @param  array<string, mixed>  $state */
    public static function save(array $state): void
    {
        SiteSettings::set('store_automation_settings', array_replace_recursive(static::defaults(), $state), self::GROUP);
    }
}
