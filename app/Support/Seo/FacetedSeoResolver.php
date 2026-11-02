<?php

namespace App\Support\Seo;

use App\Support\SeoAutomationSettings;
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

        $cat = $request->query('cat');
        $title = 'Product Collections & Catalog — Beautage';
        $description = 'Browse Beautage categories, ratings, and flagship skincare.';

        if (is_string($cat) && $cat !== '') {
            $title = ucfirst(str_replace('-', ' ', $cat)).' — Beautage collection';
            $description = 'Shop '.str_replace('-', ' ', $cat).' products with verified reviews and COD across Pakistan.';
        }

        return [
            'title' => $title,
            'description' => $description,
            'canonical' => $canonical,
            'robots' => 'index,follow',
        ];
    }
}
