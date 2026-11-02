<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /** @var list<string> */
    private array $seoColumns = [
        'meta_title',
        'meta_description',
        'meta_keywords',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'robots',
        'seo_focus_keyword',
    ];

    public function up(): void
    {
        foreach (['products', 'categories', 'cms_pages', 'blog_posts'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $blueprint) use ($tableName): void {
                if (! Schema::hasColumn($tableName, 'meta_title')) {
                    $blueprint->string('meta_title')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'meta_description')) {
                    $blueprint->text('meta_description')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'meta_keywords')) {
                    $blueprint->string('meta_keywords')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_title')) {
                    $blueprint->string('og_title')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_description')) {
                    $blueprint->text('og_description')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'og_image')) {
                    $blueprint->string('og_image')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'canonical_url')) {
                    $blueprint->string('canonical_url')->nullable();
                }
                if (! Schema::hasColumn($tableName, 'robots')) {
                    $blueprint->string('robots')->default('index,follow');
                }
                if (! Schema::hasColumn($tableName, 'seo_focus_keyword')) {
                    $blueprint->string('seo_focus_keyword')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        foreach (['products', 'categories', 'cms_pages', 'blog_posts'] as $tableName) {
            if (! Schema::hasTable($tableName)) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $blueprint) use ($tableName): void {
                foreach ($this->seoColumns as $column) {
                    if (Schema::hasColumn($tableName, $column)) {
                        $blueprint->dropColumn($column);
                    }
                }
            });
        }
    }
};
