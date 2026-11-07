@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: 'Product Collections & Catalog — Beautage Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col overflow-x-hidden"
@endsection

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/collections-catalog.css') }}?v=20261107cat">
@endpush

@section('content')
<div class="lcat">
    <nav class="lcat-crumb" aria-label="Breadcrumb">
        <a href="{{ route('home') }}">Home</a>
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        <a href="{{ route('collections') }}">Collections &amp; Categories</a>
        <i class="fa-solid fa-chevron-right" aria-hidden="true"></i>
        <span id="catalog-hero-crumb" class="lcat-crumb-current">{{ $heroCrumb }}</span>
    </nav>

        <!-- 1. BREADCRUMB & COLLECTION HERO HEADER BANNER -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-6">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap mb-4 overflow-x-auto no-scrollbar">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">Product Collections & Catalog</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-br from-purple-950 via-beautage-purple to-purple-900 text-white p-6 sm:p-10 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-16 -top-16 w-72 h-72 bg-beautage-pink/25 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-10 bottom-0 w-48 h-48 bg-amber-400/10 rounded-full blur-2xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                    <div class="max-w-2xl space-y-3 text-center lg:text-left">
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-white/10 text-pink-100 border border-white/20">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Verified flagship catalog
                        </span>
                        <h1 class="text-2xl sm:text-4xl font-serif font-bold tracking-tight">Shop Quick Bazar</h1>
                        <p class="text-xs sm:text-sm text-purple-100/95 leading-relaxed max-w-xl mx-auto lg:mx-0">
                            Filter by department, compare ratings, and browse our full catalog — six products per row on desktop for quick scanning.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center justify-center lg:justify-end gap-3">
                        <div class="px-5 py-3 rounded-2xl bg-white/10 border border-white/20 text-center min-w-[120px]">
                            <span id="header-product-count" class="text-2xl font-black text-white block leading-none">0</span>
                            <span class="text-[10px] uppercase tracking-wider text-purple-200 mt-1 block">Products</span>
                        </div>
                        <a href="{{ route('exclusive-offers') }}" class="px-4 py-3 rounded-2xl bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-bold uppercase tracking-wider shadow-lg shadow-pink-900/30 transition">
                            Bundle offers
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="lcat-layout">
        <div class="lcat-mobile-bar">
            <button type="button" id="lcat-filter-toggle" class="lcat-filter-toggle">
                <i class="fa-solid fa-filter" aria-hidden="true"></i>
                Filter &amp; Sort Products (<span id="lcat-active-filter-count">0</span>)
            </button>
            <select id="lcat-sort-mobile" class="lcat-sort" aria-label="Sort products">
                <option value="featured">Sort by: Featured</option>
                <option value="price-low">Price: Low to High</option>
                <option value="price-high">Price: High to Low</option>
                <option value="rating">Highest Rated</option>
                <option value="popularity">Most Popular</option>
            </select>
        </div>

        <div id="lcat-filter-backdrop" class="lcat-backdrop" hidden></div>

        <aside id="lcat-sidebar" class="lcat-sidebar" aria-label="Filters">
            <div class="lcat-sidebar-head">
                <h2><i class="fa-solid fa-sliders" aria-hidden="true"></i> Filters</h2>
                <div class="lcat-sidebar-actions">
                    <button type="button" id="lcat-reset" class="lcat-text-btn">Reset All</button>
                    <button type="button" id="lcat-filter-close" class="lcat-sidebar-close" aria-label="Close filters">&times;</button>
                </div>
            </div>

            <div class="lcat-filter-block">
                <h3>Product Type</h3>
                <div>
                    @foreach ($shopCategories ?? [] as $shopCategory)
                        <label class="lcat-check">
                            <span class="lcat-check-label">
                                <input type="checkbox" name="lcat-type" value="{{ $shopCategory->slug }}" @checked(($activeCategorySlug ?? null) === $shopCategory->slug)>
                                {{ $shopCategory->displayNavLabel() }}
                            </span>
                            <span class="lcat-count">({{ (int) data_get($catalogCategories->firstWhere('slug', $shopCategory->slug), 'count', 0) }})</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="lcat-filter-block">
                <h3>Price Range</h3>
                <div class="lcat-price-row">
                    <label class="lcat-price-field">
                        <span>Rs</span>
                        <input id="lcat-price-min" type="number" inputmode="numeric" min="0" max="{{ $priceCeil }}" value="{{ $priceFloor }}" aria-label="Minimum price">
                    </label>
                    <span class="lcat-price-dash" aria-hidden="true">–</span>
                    <label class="lcat-price-field">
                        <span>Rs</span>
                        <input id="lcat-price-max" type="number" inputmode="numeric" min="0" max="{{ $priceCeil }}" value="{{ $priceCeil }}" aria-label="Maximum price">
                    </label>
                </div>
            </div>

            <div id="lcat-ingredients" class="lcat-filter-block" hidden>
                <h3>Key Ingredients</h3>
                <div id="lcat-ingredient-list"></div>
            </div>

            <div class="lcat-filter-block">
                <h3>Customer Ratings</h3>
                <label class="lcat-check">
                    <span class="lcat-check-label">
                        <input type="checkbox" name="lcat-rating" value="4.8">
                        <span class="lcat-stars-row" aria-hidden="true"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></span>
                        <span class="lcat-rating-note">4.8 &amp; above</span>
                    </span>
                </label>
                <label class="lcat-check">
                    <span class="lcat-check-label">
                        <input type="checkbox" name="lcat-rating" value="4">
                        <span class="lcat-stars-row" aria-hidden="true"><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-regular fa-star"></i></span>
                        <span class="lcat-rating-note">4.0 &amp; above</span>
                    </span>
                </label>
            </div>

            <div class="lcat-filter-block">
                <h3>Availability</h3>
                <label class="lcat-check">
                    <span class="lcat-check-label">
                        <input type="checkbox" id="lcat-instock" value="1">
                        In Stock Only
                    </span>
                </label>
            </div>

            <button type="button" id="lcat-apply" class="lcat-apply">Apply Filters</button>
        </aside>

        <div class="lcat-main">
            <div class="lcat-toolbar">
                <p class="lcat-results">Showing <strong id="lcat-range">0</strong> of <strong id="lcat-total">{{ $catalogCount }}</strong> <span id="lcat-result-noun">products</span></p>
                <div class="lcat-sort-wrap">
                    <span class="lcat-sort-label">Sort by:</span>
                    <select id="lcat-sort" class="lcat-sort" aria-label="Sort products">
                        <option value="featured">Featured Best Sellers</option>
                        <option value="price-low">Price: Low to High</option>
                        <option value="price-high">Price: High to Low</option>
                        <option value="rating">Highest Customer Rating</option>
                        <option value="popularity">Most Popular</option>
                    </select>
                </div>
            </div>

            <div id="catalog-product-grid" class="lcat-grid" aria-live="polite"></div>
            <noscript><p class="lcat-empty">Turn on JavaScript to load the live catalog.</p></noscript>

            <div class="lcat-pager">
                <p class="lcat-pager-meta">Page <strong id="lcat-page-current">1</strong> of <strong id="lcat-page-total">1</strong></p>
                <div id="lcat-pages" class="lcat-pages" aria-label="Pagination"></div>
            </div>
        </div>
    </div>
