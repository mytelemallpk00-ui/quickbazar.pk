<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-clip max-w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        use App\Support\StorefrontAssets;
        $storefrontCss = StorefrontAssets::compiledStylesheet();
        $storefrontCssVersion = '2026110809';
        $supportEmail = \App\Support\SiteSettings::get('support_email', 'support@quickbazar.pk');
        if (! is_string($supportEmail) || $supportEmail === '' || str_contains(strtolower($supportEmail), 'beautage')) {
            $supportEmail = 'support@quickbazar.pk';
        }
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
        $supportPhone = \App\Support\SiteSettings::get('support_phone', '+92 300 5550199');
        if (! is_string($supportPhone) || $supportPhone === '' || str_contains(strtolower($supportPhone), 'beautage')) {
            $supportPhone = '+92 300 5550199';
        }
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
    <link rel="stylesheet" href="{{ asset('css/qb-header.css') }}?v={{ $storefrontCssVersion }}">
    <link rel="stylesheet" href="{{ asset('css/qb-footer.css') }}?v={{ $storefrontCssVersion }}">
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

    <div id="site-header-sticky" class="sticky top-0 z-40 w-full bg-white shadow-sm">
    <div class="qb-topbar">
        <div class="flex w-full items-center justify-between gap-3 px-4 py-2 sm:px-6 lg:px-8">
            <div class="flex min-w-0 items-center gap-3 sm:gap-4">
                <a href="tel:{{ $supportPhoneTel }}" class="inline-flex items-center gap-1.5 font-semibold">
                    <i data-lucide="phone" class="h-3.5 w-3.5 text-orange-400"></i>
                    <span class="truncate">{{ $supportPhone }}</span>
                </a>
                <a href="mailto:{{ $supportEmail }}" class="hidden items-center gap-1.5 font-semibold sm:inline-flex">
                    <i data-lucide="mail" class="h-3.5 w-3.5 text-orange-400"></i>
                    {{ $supportEmail }}
                </a>
            </div>
            <p class="qb-hot hidden md:inline-flex">
                <span class="qb-hot-badge">HOT</span>
                Free nationwide delivery on orders over Rs. 2,500
            </p>
            <div class="flex items-center gap-3">
                <a href="{{ route('track-order') }}" class="inline-flex items-center gap-1 font-semibold">
                    <i data-lucide="map-pin" class="h-3.5 w-3.5 text-orange-400"></i>
                    <span class="hidden sm:inline">Track order</span>
                </a>
                <span class="hidden font-semibold lg:inline">PKR (Rs)</span>
                <span class="hidden font-semibold lg:inline">ENG</span>
            </div>
        </div>
    </div>

    <header class="site-header-bar w-full border-b border-slate-100 bg-white">
        <div class="w-full px-4 py-3 sm:px-6 lg:px-8">
            <div class="flex items-center gap-3">
                <button onclick="toggleMobileNav()" class="flex h-11 w-11 shrink-0 items-center justify-center rounded-lg text-slate-800 hover:bg-slate-100 lg:hidden" aria-label="Open Menu">
                    <i data-lucide="menu" class="h-5 w-5"></i>
                </button>
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-2.5">
                    <span class="qb-logo-mark" aria-hidden="true">
                        <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                    </span>
                    <span class="min-w-0">
                        <span class="qb-wordmark block text-lg sm:text-xl">QuickBazar<span>.pk</span></span>
                        <span class="qb-kicker hidden sm:block">Express shopping</span>
                    </span>
                </a>

                <div class="relative hidden min-w-0 flex-1 lg:block">
                    <div class="qb-search">
                        <select id="search-category" aria-label="Search category">
                            <option value="all">All categories</option>
                            @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $searchCategory)
                            <option value="{{ $searchCategory->slug }}">{{ $searchCategory->displayNavLabel() }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="main-search-input" placeholder="Search for products, brands, or categories" autocomplete="off">
                        <button id="clear-search" class="hidden px-2 text-slate-400" type="button" aria-label="Clear search">
                            <i data-lucide="x" class="h-4 w-4"></i>
                        </button>
                        <button class="qb-search-go" type="button" aria-label="Search">
                            <i data-lucide="search" class="h-4 w-4"></i>
                        </button>
                    </div>
                    <div id="search-results" class="absolute left-0 right-0 top-full z-50 mt-2 hidden overflow-hidden rounded-xl border border-slate-200 bg-white p-2 shadow-xl"></div>
                </div>

                <div class="ml-auto flex items-center gap-1 sm:gap-2">
                    @auth
                        <a href="{{ route('account.index') }}" class="hidden items-center gap-2 rounded-lg px-2 py-1.5 text-left hover:bg-slate-50 lg:flex">
                            <i data-lucide="user" class="h-5 w-5 text-slate-700"></i>
                            <span>
                                <span class="block text-[11px] text-slate-500">Hello, {{ \Illuminate\Support\Str::before(auth()->user()->name, ' ') }}</span>
                                <span class="block text-xs font-extrabold text-slate-900">Account &amp; orders</span>
                            </span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden items-center gap-2 rounded-lg px-2 py-1.5 text-left hover:bg-slate-50 lg:flex">
                            <i data-lucide="user" class="h-5 w-5 text-slate-700"></i>
                            <span>
                                <span class="block text-[11px] text-slate-500">Hello, sign in</span>
                                <span class="block text-xs font-extrabold text-slate-900">Account &amp; orders</span>
                            </span>
                        </a>
                    @endauth
                    <button onclick="toggleWishlistDrawer()" class="relative hidden h-11 items-center gap-1.5 rounded-lg px-2 text-sm font-bold text-slate-800 hover:bg-slate-50 sm:flex" aria-label="Wishlist">
                        <i data-lucide="heart" class="h-5 w-5"></i>
                        <span class="hidden xl:inline">Wishlist</span>
                        <span id="wishlist-badge-desktop" class="flex h-5 min-w-5 items-center justify-center rounded-full bg-orange-500 px-1 text-[10px] font-extrabold text-white">0</span>
                    </button>
                    <button onclick="toggleWishlistDrawer()" class="relative flex h-11 w-11 items-center justify-center rounded-lg text-slate-800 sm:hidden" aria-label="Wishlist">
                        <i data-lucide="heart" class="h-5 w-5"></i>
                        <span id="wishlist-badge" class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[9px] font-bold text-white">0</span>
                    </button>
                    <button onclick="toggleCartDrawer()" class="relative flex h-11 items-center gap-2 rounded-lg px-2 text-slate-900 hover:bg-slate-50" aria-label="Shopping Bag">
                        <span class="relative">
                            <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                            <span id="cart-badge" class="absolute -right-2 -top-2 flex h-4 w-4 items-center justify-center rounded-full bg-orange-500 text-[9px] font-extrabold text-white sm:hidden">0</span>
                            <span id="cart-badge-desktop" class="absolute -right-2 -top-2 hidden h-4 min-w-4 items-center justify-center rounded-full bg-orange-500 px-1 text-[9px] font-extrabold text-white sm:flex">0</span>
                        </span>
                        <span class="hidden text-left sm:block">
                            <span class="block text-[11px] text-slate-500">Cart</span>
                            <span id="cart-total-header" class="block text-sm font-extrabold leading-tight">Rs. 0</span>
                        </span>
                    </button>
                </div>
            </div>

            <div id="mobile-search-container" class="relative mt-3 lg:hidden">
                <div class="qb-search">
                    <input type="text" id="mobile-search-input" placeholder="Search products, brands, categories" autocomplete="off">
                    <button class="qb-search-go" type="button" aria-label="Search">
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
                <a href="{{ route('home') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-orange-50 hover:text-orange-700"><i data-lucide="home" class="h-4 w-4 text-orange-500"></i> Home</a>
                <a href="{{ route('collections') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-orange-50 hover:text-orange-700"><i data-lucide="layout-grid" class="h-4 w-4 text-orange-500"></i> Shop all</a>
                @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $drawerCategory)
                    <a href="{{ $drawerCategory->collectionsUrl() }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 hover:bg-orange-50 hover:text-orange-700">
                        <i data-lucide="{{ $drawerCategory->iconName() }}" class="h-4 w-4 text-orange-500"></i>
                        {{ $drawerCategory->displayNavLabel() }}
                    </a>
                @endforeach
                <a href="{{ route('exclusive-offers') }}" onclick="toggleMobileNav()" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-red-600 hover:bg-red-50"><i data-lucide="zap" class="h-4 w-4"></i> Flash deals</a>
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

@yield('content')

@yield('below_main')

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
