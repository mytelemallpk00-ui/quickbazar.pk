<?php

namespace App\Support\Seo;

use App\Support\SeoAutomationSettings;
use App\Support\SiteSettings;
use Illuminate\Http\Request;

final class FacetedSeoResolver
{
    /** @return array{title?: string, description?: string, canonical: string, robots: string} */
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

        if ($extraFilters && SeoAutomationSettings::all()['faceted_noindex_when_extra_filters'] ?? true) {
            return [
                'canonical' => route('collections'),
                'robots' => 'noindex,follow',
            ];
        }

        $brand = trim((string) SiteSettings::get('site_name', 'Quick Bazar')) ?: 'Quick Bazar';
        $cat = $request->query('cat');
        $title = 'Shop all products — '.$brand;
        $description = 'Browse '.$brand.' categories with PKR prices, ratings, and cash on delivery across Pakistan.';

        if (is_string($cat) && $cat !== '') {
            $label = ucfirst(str_replace('-', ' ', $cat));
            $title = $label.' — '.$brand;
            $description = 'Shop '.$label.' on '.$brand.' with verified reviews and cash on delivery across Pakistan.';
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
        ];
    }
}
