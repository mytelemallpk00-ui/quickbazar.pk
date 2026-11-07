<?php

namespace App\Services\Seo;

use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProgrammaticSeoLanding;
use App\Support\SeoAutomationSettings;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

class SitemapGenerator
{
    /** Google limit: max URLs per sitemap file. */
    public const MAX_URLS_PER_FILE = 50000;

    public const INDEX_FILENAME = 'sitemap_index.xml';

    public function generate(): int
    {
        if (! SeoAutomationSettings::sitemapEnabled()) {
            return 0;
        }

        $settings = SeoAutomationSettings::all();
        $base = rtrim(config('app.url') ?: url('/'), '/');
        $urls = $this->collectUrls($settings, $base);

        if ($urls === []) {
            return 0;
        }

        $this->purgeStaleSitemapFiles();

        $chunks = array_chunk($urls, self::MAX_URLS_PER_FILE);
        $indexEntries = [];
        $written = 0;

        foreach ($chunks as $index => $chunk) {
            $part = $index + 1;
            $filename = sprintf('sitemap-%04d.xml', $part);
            $path = public_path($filename);
            file_put_contents($path, $this->wrapUrlSet($chunk));
            $written++;

            $indexEntries[] = [
                'loc' => $base.'/'.$filename,
                'lastmod' => $this->maxLastMod($chunk),
            ];
        }

        file_put_contents(public_path(self::INDEX_FILENAME), $this->wrapSitemapIndex($indexEntries));
        $written++;

        // Legacy single-file URL (Search Console bookmarks) — mirror when only one part.
        if (count($chunks) === 1) {
            file_put_contents(public_path('sitemap.xml'), $this->wrapUrlSet($chunks[0]));
            $written++;
        } else {
            $this->removeIfExists(public_path('sitemap.xml'));
        }

        return $written;
    }

    public static function indexUrl(): string
    {
        return url('/'.self::INDEX_FILENAME);
    }

    /** @param  array<string, mixed>  $settings */
    /** @return list<array{loc: string, lastmod: string, changefreq: string, priority: string}> */
    protected function collectUrls(array $settings, string $base): array
    {
        $urls = [];
        $urls[] = $this->entry($base.'/', now(), 'daily', '1.0');
        $urls[] = $this->entry($base.route('collections', [], false), now(), 'daily', '0.9');
        $urls[] = $this->entry($base.route('blog.index', [], false), now(), 'weekly', '0.7');
        $urls[] = $this->entry($base.route('exclusive-offers', [], false), now(), 'daily', '0.7');

        foreach (['help', 'shipping', 'returns', 'our-story', 'track-order'] as $page) {
            if (Route::has($page)) {
                $urls[] = $this->entry($base.route($page, [], false), now(), 'monthly', '0.4');
            }
        }

        if (! empty($settings['sitemap_include_products']) && Schema::hasTable('products')) {
            Product::query()->orderBy('id')->chunk(500, function ($products) use (&$urls, $base): void {
                foreach ($products as $product) {
                    if (! filled($product->slug)) {
                        continue;
                    }
                    $urls[] = $this->entry(
                        $base.route('product.show', $product, false),
                        $product->updated_at ?? now(),
                        'weekly',
                        '0.8'
                    );
                }
            });
        }

        if (! empty($settings['sitemap_include_pseo']) && Schema::hasTable('programmatic_seo_landings')) {
            ProgrammaticSeoLanding::query()
                ->where('is_published', true)
                ->orderBy('sort_order')
                ->each(function (ProgrammaticSeoLanding $landing) use (&$urls, $base): void {
                    $urls[] = $this->entry(
                        $base.route('pseo.show', $landing, false),
                        $landing->updated_at ?? now(),
                        'weekly',
                        '0.85'
                    );
                });
        }

        if (! empty($settings['sitemap_include_cms'])) {
            foreach (['/press-releases', '/faq', '/events'] as $path) {
                $urls[] = $this->entry($base.$path, now(), 'monthly', '0.6');
            }

            if (Schema::hasTable('categories')) {
                Category::query()->where('is_active', true)->orderBy('sort_order')->each(function (Category $category) use (&$urls, $base): void {
                    if (! filled($category->slug)) {
                        return;
                    }
                    $urls[] = $this->entry(
                        $base.route('collections', ['cat' => $category->slug], false),
                        $category->updated_at ?? now(),
                        'weekly',
                        '0.7'
                    );
                });
            }

            if (Schema::hasTable('blog_posts')) {
                BlogPost::query()->orderBy('id')->each(function (BlogPost $post) use (&$urls, $base): void {
                    if (! filled($post->slug)) {
                        return;
                    }
                    $urls[] = $this->entry(
                        $base.route('blog.show', $post, false),
                        $post->updated_at ?? now(),
                        'monthly',
                        '0.6'
                    );
                });
            }
        }

        return $urls;
    }

    protected function purgeStaleSitemapFiles(): void
    {
        foreach (glob(public_path('sitemap-*.xml')) ?: [] as $path) {
            if (is_file($path)) {
                @unlink($path);
            }
        }
    }

    protected function removeIfExists(string $path): void
    {
        if (is_file($path)) {
            @unlink($path);
        }
    }

    /** @param  list<array{loc: string, lastmod: string, changefreq: string, priority: string}>  $urls */
    protected function maxLastMod(array $urls): string
    {
        $max = now();
        foreach ($urls as $url) {
            $ts = $url['lastmod'] ?? null;
            if ($ts && strtotime($ts) > $max->getTimestamp()) {
                $max = \Illuminate\Support\Carbon::parse($ts);
            }
        }

        return $max->toAtomString();
    }

    /** @return array{loc: string, lastmod: string, changefreq: string, priority: string} */
    protected function entry(string $loc, $lastmod, string $freq, string $priority): array
    {
        return [
            'loc' => $loc,
            'lastmod' => optional($lastmod)->toAtomString() ?? now()->toAtomString(),
            'changefreq' => $freq,
            'priority' => $priority,
        ];
    }

    /** @param  list<array{loc: string, lastmod: string, changefreq: string, priority: string}>  $urls */
    protected function wrapUrlSet(array $urls): string
    {
        $body = '';
        foreach ($urls as $url) {
            $body .= '<url>'
                .'<loc>'.htmlspecialchars($url['loc'], ENT_XML1).'</loc>'
                .'<lastmod>'.htmlspecialchars($url['lastmod'], ENT_XML1).'</lastmod>'
                .'<changefreq>'.htmlspecialchars($url['changefreq'], ENT_XML1).'</changefreq>'
                .'<priority>'.htmlspecialchars($url['priority'], ENT_XML1).'</priority>'
                .'</url>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$body
            .'</urlset>';
    }

    /** @param  list<array{loc: string, lastmod: string}>  $sitemaps */
    protected function wrapSitemapIndex(array $sitemaps): string
    {
        $body = '';
        foreach ($sitemaps as $row) {
            $body .= '<sitemap>'
                .'<loc>'.htmlspecialchars($row['loc'], ENT_XML1).'</loc>'
                .'<lastmod>'.htmlspecialchars($row['lastmod'], ENT_XML1).'</lastmod>'
                .'</sitemap>';
        }

        return '<?xml version="1.0" encoding="UTF-8"?>'
            .'<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'
            .$body
            .'</sitemapindex>';
    }
}
