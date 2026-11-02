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

    public static function logoUrl(string $default = 'images/beautagelogosquare2.png'): string
    {
        return static::mediaUrl(static::stringMediaReference(static::get('logo_url', '')), $default);
    }

    public static function faviconUrl(string $default = 'images/beautagelogosquare2.png'): string
    {
        return static::mediaUrl(static::stringMediaReference(static::get('favicon_url', '')), $default);
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
    public static function footerLogoUrl(string $default = 'images/beautage-logo-round.png'): string
    {
        $configured = static::stringMediaReference(static::get('footer_logo_url', ''));

        return static::mediaUrl($configured !== '' ? $configured : $default, $default);
    }

    public static function themeColors(): array
    {
        $defaults = BrandPalette::themeDefaults();

        return [
            'purple' => (string) static::get('theme_color_purple', $defaults['purple']),
            'purpleDark' => (string) static::get('theme_color_purple_dark', $defaults['purpleDark']),
            'pink' => (string) static::get('theme_color_pink', $defaults['pink']),
            'pinkHover' => (string) static::get('theme_color_pink_hover', $defaults['pinkHover']),
            'amber' => (string) static::get('theme_color_amber', $defaults['amber']),
            'gold' => (string) static::get('theme_color_amber', $defaults['gold']),
        ];
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
