<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            return;
        }

        Schema::table('categories', function (Blueprint $table) {
            $stringColumns = [
                'icon' => true,
                'nav_icon_color' => true,
                'nav_label' => true,
                'mega_promo_badge' => true,
                'mega_promo_title' => true,
                'mega_promo_button_label' => true,
                'mega_promo_button_url' => true,
                'mega_promo_style' => false,
                'meta_title' => true,
                'meta_keywords' => true,
                'og_title' => true,
                'og_image' => true,
                'canonical_url' => true,
                'robots' => true,
                'seo_focus_keyword' => true,
            ];

            foreach ($stringColumns as $column => $nullable) {
                if (! Schema::hasColumn('categories', $column)) {
                    $col = $table->string($column);
                    if ($nullable) {
                        $col->nullable();
                    }
                    if ($column === 'mega_promo_style') {
                        $col->default('purple');
                    }
                }
            }

            if (! Schema::hasColumn('categories', 'nav_links')) {
                $table->json('nav_links')->nullable();
            }
            if (! Schema::hasColumn('categories', 'mega_promo_text')) {
                $table->text('mega_promo_text')->nullable();
            }
            if (! Schema::hasColumn('categories', 'meta_description')) {
                $table->text('meta_description')->nullable();
            }
            if (! Schema::hasColumn('categories', 'og_description')) {
                $table->text('og_description')->nullable();
            }
            if (! Schema::hasColumn('categories', 'is_featured')) {
                $table->boolean('is_featured')->default(false);
            }
        });
    }

    public function down(): void
    {
        // Non-destructive production fix — no down migration.
    }
};
