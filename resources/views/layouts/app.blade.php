<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-clip max-w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        use App\Support\StorefrontAssets;
        $storefrontCss = StorefrontAssets::compiledStylesheet();
        $storefrontCssVersion = '2026110804';
        $siteLogoUrl = \App\Support\SiteSettings::logoUrl();
        $siteFaviconUrl = \App\Support\SiteSettings::faviconUrl();
        $footerLogoUrl = \App\Support\SiteSettings::footerLogoUrl();
        $siteTagline = \App\Support\SiteSettings::get('site_tagline', 'Elevate Your Natural Elegance');
        $marqueeItems = \App\Support\SiteSettings::get('marquee_items', []);
        if (! is_array($marqueeItems) || $marqueeItems === []) {
            $marqueeItems = [
                ['text' => 'Free Express Worldwide Shipping Over Rs 7,500'],
                ['text' => 'Luxury Flash Sale: Up to 50% OFF Selected Flagship Items'],
                ['text' => 'Authorized Brand Store • 100% Original Verified Products'],
                ['text' => 'Cash on Delivery Available Across Pakistan'],
            ];
        }
        $supportPhone = \App\Support\SiteSettings::get('support_phone', '+1 (800) BEAUTAGE');
        $supportPhoneTel = preg_replace('/[^\d+]/', '', (string) $supportPhone) ?: '+18008886678';
        $footerSettings = \App\Support\FooterSettings::forStorefront();
    @endphp
    <title>@yield('title', \App\Support\SiteSettings::get('default_meta_title', 'Quick Bazar'))</title>
    @stack('structured-data')
    @include('partials.seo-head', ['seo' => $pageSeo ?? null])
    @include('partials.site-analytics-head')
    <link rel="icon" href="{{ $siteFaviconUrl }}" sizes="any">
    <link rel="apple-touch-icon" href="{{ $siteLogoUrl }}">
    @stack('head-preload')
    @if ($storefrontCss)
        <link rel="preload" as="style" href="{{ $storefrontCss }}?v={{ $storefrontCssVersion }}">
        <link rel="stylesheet" href="{{ $storefrontCss }}?v={{ $storefrontCssVersion }}">
    @else
        <link rel="preconnect" href="https://cdn.tailwindcss.com" crossorigin>
        <script src="https://cdn.tailwindcss.com"></script>
        <script src="{{ asset('js/tailwind-config.js') }}?v={{ $storefrontCssVersion }}"></script>
    @endif
    <link rel="stylesheet" href="{{ asset('css/beautage.css') }}?v={{ $storefrontCssVersion }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@600;700&display=swap" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=Playfair+Display:wght@600;700&display=swap"></noscript>
    <link rel="preconnect" href="https://unpkg.com" crossorigin>
    <script src="https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
    @include('partials.theme-brand-vars')
    @stack('styles')
