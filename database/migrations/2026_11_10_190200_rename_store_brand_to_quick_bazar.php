<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'site_settings' => ['value'],
            'products' => ['name', 'description', 'short_description', 'detail_heading', 'detail_body', 'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description'],
            'categories' => ['name', 'description', 'nav_label', 'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description'],
            'cms_pages' => ['title', 'hero_badge', 'hero_title', 'hero_subtitle', 'content_html', 'sections', 'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description'],
            'blog_posts' => ['title', 'snippet', 'content', 'author', 'meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description'],
            'programmatic_seo_landings' => ['headline', 'intro_html', 'meta_title', 'meta_description'],
            'users' => ['name'],
        ];

        foreach ($tables as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $existing = array_values(array_filter(
                $columns,
                fn (string $column): bool => Schema::hasColumn($table, $column)
            ));

            if ($existing === []) {
                continue;
            }

            foreach (DB::table($table)->select(array_merge(['id'], $existing))->get() as $row) {
                $updates = [];
                foreach ($existing as $column) {
                    $value = $row->{$column} ?? null;
                    if (! is_string($value) || $value === '') {
                        continue;
                    }
                    $next = $this->brandify($value);
                    if ($next !== $value) {
                        $updates[$column] = $next;
                    }
                }
                if ($updates !== []) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }

        if (Schema::hasTable('users')) {
            DB::table('users')->where('email', 'admin@beautage.com.pk')->update([
                'name' => 'Quick Bazar Admin',
            ]);
        }

        Cache::forget('site_settings.all');
    }

    public function down(): void
    {
        // Brand rename is not reversed.
    }

    private function brandify(string $text): string
    {
        $text = str_replace(
            ['Shop <span>Pakistan</span>', 'Shop+Pakistan', 'SHOP PAKISTAN', 'Shop Pakistan'],
            ['Quick <span>Bazar</span>', 'Quick+Bazar', 'QUICK BAZAR', 'Quick Bazar'],
            $text
        );

        $text = preg_replace('/(?<![A-Za-z0-9_])BEAUTAGE(?![A-Za-z0-9_])/', 'QUICK BAZAR', $text) ?? $text;
        $text = preg_replace('/(?<![A-Za-z0-9_])Beautage(?![A-Za-z0-9_])/', 'Quick Bazar', $text) ?? $text;

        return $text;
    }
};
