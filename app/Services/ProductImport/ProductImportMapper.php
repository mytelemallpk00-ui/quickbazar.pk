<?php

namespace App\Services\ProductImport;

use App\Models\ProductImportBatch;

class ProductImportMapper
{
    /** @param  array<string, string|null>  $row  header => value */
    public function mapRow(string $source, array $row): ?NormalizedProduct
    {
        $get = fn (array $keys) => $this->firstValue($row, $keys);

        return match ($source) {
            ProductImportBatch::SOURCE_DARAZ => $this->mapDaraz($get),
            ProductImportBatch::SOURCE_AMAZON => $this->mapAmazon($get),
            ProductImportBatch::SOURCE_SHOPIFY => $this->mapShopify($get),
            ProductImportBatch::SOURCE_WALMART => $this->mapWalmart($get),
            default => null,
        };
    }

    /** @return list<string> */
    public function templateHeaders(string $source): array
    {
        return match ($source) {
            ProductImportBatch::SOURCE_DARAZ => [
                'Product Name', 'SellerSku', 'Price', 'Sale Price', 'MainImage', 'Description', 'Category',
            ],
            ProductImportBatch::SOURCE_AMAZON => [
                'item-name', 'seller-sku', 'price', 'quantity', 'main-image-url', 'product-description', 'item-type',
            ],
            ProductImportBatch::SOURCE_SHOPIFY => [
                'Title', 'Handle', 'Variant SKU', 'Variant Price', 'Variant Compare At Price', 'Image Src', 'Body (HTML)', 'Type',
            ],
            ProductImportBatch::SOURCE_WALMART => [
                'sku', 'productName', 'price', 'mainImageUrl', 'shortDescription', 'productCategory',
            ],
            default => [],
        };
    }

    /** @return list<array<string, string>> */
    public function templateSampleRows(string $source): array
    {
        return match ($source) {
            ProductImportBatch::SOURCE_DARAZ => [[
                'Product Name' => 'Beautage LED Therapy Mask',
                'SellerSku' => 'DARAZ-SKU-001',
                'Price' => '12999',
                'Sale Price' => '9999',
                'MainImage' => 'https://example.com/mask.jpg',
                'Description' => '7-spectrum LED mask for home skincare.',
                'Category' => 'Beauty Tech',
            ]],
            ProductImportBatch::SOURCE_AMAZON => [[
                'item-name' => 'Organic Face Serum 30ml',
                'seller-sku' => 'AMZ-BTG-001',
                'price' => '24.99',
                'quantity' => '50',
                'main-image-url' => 'https://example.com/serum.jpg',
                'product-description' => 'Hyaluronic acid serum.',
                'item-type' => 'Skincare',
            ]],
            ProductImportBatch::SOURCE_SHOPIFY => [[
                'Title' => 'Velvet Blossom Eau de Parfum',
                'Handle' => 'velvet-blossom-edp',
                'Variant SKU' => 'SHOP-VEL-01',
                'Variant Price' => '89.00',
                'Variant Compare At Price' => '110.00',
                'Image Src' => 'https://cdn.shopify.com/example.jpg',
                'Body (HTML)' => '<p>Luxury fragrance.</p>',
                'Type' => 'Fragrance',
            ]],
            ProductImportBatch::SOURCE_WALMART => [[
                'sku' => 'WM-BEAUTY-001',
                'productName' => 'Hair Styler Pro',
                'price' => '59.00',
                'mainImageUrl' => 'https://example.com/styler.jpg',
                'shortDescription' => 'Infrared hair styler.',
                'productCategory' => 'Hair Care',
            ]],
            default => [],
        };
    }

    /** @param  callable(list<string>): ?string  $get */
    private function mapDaraz(callable $get): ?NormalizedProduct
    {
        $name = $get(['product name', 'name', 'title']);
        if (! $name) {
            return null;
        }

        $price = $this->money($get(['sale price', 'special price', 'price']) ?? '0');
        $old = $this->money($get(['price', 'original price']) ?? (string) $price);

        return new NormalizedProduct(
            name: $name,
            price: $price ?: $old,
            oldPrice: max($old, $price),
            description: $get(['description', 'short description']) ?? $name,
            image: $get(['mainimage', 'image', 'primary image']) ?? '',
            externalSku: $get(['sellersku', 'sku', 'shop sku']),
            sourceUrl: $get(['product url', 'url']),
            categoryName: $get(['category', 'primary category']),
        );
    }

    /** @param  callable(list<string>): ?string  $get */
    private function mapAmazon(callable $get): ?NormalizedProduct
    {
        $name = $get(['item-name', 'product name', 'title']);
        if (! $name) {
            return null;
        }

        $price = $this->money($get(['price', 'standard price']) ?? '0');
        $qty = (int) ($get(['quantity', 'quantity (US)']) ?? '1');

        return new NormalizedProduct(
            name: $name,
            price: $price,
            oldPrice: $price,
            description: strip_tags($get(['product-description', 'description']) ?? $name),
            image: $get(['main-image-url', 'image-url', 'main image url']) ?? '',
            externalSku: $get(['seller-sku', 'sku', 'listing-id']),
            categoryName: $get(['item-type', 'product type']),
            inStock: $qty > 0,
        );
    }

    /** @param  callable(list<string>): ?string  $get */
    private function mapShopify(callable $get): ?NormalizedProduct
    {
        $name = $get(['title', 'product title']);
        if (! $name) {
            return null;
        }

        $price = $this->money($get(['variant price', 'price']) ?? '0');
        $compare = $this->money($get(['variant compare at price', 'compare at price']) ?? (string) $price);

        return new NormalizedProduct(
            name: $name,
            price: $price ?: $compare,
            oldPrice: max($compare, $price),
            description: strip_tags($get(['body (html)', 'body html', 'description']) ?? $name),
            image: $get(['image src', 'image url', 'image']) ?? '',
            externalSku: $get(['variant sku', 'sku', 'handle']),
            categoryName: $get(['type', 'product type', 'vendor']),
        );
    }

    /** @param  callable(list<string>): ?string  $get */
    private function mapWalmart(callable $get): ?NormalizedProduct
    {
        $name = $get(['productname', 'product name', 'title']);
        if (! $name) {
            return null;
        }

        $price = $this->money($get(['price', 'selling price']) ?? '0');

        return new NormalizedProduct(
            name: $name,
            price: $price,
            oldPrice: $price,
            description: $get(['shortdescription', 'short description', 'description']) ?? $name,
            image: $get(['mainimageurl', 'main image url', 'image url']) ?? '',
            externalSku: $get(['sku', 'wpid']),
            categoryName: $get(['productcategory', 'category']),
        );
    }

    /** @param  array<string, string|null>  $row */
    private function firstValue(array $row, array $keys): ?string
    {
        foreach ($keys as $key) {
            $normalized = $this->normalizeKey($key);
            foreach ($row as $header => $value) {
                if ($this->normalizeKey((string) $header) === $normalized && filled($value)) {
                    return trim((string) $value);
                }
            }
        }

        return null;
    }

    private function normalizeKey(string $key): string
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '', $key) ?? '');
    }

    private function money(string $value): float
    {
        return app(ImportPriceNormalizer::class)->parse($value);
    }
}
