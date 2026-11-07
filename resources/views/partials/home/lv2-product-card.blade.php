@php
    $card = $product ?? [];
    $name = (string) ($card['name'] ?? 'Product');
    $url = (string) ($card['url'] ?? route('collections'));
    $image = $card['image'] ?: ($placeholder ?? 'https://placehold.co/640x640/0B132B/D4AF37?text=Beautage');
    $rating = max(0, min(5, (float) ($card['rating'] ?? 0)));
    $full = (int) round($rating);
@endphp
<article class="lv2-product">
    <a href="{{ $url }}" class="lv2-product-media">
        <img src="{{ $image }}" alt="{{ $name }}" width="480" height="480" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='https://placehold.co/640x640/0B132B/D4AF37?text=Beautage';">
        @if (! empty($card['discount_percent']))
            <span class="lv2-save">SAVE {{ (int) $card['discount_percent'] }}%</span>
        @endif
    </a>
    <div>
        @if (filled($card['category'] ?? null))
            <p class="lv2-muted">{{ $card['category'] }}</p>
        @endif
        <h3><a href="{{ $url }}">{{ $name }}</a></h3>
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
    @if (! empty($card['in_stock']))
        <button type="button" class="lv2-btn-navy" style="margin-top:0.8rem" onclick="addToCart({{ (int) $card['id'] }})">Add to cart</button>
    @else
        <button type="button" class="lv2-btn-navy" style="margin-top:0.8rem;opacity:.45" disabled>Out of stock</button>
    @endif
</article>
