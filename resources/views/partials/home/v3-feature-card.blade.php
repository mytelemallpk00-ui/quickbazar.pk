@php
    $card = $product ?? [];
    $name = (string) ($card['name'] ?? 'Product');
    $url = (string) ($card['url'] ?? route('collections'));
    $image = $card['image'] ?: ($placeholder ?? 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan');
    $inStock = ! empty($card['in_stock']);
    $label = filled($card['category'] ?? null) ? (string) $card['category'] : 'Featured';
    $blurb = trim((string) ($card['short'] ?? ''));
    $fallback = 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan';
@endphp
<article class="lv3-feature">
    <div class="lv3-feature-top">
        <span class="lv3-pill">{{ $label }}</span>
        @if (filled($card['sku'] ?? null))
            <span class="lv3-sku">SKU: {{ $card['sku'] }}</span>
        @endif
    </div>
    <a class="lv3-feature-photo" href="{{ $url }}">
        <img src="{{ $image }}" alt="{{ $name }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $fallback }}';">
    </a>
    <h3><a href="{{ $url }}">{{ $name }}</a></h3>
    @if ($blurb !== '')
        <p class="lv3-blurb">{{ $blurb }}</p>
    @endif
    <div class="lv3-feature-buy">
        <p class="lv3-price">
            <strong>{{ $card['price_label'] ?? '' }}</strong>
            @if (! empty($card['old_price_label']))
                <s>{{ $card['old_price_label'] }}</s>
            @endif
        </p>
        @if ($inStock)
            <button type="button" class="lv3-gold" @include('partials.home.v3-card-attrs', ['card' => $card, 'placeholder' => $image, 'mode' => 'add'])>Add to Bag</button>
        @else
            <a class="lv3-gold" href="{{ $url }}">View product</a>
        @endif
    </div>
</article>
