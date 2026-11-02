<?php

namespace App\Filament\Forms;

use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Facades\Storage;

class AdminImageUpload
{
    public const PRESET_SITE_LOGO = 'site_logo';

    public const PRESET_FAVICON = 'favicon';

    public const PRESET_OG = 'og_share';

    public const PRESET_PRODUCT_MAIN = 'product_main';

    public const PRESET_PRODUCT_HOVER = 'product_hover';

    public const PRESET_PRODUCT_GALLERY = 'product_gallery';

    public const PRESET_CATEGORY = 'category_card';

    public const PRESET_BLOG_HERO = 'blog_hero';

    public const PRESET_REVIEW_PHOTO = 'review_photo';

    public const PRESET_ADMIN_AVATAR = 'admin_avatar';

    public const PRESET_HOME_HERO = 'home_hero';

    public const PRESET_HOME_CATEGORY_TILE = 'home_category_tile';

    public const PRESET_HOME_GALLERY = 'home_gallery';

    /** @var array<string, array{directory: string, max_kb: int, hint: string}> */
    private const PRESETS = [
        self::PRESET_SITE_LOGO => [
            'directory' => 'site-branding/logo',
            'max_kb' => 2048,
            'hint' => '512 × 512 px (square). PNG or SVG with transparent background. Max 2 MB.',
        ],
        self::PRESET_FAVICON => [
            'directory' => 'site-branding/favicon',
            'max_kb' => 512,
            'hint' => '64 × 64 px (or 32 × 32 px). PNG or ICO. Max 500 KB.',
        ],
        self::PRESET_OG => [
            'directory' => 'site-branding/og',
            'max_kb' => 3072,
            'hint' => '1200 × 630 px (Open Graph). JPG or PNG. Max 3 MB.',
        ],
        self::PRESET_PRODUCT_MAIN => [
            'directory' => 'catalog/products/main',
            'max_kb' => 4096,
            'hint' => '1200 × 1200 px (square). JPG, PNG, or WebP. Max 4 MB.',
        ],
        self::PRESET_PRODUCT_HOVER => [
            'directory' => 'catalog/products/hover',
            'max_kb' => 4096,
            'hint' => '1200 × 1200 px (square). Same aspect as main image. Max 4 MB.',
        ],
        self::PRESET_PRODUCT_GALLERY => [
            'directory' => 'catalog/products/gallery',
            'max_kb' => 4096,
            'hint' => '1200 × 1200 px each (square). Up to 8 images. Max 4 MB per file.',
        ],
        self::PRESET_CATEGORY => [
            'directory' => 'catalog/categories',
            'max_kb' => 2048,
            'hint' => '800 × 800 px (square). JPG or PNG. Max 2 MB.',
        ],
        self::PRESET_BLOG_HERO => [
            'directory' => 'content/blog',
            'max_kb' => 4096,
            'hint' => '1600 × 900 px (16:9). JPG or PNG. Max 4 MB.',
        ],
        self::PRESET_REVIEW_PHOTO => [
            'directory' => 'reviews/uploads',
            'max_kb' => 4096,
            'hint' => 'Up to 3 photos per review. 1200 × 1200 px square. JPG, PNG, or WebP. Max 4 MB each.',
        ],
        self::PRESET_ADMIN_AVATAR => [
            'directory' => 'admin-profiles/avatars',
            'max_kb' => 2048,
            'hint' => '400 × 400 px (square). JPG or PNG. Max 2 MB.',
        ],
        self::PRESET_HOME_HERO => [
            'directory' => 'site-branding/homepage/hero',
            'max_kb' => 4096,
            'hint' => '800 × 800 px or larger (product/hero shot). JPG, PNG, or WebP. Max 4 MB.',
        ],
        self::PRESET_HOME_CATEGORY_TILE => [
            'directory' => 'site-branding/homepage/category-tiles',
            'max_kb' => 2048,
            'hint' => '400 × 400 px (square). JPG or PNG. Max 2 MB.',
        ],
        self::PRESET_HOME_GALLERY => [
            'directory' => 'site-branding/homepage/gallery',
            'max_kb' => 4096,
            'hint' => '800 × 1000 px (4:5). JPG or PNG. Max 4 MB.',
        ],
    ];

