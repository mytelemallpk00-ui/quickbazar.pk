<?php

namespace App\Support;

use Illuminate\Support\Str;

class GoogleMerchant
{
    public static function isFeedEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_gmc_feed_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function normalizeMerchantId(?string $value): ?string
    {
        $value = preg_replace('/\D+/', '', trim((string) $value)) ?? '';

        return $value !== '' ? $value : null;
    }

    public static function feedAccessToken(): ?string
    {
        $token = trim((string) SiteSettings::get('analytics_gmc_feed_token', ''));

        return $token !== '' ? $token : null;
    }

    public static function brandName(): string
    {
        $brand = trim((string) SiteSettings::get('analytics_gmc_brand', ''));

        if ($brand !== '') {
            return $brand;
        }

        $site = trim((string) SiteSettings::get('site_name', 'Beautage'));

        return $site !== '' ? $site : 'Beautage';
    }

    public static function currencyCode(): string
    {
        $code = strtoupper(trim((string) SiteSettings::get('analytics_gmc_currency', 'PKR')));

        return preg_match('/^[A-Z]{3}$/', $code) ? $code : 'PKR';
    }

    public static function targetCountry(): string
    {
        $code = strtoupper(trim((string) SiteSettings::get('analytics_gmc_country', 'PK')));

        return preg_match('/^[A-Z]{2}$/', $code) ? $code : 'PK';
    }

    public static function storefrontUrl(): string
    {
        return GoogleAnalytics::storefrontUrl();
    }

    public static function absoluteUrl(?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return static::storefrontUrl();
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return rtrim(static::storefrontUrl(), '/').'/'.ltrim($path, '/');
    }

    public static function feedPath(): string
    {
        return '/feeds/google-merchant.xml';
    }

    public static function feedUrl(bool $includeToken = true): string
    {
        $url = rtrim(static::storefrontUrl(), '/').static::feedPath();

        if ($includeToken && static::feedAccessToken() !== null) {
            $url .= '?token='.urlencode((string) static::feedAccessToken());
        }

        return $url;
    }

    public static function merchantCenterUrl(): ?string
    {
        $id = static::normalizeMerchantId((string) SiteSettings::get('analytics_gmc_merchant_id', ''));

        return $id !== null
            ? 'https://merchants.google.com/mc/overview?a='.$id
            : 'https://merchants.google.com/';
    }

    public static function ensureFeedToken(): string
    {
        $existing = static::feedAccessToken();
        if ($existing !== null) {
            return $existing;
        }

        $token = Str::random(40);
        SiteSettings::set('analytics_gmc_feed_token', $token, 'analytics');

        return $token;
    }

    /** @return array{feed: bool, merchant_id: ?string, feed_url: ?string} */
    public static function connectionSummary(): array
    {
        $feedReady = static::isFeedEnabled() && static::feedAccessToken() !== null;

        return [
            'feed' => $feedReady,
            'merchant_id' => static::normalizeMerchantId((string) SiteSettings::get('analytics_gmc_merchant_id', '')),
            'feed_url' => $feedReady ? static::feedUrl() : null,
        ];
    }
}
