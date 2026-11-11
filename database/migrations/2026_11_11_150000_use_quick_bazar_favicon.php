<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    public function up(): void
    {
        $setting = SiteSetting::query()->firstWhere('key', 'favicon_url');
        $current = strtolower(trim((string) ($setting?->value ?? '')));
        $replace = $current === ''
            || str_contains($current, 'beautage')
            || str_contains($current, 'logosquare')
            || str_contains($current, 'shop-pakistan')
            || str_contains($current, 'quick-bazar-logo');

        if ($replace) {
            SiteSetting::query()->updateOrCreate(
                ['key' => 'favicon_url'],
                ['group' => 'site', 'value' => 'images/quick-bazar-favicon.png'],
            );
            Cache::forget('site_settings.all');
        }
    }

    public function down(): void
    {
        //
    }
};
