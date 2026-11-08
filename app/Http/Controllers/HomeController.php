<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductReview;
use App\Support\HomepageContent;
use App\Support\LiveChatSettings;
use App\Support\MoneyFormat;
use App\Support\SiteSettings;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $homepage = SiteSettings::homepage();
        $hero = is_array($homepage['hero'] ?? null) ? $homepage['hero'] : [];
        $flash = is_array($homepage['flash_deals'] ?? null) ? $homepage['flash_deals'] : [];
        $trendingCopy = is_array($homepage['trending'] ?? null) ? $homepage['trending'] : [];

        $featuredCategories = Category::query()
            ->where('is_active', true)
            ->withCount('products')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get();

        $trendingProducts = $this->trendingProducts();

        $promoProduct = HomepageContent::resolveProduct(data_get($homepage, 'hero.product_id'))
            ?? HomepageContent::resolveProduct(data_get($homepage, 'featured_promo.product_id'))
            ?? $trendingProducts->first(fn (Product $product): bool => (float) $product->old_price > (float) $product->price)
            ?? $trendingProducts->first();

        $flashProduct = HomepageContent::resolveProduct(
            data_get($homepage, 'flash_deals.spotlight_product_id'),
            flashFallback: true,
        );

        $endsAt = now('Asia/Karachi')->endOfDay();
        $remaining = max(0, $endsAt->getTimestamp() - now('Asia/Karachi')->getTimestamp());

        return view('frontend.home', [
            'productsCatalog' => Product::catalogForJs(),
            'hero' => [
                'badge' => filled($hero['badge'] ?? null) ? (string) $hero['badge'] : 'Pakistan · Cash on delivery',
                'title' => filled($hero['title_line1'] ?? null) ? (string) $hero['title_line1'] : 'Shop verified products',
                'accent' => filled($hero['title_accent'] ?? null) ? (string) $hero['title_accent'] : 'delivered nationwide',
                'description' => filled($hero['description'] ?? null)
                    ? (string) $hero['description']
                    : 'Original catalog, clear PKR pricing, and support on WhatsApp when you need it.',
                'image' => HomepageContent::imageUrl($hero['image_url'] ?? null, 'Shop Pakistan'),
                'image_alt' => filled($hero['image_alt'] ?? null) ? (string) $hero['image_alt'] : 'Featured product',
            ],
            'promoProduct' => $promoProduct ? $this->presentProduct($promoProduct) : null,
            'featuredCategories' => $featuredCategories,
            'trendingProducts' => $trendingProducts->map(fn (Product $product): array => $this->presentProduct($product))->values(),
            'trendingCopy' => [
                'badge' => filled($trendingCopy['badge'] ?? null) ? (string) $trendingCopy['badge'] : 'Selling fast',
                'heading' => filled($trendingCopy['heading'] ?? null) ? (string) $trendingCopy['heading'] : 'Trending right now',
                'description' => filled($trendingCopy['description'] ?? null)
                    ? (string) $trendingCopy['description']
                    : 'Customer favorites with live PKR prices. Add to cart without leaving the page.',
            ],
            'flash' => [
                'badge' => filled($flash['badge'] ?? null) ? (string) $flash['badge'] : 'Flash sale',
                'title' => filled($flash['title'] ?? null) ? (string) $flash['title'] : 'Today’s deals end at midnight',
                'description' => filled($flash['description'] ?? null)
                    ? (string) $flash['description']
                    : 'Prices refresh after midnight Pakistan time. Checkout stays cash on delivery.',
                'ends_at' => $endsAt->toIso8601String(),
                'hours' => str_pad((string) intdiv($remaining, 3600), 2, '0', STR_PAD_LEFT),
                'minutes' => str_pad((string) intdiv($remaining % 3600, 60), 2, '0', STR_PAD_LEFT),
                'seconds' => str_pad((string) ($remaining % 60), 2, '0', STR_PAD_LEFT),
            ],
            'flashProduct' => $flashProduct ? $this->presentProduct($flashProduct) : null,
            'journalPosts' => BlogPost::query()->orderByDesc('is_featured')->orderByDesc('published_at')->limit(3)->get(),
            'latestReviews' => ProductReview::query()
                ->where('is_published', true)
                ->with('product:id,name,slug,image')
                ->latest()
                ->limit(3)
                ->get(),
            'reviewSummary' => $this->reviewSummary(),
            'sections' => [
                'hero' => filter_var(data_get($homepage, 'layout.hero', true), FILTER_VALIDATE_BOOLEAN),
                'steps' => filter_var(data_get($homepage, 'layout.steps', true), FILTER_VALIDATE_BOOLEAN),
                'trust' => filter_var(data_get($homepage, 'layout.trust', true), FILTER_VALIDATE_BOOLEAN),
                'categories' => filter_var(data_get($homepage, 'layout.categories', true), FILTER_VALIDATE_BOOLEAN),
                'trending' => filter_var(data_get($homepage, 'layout.trending', true), FILTER_VALIDATE_BOOLEAN),
                'reviews' => filter_var(data_get($homepage, 'layout.reviews', true), FILTER_VALIDATE_BOOLEAN),
                'journal' => filter_var(data_get($homepage, 'layout.journal', true), FILTER_VALIDATE_BOOLEAN),
                'flash' => filter_var(data_get($homepage, 'layout.flash', true), FILTER_VALIDATE_BOOLEAN),
            ],
            'pageSeo' => $this->homeSeo(),
            'whatsappUrl' => LiveChatSettings::whatsappUrl('Hi, I need help with an order on Shop Pakistan.'),
            'alpineAlreadyLoaded' => LiveChatSettings::isEnabled(),
        ]);
    }

    /** @return array<string, string> */
    private function homeSeo(): array
    {
        $brand = trim((string) SiteSettings::get('site_name', 'Beautage')) ?: 'Beautage';
        $title = trim((string) SiteSettings::get('default_meta_title', ''));
        $description = trim((string) SiteSettings::get('default_meta_description', ''));

        if ($title === '') {
            $title = $brand.' — Elevate Your Natural Elegance';
        }
        if ($description === '') {
            $description = 'Shop '.$brand.' with PKR prices, cash on delivery, nationwide shipping, and verified customer reviews.';
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => route('home'),
            'og_type' => 'website',
            'og_title' => $title,
            'og_description' => $description,
            'og_image' => SiteSettings::logoUrl(),
        ];
    }

    /** @return array{average: float, count: int, bars: list<array{label: string, count: int, percent: int}>} */
    private function reviewSummary(): array
    {
        $base = ProductReview::query()->where('is_published', true);
        $count = (clone $base)->count();
        $counts = (clone $base)
            ->selectRaw('rating, COUNT(*) as total')
            ->groupBy('rating')
            ->pluck('total', 'rating');
        $bars = [];

        for ($star = 5; $star >= 1; $star--) {
            $total = (int) ($counts[$star] ?? 0);
            $bars[] = [
                'label' => $star.' star',
                'count' => $total,
                'percent' => $count > 0 ? (int) round(($total / $count) * 100) : 0,
            ];
        }

        return [
            'average' => $count > 0 ? round((float) (clone $base)->avg('rating'), 1) : 0,
            'count' => $count,
            'bars' => $bars,
        ];
    }

    /** @return Collection<int, Product> */
    private function trendingProducts(): Collection
    {
        $base = Product::query()
            ->with('productCategory:id,name,slug')
            ->orderByDesc('reviews')
            ->orderByDesc('rating')
            ->orderBy('sort_order')
            ->orderBy('id');

        $inStock = (clone $base)->where('in_stock', true)->limit(8)->get();

        if ($inStock->isNotEmpty()) {
            return $inStock;
        }

        return $base->limit(8)->get();
    }

    /** @return array<string, mixed> */
    private function presentProduct(Product $product): array
    {
        $price = (float) $product->price;
        $old = (float) ($product->old_price ?? 0);
        $percent = null;

        if ($old > $price && $price >= 0) {
            $percent = (int) round((($old - $price) / $old) * 100);
        } elseif (preg_match('/(\d{1,2})/', (string) $product->discount, $matches) === 1) {
            $parsed = (int) $matches[1];
            $percent = $parsed > 0 ? $parsed : null;
        }

        $image = trim((string) $product->image);
        $hover = trim((string) $product->hover_image);
        $imageUrl = $image !== '' ? $this->productImage($image) : null;
        $hoverUrl = $hover !== '' ? $this->productImage($hover) : null;

        return [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->productCategory?->name ?? $product->category,
            'price_label' => MoneyFormat::pkr($price),
            'old_price_label' => $old > $price ? MoneyFormat::pkr($old) : null,
            'discount_percent' => $percent,
            'image' => $imageUrl,
            'hover_image' => ($hoverUrl && $hoverUrl !== $imageUrl) ? $hoverUrl : null,
            'rating' => $product->rating,
            'reviews' => (int) $product->reviews,
            'in_stock' => (bool) $product->in_stock,
            'url' => route('product.show', $product),
            'price' => $price,
            'old_price' => $old > $price ? $old : null,
            'short' => $product->cardExcerpt(),
        ];
    }

    private function productImage(string $image): string
    {
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://') || str_starts_with($image, '/')) {
            return $image;
        }

        return SiteSettings::assetUrl($image, 'images/beautagelogosquare2.png');
    }
}
