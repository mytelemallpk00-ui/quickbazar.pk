<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->text('description')->nullable();
                $table->string('image_url')->nullable();
                $table->string('icon')->nullable();
                $table->string('nav_icon_color')->nullable();
                $table->string('nav_label')->nullable();
                $table->json('nav_links')->nullable();
                $table->string('mega_promo_badge')->nullable();
                $table->string('mega_promo_title')->nullable();
                $table->text('mega_promo_text')->nullable();
                $table->string('mega_promo_button_label')->nullable();
                $table->string('mega_promo_button_url')->nullable();
                $table->string('mega_promo_style')->default('purple');
                $table->boolean('is_featured')->default(false);
                $table->boolean('is_active')->default(true);
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->string('meta_title')->nullable();
                $table->text('meta_description')->nullable();
                $table->string('meta_keywords')->nullable();
                $table->string('og_title')->nullable();
                $table->text('og_description')->nullable();
                $table->string('og_image')->nullable();
                $table->string('canonical_url')->nullable();
                $table->string('robots')->nullable();
                $table->string('seo_focus_keyword')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
