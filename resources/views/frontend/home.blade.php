@extends('layouts.app')

@section('title', 'QuickBazar — Shop verified products with cash on delivery')

@section('meta_description', 'Shop original products on QuickBazar with nationwide shipping, cash on delivery, verified quality, and WhatsApp support across Pakistan.')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col overflow-x-clip"
@endsection

@section('content')
@php
    $promo = $promoProduct;
    $placeholder = 'https://placehold.co/640x640/fff7ed/0b1220?text=QuickBazar';
    $heroBadge = $hero['badge'];
    $heroTitle = $hero['title'];
    $heroAccent = $hero['accent'];
    $heroText = $hero['description'];
    if (str_contains(strtolower($heroBadge.' '.$heroTitle.' '.$heroAccent), 'beautage') || str_contains(strtolower($heroTitle), 'lumina')) {
        $heroBadge = 'Mega seasonal sale';
        $heroTitle = 'Everything you need,';
        $heroAccent = 'delivered fast in Pakistan';
        $heroText = 'Verified products, clear PKR prices, and cash on delivery to your door.';
    }
    $flashBadge = $flash['badge'];
    if (str_contains(strtolower($flashBadge), 'luxury') || str_contains(strtolower($flashBadge), 'flagship') || str_contains(strtolower($flashBadge), 'beautage')) {
        $flashBadge = 'Flash deals';
    }
