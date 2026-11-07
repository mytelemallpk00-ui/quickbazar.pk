@php
    $bottomHome = request()->routeIs('home');
    $bottomShop = request()->routeIs('collections', 'product.show', 'exclusive-offers');
    $itemClass = 'bottom-nav-item flex min-h-[44px] min-w-[44px] flex-col items-center justify-center px-2 py-1 text-slate-500 transition hover:text-emerald-700';
    $activeClass = 'bottom-nav-item flex min-h-[44px] min-w-[44px] flex-col items-center justify-center px-2 py-1 text-emerald-700';
@endphp
<nav class="storefront-bottom-nav fixed inset-x-0 bottom-0 z-40 border-t border-slate-200 bg-white/95 px-2 pt-1 shadow-[0_-8px_24px_rgba(15,23,42,0.08)] backdrop-blur-md md:hidden" style="padding-bottom: max(0.35rem, env(safe-area-inset-bottom));" aria-label="Store">
    <div class="mx-auto flex max-w-lg items-end justify-around">
        <a href="{{ route('home') }}" class="{{ $bottomHome ? $activeClass : $itemClass }}">
            <i data-lucide="home" class="h-5 w-5" aria-hidden="true"></i>
            <span class="mt-0.5 text-[10px] font-bold">Home</span>
        </a>
        <a href="{{ route('collections') }}" class="{{ $bottomShop ? $activeClass : $itemClass }}">
            <i data-lucide="layout-grid" class="h-5 w-5" aria-hidden="true"></i>
            <span class="mt-0.5 text-[10px] font-semibold">Shop</span>
        </a>
        <div class="relative -top-3">
            <button type="button" onclick="toggleMobileNav()" aria-label="Open menu" class="flex h-14 w-14 items-center justify-center rounded-full border-4 border-white bg-emerald-600 text-white shadow-lg shadow-emerald-600/30 transition active:scale-95">
                <i data-lucide="menu" class="h-6 w-6" aria-hidden="true"></i>
            </button>
        </div>
        <button type="button" onclick="toggleWishlistDrawer()" aria-label="Open wishlist" class="{{ $itemClass }} relative border-0 bg-transparent">
            <i data-lucide="heart" class="h-5 w-5" aria-hidden="true"></i>
            <span class="mt-0.5 text-[10px] font-semibold">Saved</span>
            <span id="mobile-wishlist-badge" class="absolute right-1 top-0 flex h-4 w-4 items-center justify-center rounded-full bg-emerald-600 text-[9px] font-bold text-white" aria-hidden="true">0</span>
        </button>
        <button type="button" onclick="toggleCartDrawer()" aria-label="Open shopping bag" class="{{ $itemClass }} relative border-0 bg-transparent">
            <i data-lucide="shopping-bag" class="h-5 w-5" aria-hidden="true"></i>
            <span class="mt-0.5 text-[10px] font-semibold">Bag</span>
            <span id="mobile-cart-badge" class="absolute right-1 top-0 flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 text-[9px] font-bold text-white" aria-hidden="true">0</span>
        </button>
    </div>
</nav>
