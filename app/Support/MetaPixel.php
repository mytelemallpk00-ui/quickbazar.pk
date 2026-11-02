<?php

namespace App\Support;

class MetaPixel
{
    public static function normalizePixelId(?string $value): ?string
    {
        $value = preg_replace('/\D+/', '', trim((string) $value)) ?? '';
        if ($value === '' || strlen($value) < 10) {
            return null;
        }

        return $value;
    }

    public static function isEnabled(): bool
    {
        return filter_var(SiteSettings::get('analytics_meta_enabled', false), FILTER_VALIDATE_BOOLEAN)
            && static::pixelId() !== null;
    }

    public static function pixelId(): ?string
    {
        return static::normalizePixelId((string) SiteSettings::get('analytics_meta_pixel_id', ''));
    }

    public static function isConversionsApiEnabled(): bool
    {
        return static::isEnabled()
            && filter_var(SiteSettings::get('analytics_meta_capi_enabled', false), FILTER_VALIDATE_BOOLEAN)
            && filled(static::accessToken());
    }

    public static function accessToken(): ?string
    {
        $token = trim((string) SiteSettings::get('analytics_meta_access_token', ''));

        return $token !== '' ? $token : null;
    }

    public static function testEventCode(): ?string
    {
        $code = trim((string) SiteSettings::get('analytics_meta_test_event_code', ''));

        return $code !== '' ? $code : null;
    }

    /** @return array{pixel: bool, capi: bool, pixel_id: ?string} */
    public static function connectionSummary(): array
    {
        return [
            'pixel' => static::isEnabled(),
            'capi' => static::isConversionsApiEnabled(),
            'pixel_id' => static::pixelId(),
        ];
    }
}
