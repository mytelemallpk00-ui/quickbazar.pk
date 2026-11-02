<?php

namespace App\Services\ProductImport;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

class ImportSchemaSetup
{
    public static function ensure(): void
    {
        $needsMigrate = ! Schema::hasColumn('products', 'import_source')
            || ! Schema::hasTable('product_import_batches');

        if (! $needsMigrate) {
            return;
        }

        Artisan::call('migrate', [
            '--force' => true,
            '--path' => 'database/migrations/2026_10_26_200000_add_import_fields_to_products_table.php',
        ]);
        Artisan::call('migrate', [
            '--force' => true,
            '--path' => 'database/migrations/2026_10_26_200001_create_product_import_batches_table.php',
        ]);
    }
}
