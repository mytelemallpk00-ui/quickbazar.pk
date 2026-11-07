<!DOCTYPE html>
<html lang="en" class="scroll-smooth overflow-x-clip max-w-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @php
        use App\Support\StorefrontAssets;
        $storefrontCss = StorefrontAssets::compiledStylesheet();
        $storefrontCssVersion = '2026110718';
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
    <title>@yield('title', 'Beautage — Elevate Your Natural Elegance')</title>
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

    @unless ($__env->hasSection('lumiere_homepage'))
    <!-- SMOOTH SLIDING MARQUEE TOP BAR -->
    <div class="bg-gradient-to-r from-beautage-pink via-beautage-purple to-beautage-purpleDark text-white text-[11px] sm:text-xs font-semibold py-2.5 px-0 w-full overflow-hidden shadow-sm relative z-50 border-b border-pink-400/30">
        <div class="flex w-max marquee-track animate-marquee whitespace-nowrap items-center">
            @foreach([false, true] as $marqueeDuplicate)
            <div class="flex items-center gap-8 sm:gap-12 px-6 shrink-0" @if($marqueeDuplicate) aria-hidden="true" @endif>
                @foreach($marqueeItems as $index => $item)
                    @if(filled($item['text'] ?? null))
                        @if($index > 0)<span class="text-pink-300">•</span>@endif
                        <span class="flex items-center gap-2">{{ $item['text'] }}</span>
                    @endif
                @endforeach
                @if(count($marqueeItems) > 0)<span class="text-pink-300">•</span>@endif
            </div>
            @endforeach
        </div>
    </div>

    <!-- 1. TOP UTILITY ANNOUNCEMENT BAR -->
    <div class="bg-gradient-to-r from-beautage-purpleDark via-beautage-purple to-beautage-purpleDark text-xs py-2 px-4 sm:px-12 xl:px-20 w-full text-purple-100 border-b border-purple-800">
        <div class="w-full flex flex-col sm:flex-row justify-between items-center gap-1 sm:gap-2 text-center sm:text-left">
            <div class="flex items-center justify-center sm:justify-start gap-2 flex-wrap">
                <span class="bg-beautage-pink/30 text-pink-200 border border-pink-400/40 px-2 py-0.5 rounded-full text-[9px] uppercase font-extrabold tracking-wider">FLAGSHIP EXCLUSIVE</span>
                <span class="flex items-center gap-1 font-medium text-[11px] sm:text-xs">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-beautage-amber shrink-0"></i>
                    <span>Flash Deals Sale Ends in <span id="announcement-timer" class="font-mono font-bold text-amber-300 ml-1">04:32:19</span></span>
                </span>
            </div>
            <div class="hidden md:flex items-center gap-6 text-purple-200 text-xs font-semibold">
                <span class="flex items-center gap-1.5"><i data-lucide="headphones" class="w-3.5 h-3.5 text-pink-300"></i> Care Hotline: <a href="tel:{{ $supportPhoneTel }}" class="text-white hover:text-pink-300 transition">{{ $supportPhone }}</a></span>
                <span class="flex items-center gap-1.5"><i data-lucide="truck" class="w-3.5 h-3.5 text-emerald-300"></i> Free Express Worldwide Shipping over Rs 7,500</span>
                <div class="relative group cursor-pointer">
                    <span class="hover:text-white transition flex items-center gap-1">PKR (Rs) <i data-lucide="chevron-down" class="w-3 h-3"></i></span>
                    <div class="absolute right-0 top-full mt-1 w-24 bg-white border border-purple-100 rounded-lg shadow-xl py-1 hidden group-hover:block z-50 text-slate-700">
                        <span class="block px-3 py-1 text-xs text-beautage-purple font-semibold" aria-current="true">PKR (Rs)</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. PROFESSIONAL MAIN HEADER (category strip sits outside header so mobile dropdowns aren't clipped) -->
    <div id="site-header-sticky" class="sticky top-0 z-40 w-full bg-white/98 shadow-sm">
    <header class="glass-nav site-header-bar border-b border-purple-100/80 w-full lg:border-b-0">
        <div class="w-full px-4 sm:px-12 xl:px-20 py-3 sm:py-4">
            
            <!-- MOBILE TOP ROW -->
            <div class="flex lg:hidden items-center justify-between gap-2">
                <div class="flex items-center gap-1.5">
                    <button onclick="toggleMobileNav()" class="p-2.5 rounded-xl bg-purple-50 text-beautage-purple hover:bg-purple-100 transition active:scale-95 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Open Menu">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <button onclick="toggleMobileSearch()" class="p-2.5 rounded-xl bg-purple-50 text-beautage-purple hover:bg-purple-100 transition active:scale-95 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Toggle Search">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </button>
                </div>

                <a href="{{ route('home') }}" class="flex items-center justify-center focus:outline-none py-0.5">
                    <img src="{{ $siteLogoUrl }}" alt="Beautage Flagship Logo" width="140" height="56" class="h-10 sm:h-14 w-auto object-contain drop-shadow-sm" decoding="async" onerror="this.onerror=null; this.src='https://placehold.co/140x50/5E1F70/FFFFFF?text=Beautage';">
                </a>

                <div class="flex items-center gap-1.5">
                    <button onclick="toggleWishlistDrawer()" class="relative p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple transition active:scale-95 min-h-[44px] min-w-[44px] flex items-center justify-center" title="Wishlist" aria-label="Wishlist">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                        <span id="wishlist-badge" class="absolute -top-1 -right-1 bg-beautage-pink text-white text-[9px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                    </button>

                    <button onclick="toggleCartDrawer()" class="relative p-2.5 rounded-xl bg-beautage-pink hover:bg-beautage-pinkHover text-white transition active:scale-95 shadow-md shadow-pink-500/20 min-h-[44px] min-w-[44px] flex items-center justify-center" title="Shopping Bag" aria-label="Shopping Bag">
                        <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                        <span id="cart-badge" class="absolute -top-1 -right-1 bg-beautage-purple text-white text-[9px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                    </button>
                </div>
            </div>

            <!-- DESKTOP 3-COLUMN HEADER LAYOUT -->
            <div class="hidden lg:grid grid-cols-12 items-center gap-6">
                
                <div class="col-span-3 flex items-center gap-3">
                    <a href="{{ route('home') }}" class="focus:outline-none py-1">
                        <img src="{{ $siteLogoUrl }}" alt="Beautage Flagship Logo" class="h-28 w-auto object-contain drop-shadow-md" onerror="this.onerror=null; this.src='https://placehold.co/240x90/5E1F70/FFFFFF?text=Beautage';">
                    </a>
                </div>

                <div class="col-span-6 relative w-full">
                    <div class="relative flex items-center border-2 border-purple-200/90 rounded-2xl overflow-hidden bg-white focus-within:border-beautage-purple shadow-sm transition">
                        <select id="search-category" class="h-full py-3 pl-3.5 pr-3 bg-purple-50/90 text-xs font-bold text-beautage-purple border-r border-purple-100 focus:outline-none cursor-pointer">
                            <option value="all">All Departments</option>
                            @foreach(\App\Models\Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get() as $searchCategory)
                            <option value="{{ $searchCategory->slug }}">{{ $searchCategory->displayNavLabel() }}</option>
                            @endforeach
                        </select>
                        <input type="text" id="main-search-input" placeholder="Search LED masks, organic serums, luxury perfumes..." 
                               class="w-full py-3 px-4 text-xs text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none font-medium">
                        <button id="clear-search" class="hidden text-slate-400 hover:text-slate-600 mr-2">
                            <i data-lucide="x" class="w-4 h-4"></i>
                        </button>
                        <button class="bg-beautage-purple hover:bg-beautage-purpleDark text-white px-6 py-3 flex items-center justify-center transition">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <div id="search-results" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-purple-100 rounded-2xl shadow-2xl overflow-hidden z-50 p-2"></div>
                </div>

                <div class="col-span-3 flex items-center justify-end gap-2 xl:gap-3 flex-wrap">
                    @auth
                        <a href="{{ route('account.index') }}" class="p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple transition shadow-sm" title="My account">
                            <i data-lucide="user" class="w-5 h-5"></i>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="hidden sm:flex items-center gap-2 px-3 py-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple text-xs font-bold transition">
                            <i data-lucide="user" class="w-4 h-4"></i> Sign in
                        </a>
                    @endauth
                    <button onclick="toggleWishlistDrawer()" class="relative p-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple transition shadow-sm" title="Wishlist">
                        <i data-lucide="heart" class="w-5 h-5"></i>
                        <span id="wishlist-badge-desktop" class="absolute -top-1 -right-1 bg-beautage-pink text-white text-[10px] font-bold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                    </button>

                    <button onclick="toggleCartDrawer()" class="relative flex items-center gap-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white px-4 py-2.5 rounded-xl font-medium text-sm transition shadow-lg shadow-pink-500/20">
                        <div class="relative">
                            <i data-lucide="shopping-bag" class="w-5 h-5"></i>
                            <span id="cart-badge-desktop" class="absolute -top-2 -right-2 bg-beautage-purple text-white text-[10px] font-extrabold w-4 h-4 rounded-full flex items-center justify-center shadow">0</span>
                        </div>
                        <div class="flex flex-col text-left">
                            <span class="text-[9px] text-pink-100 uppercase font-extrabold leading-none">Your Bag</span>
                            <span id="cart-total-header" class="text-xs font-black text-white leading-tight mt-0.5">Rs. 0</span>
                        </div>
                    </button>
                </div>

            </div>

            <!-- MOBILE SEARCH CONTAINER -->
            <div id="mobile-search-container" class="lg:hidden mt-3 pt-3 border-t border-purple-100 hidden">
                <div class="relative flex items-center border-2 border-purple-200 rounded-xl overflow-hidden bg-white shadow-inner">
                    <input type="text" id="mobile-search-input" placeholder="Search flagship store products..." 
                           class="w-full py-3 px-3 text-xs text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none font-medium">
                    <button class="bg-beautage-purple text-white px-4 py-3 flex items-center justify-center">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

        </div>
    </header>

    @include('partials.storefront-header-nav', ['storefrontNav' => $storefrontNav ?? []])
    </div>

    <!-- Mobile Navigation Drawer -->
    <button type="button" id="mobile-nav-overlay" onclick="toggleMobileNav()" aria-label="Close menu" class="fixed inset-0 bg-beautage-purpleDark/40 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300 lg:hidden border-0 p-0 cursor-default"></button>
    <div id="mobile-nav-drawer" class="fixed top-0 left-0 h-full w-4/5 max-w-xs bg-white border-r border-purple-100 z-50 transform -translate-x-full transition-transform duration-300 ease-in-out p-6 flex flex-col justify-between lg:hidden shadow-2xl">
        <div>
            <div class="flex items-center justify-between pb-4 border-b border-purple-100">
                <img src="{{ $siteLogoUrl }}" alt="Beautage" class="h-10 w-auto" onerror="this.onerror=null; this.src='https://placehold.co/120x40/5E1F70/FFFFFF?text=Beautage';">
                <button type="button" onclick="toggleMobileNav()" class="text-slate-400 hover:text-beautage-purple p-2 min-h-[44px] min-w-[44px] flex items-center justify-center" aria-label="Close menu">
                    <i data-lucide="x" class="w-6 h-6" aria-hidden="true"></i>
                </button>
            </div>
            <div class="py-4 space-y-3 text-sm font-semibold text-slate-700">
                <a href="{{ route('home') }}" onclick="toggleMobileNav()" class="block py-2 hover:text-beautage-purple">Home</a>
                <a href="{{ route('collections') }}" onclick="toggleMobileNav()" class="block py-2 hover:text-beautage-purple">Product Collections</a>
                <a href="{{ route('help') }}" onclick="toggleMobileNav()" class="block py-2 text-beautage-pink font-bold">Help Center & FAQ</a>
                <a href="{{ route('blog.index') }}" onclick="toggleMobileNav()" class="block py-2 hover:text-beautage-purple">Flagship Blog & Journal</a>
            </div>
        </div>
        <div class="pt-4 border-t border-purple-100">
            <span class="text-xs text-slate-500">Concierge Desk:</span>
            <a href="tel:+18008886678" class="block text-sm font-bold text-beautage-purple mt-0.5">+1 (800) BEAUTAGE</a>
        </div>
    </div>
    @endunless

    <!-- MAIN HELP CENTER & FAQ CONTENT -->

@yield('content')

@yield('below_main')

@unless ($__env->hasSection('lumiere_homepage'))
@include('partials.storefront-footer', ['footerSettings' => $footerSettings])
@endunless

<!-- CART DRAWER -->
    <div id="cart-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="cart-backdrop" onclick="toggleCartDrawer()" class="absolute inset-0 bg-beautage-purpleDark/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="cart-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-purple-100 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                    <h2 class="text-lg font-bold text-beautage-purple flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-beautage-pink"></i> Your Shopping Bag
                    </h2>
                    <button onclick="toggleCartDrawer()" class="text-slate-400 hover:text-beautage-purple p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="cart-items" class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-1"></div>
            </div>

            <div class="border-t border-purple-100 pt-4 space-y-3">
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Subtotal</span>
                    <span id="cart-subtotal" class="text-beautage-purple font-semibold">Rs. 0</span>
                </div>
                <div class="flex justify-between text-sm text-slate-600">
                    <span>Estimated Tax (8%)</span>
                    <span id="cart-tax" class="text-beautage-purple font-semibold">Rs. 0</span>
                </div>
                <div class="flex justify-between text-base text-beautage-purple font-bold pt-2 border-t border-purple-100">
                    <span>Total</span>
                    <span id="cart-total" class="text-beautage-pink">Rs. 0</span>
                </div>
                <a href="{{ route('checkout') }}" class="w-full bg-beautage-pink hover:bg-beautage-pinkHover text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Proceed to Checkout
                </a>
            </div>
        </div>
    </div>

    <!-- WISHLIST DRAWER -->
    <div id="wishlist-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="wishlist-backdrop" onclick="toggleWishlistDrawer()" class="absolute inset-0 bg-beautage-purpleDark/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="wishlist-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-purple-100 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                    <h2 class="text-lg font-bold text-beautage-purple flex items-center gap-2">
                        <i data-lucide="heart" class="w-5 h-5 text-beautage-pink"></i> Your Saved Wishlist
                    </h2>
                    <button onclick="toggleWishlistDrawer()" class="text-slate-400 hover:text-beautage-purple p-1 rounded-lg">
                        <i data-lucide="x" class="w-5 h-5"></i>
                    </button>
                </div>
                <div id="wishlist-items" class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-1"></div>
            </div>
        </div>
    </div>

    <!-- QUICK VIEW MODAL -->
    <div id="quickview-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-beautage-purpleDark/40 backdrop-blur-md hidden">
        <div class="bg-white border border-purple-100 rounded-3xl max-w-2xl w-full p-6 sm:p-8 relative shadow-2xl">
            <button type="button" onclick="closeQuickView()" aria-label="Close quick view" class="absolute top-4 right-4 text-slate-400 hover:text-slate-600 min-h-[44px] min-w-[44px] flex items-center justify-center"><i data-lucide="x" class="w-5 h-5" aria-hidden="true"></i></button>
            <div id="quickview-content" class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center"></div>
        </div>
    </div>

<script src="{{ asset('js/beautage-money.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-dom.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-cart.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-recently-viewed.js') }}?v=2026102917" defer></script>
<script src="{{ asset('js/beautage-header-nav.js') }}?v=2026102917" defer></script>
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
