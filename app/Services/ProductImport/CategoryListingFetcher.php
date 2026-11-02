<?php

namespace App\Services\ProductImport;

use App\Models\ProductImportBatch;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CategoryListingFetcher
{
    /** @deprecated Use maxLimit() — kept for backward-compatible references. */
    public const MAX_LIMIT = 10000;

    public static function maxLimit(): int
    {
        $configured = (int) config('services.product_import.listing_max', 10000);

        return max(1, min(50000, $configured > 0 ? $configured : 10000));
    }

    public static function normalizeLimit(int $limit): int
    {
        if ($limit <= 0) {
            return self::maxLimit();
        }

        return max(1, min(self::maxLimit(), $limit));
    }

    /** @return list<string> */
    public function discoverProductUrls(string $source, string $listingUrl, int $limit, bool $untilEmpty = false): array
    {
        $limit = self::normalizeLimit($limit);
        $listingUrl = trim($listingUrl);

        if ($listingUrl === '') {
            return [];
        }

        if ($this->isShopifyListing($listingUrl) || $source === ProductImportBatch::SOURCE_SHOPIFY) {
            $shopify = $this->discoverShopifyCollection($listingUrl, $limit, $untilEmpty);
            if ($shopify !== []) {
                return $shopify;
            }
        }

        if (($source === ProductImportBatch::SOURCE_DARAZ || str_contains($listingUrl, 'daraz.pk'))
            && str_contains($listingUrl, 'daraz.pk')) {
            $daraz = $this->discoverDarazListing($listingUrl, $limit, $untilEmpty);
            if ($daraz !== []) {
                return $daraz;
            }
        }

        return $this->discoverFromHtml($source, $listingUrl, $limit);
    }

    /** @return list<string> */
    private function discoverShopifyCollection(string $listingUrl, int $limit, bool $untilEmpty = false): array
    {
        $base = $this->shopifyStoreBase($listingUrl);
        if (! $base) {
            return [];
        }

        $handle = null;
        if (preg_match('~/collections/([^/?#]+)~i', $listingUrl, $m)) {
            $handle = $m[1];
        }

        $urls = [];
        $page = 1;

        $maxPages = $untilEmpty ? 250 : min(250, (int) ceil($limit / 250) + 2);

        while (($untilEmpty || count($urls) < $limit) && count($urls) < $limit && $page <= $maxPages) {
            $jsonPath = $handle
                ? "/collections/{$handle}/products.json"
                : '/products.json';

            $query = http_build_query([
                'limit' => min(250, $limit - count($urls)),
                'page' => $page,
            ]);

            try {
                $response = Http::timeout(45)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept' => 'application/json,text/plain,*/*',
                    ])
                    ->get(rtrim($base, '/').$jsonPath.'?'.$query);

                if (! $response->successful()) {
                    break;
                }

                $products = $response->json('products');
                if (! is_array($products) || $products === []) {
                    break;
                }

                foreach ($products as $product) {
                    if (! is_array($product)) {
                        continue;
                    }
                    $handleSlug = $product['handle'] ?? null;
                    if (! $handleSlug) {
                        continue;
                    }
                    $urls[] = rtrim($base, '/').'/products/'.$handleSlug;
                    if (count($urls) >= $limit) {
                        break 2;
                    }
                }

                if (count($products) < 250) {
                    break;
                }
                $page++;
            } catch (\Throwable) {
                break;
            }
        }

        return array_values(array_unique($urls));
    }

    /** @return list<string> */
    private function discoverDarazListing(string $listingUrl, int $limit, bool $untilEmpty = false): array
    {
        $fromAjax = $this->discoverDarazViaAjax($listingUrl, $limit, $untilEmpty);
        if ($fromAjax !== []) {
            return $fromAjax;
        }

        $html = $this->fetchListingHtml($listingUrl);
        if (! $html) {
            return [];
        }

        $found = [];

        if (preg_match_all('#"(?:productUrl|pdpUrl|itemUrl)"\s*:\s*"(//[^"]+|https?://[^"]+)"#i', $html, $matches)) {
            foreach ($matches[1] as $href) {
                $url = $this->absoluteUrl('https://www.daraz.pk', $href);
                if ($url && $this->looksLikeProductUrl(ProductImportBatch::SOURCE_DARAZ, $url)) {
                    $found[$url] = true;
                }
                if (count($found) >= $limit) {
                    break;
                }
            }
        }

        if (count($found) < $limit && preg_match_all('#https?://(?:www\.)?daraz\.pk[^"\s<>]*(?:/products/[^"\s<>]+|-i\d+\.html)#i', $html, $matches)) {
            foreach ($matches[0] as $url) {
                $found[rtrim($url, '",')] = true;
                if (count($found) >= $limit) {
                    break;
                }
            }
        }

        return array_slice(array_keys($found), 0, $limit);
    }

    /** @return list<string> */
    private function discoverDarazViaAjax(string $listingUrl, int $limit, bool $untilEmpty = false): array
    {
        $found = [];
        $page = 1;

        $maxPages = $untilEmpty ? 500 : min(500, (int) ceil($limit / 20) + 3);

        while (($untilEmpty || count($found) < $limit) && count($found) < $limit && $page <= $maxPages) {
            $ajaxUrl = $this->darazAjaxListingUrl($listingUrl, $page);
            $body = $this->fetchDarazAjaxBody($ajaxUrl);
            if (! $body) {
                break;
            }

            $data = json_decode($body, true);
            if (! is_array($data)) {
                break;
            }

            $items = $data['mods']['listItems'] ?? null;
            if (! is_array($items) || $items === []) {
                break;
            }

            foreach ($items as $item) {
                if (! is_array($item)) {
                    continue;
                }

                $href = $item['itemUrl'] ?? $item['productUrl'] ?? null;
                if (! is_string($href) || $href === '') {
                    continue;
                }

                $url = $this->absoluteUrl('https://www.daraz.pk', $href);
                if ($url && $this->looksLikeProductUrl(ProductImportBatch::SOURCE_DARAZ, $url)) {
                    $found[$url] = true;
                }

                if (count($found) >= $limit) {
                    break 2;
                }
            }

            if (count($items) < 20) {
                break;
            }

            $page++;
        }

        return array_slice(array_keys($found), 0, $limit);
    }

    private function darazAjaxListingUrl(string $listingUrl, int $page): string
    {
        $parts = parse_url($listingUrl);
        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? 'www.daraz.pk';
        $path = $parts['path'] ?? '/';

        if ($path === '' || $path === '/') {
            $path = '/catalog/';
        }

        $query = [];
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        unset($query['ajax']);
        $query['ajax'] = 'true';

        if ($page > 1) {
            $query['page'] = (string) $page;
        } elseif (isset($query['page']) && (int) $query['page'] <= 1) {
            unset($query['page']);
        }

        $qs = http_build_query($query);

        return rtrim($scheme.'://'.$host, '/').$path.($qs !== '' ? '?'.$qs : '');
    }

    private function fetchDarazAjaxBody(string $url): ?string
    {
        try {
            $response = Http::timeout(35)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'application/json, text/plain, */*',
                    'Accept-Language' => 'en-US,en;q=0.9',
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Referer' => Str::before($url, '?').'?',
                ])
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $body = trim($response->body());
            if ($body === '' || (! str_starts_with($body, '{') && ! str_starts_with($body, '['))) {
                return null;
            }

            return $body;
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return list<string> */
    private function discoverFromHtml(string $source, string $listingUrl, int $limit): array
    {
        try {
            $html = $this->fetchListingHtml($listingUrl);
            if (! $html) {
                return [];
            }

            $baseHost = parse_url($listingUrl, PHP_URL_SCHEME).'://'.parse_url($listingUrl, PHP_URL_HOST);

            $patterns = match ($source) {
                ProductImportBatch::SOURCE_DARAZ => [
                    '#href=["\']([^"\']*(?:daraz\.pk|/products/|/[^"\']+-i\d+\.html)[^"\']*)#i',
                ],
                ProductImportBatch::SOURCE_AMAZON => [
                    '#href=["\']([^"\']*/(?:dp|gp/product)/[A-Z0-9]{8,12}[^"\']*)#i',
                ],
                ProductImportBatch::SOURCE_WALMART => [
                    '#href=["\']([^"\']*/ip/[^"\']+)#i',
                ],
                ProductImportBatch::SOURCE_SHOPIFY => [
                    '#href=["\']([^"\']*/products/[^"\']+)#i',
                ],
                default => [
                    '#href=["\']([^"\']*/products/[^"\']+)#i',
                    '#href=["\']([^"\']*/product/[^"\']+)#i',
                ],
            };

            $found = [];
            foreach ($patterns as $pattern) {
                if (preg_match_all($pattern, $html, $matches)) {
                    foreach ($matches[1] as $href) {
                        $url = $this->absoluteUrl($baseHost, html_entity_decode($href));
                        if ($url && $this->looksLikeProductUrl($source, $url) && ! $this->isListingUrl($url)) {
                            $found[$url] = true;
                        }
                        if (count($found) >= $limit) {
                            break 2;
                        }
                    }
                }
            }

            // JSON-LD ItemList / Product links
            if (count($found) < $limit && preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $blocks)) {
                foreach ($blocks[1] as $block) {
                    $data = json_decode(html_entity_decode(trim($block)), true);
                    if (! is_array($data)) {
                        continue;
                    }
                    $this->collectJsonLdUrls($data, $baseHost, $source, $found, $limit);
                }
            }

            return array_slice(array_keys($found), 0, $limit);
        } catch (\Throwable) {
            return [];
        }
    }

    /** @param  array<string, true>  $found */
    private function collectJsonLdUrls(mixed $node, string $baseHost, string $source, array &$found, int $limit): void
    {
        if (count($found) >= $limit) {
            return;
        }

        if (is_array($node)) {
            if (isset($node['@type']) && in_array($node['@type'], ['Product', 'ListItem'], true)) {
                $url = $node['url'] ?? ($node['item'] ?? null);
                if (is_array($url)) {
                    $url = $url['@id'] ?? ($url['url'] ?? null);
                }
                if (is_string($url)) {
                    $abs = $this->absoluteUrl($baseHost, $url);
                    if ($abs && $this->looksLikeProductUrl($source, $abs)) {
                        $found[$abs] = true;
                    }
                }
            }

            foreach ($node as $value) {
                $this->collectJsonLdUrls($value, $baseHost, $source, $found, $limit);
            }
        }
    }

    private function isShopifyListing(string $url): bool
    {
        return str_contains($url, '/collections/') || str_contains($url, 'myshopify.com');
    }

    private function shopifyStoreBase(string $url): ?string
    {
        $parts = parse_url($url);
        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        return $parts['scheme'].'://'.$parts['host'];
    }

    private function absoluteUrl(string $baseHost, string $href): ?string
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, 'javascript:') || str_starts_with($href, '#')) {
            return null;
        }

        if (str_starts_with($href, 'http://') || str_starts_with($href, 'https://')) {
            return Str::before($href, '?');
        }

        if (str_starts_with($href, '//')) {
            return 'https:'.$href;
        }

        if (str_starts_with($href, '/')) {
            return rtrim($baseHost, '/').Str::before($href, '?');
        }

        return rtrim($baseHost, '/').'/'.Str::before($href, '?');
    }

    private function looksLikeProductUrl(string $source, string $url): bool
    {
        return match ($source) {
            ProductImportBatch::SOURCE_DARAZ => str_contains($url, '/products/') || preg_match('#-i\d+\.html#i', $url),
            ProductImportBatch::SOURCE_AMAZON => preg_match('#/(dp|gp/product)/#i', $url),
            ProductImportBatch::SOURCE_WALMART => str_contains($url, '/ip/'),
            ProductImportBatch::SOURCE_SHOPIFY => str_contains($url, '/products/'),
            default => str_contains($url, '/product'),
        };
    }

    private function isListingUrl(string $url): bool
    {
        return str_contains($url, '/collections/')
            || str_contains($url, '/category/')
            || str_contains($url, '/catalog/')
            || str_contains($url, '/browse/');
    }

    private function fetchListingHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                    'Accept-Language' => 'en-US,en;q=0.9',
                ])
                ->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }
}
