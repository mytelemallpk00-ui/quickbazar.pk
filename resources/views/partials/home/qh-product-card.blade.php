@php
    $card = $product ?? [];
    $name = (string) ($card['name'] ?? 'Product');
    $url = (string) ($card['url'] ?? route('collections'));
    $image = $card['image'] ?: ($placeholder ?? 'https://placehold.co/640x640/0e6b5c/ffffff?text=Quick+Bazar');
    $hover = trim((string) ($card['hover_image'] ?? ''));
    if ($hover === $image) {
        $hover = '';
    }
    $rating = max(0, min(5, (float) ($card['rating'] ?? 0)));
    $full = (int) round($rating);
    $short = trim((string) ($card['short'] ?? ''));
    $inStock = ! empty($card['in_stock']);
    $priceValue = $card['price'] ?? null;
    $oldValue = $card['old_price'] ?? null;
@endphp
<article class="qh-card">
    <a href="{{ $url }}" class="qh-card-media">
        <img class="lv2-card-photo" src="{{ $image }}" alt="{{ $name }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='https://placehold.co/640x640/0e6b5c/ffffff?text=Quick+Bazar';">
        @if ($hover !== '')
            <img class="lv2-card-photo lv2-card-photo--alt" src="{{ $hover }}" alt="" width="640" height="640" loading="lazy" decoding="async">
        @endif
        @if ($inStock)
            <span class="qh-stock">In stock</span>
        @else
            <span class="qh-stock qh-stock--out">Out of stock</span>
        @endif
    </a>
    <div class="qh-card-copy">
        <h3><a href="{{ $url }}">{{ $name }}</a></h3>
        @if ($short !== '')
            <p>{{ $short }}</p>
        @endif
        @if (($card['reviews'] ?? 0) > 0)
            <p class="qh-stars" aria-label="Rated {{ number_format($rating, 1) }} out of 5">{{ str_repeat('★', $full) }}{{ str_repeat('☆', 5 - $full) }} <span>({{ number_format($rating, 1) }})</span></p>
        @endif
        <p class="qh-price">
            <strong>{{ $card['price_label'] ?? '' }}</strong>
            @if (! empty($card['old_price_label']))
                <s>{{ $card['old_price_label'] }}</s>
            @endif
        </p>
    </div>
    <div class="qh-card-actions">
        @if ($inStock)
            <button type="button" class="qh-bag" data-card-add data-id="{{ (int) ($card['id'] ?? 0) }}" data-name="{{ $name }}" data-price="{{ $priceValue }}" data-image="{{ $image }}">Add to bag</button>
        @else
            <button type="button" class="qh-bag" disabled>Out of stock</button>
        @endif
        <button type="button" class="qh-quick" data-quick-view data-id="{{ (int) ($card['id'] ?? 0) }}" data-name="{{ $name }}" data-price="{{ $priceValue }}" data-old="{{ $oldValue }}" data-image="{{ $image }}" data-category="{{ $card['category'] ?? '' }}" data-desc="{{ $short }}" data-url="{{ $url }}" data-stock="{{ $inStock ? '1' : '0' }}">Quick view</button>
    </div>
</article>
