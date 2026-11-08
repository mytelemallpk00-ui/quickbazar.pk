<?php

namespace App\Support;

class ProductTrustSettings
{
    public const GROUP = 'product_trust';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'enabled' => true,
            'trust_section' => [
                'badge' => 'AUTHENTICITY',
                'heading' => 'Product Trust & Authenticity',
                'intro' => 'Every item is sourced through verified channels and checked before dispatch from our flagship fulfillment team.',
                'items' => [
                    [
                        'icon' => 'shield-check',
                        'title' => '100% Original Products',
                        'description' => 'Authorized catalog only — no grey-market or counterfeit stock.',
                    ],
                    [
                        'icon' => 'badge-check',
                        'title' => 'Verified Supplier Chain',
                        'description' => 'Batch traceability and supplier documentation on premium SKUs.',
                    ],
                    [
                        'icon' => 'scan-line',
                        'title' => 'Pre-Dispatch Inspection',
                        'description' => 'Seal, packaging, and expiry checked before your order leaves our hub.',
                    ],
                    [
                        'icon' => 'lock',
                        'title' => 'Secure Fulfillment',
                        'description' => 'Tamper-evident packing and tracked courier handoff across Pakistan.',
                    ],
                ],
            ],
            'quality_section' => [
                'badge' => 'QUALITY PROMISE',
                'heading' => 'Our Quality Promise',
                'intro' => 'We stand behind every Shop Pakistan order with clear standards and responsive support.',
                'items' => [
                    [
                        'icon' => 'gem',
                        'title' => 'Premium Grade Standards',
                        'description' => 'Curated beauty tech, dermal tools, and cosmetics that meet our in-house QA checklist.',
                    ],
                    [
                        'icon' => 'rotate-ccw',
                        'title' => '30-Day Satisfaction',
                        'description' => 'Eligible returns for damaged or incorrect items — see our returns policy for details.',
                    ],
                    [
                        'icon' => 'headphones',
                        'title' => 'Dedicated Support',
                        'description' => 'WhatsApp & email assistance for orders, usage tips, and warranty guidance.',
                    ],
                    [
                        'icon' => 'truck',
                        'title' => 'Careful Delivery',
                        'description' => 'Temperature-aware handling where needed and COD available nationwide.',
                    ],
                ],
            ],
        ];
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        $stored = SiteSettings::get('product_trust_settings', []);
        if (! is_array($stored)) {
            $stored = [];
        }

        return static::normalize(array_replace_recursive(static::defaults(), $stored));
    }

    /** @param  array<string, mixed>  $settings */
    public static function save(array $settings): void
    {
        SiteSettings::set('product_trust_settings', static::normalize($settings), self::GROUP);
    }

    /** @return array<string, mixed> */
    public static function forStorefront(): array
    {
        $all = static::all();
        if (! filter_var($all['enabled'] ?? true, FILTER_VALIDATE_BOOLEAN)) {
            return ['enabled' => false];
        }

        return [
            'enabled' => true,
            'trust_section' => static::normalizeSection($all['trust_section'] ?? [], 'trust_section'),
            'quality_section' => static::normalizeSection($all['quality_section'] ?? [], 'quality_section'),
        ];
    }

    /** @param  array<string, mixed>  $settings */
    public static function normalize(array $settings): array
    {
        $defaults = static::defaults();

        return [
            'enabled' => filter_var($settings['enabled'] ?? $defaults['enabled'], FILTER_VALIDATE_BOOLEAN),
            'trust_section' => static::normalizeSection(
                is_array($settings['trust_section'] ?? null) ? $settings['trust_section'] : [],
                'trust_section',
            ),
            'quality_section' => static::normalizeSection(
                is_array($settings['quality_section'] ?? null) ? $settings['quality_section'] : [],
                'quality_section',
            ),
        ];
    }

    /**
     * @param  array<string, mixed>  $section
     * @return array<string, mixed>
     */
    private static function normalizeSection(array $section, string $defaultKey): array
    {
        $defaults = static::defaults()[$defaultKey];
        $items = $section['items'] ?? $defaults['items'];
        if (! is_array($items)) {
            $items = [];
        }

        $normalizedItems = [];
        foreach ($items as $item) {
            if (! is_array($item)) {
                continue;
            }
            $title = trim((string) ($item['title'] ?? ''));
            if ($title === '') {
                continue;
            }
            $normalizedItems[] = [
                'icon' => trim((string) ($item['icon'] ?? 'shield-check')) ?: 'shield-check',
                'title' => $title,
                'description' => trim((string) ($item['description'] ?? '')),
            ];
        }

        if ($normalizedItems === []) {
            $normalizedItems = $defaults['items'];
        }

        return [
            'badge' => trim((string) ($section['badge'] ?? $defaults['badge'])),
            'heading' => trim((string) ($section['heading'] ?? $defaults['heading'])),
            'intro' => trim((string) ($section['intro'] ?? $defaults['intro'])),
            'items' => $normalizedItems,
        ];
    }
}
