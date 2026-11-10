<?php

use App\Support\HomepageContent;
use App\Support\SiteSettings;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        SiteSettings::set('marquee_items', [
            ['text' => '100% Certified Authentic Skincare & Halal Health Essentials'],
            ['text' => 'Free Nationwide Delivery on Orders Over Rs. 2,599'],
            ['text' => 'Cash on Delivery Available'],
            ['text' => 'Dermatologist Approved Formulas'],
        ], 'theme');

        SiteSettings::set('site_tagline', 'Authentic skin & health wellness', 'site');

        $content = SiteSettings::get('homepage_content', []);
        if (! is_array($content)) {
            $content = [];
        }
        $defaults = HomepageContent::defaults();
        foreach (['top_bar', 'slides', 'trust_row', 'shop_pills', 'showcase_goals', 'featured_cards', 'brand_cards'] as $key) {
            $content[$key] = $defaults[$key];
        }
        SiteSettings::setHomepage($content);

        $nav = SiteSettings::headerNavigation();
        $nav['collections_button_label'] = 'Shop All Collections';
        $nav['topbar_text'] = 'Free Nationwide Delivery on Orders Over Rs. 2,599';
        $nav['main_nav_items'] = [
            ['type' => 'custom', 'label' => 'Bundle Offers (-30%)', 'icon' => 'zap', 'icon_color' => 'text-amber-200', 'url' => '/exclusive-offers', 'highlight' => true],
            ['type' => 'custom', 'label' => 'Face Serums & Cleansers', 'icon' => 'droplet', 'icon_color' => 'text-emerald-200', 'url' => '/collections?cat=skincare', 'highlight' => false],
            ['type' => 'custom', 'label' => 'Daily Sunscreens SPF 50+', 'icon' => 'sun', 'icon_color' => 'text-amber-200', 'url' => '/collections?cat=skincare', 'highlight' => false],
            ['type' => 'custom', 'label' => 'Certified Multivitamins', 'icon' => 'pill', 'icon_color' => 'text-emerald-200', 'url' => '/collections?cat=wellness-spa', 'highlight' => false],
            ['type' => 'custom', 'label' => 'Baby & Mom Care', 'icon' => 'baby', 'icon_color' => 'text-emerald-200', 'url' => '/collections', 'highlight' => false],
            ['type' => 'custom', 'label' => 'Track Order', 'icon' => 'map-pin', 'icon_color' => 'text-slate-200', 'url' => '/track-order', 'highlight' => false],
            ['type' => 'custom', 'label' => 'Help & Returns', 'icon' => 'life-buoy', 'icon_color' => 'text-slate-200', 'url' => '/help', 'highlight' => false],
        ];
        SiteSettings::setHeaderNavigation($nav);
    }

    public function down(): void
    {
    }
};
