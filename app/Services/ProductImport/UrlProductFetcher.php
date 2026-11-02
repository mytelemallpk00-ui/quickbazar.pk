<?php

namespace App\Services\ProductImport;

use App\Models\ProductImportBatch;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UrlProductFetcher
{
    public function __construct(
        protected ImportPriceNormalizer $prices,
        protected ImportContentSanitizer $content,
    ) {}

    public function fetch(string $source, string $url, string $priceMode = 'pkr'): ?NormalizedProduct
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (str_contains($url, 'daraz.pk')) {
            $daraz = $this->fetchDarazProduct($url, $priceMode);
            if ($daraz) {
                return $daraz;
            }
        }

        if ($source === ProductImportBatch::SOURCE_SHOPIFY || $this->looksLikeShopify($url)) {
            $fromShopify = $this->fetchShopifyProductJson($url, $priceMode);
            if ($fromShopify) {
                return $fromShopify;
            }
        }

        return $this->fetchFromHtml($url, $priceMode);
    }

    private function looksLikeShopify(string $url): bool
    {
        return str_contains($url, '/products/') || str_contains($url, 'myshopify.com');
    }

    private function fetchShopifyProductJson(string $url, string $priceMode = 'pkr'): ?NormalizedProduct
    {
        if (! preg_match('~/products/([^/?#]+)~i', $url, $matches)) {
            return null;
        }

        $handle = $matches[1];
        $base = preg_replace('#/products/.*$#i', '', $url);
        if (! $base) {
            return null;
        }

        $jsonUrl = rtrim($base, '/').'/products/'.$handle.'.json';

        try {
            $response = Http::timeout(20)
                ->withHeaders(['User-Agent' => 'BeautageCatalogImporter/1.0'])
                ->get($jsonUrl);

            if (! $response->successful()) {
                return null;
            }

            $product = $response->json('product');
            if (! is_array($product)) {
                return null;
            }

            $variant = is_array($product['variants'][0] ?? null) ? $product['variants'][0] : [];
            $image = $product['images'][0]['src'] ?? ($product['image']['src'] ?? '');
            $price = $this->prices->parse($variant['price'] ?? 0);
            $compare = $this->prices->parse($variant['compare_at_price'] ?? 0);
            if ($compare < $price || $compare <= 0) {
                $compare = $price;
            }

            return new NormalizedProduct(
                name: (string) ($product['title'] ?? 'Shopify product'),
                price: $price,
                oldPrice: $compare,
                description: $this->content->cleanDescription((string) ($product['body_html'] ?? ''), $url),
                image: (string) $image,
                hoverImage: (string) $image,
                gallery: collect($product['images'] ?? [])->pluck('src')->filter()->values()->all(),
                externalSku: (string) ($variant['sku'] ?? $handle),
                sourceUrl: $url,
                categoryName: (string) ($product['product_type'] ?? 'Shopify'),
            );
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchFromHtml(string $url, string $priceMode = 'pkr'): ?NormalizedProduct
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders($this->browserHeaders())
                ->get($url);

            if (! $response->successful()) {
                return null;
            }

            $html = $response->body();

            if ($jsonLd = $this->parseJsonLdProduct($html)) {
                return $this->withSourceUrl($jsonLd, $url);
            }

            $title = $this->metaContent($html, 'og:title') ?? $this->metaContent($html, 'twitter:title');
            $image = $this->metaContent($html, 'og:image') ?? $this->metaContent($html, 'twitter:image');
            $description = $this->metaContent($html, 'og:description') ?? $this->metaContent($html, 'description');

            if (! $title) {
                return null;
            }

            $priceRaw = $this->metaContent($html, 'product:price:amount')
                ?? $this->metaContent($html, 'og:price:amount');
            $price = $this->prices->parse($priceRaw);

            return new NormalizedProduct(
                name: html_entity_decode(strip_tags($title)),
                price: $price,
                oldPrice: $price,
                description: $this->content->cleanDescription(html_entity_decode(strip_tags($description ?? $title)), $url),
                image: (string) ($image ?? ''),
                hoverImage: (string) ($image ?? ''),
                sourceUrl: $url,
                externalSku: Str::limit(md5($url), 32, ''),
            );
        } catch (\Throwable) {
            return null;
        }
    }

    private function fetchDarazProduct(string $url, string $priceMode = 'pkr'): ?NormalizedProduct
    {
        $html = $this->fetchHtml($url);
        if (! $html) {
            return null;
        }

        if ($jsonLd = $this->parseJsonLdProduct($html)) {
            return $this->withSourceUrl($jsonLd, $url);
        }

        $title = $this->metaContent($html, 'og:title');
        $image = $this->metaContent($html, 'og:image');
        $description = $this->metaContent($html, 'og:description');

        if (preg_match('#"price"\s*:\s*"?([0-9.,]+)"?#i', $html, $m)) {
            $price = $this->prices->parse($m[1]);
        } else {
            $price = $this->prices->parse($this->metaContent($html, 'product:price:amount'));
        }

        if (! $title && preg_match('#"name"\s*:\s*"([^"]+)"#', $html, $m)) {
            $title = $m[1];
        }

        if (! $title) {
            return null;
        }

        $sku = null;
        if (preg_match('#-i(\d+)\.html#i', $url, $m)) {
            $sku = 'daraz-'.$m[1];
        }

        return new NormalizedProduct(
            name: html_entity_decode(strip_tags($title)),
            price: $price,
            oldPrice: $price,
            description: $this->content->cleanDescription(html_entity_decode(strip_tags($description ?? $title)), $url),
            image: (string) ($image ?? ''),
            hoverImage: (string) ($image ?? ''),
            sourceUrl: $url,
            externalSku: $sku,
        );
    }

    private function fetchHtml(string $url): ?string
    {
        try {
            $response = Http::timeout(25)
                ->withHeaders($this->browserHeaders())
                ->get($url);

            return $response->successful() ? $response->body() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /** @return array<string, string> */
    private function browserHeaders(): array
    {
        return [
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
            'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Language' => 'en-US,en;q=0.9',
        ];
    }

    private function withSourceUrl(NormalizedProduct $product, string $url): NormalizedProduct
    {
        if ($product->sourceUrl) {
            return $product;
        }

        return new NormalizedProduct(
            name: $product->name,
            price: $product->price,
            oldPrice: $product->oldPrice,
            description: $product->description,
            image: $product->image,
            hoverImage: $product->hoverImage,
            gallery: $product->gallery,
            externalSku: $product->externalSku,
            sourceUrl: $url,
            categoryName: $product->categoryName,
            rating: $product->rating,
            reviews: $product->reviews,
            inStock: $product->inStock,
        );
    }

    private function parseJsonLdProduct(string $html): ?NormalizedProduct
    {
        if (! preg_match_all('#<script[^>]+type=["\']application/ld\+json["\'][^>]*>(.*?)</script>#is', $html, $blocks)) {
            return null;
        }

        foreach ($blocks[1] as $block) {
            $data = json_decode(html_entity_decode(trim($block)), true);
            if (! is_array($data)) {
                continue;
            }

            $nodes = isset($data['@graph']) && is_array($data['@graph']) ? $data['@graph'] : [$data];

            foreach ($nodes as $node) {
                if (! is_array($node)) {
                    continue;
                }

                $type = $node['@type'] ?? null;
                $types = is_array($type) ? $type : [$type];
                if (! in_array('Product', $types, true)) {
                    continue;
                }

                $offers = $node['offers'] ?? [];
                if (isset($offers['price'])) {
                    $offer = $offers;
                } else {
                    $offer = is_array($offers[0] ?? null) ? $offers[0] : [];
                }

                $images = $node['image'] ?? '';
                if (is_array($images)) {
                    $image = is_string($images[0] ?? null) ? $images[0] : ($images[0]['url'] ?? '');
                } else {
                    $image = (string) $images;
                }

                $parsedPrice = $this->prices->parse($offer['price'] ?? 0);

                $desc = strip_tags((string) ($node['description'] ?? $node['name'] ?? ''));

                return new NormalizedProduct(
                    name: (string) ($node['name'] ?? 'Imported product'),
                    price: $parsedPrice,
                    oldPrice: $parsedPrice,
                    description: $this->content->cleanDescription($desc, null),
                    image: $image,
                    hoverImage: $image,
                    externalSku: (string) ($node['sku'] ?? $node['mpn'] ?? ''),
                    categoryName: (string) ($node['category'] ?? null),
                );
            }
        }

        return null;
    }

    private function metaContent(string $html, string $property): ?string
    {
        $pattern = '#<meta[^>]+(?:property|name)=["\']'.preg_quote($property, '#').'["\'][^>]+content=["\']([^"\']+)#i';
        if (preg_match($pattern, $html, $m)) {
            return $m[1];
        }

        $pattern2 = '#<meta[^>]+content=["\']([^"\']+)["\'][^>]+(?:property|name)=["\']'.preg_quote($property, '#').'["\']#i';
        if (preg_match($pattern2, $html, $m)) {
            return $m[1];
        }

        return null;
    }
}