    public static function make(string $name, string $preset, bool $multiple = false): FileUpload
    {
        $config = self::PRESETS[$preset] ?? self::PRESETS[self::PRESET_PRODUCT_MAIN];

        $field = FileUpload::make($name)
            ->image()
            ->disk('public')
            ->directory($config['directory'])
            ->visibility('public')
            ->maxSize($config['max_kb'])
            ->helperText('Recommended upload size: '.$config['hint'])
            ->downloadable()
            ->openable()
            ->afterStateHydrated(function (FileUpload $component, mixed $state) use ($multiple): void {
                if ($multiple) {
                    if (is_string($state) && $state !== '') {
                        $component->state([self::pathForUpload($state) ?? ltrim($state, '/')]);
                    } elseif (! is_array($state)) {
                        $component->state([]);
                    }

                    return;
                }

                if (is_string($state) && $state !== '') {
                    $component->state([self::pathForUpload($state) ?? ltrim($state, '/')]);
                } elseif (! is_array($state)) {
                    $component->state([]);
                }
            })
            ->formatStateUsing(function (mixed $state) use ($multiple): array {
                if ($multiple) {
                    if (is_string($state) && $state !== '') {
                        return array_values(array_filter([self::pathForUpload($state) ?? ltrim($state, '/')]));
                    }

                    if (! is_array($state)) {
                        return [];
                    }

                    return array_values(array_filter(array_map(
                        fn ($item) => self::pathForUpload((string) $item) ?? ltrim((string) $item, '/'),
                        $state,
                    )));
                }

                if (is_string($state) && $state !== '') {
                    return [self::pathForUpload($state) ?? ltrim($state, '/')];
                }

                if (! is_array($state)) {
                    return [];
                }

                return array_values(array_filter(array_map(
                    fn ($item) => self::pathForUpload((string) $item) ?? ltrim((string) $item, '/'),
                    $state,
                )));
            })
            ->dehydrateStateUsing(function (mixed $state) use ($multiple): array|string|null {
                if ($multiple) {
                    if (is_string($state) && $state !== '') {
                        return [self::toStoredUrl($state)];
                    }

                    if (! is_array($state)) {
                        return [];
                    }

                    return array_values(array_filter(array_map(
                        fn ($item) => self::toStoredUrl((string) $item),
                        $state,
                    )));
                }

                if (is_string($state)) {
                    return $state !== '' ? self::toStoredUrl($state) : null;
                }

                if (! is_array($state)) {
                    return null;
                }

                $file = (string) (reset($state) ?: '');

                return $file !== '' ? self::toStoredUrl($file) : null;
            });

        if ($multiple) {
            $field->multiple()->reorderable();
        }

        return $field;
    }

    private static function pathForUpload(?string $value): ?string
    {
        if (! $value) {
            return null;
        }

        if (! str_contains($value, '://') && ! str_starts_with($value, '/')) {
            return $value;
        }

        $path = parse_url($value, PHP_URL_PATH);
        if (! is_string($path)) {
            return null;
        }

        if (str_starts_with($path, '/storage/')) {
            return ltrim(substr($path, strlen('/storage/')), '/');
        }

        return null;
    }

    private static function toStoredUrl(string $path): string
    {
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $relative = parse_url($path, PHP_URL_PATH);
            if (is_string($relative) && str_starts_with($relative, '/storage/')) {
                return $relative;
            }

            return $path;
        }

        if (str_starts_with($path, '/storage/')) {
            return $path;
        }

        if (str_starts_with($path, 'storage/')) {
            return '/'.ltrim($path, '/');
        }

        return '/storage/'.ltrim($path, '/');
    }
}
