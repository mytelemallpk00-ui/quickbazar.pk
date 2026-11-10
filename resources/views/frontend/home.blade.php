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
<link rel="stylesheet" href="{{ asset('css/qb-home.css') }}?v=2026111037">
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
    @if (($sections['hero'] ?? true) && ! empty($slides))
    <section class="qh-wrap" aria-roledescription="carousel" aria-label="Featured offers">
        <div class="qh-slider" id="qh-slider">
            @foreach ($slides as $index => $slide)
                @php
                    $checks = preg_split('/\r\n|\r|\n/', (string) ($slide['checks'] ?? '')) ?: [];
                    $checks = array_values(array_filter(array_map('trim', $checks)));
                @endphp
                <article class="qh-hero qh-slide {{ $index === 0 ? 'is-on' : '' }}" data-slide>
                    <div class="qh-hero-copy">
                        @if (filled($slide['badge'] ?? null))
                            <span class="qh-pill">{{ $slide['badge'] }}</span>
                        @endif
                        <h1 @if ($index === 0) id="home-hero-title" @endif>{{ $slide['title'] ?? '' }} <span>{{ $slide['accent'] ?? '' }}</span></h1>
                        @if (filled($slide['description'] ?? null))
                            <p>{{ $slide['description'] }}</p>
                        @endif
                        @if ($checks !== [])
                            <ul class="qh-checks">
                                @foreach ($checks as $check)
                                    <li>{{ $check }}</li>
                                @endforeach
                            </ul>
                        @endif
                        <div class="qh-hero-actions">
                            <a class="qh-btn" href="{{ url($slide['button_url'] ?? '/collections') }}">{{ $slide['button_label'] ?? 'Explore Collection' }} <span aria-hidden="true">→</span></a>
                            @if (filled($slide['price_line'] ?? null))
                                <p class="qh-from">{{ $slide['price_line'] }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="qh-hero-visual">
                        <div class="qh-hero-product">
                            <img src="{{ $slide['image_url'] ?: $placeholder }}" alt="{{ $slide['image_alt'] ?? ($slide['title'] ?? 'Quick Bazar') }}" width="520" height="520" @if ($index === 0) fetchpriority="high" @endif decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        </div>
                    </div>
                </article>
            @endforeach
            @if (count($slides) > 1)
                <button type="button" class="qh-arrow qh-arrow--prev" data-slider-prev aria-label="Previous slide">‹</button>
                <button type="button" class="qh-arrow qh-arrow--next" data-slider-next aria-label="Next slide">›</button>
                <div class="qh-dots">
                    @foreach ($slides as $index => $slide)
                        <button type="button" class="{{ $index === 0 ? 'is-on' : '' }}" data-slider-dot="{{ $index }}" aria-label="Slide {{ $index + 1 }}"></button>
                    @endforeach
                </div>
            @endif
        </div>
    </section>
    @else
    <h1 class="sr-only">{{ $pageSeo['title'] ?? 'Home' }}</h1>
    @endif

    @if ($sections['trust'] ?? true)
    <section class="qh-wrap" aria-labelledby="home-trust-title">
        <h2 id="home-trust-title" class="sr-only">Why shop here</h2>
        <div class="qh-trust">
            @foreach ($trustRow as $item)
                <article>
                    <span aria-hidden="true"><i data-lucide="{{ $item['icon'] ?? 'badge-check' }}"></i></span>
                    <div><h3>{{ $item['title'] ?? '' }}</h3><p>{{ $item['text'] ?? '' }}</p></div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    @php
        $weeklyDeals = is_array($weeklyDeals ?? null) ? $weeklyDeals : [];
        $dealBanner = is_array($weeklyDeals['banner'] ?? null) ? $weeklyDeals['banner'] : [];
        $dealProducts = array_values(array_filter(is_array($weeklyDeals['products'] ?? null) ? $weeklyDeals['products'] : [], 'is_array'));
    @endphp
    @if ($dealProducts !== [])
    <section class="qh-wrap qh-section" aria-labelledby="home-deals-title">
        <div class="qh-deals-head">
            <h2 id="home-deals-title">{{ $weeklyDeals['title'] ?? 'Deals of the Week' }}</h2>
            <a class="qh-text-link" href="{{ url($weeklyDeals['view_url'] ?? '/exclusive-offers') }}">{{ $weeklyDeals['view_label'] ?? 'View all' }}</a>
        </div>
        <div class="qh-deals">
            <a class="qh-deals-banner" href="{{ url($dealBanner['url'] ?? '/collections') }}">
                <img src="{{ $dealBanner['image_url'] ?? $placeholder }}" alt="{{ $dealBanner['title'] ?? 'Weekly spotlight' }}" width="720" height="960" loading="lazy" decoding="async">
                <span>
                    <small>{{ $dealBanner['kicker'] ?? 'Featured' }}</small>
                    <strong>{{ $dealBanner['title'] ?? 'Weekly spotlight picks' }}</strong>
                </span>
            </a>
            <div class="qh-deals-panel">
                <div class="qh-deals-timer">
                    <span>Offer ends in</span>
                    <strong id="qh-deals-clock" data-ends="{{ now()->endOfDay()->toIso8601String() }}">00:00:00</strong>
                </div>
                <div class="qh-deals-grid">
                    @foreach ($dealProducts as $deal)
                        @php $dealStars = max(0, min(5, (int) round((float) ($deal['rating'] ?? 4)))); @endphp
                        <article class="qh-deal">
                            <a href="{{ url($deal['url'] ?? '/collections') }}" class="qh-deal-media">
                                @if (! empty($deal['save']))
                                    <span class="qh-deal-save">{{ $deal['save'] }}</span>
                                @endif
                                <img src="{{ $deal['image_url'] ?: $placeholder }}" alt="{{ $deal['name'] ?? 'Deal' }}" width="480" height="480" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                            </a>
                            <h3><a href="{{ url($deal['url'] ?? '/collections') }}">{{ $deal['name'] ?? '' }}</a></h3>
                            <p class="qh-price"><strong>{{ $deal['price'] ?? '' }}</strong> @if (! empty($deal['old_price']))<s>{{ $deal['old_price'] }}</s>@endif</p>
                            <p class="qh-deal-stars" aria-label="Rated {{ $dealStars }} out of 5">{{ str_repeat('★', $dealStars) }}{{ str_repeat('☆', 5 - $dealStars) }} <span>({{ $deal['reviews'] ?? '0' }})</span></p>
                            <a class="qh-bag" href="{{ url($deal['url'] ?? '/collections') }}">Add to cart</a>
                        </article>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    @if ($sections['categories'] ?? true)
    <section id="categories" class="qh-wrap qh-section" aria-labelledby="home-categories-title">
        <div class="qh-band">
            <div class="qh-head">
                <div>
                    <p class="qh-kicker">Browse by category</p>
                    <h2 id="home-categories-title">Shop by Categories</h2>
                </div>
                <a class="qh-text-link" href="{{ route('collections') }}">View all →</a>
            </div>
            <div class="qh-pills">
                @foreach ($shopPills as $pill)
                    <a class="qh-pill-card" href="{{ url($pill['url'] ?? '/collections') }}">
                        <span class="qh-ico" aria-hidden="true"><i data-lucide="{{ $pill['icon'] ?? 'tag' }}"></i></span>
                        <span class="qh-pill-copy">
                            <strong>{{ $pill['label'] ?? '' }}</strong>
                            <small>{{ $pill['count'] ?? '' }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if (! empty($showcaseGoals))
    <section class="qh-wrap qh-section" aria-labelledby="home-goals-title">
        <div class="qh-band qh-band--goals">
            <div class="qh-head">
                <div>
                    <p class="qh-kicker">Shop by goal and concern</p>
                    <h2 id="home-goals-title">Find Products by Goal</h2>
                </div>
            </div>
            <div class="qh-goals">
                @foreach ($showcaseGoals as $goal)
                    <a class="qh-goal" href="{{ url($goal['url'] ?? '/collections') }}">
                        <span class="qh-ico" aria-hidden="true"><i data-lucide="{{ $goal['icon'] ?? 'sparkles' }}"></i></span>
                        <span class="qh-goal-copy">
                            <strong>{{ $goal['title'] ?? '' }}</strong>
                            <small>{{ $goal['text'] ?? '' }}</small>
                            <em>Explore products →</em>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if ($sections['trending'] ?? true)
    <section id="trending" class="qh-wrap qh-section" aria-labelledby="home-trending-title">
        <div class="qh-head">
            <div>
                <p class="qh-kicker">Bestselling derma essentials</p>
                <h2 id="home-trending-title">Featured Derma &amp; Skincare Products</h2>
                <p class="qh-muted">Open a card to see the product, the PKR price, and delivery details.</p>
            </div>
            <a class="qh-text-link" href="{{ route('collections') }}">Shop all →</a>
        </div>
        <div class="qh-grid qh-grid--shop">
            @foreach (array_slice($featuredCards, 0, 12) as $card)
                @php
                    $cardName = (string) ($card['name'] ?? 'Product');
                    $cardUrl = url($card['url'] ?? '/collections');
                    $cardImage = $card['image_url'] ?: $placeholder;
                    $cardShort = trim((string) ($card['short'] ?? ''));
                    $cardCategory = trim((string) ($card['category'] ?? ''));
                    $cardPrice = $card['price_value'] ?? null;
                    $cardOld = $card['old_price_value'] ?? null;
                    $cardSave = (int) ($card['save_percent'] ?? 0);
                    if ($cardSave < 1 && is_numeric($cardPrice) && is_numeric($cardOld) && (float) $cardOld > (float) $cardPrice) {
                        $cardSave = (int) round((1 - ((float) $cardPrice / (float) $cardOld)) * 100);
                    }
                    $cardStars = max(0, min(5, (int) round((float) ($card['rating'] ?? 0))));
                    $cardReviews = (int) ($card['reviews'] ?? 0);
                    $cardStock = ($card['in_stock'] ?? true) ? '1' : '0';
                @endphp
                <article class="lv2-product">
                    <a href="{{ $cardUrl }}" class="lv2-product-media">
                        <img class="lv2-card-photo" src="{{ $cardImage }}" alt="{{ $cardName }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                        @if ($cardSave > 0)
                            <span class="lv2-save">SAVE {{ $cardSave }}%</span>
                        @endif
                    </a>
                    <div class="lv2-product-copy">
                        @if ($cardCategory !== '')
                            <p class="lv2-muted">{{ $cardCategory }}</p>
                        @endif
                        <h3><a href="{{ $cardUrl }}">{{ $cardName }}</a></h3>
                        @if ($cardShort !== '')
                            <p class="lv2-card-desc">{{ $cardShort }}</p>
                        @endif
                        @if ($cardReviews > 0)
                            <p class="lv2-stars" aria-label="Rated {{ number_format((float) ($card['rating'] ?? 0), 1) }} out of 5">{{ str_repeat('★', $cardStars) }}{{ str_repeat('☆', 5 - $cardStars) }} <span class="lv2-muted">({{ number_format($cardReviews) }})</span></p>
                        @endif
                        <p class="lv2-price-row">
                            <span class="lv2-price">{{ $card['price'] ?? '' }}</span>
                            @if (! empty($card['old_price']))
                                <span class="lv2-old">{{ $card['old_price'] }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="lv2-card-actions">
                        <button type="button" class="lv2-card-quick" data-quick-view data-id="{{ (int) ($card['id'] ?? 0) }}" data-name="{{ $cardName }}" data-price="{{ $cardPrice }}" data-old="{{ $cardOld }}" data-image="{{ $cardImage }}" data-category="{{ $cardCategory }}" data-desc="{{ $cardShort }}" data-url="{{ $cardUrl }}" data-stock="{{ $cardStock }}">Quick view</button>
                        @if ($cardStock === '1')
                            <button type="button" class="lv2-btn-navy" data-card-add data-id="{{ (int) ($card['id'] ?? 0) }}" data-name="{{ $cardName }}" data-price="{{ $cardPrice }}" data-old="{{ $cardOld }}" data-image="{{ $cardImage }}" data-category="{{ $cardCategory }}" data-desc="{{ $cardShort }}" data-url="{{ $cardUrl }}" data-stock="1">Add to cart</button>
                        @else
                            <button type="button" class="lv2-btn-navy" disabled>Out of stock</button>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
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

    @if (! empty($brandCards))
    <section class="qh-wrap qh-section" aria-labelledby="home-brands-title">
        <div class="qh-band">
            <div class="qh-head">
                <div>
                    <p class="qh-kicker">Trusted derma labs and brands</p>
                    <h2 id="home-brands-title">Top Brands</h2>
                </div>
                <a class="qh-text-link" href="{{ route('collections') }}">View all brands →</a>
            </div>
            <div class="qh-brands">
                @foreach ($brandCards as $brand)
                    <a class="qh-brand" href="{{ url($brand['url'] ?? '/collections') }}">
                        <span class="qh-brand-mark" aria-hidden="true">{{ mb_strtoupper(mb_substr((string) ($brand['name'] ?? 'Q'), 0, 1)) }}</span>
                        <span>
                            <strong>{{ $brand['name'] ?? '' }}</strong>
                            <small>{{ $brand['count'] ?? '' }}</small>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if ($sections['steps'] ?? true)
    <section class="qh-wrap qh-section" aria-labelledby="home-steps-title">
        <div class="qh-band qh-band--goals">
            <div class="qh-head"><div><p class="qh-kicker">Ordering</p><h2 id="home-steps-title">How it works</h2></div></div>
            <ol class="qh-steps">
                <li><span>1</span><div><h3>Choose a product</h3><p>Compare PKR prices and open any product page.</p></div></li>
                <li><span>2</span><div><h3>Checkout your way</h3><p>Pay cash on delivery, or use the methods shown at checkout.</p></div></li>
                <li><span>3</span><div><h3>Track the parcel</h3><p>Follow the order after it is placed.</p></div></li>
            </ol>
        </div>
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
                    <header>
                        <span class="qh-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr((string) $review->author_name, 0, 1)) }}</span>
                        <span>
                            <h3>{{ $review->author_name }}</h3>
                            <p class="qh-stars" aria-label="Rated {{ (int) round((float) $review->rating) }} out of 5">{{ str_repeat('★', (int) round((float) $review->rating)) }}{{ str_repeat('☆', max(0, 5 - (int) round((float) $review->rating))) }}</p>
                        </span>
                    </header>
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
        <div class="qh-band">
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
        </div>
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
                <p class="qh-kicker qh-kicker--light">Store updates</p>
                <h2 id="home-news-title">Offers and new arrivals</h2>
                <p>New products, delivery notes, and weekly offers. No account required.</p>
                <ul>
                    <li>New arrivals</li>
                    <li>Delivery updates</li>
                    <li>Weekly offers</li>
                </ul>
            </div>
            <div class="qh-news-card">
                <form id="qh-newsletter" class="qh-news-form">
                    <label for="qh-email">Email address</label>
                    <input id="qh-email" name="email" type="email" required autocomplete="email" placeholder="you@email.com">
                    <button type="submit">Subscribe</button>
                    <p id="qh-news-note" role="status"></p>
                </form>
                @if (! empty($whatsappUrl))
                    <a class="qh-wa" href="{{ $whatsappUrl }}" target="_blank" rel="noopener">Order on WhatsApp</a>
                @endif
            </div>
        </div>
    </section>
</main>
@endsection

@push('scripts')
<script>
    window.products = @json($productsCatalog);
    (function () {
        const root = document.getElementById('qh-slider');
        if (root) {
            const slides = Array.from(root.querySelectorAll('[data-slide]'));
            const dots = Array.from(root.querySelectorAll('[data-slider-dot]'));
            let index = 0;
            const show = function (next) {
                index = (next + slides.length) % slides.length;
                slides.forEach(function (slide, i) { slide.classList.toggle('is-on', i === index); });
                dots.forEach(function (dot, i) { dot.classList.toggle('is-on', i === index); });
            };
            root.querySelector('[data-slider-prev]')?.addEventListener('click', function () { show(index - 1); });
            root.querySelector('[data-slider-next]')?.addEventListener('click', function () { show(index + 1); });
            dots.forEach(function (dot) {
                dot.addEventListener('click', function () { show(Number(dot.getAttribute('data-slider-dot'))); });
            });
            if (slides.length > 1) window.setInterval(function () { show(index + 1); }, 7000);
        }
        const clock = document.getElementById('qh-deals-clock');
        if (clock) {
            const ends = new Date(clock.getAttribute('data-ends')).getTime();
            const tickDeals = function () {
                const remaining = Math.max(0, Math.floor((ends - Date.now()) / 1000));
                const hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
                const minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                const seconds = String(remaining % 60).padStart(2, '0');
                clock.textContent = hours + ':' + minutes + ':' + seconds;
            };
            tickDeals();
            window.setInterval(tickDeals, 1000);
        }
    })();
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
