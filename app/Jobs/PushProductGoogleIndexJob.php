<?php

namespace App\Jobs;

use App\Models\GoogleIndexLog;
use App\Models\Product;
use App\Services\GoogleIndex\GoogleIndexException;
use App\Services\GoogleIndex\GoogleIndexService;
use App\Services\GoogleIndex\GoogleIndexUrl;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PushProductGoogleIndexJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $productId) {}

    public function handle(GoogleIndexService $indexing): void
    {
        $product = Product::query()->find($this->productId);
        if (! $product || ! filled($product->slug)) {
            return;
        }

        if (! $indexing->isConfigured()) {
            return;
        }

        try {
            $indexing->publish(
                GoogleIndexUrl::forProduct($product),
                GoogleIndexLog::ACTION_UPDATED,
                $product,
            );
        } catch (GoogleIndexException $e) {
            Log::info('Google Index skipped for product '.$product->id.': '.$e->getMessage());
        }
    }
}
