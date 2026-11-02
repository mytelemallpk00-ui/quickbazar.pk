<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('programmatic_seo_landings', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('location')->nullable();
            $table->string('use_case')->nullable();
            $table->string('headline');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('intro_html')->nullable();
            $table->string('robots')->default('index,follow');
            $table->boolean('is_published')->default(true);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });

        if (! Schema::hasTable('categories')) {
            return;
        }

        $categoryId = DB::table('categories')->orderBy('sort_order')->value('id');
        if (! $categoryId) {
            return;
        }

        $now = now();
        DB::table('programmatic_seo_landings')->insert([
            [
                'slug' => 'skincare-karachi',
                'category_id' => $categoryId,
                'location' => 'Karachi',
                'use_case' => 'Daily skincare',
                'headline' => 'Premium skincare in Karachi',
                'meta_title' => 'Buy skincare online in Karachi — Beautage',
                'meta_description' => 'Shop dermatologist-trusted skincare delivered across Karachi. Original products, COD, and fast dispatch from Beautage.',
                'intro_html' => '<p>Discover cleansers, serums, and moisturizers curated for Karachi climate and daily routines.</p>',
                'robots' => 'index,follow',
                'is_published' => true,
                'sort_order' => 10,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'slug' => 'bridal-glow-lahore',
                'category_id' => $categoryId,
                'location' => 'Lahore',
                'use_case' => 'Bridal glow',
                'headline' => 'Bridal glow essentials — Lahore',
                'meta_title' => 'Bridal beauty & glow kits in Lahore | Beautage',
                'meta_description' => 'High-intent bridal skincare and glow products with verified reviews and secure checkout.',
                'intro_html' => '<p>Pre-event routines, brightening serums, and spa-grade masks for Lahore brides and guests.</p>',
                'robots' => 'index,follow',
                'is_published' => true,
                'sort_order' => 20,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('programmatic_seo_landings');
    }
};
