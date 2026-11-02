<?php

namespace App\Jobs;

use App\Models\Product;
use App\Services\ProductAi\ProductAiException;
use App\Services\ProductAi\ProductAiService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class AutoGenerateProductSeoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $productId) {}

    public function handle(ProductAiService $ai): void
    {
        $product = Product::query()->with('productCategory')->find($this->productId);
        if (! $product || (filled($product->meta_title) && filled($product->meta_description))) {
            return;
        }

        try {
            $content = $ai->generate(
                $product->name,
                $product->seo_focus_keyword,
                $product->short_description,
                $product->productCategory?->name,
                ProductAiService::PROVIDER_SMART,
            );
            $product->forceFill($content->toFormState())->saveQuietly();
        } catch (ProductAiException $e) {
            Log::info('Auto SEO skipped for product '.$product->id.': '.$e->getMessage());
        }
    }
}
