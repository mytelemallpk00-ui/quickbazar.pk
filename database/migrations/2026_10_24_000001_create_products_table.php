<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('category');
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2);
            $table->decimal('rating', 2, 1)->default(0);
            $table->unsignedInteger('reviews')->default(0);
            $table->string('image');
            $table->string('hover_image');
            $table->string('badge')->nullable();
            $table->boolean('is_flash')->default(false);
            $table->string('discount')->nullable();
            $table->unsignedTinyInteger('claimed_percent')->nullable();
            $table->string('claimed_text')->nullable();
            $table->text('description');
            $table->boolean('in_stock')->default(true);
            $table->json('gallery_images')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
