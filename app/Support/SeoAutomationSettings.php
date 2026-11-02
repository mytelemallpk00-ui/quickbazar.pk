<?php

namespace App\Support;

class SeoAutomationSettings
{
    public const GROUP = 'seo_automation';

    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'sitemap_enabled' => true,
            'sitemap_include_products' => true,
            'sitemap_include_pseo' => true,
            'sitemap_include_cms' => true,
            'auto_index_on_product_save' => false,
            'auto_ai_seo_on_product_create' => false,
            'faceted_indexed_params' => ['cat'],
            'faceted_noindex_when_extra_filters' => true,
        ];
    }

    /** @return array<string, mixed> */
    public static function all(): array
    {
        $stored = SiteSettings::get('seo_automation_settings', []);
        if (! is_array($stored)) {
            $stored = [];
        }

        return array_replace_recursive(static::defaults(), $stored);
    }

    /** @param  array<string, mixed>  $state */
    public static function save(array $state): void
    {
        SiteSettings::set('seo_automation_settings', array_replace_recursive(static::defaults(), $state), self::GROUP);
    }

    public static function sitemapEnabled(): bool
    {
        return (bool) (static::all()['sitemap_enabled'] ?? true);
    }

    public static function autoIndexOnProductSave(): bool
    {
        return (bool) (static::all()['auto_index_on_product_save'] ?? false);
    }

    public static function autoAiSeoOnProductCreate(): bool
    {
        return (bool) (static::all()['auto_ai_seo_on_product_create'] ?? false);
    }

    /** @return list<string> */
    public static function facetedIndexedParams(): array
    {
        $params = static::all()['faceted_indexed_params'] ?? ['cat'];
        if (is_string($params)) {
            $params = array_filter(array_map('trim', explode(',', $params)));
        }

        return is_array($params) ? array_values($params) : ['cat'];
    }
}
