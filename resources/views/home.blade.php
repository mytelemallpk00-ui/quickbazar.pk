@extends('layouts.app')

@section('lumiere_homepage', '1')

@section('title', 'Beautage — Elevate Your Natural Elegance | Flagship Store')

@section('body_attrs')
class="lumiere-home bg-gray-50 text-gray-800 antialiased pb-24 md:pb-0"
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body.lumiere-home { font-family: Inter, sans-serif; }
    .mega-menu-container { transition: opacity 0.2s ease, visibility 0.2s ease, transform 0.2s ease; }
    body.lumiere-home ::-webkit-scrollbar { width: 8px; height: 8px; }
    body.lumiere-home ::-webkit-scrollbar-track { background: #f1f1f1; }
    body.lumiere-home ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
</style>
@endpush

@section('content')
@php
    $siteName = (string) \App\Support\SiteSettings::get('site_name', 'Beautage');
    $siteTagline = (string) \App\Support\SiteSettings::get('site_tagline', 'Elevate Your Natural Elegance');
    $siteLogoUrl = \App\Support\SiteSettings::logoUrl();
    $supportPhone = (string) \App\Support\SiteSettings::get('support_phone', '+1 (800) BEAUTAGE');
    $supportPhoneTel = preg_replace('/[^\d+]/', '', $supportPhone) ?: '';
    $footer = \App\Support\FooterSettings::forStorefront();
    $monogram = mb_strtoupper(mb_substr(trim($siteName) !== '' ? $siteName : 'B', 0, 1));

    $money = function ($amount): string {
        $n = (float) $amount;
        $formatted = abs($n - round($n)) < 0.001 ? number_format($n, 0) : number_format($n, 2);

        return 'Rs. '.$formatted;
    };
    $imageUrl = function ($path, string $alt = 'Product'): string {
        $path = trim((string) $path);
        if ($path === '') {
            return 'https://placehold.co/400x400/1C2541/D4AF37?text='.rawurlencode($alt !== '' ? $alt : 'Beautage');
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
            return $path;
        }

        return \App\Support\HomepageContent::imageUrl($path, $alt);
    };
    $offLabel = function (array $item): ?string {
        $price = (float) ($item['price'] ?? 0);
        $old = (float) ($item['oldPrice'] ?? 0);
        if ($old > $price && $old > 0) {
            return ((int) round((1 - ($price / $old)) * 100)).'% OFF';
        }
        $preset = trim((string) ($item['discount'] ?? ''));

        return $preset !== '' ? $preset : null;
    };

    $catalog = collect($productsCatalog ?? [])->values();
    $heroItem = null;
    if ($flashSpotlightProduct) {
        $heroItem = $catalog->firstWhere('id', $flashSpotlightProduct->id);
    }
    if (! $heroItem) {
        $heroItem = $catalog->first(fn ($item) => ! empty($item['flash'])) ?? $catalog->first();
    }

    $flashProducts = $catalog->filter(fn ($item) => ! empty($item['flash']))->values();
    if ($flashProducts->isEmpty()) {
        $flashProducts = $catalog->take(4)->values();
    } else {
        $flashProducts = $flashProducts->take(4)->values();
    }

    $hairProducts = $catalog->filter(function ($item) {
        $haystack = strtolower(((string) ($item['category'] ?? '')).' '.((string) ($item['categorySlug'] ?? '')));

        return str_contains($haystack, 'hair');
    })->values();
    $trendingProducts = ($hairProducts->isNotEmpty() ? $hairProducts : $catalog)->take(3)->values();

    $bestProducts = $catalog->sortByDesc(function ($item) {
        return ((float) ($item['rating'] ?? 0) * 100000) + (int) ($item['reviews'] ?? 0);
    })->take(4)->values();

    $bundleItem = $catalog->first(function ($item) {
        $haystack = strtolower(((string) ($item['name'] ?? '')).' '.((string) ($item['badge'] ?? '')));

        return str_contains($haystack, 'bundle') || str_contains($haystack, 'kit');
    });
    if (! $bundleItem && $featuredPromoProduct) {
        $bundleItem = $catalog->firstWhere('id', $featuredPromoProduct->id);
    }
    if (! $bundleItem) {
        $bundleItem = $catalog->get(1) ?? $catalog->first();
    }
    $bundleEyebrow = trim((string) data_get($homepage, 'featured_promo.eyebrow', ''));
    if ($bundleEyebrow === '') {
        $bundleEyebrow = 'Exclusive Bundle';
    }

    $categoryCounts = $catalog->countBy(fn ($item) => (string) ($item['categorySlug'] ?? ''));
    $iconCycle = [
        ['fa-spray-can-sparkles', 'from-brand-navy to-brand-dark', 'text-brand-gold'],
        ['fa-droplet', 'from-amber-600 to-amber-400', 'text-white'],
        ['fa-pump-soap', 'from-purple-600 to-indigo-500', 'text-white'],
        ['fa-wand-magic-sparkles', 'from-rose-500 to-pink-400', 'text-white'],
        ['fa-spa', 'from-teal-600 to-emerald-400', 'text-white'],
        ['fa-box-archive', 'from-amber-700 to-yellow-600', 'text-white'],
    ];
    $slugIcons = [
        'hair-care' => $iconCycle[0],
        'skincare' => $iconCycle[2],
        'beauty-tech' => $iconCycle[3],
        'fragrance' => $iconCycle[4],
        'wellness' => $iconCycle[1],
    ];
    $categoryTiles = collect($shopCategories ?? [])->take(6)->values();
    $menuColumns = collect($shopCategories ?? [])->take(3)->map(function ($category) use ($catalog) {
        $products = $catalog->filter(function ($item) use ($category) {
            return (string) ($item['categorySlug'] ?? '') === (string) $category->slug
                || (int) ($item['categoryId'] ?? 0) === (int) $category->id;
        })->take(4)->values();

        return ['category' => $category, 'products' => $products];
    });
    $navCategories = collect($shopCategories ?? [])->take(3);

    $heroWords = preg_split('/\s+/', trim((string) ($heroItem['name'] ?? ''))) ?: [];
    $heroLead = $heroItem ? implode(' ', count($heroWords) > 2 ? array_slice($heroWords, 0, -2) : $heroWords) : (string) data_get($homepage, 'hero.title_line1', 'Beautage');
    $heroAccent = ($heroItem && count($heroWords) > 2) ? implode(' ', array_slice($heroWords, -2)) : (string) data_get($homepage, 'hero.title_accent', '');
    $heroDescription = $heroItem
        ? \Illuminate\Support\Str::limit(trim(strip_tags((string) ($heroItem['desc'] ?? ''))), 220)
        : (string) data_get($homepage, 'hero.description', '');
    $heroBadge = $heroItem
        ? ((string) ($heroItem['badge'] ?? '') !== '' ? $heroItem['badge'] : 'Flagship')
        : (string) data_get($homepage, 'hero.badge', 'Flagship');
    $heroSave = $heroItem ? $offLabel($heroItem) : null;
    $heroRating = $heroItem ? (float) ($heroItem['rating'] ?? 0) : 0;
    $heroReviews = $heroItem ? (int) ($heroItem['reviews'] ?? 0) : 0;
    $heroUrl = ($heroItem && filled($heroItem['slug'] ?? null)) ? route('product.show', $heroItem['slug']) : route('collections');

    $bestBadges = [
        ['BEST SELLER', 'bg-brand-gold text-brand-navy'],
        ['POPULAR', 'bg-brand-accent text-white'],
        ['ORGANIC', 'bg-emerald-500 text-white'],
        ['LIMITED', 'bg-purple-500 text-white'],
    ];
    $trendBadges = [
        ['Best Seller', 'bg-amber-100 text-amber-800'],
        ['New Formula', 'bg-blue-100 text-blue-800'],
        ['Organic 100%', 'bg-purple-100 text-purple-800'],
    ];

    $reviewStats = $homeReviewStats ?? ['avg' => 0, 'customers' => 0, 'published' => 0];
    $reviewCards = collect($homeFeaturedReviews ?? [])->take(3);
    $customerCount = (int) ($reviewStats['customers'] ?? 0);

    $socialIcons = [
        'facebook' => 'fa-brands fa-facebook-f',
        'instagram' => 'fa-brands fa-instagram',
        'twitter' => 'fa-brands fa-x-twitter',
        'youtube' => 'fa-brands fa-youtube',
        'tiktok' => 'fa-brands fa-tiktok',
        'pin' => 'fa-brands fa-pinterest-p',
        'pinterest' => 'fa-brands fa-pinterest-p',
    ];
@endphp

<div id="toast" class="hidden"></div>

<div class="w-full bg-brand-navy text-white text-xs py-2 px-4">
    <div class="w-full max-w-[1600px] mx-auto flex flex-col sm:flex-row justify-between items-center gap-2">
        <div class="flex items-center space-x-4">
            <span class="flex items-center gap-1.5"><i class="fa-solid fa-truck-fast text-brand-gold"></i> Free Shipping on Orders Over Rs 7,500</span>
            <span class="hidden md:inline text-gray-400">|</span>
            <span class="hidden md:flex items-center gap-1.5"><i class="fa-solid fa-shield-halved text-brand-gold"></i> 100% Genuine Original Products</span>
        </div>
        <div class="flex items-center space-x-6">
            <div class="flex items-center space-x-1 text-white">
                <span>PKR (Rs)</span>
            </div>
            <div class="flex items-center space-x-1">
                <span>English</span>
            </div>
            <a href="{{ route('help') }}" class="hover:text-brand-gold transition">Help &amp; Support</a>
        </div>
    </div>
</div>

<header class="sticky top-0 z-40 w-full bg-white shadow-sm border-b border-gray-100">
    <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-8">
        <div class="flex items-center justify-between h-20 gap-4">
            <div class="flex items-center space-x-3 flex-shrink-0">
                <a href="{{ route('home') }}" class="flex items-center gap-2">
                    <div class="relative w-10 h-10 rounded-xl bg-gradient-to-br from-brand-navy to-brand-dark flex items-center justify-center text-brand-gold font-bold text-xl shadow-md overflow-hidden">
                        <span>{{ $monogram }}</span>
                        <img src="{{ $siteLogoUrl }}" alt="" class="absolute inset-0 w-full h-full object-cover" onerror="this.remove()">
                    </div>
                    <div>
                        <span class="text-xl font-extrabold tracking-tight text-brand-navy block leading-none">{{ mb_strtoupper($siteName) }}</span>
                        <span class="text-[10px] uppercase tracking-widest text-brand-gold font-semibold">{{ $siteTagline }}</span>
                    </div>
                </a>
            </div>

            <div class="hidden lg:flex flex-1 max-w-xl mx-6">
                <div class="relative w-full">
                    <div class="relative w-full flex items-center border-2 border-brand-navy/10 rounded-xl bg-gray-50 focus-within:border-brand-navy transition overflow-hidden">
                        <input type="text" id="main-search-input" placeholder="Search skincare, hair care, serums, devices..." class="w-full bg-transparent px-4 py-2.5 text-sm text-gray-700 outline-none" autocomplete="off">
                        <button type="button" class="bg-brand-navy hover:bg-brand-dark text-white px-6 py-3 transition flex items-center justify-center" aria-label="Search">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                    </div>
                    <div id="search-results" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-gray-100 rounded-2xl shadow-2xl overflow-hidden z-50 p-2"></div>
                </div>
            </div>

            <div class="flex items-center space-x-4 sm:space-x-6">
                <div class="hidden sm:flex items-center space-x-1 bg-gray-100 px-3 py-2 rounded-xl text-xs font-semibold text-gray-700">
                    <span>PKR</span>
                </div>

                @auth
                <a href="{{ route('account.index') }}" class="hidden lg:flex flex-col items-center text-gray-600 hover:text-brand-navy transition group">
                    <div class="relative p-2 rounded-full group-hover:bg-gray-100">
                        <i class="fa-regular fa-user text-xl"></i>
                    </div>
                    <span class="text-[11px] font-medium">Account</span>
                </a>
                @else
                <a href="{{ route('login') }}" class="hidden lg:flex flex-col items-center text-gray-600 hover:text-brand-navy transition group">
                    <div class="relative p-2 rounded-full group-hover:bg-gray-100">
                        <i class="fa-regular fa-user text-xl"></i>
                    </div>
                    <span class="text-[11px] font-medium">Account</span>
                </a>
                @endauth

                <button type="button" onclick="toggleWishlistDrawer()" class="relative flex flex-col items-center text-gray-600 hover:text-brand-navy transition group" aria-label="Wishlist">
                    <div class="relative p-2 rounded-full group-hover:bg-gray-100">
                        <i class="fa-regular fa-heart text-xl"></i>
                        <span id="wishlist-badge" class="absolute top-1 right-1 bg-brand-gold text-brand-navy text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                        <span id="wishlist-badge-desktop" class="hidden">0</span>
                    </div>
                    <span class="text-[11px] font-medium hidden sm:block">Wishlist</span>
                </button>

                <button type="button" onclick="toggleCartDrawer()" class="relative flex flex-col items-center text-gray-600 hover:text-brand-navy transition group" aria-label="Cart">
                    <div class="relative p-2 rounded-full group-hover:bg-gray-100">
                        <i class="fa-solid fa-bag-shopping text-xl"></i>
                        <span id="cart-badge" class="absolute top-1 right-1 bg-brand-accent text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                        <span id="cart-badge-desktop" class="hidden">0</span>
                    </div>
                    <span class="text-[11px] font-medium hidden sm:block">Cart</span>
                </button>

                <button type="button" class="lg:hidden p-2 text-gray-700 hover:text-brand-navy" onclick="toggleMobileMenu()" aria-label="Open menu">
                    <i class="fa-solid fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="w-full bg-brand-navy text-white hidden lg:block">
        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-8 flex justify-between items-center">
            <div class="relative group py-3">
                <button type="button" class="bg-brand-gold hover:bg-yellow-500 text-brand-navy font-bold px-6 py-2.5 rounded-xl flex items-center gap-2.5 transition shadow">
                    <i class="fa-solid fa-grip text-base"></i>
                    <span>Collections &amp; Categories</span>
                    <i class="fa-solid fa-chevron-down text-xs ml-1"></i>
                </button>
                <div class="mega-menu-container absolute left-0 top-full w-[min(900px,80vw)] bg-white text-gray-800 shadow-2xl rounded-2xl border border-gray-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible translate-y-2 group-hover:translate-y-0 p-8 grid grid-cols-3 gap-8 z-50">
                    @forelse ($menuColumns as $column)
                        @php $menuCategory = $column['category']; @endphp
                        <div>
                            <h4 class="font-extrabold text-brand-navy text-sm uppercase tracking-wider mb-4 border-b pb-2 flex items-center gap-2">
                                <i class="fa-solid fa-layer-group text-brand-gold"></i> {{ $menuCategory->name }}
                            </h4>
                            <ul class="space-y-3 text-sm text-gray-600">
                                @forelse ($column['products'] as $menuProduct)
                                <li>
                                    <a href="{{ filled($menuProduct['slug'] ?? null) ? route('product.show', $menuProduct['slug']) : route('collections', ['cat' => $menuCategory->slug]) }}" class="hover:text-brand-accent flex items-center gap-2.5">
                                        <i class="fa-solid fa-droplet text-brand-gold"></i> {{ $menuProduct['name'] }}
                                    </a>
                                </li>
                                @empty
                                <li>
                                    <a href="{{ route('collections', ['cat' => $menuCategory->slug]) }}" class="hover:text-brand-accent flex items-center gap-2.5">
                                        <i class="fa-solid fa-arrow-right text-brand-gold"></i> Shop {{ $menuCategory->name }}
                                    </a>
                                </li>
                                @endforelse
                            </ul>
                        </div>
                    @empty
                        <div class="col-span-3 text-sm text-gray-500">Collections will appear here as categories are published.</div>
                    @endforelse
                </div>
            </div>

            <ul class="flex items-center space-x-8 text-sm font-medium py-3">
                <li><a href="#flash-deals" class="text-brand-gold flex items-center gap-1.5"><i class="fa-solid fa-fire"></i> Hot Deals</a></li>
                @foreach ($navCategories as $navCategory)
                <li>
                    <a href="{{ route('collections', ['cat' => $navCategory->slug]) }}" class="hover:text-brand-gold transition flex items-center gap-1.5">
                        <i class="fa-solid fa-tag"></i> {{ $navCategory->name }}
                    </a>
                </li>
                @endforeach
                <li><a href="#bestsellers" class="hover:text-brand-gold transition flex items-center gap-1.5"><i class="fa-solid fa-award"></i> Best Sellers</a></li>
                <li><a href="#reviews" class="hover:text-brand-gold transition flex items-center gap-1.5"><i class="fa-solid fa-star"></i> Reviews</a></li>
            </ul>
        </div>
    </div>
</header>

<div id="mobile-menu" class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm hidden" onclick="if (event.target === this) toggleMobileMenu()">
    <div class="absolute left-0 top-0 bottom-0 w-80 max-w-[85vw] bg-white shadow-2xl p-6 flex flex-col justify-between">
        <div>
            <div class="flex items-center justify-between pb-6 border-b border-gray-100">
                <span class="text-lg font-bold text-brand-navy">Menu &amp; Collections</span>
                <button type="button" onclick="toggleMobileMenu()" class="p-2 text-gray-500 hover:text-red-500" aria-label="Close menu"><i class="fa-solid fa-xmark text-xl"></i></button>
            </div>
            <div class="mt-4" id="mobile-search-container">
                <div class="relative w-full flex items-center border border-gray-200 rounded-lg bg-gray-50 mb-2">
                    <input type="text" id="mobile-search-input" placeholder="Search..." class="w-full bg-transparent px-3 py-2 text-sm outline-none" autocomplete="off">
                    <button type="button" class="p-2 text-brand-navy" aria-label="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
                <div id="mobile-search-results" class="hidden mb-4 bg-white border border-gray-100 rounded-xl shadow-sm overflow-hidden"></div>
            </div>
            <ul class="space-y-4 font-medium text-gray-700 text-sm">
                <li><a href="#flash-deals" onclick="toggleMobileMenu()" class="flex items-center justify-between py-2 border-b border-gray-100 text-brand-accent"><span class="flex items-center gap-2"><i class="fa-solid fa-fire"></i> Hot Deals</span> <i class="fa-solid fa-chevron-right text-xs"></i></a></li>
                @foreach ($navCategories as $navCategory)
                <li>
                    <a href="{{ route('collections', ['cat' => $navCategory->slug]) }}" class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="flex items-center gap-2"><i class="fa-solid fa-tag"></i> {{ $navCategory->name }}</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </a>
                </li>
                @endforeach
                <li><a href="#bundles" onclick="toggleMobileMenu()" class="flex items-center justify-between py-2 border-b border-gray-100"><span class="flex items-center gap-2"><i class="fa-solid fa-box-archive"></i> Bundle Kits</span> <i class="fa-solid fa-chevron-right text-xs"></i></a></li>
                <li><a href="#bestsellers" onclick="toggleMobileMenu()" class="flex items-center justify-between py-2 border-b border-gray-100"><span class="flex items-center gap-2"><i class="fa-solid fa-award"></i> Best Sellers</span> <i class="fa-solid fa-chevron-right text-xs"></i></a></li>
                <li><a href="#reviews" onclick="toggleMobileMenu()" class="flex items-center justify-between py-2"><span class="flex items-center gap-2"><i class="fa-solid fa-star"></i> Customer Reviews</span> <i class="fa-solid fa-chevron-right text-xs"></i></a></li>
            </ul>
        </div>
        <div class="pt-6 border-t border-gray-100 text-center text-xs text-gray-400">
            &copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.
        </div>
    </div>
</div>

<section class="w-full px-2 sm:px-6 mt-4">
    <div class="w-full max-w-[1600px] mx-auto rounded-3xl overflow-hidden bg-gradient-to-r from-brand-navy via-brand-dark to-[#243B55] text-white shadow-2xl">
        <div class="relative w-full grid grid-cols-1 lg:grid-cols-12 items-center p-8 sm:p-14 lg:p-20 gap-8">
            <div class="lg:col-span-7 space-y-6">
                @if (filled($heroBadge))
                <span class="bg-brand-gold/20 border border-brand-gold/40 text-brand-lightgold text-xs font-semibold px-4 py-1.5 rounded-full inline-flex items-center gap-2">
                    <i class="fa-solid fa-award text-brand-gold"></i> {{ $heroBadge }}
                </span>
                @endif
                <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight leading-tight uppercase">
                    {{ $heroLead }}
                    @if (filled($heroAccent))
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-gold to-yellow-200">{{ $heroAccent }}</span>
                    @endif
                </h1>
                @if (filled($heroDescription))
                <p class="text-gray-300 text-base sm:text-lg max-w-2xl">{{ $heroDescription }}</p>
                @endif
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#bestsellers" class="bg-brand-gold hover:bg-yellow-500 text-brand-navy font-bold px-8 py-4 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                        <span>Shop Now</span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </a>
                    <a href="{{ $heroUrl }}" class="border border-white/30 hover:bg-white/10 text-white font-semibold px-8 py-4 rounded-xl transition flex items-center gap-2">
                        <span>Explore Formula</span>
                    </a>
                </div>
            </div>
            @if ($heroItem)
            <div class="lg:col-span-5 flex justify-center">
                <div class="relative w-full max-w-md">
                    <div class="absolute -inset-2 bg-gradient-to-tr from-brand-gold to-brand-accent rounded-3xl blur-xl opacity-30 animate-pulse"></div>
                    <div class="relative bg-white/10 backdrop-blur-md border border-white/20 p-8 rounded-3xl shadow-2xl text-center space-y-4">
                        @if ($heroSave)
                        <span class="absolute top-4 right-4 bg-red-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow">{{ $heroSave }}</span>
                        @endif
                        <img src="{{ $imageUrl($heroItem['image'] ?? '', $heroItem['name'] ?? 'Product') }}" alt="{{ $heroItem['name'] }}" class="w-full h-72 object-cover rounded-2xl shadow-md border border-white/10" onerror="this.onerror=null; this.src='https://placehold.co/400x400/1C2541/D4AF37?text=Beautage';">
                        <div>
                            <h3 class="font-bold text-xl text-white">{{ $heroItem['name'] }}</h3>
                            <div class="flex items-center justify-center space-x-1 text-brand-gold my-1">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star text-xs {{ $i <= round($heroRating) ? '' : 'opacity-40' }}"></i>
                                @endfor
                                <span class="text-xs text-gray-300 ml-1">({{ number_format($heroRating, 1) }} / {{ number_format($heroReviews) }} reviews)</span>
                            </div>
                            <div class="flex items-center justify-center space-x-3 mt-2">
                                <span class="text-3xl font-black text-brand-gold">{{ $money($heroItem['price'] ?? 0) }}</span>
                                @if ((float) ($heroItem['oldPrice'] ?? 0) > (float) ($heroItem['price'] ?? 0))
                                <span class="text-base text-gray-400 line-through">{{ $money($heroItem['oldPrice']) }}</span>
                                @endif
                            </div>
                        </div>
                        <button type="button" onclick="addToCart({{ (int) $heroItem['id'] }})" class="w-full bg-brand-gold hover:bg-yellow-500 text-brand-navy font-bold py-3.5 rounded-xl transition shadow">
                            Add To Cart
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<section class="w-full px-2 sm:px-6 mt-8">
    <div class="w-full max-w-[1600px] mx-auto grid grid-cols-2 md:grid-cols-4 gap-4 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
        <div class="flex items-center space-x-4 p-3">
            <div class="w-12 h-12 rounded-xl bg-brand-lightgold/40 text-brand-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-truck-fast"></i></div>
            <div>
                <h4 class="font-bold text-sm">Express Shipping</h4>
                <p class="text-xs text-gray-500">Free over Rs 7,500</p>
            </div>
        </div>
        <div class="flex items-center space-x-4 p-3">
            <div class="w-12 h-12 rounded-xl bg-brand-lightgold/40 text-brand-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-rotate-left"></i></div>
            <div>
                <h4 class="font-bold text-sm">30-Day Guarantee</h4>
                <p class="text-xs text-gray-500">Hassle-free returns</p>
            </div>
        </div>
        <div class="flex items-center space-x-4 p-3">
            <div class="w-12 h-12 rounded-xl bg-brand-lightgold/40 text-brand-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-headset"></i></div>
            <div>
                <h4 class="font-bold text-sm">24/7 Support</h4>
                <p class="text-xs text-gray-500">Dedicated expert team</p>
            </div>
        </div>
        <div class="flex items-center space-x-4 p-3">
            <div class="w-12 h-12 rounded-xl bg-brand-lightgold/40 text-brand-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-award"></i></div>
            <div>
                <h4 class="font-bold text-sm">100% Original</h4>
                <p class="text-xs text-gray-500">Verified products</p>
            </div>
        </div>
    </div>
</section>

<section id="categories" class="w-full px-2 sm:px-6 mt-16">
    <div class="w-full max-w-[1600px] mx-auto">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy">Shop By Category</h2>
                <p class="text-sm text-gray-500">Explore our curated collections for head-to-toe radiance</p>
            </div>
            <a href="{{ route('collections') }}" class="text-xs font-bold text-brand-accent hover:underline flex items-center gap-1">View All <i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-6">
            @foreach ($categoryTiles as $index => $category)
                @php
                    $icon = $slugIcons[$category->slug] ?? $iconCycle[$index % count($iconCycle)];
                    $count = (int) ($categoryCounts[$category->slug] ?? 0);
                @endphp
                <a href="{{ route('collections', ['cat' => $category->slug]) }}" class="group flex flex-col items-center bg-white p-6 rounded-2xl shadow-sm hover:shadow-xl transition border border-gray-100 text-center">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-tr {{ $icon[1] }} flex items-center justify-center {{ $icon[2] }} text-2xl shadow-md group-hover:scale-105 transition duration-300 mb-4">
                        <i class="fa-solid {{ $icon[0] }}"></i>
                    </div>
                    <h3 class="font-bold text-sm text-gray-800 group-hover:text-brand-accent transition">{{ $category->name }}</h3>
                    <span class="text-xs text-gray-400 mt-1">{{ $count }} {{ \Illuminate\Support\Str::plural('Item', $count) }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<section id="flash-deals" class="w-full px-2 sm:px-6 mt-16">
    <div class="w-full max-w-[1600px] mx-auto">
        <div class="bg-gradient-to-r from-red-500 to-pink-600 rounded-3xl p-6 sm:p-10 text-white shadow-xl flex flex-col lg:flex-row items-center justify-between mb-8 gap-4">
            <div class="flex items-center space-x-4">
                <div class="bg-white/20 p-4 rounded-2xl backdrop-blur-sm">
                    <i class="fa-solid fa-bolt text-3xl text-brand-gold animate-bounce"></i>
                </div>
                <div>
                    <span class="bg-white text-red-600 text-xs font-extrabold px-3 py-1 rounded-full uppercase">Limited Time</span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold mt-1">Flash Deals of the Day</h2>
                    <p class="text-sm text-red-100">Grab top sellers before stocks run out!</p>
                </div>
            </div>
            <div class="flex items-center space-x-3 bg-black/30 backdrop-blur-md px-6 py-3.5 rounded-2xl border border-white/10 text-center">
                <div>
                    <span id="hours" class="text-2xl font-black">08</span>
                    <span class="block text-[10px] text-gray-300 uppercase">Hours</span>
                </div>
                <span class="text-2xl font-bold">:</span>
                <div>
                    <span id="minutes" class="text-2xl font-black">34</span>
                    <span class="block text-[10px] text-gray-300 uppercase">Mins</span>
                </div>
                <span class="text-2xl font-bold">:</span>
                <div>
                    <span id="seconds" class="text-2xl font-black">19</span>
                    <span class="block text-[10px] text-gray-300 uppercase">Secs</span>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6">
            @forelse ($flashProducts as $product)
                @include('partials.home.design-product-card', [
                    'product' => $product,
                    'variant' => 'compact',
                    'badge' => $offLabel($product) ?? ($product['badge'] ?? 'Deal'),
                    'badgeClass' => 'bg-red-500 text-white',
                    'money' => $money,
                    'imageUrl' => $imageUrl,
                ])
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-8 text-center text-sm text-gray-500">Flash deals will appear here when products are published.</div>
            @endforelse
        </div>
    </div>
</section>

<section id="trending" class="w-full px-2 sm:px-6 mt-16">
    <div class="w-full max-w-[1600px] mx-auto">
        <div class="flex items-center justify-between mb-8 gap-4">
            <div>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-navy">Trending Hair &amp; Scalp Solutions</h2>
                <p class="text-sm text-gray-500">Formulated with natural botanical extracts for visible restoration</p>
            </div>
            <div class="hidden sm:flex gap-2">
                <a href="#trending" class="px-4 py-2 bg-brand-navy text-white text-xs font-bold rounded-xl shadow">All</a>
                @foreach ($navCategories->take(2) as $navCategory)
                <a href="{{ route('collections', ['cat' => $navCategory->slug]) }}" class="px-4 py-2 bg-white text-gray-600 hover:bg-gray-100 text-xs font-bold rounded-xl border border-gray-200 transition">{{ $navCategory->name }}</a>
                @endforeach
            </div>
        </section>

        @include('partials.home.showcase')
        @include('partials.home.flash-deals')
        @include('partials.home.aesthetics-gallery')
        @include('partials.home.featured-promo')

        @include('partials.recently-viewed-section', ['recentlyVariant' => 'home', 'recentlyLimit' => 8])
        @include('partials.home.trending')
        @include('partials.home.customer-reviews')
        @include('partials.home.endorsements')

    </main>
@endsection

@push('scripts')
@include('partials.scripts.products-catalog')
<script>
    window.products = (typeof products !== 'undefined') ? products : [];
    let wishlist = [];

    function showToast(title, message) {
        const container = document.getElementById('toast-container');
        if (!container) return;
        const text = message ? title + ' — ' + message : String(title || '');
        const toast = document.createElement('div');
        toast.className = 'pointer-events-auto bg-[#0B132B] text-white px-6 py-3 rounded-xl shadow-2xl text-sm font-semibold border border-[#D4AF37]/40 flex items-center gap-3 transform translate-y-4 opacity-0 transition-all duration-300';
        toast.textContent = text;
        container.appendChild(toast);
        requestAnimationFrame(() => toast.classList.remove('translate-y-4', 'opacity-0'));
        setTimeout(() => {
            toast.classList.add('translate-y-4', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }

    function toggleCartDrawer() {
        const drawer = document.getElementById('cart-drawer');
        const backdrop = document.getElementById('cart-backdrop');
        const panel = document.getElementById('cart-panel');
        if (!drawer || !backdrop || !panel) return;
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            }, 10);
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.add('translate-x-full');
            setTimeout(() => drawer.classList.add('hidden'), 300);
        }
    }

    function toggleWishlistDrawer() {
        const drawer = document.getElementById('wishlist-drawer');
        const backdrop = document.getElementById('wishlist-backdrop');
        const panel = document.getElementById('wishlist-panel');
        if (!drawer || !backdrop || !panel) return;
        if (drawer.classList.contains('hidden')) {
            drawer.classList.remove('hidden');
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                panel.classList.remove('translate-x-full');
            }, 10);
        } else {
            backdrop.classList.add('opacity-0');
            panel.classList.add('translate-x-full');
            setTimeout(() => drawer.classList.add('hidden'), 300);
        }
    }

    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        if (menu) menu.classList.toggle('hidden');
    }

    function updateWishlistUI() {
        ['wishlist-badge', 'wishlist-badge-desktop', 'mobile-wishlist-badge'].forEach((id) => {
            const el = document.getElementById(id);
            if (el) el.textContent = String(wishlist.length);
        });
        const container = document.getElementById('wishlist-items');
        if (!container) return;
        const money = (amount) => window.BeautageMoney ? BeautageMoney.formatDecimal(amount) : ('Rs. ' + Number(amount || 0).toFixed(0));
        const esc = (value) => window.BeautageDom ? BeautageDom.escapeHtml(value) : String(value ?? '');
        if (wishlist.length === 0) {
            container.innerHTML = '<div class="text-center py-12 text-slate-400 text-xs">No saved items in your wishlist yet.</div>';
            return;
        }
        container.innerHTML = wishlist.map((item) => `
            <div class="flex items-center gap-4 p-3 rounded-2xl bg-gray-50 border border-gray-100">
                <img src="${esc(item.image)}" alt="" class="w-16 h-16 object-cover rounded-xl">
                <div class="flex-1 min-w-0">
                    <h4 class="text-xs font-bold text-[#0B132B] truncate">${esc(item.name)}</h4>
                    <span class="text-xs font-extrabold text-slate-800">${money(item.price)}</span>
                </div>
                <div class="flex flex-col gap-2">
                    <button type="button" onclick="addToCart(${Number(item.id)}); toggleWishlist(${Number(item.id)});" class="px-3 py-1.5 bg-[#0B132B] text-white rounded-lg text-[10px] font-bold">Move to Bag</button>
                    <button type="button" onclick="toggleWishlist(${Number(item.id)})" class="text-rose-500 text-[10px] font-semibold text-center">Remove</button>
                </div>
            </div>
        `).join('');
    }

    function toggleWishlist(productId) {
        const product = (window.products || []).find((item) => Number(item.id) === Number(productId));
        if (!product) return;
        const index = wishlist.findIndex((item) => Number(item.id) === Number(productId));
        if (index > -1) {
            wishlist.splice(index, 1);
            showToast('Wishlist', 'Removed "' + product.name + '" from your wishlist.');
        } else {
            wishlist.push(product);
            showToast('Wishlist', 'Saved "' + product.name + '" to your wishlist.');
        }
        document.querySelectorAll('[data-wishlist-for="' + productId + '"]').forEach((button) => {
            button.classList.toggle('text-red-500', index === -1);
        });
        updateWishlistUI();
    }

    function openQuickView(productId) {
        const product = (window.products || []).find((item) => Number(item.id) === Number(productId));
        const modal = document.getElementById('quickview-modal');
        const content = document.getElementById('quickview-content');
        if (!product || !modal || !content) return;
        const money = (amount) => window.BeautageMoney ? BeautageMoney.formatDecimal(amount) : ('Rs. ' + Number(amount || 0).toFixed(0));
        const esc = (value) => window.BeautageDom ? BeautageDom.escapeHtml(value) : String(value ?? '');
        const productUrl = product.slug ? '/products/' + encodeURIComponent(product.slug) : '/collections';
        content.innerHTML = `
            <div class="aspect-square rounded-2xl overflow-hidden bg-gray-50">
                <img src="${esc(product.image)}" alt="${esc(product.name)}" class="w-full h-full object-cover">
            </div>
            <div class="space-y-4">
                <span class="px-2.5 py-1 bg-[#F4E8C1] text-[#0B132B] text-[10px] font-bold rounded-full uppercase">${esc(product.category || '')}</span>
                <h2 class="text-xl font-bold text-[#0B132B]"><a href="${productUrl}">${esc(product.name)}</a></h2>
                <div class="flex items-center gap-3">
                    <span class="text-2xl font-black text-slate-800">${money(product.price)}</span>
                    <span class="text-sm text-slate-400 line-through">${money(product.oldPrice)}</span>
                </div>
                <p class="text-xs text-slate-600 leading-relaxed">${esc(product.desc || '')}</p>
                <button type="button" onclick="addToCart(${Number(product.id)}); closeQuickView();" class="w-full bg-[#D4AF37] hover:bg-yellow-500 text-[#0B132B] font-bold py-3 rounded-xl transition text-xs uppercase">Add To Bag</button>
            </div>
        `;
        modal.classList.remove('hidden');
    }

    function closeQuickView() {
        const modal = document.getElementById('quickview-modal');
        if (modal) modal.classList.add('hidden');
    }

    function setupSearch() {
        const input = document.getElementById('main-search-input');
        const mobileInput = document.getElementById('mobile-search-input');
        const boxes = [document.getElementById('search-results'), document.getElementById('mobile-search-results')].filter(Boolean);
        if (!boxes.length) return;
        const esc = (value) => window.BeautageDom ? BeautageDom.escapeHtml(value) : String(value ?? '');
        const money = (amount) => window.BeautageMoney ? BeautageMoney.formatDecimal(amount) : ('Rs. ' + Number(amount || 0).toFixed(0));

        function renderSearch(query) {
            const html = !query
                ? ''
                : (() => {
                    const matches = (window.products || []).filter((product) => {
                        const name = String(product.name || '').toLowerCase();
                        const desc = String(product.desc || '').toLowerCase();
                        return name.includes(query) || desc.includes(query);
                    }).slice(0, 6);
                    return matches.length
                        ? matches.map((product) => `
                            <button type="button" onclick="selectSearchResult(${Number(product.id)})" class="w-full flex items-center gap-3 p-2 rounded-xl hover:bg-gray-50 text-left">
                                <img src="${esc(product.image)}" alt="" class="w-10 h-10 object-cover rounded-lg">
                                <span>
                                    <span class="block text-xs font-bold text-[#0B132B]">${esc(product.name)}</span>
                                    <span class="block text-[10px] text-slate-500">${money(product.price)}</span>
                                </span>
                            </button>
                        `).join('')
                        : '<div class="p-3 text-xs text-slate-500 text-center">No products found.</div>';
                })();
            boxes.forEach((box) => {
                if (!query) {
                    box.classList.add('hidden');
                    box.innerHTML = '';
                    return;
                }
                box.innerHTML = html;
                box.classList.remove('hidden');
            });
        }

        if (input) input.addEventListener('input', (event) => renderSearch(event.target.value.toLowerCase().trim()));
        if (mobileInput) mobileInput.addEventListener('input', (event) => renderSearch(event.target.value.toLowerCase().trim()));
        document.addEventListener('click', (event) => {
            const target = event.target;
            if (boxes.some((box) => box.contains(target))) return;
            if (input && input.contains(target)) return;
            if (mobileInput && mobileInput.contains(target)) return;
            boxes.forEach((box) => box.classList.add('hidden'));
        });
    }

    function selectSearchResult(id) {
        ['search-results', 'mobile-search-results'].forEach((id) => {
            const box = document.getElementById(id);
            if (box) box.classList.add('hidden');
        });
        openQuickView(id);
    }

    function startCountdownTimer() {
        let totalSeconds = 8 * 3600 + 34 * 60 + 19;
        setInterval(() => {
            if (totalSeconds > 0) totalSeconds--;
            const hours = document.getElementById('hours');
            const minutes = document.getElementById('minutes');
            const seconds = document.getElementById('seconds');
            if (!hours || !minutes || !seconds) return;
            hours.textContent = String(Math.floor(totalSeconds / 3600)).padStart(2, '0');
            minutes.textContent = String(Math.floor((totalSeconds % 3600) / 60)).padStart(2, '0');
            seconds.textContent = String(totalSeconds % 60).padStart(2, '0');
        }, 1000);
    }

    let lastScrollTop = 0;
    const mobileBottomNav = document.getElementById('mobile-bottom-nav');
    window.addEventListener('scroll', function () {
        if (!mobileBottomNav) return;
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        if (scrollTop > lastScrollTop && scrollTop > 100) {
            mobileBottomNav.style.transform = 'translateY(100%)';
        } else {
            mobileBottomNav.style.transform = 'translateY(0)';
        }
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, false);

    document.addEventListener('DOMContentLoaded', () => {
        window.products = (typeof products !== 'undefined') ? products : [];
        window.addToCart = function (productId) {
            const added = window.BeautageCart && BeautageCart.addProductById(productId);
            if (!added) return;
            const product = window.products.find((item) => Number(item.id) === Number(productId));
            showToast('Added to Bag', product ? (product.name + ' added successfully!') : 'Item added to cart successfully!');
        };
        if (window.BeautageCart) BeautageCart.updateCartUI();
        updateWishlistUI();
        setupSearch();
        startCountdownTimer();
        if (window.lucide) lucide.createIcons();
    });
</script>
@endpush
