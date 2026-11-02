<?php

namespace App\Support;

class WebmasterTools
{
    public static function normalizeToken(?string $value, int $minLen = 8, int $maxLen = 256): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/content=["\']([^"\']+)["\']/i', $value, $matches)) {
            $value = trim($matches[1]);
        }

        $value = trim($value);
        $len = strlen($value);
        if ($len < $minLen || $len > $maxLen) {
            return null;
        }

        if (! preg_match('/^[a-zA-Z0-9_\-]+$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function normalizeIndexNowKey(?string $value): ?string
    {
        $value = strtolower(trim((string) $value));
        if ($value === '') {
            return null;
        }

        if (! preg_match('/^[a-f0-9]{32,128}$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function bingEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_bing_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function bingVerificationToken(): ?string
    {
        if (! static::bingEnabled()) {
            return null;
        }

        return static::normalizeToken((string) SiteSettings::get('analytics_bing_verification', ''));
    }

    public static function yandexEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_yandex_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function yandexVerificationToken(): ?string
    {
        if (! static::yandexEnabled()) {
            return null;
        }

        return static::normalizeToken((string) SiteSettings::get('analytics_yandex_verification', ''));
    }

    public static function pinterestEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_pinterest_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function pinterestVerificationToken(): ?string
    {
        if (! static::pinterestEnabled()) {
            return null;
        }

        return static::normalizeToken((string) SiteSettings::get('analytics_pinterest_verification', ''));
    }

    public static function indexNowEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_indexnow_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function indexNowKey(): ?string
    {
        if (! static::indexNowEnabled()) {
            return null;
        }

        return static::normalizeIndexNowKey((string) SiteSettings::get('analytics_indexnow_key', ''));
    }

    public static function sitemapUrl(): string
    {
        if (class_exists(\App\Services\Seo\SitemapGenerator::class)) {
            return \App\Services\Seo\SitemapGenerator::indexUrl();
        }

        return rtrim(GoogleAnalytics::storefrontUrl(), '/').'/sitemap_index.xml';
    }

    /** @return list<array{label: string, url: string}> */
    public static function portalLinks(): array
    {
        return [
            ['label' => 'Google Search Console', 'url' => 'https://search.google.com/search-console'],
            ['label' => 'Bing Webmaster Tools', 'url' => 'https://www.bing.com/webmasters'],
            ['label' => 'Yandex Webmaster', 'url' => 'https://webmaster.yandex.com/'],
            ['label' => 'Pinterest Business', 'url' => 'https://www.pinterest.com/settings/claim/'],
            ['label' => 'IndexNow docs', 'url' => 'https://www.indexnow.org/documentation'],
        ];
    }
}
