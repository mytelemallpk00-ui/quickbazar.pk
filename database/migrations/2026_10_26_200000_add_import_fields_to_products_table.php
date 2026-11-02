<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'import_source')) {
                $table->string('import_source', 32)->nullable()->after('sort_order');
            }
            if (! Schema::hasColumn('products', 'external_sku')) {
                $table->string('external_sku')->nullable()->after('import_source');
            }
            if (! Schema::hasColumn('products', 'source_url')) {
                $table->string('source_url', 2048)->nullable()->after('external_sku');
            }
        });

    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['import_source', 'external_sku', 'source_url']);
        });
    }
};
