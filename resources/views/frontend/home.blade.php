@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: 'Beautage — Elevate Your Natural Elegance')

@section('body_attrs')
class="lumiere-v2 lv3-body font-sans antialiased min-h-screen flex flex-col overflow-x-clip"
@endsection

@push('structured-data')
@php
    $brandName = \App\Support\SiteSettings::get('site_name', 'Beautage');
    $homeUrl = route('home');
    $logo = \App\Support\SiteSettings::logoUrl();
    if (! str_starts_with($logo, 'http://') && ! str_starts_with($logo, 'https://')) {
        $logo = url($logo);
    }
    $homeJsonLd = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Organization',
                'name' => $brandName,
                'url' => $homeUrl,
                'logo' => $logo,
            ],
            [
                '@type' => 'WebSite',
                'name' => $brandName,
                'url' => $homeUrl,
                'publisher' => ['@type' => 'Organization', 'name' => $brandName],
            ],
        ],
    ];
@endphp
@include('partials.json-ld-script', ['jsonLd' => $homeJsonLd])
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/lumiere-home-v3.css') }}?v=2026110819">
@endpush

@section('content')
@php
    $promo = $promoProduct;
    $placeholder = 'https://placehold.co/640x640/0B132B/D4AF37?text=Shop+Pakistan';
    $heroName = (string) ($promo['name'] ?? '');
    $words = $heroName !== '' ? preg_split('/\s+/', $heroName) : [];
    $words = is_array($words) ? $words : [];
    if (count($words) > 1) {
        $heroLead = implode(' ', array_slice($words, 0, -1));
        $heroAccentWord = (string) end($words);
    } else {
        $heroLead = $heroName !== '' ? $heroName : (string) ($hero['title'] ?? 'Shop the catalog');
        $heroAccentWord = $heroName !== '' ? '' : (string) ($hero['accent'] ?? '');
    }
    $heroText = (string) ($hero['description'] ?? '');
    if ($heroText === '' && filled($promo['short'] ?? null)) {
        $heroText = (string) $promo['short'];
    }
    if ($heroText === '') {
        $heroText = 'Original products, PKR prices, cash on delivery, and nationwide shipping from the live catalog.';
    }
    $catalog = collect($trendingProducts ?? [])->values();
    $flashCards = $catalog->filter(fn ($row) => ! empty($row['discount_percent']))->take(4)->values();
    if ($flashCards->isEmpty()) {
        $flashCards = $catalog->take(4)->values();
    }
    $flashIds = $flashCards->pluck('id')->all();
    $trendingCards = $catalog->reject(fn ($row) => in_array($row['id'] ?? null, $flashIds, true))->take(3)->values();
    if ($trendingCards->count() < min(3, $catalog->count())) {
        $trendingCards = $catalog->take(3)->values();
    }
    $trendIds = $trendingCards->pluck('id')->all();
    $bestCards = $catalog->reject(fn ($row) => in_array($row['id'] ?? null, $trendIds, true))->take(4)->values();
    if ($bestCards->isEmpty()) {
        $bestCards = $catalog->take(4)->values();
    }
    $galleryCards = $catalog->take(4)->values();
    $wideProduct = $flashProduct && (int) ($flashProduct['id'] ?? 0) !== (int) ($promo['id'] ?? 0)
        ? $flashProduct
        : ($bestCards->first() ?: $promo);
    $quizPool = $catalog->map(fn ($row) => [
        'id' => (int) ($row['id'] ?? 0),
        'name' => (string) ($row['name'] ?? ''),
        'category' => (string) ($row['category'] ?? ''),
        'short' => (string) ($row['short'] ?? ''),
        'price_label' => (string) ($row['price_label'] ?? ''),
        'old_price_label' => $row['old_price_label'] ?? null,
        'price' => $row['price'] ?? 0,
        'old_price' => $row['old_price'] ?? null,
        'image' => $row['image'] ?: $placeholder,
        'url' => (string) ($row['url'] ?? route('collections')),
        'in_stock' => (bool) ($row['in_stock'] ?? false),
    ])->values();
    $iconTones = ['navy', 'amber', 'indigo', 'rose', 'teal', 'gold'];
