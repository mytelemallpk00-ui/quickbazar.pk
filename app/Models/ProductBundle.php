<?php

namespace App\Models;

use App\Support\ProductFrequentlyBoughtTogether;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductBundle extends Model
{
    public const DISCOUNT_PERCENT = 'percent';

    public const DISCOUNT_FIXED = 'fixed';

    protected $fillable = [
        'product_id',
        'badge_label',
        'subtitle',
        'discount_type',
        'discount_value',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'float',
            'is_active' => 'boolean',
        ];
    }

    /** @return BelongsTo<Product, $this> */
    public function hostProduct(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /** @return HasMany<ProductBundleItem, $this> */
    public function bundleItems(): HasMany
    {
        return $this->hasMany(ProductBundleItem::class)->orderBy('sort_order');
    }

    public static function discountTypeOptions(): array
    {
        return [
            self::DISCOUNT_PERCENT => 'Percentage off combined total',
            self::DISCOUNT_FIXED => 'Fixed amount off combined total',
        ];
    }

    public function calculateDiscountedTotal(float $subtotal): float
    {
        if ($subtotal <= 0) {
            return 0.0;
        }

        if ($this->discount_type === self::DISCOUNT_FIXED) {
            return max(0, round($subtotal - (float) $this->discount_value, 2));
        }

        $percent = min(100, max(0, (float) $this->discount_value));

        return round($subtotal * (1 - ($percent / 100)), 2);
    }

    public function calculateSavings(float $subtotal): float
    {
        return max(0, round($subtotal - $this->calculateDiscountedTotal($subtotal), 2));
    }

    public function estimatedFullBundleSubtotal(): float
    {
        $this->loadMissing(['hostProduct', 'bundleItems.product']);

        $total = (float) ($this->hostProduct?->price ?? 0);
        foreach ($this->bundleItems as $item) {
            $total += (float) ($item->product?->price ?? 0);
        }

        return round($total, 2);
    }

    public function storefrontUrl(): ?string
    {
        $slug = $this->hostProduct?->slug;
        if (! $slug) {
            return null;
        }

        return route('product.show', ['product' => $slug]);
    }

    /** @return array<string, mixed>|null */
    public function toFrontendArray(): ?array
    {
        if (! $this->is_active) {
            return null;
        }

        $host = $this->hostProduct;
        if (! $host) {
            return null;
        }

        $items = [
            [
                'id' => $host->id,
                'name' => $host->name,
                'price' => (float) $host->price,
                'image' => ProductFrequentlyBoughtTogether::imageUrl($host),
                'required' => true,
            ],
        ];

        foreach ($this->bundleItems as $bundleItem) {
            $product = $bundleItem->product;
            if (! $product) {
                continue;
            }

            $items[] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'image' => ProductFrequentlyBoughtTogether::imageUrl($product),
                'required' => false,
            ];
        }

        if (count($items) < 2) {
            return null;
        }

        return [
            'badgeLabel' => $this->badge_label ?: 'SAVE WITH BUNDLE DEALS',
            'subtitle' => $this->subtitle,
            'discountType' => $this->discount_type,
            'discountValue' => (float) $this->discount_value,
            'items' => $items,
        ];
    }
}
