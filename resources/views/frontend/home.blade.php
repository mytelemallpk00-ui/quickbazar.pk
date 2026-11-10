@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: 'Quick Bazar — Elevate Your Natural Elegance')

@section('body_attrs')
class="lumiere-v2 qh-home bg-[#f4f7f6] text-gray-800 font-sans antialiased min-h-screen flex flex-col overflow-x-clip"
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

@push('styles')
<link rel="stylesheet" href="{{ asset('css/qb-home.css') }}?v=2026111020">
@endpush

@section('content')
@php
    $promo = $promoProduct;
    $placeholder = 'https://placehold.co/640x640/0e6b5c/ffffff?text=Quick+Bazar';
    $heroImage = $promo['image'] ?? ($hero['image'] ?? $placeholder);
    $heroAlt = $promo['name'] ?? ($hero['image_alt'] ?? 'Featured product');
    $fromPrice = $promo['price_label'] ?? null;
    $reviewCount = (int) ($reviewSummary['count'] ?? 0);
    $reviewAverage = (float) ($reviewSummary['average'] ?? 0);
@endphp
<main class="qh">
    @if ($sections['hero'] ?? true)
    <section class="qh-wrap" aria-labelledby="home-hero-title">
        <div class="qh-hero">
            <div class="qh-hero-copy">
                <span class="qh-pill">{{ $hero['badge'] ?? 'Ready to ship' }}</span>
                <h1 id="home-hero-title">{{ $hero['title'] ?? 'Everyday care, priced in PKR' }} <span>{{ $hero['accent'] ?? '' }}</span></h1>
                <p>{{ $hero['description'] ?? '' }}</p>
                <ul class="qh-checks">
                    <li>Live catalog prices in PKR</li>
                    <li>Cash on delivery at checkout</li>
                    <li>Nationwide shipping</li>
                </ul>
                <div class="qh-hero-actions">
                    <a class="qh-btn" href="{{ route('collections') }}">Explore collection <span aria-hidden="true">→</span></a>
                    @if ($fromPrice)
                        <p class="qh-from">From {{ $fromPrice }} with cash on delivery</p>
                    @endif
                </div>
            </div>
            <div class="qh-hero-visual">
                @if ($promo)
                    <a href="{{ $promo['url'] }}" class="qh-hero-product">
                        <img src="{{ $heroImage ?: $placeholder }}" alt="{{ $heroAlt }}" width="520" height="520" fetchpriority="high" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        <span>{{ $promo['name'] }}</span>
                    </a>
                @else
                    <div class="qh-hero-product">
                        <img src="{{ $placeholder }}" alt="Quick Bazar" width="520" height="520">
                    </div>
                @endif
            </div>
        </div>
    </section>
    @else
    <h1 class="sr-only">{{ $pageSeo['title'] ?? 'Home' }}</h1>
    @endif

    @if ($sections['trust'] ?? true)
    <section class="qh-wrap" aria-labelledby="home-trust-title">
        <h2 id="home-trust-title" class="sr-only">Why shop here</h2>
        <div class="qh-trust">
            <article>
                <span aria-hidden="true"><i data-lucide="badge-check"></i></span>
                <div><h3>Catalog you can browse</h3><p>Prices and stock come from the live store.</p></div>
            </article>
            <article>
                <span aria-hidden="true"><i data-lucide="shield-check"></i></span>
                <div><h3>Checkout you control</h3><p>Cash on delivery and the methods shown at checkout.</p></div>
            </article>
            <article>
                <span aria-hidden="true"><i data-lucide="truck"></i></span>
                <div><h3>Nationwide delivery</h3><p>Free shipping from {{ $freeShippingLabel ?? 'the checkout threshold' }}.</p></div>
            </article>
            <article>
                <span aria-hidden="true"><i data-lucide="message-circle"></i></span>
                <div><h3>WhatsApp support</h3><p>Ask about an order before you buy.</p></div>
            </article>
        </div>
    </section>
    @endif

    @if ($sections['categories'] ?? true)
    <section id="categories" class="qh-wrap qh-section" aria-labelledby="home-categories-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">Browse by category</p>
                <h2 id="home-categories-title">Shop by category</h2>
            </div>
            <a class="qh-text-link" href="{{ route('collections') }}">View all →</a>
        </div>
        @if ($featuredCategories->isEmpty())
            <p class="qh-empty">Categories will show here once they are published.</p>
        @else
            <div class="qh-pills">
                @foreach ($featuredCategories as $category)
                    <a class="qh-pill-card" href="{{ $category->collectionsUrl() }}">
                        <span aria-hidden="true"><i data-lucide="{{ $category->iconName() }}"></i></span>
                        <span>
                            <strong>{{ $category->displayNavLabel() }}</strong>
                            <small>{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'product' : 'products' }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if (! empty($goalCards))
    <section class="qh-wrap qh-section" aria-labelledby="home-goals-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">Shop by goal and concern</p>
                <h2 id="home-goals-title">Find products by goal</h2>
            </div>
        </div>
        <div class="qh-goals">
            @foreach ($goalCards as $goal)
                <a class="qh-goal" href="{{ $goal['url'] }}">
                    <span aria-hidden="true"><i data-lucide="{{ $goal['icon'] }}"></i></span>
                    <span>
                        <strong>{{ $goal['title'] }}</strong>
                        <small>{{ $goal['text'] }}</small>
                        <em>Explore products →</em>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    @if ($sections['trending'] ?? true)
    <section id="trending" class="qh-wrap qh-section" aria-labelledby="home-trending-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">{{ $trendingCopy['badge'] ?? 'Bestselling' }}</p>
                <h2 id="home-trending-title">{{ $trendingCopy['heading'] ?? 'Featured products' }}</h2>
                <p class="qh-muted">{{ $trendingCopy['description'] ?? '' }}</p>
            </div>
            <a class="qh-text-link" href="{{ route('collections') }}">Shop all →</a>
        </div>
        @if (collect($trendingProducts)->isEmpty())
            <p class="qh-empty">No products are published yet.</p>
        @else
            <div class="qh-grid">
                @foreach (collect($trendingProducts)->take(8) as $product)
                    @include('partials.home.qh-product-card', ['product' => $product, 'placeholder' => $placeholder])
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if (($sections['flash'] ?? true) && $flashProduct)
    <section class="qh-wrap qh-section" aria-labelledby="home-offer-title">
        <div class="qh-offer">
            <div>
                <p class="qh-kicker qh-kicker--light">{{ $flash['badge'] ?? 'Today' }}</p>
                <h2 id="home-offer-title">{{ $flashProduct['name'] }}</h2>
                <p>{{ $flash['description'] ?? 'A current catalog price, ready to add to the bag.' }}</p>
                <p class="qh-offer-price">
                    <strong>{{ $flashProduct['price_label'] }}</strong>
                    @if (! empty($flashProduct['old_price_label']))
                        <s>{{ $flashProduct['old_price_label'] }}</s>
                    @endif
                </p>
                <div class="qh-timer" aria-live="polite">
                    <div><strong id="hours">{{ $flash['hours'] }}</strong><span>Hours</span></div>
                    <div><strong id="minutes">{{ $flash['minutes'] }}</strong><span>Minutes</span></div>
                    <div><strong id="seconds">{{ $flash['seconds'] }}</strong><span>Seconds</span></div>
                </div>
                <a class="qh-btn qh-btn--light" href="{{ $flashProduct['url'] }}">View this offer</a>
            </div>
            <a href="{{ $flashProduct['url'] }}" class="qh-offer-photo">
                <img src="{{ $flashProduct['image'] ?: $placeholder }}" alt="{{ $flashProduct['name'] }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
            </a>
        </div>
    </section>
    @endif

    @if ($departmentCards->isNotEmpty())
    <section class="qh-wrap qh-section" aria-labelledby="home-brands-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">Departments</p>
                <h2 id="home-brands-title">Shop the full catalog</h2>
            </div>
            <a class="qh-text-link" href="{{ route('collections') }}">View all →</a>
        </div>
        <div class="qh-brands">
            @foreach ($departmentCards as $category)
                <a class="qh-brand" href="{{ $category->collectionsUrl() }}">
                    <strong>{{ $category->displayNavLabel() }}</strong>
                    <span>{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'product' : 'products' }}</span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    @if ($sections['steps'] ?? true)
    <section class="qh-wrap qh-section" aria-labelledby="home-steps-title">
        <div class="qh-head"><div><p class="qh-kicker">Ordering</p><h2 id="home-steps-title">How it works</h2></div></div>
        <ol class="qh-steps">
            <li><span>1</span><h3>Choose a product</h3><p>Compare PKR prices and open any product page.</p></li>
            <li><span>2</span><h3>Checkout your way</h3><p>Pay cash on delivery, or use the methods shown at checkout.</p></li>
            <li><span>3</span><h3>Track the parcel</h3><p>Follow the order after it is placed.</p></li>
        </ol>
    </section>
    @endif

    @if (($sections['reviews'] ?? true) && $latestReviews->isNotEmpty())
    <section id="reviews" class="qh-wrap qh-section" aria-labelledby="home-reviews-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">{{ number_format($reviewAverage, 1) }} average</p>
                <h2 id="home-reviews-title">Loved by customers</h2>
                <p class="qh-muted">{{ number_format($reviewCount) }} published {{ $reviewCount === 1 ? 'review' : 'reviews' }}.</p>
            </div>
        </div>
        <div class="qh-reviews">
            @foreach ($latestReviews as $review)
                <article>
                    <p class="qh-stars" aria-hidden="true">{{ str_repeat('★', (int) round((float) $review->rating)) }}</p>
                    <h3>{{ $review->author_name }}</h3>
                    @if ($review->title)
                        <p class="qh-review-title">{{ $review->title }}</p>
                    @endif
                    <p>{{ $review->body }}</p>
                    @if ($review->product)
                        <a href="{{ route('product.show', $review->product) }}">{{ $review->product->name }}</a>
                    @endif
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <section class="qh-wrap qh-section" aria-labelledby="home-faq-title">
        <div class="qh-head"><div><p class="qh-kicker">Help</p><h2 id="home-faq-title">Questions before you order</h2></div></div>
        <div class="qh-faq">
            <details open>
                <summary>Do you deliver across Pakistan?</summary>
                <p>Yes. Shipping runs nationwide. The city you enter at checkout sets the delivery options.</p>
            </details>
            <details>
                <summary>Can I pay cash on delivery?</summary>
                <p>Cash on delivery is available for eligible orders. Checkout lists the payment choices for your cart.</p>
            </details>
            <details>
                <summary>When is delivery free?</summary>
                <p>Free shipping starts at {{ $freeShippingLabel ?? 'the amount shown at checkout' }} on eligible methods.</p>
            </details>
            <details>
                <summary>How do returns work?</summary>
                <p>Most items can be returned within 30 days. Keep the order number and follow the returns page.</p>
            </details>
        </div>
        <p class="qh-faq-links">
            <a href="{{ route('shipping') }}">Shipping</a>
            <a href="{{ route('returns') }}">Returns</a>
            <a href="{{ route('track-order') }}">Track an order</a>
            <a href="{{ route('help') }}">Help</a>
        </p>
    </section>

    @if (($sections['journal'] ?? true) && $journalPosts->isNotEmpty())
    <section class="qh-wrap qh-section" aria-labelledby="home-journal-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">Journal</p>
                <h2 id="home-journal-title">Guides from the store</h2>
            </div>
            <a class="qh-text-link" href="{{ route('blog.index') }}">Read the journal →</a>
        </div>
        <div class="qh-journal">
            @foreach ($journalPosts as $post)
                <a href="{{ route('blog.show', $post) }}">
                    <img src="{{ $post->image }}" alt="" width="640" height="360" loading="lazy" decoding="async">
                    <h3>{{ $post->title }}</h3>
                    <p>{{ $post->snippet }}</p>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    <section class="qh-wrap qh-section" aria-labelledby="home-news-title">
        <div class="qh-news">
            <div>
                <h2 id="home-news-title">Offers and new arrivals</h2>
                <p>Leave an email for store updates. No account required.</p>
            </div>
            <form id="qh-newsletter" class="qh-news-form">
                <label class="sr-only" for="qh-email">Email</label>
                <input id="qh-email" name="email" type="email" required autocomplete="email" placeholder="Email address">
                <button type="submit">Subscribe</button>
                <p id="qh-news-note" role="status"></p>
            </form>
            @if (! empty($whatsappUrl))
                <a class="qh-wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Order on WhatsApp</a>
            @endif
        </div>
    </section>
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
        if (hours && minutes && seconds) {
            const tick = function () {
                const remaining = Math.max(0, Math.floor((ends - Date.now()) / 1000));
                hours.textContent = String(Math.floor(remaining / 3600)).padStart(2, '0');
                minutes.textContent = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                seconds.textContent = String(remaining % 60).padStart(2, '0');
            };
            tick();
            window.setInterval(tick, 1000);
        }

        const form = document.getElementById('qh-newsletter');
        const note = document.getElementById('qh-news-note');
        if (!form) return;
        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            const email = new FormData(form).get('email');
            note.textContent = 'Saving…';
            try {
                const response = await fetch(@json(route('newsletter.subscribe')), {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ email: email, list: 'store' })
                });
                const data = await response.json();
                note.textContent = data.message || (response.ok ? 'Subscribed.' : 'Try again.');
                if (response.ok) form.reset();
            } catch (error) {
                note.textContent = 'Could not subscribe just now.';
            }
        });
    })();
</script>
@endpush
