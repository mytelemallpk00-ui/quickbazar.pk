@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: 'Quick Bazar — Elevate Your Natural Elegance')

@section('body_attrs')
class="lumiere-v2 bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col overflow-x-clip"
@endsection

@push('structured-data')
@php
    $brandName = \App\Support\SiteSettings::get('site_name', 'Quick Bazar');
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

@section('content')
@php
    $promo = $promoProduct;
    $placeholder = 'https://placehold.co/640x640/0B132B/D4AF37?text=Quick+Bazar';
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
    $heroText = filled($promo['category'] ?? null)
        ? trim((string) ($hero['description'] ?? ''))
        : (string) ($hero['description'] ?? '');
    if ($heroText === '') {
        $heroText = 'Original products, PKR prices, cash on delivery, and nationwide shipping from the live catalog.';
    }
    $flashCards = collect($trendingProducts ?? [])->filter(fn ($row) => ! empty($row['discount_percent']))->take(4)->values();
    if ($flashCards->isEmpty()) {
        $flashCards = collect($trendingProducts ?? [])->take(4)->values();
    }
    $bestCards = collect($trendingProducts ?? [])->count() > 4
        ? collect($trendingProducts)->slice(4, 4)->values()
        : collect($trendingProducts ?? [])->take(4)->values();
    $wideProduct = $flashProduct && (int) ($flashProduct['id'] ?? 0) !== (int) ($promo['id'] ?? 0)
        ? $flashProduct
        : ($bestCards->first() ?: $promo);
    $reviewCount = (int) ($reviewSummary['count'] ?? 0);
    $reviewAverage = (float) ($reviewSummary['average'] ?? 0);
