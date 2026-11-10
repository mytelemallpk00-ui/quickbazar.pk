<?php

namespace App\Support;

use App\Models\Product;
use App\Models\ProductBundle;

class ProductFrequentlyBoughtTogether
{
    /** @return array<string, mixed>|null */
    public static function payloadFor(Product $host): ?array
    {
        $host->loadMissing(['bundle.bundleItems.product']);

        $bundle = $host->bundle;
        if ($bundle && $bundle->is_active) {
            $payload = $bundle->toFrontendArray();
            if ($payload !== null) {
                return $payload;
            }
        }

        return static::catalogFallback($host);
    }

    /** @return array<string, mixed>|null */
    protected static function catalogFallback(Product $host): ?array
    {
        $companions = Product::query()
            ->when(
                $host->category_id,
                fn ($query) => $query->where('category_id', $host->category_id),
                fn ($query) => $query->where('category', $host->category),
            )
            ->whereKeyNot($host->id)
            ->orderByDesc('in_stock')
            ->orderByDesc('is_flash')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->limit(2)
            ->get();

        if ($companions->isEmpty()) {
            $companions = Product::query()
                ->whereKeyNot($host->id)
                ->orderByDesc('in_stock')
                ->orderBy('sort_order')
                ->limit(2)
                ->get();
        }

        if ($companions->isEmpty()) {
            return null;
        }

        $items = [static::itemFromProduct($host, true)];

        foreach ($companions as $companion) {
            $items[] = static::itemFromProduct($companion, false);
        }

        return [
            'badgeLabel' => 'FREQUENTLY BOUGHT TOGETHER',
            'subtitle' => 'Pair with these bestsellers — select items and add the set to your bag.',
            'discountType' => ProductBundle::DISCOUNT_PERCENT,
            'discountValue' => 10.0,
            'items' => $items,
        ];
    }

    /** @return array<string, mixed> */
    protected static function itemFromProduct(Product $product, bool $required): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => (float) $product->price,
            'image' => static::imageUrl($product),
            'required' => $required,
        ];
    }

    public static function imageUrl(Product $product): string
    {
        $image = trim((string) $product->image);

        return $image !== ''
            ? $image
            : 'https://placehold.co/160x160/5E1F70/FFFFFF?text=Quick+Bazar';
    }
}
