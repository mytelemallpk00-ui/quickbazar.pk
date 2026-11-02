<?php

namespace App\Support;

class GoogleAnalytics
{
    public static function normalizeMeasurementId(?string $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        if ($value === '') {
            return null;
        }

        if (! preg_match('/^G-[A-Z0-9]{6,}$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function normalizeSiteVerificationToken(?string $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if (preg_match('/content=["\']([^"\']+)["\']/i', $value, $matches)) {
            $value = $matches[1];
        }

        $value = trim($value);
        if ($value === '' || ! preg_match('/^[a-zA-Z0-9_\-]+$/', $value)) {
            return null;
        }

        return $value;
    }

    public static function measurementId(): ?string
    {
        if (! static::isEnabled()) {
            return null;
        }

        return static::normalizeMeasurementId((string) SiteSettings::get('analytics_ga_measurement_id', ''));
    }

    public static function isEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_ga_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function siteVerificationToken(): ?string
    {
        if (! static::isSearchConsoleEnabled()) {
            return null;
        }

        return static::normalizeSiteVerificationToken((string) SiteSettings::get('analytics_gsc_verification', ''));
    }

    public static function isSearchConsoleEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_gsc_enabled', false), FILTER_VALIDATE_BOOLEAN);
    }

    public static function storefrontUrl(): string
    {
        $configured = trim((string) config('app.url', ''));
        if ($configured !== '') {
            return rtrim($configured, '/');
        }

        return rtrim(url('/'), '/');
    }

    /** @return array{ga: bool, gsc: bool, ga_id: ?string, storefront_url: string} */
    public static function connectionSummary(): array
    {
        $gaId = static::measurementId();

        return [
            'ga' => $gaId !== null,
            'gsc' => static::siteVerificationToken() !== null,
            'ga_id' => $gaId,
            'storefront_url' => static::storefrontUrl(),
        ];
    }
}
