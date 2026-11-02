<?php

namespace App\Support;

use App\Models\ProductBundle;
use Illuminate\Support\Collection;

class BundleDealsCatalog
{
    /** @return Collection<int, array<string, mixed>> */
    public static function activeDeals(?int $limit = null): Collection
    {
        $query = ProductBundle::query()
            ->where('is_active', true)
            ->with(['hostProduct', 'bundleItems.product'])
            ->whereHas('hostProduct')
            ->whereHas('bundleItems')
            ->latest('updated_at');

        if ($limit !== null) {
            $query->limit($limit);
        }

        return $query->get()
            ->filter(fn (ProductBundle $bundle) => $bundle->toFrontendArray() !== null)
            ->values()
            ->map(fn (ProductBundle $bundle) => static::mapDeal($bundle));
    }

    /** @return array<string, mixed> */
    public static function mapDeal(ProductBundle $bundle): array
    {
        $subtotal = $bundle->estimatedFullBundleSubtotal();
        $discounted = $bundle->calculateDiscountedTotal($subtotal);
        $savings = $bundle->calculateSavings($subtotal);
        $host = $bundle->hostProduct;
        $companionCount = $bundle->bundleItems->count();

        return [
            'title' => $host?->name ?? 'Bundle deal',
            'subtitle' => $bundle->subtitle ?: 'Frequently bought together — save when you add the full set.',
            'badge' => $bundle->badge_label ?: 'SAVE WITH BUNDLE DEALS',
            'image' => $host?->image,
            'companionCount' => $companionCount,
            'subtotal' => $subtotal,
            'discounted' => $discounted,
            'savings' => $savings,
            'subtotalFormatted' => MoneyFormat::pkr($subtotal),
            'discountedFormatted' => MoneyFormat::pkr($discounted),
            'savingsFormatted' => MoneyFormat::pkr($savings),
            'url' => ($bundle->storefrontUrl() ?? route('home')).'#frequently-bought-together',
        ];
    }
}
