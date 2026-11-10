<?php

namespace App\Observers;

use App\Jobs\GenerateSitemapJob;
use App\Models\Product;
use App\Support\SeoAutomationSettings;

class ProductSeoObserver
{
    public function created(Product $product): void
    {
        $this->afterCatalogChange($product);
    }

    public function updated(Product $product): void
    {
        if ($product->wasChanged(['slug', 'price', 'name', 'in_stock', 'meta_title', 'meta_description'])) {
            $this->afterCatalogChange($product);
        }
    }

    public function deleted(Product $product): void
    {
        $this->afterCatalogChange($product);
    }

    protected function afterCatalogChange(Product $product): void
    {
        if (SeoAutomationSettings::sitemapEnabled()) {
            GenerateSitemapJob::dispatch()->delay(now()->addMinutes(2));
        }
    }
}
