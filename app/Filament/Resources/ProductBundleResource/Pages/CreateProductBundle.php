<?php

namespace App\Filament\Resources\ProductBundleResource\Pages;

use App\Filament\Resources\ProductBundleResource;
use App\Models\ProductBundle;
use App\Support\ProductFrequentlyBoughtTogether;
use Filament\Resources\Pages\CreateRecord;

class CreateProductBundle extends CreateRecord
{
    protected static string $resource = ProductBundleResource::class;

    protected function afterCreate(): void
    {
        $bundle = $this->getRecord();
        if (! $bundle instanceof ProductBundle || $bundle->bundleItems()->exists()) {
            return;
        }

        $host = $bundle->hostProduct;
        if ($host === null) {
            return;
        }

        $ids = ProductFrequentlyBoughtTogether::suggestedCompanionIds($host, 2);
        foreach (array_values($ids) as $index => $productId) {
            $bundle->bundleItems()->create([
                'product_id' => $productId,
                'sort_order' => $index + 1,
            ]);
        }
    }
}
