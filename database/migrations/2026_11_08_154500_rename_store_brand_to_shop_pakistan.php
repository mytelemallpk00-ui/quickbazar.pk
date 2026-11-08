<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('site_settings')) {
            foreach (DB::table('site_settings')->get(['id', 'value']) as $row) {
                if (! is_string($row->value) || $row->value === '') {
                    continue;
                }

                $next = str_replace(
                    ['QuickBazar.pk', 'QuickBazar', 'QUICK BAZAR', 'Quick Bazar'],
                    ['Shop Pakistan', 'Shop Pakistan', 'SHOP PAKISTAN', 'Shop Pakistan'],
                    $row->value,
                );

                if ($next !== $row->value) {
                    DB::table('site_settings')->where('id', $row->id)->update(['value' => $next]);
                }
            }

            Cache::forget('site_settings.all');
        }

        if (Schema::hasTable('users')) {
            DB::table('users')->where('name', 'Quick Bazar Admin')->update([
                'name' => 'Shop Pakistan Admin',
            ]);
        }
    }

    public function down(): void
    {
        // Brand rename is not reversed.
    }
};
