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

        return view('collections', [
            'pageSeo' => FacetedSeoResolver::forCollections($request),
            'productsCatalog' => Product::collectionForJs(),
            'shopCategories' => $shopCategories,
            'activeCategorySlug' => $activeCategorySlug,
            'catalogCategories' => $shopCategories->map(fn (Category $category) => [
                'id' => $category->id,
                'slug' => $category->slug,
                'name' => $category->name,
                'label' => $category->displayNavLabel(),
            ])->values(),
        ]);
    }
}
