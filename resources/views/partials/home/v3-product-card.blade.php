@php
    $card = $product ?? [];
    $name = (string) ($card['name'] ?? 'Product');
    $url = (string) ($card['url'] ?? route('collections'));
    $image = $card['image'] ?: ($placeholder ?? 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan');
    $hover = trim((string) ($card['hover_image'] ?? ''));
    if ($hover === $image) {
        $hover = '';
    }
    $rating = max(0, min(5, (float) ($card['rating'] ?? 0)));
    $full = (int) round($rating);
    $inStock = ! empty($card['in_stock']);
    $fallback = 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan';
@endphp
<article class="lv3-card">
    <div class="lv3-media">
        <button type="button" class="lv3-shot{{ $hover !== '' ? ' has-alt' : '' }}" @include('partials.home.v3-card-attrs', ['card' => $card, 'placeholder' => $image, 'mode' => 'quick']) aria-label="Quick view {{ $name }}">
            <img class="base" src="{{ $image }}" alt="" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $fallback }}';">
            @if ($hover !== '')
                <img class="alt" src="{{ $hover }}" alt="" width="640" height="640" loading="lazy" decoding="async">
            @endif
        </button>
        @if (! empty($card['discount_percent']))
            <span class="lv3-flag">{{ (int) $card['discount_percent'] }}% OFF</span>
        @elseif (! empty($flag))
            <span class="lv3-flag">{{ $flag }}</span>
        @endif
        <button
            type="button"
            class="lv3-wish"
            data-lv3-wish
            data-id="{{ (int) ($card['id'] ?? 0) }}"
            data-name="{{ $name }}"
            data-price="{{ $card['price_label'] ?? '' }}"
            data-raw-price="{{ $card['price'] ?? 0 }}"
            data-image="{{ $image }}"
            data-url="{{ $url }}"
            aria-pressed="false"
            aria-label="Save {{ $name }}"
        >
            <i data-lucide="heart"></i>
        </button>
    </div>
    <div class="lv3-copy">
        @if (filled($card['category'] ?? null))
            <p class="lv3-dept">{{ $card['category'] }}</p>
        @endif
        <h3><a href="{{ $url }}">{{ $name }}</a></h3>
        @if (($card['reviews'] ?? 0) > 0)
            <p class="lv3-stars" aria-label="Rated {{ number_format($rating, 1) }} out of 5">{{ str_repeat('★', $full) }}{{ str_repeat('☆', 5 - $full) }} <span>({{ number_format((int) $card['reviews']) }})</span></p>
        @endif
    </div>
    <div class="lv3-foot">
        <p class="lv3-price">
            <strong>{{ $card['price_label'] ?? '' }}</strong>
            @if (! empty($card['old_price_label']))
                <s>{{ $card['old_price_label'] }}</s>
            @endif
        </p>
        <div class="lv3-actions">
            <button type="button" class="lv3-eye" @include('partials.home.v3-card-attrs', ['card' => $card, 'placeholder' => $image, 'mode' => 'quick']) aria-label="Quick view {{ $name }}">
                <i data-lucide="eye"></i>
            </button>
            @if ($inStock)
                <button type="button" class="lv3-bag" @include('partials.home.v3-card-attrs', ['card' => $card, 'placeholder' => $image, 'mode' => 'add'])>
                    <i data-lucide="shopping-bag"></i> Add to Bag
                </button>
            @else
                <a class="lv3-bag" href="{{ $url }}">View product</a>
            @endif
        </div>
    </div>
</article>