@endphp
<main class="lv2-page">
    @if ($sections['hero'] ?? true)
    <section class="lv2-wrap" aria-labelledby="home-hero-title">
        <div class="lv2-hero">
            <div class="lv2-hero-grid">
                <div>
                    <span class="lv2-kicker">{{ $hero['badge'] ?? 'Flagship catalog' }}</span>
                    <h1 id="home-hero-title" class="lv2-h1">
                        {{ $heroLead }}
                        @if ($heroAccentWord !== '')
                            <span class="lv2-accent">{{ $heroAccentWord }}</span>
                        @endif
                    </h1>
                    <p class="lv2-lead">{{ $heroText }}</p>
                    <div class="lv2-actions">
                        <a class="lv2-btn" href="{{ $promo['url'] ?? route('collections') }}">Shop Now</a>
                        <a class="lv2-btn-ghost" href="#bestsellers">Explore Formula</a>
                    </div>
                </div>
                <div class="lv2-hero-card">
                    @if ($promo)
                        @if (! empty($promo['discount_percent']))
                            <span class="lv2-save">SAVE {{ (int) $promo['discount_percent'] }}%</span>
                        @endif
                        <img src="{{ $promo['image'] ?: $placeholder }}" alt="{{ $promo['name'] }}" width="640" height="640" fetchpriority="high" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        <p class="lv2-card-name">{{ $promo['name'] }}</p>
                        @if (($promo['reviews'] ?? 0) > 0)
                            <p class="lv2-stars">★★★★★ <span style="color:#d1d5db">({{ number_format((float) $promo['rating'], 1) }} / {{ number_format((int) $promo['reviews']) }} reviews)</span></p>
                        @endif
                        <p class="lv2-price-row">
                            <span class="lv2-price">{{ $promo['price_label'] }}</span>
                            @if (! empty($promo['old_price_label']))
                                <span class="lv2-old">{{ $promo['old_price_label'] }}</span>
                            @endif
                        </p>
                        @if (! empty($promo['in_stock']))
                            <button type="button" class="lv2-btn" style="width:100%;margin-top:0.8rem" onclick="addToCart({{ (int) $promo['id'] }})">Add To Cart</button>
                        @else
                            <a class="lv2-btn" style="width:100%;margin-top:0.8rem" href="{{ $promo['url'] }}">View product</a>
                        @endif
                    @else
                        <h2>Shop the live catalog</h2>
                        <p class="lv2-lead">Products appear here as soon as they are published.</p>
                        <a class="lv2-btn" href="{{ route('collections') }}">Browse collections</a>
                    @endif
                </div>
            </div>
        </div>
    </section>
    @else
    <h1 class="sr-only">{{ $pageSeo['title'] ?? 'Home' }}</h1>
    @endif

    @if ($sections['trust'] ?? true)
    <section class="lv2-wrap" aria-labelledby="home-trust-title">
        <h2 id="home-trust-title" class="sr-only">Shop with confidence</h2>
        <div class="lv2-trust">
            <article>
                <span class="lv2-icon" aria-hidden="true"><i data-lucide="truck"></i></span>
                <div><h3>Express Shipping</h3><p>Nationwide delivery</p></div>
            </article>
            <article>
                <span class="lv2-icon" aria-hidden="true"><i data-lucide="rotate-ccw"></i></span>
                <div><h3>30-Day Guarantee</h3><p>Hassle-free returns</p></div>
            </article>
            <article>
                <span class="lv2-icon" aria-hidden="true"><i data-lucide="headphones"></i></span>
                <div><h3>24/7 Support</h3><p>Dedicated expert team</p></div>
            </article>
            <article>
                <span class="lv2-icon" aria-hidden="true"><i data-lucide="award"></i></span>
                <div><h3>Certified Safe</h3><p>Original catalog products</p></div>
            </article>
        </div>
    </section>
    @endif

    @if ($sections['categories'] ?? true)
    <section id="categories" class="lv2-wrap lv2-section" aria-labelledby="home-categories-title">
        <div class="lv2-head">
            <div>
                <h2 id="home-categories-title">Shop By Category</h2>
                <p class="lv2-muted">Explore our curated collections for head-to-toe radiance</p>
            </div>
            <a class="lv2-link" href="{{ route('collections') }}">View All</a>
        </div>
        @if ($featuredCategories->isEmpty())
            <p class="lv2-empty">Categories will show here once they are published in the catalog.</p>
        @else
            <div class="lv2-cats">
                @foreach ($featuredCategories as $category)
                    <a class="lv2-cat" href="{{ $category->collectionsUrl() }}">
                        <span class="lv2-cat-icon" aria-hidden="true"><i data-lucide="{{ $category->iconName() }}"></i></span>
                        <h3>{{ $category->displayNavLabel() }}</h3>
                        <span class="lv2-muted">{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'item' : 'items' }}</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($sections['flash'] ?? true)
    <section class="lv2-wrap lv2-section" aria-labelledby="home-flash-title">
        <div class="lv2-flash">
            <div>
                <span class="lv2-kicker">Limited Time</span>
                <h2 id="home-flash-title">Flash Deals of the Day</h2>
                <p>{{ $flash['description'] ?? 'Grab top sellers before stocks run out.' }}</p>
            </div>
            <div class="lv2-timer" aria-live="polite">
                <div><strong id="hours">{{ $flash['hours'] }}</strong><span>Hours</span></div>
                <div><strong id="minutes">{{ $flash['minutes'] }}</strong><span>Minutes</span></div>
                <div><strong id="seconds">{{ $flash['seconds'] }}</strong><span>Seconds</span></div>
            </div>
        </div>
        @if ($flashCards->isEmpty())
            <p class="lv2-empty">Flash deals appear when discounted products are in the catalog.</p>
        @else
            <div class="lv2-grid lv2-grid-4">
                @foreach ($flashCards as $product)
                    @include('partials.home.lv2-product-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($sections['trending'] ?? true)
    <section id="trending" class="lv2-wrap lv2-section" aria-labelledby="home-trending-title">
        <div class="lv2-head">
            <div>
                <h2 id="home-trending-title">{{ $trendingCopy['heading'] ?? 'Trending now' }}</h2>
                <p class="lv2-muted">{{ $trendingCopy['description'] ?? '' }}</p>
            </div>
            <a class="lv2-link" href="{{ route('collections') }}">View All</a>
        </div>
        @if (collect($trendingProducts)->isEmpty())
            <p class="lv2-empty">No products are published yet.</p>
        @else
            <div class="lv2-grid lv2-grid-4">
                @foreach (collect($trendingProducts)->take(8) as $product)
                    @include('partials.home.lv2-product-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($wideProduct)
    <section id="bundles" class="lv2-wrap">
        <div class="lv2-wide">
            <div>
                <span class="lv2-kicker">Bundle the routine</span>
                <h2>{{ $wideProduct['name'] }}</h2>
                <p class="lv2-lead">Pair it with the rest of the catalog. Prices stay in PKR, and checkout still supports cash on delivery.</p>
                <div class="lv2-actions">
                    <a class="lv2-btn" href="{{ $wideProduct['url'] }}">View this product</a>
                    <a class="lv2-btn-ghost" href="{{ route('exclusive-offers') }}">See offers</a>
                </div>
            </div>
            <img src="{{ $wideProduct['image'] ?: $placeholder }}" alt="{{ $wideProduct['name'] }}" width="720" height="480" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
        </div>
    </section>
    @endif

    <section id="bestsellers" class="lv2-wrap lv2-section" aria-labelledby="home-best-title">
        <div class="lv2-head">
            <div>
                <h2 id="home-best-title">Best Sellers</h2>
                <p class="lv2-muted">Highest-rated products currently in the catalog.</p>
            </div>
            <a class="lv2-link" href="{{ route('collections') }}">View All</a>
        </div>
        @if ($bestCards->isEmpty())
            <p class="lv2-empty">Best sellers will show here when products are published.</p>
        @else
            <div class="lv2-grid lv2-grid-4">
                @foreach ($bestCards as $product)
                    @include('partials.home.lv2-product-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>

    @if ($sections['steps'] ?? true)
    <section class="lv2-wrap lv2-section" aria-labelledby="home-steps-title">
        <div class="lv2-head"><h2 id="home-steps-title">How it works</h2></div>
        <ol class="lv2-steps">
            <li class="lv2-step"><span class="lv2-step-no">1</span><h3>Choose a product</h3><p class="lv2-muted">Browse departments, compare PKR prices, and open any product page.</p></li>
            <li class="lv2-step"><span class="lv2-step-no">2</span><h3>Checkout your way</h3><p class="lv2-muted">Pay cash on delivery, or use the methods shown at checkout.</p></li>
            <li class="lv2-step"><span class="lv2-step-no">3</span><h3>Track the parcel</h3><p class="lv2-muted">Nationwide shipping with order tracking after the order is placed.</p></li>
        </ol>
    </section>
    @endif

    @if (($sections['reviews'] ?? true) && $latestReviews->isNotEmpty())
    <section id="reviews" class="lv2-wrap lv2-section" aria-labelledby="home-reviews-title">
        <div class="lv2-center">
            <p class="lv2-stars" aria-hidden="true">★★★★★</p>
            <h2 id="home-reviews-title">Loved by our customers</h2>
            <p class="lv2-muted">{{ number_format($reviewAverage, 1) }} average from {{ number_format($reviewCount) }} published {{ $reviewCount === 1 ? 'review' : 'reviews' }}.</p>
        </div>
        <div class="lv2-reviews">
            @foreach ($latestReviews as $review)
                <article class="lv2-review">
                    <h3>{{ $review->author_name }}</h3>
                    <p class="lv2-stars">{{ str_repeat('★', (int) round((float) $review->rating)) }}</p>
                    @if ($review->title)
                        <p style="margin:0.4rem 0 0;font-weight:700;color:#0b132b">{{ $review->title }}</p>
                    @endif
                    <p class="lv2-muted" style="font-size:0.9rem;line-height:1.55">{{ $review->body }}</p>
                    @if ($review->product)
                        <a class="lv2-link" href="{{ route('product.show', $review->product) }}">{{ $review->product->name }}</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <section class="lv2-wrap lv2-section" aria-labelledby="home-shipping-title">
        <div class="lv2-head"><h2 id="home-shipping-title">Shipping and returns</h2></div>
        <div class="lv2-ship-grid">
            <article class="lv2-ship-card">
                <h3>Nationwide shipping</h3>
                <p class="lv2-muted">Orders ship across Pakistan. Delivery timing depends on the city you enter at checkout.</p>
                <a class="lv2-link" href="{{ route('shipping') }}">Shipping details</a>
            </article>
            <article class="lv2-ship-card">
                <h3>30-day returns</h3>
                <p class="lv2-muted">Start a return from the returns page if an item is not right. Keep the order number handy.</p>
                <a class="lv2-link" href="{{ route('returns') }}">Returns policy</a>
            </article>
            <article class="lv2-ship-card">
                <h3>Cash on delivery</h3>
                <p class="lv2-muted">Pay in PKR when the parcel arrives, or choose another method shown at checkout.</p>
                <a class="lv2-link" href="{{ route('checkout') }}">Go to checkout</a>
            </article>
        </div>
    </section>

    <section class="lv2-wrap lv2-section" aria-labelledby="home-faq-title">
        <div class="lv2-head"><h2 id="home-faq-title">Frequently asked questions</h2></div>
        <div class="lv2-faq-list">
            <details class="lv2-faq" open>
                <summary>Do you deliver across Pakistan?</summary>
                <p>Yes. The store ships nationwide. You can check a city during checkout and follow the parcel from the track-order page.</p>
            </details>
            <details class="lv2-faq">
                <summary>Can I pay cash on delivery?</summary>
                <p>Cash on delivery is available for eligible orders. The checkout page shows the payment choices for your cart.</p>
            </details>
            <details class="lv2-faq">
                <summary>How do returns work?</summary>
                <p>Most items can be returned within 30 days. Open the returns page, keep your order number, and follow the steps listed there.</p>
            </details>
            <details class="lv2-faq">
                <summary>Are the prices the live catalog prices?</summary>
                <p>Yes. Product cards and the product page use the PKR prices stored on each product. A crossed-out price appears only when the catalog has a higher previous price.</p>
            </details>
        </div>
    </section>

    @if (($sections['journal'] ?? true) && $journalPosts->isNotEmpty())
    <section class="lv2-wrap lv2-section" aria-labelledby="home-journal-title">
        <div class="lv2-head">
            <h2 id="home-journal-title">Guides from the store</h2>
            <a class="lv2-link" href="{{ route('blog.index') }}">Read the journal</a>
        </div>
        <div class="lv2-grid lv2-grid-3">
            @foreach ($journalPosts as $post)
                <a class="lv2-product" href="{{ route('blog.show', $post) }}">
                    <img src="{{ $post->image }}" alt="{{ $post->title }}" width="640" height="360" loading="lazy" decoding="async">
                    <h3>{{ $post->title }}</h3>
                    <p class="lv2-muted">{{ $post->snippet }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
<script>
    window.products = @json($productsCatalog);
    (function () {
        const ends = new Date(@json($flash['ends_at'] ?? now()->toIso8601String())).getTime();
        const hours = document.getElementById('hours');
        const minutes = document.getElementById('minutes');
        const seconds = document.getElementById('seconds');
        if (!hours || !minutes || !seconds) return;
        const tick = function () {
            const remaining = Math.max(0, Math.floor((ends - Date.now()) / 1000));
            hours.textContent = String(Math.floor(remaining / 3600)).padStart(2, '0');
            minutes.textContent = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
            seconds.textContent = String(remaining % 60).padStart(2, '0');
        };
        tick();
        window.setInterval(tick, 1000);
    })();
</script>
@endpush
