<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'seo_title')) {
                $table->string('seo_title', 120)->nullable()->after('name');
            }
            if (! Schema::hasColumn('products', 'meta_description')) {
                $table->string('meta_description', 320)->nullable();
            }
            if (! Schema::hasColumn('products', 'slug')) {
                $table->string('slug')->nullable()->unique();
            }
            if (! Schema::hasColumn('products', 'seo_tags')) {
                $table->text('seo_tags')->nullable();
            }
            if (! Schema::hasColumn('products', 'is_seo_optimized')) {
                $table->boolean('is_seo_optimized')->default(false)->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            foreach (['seo_title', 'seo_tags', 'is_seo_optimized'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
