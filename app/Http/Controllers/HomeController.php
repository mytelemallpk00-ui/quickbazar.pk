<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\HomepageContent;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $homepage = SiteSettings::homepage();

        $endorsementProductIds = collect(data_get($homepage, 'endorsements.cards', []))
            ->pluck('product_id')
            ->map(fn ($id) => (int) $id)
            ->filter(fn (int $id) => $id > 0)
            ->unique()
            ->values();

        $endorsementProductSlugs = $endorsementProductIds->isEmpty()
            ? collect()
            : Product::query()->whereIn('id', $endorsementProductIds)->pluck('slug', 'id');

        $shopCategories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $homePageCategories = $shopCategories
            ->map(fn (Category $category) => [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
            ])
            ->values();

        [$homeReviewStats, $homeFeaturedReviews] = self::homepageReviewFeed();

        return view('home', [
            'productsCatalog' => Product::catalogForJs(),
            'homepage' => $homepage,
            'shopCategories' => $shopCategories,
            'homePageCategories' => $homePageCategories,
            'flashSpotlightProduct' => HomepageContent::resolveProduct(
                data_get($homepage, 'flash_deals.spotlight_product_id'),
                flashFallback: true,
            ),
            'featuredPromoProduct' => HomepageContent::resolveProduct(
                data_get($homepage, 'featured_promo.product_id'),
            ),
            'endorsementProductSlugs' => $endorsementProductSlugs,
            'homeReviewStats' => $homeReviewStats,
            'homeFeaturedReviews' => $homeFeaturedReviews,
        ]);
    }

    /**
     * Mixed published product-page reviews for the homepage strip (one per product first, then fill).
     *
     * @return array{0: array{avg: float, customers: int, published: int}, 1: Collection<int, ProductReview>}
     */
    private static function homepageReviewFeed(): array
    {
        $publishedQuery = ProductReview::query()->where('is_published', true);

        $publishedCount = (int) $publishedQuery->clone()->count();

        if ($publishedCount > 0) {
            $avg = round((float) $publishedQuery->clone()->avg('rating'), 1);
            $customers = (int) $publishedQuery->clone()->distinct('author_name')->count('author_name');
            if ($customers < 1) {
                $customers = $publishedCount;
            }
        } else {
            $avg = round((float) Product::query()->where('reviews', '>', 0)->avg('rating'), 1);
            $customers = (int) Product::query()->sum('reviews');
        }

        $pool = ProductReview::query()
            ->where('is_published', true)
            ->whereHas('product')
            ->with(['product:id,slug,name,image'])
            ->inRandomOrder()
            ->limit(48)
            ->get();

        $picked = collect();
        $usedProducts = [];

        foreach ($pool as $review) {
            if (isset($usedProducts[$review->product_id])) {
                continue;
            }
            $usedProducts[$review->product_id] = true;
            $picked->push($review);
            if ($picked->count() >= 8) {
                break;
            }
        }

        if ($picked->count() < 8) {
            $extra = ProductReview::query()
                ->where('is_published', true)
                ->whereHas('product')
                ->with(['product:id,slug,name,image'])
                ->whereNotIn('id', $picked->pluck('id'))
                ->latest()
                ->limit(8 - $picked->count())
                ->get();
            $picked = $picked->merge($extra);
        }

        return [
            [
                'avg' => $avg,
                'customers' => max($customers, $publishedCount),
                'published' => $publishedCount,
            ],
            $picked->take(8)->values(),
        ];
    }
}
