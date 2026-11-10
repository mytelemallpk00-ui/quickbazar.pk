<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use Illuminate\View\View;

class BlogController extends Controller
{
    public function index(): View
    {
        $posts = BlogPost::query()
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->get();

        return view('blog.index', [
            'articlesCatalog' => $posts->map(fn (BlogPost $post) => $post->toJournalArray())->values(),
            'featuredPost' => $posts->firstWhere('is_featured', true) ?? $posts->first(),
            'blogCategories' => $posts->pluck('category')->filter()->unique()->values(),
            'pageSeo' => [
                'title' => 'Journal — Quick Bazar',
                'description' => 'Guides on skincare, hair, fragrance, and delivery from the Quick Bazar journal.',
                'canonical' => route('blog.index'),
                'robots' => 'index,follow',
                'og_type' => 'website',
            ],
        ]);
    }

    public function show(BlogPost $post): View
    {
        $seo = $post->toSeoArray();
        $seo['canonical'] = $seo['canonical'] ?: route('blog.show', $post);
        $seo['og_type'] = 'article';

        return view('blog.show', [
            'post' => $post,
            'pageSeo' => $seo,
            'relatedPosts' => BlogPost::query()
                ->whereKeyNot($post->id)
                ->orderByDesc('published_at')
                ->limit(3)
                ->get(),
        ]);
    }
}
