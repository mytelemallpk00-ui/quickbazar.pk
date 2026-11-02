@php echo '<?xml version="1.0" encoding="UTF-8"?>'; @endphp
<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">
    <channel>
        <title>{{ $storeName }}</title>
        <link>{{ $storeUrl }}</link>
        <description>{{ $storeName }} product feed for Google Merchant Center</description>
@foreach ($products as $product)
@php
    $id = filled($product->external_sku) ? $product->external_sku : (string) $product->id;
    $link = route('product.show', $product, absolute: true);
    $image = \App\Support\GoogleMerchant::absoluteUrl($product->image);
    $description = strip_tags((string) ($product->short_description ?: $product->description ?: $product->name));
    $description = \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $description) ?? '', 5000, '');
    $price = number_format((float) $product->price, 2, '.', '').' '.$currency;
    $availability = $product->in_stock ? 'in_stock' : 'out_of_stock';
    $categoryName = $product->productCategory?->name ?? $product->category;
@endphp
        <item>
            <g:id>{{ $id }}</g:id>
            <g:title>{{ $product->name }}</g:title>
            <g:description>{{ $description }}</g:description>
            <g:link>{{ $link }}</g:link>
            <g:image_link>{{ $image }}</g:image_link>
            <g:availability>{{ $availability }}</g:availability>
            <g:price>{{ $price }}</g:price>
            <g:brand>{{ $brand }}</g:brand>
            <g:condition>new</g:condition>
            <g:identifier_exists>false</g:identifier_exists>
@if (filled($categoryName))
            <g:product_type>{{ $categoryName }}</g:product_type>
@endif
            <g:target_country>{{ $country }}</g:target_country>
        </item>
@endforeach
    </channel>
</rss>
