@php
    $card = $product ?? [];
    $name = (string) ($card['name'] ?? 'Product');
    $url = (string) ($card['url'] ?? route('collections'));
    $image = $card['image'] ?: ($placeholder ?? 'https://placehold.co/640x640/0B132B/D4AF37?text=Quick+Bazar');
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
<article class="lv2-product">
    <a href="{{ $url }}" class="lv2-product-media">
        <img class="lv2-card-photo" src="{{ $image }}" alt="{{ $name }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='https://placehold.co/640x640/0B132B/D4AF37?text=Quick+Bazar';">
        @if ($hover !== '')
            <img class="lv2-card-photo lv2-card-photo--alt" src="{{ $hover }}" alt="" width="640" height="640" loading="lazy" decoding="async">
        @endif
        @if (! empty($card['discount_percent']))
            <span class="lv2-save">SAVE {{ (int) $card['discount_percent'] }}%</span>
        @endif
    </a>
    <div class="lv2-product-copy">
        @if (filled($card['category'] ?? null))
            <p class="lv2-muted">{{ $card['category'] }}</p>
        @endif
        <h3><a href="{{ $url }}">{{ $name }}</a></h3>
        @if ($short !== '')
            <p class="lv2-card-desc">{{ $short }}</p>
        @endif
        @if (($card['reviews'] ?? 0) > 0)
            <p class="lv2-stars" aria-label="Rated {{ number_format($rating, 1) }} out of 5">{{ str_repeat('★', $full) }}{{ str_repeat('☆', 5 - $full) }} <span class="lv2-muted">({{ number_format((int) $card['reviews']) }})</span></p>
        @endif
        <p class="lv2-price-row">
            <span class="lv2-price">{{ $card['price_label'] ?? '' }}</span>
            @if (! empty($card['old_price_label']))
                <span class="lv2-old">{{ $card['old_price_label'] }}</span>
            @endif
        </p>
    </div>
    <div class="lv2-card-actions">
        <button
            type="button"
            class="lv2-card-quick"
            data-quick-view
            data-id="{{ (int) ($card['id'] ?? 0) }}"
            data-name="{{ $name }}"
            data-price="{{ $priceValue }}"
            data-old="{{ $oldValue }}"
            data-image="{{ $image }}"
            data-category="{{ $card['category'] ?? '' }}"
            data-desc="{{ $short }}"
            data-url="{{ $url }}"
            data-stock="{{ $inStock ? '1' : '0' }}"
        >Quick view</button>
        @if ($inStock)
            <button
                type="button"
                class="lv2-btn-navy"
                data-card-add
                data-id="{{ (int) ($card['id'] ?? 0) }}"
                data-name="{{ $name }}"
                data-price="{{ $priceValue }}"
                data-image="{{ $image }}"
            >Add to cart</button>
        @else
            <button type="button" class="lv2-btn-navy" disabled>Out of stock</button>
        @endif
    </div>
</article>
