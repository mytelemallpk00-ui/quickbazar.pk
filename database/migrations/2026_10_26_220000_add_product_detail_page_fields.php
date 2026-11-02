<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'detail_heading')) {
                $table->string('detail_heading')->nullable()->after('description');
            }
            if (! Schema::hasColumn('products', 'detail_body')) {
                $table->text('detail_body')->nullable()->after('detail_heading');
            }
            if (! Schema::hasColumn('products', 'detail_highlights')) {
                $table->json('detail_highlights')->nullable()->after('detail_body');
            }
            if (! Schema::hasColumn('products', 'specs_heading')) {
                $table->string('specs_heading')->nullable()->after('detail_highlights');
            }
            if (! Schema::hasColumn('products', 'specs_rows')) {
                $table->json('specs_rows')->nullable()->after('specs_heading');
            }
            if (! Schema::hasColumn('products', 'routine_heading')) {
                $table->string('routine_heading')->nullable()->after('specs_rows');
            }
            if (! Schema::hasColumn('products', 'routine_body')) {
                $table->text('routine_body')->nullable()->after('routine_heading');
            }
            if (! Schema::hasColumn('products', 'video_url')) {
                $table->string('video_url', 500)->nullable()->after('routine_body');
            }
            if (! Schema::hasColumn('products', 'promo_label')) {
                $table->string('promo_label')->nullable()->after('video_url');
            }
            if (! Schema::hasColumn('products', 'units_left')) {
                $table->unsignedSmallInteger('units_left')->nullable()->after('promo_label');
            }
        });

        if (! Schema::hasTable('product_reviews')) {
            Schema::create('product_reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->string('author_name');
                $table->string('author_email')->nullable();
                $table->unsignedTinyInteger('rating');
                $table->string('title')->nullable();
                $table->text('body');
                $table->boolean('is_verified_buyer')->default(false);
                $table->boolean('is_published')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_reviews');

        Schema::table('products', function (Blueprint $table) {
            foreach ([
                'detail_heading',
                'detail_body',
                'detail_highlights',
                'specs_heading',
                'specs_rows',
                'routine_heading',
                'routine_body',
                'video_url',
                'promo_label',
                'units_left',
            ] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
