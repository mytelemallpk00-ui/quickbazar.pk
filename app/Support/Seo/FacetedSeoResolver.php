<?php

namespace App\Support\Seo;

use App\Models\Category;
use App\Support\SeoAutomationSettings;
use App\Support\SiteSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

final class FacetedSeoResolver
{
    /** @return array{title?: string, description?: string, canonical: string, robots: string, og_title?: string, og_description?: string, og_image?: mixed, og_type?: string} */
    public static function forCollections(Request $request): array
    {
        $indexed = SeoAutomationSettings::facetedIndexedParams();
        $query = $request->query();
        $extraFilters = false;

        foreach ($query as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }
            if (! in_array((string) $key, $indexed, true)) {
                $extraFilters = true;
                break;
            }
        }

        $canonicalQuery = [];
        foreach ($indexed as $param) {
            if ($request->filled($param)) {
                $canonicalQuery[$param] = $request->query($param);
            }
        }

        $canonical = route('collections', $canonicalQuery);
        $brand = trim((string) SiteSettings::get('site_name', 'Beautage')) ?: 'Beautage';
        $noindexExtras = (bool) (SeoAutomationSettings::all()['faceted_noindex_when_extra_filters'] ?? true);
        $cat = $request->query('cat');
        $category = null;

        if (is_string($cat) && $cat !== '' && ! str_contains($cat, ',')) {
            $category = Category::query()->where('slug', $cat)->where('is_active', true)->first();
        }

        if ($category) {
            $title = filled($category->meta_title)
                ? (string) $category->meta_title
                : ($category->displayNavLabel().' — '.$brand);
            $description = filled($category->meta_description)
                ? (string) $category->meta_description
                : ('Shop '.$category->displayNavLabel().' at '.$brand.'. Compare PKR prices, ratings, and availability.');
            $robots = ($extraFilters && $noindexExtras)
                ? 'noindex,follow'
                : (filled($category->robots) ? (string) $category->robots : 'index,follow');

            return [
                'title' => $title,
                'description' => Str::limit(trim(preg_replace('/\s+/', ' ', strip_tags($description)) ?? ''), 160, ''),
                'canonical' => filled($category->canonical_url) && ! ($extraFilters && $noindexExtras)
                    ? (string) $category->canonical_url
                    : route('collections', ['cat' => $category->slug]),
                'robots' => $robots,
                'og_title' => filled($category->og_title) ? (string) $category->og_title : $title,
                'og_description' => filled($category->og_description) ? (string) $category->og_description : $description,
                'og_image' => $category->og_image ?: $category->image_url,
                'og_type' => 'website',
            ];
        }

        if ($extraFilters && $noindexExtras) {
            return [
                'title' => 'Collections & Categories — '.$brand,
                'description' => 'Browse '.$brand.' collections with PKR prices, ratings, and cash on delivery across Pakistan.',
                'canonical' => route('collections'),
                'robots' => 'noindex,follow',
                'og_type' => 'website',
            ];
        }

        return [
            'title' => 'Collections & Categories — '.$brand,
            'description' => 'Browse '.$brand.' collections. Filter by department, price, rating, and availability, then add products to your bag.',
            'canonical' => $canonical,
            'robots' => 'index,follow',
            'og_type' => 'website',
        ];
    }
}
