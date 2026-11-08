<?php

namespace Database\Seeders;

use App\Support\SiteSettings;
use Illuminate\Database\Seeder;

class SiteSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            'site_name' => 'Shop Pakistan',
            'site_tagline' => 'Verified products, delivered across Pakistan',
            'support_phone' => '+92 300 5550199',
            'support_email' => 'hello@quickbazar.pk',
            'logo_url' => '/images/beautagelogosquare2.png',
            'theme_color_purple' => '#5E1F70',
            'theme_color_purple_dark' => '#3B0764',
            'theme_color_pink' => '#E025A8',
            'theme_color_pink_hover' => '#C21B8F',
            'theme_color_amber' => '#F59E0B',
            'marquee_items' => [
                ['text' => 'Free Express Worldwide Shipping Over $75'],
                ['text' => 'Luxury Flash Sale: Up to 50% OFF Selected Flagship Items'],
                ['text' => 'Authorized Brand Store • 100% Original Verified Products'],
                ['text' => 'Cash on Delivery Available Across Pakistan'],
            ],
            'footer_about' => 'Crafting high-end lifestyle products designed for modern comfort, sustainability, and uncompromising quality.',
        ];

        foreach ($defaults as $key => $value) {
            SiteSettings::set($key, $value, str_starts_with($key, 'theme_') ? 'theme' : 'site');
        }
    }
}
