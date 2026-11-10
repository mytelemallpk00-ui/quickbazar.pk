<?php

namespace App\Support;

use Illuminate\Support\Facades\Route;

final class FooterSettings
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'trust_enabled' => true,
            'trust_badges' => [
                ['enabled' => true, 'icon' => 'truck', 'title' => 'Free Express Shipping', 'subtitle' => 'On orders over Rs 5,000'],
                ['enabled' => true, 'icon' => 'shield-check', 'title' => 'Secure Checkout', 'subtitle' => '100% protected payments'],
                ['enabled' => true, 'icon' => 'headphones', 'title' => '24/7 Dedicated Support', 'subtitle' => 'Always here to help you'],
                ['enabled' => true, 'icon' => 'rotate-ccw', 'title' => 'Hassle-Free Returns', 'subtitle' => '30-day money-back guarantee'],
            ],
            'newsletter_enabled' => true,
            'newsletter_badge' => 'NEWSLETTER OFFER',
            'newsletter_heading' => 'Get 15% Off Your First Order',
            'newsletter_description' => 'Subscribe to our newsletter to receive early access to sales, new arrivals, and exclusive promo codes.',
            'newsletter_placeholder' => 'Enter your email address...',
            'newsletter_button' => 'Subscribe',
            'partner_hub_enabled' => true,
            'partner_hub_badge' => 'CORPORATE & PARTNERS',
            'partner_hub_title' => 'Quick Bazar Partner',
            'partner_hub_subtitle' => 'Media and partner resources — separate from customer shopping.',
            'partner_hub_buttons' => [
                ['enabled' => true, 'label' => 'Press Releases', 'icon' => 'newspaper', 'style' => 'outline', 'link_type' => 'href', 'href' => 'press-releases'],
                ['enabled' => true, 'label' => 'FAQ', 'icon' => 'help-circle', 'style' => 'outline', 'link_type' => 'href', 'href' => 'faq'],
                ['enabled' => true, 'label' => 'Events', 'icon' => 'calendar-days', 'style' => 'outline', 'link_type' => 'href', 'href' => 'events'],
            ],
            'partner_newsletter_enabled' => true,
            'partner_newsletter_badge' => 'PARTNER UPDATES',
            'partner_newsletter_heading' => 'IR & partner briefing list',
            'partner_newsletter_description' => 'Quarterly highlights, event invites, and press notices — not mixed with store promo emails.',
            'partner_newsletter_placeholder' => 'Partner email…',
            'partner_newsletter_button' => 'Join briefing list',
            'policy_links' => [
                ['enabled' => true, 'icon' => 'rotate-ccw', 'label' => 'Return and refund policy', 'href' => 'returns'],
                ['enabled' => true, 'icon' => 'shield', 'label' => 'Privacy policy', 'href' => 'help'],
                ['enabled' => true, 'icon' => 'file-text', 'label' => 'Terms of service', 'href' => 'help'],
                ['enabled' => true, 'icon' => 'truck', 'label' => 'Shipping policy', 'href' => 'shipping'],
                ['enabled' => true, 'icon' => 'headphones', 'label' => 'Contact information', 'href' => 'help'],
                ['enabled' => true, 'icon' => 'scale', 'label' => 'Legal notice', 'href' => 'help'],
            ],
            'about_text' => 'Crafting high-end lifestyle products designed for modern comfort, sustainability, and uncompromising quality.',
            'show_tagline_under_logo' => true,
            'social_links' => [
                ['enabled' => true, 'platform' => 'Facebook', 'icon' => 'facebook', 'href' => 'https://facebook.com'],
                ['enabled' => true, 'platform' => 'Instagram', 'icon' => 'instagram', 'href' => 'https://instagram.com'],
                ['enabled' => true, 'platform' => 'Twitter', 'icon' => 'twitter', 'href' => 'https://twitter.com'],
                ['enabled' => true, 'platform' => 'Pinterest', 'icon' => 'pin', 'href' => 'https://pinterest.com'],
                ['enabled' => true, 'platform' => 'YouTube', 'icon' => 'youtube', 'href' => 'https://youtube.com'],
            ],
            'link_columns' => [
                [
                    'title' => 'Categories',
                    'links' => [
                        ['enabled' => true, 'icon' => 'cpu', 'label' => 'Electronics & Gadgets', 'href' => 'collections'],
                        ['enabled' => true, 'icon' => 'shirt', 'label' => 'Fashion & Apparel', 'href' => 'collections'],
                        ['enabled' => true, 'icon' => 'home', 'label' => 'Home & Kitchen', 'href' => 'collections'],
                        ['enabled' => true, 'icon' => 'sparkles', 'label' => 'Beauty & Skincare', 'href' => 'collections'],
                        ['enabled' => true, 'icon' => 'leaf', 'label' => 'Sustainable Living', 'href' => 'collections'],
                    ],
                ],
                [
                    'title' => 'Customer Care',
                    'links' => [
                        ['enabled' => true, 'icon' => 'help-circle', 'label' => 'Help Center & FAQ', 'href' => 'help'],
                        ['enabled' => true, 'icon' => 'package', 'label' => 'Track Your Order', 'href' => 'track-order'],
                        ['enabled' => true, 'icon' => 'rotate-ccw', 'label' => 'Returns & Exchanges', 'href' => 'returns'],
                        ['enabled' => true, 'icon' => 'truck', 'label' => 'Shipping & Delivery', 'href' => 'shipping'],
                        ['enabled' => true, 'icon' => 'ruler', 'label' => 'Size Guide', 'href' => 'size-guide'],
                    ],
                ],
                [
                    'title' => 'About Quick Bazar',
                    'links' => [
                        ['enabled' => true, 'icon' => 'book-open', 'label' => 'Our Story', 'href' => 'our-story'],
                        ['enabled' => true, 'icon' => 'briefcase', 'label' => 'Careers', 'href' => 'careers'],
                        ['enabled' => true, 'icon' => 'newspaper', 'label' => 'Press & Media', 'href' => 'press'],
                        ['enabled' => true, 'icon' => 'globe', 'label' => 'Sustainability', 'href' => 'our-story'],
                    ],
                ],
            ],
            'mobile_app_enabled' => true,
            'mobile_app_title' => 'Mobile App',
            'mobile_app_description' => 'Download our app for exclusive mobile-only discounts.',
            'mobile_app_ios_href' => 'https://apple.com',
            'mobile_app_android_href' => 'https://google.com',
            'copyright_line' => '© '.date('Y').' Quick Bazar. All rights reserved.',
        ];
    }

    /** @return array<string, mixed> */
    public static function forStorefront(): array
    {
        $saved = SiteSettings::get('footer_settings', []);
        if (! is_array($saved)) {
            $saved = [];
        }

        $legacyAbout = trim((string) SiteSettings::get('footer_about', ''));
        $merged = self::mergeRecursive(self::defaults(), $saved);
        if ($legacyAbout !== '' && blank($saved['about_text'] ?? null)) {
            $merged['about_text'] = $legacyAbout;
        }

        return $merged;
    }

    public static function resolveHref(?string $href): ?string
    {
        if (blank($href)) {
            return null;
        }

        $href = trim((string) $href);

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return $href;
        }

        if (str_starts_with($href, '/')) {
            return url($href);
        }

        if (Route::has($href)) {
            return route($href);
        }

        return url('/'.ltrim($href, '/'));
    }

    public static function isExternalUrl(?string $url): bool
    {
        return is_string($url) && (str_starts_with($url, 'http://') || str_starts_with($url, 'https://'));
    }

    /** @param  array<string, mixed>  $button */
    public static function resolvePartnerButtonHref(array $button): ?string
    {
        return self::resolveHref($button['href'] ?? null);
    }

    public static function partnerButtonClass(string $style): string
    {
        return match ($style) {
            'apply' => 'site-partner-hub-btn site-partner-hub-btn--apply',
            'portal' => 'site-partner-hub-btn site-partner-hub-btn--portal',
            'outline-white' => 'site-partner-hub-btn site-partner-hub-btn--outline-white',
            default => 'site-partner-hub-btn site-partner-hub-btn--outline',
        };
    }

    /**
     * @param  array<string, mixed>  $defaults
     * @param  array<string, mixed>  $saved
     * @return array<string, mixed>
     */
    private static function mergeRecursive(array $defaults, array $saved): array
    {
        foreach ($saved as $key => $value) {
            if (is_array($value) && isset($defaults[$key]) && is_array($defaults[$key]) && self::isList($value)) {
                $defaults[$key] = $value;

                continue;
            }
            if (is_array($value) && isset($defaults[$key]) && is_array($defaults[$key]) && ! self::isList($value)) {
                $defaults[$key] = self::mergeRecursive($defaults[$key], $value);

                continue;
            }
            $defaults[$key] = $value;
        }

        return $defaults;
    }

    /** @param  array<mixed>  $array */
    private static function isList(array $array): bool
    {
        if ($array === []) {
            return true;
        }

        return array_keys($array) === range(0, count($array) - 1);
    }
}
