@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: 'Product Collections & Catalog — Beautage Flagship Store')

@section('body_attrs')
class="lumiere-v2 bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col selection:bg-amber-200 selection:text-slate-900 pb-36 md:pb-0 overflow-x-hidden"
@endsection

@push('structured-data')
@php
    $crumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Collections & Categories', 'url' => route('collections')],
    ];
    if (filled($activeCategorySlug ?? null) && filled($heroCrumb ?? null) && ($heroCrumb ?? '') !== 'All departments') {
        $crumbs[] = ['name' => $heroCrumb, 'url' => route('collections', ['cat' => $activeCategorySlug])];
    }
    $collectionJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($crumbs)->values()->map(fn ($crumb, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ])->all(),
    ];
@endphp
@include('partials.json-ld-script', ['jsonLd' => $collectionJsonLd])
@endpush

@section('content')
<main class="lv2-page">

        <div class="lv2-wrap lv2-collection">
            <nav class="lv2-crumbs" aria-label="Breadcrumb">
                <a href="{{ route('home') }}">Home</a>
                <span aria-hidden="true">/</span>
                <a href="{{ route('collections') }}">Collections & Categories</a>
                @if (filled($activeCategorySlug ?? null) && ($heroCrumb ?? '') !== 'All departments')
                    <span aria-hidden="true">/</span>
                    <span>{{ $heroCrumb }}</span>
                @endif
            </nav>
            <section class="lv2-collection-hero" aria-labelledby="collection-heading">
                <span class="lv2-kicker">Professional Botanical Collection</span>
                <h1 id="collection-heading">{{ $heroTitle ?? 'Collections & Categories' }}</h1>
                <p>{{ $heroBlurb ?? 'Browse the live catalog. Filter by department, price, rating, and availability.' }}</p>
                <div class="lv2-meta">
                    <span><strong id="header-product-count">0</strong> products</span>
                    <span>Original catalog prices</span>
                    <span>Nationwide shipping</span>
                </div>
            </section>
        </div>

        <!-- 2. MAIN CATALOG SECTION WITH FILTERS & PRODUCTS -->
        <section class="lv2-wrap lv2-section">
            <div class="lv2-catalog">
                <aside class="lv2-filters">
                    <div class="lv2-filters-head">
                        <h2><i data-lucide="filter" class="w-4 h-4"></i> Filter</h2>
                        <button type="button" onclick="resetFilters()">Reset</button>
                    </div>

                    <!-- Department / Category Filter -->
                    <div class="lv2-filter-block">
                        <p class="lv2-filter-label">Departments</p>
                        @php
                            $activeCatSlug = $activeCategorySlug ?? null;
                        @endphp
                        <div class="lv2-filter-options">
                            <label>
                                <input type="radio" name="sidebar-cat" value="all" @checked(! $activeCatSlug) onchange="applySidebarFilters()">
                                <span>All departments</span>
                            </label>
                            @foreach ($shopCategories ?? [] as $shopCategory)
                                <label>
                                    <input type="radio" name="sidebar-cat" value="{{ $shopCategory->slug }}" @checked($activeCatSlug === $shopCategory->slug) onchange="applySidebarFilters()">
                                    <span>{{ $shopCategory->displayNavLabel() }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="lv2-filter-block">
                        <div class="lv2-filter-label">
                            <span>Max price</span>
                            <span id="price-slider-value">{{ \App\Support\MoneyFormat::pkr($priceCeil ?? 0) }}</span>
                        </div>
                        <input type="range" id="price-range" min="{{ (int) ($priceFloor ?? 0) }}" max="{{ (int) ($priceCeil ?? 1) }}" step="{{ max(1, (int) round((((int) ($priceCeil ?? 1)) - ((int) ($priceFloor ?? 0))) / 40)) }}" value="{{ (int) ($priceCeil ?? 1) }}" oninput="applySidebarFilters()">
                        <div class="lv2-filter-range">
                            <span>{{ \App\Support\MoneyFormat::pkr($priceFloor ?? 0) }}</span>
                            <span>{{ \App\Support\MoneyFormat::pkr($priceCeil ?? 0) }}</span>
                        </div>
                    </div>

                    <div class="lv2-filter-block">
                        <label class="lv2-filter-label" for="rating-filter">Minimum rating</label>
                        <select id="rating-filter" onchange="applySidebarFilters()">
                            <option value="0">All ratings</option>
                            <option value="4.5">4.5 and above</option>
                            <option value="4.8">4.8 and above</option>
                        </select>
                    </div>

                    <div class="lv2-filter-block">
                        <label class="lv2-check">
                            <input type="checkbox" id="instock-filter" onchange="applySidebarFilters()">
                            <span>In stock only</span>
                        </label>
                    </div>
                </aside>

                <!-- MAIN PRODUCT CATALOG GRID (SPAN 9) -->
                <div class="lv2-catalog-main">
                    <div class="lv2-pills lg:hidden">
                        <button type="button" data-catalog-cat="all" onclick="quickCategoryFilter('all')" class="catalog-quick-cat is-on">All</button>
                        @foreach ($shopCategories ?? [] as $shopCategory)
                            <button type="button" data-catalog-cat="{{ $shopCategory->slug }}" onclick="quickCategoryFilter(@json($shopCategory->slug))" class="catalog-quick-cat">{{ $shopCategory->displayNavLabel() }}</button>
                        @endforeach
                    </div>

                    <div class="lv2-toolbar">
                        <p class="lv2-muted">Showing <strong id="product-visible-count">0</strong> of <strong id="product-count-label">0</strong> products</p>
                        <label class="lv2-sort">
                            <span>Sort</span>
                            <select id="sort-select" onchange="applySidebarFilters()">
                                <option value="featured">Featured</option>
                                <option value="price-low">Price: low to high</option>
                                <option value="price-high">Price: high to low</option>
                                <option value="rating">Highest rating</option>
                                <option value="popularity">Most popular</option>
                            </select>
                        </label>
                    </div>

                    <div id="catalog-product-grid" class="lv2-grid lv2-grid-3"></div>

                    <!-- Infinite Scroll Loading Indicator -->
                    <div id="infinite-scroll-loader" class="lv2-empty hidden">Loading more products…</div>
                    <div id="catalog-end-message" class="lv2-empty hidden">You have seen every product in this collection.</div>

                </div>

            </div>
        </section>

        @include('partials.recently-viewed-section', ['recentlyVariant' => 'collections', 'recentlyLimit' => 8])

    </main>
@endsection

@section('below_main')

@endsection

@push('scripts')
@include('partials.scripts.products-catalog')
<script>
window.CATALOG_PAGE = {
    activeSlug: @json($activeCategorySlug ?? null),
    categories: @json($catalogCategories ?? []),
    priceCeil: @json((int) ($priceCeil ?? 1)),
};
</script>
@verbatim
<script>
        let cart = [];
        let wishlist = [];
        const CATALOG_PAGE_SIZE = 12;
        let displayedCount = CATALOG_PAGE_SIZE;
        let currentFilteredProducts = [];
        let isLoadingMore = false;

        document.addEventListener('DOMContentLoaded', () => {
            if (window.BeautageCart) {
                cart = BeautageCart.readStorage();
                BeautageCart.updateCartUI();
            }
            const catParam = window.CATALOG_PAGE?.activeSlug
                || new URLSearchParams(window.location.search).get('cat');
            if (catParam) {
                const radio = document.querySelector(`input[name="sidebar-cat"][value="${catParam}"]`);
                if (radio) {
                    radio.checked = true;
                }
            }

            applySidebarFilters();
            lucide.createIcons();
            startCountdownTimer();

            // Setup Infinite Scroll Listener
            window.addEventListener('scroll', handleInfiniteScroll);
        });

        function handleInfiniteScroll() {
            if (isLoadingMore) return;
            const scrollableHeight = document.documentElement.scrollHeight - window.innerHeight;
            const currentScroll = window.scrollY;

            if (currentScroll >= scrollableHeight - 300) {
                if (displayedCount < currentFilteredProducts.length) {
                    loadMoreProducts();
                }
            }
        }

        function loadMoreProducts() {
            isLoadingMore = true;
            const loader = document.getElementById('infinite-scroll-loader');
            loader.classList.remove('hidden');

            setTimeout(() => {
                displayedCount += CATALOG_PAGE_SIZE;
                renderCatalogGrid(currentFilteredProducts, true);
                loader.classList.add('hidden');
                isLoadingMore = false;
            }, 600);
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-beautage-purple text-white px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold flex items-center gap-3 border border-purple-500/40 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300';
            toast.innerHTML = `<i data-lucide="sparkles" class="w-4 h-4 text-beautage-amber shrink-0"></i><span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value.trim();
            if(email) {
                showToast(`Thank you for subscribing! A 15% off coupon has been sent to ${email}.`);
                emailInput.value = '';
            }
        }

        function normalizeCatalogToken(value) {
            return String(value || '').toLowerCase().replace(/[^a-z0-9]+/g, '');
        }

        function productMatchesCatalogCategory(product, slug) {
            if (slug === 'all') {
                return true;
            }

            const categories = window.CATALOG_PAGE?.categories || [];
            const category = categories.find((entry) => entry.slug === slug);

            if (product.categoryId != null && category?.id != null && Number(product.categoryId) === Number(category.id)) {
                return true;
            }

            if (product.categorySlug && product.categorySlug === slug) {
                return true;
            }

            if (category?.name && normalizeCatalogToken(product.category) === normalizeCatalogToken(category.name)) {
                return true;
            }

            return normalizeCatalogToken(product.category) === normalizeCatalogToken(slug)
                || normalizeCatalogToken(product.categorySlug) === normalizeCatalogToken(slug);
        }

        function applySidebarFilters() {
            const selectedCat = document.querySelector('input[name="sidebar-cat"]:checked').value;
            const maxPrice = parseFloat(document.getElementById('price-range').value);
            const minRating = parseFloat(document.getElementById('rating-filter').value);
            const inStockOnly = document.getElementById('instock-filter').checked;
            const sortBy = document.getElementById('sort-select').value;

            document.getElementById('price-slider-value').innerText = BeautageMoney.format(maxPrice);

            let filtered = products.filter(p => {
                if (! productMatchesCatalogCategory(p, selectedCat)) return false;
                if(p.price > maxPrice) return false;
                if(p.rating < minRating) return false;
                if(inStockOnly && !p.inStock) return false;
                return true;
            });

            if(sortBy === 'price-low') {
                filtered.sort((a, b) => a.price - b.price);
            } else if(sortBy === 'price-high') {
                filtered.sort((a, b) => b.price - a.price);
            } else if(sortBy === 'rating') {
                filtered.sort((a, b) => b.rating - a.rating);
            } else if(sortBy === 'popularity') {
                filtered.sort((a, b) => b.reviews - a.reviews);
            }

            currentFilteredProducts = filtered;
            displayedCount = CATALOG_PAGE_SIZE;
            syncQuickCategoryPills(selectedCat);
            renderCatalogGrid(filtered, false);

            try {
                const url = new URL(window.location.href);
                url.search = '';
                if (selectedCat && selectedCat !== 'all') {
                    url.searchParams.set('cat', selectedCat);
                }
                window.history.replaceState({}, '', url.pathname + url.search);
            } catch (e) { /* ignore */ }
        }

        function quickCategoryFilter(category) {
            const radio = document.querySelector(`input[name="sidebar-cat"][value="${category}"]`);
            if (radio) {
                radio.checked = true;
            }
            applySidebarFilters();
        }

        function syncQuickCategoryPills(selectedCat) {
            document.querySelectorAll('.catalog-quick-cat').forEach((btn) => {
                const cat = btn.dataset.catalogCat || 'all';
                btn.classList.toggle('is-on', cat === selectedCat);
            });
        }

        function resetFilters() {
            document.querySelector('input[name="sidebar-cat"][value="all"]').checked = true;
            document.getElementById('price-range').value = window.CATALOG_PAGE.priceCeil || 300;
            document.getElementById('rating-filter').value = "0";
            document.getElementById('instock-filter').checked = false;
            document.getElementById('sort-select').value = "featured";
            applySidebarFilters();
            showToast('All filters have been reset.');
        }

        function renderCatalogGrid(items, isAppending = false) {
            const container = document.getElementById('catalog-product-grid');
            const endMessage = document.getElementById('catalog-end-message');
            
            document.getElementById('product-count-label').innerText = items.length;
            document.getElementById('header-product-count').innerText = items.length;
            document.getElementById('product-visible-count').innerText = Math.min(displayedCount, items.length);

            if(items.length === 0) {
                container.innerHTML = `<p class="lv2-empty">No products match these filters. Try another department or a higher price.</p>`;
                endMessage.classList.add('hidden');
                return;
            }

            const visibleItems = items.slice(0, displayedCount);

            container.innerHTML = visibleItems.map(p => window.ShopProductCard.html(p)).join('');

            if (displayedCount >= items.length) {
                endMessage.classList.remove('hidden');
            } else {
                endMessage.classList.add('hidden');
            }

            lucide.createIcons();
        }

        function addToCart(productId) {
            const product = products.find(p => p.id === productId);
            if(!product) return;

            const existing = cart.find(item => item.id === productId);
            if(existing) {
                existing.qty += 1;
            } else {
                cart.push({ ...product, qty: 1 });
            }

            updateCartUI();
            showToast(`Added "${product.name}" to your shopping bag.`);
        }

        function removeFromCart(productId) {
            cart = cart.filter(item => item.id !== productId);
            updateCartUI();
        }

        function updateCartQty(productId, delta) {
            const item = cart.find(i => i.id === productId);
            if(item) {
                item.qty += delta;
                if(item.qty <= 0) {
                    removeFromCart(productId);
                } else {
                    updateCartUI();
                }
            }
        }

        function updateCartUI() {
            if (window.BeautageCart) {
                BeautageCart.writeStorage(cart);
                BeautageCart.updateCartUI();
            }
        }

        function toggleWishlist(productId) {
            const product = products.find(p => p.id === productId);
            const index = wishlist.findIndex(item => item.id === productId);

            if(index > -1) {
                wishlist.splice(index, 1);
                showToast(`Removed "${product.name}" from your wishlist.`);
            } else {
                wishlist.push(product);
                showToast(`Saved "${product.name}" to your wishlist.`);
            }

            updateWishlistUI();
            applySidebarFilters();
        }

        function updateWishlistUI() {
            document.getElementById('wishlist-badge').innerText = wishlist.length;
            document.getElementById('mobile-wishlist-badge').innerText = wishlist.length;
            document.getElementById('wishlist-badge-desktop').innerText = wishlist.length;

            const container = document.getElementById('wishlist-items');
            if(wishlist.length === 0) {
                container.innerHTML = `<div class="text-center py-12 text-slate-400 text-xs">No saved items in your wishlist yet.</div>`;
                return;
            }

            container.innerHTML = wishlist.map(item => `
                <div class="flex items-center gap-4 p-3 rounded-2xl bg-purple-50/50 border border-purple-100">
                    <img src="${item.image}" class="w-16 h-16 object-cover rounded-xl">
                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-beautage-purple">${item.name}</h4>
                        <span class="text-xs font-extrabold text-slate-800">${BeautageMoney.formatDecimal(item.price)}</span>
                    </div>
                    <div class="flex flex-col gap-2">
                        <button onclick="addToCart(${item.id}); toggleWishlist(${item.id});" class="px-3 py-1.5 bg-beautage-pink text-white rounded-lg text-[10px] font-bold">Move to Bag</button>
                        <button onclick="toggleWishlist(${item.id})" class="text-rose-500 text-[10px] font-semibold text-center">Remove</button>
                    </div>
                </div>
            `).join('');
        }

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-backdrop');
            const panel = document.getElementById('cart-panel');

            if(drawer.classList.contains('hidden')) {
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

            if(drawer.classList.contains('hidden')) {
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

            if(drawer.classList.contains('-translate-x-full')) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                drawer.classList.remove('-translate-x-full');
            } else {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('-translate-x-full');
            }
        }

        function openQuickView(productId) {
            const product = products.find(p => p.id === productId);
            if(!product) return;

            const modal = document.getElementById('quickview-modal');
            const content = document.getElementById('quickview-content');

            content.innerHTML = `
                <div class="aspect-square rounded-2xl overflow-hidden bg-purple-50">
                    <img src="${product.image}" class="w-full h-full object-cover">
                </div>
                <div class="space-y-4">
                    <span class="px-2.5 py-1 bg-purple-100 text-beautage-purple text-[10px] font-bold rounded-full uppercase">${product.category}</span>
                    <h2 class="text-xl font-serif font-bold text-beautage-purple">${product.name}</h2>
                    <div class="flex items-center gap-3">
                        <span class="text-2xl font-black text-slate-800">${BeautageMoney.formatDecimal(product.price)}</span>
                        <span class="text-sm text-slate-400 line-through">${BeautageMoney.formatDecimal(product.oldPrice)}</span>
                        <span class="text-xs text-emerald-600 font-bold bg-emerald-50 px-2 py-0.5 rounded">In Stock</span>
                    </div>
                    <p class="text-xs text-slate-600 leading-relaxed">${product.desc}</p>
                    <div class="flex items-center gap-4 pt-2">
                        <button onclick="addToCart(${product.id}); closeQuickView();" class="flex-1 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold py-3 rounded-xl transition shadow-md shadow-pink-500/20 text-xs uppercase">
                            Add To Bag
                        </button>
                        <button onclick="toggleWishlist(${product.id});" class="p-3 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple transition">
                            <i data-lucide="heart" class="w-5 h-5"></i>
                        </button>
                    </div>
                </div>
            `;
            modal.classList.remove('hidden');
            lucide.createIcons();
        }

        function closeQuickView() {
            document.getElementById('quickview-modal').classList.add('hidden');
        }

        function setActiveBottomNav(el) {
            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                item.classList.remove('text-beautage-pink');
                item.classList.add('text-slate-500');
            });
            el.classList.remove('text-slate-500');
            el.classList.add('text-beautage-pink');
        }

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                const announcement = document.getElementById('announcement-timer');
                if (announcement) announcement.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
