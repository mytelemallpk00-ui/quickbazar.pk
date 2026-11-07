<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Support\Seo\FacetedSeoResolver;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function __invoke(Request $request): View
    {
        $shopCategories = Category::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $catParam = $request->query('cat');
        $activeCategorySlug = is_string($catParam) && $catParam !== '' ? $catParam : null;

        $productsCatalog = Product::collectionForJs();
        $categoryCounts = [];
        $prices = [];
        foreach ($productsCatalog as $product) {
            $slug = (string) ($product['categorySlug'] ?? '');
            if ($slug !== '') {
                $categoryCounts[$slug] = ($categoryCounts[$slug] ?? 0) + 1;
            }
            $prices[] = (float) ($product['price'] ?? 0);
        }

        $priceFloor = $prices === [] ? 0 : (int) floor(min($prices));
        $priceCeil = $prices === [] ? 1000 : (int) ceil(max($prices));
        if ($priceCeil <= $priceFloor) {
            $priceCeil = $priceFloor + 1;
        }

        $singleSlug = is_string($activeCategorySlug) && $activeCategorySlug !== '' && ! str_contains($activeCategorySlug, ',')
            ? $activeCategorySlug
            : null;
        $activeCategory = $singleSlug ? $shopCategories->firstWhere('slug', $singleSlug) : null;
        $defaultBlurb = 'Browse the live Beautage catalog. Filter by department, price, rating, and availability, then add verified products to your bag.';

        return view('collections', [
            'pageSeo' => FacetedSeoResolver::forCollections($request),
            'productsCatalog' => $productsCatalog,
            'shopCategories' => $shopCategories,
            'activeCategorySlug' => $activeCategorySlug,
            'catalogCategories' => $shopCategories->map(fn (Category $category) => [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
                'label' => $category->displayNavLabel(),
                'description' => $this->plainText($category->description),
                'count' => (int) ($categoryCounts[$category->slug] ?? 0),
            ])->values(),
            'priceFloor' => $priceFloor,
            'priceCeil' => $priceCeil,
            'catalogCount' => count($productsCatalog),
            'heroTitle' => $activeCategory ? $activeCategory->displayNavLabel() : 'Collections & Categories',
            'heroBlurb' => $activeCategory
                ? ($this->plainText($activeCategory->description) ?: $defaultBlurb)
                : $defaultBlurb,
            'heroCrumb' => $activeCategory ? $activeCategory->displayNavLabel() : 'All departments',
            'defaultBlurb' => $defaultBlurb,
        ]);
    }

    private function plainText(mixed $value): string
    {
        $plain = preg_replace('/\s+/u', ' ', strip_tags((string) $value));
        $plain = trim(is_string($plain) ? $plain : '');
        if ($plain === '') {
            return '';
        }
        if (function_exists('mb_strlen') && mb_strlen($plain) > 320) {
            return rtrim(mb_substr($plain, 0, 317)).'…';
        }
        if (strlen($plain) > 320) {
            return rtrim(substr($plain, 0, 317)).'…';
        }

        return $plain;
    }
}
