@extends('layouts.app')

@section('title', 'QuickBazar — Shop verified products with cash on delivery')

@section('meta_description', 'Shop original products on QuickBazar with nationwide shipping, cash on delivery, verified quality, and WhatsApp support across Pakistan.')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col overflow-x-clip"
@endsection

@section('content')
@php
    $promo = $promoProduct;
    $placeholder = 'https://placehold.co/640x640/ecfdf5/047857?text=QuickBazar';
@endphp
<main>
    <section class="px-4 py-8 sm:px-8 sm:py-12 lg:px-12" aria-labelledby="home-hero-title">
        <div class="mx-auto grid w-full items-center gap-6 lg:grid-cols-12">
            <div class="lg:col-span-7">
                <p class="inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-800">
                    <i data-lucide="badge-check" class="h-3.5 w-3.5" aria-hidden="true"></i>
                    {{ $hero['badge'] }}
                </p>
                <h1 id="home-hero-title" class="mt-4 max-w-xl text-3xl font-extrabold leading-tight tracking-tight text-slate-900 sm:text-5xl">
                    {{ $hero['title'] }}
                    <span class="text-emerald-700">{{ $hero['accent'] }}</span>
                </h1>
                <p class="mt-4 max-w-xl text-sm leading-relaxed text-slate-600 sm:text-base">
                    {{ $hero['description'] }}
                </p>
                <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('collections') }}" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl bg-emerald-600 px-6 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700">
                        Shop Now
                        <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
                    </a>
                    <a href="#categories" class="inline-flex min-h-12 items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white px-6 text-sm font-bold text-slate-800 transition hover:border-emerald-300 hover:text-emerald-800">
                        <i data-lucide="layout-grid" class="h-4 w-4" aria-hidden="true"></i>
                        Browse Categories
                    </a>
                </div>
                <ul class="mt-6 flex flex-wrap gap-2 text-xs font-semibold text-slate-600">
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 ring-1 ring-slate-200">
                        <i data-lucide="truck" class="h-3.5 w-3.5 text-emerald-600" aria-hidden="true"></i>
                        Nationwide shipping
                    </li>
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 ring-1 ring-slate-200">
                        <i data-lucide="banknote" class="h-3.5 w-3.5 text-emerald-600" aria-hidden="true"></i>
                        Cash on delivery
                    </li>
                    <li class="inline-flex items-center gap-1.5 rounded-full bg-white px-3 py-1.5 ring-1 ring-slate-200">
                        <i data-lucide="shield-check" class="h-3.5 w-3.5 text-emerald-600" aria-hidden="true"></i>
                        Verified quality
                    </li>
                </ul>
            </div>

            <div class="lg:col-span-5">
                <article class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-xl shadow-slate-200/70">
                    <div class="relative aspect-[4/3] bg-gradient-to-br from-emerald-50 via-white to-teal-50">
                        <img
                            src="{{ $promo['image'] ?? $hero['image'] }}"
                            alt="{{ $promo['name'] ?? $hero['image_alt'] }}"
                            width="640"
                            height="480"
                            fetchpriority="high"
                            decoding="async"
                            class="h-full w-full object-cover"
                            onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                        >
                        @if(! empty($promo['discount_percent']))
                            <span class="absolute left-4 top-4 rounded-full bg-emerald-600 px-3 py-1 text-xs font-extrabold text-white">
                                {{ $promo['discount_percent'] }}% off
                            </span>
                        @endif
                    </div>
                    <div class="space-y-4 p-5 sm:p-6">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-teal-700">{{ $promo['category'] ?? 'Featured offer' }}</p>
                            <h2 class="mt-1 text-xl font-bold text-slate-900">{{ $promo['name'] ?? 'Today’s store offer' }}</h2>
                        </div>
                        @if($promo)
                            <p class="flex items-baseline gap-2">
                                <span class="text-2xl font-extrabold text-slate-900">{{ $promo['price_label'] }}</span>
                                @if($promo['old_price_label'])
                                    <span class="text-sm text-slate-400 line-through">{{ $promo['old_price_label'] }}</span>
                                @endif
                            </p>
                            <div class="flex flex-col gap-2 sm:flex-row">
                                @if($promo['in_stock'])
                                    <button type="button" onclick="addToCart({{ (int) $promo['id'] }})" class="inline-flex min-h-11 flex-1 items-center justify-center gap-2 rounded-xl bg-emerald-600 text-sm font-bold text-white transition hover:bg-emerald-700">
                                        <i data-lucide="shopping-bag" class="h-4 w-4" aria-hidden="true"></i>
                                        Add to Cart
                                    </button>
                                @endif
                                <a href="{{ $promo['url'] }}" class="inline-flex min-h-11 flex-1 items-center justify-center rounded-xl border border-slate-200 text-sm font-bold text-slate-800 transition hover:border-emerald-300">
                                    View product
                                </a>
                            </div>
                        @else
                            <a href="{{ route('collections') }}" class="inline-flex min-h-11 items-center justify-center rounded-xl bg-emerald-600 px-5 text-sm font-bold text-white hover:bg-emerald-700">
                                Shop the catalog
                            </a>
                        @endif
                    </div>
                </article>
            </div>
        </div>
    </section>

    <section class="border-y border-slate-200 bg-white" aria-label="Why shop with QuickBazar">
        <ul class="mx-auto grid w-full grid-cols-1 sm:grid-cols-2 lg:grid-cols-4">
            <li>
                <a href="{{ route('shipping') }}" class="flex h-full items-start gap-3 px-5 py-5 transition hover:bg-emerald-50">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        <i data-lucide="truck" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-sm font-bold text-slate-900">Nationwide Shipping</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">Delivery across Pakistan, tracked from checkout.</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('checkout') }}" class="flex h-full items-start gap-3 px-5 py-5 transition hover:bg-emerald-50">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                        <i data-lucide="banknote" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-sm font-bold text-slate-900">Cash on Delivery</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">Pay in PKR when the parcel reaches you.</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ route('our-story') }}" class="flex h-full items-start gap-3 px-5 py-5 transition hover:bg-emerald-50">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-700">
                        <i data-lucide="shield-check" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-sm font-bold text-slate-900">100% Verified Quality</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">Catalog items are listed with real store prices.</span>
                    </span>
                </a>
            </li>
            <li>
                <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener noreferrer" class="flex h-full items-start gap-3 px-5 py-5 transition hover:bg-emerald-50">
                    <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-teal-50 text-teal-700">
                        <i data-lucide="message-circle" class="h-5 w-5" aria-hidden="true"></i>
                    </span>
                    <span>
                        <span class="block text-sm font-bold text-slate-900">24/7 Support</span>
                        <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">WhatsApp and live chat when you need a hand.</span>
                    </span>
                </a>
            </li>
        </ul>
    </section>

    <section id="categories" class="px-4 py-12 sm:px-8 lg:px-12" aria-labelledby="home-categories-title">
        <div class="mx-auto w-full">
            <div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Departments</p>
                    <h2 id="home-categories-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Featured categories</h2>
                </div>
                <a href="{{ route('collections') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">View all products</a>
            </div>

            @if($featuredCategories->isEmpty())
                <p class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-500">Categories will show here once they are published in the catalog.</p>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 xl:grid-cols-5">
                    @foreach($featuredCategories as $category)
                        <a href="{{ $category->collectionsUrl() }}" class="group rounded-2xl border border-slate-200 bg-white p-5 text-center shadow-sm transition hover:-translate-y-0.5 hover:border-emerald-200 hover:shadow-md">
                            <span class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-700 transition duration-300 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white">
                                <i data-lucide="{{ $category->iconName() }}" class="h-7 w-7" aria-hidden="true"></i>
                            </span>
                            <h3 class="mt-4 text-base font-bold text-slate-900">{{ $category->displayNavLabel() }}</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ number_format($category->products_count) }} {{ $category->products_count === 1 ? 'item' : 'items' }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    <section class="px-4 pb-12 sm:px-8 lg:px-12" aria-labelledby="home-trending-title">
        <div class="mx-auto w-full">
            <div class="mb-8">
                <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">{{ $trendingCopy['badge'] }}</p>
                <h2 id="home-trending-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">{{ $trendingCopy['heading'] }}</h2>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">{{ $trendingCopy['description'] }}</p>
            </div>

            @if($trendingProducts->isEmpty())
                <p class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-8 text-sm text-slate-500">No products are published yet.</p>
            @else
                <div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                    @foreach($trendingProducts as $product)
                        <article class="group flex flex-col overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                            <a href="{{ $product['url'] }}" class="relative block aspect-square overflow-hidden bg-slate-100">
                                <img
                                    src="{{ $product['image'] ?: $placeholder }}"
                                    alt="{{ $product['name'] }}"
                                    width="480"
                                    height="480"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover transition duration-500 group-hover:scale-110"
                                    onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                                >
                                @if($product['discount_percent'])
                                    <span class="absolute left-3 top-3 rounded-full bg-emerald-600 px-2.5 py-1 text-[11px] font-extrabold text-white">
                                        -{{ $product['discount_percent'] }}%
                                    </span>
                                @endif
                            </a>
                            <div class="flex flex-1 flex-col p-4">
                                @if(filled($product['category']))
                                    <p class="text-[11px] font-bold uppercase tracking-wide text-teal-700">{{ $product['category'] }}</p>
                                @endif
                                <h3 class="mt-1 line-clamp-2 text-sm font-bold text-slate-900">
                                    <a href="{{ $product['url'] }}" class="hover:text-emerald-700">{{ $product['name'] }}</a>
                                </h3>
                                <p class="mt-3 flex items-baseline gap-2">
                                    <span class="text-lg font-extrabold text-slate-900">{{ $product['price_label'] }}</span>
                                    @if($product['old_price_label'])
                                        <span class="text-xs text-slate-400 line-through">{{ $product['old_price_label'] }}</span>
                                    @endif
                                </p>
                                <button
                                    type="button"
                                    @if($product['in_stock']) onclick="addToCart({{ (int) $product['id'] }})" @else disabled @endif
                                    class="mt-4 inline-flex min-h-11 items-center justify-center gap-2 rounded-xl px-2 text-xs font-bold transition sm:text-sm {{ $product['in_stock'] ? 'bg-emerald-600 text-white hover:bg-emerald-700' : 'cursor-not-allowed bg-slate-100 text-slate-400' }}"
                                >
                                    <i data-lucide="shopping-bag" class="h-4 w-4" aria-hidden="true"></i>
                                    {{ $product['in_stock'] ? 'Add to Cart' : 'Out of stock' }}
                                </button>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if($latestReviews->isNotEmpty())
    <section class="px-4 pb-12 sm:px-8 lg:px-12" aria-labelledby="home-reviews-title">
        <div class="mx-auto w-full">
            <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Verified buyers</p>
            <h2 id="home-reviews-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Recent reviews</h2>
            <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach($latestReviews as $review)
                    <article class="rounded-2xl border border-slate-200 bg-white p-5">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-bold text-slate-900">{{ $review->author_name }}</p>
                            <p class="text-xs font-bold text-emerald-700">{{ $review->rating }}/5</p>
                        </div>
                        @if($review->title)
                            <h3 class="mt-2 text-sm font-bold text-slate-800">{{ $review->title }}</h3>
                        @endif
                        <p class="mt-2 text-sm leading-relaxed text-slate-600">{{ $review->body }}</p>
                        @if($review->product)
                            <a href="{{ route('product.show', $review->product) }}" class="mt-3 inline-block text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ $review->product->name }}</a>
                        @endif
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    @if($journalPosts->isNotEmpty())
    <section class="px-4 pb-12 sm:px-8 lg:px-12" aria-labelledby="home-journal-title">
        <div class="mx-auto w-full">
            <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div>
                    <p class="text-xs font-bold uppercase tracking-wide text-emerald-700">Journal</p>
                    <h2 id="home-journal-title" class="mt-1 text-2xl font-extrabold text-slate-900 sm:text-3xl">Guides from the store</h2>
                </div>
                <a href="{{ route('blog.index') }}" class="text-sm font-bold text-emerald-700 hover:text-emerald-800">Read the journal</a>
            </div>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                @foreach($journalPosts as $post)
                    <a href="{{ route('blog.show', $post) }}" class="group overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:border-emerald-200 hover:shadow-md">
                        <img src="{{ $post->image }}" alt="" width="640" height="360" loading="lazy" decoding="async" class="aspect-video w-full object-cover transition duration-500 group-hover:scale-105">
                        <span class="block p-4">
                            <span class="text-[11px] font-bold uppercase tracking-wide text-teal-700">{{ $post->category }}</span>
                            <span class="mt-1 block text-base font-bold text-slate-900 group-hover:text-emerald-800">{{ $post->title }}</span>
                            <span class="mt-2 block text-sm leading-relaxed text-slate-600">{{ $post->snippet }}</span>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    <section class="px-4 pb-14 sm:px-8 lg:px-12" aria-labelledby="home-flash-title">
        <div
            class="mx-auto grid w-full items-center gap-8 overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-700 via-emerald-600 to-teal-500 px-5 py-8 text-white shadow-xl sm:px-10 sm:py-12 lg:grid-cols-12"
            x-data="qbFlashSale(@js($flash['ends_at']), @js($flash['hours']), @js($flash['minutes']), @js($flash['seconds']))"
        >
            <div class="lg:col-span-7">
                <p class="inline-flex items-center gap-2 rounded-full bg-white/15 px-3 py-1 text-xs font-bold uppercase tracking-wide">
                    <i data-lucide="timer" class="h-3.5 w-3.5" aria-hidden="true"></i>
                    {{ $flash['badge'] }}
                </p>
                <h2 id="home-flash-title" class="mt-3 text-3xl font-extrabold tracking-tight sm:text-4xl">{{ $flash['title'] }}</h2>
                <p class="mt-3 max-w-xl text-sm leading-relaxed text-emerald-50">{{ $flash['description'] }}</p>
                <div class="mt-6 flex items-center gap-2" aria-live="polite" aria-atomic="true">
                    <span class="sr-only" x-text="ended ? 'This flash sale has ended' : 'Time remaining ' + hours + ' hours ' + minutes + ' minutes ' + seconds + ' seconds'"></span>
                    <template x-for="part in [{label: 'Hours', value: hours}, {label: 'Minutes', value: minutes}, {label: 'Seconds', value: seconds}]" :key="part.label">
                        <span class="flex min-w-16 flex-col items-center rounded-2xl bg-slate-950/30 px-3 py-2">
                            <span class="font-mono text-2xl font-black tabular-nums" x-text="part.value"></span>
                            <span class="text-[10px] font-bold uppercase tracking-wide text-emerald-100" x-text="part.label"></span>
                        </span>
                    </template>
                </div>
                <a href="{{ route('exclusive-offers') }}" class="mt-6 inline-flex min-h-11 items-center justify-center rounded-xl bg-white px-5 text-sm font-bold text-emerald-800 transition hover:bg-emerald-50">
                    Shop the flash sale
                </a>
            </div>
            @if($flashProduct)
                <a href="{{ $flashProduct['url'] }}" class="flex items-center gap-4 rounded-2xl bg-white/10 p-4 ring-1 ring-white/20 backdrop-blur-sm lg:col-span-5">
                    <img
                        src="{{ $flashProduct['image'] ?: $placeholder }}"
                        alt=""
                        width="112"
                        height="112"
                        loading="lazy"
                        decoding="async"
                        class="h-28 w-28 shrink-0 rounded-xl object-cover"
                        onerror="this.onerror=null;this.src='{{ $placeholder }}';"
                    >
                    <span>
                        <span class="block text-xs font-bold uppercase tracking-wide text-emerald-100">{{ $flashProduct['category'] ?: 'Spotlight' }}</span>
                        <span class="mt-1 block text-lg font-bold">{{ $flashProduct['name'] }}</span>
                        <span class="mt-2 flex items-baseline gap-2">
                            <span class="text-xl font-extrabold">{{ $flashProduct['price_label'] }}</span>
                            @if($flashProduct['old_price_label'])
                                <span class="text-sm text-emerald-100 line-through">{{ $flashProduct['old_price_label'] }}</span>
                            @endif
                        </span>
                    </span>
                </a>
            @endif
        </div>
    </section>
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