@endphp
<main class="qb-page">
    @if ($sections['hero'] ?? true)
    <section class="px-4 py-5 sm:px-6 sm:py-8 lg:px-8" aria-labelledby="home-hero-title">
        <div class="qb-hero w-full">
            <div>
                <p class="qb-pill">
                    <i data-lucide="zap" class="h-3.5 w-3.5" aria-hidden="true"></i>
                    {{ $heroBadge }}
                </p>
                <h1 id="home-hero-title" class="mt-4 max-w-2xl text-3xl font-extrabold leading-tight tracking-tight sm:text-5xl">
                    {{ $heroTitle }}
                    <span style="color: var(--qb-highlight)">{{ $heroAccent }}</span>
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-200 sm:text-base">{{ $heroText }}</p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('collections') }}" class="qb-btn">
                        Start shopping
                        <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                    <a href="{{ route('exclusive-offers') }}" class="qb-btn-line">View daily deals</a>
                </div>
                <ul class="mt-6 flex flex-wrap gap-2 text-xs font-semibold text-slate-200">
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5">Nationwide shipping</li>
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5">Cash on delivery</li>
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white/10 px-3 py-1.5">Verified quality</li>
                </ul>
            </div>
            <div class="qb-hero-card">
                @if($promo)
                    <img src="{{ $promo['image'] ?? $hero['image'] }}" alt="{{ $promo['name'] ?? $hero['image_alt'] }}" width="640" height="480" fetchpriority="high" decoding="async" class="aspect-[4/3] w-full rounded-xl object-cover sm:aspect-video lg:aspect-[4/3]" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                    <p class="mt-4 text-xs font-bold uppercase tracking-wide" style="color: var(--qb-highlight)">{{ $promo['category'] ?? 'Featured offer' }}</p>
                    <h2 class="mt-1 text-lg font-extrabold sm:text-xl">{{ $promo['name'] }}</h2>
                    <p class="mt-2 flex items-baseline gap-2">
                        <span class="text-2xl font-extrabold">{{ $promo['price_label'] }}</span>
                        @if($promo['old_price_label'])
                            <span class="text-sm text-slate-300 line-through">{{ $promo['old_price_label'] }}</span>
                        @endif
                    </p>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                        @if($promo['in_stock'])
                            <button type="button" onclick="addToCart({{ (int) $promo['id'] }})" class="qb-btn flex-1">Add to cart</button>
                        @endif
                        <a href="{{ $promo['url'] }}" class="qb-btn-line flex-1">View product</a>
                    </div>
                @else
                    <div class="flex min-h-48 flex-col items-start justify-center gap-3">
                        <i data-lucide="zap" class="h-8 w-8" style="color: var(--qb-highlight)" aria-hidden="true"></i>
                        <h2 class="text-2xl font-extrabold">Express delivery</h2>
                        <p class="text-sm text-slate-300">Available across Lahore, Karachi, Islamabad and 50+ cities.</p>
                        <a href="{{ route('collections') }}" class="qb-btn">Shop the catalog</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
    @endif

    @if ($sections['steps'] ?? true)
    <section class="px-4 pb-4 sm:px-6 lg:px-8" aria-labelledby="home-steps-title">
        <div class="w-full">
            <h2 id="home-steps-title" class="sr-only">How to order</h2>
            <ol class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                <li class="qb-card flex items-start gap-3 p-4">
                    <span class="qb-step-no">1</span>
                    <span>
                        <span class="block text-sm font-extrabold text-slate-900">Browse departments</span>
                        <span class="mt-1 block text-xs leading-relaxed text-slate-500">Filter the catalog and compare live PKR prices.</span>
                    </span>
                </li>
                <li class="qb-card flex items-start gap-3 p-4">
                    <span class="qb-step-no">2</span>
                    <span>
                        <span class="block text-sm font-extrabold text-slate-900">Pay on delivery</span>
                        <span class="mt-1 block text-xs leading-relaxed text-slate-500">Cash, wallet, card, or bank transfer at checkout.</span>
                    </span>
                </li>
                <li class="qb-card flex items-start gap-3 p-4">
                    <span class="qb-step-no">3</span>
                    <span>
                        <span class="block text-sm font-extrabold text-slate-900">Track the parcel</span>
                        <span class="mt-1 block text-xs leading-relaxed text-slate-500">Door-to-door shipping with order tracking.</span>
                    </span>
                </li>
            </ol>
        </div>
    </section>
    @endif

    @if ($sections['trust'] ?? true)
    <section class="border-y border-slate-200 bg-white" aria-label="Why shop with Quick Bazar">
        <ul class="grid w-full grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <li><a href="{{ route('shipping') }}" class="flex h-full items-start gap-3 px-4 py-5 transition hover:bg-[var(--qb-soft)] sm:px-6"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: var(--qb-soft); color: var(--qb-orange)"><i data-lucide="truck" class="h-5 w-5"></i></span><span><span class="block text-sm font-bold text-slate-900">Nationwide shipping</span><span class="mt-0.5 block text-xs text-slate-500">Delivery across Pakistan, tracked from checkout.</span></span></a></li>
            <li><a href="{{ route('checkout') }}" class="flex h-full items-start gap-3 px-4 py-5 transition hover:bg-[var(--qb-soft)] sm:px-6"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: var(--qb-soft); color: var(--qb-orange)"><i data-lucide="banknote" class="h-5 w-5"></i></span><span><span class="block text-sm font-bold text-slate-900">Cash on delivery</span><span class="mt-0.5 block text-xs text-slate-500">Pay in PKR when the parcel reaches you.</span></span></a></li>
            <li><a href="{{ route('our-story') }}" class="flex h-full items-start gap-3 px-4 py-5 transition hover:bg-[var(--qb-soft)] sm:px-6"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: var(--qb-soft); color: var(--qb-orange)"><i data-lucide="shield-check" class="h-5 w-5"></i></span><span><span class="block text-sm font-bold text-slate-900">Verified quality</span><span class="mt-0.5 block text-xs text-slate-500">Catalog items are listed with real store prices.</span></span></a></li>
            <li><a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex h-full items-start gap-3 px-4 py-5 transition hover:bg-[var(--qb-soft)] sm:px-6"><span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl" style="background: var(--qb-soft); color: var(--qb-orange)"><i data-lucide="message-circle" class="h-5 w-5"></i></span><span><span class="block text-sm font-bold text-slate-900">24/7 support</span><span class="mt-0.5 block text-xs text-slate-500">WhatsApp and live chat when you need a hand.</span></span></a></li>
        </ul>
    </section>
    @endif

    @if ($sections['categories'] ?? true)
    <section id="categories" class="px-4 py-10 sm:px-6 lg:px-8" aria-labelledby="home-categories-title">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="qb-eyebrow">Departments</p>
                <h2 id="home-categories-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Featured categories</h2>
            </div>
            <a href="{{ route('collections') }}" class="text-sm font-bold" style="color: var(--qb-orange)">View all products</a>
        </div>
        @if($featuredCategories->isEmpty())
            <p class="qb-card px-5 py-8 text-sm text-slate-500">Categories will show here once they are published in the catalog.</p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
                @foreach($featuredCategories as $category)
                    <a href="{{ $category->collectionsUrl() }}" class="qb-card group p-4 text-center transition hover:-translate-y-0.5 sm:p-5">
                        <span class="mx-auto flex h-14 w-14 items-center justify-center rounded-full text-white transition group-hover:scale-105 sm:h-16 sm:w-16" style="background: var(--qb-navy)">
                            <i data-lucide="{{ $category->iconName() }}" class="h-6 w-6 sm:h-7 sm:w-7" aria-hidden="true"></i>
                        </span>
                        <h3 class="mt-3 text-sm font-bold text-slate-900 sm:text-base">{{ $category->displayNavLabel() }}</h3>
                        <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'item' : 'items' }}</p>
                    </a>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if ($sections['trending'] ?? true)
    <section class="px-4 pb-10 sm:px-6 lg:px-8" aria-labelledby="home-trending-title">
        <div class="mb-6">
            <p class="qb-eyebrow">{{ $trendingCopy['badge'] }}</p>
            <h2 id="home-trending-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $trendingCopy['heading'] }}</h2>
            <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $trendingCopy['description'] }}</p>
        </div>
        @if($trendingProducts->isEmpty())
            <p class="qb-card px-5 py-8 text-sm text-slate-500">No products are published yet.</p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                @foreach($trendingProducts as $product)
                    <article class="qb-card group flex flex-col overflow-hidden">
                        <a href="{{ $product['url'] }}" class="relative block aspect-square overflow-hidden bg-slate-100">
                            <img src="{{ $product['image'] ?: $placeholder }}" alt="{{ $product['name'] }}" width="480" height="480" loading="lazy" decoding="async" class="h-full w-full object-cover transition duration-500 group-hover:scale-105" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                            @if($product['discount_percent'])
                                <span class="absolute left-2 top-2 rounded-full px-2 py-1 text-[11px] font-extrabold text-white sm:left-3" style="background: var(--qb-orange)">-{{ $product['discount_percent'] }}%</span>
                            @endif
                        </a>
                        <div class="flex flex-1 flex-col p-3 sm:p-4">
                            @if(filled($product['category']))
                                <p class="qb-eyebrow">{{ $product['category'] }}</p>
                            @endif
                            <h3 class="mt-1 line-clamp-2 text-sm font-bold text-slate-900"><a href="{{ $product['url'] }}">{{ $product['name'] }}</a></h3>
                            <p class="mt-3 flex items-baseline gap-2">
                                <span class="text-base font-extrabold text-slate-900 sm:text-lg">{{ $product['price_label'] }}</span>
                                @if($product['old_price_label'])
                                    <span class="text-xs text-slate-400 line-through">{{ $product['old_price_label'] }}</span>
                                @endif
                            </p>
                            <button type="button" @if($product['in_stock']) onclick="addToCart({{ (int) $product['id'] }})" @else disabled @endif class="mt-4 min-h-11 rounded-xl px-2 text-xs font-bold sm:text-sm {{ $product['in_stock'] ? 'qb-btn-dark' : 'cursor-not-allowed bg-slate-100 text-slate-400' }}">
                                {{ $product['in_stock'] ? 'Add to cart' : 'Out of stock' }}
                            </button>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
    @endif

    @if (($sections['reviews'] ?? true) && $latestReviews->isNotEmpty())
    <section class="px-4 pb-10 sm:px-6 lg:px-8" aria-labelledby="home-reviews-title">
        <div class="qb-reviews">
            <h2 id="home-reviews-title" class="text-lg font-extrabold text-slate-900">Reviews</h2>
            <div class="mt-4 grid gap-6 lg:grid-cols-12 lg:items-center">
                <div class="lg:col-span-4">
                    <p class="text-4xl font-extrabold leading-none text-slate-900">{{ number_format($reviewSummary['average'], 1) }}</p>
                    <p class="qb-stars mt-2" aria-hidden="true">
                        @for($star = 1; $star <= 5; $star++)
                            <span class="{{ $star <= round($reviewSummary['average']) ? 'is-on' : '' }}">★</span>
                        @endfor
                    </p>
                    <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($reviewSummary['count']) }} {{ $reviewSummary['count'] === 1 ? 'rating' : 'ratings' }}</p>
                </div>
                <ul class="space-y-2 lg:col-span-8">
                    @foreach($reviewSummary['bars'] as $bar)
                        <li class="grid grid-cols-[4.5rem_1fr_2.5rem] items-center gap-2 text-xs font-semibold text-slate-600">
                            <span>{{ $bar['label'] }}</span>
                            <span class="qb-bar" aria-hidden="true"><span style="width: {{ $bar['percent'] }}%"></span></span>
                            <span class="text-right">{{ $bar['count'] }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="mt-6 divide-y divide-slate-100">
                @foreach($latestReviews as $review)
                    <article class="grid grid-cols-1 gap-3 py-4 sm:grid-cols-[auto_1fr_auto] sm:items-start">
                        <span class="qb-avatar" aria-hidden="true">{{ $review->authorInitials() }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-extrabold text-slate-900">{{ $review->author_name }}</p>
                            <p class="text-[11px] font-semibold text-slate-400">{{ $review->created_at?->diffForHumans() }} @if($review->is_verified_buyer) · Verified buyer @endif</p>
                            @if($review->title)
                                <h3 class="mt-2 text-sm font-bold text-slate-800">{{ $review->title }}</h3>
                            @endif
                            <p class="mt-1 text-sm leading-relaxed text-slate-600">{{ $review->body }}</p>
                            @if($review->product)
                                <a href="{{ route('product.show', $review->product) }}" class="mt-2 inline-flex items-center gap-2 text-xs font-bold" style="color: var(--qb-navy)">
                                    @if(filled($review->product->image))
                                        <img src="{{ $review->product->image }}" alt="" width="36" height="36" loading="lazy" decoding="async" class="h-9 w-9 rounded-md object-cover">
                                    @endif
                                    {{ $review->product->name }}
                                </a>
                            @endif
                        </div>
                        <p class="flex items-center gap-1 text-sm font-extrabold text-slate-900 sm:justify-end">
                            {{ number_format($review->rating, 1) }}
                            <span class="qb-stars" aria-label="{{ $review->rating }} out of 5">
                                @for($star = 1; $star <= 5; $star++)
                                    <span class="{{ $star <= $review->rating ? 'is-on' : '' }}">★</span>
                                @endfor
                            </span>
                        </p>
                    </article>
                @endforeach
            </div>
            <a href="{{ route('collections') }}" class="mt-2 inline-flex text-sm font-extrabold" style="color: var(--qb-orange)">Shop reviewed products</a>
        </div>
    </section>
    @endif

    @if (($sections['journal'] ?? true) && $journalPosts->isNotEmpty())
    <section class="px-4 pb-10 sm:px-6 lg:px-8" aria-labelledby="home-journal-title">
        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
            <div>
                <p class="qb-eyebrow">Journal</p>
                <h2 id="home-journal-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Guides from the store</h2>
            </div>
            <a href="{{ route('blog.index') }}" class="text-sm font-bold" style="color: var(--qb-orange)">Read the journal</a>
        </div>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            @foreach($journalPosts as $post)
                <a href="{{ route('blog.show', $post) }}" class="qb-card group overflow-hidden">
                    <img src="{{ $post->image }}" alt="" width="640" height="360" loading="lazy" decoding="async" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
                    <span class="block p-4">
                        <span class="qb-eyebrow">{{ $post->category }}</span>
                        <span class="mt-1 block text-base font-bold text-slate-900">{{ $post->title }}</span>
                        <span class="mt-2 block text-sm leading-relaxed text-slate-600">{{ $post->snippet }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>
    @endif

    @if ($sections['flash'] ?? true)
    <section class="px-4 pb-12 sm:px-6 lg:px-8" aria-labelledby="home-flash-title">
        <div class="grid w-full items-center gap-6 overflow-hidden rounded-2xl px-5 py-8 text-white sm:rounded-3xl sm:px-10 sm:py-12 lg:grid-cols-12" style="background: linear-gradient(135deg, var(--qb-navy), var(--qb-navy-deep) 70%)" x-data="qbFlashSale(@js($flash['ends_at']), @js($flash['hours']), @js($flash['minutes']), @js($flash['seconds']))">
            <div class="lg:col-span-7">
                <p class="qb-pill"><i data-lucide="timer" class="h-3.5 w-3.5" aria-hidden="true"></i> {{ $flashBadge }}</p>
                <h2 id="home-flash-title" class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $flash['title'] }}</h2>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-slate-200">{{ $flash['description'] }}</p>
                <div class="mt-6 flex flex-wrap items-center gap-2" aria-live="polite" aria-atomic="true">
                    <span class="sr-only" x-text="ended ? 'This flash sale has ended' : 'Time remaining ' + hours + ' hours ' + minutes + ' minutes ' + seconds + ' seconds'"></span>
                    <template x-for="part in [{label: 'Hours', value: hours}, {label: 'Minutes', value: minutes}, {label: 'Seconds', value: seconds}]" :key="part.label">
                        <span class="flex min-w-16 flex-col items-center rounded-2xl bg-white/10 px-3 py-2">
                            <span class="font-mono text-2xl font-black tabular-nums" x-text="part.value"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wide text-slate-300" x-text="part.label"></span>
                        </span>
                    </template>
                </div>
                <a href="{{ route('exclusive-offers') }}" class="qb-btn mt-6">Shop the flash sale</a>
            </div>
            @if($flashProduct)
                <a href="{{ $flashProduct['url'] }}" class="flex items-center gap-4 rounded-2xl bg-white/10 p-4 ring-1 ring-white/20 lg:col-span-5">
                    <img src="{{ $flashProduct['image'] ?: $placeholder }}" alt="" width="112" height="112" loading="lazy" decoding="async" class="h-24 w-24 shrink-0 rounded-xl object-cover sm:h-28 sm:w-28" onerror="this.onerror=null;this.src='{{ $placeholder }}';">
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-wide" style="color: var(--qb-highlight)">{{ $flashProduct['category'] ?: 'Spotlight' }}</span>
                        <span class="mt-1 block text-lg font-bold">{{ $flashProduct['name'] }}</span>
                        <span class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl font-extrabold">{{ $flashProduct['price_label'] }}</span>
                            @if($flashProduct['old_price_label'])
                                <span class="text-sm text-slate-300 line-through">{{ $flashProduct['old_price_label'] }}</span>
                            @endif
                        </span>
                    </span>
                </a>
            @endif
        </div>
    </section>
    @endif
</main>
@endsection

@push('scripts')
@unless($alpineAlreadyLoaded)
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
@endunless
<script>
    window.products = @json($productsCatalog);
    window.qbFlashSale = function (endsAt, hours, minutes, seconds) {
        return {
            endsAt: endsAt,
            hours: hours,
            minutes: minutes,
            seconds: seconds,
            ended: false,
            init() {
                this.tick();
                this._timer = window.setInterval(() => this.tick(), 1000);
            },
            tick() {
                const remaining = Math.max(0, Math.floor((new Date(this.endsAt).getTime() - Date.now()) / 1000));
                this.ended = remaining === 0;
                this.hours = String(Math.floor(remaining / 3600)).padStart(2, '0');
                this.minutes = String(Math.floor((remaining % 3600) / 60)).padStart(2, '0');
                this.seconds = String(remaining % 60).padStart(2, '0');
            },
            destroy() {
                window.clearInterval(this._timer);
            },
        };
    };
</script>
@endpush
