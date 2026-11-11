<?php

namespace App\Support;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Cache;

class SiteSettings
{
    /** @return array<string, mixed> */
    public static function all(): array
    {
        return Cache::rememberForever('site_settings.all', function (): array {
            return SiteSetting::query()
                ->pluck('value', 'key')
                ->all();
        });
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $all = static::all();

        return array_key_exists($key, $all) ? $all[$key] : $default;
    }

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        SiteSetting::query()->updateOrCreate(
            ['key' => $key],
            ['group' => $group, 'value' => $value],
        );

        Cache::forget('site_settings.all');
    }

    public const BRAND_LOGO = 'images/quick-bazar-logo.png';

    public const BRAND_FAVICON = 'images/quick-bazar-favicon.png';

    public const BRAND_ASSET_VERSION = '2026111105';

    public static function logoUrl(string $default = self::BRAND_LOGO): string
    {
        return static::versionedBrandUrl(static::brandPublicUrl(static::brandAsset(static::get('logo_url', '')), $default));
    }

    public static function faviconUrl(string $default = self::BRAND_FAVICON): string
    {
        return static::versionedBrandUrl(static::brandPublicUrl(static::faviconAsset(static::get('favicon_url', '')), $default));
    }

    public static function stringMediaReference(mixed $value): string
    {
        if (is_array($value)) {
            $value = reset($value);
        }

        return trim((string) ($value ?? ''));
    }

    /** @return array<int, string> State shape Filament FileUpload expects (never a bare string). */
    public static function fileUploadFormState(mixed $value): array
    {
        $diskPath = static::fileUploadDiskPath($value);
        if ($diskPath !== null && $diskPath !== '') {
            return [$diskPath];
        }

        return [];
    }

    /** Relative path on the public disk for Filament FileUpload, or null if not stored there. */
    public static function fileUploadDiskPath(mixed $value): ?string
    {
        $value = static::stringMediaReference($value);
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        if (str_starts_with($value, 'storage/')) {
            return ltrim(substr($value, strlen('storage/')), '/');
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $relative = parse_url($value, PHP_URL_PATH);
            if (is_string($relative) && str_starts_with($relative, '/storage/')) {
                return ltrim(substr($relative, strlen('/storage/')), '/');
            }

            return null;
        }

        if (str_starts_with($value, '/images/') || str_starts_with($value, 'images/')) {
            return null;
        }

        if (! str_starts_with($value, '/')) {
            return ltrim($value, '/');
        }

        return null;
    }

    /** Normalize Filament FileUpload state before persisting to site_settings. */
    public static function normalizeMediaForStorage(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $value = $value[array_key_first($value)] ?? null;
        }

        $value = trim((string) ($value ?? ''));
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
            $relative = parse_url($value, PHP_URL_PATH);
            if (is_string($relative) && str_starts_with($relative, '/storage/')) {
                return $relative;
            }

            return $value;
        }

        if (str_starts_with($value, '/storage/')) {
            return $value;
        }

        if (str_starts_with($value, 'storage/')) {
            return '/'.ltrim($value, '/');
        }

        if (str_starts_with($value, '/images/')) {
            return $value;
        }

        return '/storage/'.ltrim($value, '/');
    }

    public static function assetUrl(string $path, string $default): string
    {
        return static::mediaUrl($path, $default);
    }

    public static function mediaUrl(string $path, string $default): string
    {
        $path = trim($path);
        if ($path === '') {
            return static::publicAsset($default);
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $host = parse_url($path, PHP_URL_HOST);
            if (is_string($host) && (str_contains($host, 'localhost') || str_contains($host, '127.0.0.1'))) {
                $relative = parse_url($path, PHP_URL_PATH);
                if (is_string($relative) && $relative !== '') {
                    return $relative;
                }
            }

            return $path;
        }

        if (str_starts_with($path, '/')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/'.$path;
        }

        if (str_contains($path, '/')) {
            return '/storage/'.ltrim($path, '/');
        }

        return static::publicAsset($path);
    }

    private static function publicAsset(string $path): string
    {
        return '/'.ltrim($path, '/');
    }

    /** @return array<string, mixed> */
    public static function headerNavigation(): array
    {
        $value = static::get('header_navigation', []);

        return is_array($value) ? $value : [];
    }

    /** @param  array<string, mixed>  $navigation */
    public static function setHeaderNavigation(array $navigation): void
    {
        static::set('header_navigation', $navigation, 'navigation');
    }

    public static function reviewsAutoApprove(): bool
    {
        $value = static::get('reviews_auto_approve', false);

        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }

    /** @return array<int, string> */
    public static function reviewPhotoUrls(?array $paths): array
    {
        if (! $paths) {
            return [];
        }

        return array_values(array_filter(array_map(function ($path) {
            $path = trim((string) $path);
            if ($path === '') {
                return null;
            }

            if (! str_starts_with($path, 'storage/') && ! str_starts_with($path, 'http')) {
                $path = 'storage/'.$path;
            }

            return static::assetUrl($path, 'images/beautagelogosquare2.png');
        }, $paths)));
    }

    /** @return array<string, string> */
    public static function footerLogoUrl(string $default = self::BRAND_LOGO): string
    {
        return static::versionedBrandUrl(static::brandPublicUrl(static::brandAsset(static::get('footer_logo_url', '')), $default));
    }

    public static function brandPublicUrl(string $path, string $default): string
    {
        $lower = strtolower($path);
        if (str_ends_with($lower, 'quick-bazar-logo.png')) {
            return '/images/quick-bazar-logo.png';
        }
        if (str_ends_with($lower, 'quick-bazar-favicon.png')) {
            return '/images/quick-bazar-favicon.png';
        }

        return static::mediaUrl($path, $default);
    }

    public static function brandAsset(mixed $value): string
    {
        $path = static::stringMediaReference($value);
        if (static::isRetiredBrandPath($path)) {
            return self::BRAND_LOGO;
        }

        return $path;
    }

    public static function faviconAsset(mixed $value): string
    {
        $path = static::stringMediaReference($value);
        $lower = strtolower($path);
        if (static::isRetiredBrandPath($path) || str_contains($lower, 'quick-bazar-logo')) {
            return self::BRAND_FAVICON;
        }

        return $path;
    }

    public static function versionedBrandUrl(string $url): string
    {
        if (! str_contains($url, 'quick-bazar-logo') && ! str_contains($url, 'quick-bazar-favicon')) {
            return $url;
        }

        $join = str_contains($url, '?') ? '&' : '?';

        return $url.$join.'v='.self::BRAND_ASSET_VERSION;
    }

    protected static function isRetiredBrandPath(string $path): bool
    {
        if ($path === '') {
            return true;
        }

        $lower = strtolower($path);

        return str_contains($lower, 'beautage')
            || str_contains($lower, 'logosquare')
            || str_contains($lower, 'shop-pakistan');
    }

    public static function themeColors(): array
    {
        $defaults = BrandPalette::themeDefaults();
        $legacy = BrandPalette::legacyColors();

        $amber = static::resolveThemeColor('theme_color_amber', $defaults['amber'], $legacy['amber']);

        return [
            'purple' => static::resolveThemeColor('theme_color_purple', $defaults['purple'], $legacy['purple']),
            'purpleDark' => static::resolveThemeColor('theme_color_purple_dark', $defaults['purpleDark'], $legacy['purpleDark']),
            'pink' => static::resolveThemeColor('theme_color_pink', $defaults['pink'], $legacy['pink']),
            'pinkHover' => static::resolveThemeColor('theme_color_pink_hover', $defaults['pinkHover'], $legacy['pinkHover']),
            'amber' => $amber,
            'gold' => $amber,
        ];
    }

    /** @param  list<string>  $legacy */
    private static function resolveThemeColor(string $key, string $fallback, array $legacy): string
    {
        $value = trim((string) static::get($key, ''));
        if ($value === '' || in_array(strtolower($value), $legacy, true)) {
            return $fallback;
        }

        return $value;
    }

    /** @return array<string, mixed> */
    public static function homepage(): array
    {
        $stored = static::get('homepage_content', []);

        return HomepageContent::mergeWithDefaults(is_array($stored) ? $stored : []);
    }

    /** @param  array<string, mixed>  $content */
    public static function setHomepage(array $content): void
    {
        static::set('homepage_content', $content, 'homepage');
    }
}
