<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductBundle;
use App\Models\ProductBundleItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class ProductBundleSeeder extends Seeder
{
    public function run(): void
    {
        $existingHostIds = ProductBundle::query()->pluck('product_id');

        $products = Product::query()
            ->orderByDesc('is_flash')
            ->orderBy('sort_order')
            ->orderByDesc('price')
            ->orderBy('id')
            ->get();

        if ($products->count() < 2) {
            return;
        }

        $created = 0;
        $maxBundles = 12;

        /** @var Collection<int|string, Collection<int, Product>> $byCategory */
        $byCategory = $products->groupBy(fn (Product $p) => $p->category_id ?: 'uncategorized');

        foreach ($byCategory as $group) {
            if ($created >= $maxBundles || $group->count() < 2) {
                continue;
            }

            $hosts = $group->take(min(2, $group->count() - 1));
            foreach ($hosts as $host) {
                if ($created >= $maxBundles) {
                    break;
                }
                if ($existingHostIds->contains($host->id)) {
                    continue;
                }

                $companions = $group
                    ->where('id', '!=', $host->id)
                    ->take($group->count() >= 4 ? 2 : 1)
                    ->values();

                if ($companions->isEmpty()) {
                    continue;
                }

                $discountPercent = $companions->count() >= 2 ? 20.0 : 15.0;
                $categoryLabel = trim((string) ($host->category ?: 'Skincare'));

                $bundle = ProductBundle::query()->create([
                    'product_id' => $host->id,
                    'badge_label' => 'BUNDLE DEAL',
                    'subtitle' => "{$categoryLabel} combo — saath khareedein aur {$discountPercent}% bachayein.",
                    'discount_type' => ProductBundle::DISCOUNT_PERCENT,
                    'discount_value' => $discountPercent,
                    'is_active' => true,
                ]);

                foreach ($companions as $index => $companion) {
                    ProductBundleItem::query()->create([
                        'product_bundle_id' => $bundle->id,
                        'product_id' => $companion->id,
                        'sort_order' => $index,
                    ]);
                }

                $existingHostIds->push($host->id);
                $created++;
            }
        }

        if ($created > 0) {
            return;
        }

        $this->seedPairwiseFallback($products, $existingHostIds, $maxBundles);
    }

    /** @param  Collection<int, Product>  $products */
    protected function seedPairwiseFallback(Collection $products, Collection $existingHostIds, int $maxBundles): void
    {
        $created = 0;
        $list = $products->values();

        for ($i = 0; $i < $list->count() - 1 && $created < min(6, $maxBundles); $i += 2) {
            $host = $list[$i];
            $companion = $list[$i + 1];

            if ($existingHostIds->contains($host->id)) {
                continue;
            }

            $bundle = ProductBundle::query()->create([
                'product_id' => $host->id,
                'badge_label' => 'SAVE WITH BUNDLE DEALS',
                'subtitle' => 'Quick Bazar bestseller pair — add both to your bag and save 15%.',
                'discount_type' => ProductBundle::DISCOUNT_PERCENT,
                'discount_value' => 15.0,
                'is_active' => true,
            ]);

            ProductBundleItem::query()->create([
                'product_bundle_id' => $bundle->id,
                'product_id' => $companion->id,
                'sort_order' => 0,
            ]);

            $existingHostIds->push($host->id);
            $created++;
        }
    }
}
