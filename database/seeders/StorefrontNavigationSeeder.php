<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Support\SiteSettings;
use Illuminate\Database\Seeder;

class StorefrontNavigationSeeder extends Seeder
{
    public function run(): void
    {
        if (Category::query()->exists()) {
            return;
        }

        $categories = [
            [
                'name' => 'Skincare & Serums',
                'slug' => 'skincare',
                'icon' => 'droplet',
                'nav_icon_color' => 'text-beautage-pink',
                'sort_order' => 10,
                'nav_links' => [
                    ['label' => 'Botanical Youth Renewal Serum', 'url' => 'skincare'],
                    ['label' => 'Hyaluronic Hydration Boost', 'url' => 'skincare'],
                    ['label' => 'Gold Eye Contour Elixir', 'url' => 'skincare'],
                    ['label' => 'Vitamin C Brightening', 'url' => 'skincare'],
                ],
                'mega_promo_badge' => 'Bestseller Elixir',
                'mega_promo_title' => 'Botanical Youth Serum',
                'mega_promo_text' => 'Organic rose stem cells & hyaluronic acid.',
                'mega_promo_button_label' => 'Shop Skincare',
                'mega_promo_button_url' => '/collections?cat=skincare',
                'mega_promo_style' => 'pink',
            ],
            [
                'name' => 'Beauty Tech',
                'slug' => 'beauty-tech',
                'icon' => 'zap',
                'nav_icon_color' => 'text-purple-600',
                'sort_order' => 20,
                'nav_links' => [
                    ['label' => 'Lumina Pro LED Mask', 'url' => '/products/lumina-pro-led-mask'],
                    ['label' => 'Microcurrent Wand', 'url' => 'beauty-tech'],
                    ['label' => 'Ultrasonic Pore Cleaner', 'url' => 'beauty-tech'],
                ],
                'mega_promo_style' => 'dark',
                'mega_promo_badge' => 'Flagship Device',
                'mega_promo_title' => 'Lumina Pro Mask',
                'mega_promo_text' => 'Medical-grade photon light therapy.',
                'mega_promo_button_label' => 'Explore Tech',
                'mega_promo_button_url' => '/products/lumina-pro-led-mask',
            ],
            [
                'name' => 'Hair Care & Styling',
                'slug' => 'hair-care',
                'icon' => 'scissors',
                'nav_icon_color' => 'text-amber-500',
                'sort_order' => 30,
                'nav_links' => [
                    ['label' => 'Natural Hair Oils', 'url' => 'hair-care'],
                    ['label' => 'Keratin Repair Shampoo', 'url' => 'hair-care'],
                    ['label' => 'Ionic Hair Styler Pro', 'url' => 'hair-care'],
                ],
                'mega_promo_style' => 'purple',
            ],
            [
                'name' => 'Luxury Fragrance',
                'slug' => 'fragrance',
                'icon' => 'flower-2',
                'nav_icon_color' => 'text-rose-500',
                'sort_order' => 40,
                'nav_links' => [
                    ['label' => 'Velvet Blossom EDP', 'url' => 'fragrance'],
                    ['label' => 'Amber Royale', 'url' => 'fragrance'],
                ],
                'mega_promo_style' => 'rose',
            ],
            [
                'name' => 'Wellness & Spa',
                'slug' => 'wellness',
                'icon' => 'heart-pulse',
                'nav_icon_color' => 'text-emerald-500',
                'sort_order' => 50,
                'nav_links' => [
                    ['label' => 'Aromatherapy Oils', 'url' => 'wellness'],
                    ['label' => 'Organic Body Scrubs', 'url' => 'wellness'],
                ],
                'mega_promo_style' => 'emerald',
            ],
        ];

        $ids = [];
        foreach ($categories as $data) {
            $ids[] = Category::query()->create($data)->id;
        }

        if (SiteSettings::headerNavigation() !== []) {
            return;
        }

        SiteSettings::setHeaderNavigation([
            'collections_category_ids' => array_slice($ids, 0, 3),
            'collections_featured' => [
                'badge' => 'Featured Innovation',
                'title' => 'Lumina Pro Mask',
                'text' => '7-spectrum targeted LED dermal care.',
                'button_label' => 'Explore Device',
                'button_url' => '/products/lumina-pro-led-mask',
            ],
            'main_nav_items' => [
                ['type' => 'home'],
                ['type' => 'category', 'category_id' => $ids[0], 'show_dropdown' => true],
                ['type' => 'category', 'category_id' => $ids[1], 'show_dropdown' => true],
                ['type' => 'category', 'category_id' => $ids[2], 'show_dropdown' => true],
                ['type' => 'category', 'category_id' => $ids[3], 'show_dropdown' => true],
                ['type' => 'category', 'category_id' => $ids[4], 'show_dropdown' => true],
                [
                    'type' => 'bundles',
                ],
            ],
        ]);
    }
}