</head>
<body @yield('body_attrs')>
<!-- Toast Notification Container -->
    <div id="toast-container" class="fixed bottom-28 md:bottom-6 right-4 sm:right-6 z-50 flex flex-col gap-2 pointer-events-none max-w-[90vw] sm:max-w-md"></div>

    <div class="relative z-50 w-full overflow-hidden bg-emerald-800 text-[11px] font-semibold text-white sm:text-xs">
        <div class="flex w-max items-center whitespace-nowrap marquee-track animate-marquee">
            @foreach([false, true] as $marqueeDuplicate)
            <div class="flex shrink-0 items-center gap-8 px-6 sm:gap-12" @if($marqueeDuplicate) aria-hidden="true" @endif>
                @foreach($marqueeItems as $index => $item)
                    @if(filled($item['text'] ?? null))
                        @if($index > 0)<span class="text-emerald-200">•</span>@endif
                        <span class="flex items-center gap-2">{{ $item['text'] }}</span>
                    @endif
                @endforeach
                @if(count($marqueeItems) > 0)<span class="text-emerald-200">•</span>@endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- 2. PROFESSIONAL MAIN HEADER (category strip sits outside header so mobile dropdowns aren't clipped) -->
    <div id="site-header-sticky" class="sticky top-0 z-40 w-full border-b border-slate-200 bg-white">
    <header class="glass-nav site-header-bar w-full">
        <div class="mx-auto w-full max-w-7xl px-4 py-2.5 sm:px-8">
            <div class="flex items-center justify-between gap-2 lg:hidden">
                <div class="flex items-center gap-1">
                    <button onclick="toggleMobileNav()" class="flex h-11 w-11 items-center justify-center rounded-full text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800" aria-label="Open Menu">
                        <i data-lucide="menu" class="h-5 w-5"></i>
                    </button>
                    <button onclick="toggleMobileSearch()" class="flex h-11 w-11 items-center justify-center rounded-full text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800" aria-label="Toggle Search">
                        <i data-lucide="search" class="h-5 w-5"></i>
                    </button>
                </div>

                <a href="{{ route('home') }}" class="flex items-center justify-center focus:outline-none">
                    <img src="{{ $siteLogoUrl }}" alt="QuickBazar" width="140" height="56" class="h-9 w-auto object-contain" decoding="async" onerror="this.onerror=null; this.src='https://placehold.co/140x50/047857/FFFFFF?text=QuickBazar';">
                </a>

                <div class="flex items-center gap-1">
                    <button onclick="toggleWishlistDrawer()" class="relative flex h-11 w-11 items-center justify-center rounded-full text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800" title="Wishlist" aria-label="Wishlist">
                        <i data-lucide="heart" class="h-5 w-5"></i>
                        <span id="wishlist-badge" class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[9px] font-bold text-white">0</span>
                    </button>
                    <button onclick="toggleCartDrawer()" class="relative flex h-11 w-11 items-center justify-center rounded-full bg-emerald-600 text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700" title="Shopping Bag" aria-label="Shopping Bag">
                        <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                        <span id="cart-badge" class="absolute -right-0.5 -top-0.5 flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 text-[9px] font-extrabold text-white">0</span>
                    </button>
                </div>
            </div>

            <div class="hidden items-center gap-6 lg:grid lg:grid-cols-12">
                <div class="col-span-2 flex items-center">
                    <a href="{{ route('home') }}" class="focus:outline-none">
                        <img src="{{ $siteLogoUrl }}" alt="QuickBazar" class="h-11 w-auto object-contain" onerror="this.onerror=null; this.src='https://placehold.co/180x48/047857/FFFFFF?text=QuickBazar';">
                    </a>
                </div>

                <div class="relative col-span-6 w-full">
                    <div class="relative flex items-center overflow-hidden rounded-full border border-slate-200 bg-slate-50 transition focus-within:border-emerald-500 focus-within:bg-white focus-within:ring-4 focus-within:ring-emerald-100">
                        <select id="search-category" class="h-full cursor-pointer border-r border-slate-200 bg-transparent py-3 pl-4 pr-2 text-xs font-bold text-slate-700 focus:outline-none">
                            <option value="all">All</option>
                            @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $searchCategory)
                            <option value="{{ $searchCategory->slug }}">{{ $searchCategory->displayNavLabel() }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="main-search-input" placeholder="Search products, brands, categories..."
                               class="w-full bg-transparent px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none">
                        <button id="clear-search" class="mr-1 hidden text-slate-400 hover:text-slate-600" type="button">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                        <button class="m-1 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 text-white transition hover:bg-emerald-700" type="button" aria-label="Search">
                            <i data-lucide="search" class="h-4 w-4"></i>
                        </button>
                    </div>
                    <div id="search-results" class="absolute left-0 right-0 top-full z-50 mt-2 hidden overflow-hidden rounded-2xl border border-slate-200 bg-white p-2 shadow-xl"></div>
                </div>

                <div class="col-span-4 flex items-center justify-end gap-1">
                    <a href="{{ route('track-order') }}" class="hidden items-center gap-1.5 rounded-full px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-800 xl:flex">
                        <i data-lucide="package-search" class="h-4 w-4 text-emerald-600"></i>
                        Track
                    </a>
                    <a href="{{ route('help') }}" class="hidden items-center gap-1.5 rounded-full px-3 py-2 text-xs font-bold text-slate-600 transition hover:bg-emerald-50 hover:text-emerald-800 xl:flex">
                        <i data-lucide="life-buoy" class="h-4 w-4 text-emerald-600"></i>
                        Help
                    </a>
                    @auth
                        <a href="{{ route('account.index') }}" class="flex h-11 w-11 items-center justify-center rounded-full text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800" title="My account">
                            <i data-lucide="user" class="h-5 w-5"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="flex items-center gap-2 rounded-full px-3 py-2 text-xs font-bold text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800">
                            <i data-lucide="user" class="h-4 w-4"></i> Sign in
                        </a>
                    @endauth
                    <button onclick="toggleWishlistDrawer()" class="relative flex h-11 w-11 items-center justify-center rounded-full text-slate-800 transition hover:bg-emerald-50 hover:text-emerald-800" title="Wishlist">
                        <i data-lucide="heart" class="h-5 w-5"></i>
                        <span id="wishlist-badge-desktop" class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[10px] font-bold text-white">0</span>
                    </button>
                    <button onclick="toggleCartDrawer()" class="relative ml-1 flex items-center gap-2.5 rounded-full bg-emerald-600 py-2 pl-3 pr-4 text-white shadow-md shadow-emerald-600/25 transition hover:bg-emerald-700">
                        <span class="relative">
                            <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                            <span id="cart-badge-desktop" class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 text-[10px] font-extrabold text-white">0</span>
                        </span>
                        <span class="text-left">
                            <span class="block text-[9px] font-bold uppercase leading-none text-emerald-100">Bag</span>
                            <span id="cart-total-header" class="mt-0.5 block text-xs font-black leading-tight">Rs. 0</span>
                        </span>
                    </button>
                </div>
            </div>

            <div id="mobile-search-container" class="mt-3 hidden border-t border-slate-100 pt-3 lg:hidden">
                <div class="flex items-center overflow-hidden rounded-full border border-slate-200 bg-slate-50">
                    <input type="text" id="mobile-search-input" placeholder="Search products..."
                           class="w-full bg-transparent px-4 py-3 text-sm text-slate-800 placeholder-slate-400 focus:outline-none">
                    <button class="m-1 flex h-9 w-9 items-center justify-center rounded-full bg-emerald-600 text-white" type="button" aria-label="Search">
                        <i data-lucide="search" class="h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>

    @include('partials.storefront-header-nav', ['storefrontNav' => $storefrontNav ?? []])
    </div>

    <!-- Mobile Navigation Drawer -->
    <button type="button" id="mobile-nav-overlay" onclick="toggleMobileNav()" aria-label="Close menu" class="pointer-events-none fixed inset-0 z-50 cursor-default border-0 bg-slate-900/40 p-0 opacity-0 backdrop-blur-sm transition-opacity duration-300 lg:hidden"></button>
    <div id="mobile-nav-drawer" class="fixed left-0 top-0 z-50 flex h-full w-[min(100%,20rem)] -translate-x-full flex-col justify-between border-r border-slate-200 bg-white p-5 shadow-2xl transition-transform duration-300 ease-in-out lg:hidden">
        <div class="min-h-0 overflow-y-auto">
            <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                <img src="{{ $siteLogoUrl }}" alt="QuickBazar" class="h-9 w-auto" onerror="this.onerror=null; this.src='https://placehold.co/120x40/047857/FFFFFF?text=QuickBazar';">
                <button type="button" onclick="toggleMobileNav()" class="flex h-11 w-11 items-center justify-center rounded-full text-slate-500 hover:bg-slate-100 hover:text-slate-800" aria-label="Close menu">
                    <i data-lucide="x" class="h-5 w-5" aria-hidden="true"></i>
                </button>
            </div>
            <nav class="space-y-1 py-4 text-sm font-semibold text-slate-700" aria-label="Mobile">
                <a href="{{ route('home') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="home" class="h-4 w-4 text-emerald-600"></i> Home</a>
                <a href="{{ route('collections') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="layout-grid" class="h-4 w-4 text-emerald-600"></i> Shop all</a>
                @foreach(($storefrontNav['mainNavItems'] ?? []) as $drawerItem)
                    @if(filled($drawerItem['url'] ?? null) && filled($drawerItem['label'] ?? null))
                    <a href="{{ $drawerItem['url'] }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800">
                        <i data-lucide="{{ $drawerItem['icon'] ?? 'chevron-right' }}" class="h-4 w-4 text-emerald-600"></i>
                        {{ $drawerItem['label'] }}
                    </a>
                    @endif
                @endforeach
                <a href="{{ route('track-order') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="package-search" class="h-4 w-4 text-emerald-600"></i> Track order</a>
                <a href="{{ route('help') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="life-buoy" class="h-4 w-4 text-emerald-600"></i> Help</a>
                <a href="{{ route('blog.index') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="newspaper" class="h-4 w-4 text-emerald-600"></i> Journal</a>
                @auth
                    <a href="{{ route('account.index') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="user" class="h-4 w-4 text-emerald-600"></i> Account</a>
                @else
                    <a href="{{ route('login') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-emerald-50 hover:text-emerald-800"><i data-lucide="user" class="h-4 w-4 text-emerald-600"></i> Sign in</a>
                @endauth
            </nav>
        </div>
        <div class="border-t border-slate-200 pt-4">
            <span class="text-xs text-slate-500">Support</span>
            <a href="tel:{{ $supportPhoneTel }}" class="mt-0.5 block text-sm font-bold text-emerald-800">{{ $supportPhone }}</a>
        </div>
    </div>
    @endunless

    <!-- MAIN HELP CENTER & FAQ CONTENT -->

@yield('content')

@yield('below_main')

@unless ($__env->hasSection('lumiere_homepage'))
@include('partials.storefront-footer', ['footerSettings' => $footerSettings])
@include('partials.storefront-bottom-nav')

<!-- CART DRAWER -->
    <div id="cart-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="cart-backdrop" onclick="toggleCartDrawer()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="cart-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-slate-200 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-emerald-600"></i> Your Shopping Bag
                    </h2>
                    <button onclick="toggleCartDrawer()" class="text-slate-400 hover:text-slate-900 p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="cart-items" class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-1"></div>
            </div>

            <div class="border-t border-slate-200 pt-4 space-y-3">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="text-slate-900 font-semibold">Rs. 0</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Estimated Tax (8%)</span>
                    <span id="cart-tax" class="text-slate-900 font-semibold">Rs. 0</span>
                </div>
                <div class="flex justify-between text-base text-slate-900 font-bold pt-2 border-t border-slate-200">
                    <span>Total</span>
                    <span id="cart-total" class="text-emerald-600">Rs. 0</span>
                </div>
                <a href="{{ route('checkout') }}" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Proceed to Checkout
                </a>
            </div>
        </div>
    </div>

    <!-- WISHLIST DRAWER -->
    <div id="wishlist-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="wishlist-backdrop" onclick="toggleWishlistDrawer()" class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="wishlist-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-slate-200 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-slate-200 pb-4">
                    <h2 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                        <i data-lucide="heart" class="w-5 h-5 text-emerald-600"></i> Your Saved Wishlist
                    </h2>
                    <button onclick="toggleWishlistDrawer()" class="text-slate-400 hover:text-slate-900 p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="wishlist-items" class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-1"></div>
            </div>
        </div>
    </div>

    <!-- QUICK VIEW MODAL -->
    <div id="quickview-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-md hidden">
        <div class="bg-white border border-slate-200 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative shadow-2xl">
            <button type="button" onclick="closeQuickView()" aria-label="Close quick view" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 min-h-[44px] min-w-[44px] flex items-center justify-center"><i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i></button>
            <div id="quickview-content" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center"></div>
        </div>
    </div>

<script src="{{ asset('js/beautage-money.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-dom.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-cart.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-recently-viewed.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-header-nav.js') }}?v=2026110804" defer></script>
<script src="{{ asset('js/beautage-chrome.js') }}?v=2026110804" defer></script>
@php
    $liveChatEnabled = \App\Support\LiveChatSettings::isEnabled();
@endphp
@if ($liveChatEnabled)
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
@endif
<script src="{{ asset('js/beautage-newsletter.js') }}?v=2026103020" defer></script>
<script src="{{ asset('js/beautage-boot.js') }}?v=2026102920" defer></script>
@include('partials.live-chat-widget')
@include('partials.site-analytics')
@stack('scripts')
</body>
</html>
