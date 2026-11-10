<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\ProductFaqGenerator;
use App\Support\ProductFrequentlyBoughtTogether;
use App\Support\SiteSettings;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function show(Product $product): View
    {
        if (Schema::hasColumn('products', 'faq_items') && blank($product->faq_items)) {
            $product->forceFill([
                'faq_items' => ProductFaqGenerator::forProduct($product),
            ])->saveQuietly();
        }

        $publishedReviews = $product->publishedReviews()->latest()->get();
        $bundlePayload = ProductFrequentlyBoughtTogether::payloadFor($product);

        $product->loadMissing('productCategory');
        $pageSeo = $product->toSeoArray();
        $brand = trim((string) SiteSettings::get('site_name', 'Quick Bazar')) ?: 'Quick Bazar';
        if (blank($product->meta_title) && blank($product->seo_title)) {
            $pageSeo['title'] = $product->name.' | '.$brand;
        }
        $pageSeo['og_title'] = filled($pageSeo['og_title'] ?? null) ? $pageSeo['og_title'] : $pageSeo['title'];
        if (blank($pageSeo['description'])) {
            $pageSeo['description'] = $product->short_description ?: strip_tags((string) $product->description);
        }
        $pageSeo['canonical'] = $pageSeo['canonical'] ?: route('product.show', $product);
        if (blank($pageSeo['og_image']) && filled($product->image)) {
            $pageSeo['og_image'] = $product->image;
        }
        $pageSeo['og_type'] = 'product';

        return view('product-detail', [
            'product' => $product,
            'pageSeo' => $pageSeo,
            'currentProductCatalog' => $product->toDetailCatalogArray(),
            'relatedProductsCatalog' => $product->relatedForJs(6),
            'galleryImages' => $product->gallery_images ?? [$product->image],
            'publishedReviews' => $publishedReviews,
            'bundlePayload' => $bundlePayload,
        ]);
    }
}
