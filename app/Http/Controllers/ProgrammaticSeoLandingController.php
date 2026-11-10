<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProgrammaticSeoLanding;
use Illuminate\View\View;

class ProgrammaticSeoLandingController extends Controller
{
    public function __invoke(ProgrammaticSeoLanding $landing): View
    {
        abort_unless($landing->is_published, 404);

        $activeCategorySlug = $landing->category?->slug;

        $pageSeo = $landing->toSeoArray();
        $tokens = [
            '{location}' => (string) ($landing->location ?? ''),
            '{category}' => (string) ($landing->category?->name ?? 'Beauty'),
            '{use_case}' => (string) ($landing->use_case ?? ''),
            '{brand}' => 'Quick Bazar',
        ];
        $headline = $landing->applyTokens($landing->headline, $tokens);
        $introHtml = $landing->applyTokens((string) ($landing->intro_html ?? ''), $tokens);

        $featuredQuery = Product::query()->where('in_stock', true);
        if ($landing->category_id) {
            $featuredQuery->where('category_id', $landing->category_id);
        }

        return view('pages.pseo-landing', [
            'landing' => $landing,
            'headline' => $headline,
            'introHtml' => $introHtml,
            'pageSeo' => $pageSeo,
            'activeCategorySlug' => $activeCategorySlug,
            'featuredProducts' => $featuredQuery->orderByDesc('is_flash')->orderBy('name')->limit(20)->get(),
        ]);
    }
}
