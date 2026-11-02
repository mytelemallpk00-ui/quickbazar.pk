<?php

namespace App\Support;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Str;

class HomepageContent
{
    /** @return array<string, mixed> */
    public static function defaults(): array
    {
        return [
            'hero' => [
                'enabled' => true,
                'background_from' => null,
                'background_to' => null,
                'badge' => 'NEW BEAUTAGE TECH RELEASE',
                'title_line1' => 'Lumina Pro 7-Spectrum',
                'title_accent' => 'LED Therapy Mask',
                'description' => 'Medical-grade dermal light therapy engine. Targets collagen revival, deep pigment balancing, and targeted cellular radiance.',
                'price_label' => 'Special Intro Price',
                'price_display' => '249',
                'compare_price_display' => '349',
                'button_label' => 'Reserve Order',
                'product_id' => null,
                'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=600',
                'image_alt' => 'Lumina Pro Mask',
            ],
            'promo_cards' => [
                [
                    'style' => 'light',
                    'badge' => 'Bestseller',
                    'title' => 'Botanical Youth Serum',
                    'description' => '100% Organic Hyaluronic Acid & Rose Stem Cells.',
                    'price_display' => '89',
                    'button_label' => 'Add To Bag',
                    'button_style' => 'text',
                    'product_id' => null,
                    'image_url' => '',
                ],
                [
                    'style' => 'gradient',
                    'badge' => 'Save 25%',
                    'title' => 'Sculpting Microcurrent Wand',
                    'description' => 'Tone & lift facial muscles with targeted EMS energy.',
                    'price_display' => '129',
                    'button_label' => 'Claim Offer',
                    'button_style' => 'solid',
                    'product_id' => null,
                    'image_url' => '',
                ],
            ],
            'category_intro' => [
                'badge' => 'BEAUTAGE FLAGSHIP STORE • 100% ORIGINAL & VERIFIED PRODUCTS',
                'heading' => "Pakistan's Best Beauty Store: Shop Smart, Glow Gold Every Day",
                'description' => 'Browse thousands of authentic beauty essentials at the best online shopping store in Pakistan, from professional LED devices and organic botanical serums to luxury fragrances and hair care rituals, all in one trusted place.',
                'trust_badges' => [
                    ['icon' => 'shield-check', 'label' => 'Authorized Brand Stores'],
                    ['icon' => 'rotate-ccw', 'label' => 'Hassle-Free Returns'],
                    ['icon' => 'truck', 'label' => 'Cash on Delivery Available'],
                ],
            ],
            'category_tiles' => [
                [
                    'label' => 'Skincare',
                    'filter_value' => 'Skincare',
                    'collection_slug' => 'skincare',
                    'image_url' => 'https://images.unsplash.com/photo-1620916566398-39f1143ab7be?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Beauty Tech',
                    'filter_value' => 'Beauty Tech',
                    'collection_slug' => 'beauty-tech',
                    'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Hair Care',
                    'filter_value' => 'Haircare',
                    'collection_slug' => 'hair-care',
                    'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Fragrance',
                    'filter_value' => 'Fragrance',
                    'collection_slug' => 'fragrance',
                    'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Wellness & Spa',
                    'filter_value' => 'Wellness',
                    'collection_slug' => 'wellness',
                    'image_url' => 'https://images.unsplash.com/photo-1608248597359-f5383a886f49?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'Organic Serums',
                    'filter_value' => 'Skincare',
                    'collection_slug' => 'skincare',
                    'image_url' => 'https://images.unsplash.com/photo-1535585209827-a15fcdbc4c2d?auto=format&fit=crop&q=80&w=300',
                    'is_view_all' => false,
                ],
                [
                    'label' => 'View All Products',
                    'filter_value' => 'all',
                    'collection_slug' => '',
                    'image_url' => '',
                    'is_view_all' => true,
                ],
            ],
            'showcase' => [
                'badge' => 'Curated Departments',
                'heading' => 'Shop by Category Showcase',
                'description' => 'Explore our signature lines engineered for targeted elegance and results.',
            ],
            'flash_deals' => [
                'badge' => 'TIME-SENSITIVE LUXURY FLASH DEALS',
                'title' => 'Exclusive Flash Vault',
                'title_badge' => 'Up to 50% OFF',
                'description' => 'Lock in verified flagship skincare, advanced dermal tech, and artisanal fragrances at unmatched introductory prices before inventory depletion.',
                'spotlight_product_id' => null,
                'spotlight_badge' => 'Spotlight Deal',
            ],
            'aesthetics_gallery' => [
                'badge' => 'FLAGSHIP COLLECTIONS',
                'heading' => 'Explore Signature Aesthetics',
                'description' => 'Immerse yourself in our curated visual gallery highlighting premium craftsmanship and organic formulations.',
                'cards' => [
                    ['badge' => 'Pure Formulation', 'title' => 'Organic Botanicals', 'description' => 'Sustainably harvested plant extracts.', 'collection_slug' => 'skincare', 'image_url' => 'https://images.unsplash.com/photo-1608248597359-f5383a886f49?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Advanced Dermal', 'title' => 'Photon LED Tech', 'description' => 'Medical-grade light therapy at home.', 'collection_slug' => 'beauty-tech', 'image_url' => 'https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Signature Scent', 'title' => 'Artisanal Fragrances', 'description' => 'Enchanting notes for lasting elegance.', 'collection_slug' => 'fragrance', 'image_url' => 'https://images.unsplash.com/photo-1541643600914-78b084683601?auto=format&fit=crop&q=80&w=600'],
                    ['badge' => 'Ionic Styling', 'title' => 'Luxury Hair Care', 'description' => 'Mirror shine and silky repair rituals.', 'collection_slug' => 'haircare', 'image_url' => 'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=600'],
                ],
            ],
            'featured_promo' => [
                'eyebrow' => 'Dermal Innovation',
                'title' => 'Thermal Cryo Sculpting Engine',
                'description' => 'Dual-action cryo-cooling and deep thermal vibration for pore tightening, lymphatic drainage, and immediate jawline definition.',
                'product_id' => null,
                'visual_title' => 'Beautage Thermal Cryo Wand',
                'visual_subtitle' => 'Ergonomic Rose-Gold Finish',
                'visual_badge' => 'Clinical Grade',
                'image_url' => '',
            ],
            'trending' => [
                'badge' => 'Curated Selection',
                'heading' => 'Trending Beauty & Wellness Catalog',
                'description' => 'High-performance formulations & devices designed to elevate your elegance',
            ],
            'endorsements' => [
                'badge' => 'Verified Stars & Icons',
                'heading' => 'Celebrity Endorsement',
                'description' => 'Watch live video testimonials, personal routines, and proven clinical results shared by our beloved stars and beauty experts.',
                'cards' => [
                    ['name' => 'Fahad Sheikh', 'badge' => 'Hair Growth', 'quote' => 'Alopecia & bald patches cured with hair growth bundle in 4 weeks.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Merium Pervaiz', 'badge' => 'Live Demo', 'quote' => 'Live usage & results of hair dye shampoo on her husband & father.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Humaima Malick', 'badge' => 'At-Home Facial', 'quote' => 'Look how our glitter mask & face polish gave complete at-home facial.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Dananeer Mobeen', 'badge' => 'Hair Care', 'quote' => 'Weekend pawri with champi of our bestseller hair growth bundle!', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Saboor Aly', 'badge' => 'Skin Glow', 'quote' => 'Saboor always prefers ubtan on expensive facials once a week application gives extreme glowing spotless skin.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1517841905240-472988babdf9?auto=format&fit=crop&q=80&w=500'],
                    ['name' => 'Minal Khan', 'badge' => 'Pore Care', 'quote' => 'Minal Khan got super big pores which got completely vanished in 4 weeks by niacinamide & rose gel.', 'video_url' => '', 'product_id' => null, 'image_url' => 'https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&q=80&w=500'],
                ],
            ],
        ];
    }

    /** @param  array<string, mixed>  $stored */
    public static function mergeWithDefaults(array $stored): array
    {
        $defaults = static::defaults();

        $merged = array_replace_recursive($defaults, $stored);

        if (! is_array($merged['promo_cards'] ?? null) || $merged['promo_cards'] === []) {
            $merged['promo_cards'] = $defaults['promo_cards'];
        }

        if (! is_array($merged['category_tiles'] ?? null) || $merged['category_tiles'] === []) {
            $merged['category_tiles'] = $defaults['category_tiles'];
        }

        if (! is_array($merged['category_intro']['trust_badges'] ?? null) || $merged['category_intro']['trust_badges'] === []) {
            $merged['category_intro']['trust_badges'] = $defaults['category_intro']['trust_badges'];
        }

        foreach (['aesthetics_gallery.cards', 'endorsements.cards'] as $path) {
            if (! is_array(data_get($merged, $path)) || data_get($merged, $path) === []) {
                data_set($merged, $path, data_get($defaults, $path, []));
            }
        }

        return $merged;
    }

    /** @param  array<string, mixed>  $incoming
     * @param  array<string, mixed>  $previous */
    public static function preserveImageFields(array $incoming, array $previous): array
    {
        $heroImage = data_get($incoming, 'hero.image_url');
        if (blank($heroImage)) {
            data_set($incoming, 'hero.image_url', data_get($previous, 'hero.image_url'));
        }

        $tiles = data_get($incoming, 'category_tiles', []);
        if (is_array($tiles)) {
            foreach ($tiles as $index => $tile) {
                if (! is_array($tile)) {
                    continue;
                }
                if (! empty($tile['is_view_all'])) {
                    continue;
                }
                if (blank($tile['image_url'] ?? null)) {
                    $prev = data_get($previous, "category_tiles.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "category_tiles.{$index}.image_url", $prev);
                    }
                }
            }
        }

        $promos = data_get($incoming, 'promo_cards', []);
        if (is_array($promos)) {
            foreach ($promos as $index => $card) {
                if (! is_array($card)) {
                    continue;
                }
                if (blank($card['image_url'] ?? null)) {
                    $prev = data_get($previous, "promo_cards.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "promo_cards.{$index}.image_url", $prev);
                    }
                }
            }
        }

        foreach (['aesthetics_gallery.cards', 'endorsements.cards'] as $path) {
            $items = data_get($incoming, $path, []);
            if (! is_array($items)) {
                continue;
            }
            foreach ($items as $index => $item) {
                if (! is_array($item)) {
                    continue;
                }
                if (blank($item['image_url'] ?? null)) {
                    $prev = data_get($previous, "{$path}.{$index}.image_url");
                    if ($prev) {
                        data_set($incoming, "{$path}.{$index}.image_url", $prev);
                    }
                }
            }
        }

        $featuredImage = data_get($incoming, 'featured_promo.image_url');
        if (blank($featuredImage)) {
            data_set($incoming, 'featured_promo.image_url', data_get($previous, 'featured_promo.image_url'));
        }

        return $incoming;
    }

    public static function resolveProduct(mixed $productId, bool $flashFallback = false): ?Product
    {
        $id = (int) $productId;
        if ($id > 0) {
            return Product::query()->find($id);
        }

        if ($flashFallback) {
            return Product::query()
                ->where('is_flash', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->first();
        }

        return null;
    }

    /** @param  array<string, mixed>  $tile */
    public static function resolveTileCategorySlug(array $tile): ?string
    {
        $slug = trim((string) ($tile['collection_slug'] ?? ''));
        if ($slug !== '') {
            return $slug;
        }

        $needles = array_values(array_unique(array_filter([
            $tile['label'] ?? null,
            $tile['filter_value'] ?? null,
        ], fn ($value) => is_string($value) && $value !== '' && $value !== 'all')));

        foreach ($needles as $needle) {
            $matched = static::matchCategorySlug($needle);
            if ($matched) {
                return $matched;
            }
        }

        return null;
    }

    public static function matchCategorySlug(string $needle): ?string
    {
        $needle = trim($needle);
        if ($needle === '') {
            return null;
        }

        $needleSlug = Str::slug($needle);
        $needleCompact = strtolower(preg_replace('/[^a-z0-9]+/i', '', $needle) ?? '');

        $categories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        foreach ($categories as $category) {
            $name = (string) $category->name;
            $navLabel = (string) ($category->nav_label ?? '');
            $nameSlug = Str::slug($name);
            $nameCompact = strtolower(preg_replace('/[^a-z0-9]+/i', '', $name) ?? '');

            if ($category->slug === $needleSlug || $nameSlug === $needleSlug) {
                return $category->slug;
            }

            if ($needleCompact !== '' && ($nameCompact === $needleCompact || str_contains($nameCompact, $needleCompact) || str_contains($needleCompact, $nameCompact))) {
                return $category->slug;
            }

            if (stripos($name, $needle) !== false || ($navLabel !== '' && stripos($navLabel, $needle) !== false)) {
                return $category->slug;
            }
        }

        return null;
    }

    /** @param  array<string, mixed>  $tile */
    public static function tileCollectionUrl(array $tile): string
    {
        if (! empty($tile['is_view_all']) || ($tile['filter_value'] ?? '') === 'all') {
            return route('collections');
        }

        $slug = static::resolveTileCategorySlug($tile);
        if ($slug) {
            return route('collections', ['cat' => $slug]);
        }

        return route('collections');
    }

    public static function imageUrl(?string $path, string $fallbackAlt = ''): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return 'https://placehold.co/400x400/5E1F70/FFFFFF?text='.urlencode($fallbackAlt ?: 'Image');
        }

        return SiteSettings::assetUrl($path, 'images/beautagelogosquare2.png');
    }
}