</div>

@include('partials.recently-viewed-section', ['recentlyVariant' => 'collections', 'recentlyLimit' => 8])
@endsection

@push('scripts')
@include('partials.scripts.products-catalog')
<script>
window.products = (typeof products !== 'undefined' && Array.isArray(products)) ? products : [];
window.CATALOG_PAGE = {
    activeSlug: @json($activeCategorySlug ?? null),
    categories: @json($catalogCategories ?? []),
    priceFloor: @json($priceFloor ?? 0),
    priceCeil: @json($priceCeil ?? 0),
    defaultTitle: 'Collections & Categories',
    defaultBlurb: @json($defaultBlurb ?? ''),
};
</script>
<script src="{{ asset('js/collections-catalog.js') }}?v=20261107cat"></script>
@verbatim
<script>
        let cart = [];

        document.addEventListener('DOMContentLoaded', () => {
            if (window.BeautageCart) {
                cart = BeautageCart.readStorage();
                BeautageCart.updateCartUI();
            }
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            startCountdownTimer();
        });

        function showToast(message) {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = 'bg-slate-900 text-white px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold flex items-center gap-3 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300';
            const label = document.createElement('span');
            label.textContent = message;
            toast.appendChild(label);
            container.appendChild(toast);
            if (typeof lucide !== 'undefined') lucide.createIcons();
            setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email') || document.getElementById('partner-newsletter-email');
            const email = emailInput ? emailInput.value.trim() : '';
            if (email) {
                showToast('Thank you for subscribing.');
                emailInput.value = '';
            }
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
                setTimeout(() => { drawer.classList.add('hidden'); }, 300);
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
                setTimeout(() => { drawer.classList.add('hidden'); }, 300);
            }
        }

        function toggleMobileNav() {
            const overlay = document.getElementById('mobile-nav-overlay');
            const drawer = document.getElementById('mobile-nav-drawer');
            if (!overlay || !drawer) return;
            if (drawer.classList.contains('-translate-x-full')) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                drawer.classList.remove('-translate-x-full');
            } else {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('-translate-x-full');
            }
        }

        function toggleMobileSearch() {
            const searchContainer = document.getElementById('mobile-search-container');
            if (!searchContainer) return;
            searchContainer.classList.toggle('hidden');
            if (!searchContainer.classList.contains('hidden')) {
                const input = document.getElementById('mobile-search-input');
                if (input) input.focus();
            }
        }

        function openQuickView(productId) {
            const product = (window.products || []).find(p => Number(p.id) === Number(productId));
            if (!product) return;
            const modal = document.getElementById('quickview-modal');
            const content = document.getElementById('quickview-content');
            if (!modal || !content) return;
            const price = window.BeautageMoney ? BeautageMoney.formatDecimal(product.price) : product.price;
            const oldPrice = window.BeautageMoney ? BeautageMoney.formatDecimal(product.oldPrice) : product.oldPrice;
            content.innerHTML = `
                <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100">
                    <img src="${product.image}" alt="${product.name}" class="w-full h-full object-cover">
                </div>
                <div class="space-y-4">
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-bold rounded-full uppercase">${product.category || ''}</span>
                    <h2 class="text-xl font-bold text-slate-900">${product.name}</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-black text-slate-900">${price}</span>
                        <span class="text-sm text-slate-400 line-through">${oldPrice}</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">${product.desc || ''}</p>
                    <div class="flex items-center gap-4 pt-2">
                        <button onclick="addToCart(${product.id}); closeQuickView();" class="flex-1 bg-slate-900 hover:bg-slate-700 text-white font-bold py-3 rounded-xl transition text-xs uppercase">Add To Bag</button>
                        <button onclick="toggleWishlist(${product.id});" class="p-3 rounded-xl bg-slate-100 text-slate-800">Save</button>
                    </div>
                </div>`;
            modal.classList.remove('hidden');
        }

        function closeQuickView() {
            const modal = document.getElementById('quickview-modal');
            if (modal) modal.classList.add('hidden');
        }

        function setActiveBottomNav(el) {
            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                item.classList.remove('text-beautage-pink');
                item.classList.add('text-slate-500');
            });
            if (!el) return;
            el.classList.remove('text-slate-500');
            el.classList.add('text-beautage-pink');
        }

        function startCountdownTimer() {
            const timerEl = document.getElementById('announcement-timer');
            if (!timerEl) return;
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if (totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                timerEl.textContent = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
