<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('product_bundles')) {
            Schema::create('product_bundles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('badge_label')->nullable();
                $table->string('subtitle')->nullable();
                $table->string('discount_type', 20)->default('percent');
                $table->decimal('discount_value', 10, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('product_bundle_items')) {
            Schema::create('product_bundle_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_bundle_id')->constrained()->cascadeOnDelete();
                $table->foreignId('product_id')->constrained()->cascadeOnDelete();
                $table->unsignedSmallInteger('sort_order')->default(0);
                $table->timestamps();

                $table->unique(['product_bundle_id', 'product_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_bundle_items');
        Schema::dropIfExists('product_bundles');
    }
};
