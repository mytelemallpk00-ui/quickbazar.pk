<?php

use App\Models\SiteSetting;
use App\Support\BrandPalette;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $colors = BrandPalette::themeDefaults();
        $rows = [
            'logo_url' => ['site', 'images/quick-bazar-logo.png'],
            'favicon_url' => ['site', 'images/quick-bazar-logo.png'],
            'footer_logo_url' => ['site', 'images/quick-bazar-logo.png'],
            'theme_color_purple' => ['theme', $colors['purple']],
            'theme_color_purple_dark' => ['theme', $colors['purpleDark']],
            'theme_color_pink' => ['theme', $colors['pink']],
            'theme_color_pink_hover' => ['theme', $colors['pinkHover']],
            'theme_color_amber' => ['theme', $colors['amber']],
        ];

        foreach ($rows as $key => [$group, $value]) {
            SiteSetting::query()->updateOrCreate(
                ['key' => $key],
                ['group' => $group, 'value' => $value],
            );
        }

        Cache::forget('site_settings.all');
    }

    public function down(): void
    {
        //
    }
};
