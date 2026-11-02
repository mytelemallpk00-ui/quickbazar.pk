<?php

namespace App\Http\Controllers;

use App\Models\CmsPage;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PageController extends Controller
{
    public function showBySlug(string $slug): View
    {
        $page = null;

        if (Schema::hasTable('cms_pages')) {
            $page = CmsPage::query()
                ->where('slug', $slug)
                ->where('is_published', true)
                ->first();
        }

        $page ??= CmsPage::defaultPage($slug);

        if (! $page) {
            abort(404);
        }

        return $this->show($page);
    }

    public function show(CmsPage $page): View
    {
        if (! $page->is_published) {
            abort(404);
        }

        return view($page->viewName(), [
            'page' => $page,
        ]);
    }
}
