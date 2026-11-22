<?php

namespace App\Filament\Forms;

use App\Support\SiteSettings;
use Filament\Forms\Components\FileUpload;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

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

    public const PRESET_HOME_BANNER = 'home_banner';

    public const PRESET_HOME_CATEGORY_TILE = 'home_category_tile';

    public const PRESET_HOME_GALLERY = 'home_gallery';

    /**
     * Crop ratios for the free in-admin editor. Logo and favicon stay out
     * because those uploads can be SVG or ICO, which the crop tool cannot edit.
     *
     * @var array<string, list<string>>
     */
    private const EDITOR_RATIOS = [
        self::PRESET_OG => ['1.91:1'],
        self::PRESET_PRODUCT_MAIN => ['1:1'],
        self::PRESET_PRODUCT_HOVER => ['1:1'],
        self::PRESET_PRODUCT_GALLERY => ['1:1'],
        self::PRESET_CATEGORY => ['1:1'],
        self::PRESET_BLOG_HERO => ['16:9'],
        self::PRESET_REVIEW_PHOTO => ['1:1'],
        self::PRESET_ADMIN_AVATAR => ['1:1'],
        self::PRESET_HOME_HERO => ['1:1'],
        self::PRESET_HOME_BANNER => ['20:7', '1600:560'],
        self::PRESET_HOME_CATEGORY_TILE => ['1:1'],
        self::PRESET_HOME_GALLERY => ['4:5'],
    ];

    /** @var array<string, array{directory: string, max_kb: int, hint: string}> */
    private const PRESETS = [
        self::PRESET_SITE_LOGO => [
            'directory' => 'site-branding/logo',
            'max_kb' => 2048,
            'hint' => '1400 × 430 px, wide PNG, transparent background. Max 2 MB.',
        ],
        self::PRESET_FAVICON => [
            'directory' => 'site-branding/favicon',
            'max_kb' => 512,
            'hint' => '512 × 512 px square PNG. Max 500 KB.',
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
        self::PRESET_HOME_BANNER => [
            'directory' => 'site-branding/homepage/banners',
            'max_kb' => 4096,
            'hint' => '1600 × 560 px. One wide banner per slide. JPG, PNG, or WebP. Max 4 MB.',
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
            ->hint('Size: '.$config['hint'])
            ->helperText('Upload size: '.$config['hint'].(isset(self::EDITOR_RATIOS[$preset]) ? ' Use the pencil icon to crop, rotate, or flip.' : ''))
            ->downloadable()
            ->openable()
            ->formatStateUsing(function (mixed $state) use ($multiple): array {
                $paths = self::existingDiskPaths($state);
                if (! $multiple && $paths !== []) {
                    $paths = [end($paths)];
                }

                $keyed = [];
                foreach ($paths as $path) {
                    $keyed[(string) Str::uuid()] = $path;
                }

                return $keyed;
            })
            ->dehydrateStateUsing(function (mixed $state) use ($multiple): array|string|null {
                $stored = [];
                foreach (self::existingDiskPaths($state) as $path) {
                    $stored[] = self::toStoredUrl($path);
                }

                if ($multiple) {
                    return $stored;
                }

                return $stored === [] ? null : $stored[array_key_last($stored)];
            });

        if ($multiple) {
            $field->multiple()->reorderable();
        }

        if (isset(self::EDITOR_RATIOS[$preset])) {
            $field
                ->imageEditor()
                ->imageEditorMode(1)
                ->imageEditorAspectRatios(self::EDITOR_RATIOS[$preset]);
        }

        return $field;
    }

    /** @return list<string> */
    private static function existingDiskPaths(mixed $state): array
    {
        $paths = [];
        foreach (Arr::wrap($state) as $item) {
            if ($item instanceof TemporaryUploadedFile || ! is_string($item)) {
                continue;
            }

            $diskPath = SiteSettings::fileUploadDiskPath($item);
            if ($diskPath === null || $diskPath === '' || str_contains($diskPath, 'livewire-tmp')) {
                continue;
            }

            try {
                if (! Storage::disk('public')->exists($diskPath)) {
                    continue;
                }
            } catch (\Throwable) {
                continue;
            }

            $paths[] = $diskPath;
        }

        return array_values(array_unique($paths));
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
