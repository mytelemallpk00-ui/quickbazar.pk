<?php

namespace App\Support;

class CheckoutSettings
{
    public const GROUP = 'checkout';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'page_badge' => 'FLAGSHIP SECURE CHECKOUT PORTAL',
            'page_title' => 'Finalize Your Luxury Order',
            'page_subtitle' => 'Please verify your shipping details and choose your preferred payment method.',
            'currency' => 'PKR',
            'tax_rate' => 0.08,
            'free_shipping_threshold' => 5000,
            'show_express_checkout' => false,
            'shipping_methods' => [
                [
                    'id' => 'standard',
                    'label' => 'Free Express Flagship Delivery',
                    'description' => '2-3 Business Days Across Pakistan',
                    'price' => 0,
                    'free_over_threshold' => true,
                    'enabled' => true,
                ],
                [
                    'id' => 'express',
                    'label' => 'VIP Overnight Priority Dispatch',
                    'description' => 'Next Day Air Priority (Guaranteed Courier)',
                    'price' => 250,
                    'free_over_threshold' => false,
                    'enabled' => true,
                ],
            ],
            'payment_methods' => [
                [
                    'id' => 'cod',
                    'label' => 'Cash on Delivery',
                    'short_label' => 'Cash on Delivery',
                    'icon' => 'banknote',
                    'enabled' => true,
                    'instructions' => 'Pay with cash when the courier delivers your order.',
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        $stored = SiteSettings::get('checkout_settings', []);
        if (! is_array($stored)) {
            $stored = [];
        }

        return static::normalize(array_merge(static::defaults(), $stored));
    }

    /** @param  array<string, mixed>  $settings */
    public static function save(array $settings): void
    {
        SiteSettings::set('checkout_settings', static::normalize($settings), self::GROUP);
    }

    /** @return array<string, mixed> */
    public static function forStorefront(): array
    {
        $all = static::all();

        return [
            'page_badge' => (string) $all['page_badge'],
            'page_title' => (string) $all['page_title'],
            'page_subtitle' => (string) $all['page_subtitle'],
            'currency' => (string) $all['currency'],
            'tax_rate' => (float) $all['tax_rate'],
            'tax_percent_label' => round((float) $all['tax_rate'] * 100, 2),
            'free_shipping_threshold' => (float) $all['free_shipping_threshold'],
            'show_express_checkout' => (bool) $all['show_express_checkout'],
            'shipping_methods' => array_values(array_filter(
                $all['shipping_methods'],
                fn (array $m): bool => (bool) ($m['enabled'] ?? false),
            )),
            'payment_methods' => array_values(array_filter(
                $all['payment_methods'],
                fn (array $m): bool => (bool) ($m['enabled'] ?? false),
            )),
        ];
    }

    public static function shippingPriceFor(string $methodId, float $subtotalAfterDiscount): float
    {
        foreach (static::all()['shipping_methods'] as $method) {
            if (($method['id'] ?? '') !== $methodId || ! ($method['enabled'] ?? false)) {
                continue;
            }

            $price = (float) ($method['price'] ?? 0);
            $threshold = (float) static::all()['free_shipping_threshold'];
            if ($price > 0 && ($method['free_over_threshold'] ?? false) && $subtotalAfterDiscount >= $threshold) {
                return 0.0;
            }

            if (($method['free_over_threshold'] ?? false) && $subtotalAfterDiscount >= $threshold && $price === 0.0) {
                return 0.0;
            }

            return max(0, $price);
        }

        return 0.0;
    }

    public static function isPaymentMethodAllowed(string $paymentMethodLabel): bool
    {
        $needle = trim($paymentMethodLabel);
        if ($needle === '') {
            return false;
        }

        foreach (static::all()['payment_methods'] as $method) {
            if (! ($method['enabled'] ?? false)) {
                continue;
            }
            $label = (string) ($method['label'] ?? '');
            $short = (string) ($method['short_label'] ?? $label);
            if ($needle === $label || $needle === $short || str_starts_with($needle, $short)) {
                return true;
            }
        }

        return false;
    }

    /** @param  array<string, mixed>  $settings
     * @return array<string, mixed>
     */
    public static function normalize(array $settings): array
    {
        $defaults = static::defaults();
        $settings['currency'] = strtoupper(substr(trim((string) ($settings['currency'] ?? $defaults['currency'])), 0, 8));
        $settings['tax_rate'] = max(0, min(1, (float) ($settings['tax_rate'] ?? $defaults['tax_rate'])));
        $settings['free_shipping_threshold'] = max(0, (float) ($settings['free_shipping_threshold'] ?? $defaults['free_shipping_threshold']));
        $settings['show_express_checkout'] = filter_var($settings['show_express_checkout'] ?? false, FILTER_VALIDATE_BOOLEAN);

        $settings['shipping_methods'] = static::normalizeShippingMethods(
            is_array($settings['shipping_methods'] ?? null) ? $settings['shipping_methods'] : $defaults['shipping_methods'],
            $defaults['shipping_methods'],
        );

        $settings['payment_methods'] = static::normalizePaymentMethods(
            is_array($settings['payment_methods'] ?? null) ? $settings['payment_methods'] : $defaults['payment_methods'],
            $defaults['payment_methods'],
        );

        foreach (['page_badge', 'page_title', 'page_subtitle'] as $key) {
            $settings[$key] = trim((string) ($settings[$key] ?? $defaults[$key]));
        }

        return array_merge($defaults, $settings);
    }

    /**
     * @param  list<array<string, mixed>>  $methods
     * @param  list<array<string, mixed>>  $fallback
     * @return list<array<string, mixed>>
     */
    protected static function normalizeShippingMethods(array $methods, array $fallback): array
    {
        $out = [];
        foreach ($methods as $i => $method) {
            if (! is_array($method)) {
                continue;
            }
            $id = trim((string) ($method['id'] ?? ('ship_'.$i)));
            if ($id === '') {
                continue;
            }
            $out[] = [
                'id' => $id,
                'label' => trim((string) ($method['label'] ?? 'Delivery')),
                'description' => trim((string) ($method['description'] ?? '')),
                'price' => max(0, (float) ($method['price'] ?? 0)),
                'free_over_threshold' => filter_var($method['free_over_threshold'] ?? true, FILTER_VALIDATE_BOOLEAN),
                'enabled' => filter_var($method['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN),
            ];
        }

        return $out !== [] ? $out : $fallback;
    }

    /**
     * @param  list<array<string, mixed>>  $methods
     * @param  list<array<string, mixed>>  $fallback
     * @return list<array<string, mixed>>
     */
    protected static function normalizePaymentMethods(array $methods, array $fallback): array
    {
        $out = [];
        foreach ($methods as $i => $method) {
            if (! is_array($method)) {
                continue;
            }
            $id = trim((string) ($method['id'] ?? ''));
            if ($id !== 'cod') {
                continue;
            }
            $label = trim((string) ($method['label'] ?? 'Cash on Delivery'));
            if ($label === '') {
                $label = 'Cash on Delivery';
            }
            $out[] = [
                'id' => 'cod',
                'label' => $label,
                'short_label' => 'Cash on Delivery',
                'icon' => 'banknote',
                'enabled' => true,
                'instructions' => trim((string) ($method['instructions'] ?? 'Pay with cash when the courier delivers your order.')),
            ];
            break;
        }

        return $out !== [] ? $out : $fallback;
    }
}
