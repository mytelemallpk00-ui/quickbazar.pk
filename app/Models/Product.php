<?php

namespace App\Models;

use App\Models\Concerns\HasSeoMeta;
use App\Support\ProductFaqGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasSeoMeta;

    protected $fillable = [
        'category_id',
        'slug',
        'name',
        'category',
        'price',
        'old_price',
        'rating',
        'reviews',
        'image',
        'hover_image',
        'badge',
        'is_flash',
        'discount',
        'claimed_percent',
        'claimed_text',
        'description',
        'short_description',
        'in_stock',
        'stock_quantity',
        'reorder_cycle_days',
        'gallery_images',
        'sort_order',
        'import_source',
        'external_sku',
        'source_url',
        'detail_heading',
        'detail_body',
        'detail_highlights',
        'specs_heading',
        'specs_rows',
        'routine_heading',
        'routine_body',
        'faq_items',
        'video_url',
        'promo_label',
        'units_left',
        'seo_title',
        'seo_tags',
        'is_seo_optimized',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'float',
            'old_price' => 'float',
            'rating' => 'float',
            'is_flash' => 'boolean',
            'in_stock' => 'boolean',
            'stock_quantity' => 'integer',
            'reorder_cycle_days' => 'integer',
            'gallery_images' => 'array',
            'detail_highlights' => 'array',
            'specs_rows' => 'array',
            'faq_items' => 'array',
            'units_left' => 'integer',
            'is_seo_optimized' => 'boolean',
        ];
    }

    /** @return HasOne<ProductBundle, $this> */
    public function bundle(): HasOne
    {
        return $this->hasOne(ProductBundle::class);
    }

    public function activeBundle(): ?ProductBundle
    {
        $bundle = $this->bundle;
        if (! $bundle || ! $bundle->is_active) {
            return null;
        }

        return $bundle;
    }

    /** @return HasMany<ProductReview, $this> */
    public function productReviews(): HasMany
    {
        return $this->hasMany(ProductReview::class);
    }

    /** @return HasMany<ProductReview, $this> */
    public function publishedReviews(): HasMany
    {
        return $this->productReviews()->where('is_published', true);
    }

    public function refreshReviewAggregates(): void
    {
        $query = $this->productReviews()->where('is_published', true);
        $count = (int) $query->count();
        $average = $count > 0 ? round((float) $query->avg('rating'), 1) : 0.0;

        $this->updateQuietly([
            'reviews' => $count,
            'rating' => $average,
        ]);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function productCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    protected static function booted(): void
    {
        static::saving(function (Product $product): void {
            if ($product->category_id) {
                $name = Category::query()->whereKey($product->category_id)->value('name');
                if ($name) {
                    $product->category = $name;
                }
            }

            if (blank($product->slug) && filled($product->name)) {
                $product->slug = Str::slug($product->name);
            }

            if (
                Schema::hasColumn('products', 'faq_items')
                && blank($product->faq_items)
                && filled($product->name)
            ) {
                $product->faq_items = ProductFaqGenerator::forProduct($product);
            }
        });
    }

    /** @return array<int, array<string, mixed>> */
    public static function catalogForJs(): array
    {
        return static::query()
            ->with('productCategory')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product) => $product->toHomeCatalogArray())
            ->values()
            ->all();
    }

    /** @return array<int, array<string, mixed>> */
    public static function collectionForJs(): array
    {
        return static::query()
            ->with('productCategory')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Product $product) => $product->toCollectionCatalogArray())
            ->values()
            ->all();
    }

    /** @return array<string, mixed> */
    public function toHomeCatalogArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'categoryId' => $this->category_id,
            'categorySlug' => $this->productCategory?->slug,
            'category' => $this->productCategory?->name ?? $this->category,
            'price' => $this->price,
            'oldPrice' => $this->old_price,
            'rating' => $this->rating,
            'reviews' => $this->reviews,
            'image' => $this->image,
            'hoverImage' => $this->hover_image,
            'badge' => $this->badge,
            'flash' => $this->is_flash,
            'discount' => $this->discount,
            'claimedPercent' => $this->claimed_percent,
            'claimedText' => $this->claimed_text,
            'desc' => $this->description,
            'short' => $this->cardExcerpt(),
        ];
    }

    public function cardExcerpt(int $limit = 110): string
    {
        $text = trim(preg_replace('/\s+/u', ' ', strip_tags((string) ($this->short_description ?: $this->description ?: ''))) ?? '');

        return Str::limit($text, $limit, '…');
    }

    /** @return array<string, mixed> */
    public function toCollectionCatalogArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'categoryId' => $this->category_id,
            'categorySlug' => $this->productCategory?->slug,
            'category' => $this->productCategory?->name ?? $this->category,
            'price' => $this->price,
            'oldPrice' => $this->old_price,
            'rating' => $this->rating,
            'reviews' => $this->reviews,
            'image' => $this->image,
            'hoverImage' => $this->hover_image,
            'badge' => $this->badge,
            'inStock' => $this->in_stock,
            'desc' => $this->description,
            'short' => $this->cardExcerpt(),
        ];
    }

    /** @return array<string, mixed> */
    public function toDetailCatalogArray(): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'name' => $this->name,
            'category' => $this->category,
            'price' => $this->price,
            'oldPrice' => $this->old_price,
            'image' => $this->image,
            'badge' => $this->badge,
            'desc' => $this->description,
            'short' => $this->cardExcerpt(),
            'inStock' => (bool) $this->in_stock,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function relatedForJs(int $limit = 6): array
    {
        return static::query()
            ->where('category', $this->category)
            ->whereKeyNot($this->id)
            ->orderBy('sort_order')
            ->limit($limit)
            ->get()
            ->map(fn (Product $product) => [
                'id' => $product->id,
                'slug' => $product->slug,
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'oldPrice' => $product->old_price,
                'rating' => $product->rating,
                'reviews' => $product->reviews,
                'inStock' => (bool) $product->in_stock,
                'image' => $product->image,
                'hoverImage' => $product->hover_image,
                'badge' => $product->badge,
                'short' => $product->cardExcerpt(),
            ])
            ->values()
            ->all();
    }
}