@endphp
<main class="lv3">
    @if ($sections['hero'] ?? true)
    <section class="lv3-wrap" aria-labelledby="home-hero-title">
        <div class="lv3-hero">
            <div class="lv3-hero-grid">
                <div class="lv3-hero-copy">
                    <span class="lv3-kicker"><i data-lucide="sparkles"></i> {{ $hero['badge'] ?? 'Flagship catalog' }}</span>
                    <h1 id="home-hero-title">
                        {{ $heroLead }}
                        @if ($heroAccentWord !== '')
                            <span class="lv3-accent">{{ $heroAccentWord }}</span>
                        @endif
                    </h1>
                    <p class="lv3-lead">{{ $heroText }}</p>
                    <div class="lv3-hero-actions">
                        <a class="lv3-gold" href="{{ route('collections') }}">Explore Collections <i data-lucide="arrow-right"></i></a>
                        @if ($promo)
                            <button type="button" class="lv3-ghost" @include('partials.home.v3-card-attrs', ['card' => $promo, 'placeholder' => $promo['image'] ?: $placeholder, 'mode' => 'quick'])>Quick View Formula</button>
                        @else
                            <a class="lv3-ghost" href="{{ route('collections') }}">Browse the catalog</a>
                        @endif
                    </div>
                </div>
                <div class="lv3-hero-card">
                    @if ($promo)
                        @if (! empty($promo['discount_percent']))
                            <span class="lv3-vip">SAVE {{ (int) $promo['discount_percent'] }}%</span>
                        @else
                            <span class="lv3-vip">FROM THE CATALOG</span>
                        @endif
                        <img src="{{ $promo['image'] ?: $placeholder }}" alt="{{ $promo['name'] }}" width="640" height="640" fetchpriority="high" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        <h2 class="lv3-hero-name">{{ $promo['name'] }}</h2>
                        @if (($promo['reviews'] ?? 0) > 0)
                            <p class="lv3-stars">★★★★★ <span>({{ number_format((float) $promo['rating'], 1) }} / {{ number_format((int) $promo['reviews']) }} reviews)</span></p>
                        @endif
                        <p class="lv3-price lv3-price-lg">
                            <strong>{{ $promo['price_label'] }}</strong>
                            @if (! empty($promo['old_price_label']))
                                <s>{{ $promo['old_price_label'] }}</s>
                            @endif
                        </p>
                        @if (! empty($promo['in_stock']))
                            <button type="button" class="lv3-gold lv3-block" @include('partials.home.v3-card-attrs', ['card' => $promo, 'placeholder' => $promo['image'] ?: $placeholder, 'mode' => 'add'])>Add To Bag</button>
                        @else
                            <a class="lv3-gold lv3-block" href="{{ $promo['url'] }}">View product</a>
                        @endif
                    @else
                        <h2 class="lv3-hero-name">Shop the live catalog</h2>
                        <p class="lv3-lead">Products appear here as soon as they are published.</p>
                        <a class="lv3-gold lv3-block" href="{{ route('collections') }}">Browse collections</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @else
    <h1 class="lv3-sr">{{ $pageSeo['title'] ?? 'Home' }}</h1>
    @endif

    @if ($sections['trust'] ?? true)
    <section class="lv3-wrap" aria-labelledby="home-trust-title">
        <h2 id="home-trust-title" class="lv3-sr">Shop with confidence</h2>
        <div class="lv3-trust">
            <article>
                <span class="lv3-ico"><i data-lucide="truck"></i></span>
                <div><h3>Nationwide Delivery</h3><p>Express courier across Pakistan</p></div>
            </article>
            <article>
                <span class="lv3-ico"><i data-lucide="rotate-ccw"></i></span>
                <div><h3>30-Day Guarantee</h3><p>Returns from the returns page</p></div>
            </article>
            <article>
                <span class="lv3-ico"><i data-lucide="headphones"></i></span>
                <div><h3>Support Team</h3><p>Help with orders and products</p></div>
            </article>
            <article>
                <span class="lv3-ico"><i data-lucide="award"></i></span>
                <div><h3>Live Catalog Prices</h3><p>PKR prices from each product</p></div>
            </article>
        </div>
    </section>
    @endif

    @if ($sections['categories'] ?? true)
    <section id="categories" class="lv3-wrap lv3-section" aria-labelledby="home-categories-title">
        <div class="lv3-head">
            <div>
                <span class="lv3-eyebrow">Curated Selections</span>
                <h2 id="home-categories-title">Shop By Category</h2>
            </div>
            <a class="lv3-link" href="{{ route('collections') }}">View All Collections <i data-lucide="arrow-right"></i></a>
        </div>
        @if ($featuredCategories->isEmpty())
            <p class="lv3-empty">Categories will show here once they are published in the catalog.</p>
        @else
            <div class="lv3-cats">
                @foreach ($featuredCategories->take(6) as $category)
                    <a class="lv3-cat" href="{{ $category->collectionsUrl() }}">
                        <span class="lv3-cat-ico tone-{{ $iconTones[$loop->index % count($iconTones)] }}" aria-hidden="true">
                            <i data-lucide="{{ $category->iconName() }}"></i>
                        </span>
                        <h3>{{ $category->displayNavLabel() }}</h3>
                        <span>{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'product' : 'products' }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($sections['flash'] ?? true)
    <section id="flash-deals" class="lv3-wrap lv3-section" aria-labelledby="home-flash-title">
        <div class="lv3-flash">
            <div class="lv3-flash-copy">
                <span class="lv3-ico lg"><i data-lucide="zap"></i></span>
                <div>
                    <span class="lv3-pill on-dark">{{ $flash['badge'] ?? 'Limited VIP Offer' }}</span>
                    <h2 id="home-flash-title">Flash Deals of the Day</h2>
                    <p>{{ $flash['description'] ?? 'Secure catalog deals before the day ends.' }}</p>
                </div>
            </div>
            <div class="lv3-timer" data-ends="{{ $flash['ends_at'] }}" aria-live="polite">
                <div><strong id="hours">{{ $flash['hours'] }}</strong><span>Hours</span></div>
                <b aria-hidden="true">:</b>
                <div><strong id="minutes">{{ $flash['minutes'] }}</strong><span>Mins</span></div>
                <b aria-hidden="true">:</b>
                <div><strong id="seconds">{{ $flash['seconds'] }}</strong><span>Secs</span></div>
            </div>
        </div>
        @if ($flashCards->isEmpty())
            <p class="lv3-empty">Flash deals appear when products are in the catalog.</p>
        @else
            <div class="lv3-grid lv3-grid-4">
                @foreach ($flashCards as $product)
                    @include('partials.home.v3-product-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($sections['trending'] ?? true)
    <section id="trending" class="lv3-wrap lv3-section" aria-labelledby="home-trending-title">
        <div class="lv3-head">
            <div>
                <span class="lv3-eyebrow">{{ $trendingCopy['badge'] ?? 'From the catalog' }}</span>
                <h2 id="home-trending-title">{{ $trendingCopy['heading'] ?? 'Trending right now' }}</h2>
            </div>
            <a class="lv3-link" href="{{ route('collections') }}">View All <i data-lucide="arrow-right"></i></a>
        </div>
        @if ($trendingCards->isEmpty())
            <p class="lv3-empty">No products are published yet.</p>
        @else
            <div class="lv3-grid lv3-grid-3">
                @foreach ($trendingCards as $product)
                    @include('partials.home.v3-feature-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    <section id="quiz-section" class="lv3-wrap lv3-section" aria-labelledby="home-quiz-title">
        <div class="lv3-quiz">
            <div class="lv3-center">
                <span class="lv3-pill on-dark">Haute Diagnostic</span>
                <h2 id="home-quiz-title">Personalized Beauty & Hair Quiz</h2>
                <p>Answer two questions and we’ll point you to a product from the live catalog, with its PKR price.</p>
            </div>
            @if ($quizPool->isEmpty())
                <p class="lv3-empty light">The quiz opens once products are published. <a href="{{ route('collections') }}">Browse collections</a></p>
            @else
                <div id="lv3-quiz-step-1" class="lv3-quiz-panel">
                    <h3>1. What are you shopping for?</h3>
                    <div class="lv3-quiz-choices">
                        <button type="button" data-quiz-step="1" data-quiz-key="hair">Hair & scalp care</button>
                        <button type="button" data-quiz-step="1" data-quiz-key="skin">Skin & body care</button>
                    </div>
                </div>
                <div id="lv3-quiz-step-2" class="lv3-quiz-panel" hidden>
                    <h3>2. What kind of formula do you want?</h3>
                    <div class="lv3-quiz-choices">
                        <button type="button" data-quiz-step="2" data-quiz-key="repair">Repair and moisture</button>
                        <button type="button" data-quiz-step="2" data-quiz-key="daily">Everyday care</button>
                    </div>
                </div>
                <div id="lv3-quiz-result" class="lv3-quiz-panel" hidden>
                    <img id="lv3-quiz-img" alt="" width="120" height="120">
                    <h3 id="lv3-quiz-name"></h3>
                    <p id="lv3-quiz-blurb"></p>
                    <p class="lv3-price lv3-price-lg" id="lv3-quiz-price"></p>
                    <div class="lv3-quiz-actions">
                        <button type="button" id="lv3-quiz-add" class="lv3-gold">Add to Bag</button>
                        <a id="lv3-quiz-link" class="lv3-ghost" href="{{ route('collections') }}">View product</a>
                        <button type="button" id="lv3-quiz-reset" class="lv3-ghost">Retake Quiz</button>
                    </div>
                </div>
                <script type="application/json" id="lv3-quiz-data">@json($quizPool)</script>
            @endif
        </div>
    </section>

    @if ($wideProduct)
    <section id="bundles" class="lv3-wrap lv3-section" aria-labelledby="home-bundle-title">
        <div class="lv3-bundle">
            <span class="lv3-pill on-dark">Catalog highlight</span>
            <h2 id="home-bundle-title">{{ $wideProduct['name'] }}</h2>
            <p>{{ filled($wideProduct['short'] ?? null) ? $wideProduct['short'] : 'This price is the live catalog price in PKR. Checkout still supports cash on delivery.' }}</p>
            <p class="lv3-price lv3-price-lg">
                <strong>{{ $wideProduct['price_label'] }}</strong>
                @if (! empty($wideProduct['old_price_label']))
                    <s>{{ $wideProduct['old_price_label'] }}</s>
                @endif
            </p>
            <div class="lv3-hero-actions">
                @if (! empty($wideProduct['in_stock']))
                    <button type="button" class="lv3-gold" @include('partials.home.v3-card-attrs', ['card' => $wideProduct, 'placeholder' => $wideProduct['image'] ?: $placeholder, 'mode' => 'add'])>Add to Bag <i data-lucide="arrow-right"></i></button>
                @endif
                <a class="lv3-ghost" href="{{ $wideProduct['url'] }}">View this product</a>
            </div>
        </div>
    </section>
    @endif

    <section id="bestsellers" class="lv3-wrap lv3-section" aria-labelledby="home-best-title">
        <div class="lv3-head">
            <div>
                <span class="lv3-eyebrow">Iconic picks</span>
                <h2 id="home-best-title">Best Sellers</h2>
            </div>
            <a class="lv3-link" href="{{ route('collections') }}">View All <i data-lucide="arrow-right"></i></a>
        </div>
        @if ($bestCards->isEmpty())
            <p class="lv3-empty">Best sellers will show here when products are published.</p>
        @else
            <div class="lv3-grid lv3-grid-4">
                @foreach ($bestCards as $product)
                    @include('partials.home.v3-product-card', ['product' => $product, 'placeholder' => $placeholder, 'flag' => 'BEST SELLER'])
                @endforeach
            </div>
        @endif
    </section>

    @if ($galleryCards->isNotEmpty())
    <section id="ugc-gallery" class="lv3-wrap lv3-section" aria-labelledby="home-gallery-title">
        <div class="lv3-gallery">
            <div class="lv3-head">
                <div>
                    <span class="lv3-eyebrow">From the catalog</span>
                    <h2 id="home-gallery-title">Shop the edit</h2>
                    <p class="lv3-muted">Real product photos from the store, with the same PKR prices as the product page.</p>
                </div>
                <a class="lv3-bag" href="{{ route('collections') }}">View the catalog</a>
            </div>
            <div class="lv3-grid lv3-grid-4">
                @foreach ($galleryCards as $product)
                    <a class="lv3-ugc" href="{{ $product['url'] }}">
                        <img src="{{ $product['image'] ?: $placeholder }}" alt="{{ $product['name'] }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        <span>
                            <strong>{{ $product['name'] }}</strong>
                            <em>{{ $product['price_label'] }}</em>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (($sections['journal'] ?? true) && $journalPosts->isNotEmpty())
    <section id="blog-section" class="lv3-wrap lv3-section" aria-labelledby="home-journal-title">
        <div class="lv3-center">
            <span class="lv3-eyebrow">Expert Editorial</span>
            <h2 id="home-journal-title">The Beauty Journal</h2>
            <p class="lv3-muted">Guides published by the store.</p>
        </div>
        <div class="lv3-grid lv3-grid-3">
            @foreach ($journalPosts as $post)
                <article class="lv3-post">
                    <a href="{{ route('blog.show', $post) }}">
                        <span class="lv3-post-photo">
                            <img src="{{ $post->image }}" alt="{{ $post->title }}" width="640" height="360" loading="lazy" decoding="async">
                            @if (filled($post->category))
                                <em>{{ $post->category }}</em>
                            @endif
                        </span>
                        <span class="lv3-post-copy">
                            <small>
                                @if ($post->published_at)
                                    {{ $post->published_at->format('F j, Y') }}
                                @endif
                                @if (filled($post->read_time))
                                    · {{ $post->read_time }}
                                @endif
                            </small>
                            <h3>{{ $post->title }}</h3>
                            <p>{{ $post->snippet }}</p>
                            <strong>Read Full Article <i data-lucide="arrow-right"></i></strong>
                        </span>
                    </a>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <section id="faq" class="lv3-wrap lv3-section" aria-labelledby="home-faq-title">
        <div class="lv3-faq-box">
            <div class="lv3-center">
                <span class="lv3-eyebrow">VIP Concierge</span>
                <h2 id="home-faq-title">Frequently Asked Questions</h2>
                <p class="lv3-muted">Delivery, payment, and returns for this store.</p>
            </div>
            <div class="lv3-faq-list">
                <details open>
                    <summary>Do you deliver across Pakistan?</summary>
                    <p>Yes. Orders ship nationwide. You can check a city during checkout and follow the parcel from the <a href="{{ route('track-order') }}">track order</a> page.</p>
                </details>
                <details>
                    <summary>Can I pay cash on delivery?</summary>
                    <p>Cash on delivery is available for eligible orders. <a href="{{ route('checkout') }}">Checkout</a> shows the payment choices for your cart.</p>
                </details>
                <details>
                    <summary>What is the 30-day return policy?</summary>
                    <p>Most items can be returned within 30 days. Open the <a href="{{ route('returns') }}">returns page</a>, keep your order number, and follow the steps listed there. Shipping details are on the <a href="{{ route('shipping') }}">shipping page</a>.</p>
                </details>
                <details>
                    <summary>Are the prices the live catalog prices?</summary>
                    <p>Yes. Product cards and the product page use the PKR prices stored on each product. A crossed-out price appears only when the catalog has a higher previous price.</p>
                </details>
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    window.products = @json($productsCatalog);
</script>
<script src="{{ asset('js/lumiere-home-v3.js') }}?v=2026110819" defer></script>
@endpush
