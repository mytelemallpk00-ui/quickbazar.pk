@php
    $trust = \App\Support\ProductTrustSettings::forStorefront();
    $trustItems = is_array($trust['trust_section']['items'] ?? null) ? $trust['trust_section']['items'] : [];
    $qualityItems = is_array($trust['quality_section']['items'] ?? null) ? $trust['quality_section']['items'] : [];
    $deliveryItem = collect($qualityItems)->first(fn ($item) => ($item['icon'] ?? '') === 'truck') ?? ($qualityItems[3] ?? null);
    $assureCards = [
        [
            'kicker' => 'Trust',
            'icon' => $trustItems[0]['icon'] ?? 'shield-check',
            'title' => $trustItems[0]['title'] ?? '100% original products',
            'text' => $trustItems[0]['description'] ?? 'Authorized catalog only. Every item is checked before it leaves our hub.',
        ],
        [
            'kicker' => 'Authenticity',
            'icon' => $trustItems[1]['icon'] ?? 'badge-check',
            'title' => $trustItems[1]['title'] ?? 'Verified before dispatch',
            'text' => $trustItems[1]['description'] ?? 'Seal, packaging, and expiry are inspected on premium orders.',
        ],
        [
            'kicker' => 'Delivery',
            'icon' => $deliveryItem['icon'] ?? 'truck',
            'title' => $deliveryItem['title'] ?? 'Cash on delivery nationwide',
            'text' => $deliveryItem['description'] ?? 'Tracked door-to-door shipping across Pakistan.',
        ],
    ];
@endphp
<section class="qb-highlights qb-highlights--assure" aria-label="Trust, authenticity, and delivery">
    <div class="qb-assure">
        @foreach ($assureCards as $card)
        <article>
            <span class="qb-assure-ico" aria-hidden="true"><i data-lucide="{{ $card['icon'] }}"></i></span>
            <div>
                <p>{{ $card['kicker'] }}</p>
                <h3>{{ $card['title'] }}</h3>
                <span>{{ $card['text'] }}</span>
            </div>
        </article>
        @endforeach
    </div>
</section>
