@if($product ?? null)
@php
    $pageSeo = $pageSeo ?? [];
    $galleryImages = $galleryImages ?? [];
    $jsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => $product->name,
        'description' => \Illuminate\Support\Str::limit(strip_tags((string) ($pageSeo['description'] ?? $product->short_description ?? $product->description ?? '')), 500),
        'image' => $galleryImages ?: [$product->image],
        'sku' => (string) ($product->external_sku ?: $product->slug ?: $product->id),
        'brand' => ['@type' => 'Brand', 'name' => 'Beautage'],
        'offers' => [
            '@type' => 'Offer',
            'url' => route('product.show', $product),
            'priceCurrency' => 'PKR',
            'price' => number_format((float) $product->price, 2, '.', ''),
            'availability' => $product->in_stock ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
        ],
    ];
    if ((int) $product->reviews > 0) {
        $jsonLd['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => number_format((float) $product->rating, 1, '.', ''),
            'reviewCount' => (int) $product->reviews,
        ];
    }
@endphp
<script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG) !!}</script>
@endif
