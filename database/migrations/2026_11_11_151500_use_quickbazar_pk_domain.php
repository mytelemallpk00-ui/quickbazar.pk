<?php

use App\Models\SiteSetting;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        SiteSetting::query()->orderBy('id')->each(function (SiteSetting $setting): void {
            $value = $setting->value;
            $updated = $this->rewrite($value);
            if ($updated !== $value) {
                $setting->value = $updated;
                $setting->save();
            }
        });

        foreach (['products', 'categories', 'cms_pages', 'blog_posts'] as $table) {
            if (! Schema::hasTable($table) || ! Schema::hasColumn($table, 'canonical_url')) {
                continue;
            }

            DB::table($table)
                ->whereNotNull('canonical_url')
                ->orderBy('id')
                ->select(['id', 'canonical_url'])
                ->each(function (object $row) use ($table): void {
                    $updated = $this->rewrite($row->canonical_url);
                    if ($updated !== $row->canonical_url) {
                        DB::table($table)->where('id', $row->id)->update(['canonical_url' => $updated]);
                    }
                });
        }

        Cache::forget('site_settings.all');
    }

    public function down(): void
    {
        //
    }

    private function rewrite(mixed $value): mixed
    {
        if (is_array($value)) {
            foreach ($value as $key => $item) {
                $value[$key] = $this->rewrite($item);
            }

            return $value;
        }

        if (! is_string($value) || $value === '') {
            return $value;
        }

        return str_replace(
            [
                'https://lightgrey-panther-710834.hostingersite.com',
                'http://lightgrey-panther-710834.hostingersite.com',
                'http://quickbazar.pk',
            ],
            'https://quickbazar.pk',
            $value,
        );
    }
};
