@extends('layouts.app')

@section('title', 'Quick Bazar — Elevate Your Natural Elegance | Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white overflow-x-clip max-w-full"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col">

        @php
            $hero = $homepage['hero'] ?? [];
            $heroEnabled = (bool) ($hero['enabled'] ?? true);
            $heroProductId = (int) ($hero['product_id'] ?? 0);
            $heroImage = \App\Support\HomepageContent::imageUrl($hero['image_url'] ?? null, $hero['image_alt'] ?? 'Hero');
            $promoCards = $homepage['promo_cards'] ?? [];
            $categoryIntro = $homepage['category_intro'] ?? [];
            $categoryTiles = $homepage['category_tiles'] ?? [];
        @endphp

        <!-- 3. HERO BANNER & HYBRID PROMO CARDS -->
        <section id="hero" class="w-full max-w-full px-4 sm:px-12 xl:px-20 py-6 sm:py-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6 w-full max-w-full">

                @if($heroEnabled)
                @php
                    $heroFrom = $hero['background_from'] ?? null;
                    $heroTo = $hero['background_to'] ?? null;
                    $heroGradientStyle = (filled($heroFrom) && filled($heroTo))
                        ? 'background: linear-gradient(90deg, '.e($heroFrom).', '.e($heroTo).');'
                        : '';
                @endphp
                <div class="lg:col-span-8 relative rounded-3xl overflow-hidden text-white p-5 sm:p-12 xl:p-16 shadow-xl flex items-center min-h-[320px] sm:min-h-[460px] {{ $heroGradientStyle ? '' : 'bg-gradient-to-r from-purple-900 via-beautage-purple to-beautage-purpleDark' }}" @if($heroGradientStyle) style="{{ $heroGradientStyle }}" @endif>
                    <div class="absolute -right-12 -top-12 w-96 h-96 bg-beautage-pink/30 rounded-full blur-3xl pointer-events-none"></div>

                    <div class="relative z-10 max-w-2xl space-y-6">
                        @if(filled($hero['badge'] ?? null))
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-white/10 text-pink-200 border border-white/20 backdrop-blur-sm">
                            <span class="w-2 h-2 rounded-full bg-beautage-pink animate-pulse"></span>
                            {{ $hero['badge'] }}
                        </span>
                        @endif
                        <h1 class="text-2xl sm:text-5xl xl:text-6xl font-serif font-bold text-white leading-tight tracking-tight">
                            {{ $hero['title_line1'] ?? '' }}
                            @if(filled($hero['title_accent'] ?? null))
                            <br><span class="bg-clip-text text-transparent bg-gradient-to-r from-pink-300 via-amber-200 to-white">{{ $hero['title_accent'] }}</span>
                            @endif
                        </h1>
                        @if(filled($hero['description'] ?? null))
                        <p class="text-purple-100 text-xs sm:text-sm xl:text-base leading-relaxed">
                            {{ $hero['description'] }}
                        </p>
                        @endif

                        <div class="flex flex-wrap items-center gap-6 pt-2">
                            @if(filled($hero['price_display'] ?? null))
                            <div>
                                @if(filled($hero['price_label'] ?? null))
                                <span class="text-xs text-purple-200 block uppercase font-semibold">{{ $hero['price_label'] }}</span>
                                @endif
                                <div class="flex items-baseline gap-2">
                                    <span class="text-3xl font-extrabold text-white">Rs. {{ $hero['price_display'] }}</span>
                                    @if(filled($hero['compare_price_display'] ?? null))
                                    <span class="text-sm text-purple-300 line-through">Rs. {{ $hero['compare_price_display'] }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif
                            @if(filled($hero['button_label'] ?? null))
                                @if($heroProductId > 0)
                                <button type="button" onclick="addToCart({{ $heroProductId }})" class="bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold px-8 py-3.5 rounded-xl transition flex items-center gap-2 shadow-lg shadow-pink-500/30 text-xs uppercase tracking-wider">
                                    <span>{{ $hero['button_label'] }}</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </button>
                                @else
                                <a href="{{ route('collections') }}" class="bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold px-8 py-3.5 rounded-xl transition inline-flex items-center gap-2 shadow-lg shadow-pink-500/30 text-xs uppercase tracking-wider">
                                    <span>{{ $hero['button_label'] }}</span>
                                    <i data-lucide="arrow-right" class="w-4 h-4"></i>
                                </a>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="hidden sm:block absolute right-8 bottom-6 lg:bottom-10 w-64 xl:w-80 pointer-events-none opacity-95">
                        <img src="{{ $heroImage }}" alt="{{ $hero['image_alt'] ?? 'Hero product' }}" class="w-full h-auto object-cover rounded-2xl border-2 border-white/20 shadow-2xl transform rotate-[-2deg]" onerror="this.onerror=null; this.src='https://placehold.co/400x400/5E1F70/FFFFFF?text=Hero';">
                    </div>
                </div>
                @endif

                @if(count($promoCards) > 0)
                <div class="{{ $heroEnabled ? 'lg:col-span-4' : 'lg:col-span-12' }} flex flex-col gap-6">
                    @foreach($promoCards as $card)
                        @php
                            $cardProductId = (int) ($card['product_id'] ?? 0);
                            $isGradient = ($card['style'] ?? 'light') === 'gradient';
                            $buttonStyle = $card['button_style'] ?? 'text';
                            $cardImage = filled($card['image_url'] ?? null)
                                ? \App\Support\HomepageContent::imageUrl($card['image_url'], $card['title'] ?? 'Promo')
                                : null;
                        @endphp
                        <div class="flex-1 rounded-2xl p-6 border shadow-md flex flex-col justify-between relative overflow-hidden {{ $isGradient ? 'bg-gradient-to-br from-beautage-purpleLight to-pink-50 text-beautage-purple border-purple-200' : 'bg-white border-purple-100 group' }}">
                            @if($cardImage)
                            <div class="absolute -right-4 -bottom-4 w-28 h-28 opacity-90 pointer-events-none hidden sm:block">
                                <img src="{{ $cardImage }}" alt="" class="w-full h-full object-contain drop-shadow-lg">
                            </div>
                            @endif
                            @if(! $isGradient)
                            <div class="absolute right-0 top-0 w-32 h-32 bg-pink-50 rounded-full -mr-10 -mt-10 group-hover:scale-110 transition duration-300"></div>
                            @endif
                            <div>
                                @if(filled($card['badge'] ?? null))
                                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider border {{ $isGradient ? 'text-beautage-purple bg-purple-100 border-purple-200' : 'text-beautage-pink bg-pink-50 border-pink-200' }}">{{ $card['badge'] }}</span>
                                @endif
                                @if(filled($card['title'] ?? null))
                                <h3 class="text-lg font-bold text-beautage-purple mt-3">{{ $card['title'] }}</h3>
                                @endif
                                @if(filled($card['description'] ?? null))
                                <p class="text-xs {{ $isGradient ? 'text-slate-600' : 'text-slate-500' }} mt-1">{{ $card['description'] }}</p>
                                @endif
                            </div>
                            <div class="mt-6 flex items-center justify-between z-10">
                                @if(filled($card['price_display'] ?? null))
                                <span class="text-lg font-extrabold {{ $isGradient ? 'text-beautage-purple' : 'text-slate-800' }}">Rs. {{ $card['price_display'] }}</span>
                                @endif
                                @if(filled($card['button_label'] ?? null))
                                    @if($cardProductId > 0)
                                        @if($buttonStyle === 'solid')
                                        <button type="button" onclick="addToCart({{ $cardProductId }})" class="px-4 py-2 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-bold text-xs rounded-xl transition shadow-sm">
                                            {{ $card['button_label'] }}
                                        </button>
                                        @else
                                        <button type="button" onclick="addToCart({{ $cardProductId }})" class="text-xs font-bold text-beautage-pink hover:text-beautage-pinkHover flex items-center gap-1">
                                            {{ $card['button_label'] }} <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                        </button>
                                        @endif
                                    @elseif($buttonStyle === 'solid')
                                    <a href="{{ route('collections') }}" class="px-4 py-2 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-bold text-xs rounded-xl transition shadow-sm inline-block">
                                        {{ $card['button_label'] }}
                                    </a>
                                    @else
                                    <a href="{{ route('collections') }}" class="text-xs font-bold text-beautage-pink hover:text-beautage-pinkHover inline-flex items-center gap-1">
                                        {{ $card['button_label'] }} <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                                    </a>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                @endif

            </div>
        </section>

        <!-- 4. CATEGORY SECTION -->
        <section class="w-full px-6 sm:px-12 xl:px-20 py-10">
            <div class="text-center max-w-3xl mx-auto space-y-3 mb-8">
                @if(filled($categoryIntro['badge'] ?? null))
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full text-xs font-bold bg-purple-100 text-beautage-purple border border-purple-200 shadow-xs">
                    <i data-lucide="sparkles" class="w-3.5 h-3.5 text-beautage-pink"></i>
                    <span>{{ $categoryIntro['badge'] }}</span>
                </div>
                @endif

                @if(filled($categoryIntro['heading'] ?? null))
                <h2 class="text-2xl sm:text-4xl font-serif font-bold text-beautage-purple tracking-tight">
                    {{ $categoryIntro['heading'] }}
                </h2>
                @endif

                @if(filled($categoryIntro['description'] ?? null))
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl mx-auto font-medium">
                    {{ $categoryIntro['description'] }}
                </p>
                @endif

                @if(! empty($categoryIntro['trust_badges']))
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    @foreach($categoryIntro['trust_badges'] as $trust)
                        @if(filled($trust['label'] ?? null))
                        <div class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold bg-purple-50 text-beautage-purple border border-purple-200 shadow-xs">
                            @if(filled($trust['icon'] ?? null))
                            <i data-lucide="{{ $trust['icon'] }}" class="w-4 h-4 text-beautage-pink"></i>
                            @endif
                            <span>{{ $trust['label'] }}</span>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endif
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-7 gap-5 pt-4">
                @foreach($categoryTiles as $tile)
                    @php
                        $filter = $tile['filter_value'] ?? 'all';
                        $isViewAll = (bool) ($tile['is_view_all'] ?? false);
                        $tileImage = \App\Support\HomepageContent::imageUrl($tile['image_url'] ?? null, $tile['label'] ?? 'Category');
                    @endphp
                    @php $tileUrl = \App\Support\HomepageContent::tileCollectionUrl($tile); @endphp
                    @if($isViewAll)
                    <a href="{{ $tileUrl }}" class="bg-gradient-to-br from-beautage-purple to-beautage-purpleDark p-5 rounded-2xl border border-purple-700 shadow-md hover:shadow-xl transition-all duration-300 cursor-pointer text-center group flex flex-col items-center justify-between text-white">
                        <div class="w-24 h-24 rounded-xl bg-white/10 flex items-center justify-center overflow-hidden mb-3 p-2 group-hover:scale-105 transition duration-300">
                            <i data-lucide="arrow-right" class="w-10 h-10 text-pink-300"></i>
                        </div>
                        <span class="text-xs font-bold text-white group-hover:text-pink-200 transition">{{ $tile['label'] ?? 'View all' }}</span>
                    </a>
                    @else
                    <a href="{{ $tileUrl }}" class="bg-white p-5 rounded-2xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer text-center group flex flex-col items-center justify-between">
                        <div class="w-24 h-24 rounded-xl bg-purple-50 flex items-center justify-center overflow-hidden mb-3 p-2 group-hover:scale-105 transition duration-300">
                            <img src="{{ $tileImage }}" alt="{{ $tile['label'] ?? 'Category' }}" class="w-full h-full object-contain drop-shadow" onerror="this.onerror=null; this.src='https://placehold.co/150x150/5E1F70/FFFFFF?text=Category';">
                        </div>
                        <span class="text-xs font-bold text-beautage-purple group-hover:text-beautage-pink transition">{{ $tile['label'] ?? '' }}</span>
                    </a>
                    @endif
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
    window.HOME_PAGE = {
        spotlightProductId: @json($flashSpotlightProduct?->id),
        defaultShowcaseCategorySlug: @json($shopCategories->first()?->slug ?? 'all'),
        categories: @json($homePageCategories),
    };
</script>
@verbatim
<script>
        let cart = [];
        let wishlist = [];
        let currentFilter = 'all';
        let currentFlashFilter = 'all';
        const homePageConfig = window.HOME_PAGE || {};
        let currentShowcaseCategory = homePageConfig.defaultShowcaseCategorySlug || 'all';
        const HOME_SPOTLIGHT_PRODUCT_ID = homePageConfig.spotlightProductId || null;
        const HOME_CATEGORY_PRODUCT_LIMIT = 12;

        function findHomeCategory(slug) {
            if (slug === 'all') {
                return { id: null, slug: 'all', name: 'All' };
            }

            return (homePageConfig.categories || []).find((category) => category.slug === slug) || null;
        }

        function normalizeCategoryLabel(value) {
            return String(value || '').trim().toLowerCase().replace(/[^a-z0-9]+/g, '');
        }

        function productMatchesCategory(product, slug) {
            if (slug === 'all') {
                return true;
            }

            const category = findHomeCategory(slug);
            if (!category) {
                return false;
            }

            if (category.id && product.categoryId) {
                return Number(product.categoryId) === Number(category.id);
            }

            if (category.slug && product.categorySlug) {
                return product.categorySlug === category.slug;
            }

            return normalizeCategoryLabel(product.category) === normalizeCategoryLabel(category.name);
        }

        function setupHomeCategoryTabs() {
            document.querySelectorAll('[data-showcase-tab]').forEach((button) => {
                button.addEventListener('click', () => {
                    switchCategoryShowcase(button.dataset.showcaseTab || 'all');
                });
            });

            document.querySelectorAll('[data-flash-tab]').forEach((button) => {
                button.addEventListener('click', () => {
                    filterFlashDeals(button.dataset.flashTab || 'all');
                });
            });

            document.querySelectorAll('[data-trend-tab]').forEach((button) => {
                button.addEventListener('click', () => {
                    setFilterTab(button.dataset.trendTab || 'all');
                });
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            if (window.BeautageCart) {
                cart = BeautageCart.readStorage();
                BeautageCart.updateCartUI();
            }
            setupHomeCategoryTabs();
            renderFlashDeals(currentFlashFilter);
            renderCategoryShowcase(currentShowcaseCategory);
            renderProducts(products);
            setupSearch();
            lucide.createIcons();
            startCountdownTimer();
        });

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-beautage-purple text-white px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold flex items-center gap-3 border border-purple-500/40 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300';
            toast.innerHTML = `<i data-lucide="sparkles" class="w-4 h-4 text-beautage-amber shrink-0"></i><span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => {
                toast.classList.remove('translate-y-4', 'opacity-0');
            }, 10);

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

        function switchCategoryShowcase(categorySlug) {
            currentShowcaseCategory = categorySlug;
            document.querySelectorAll('.showcase-tab').forEach(tab => {
                tab.classList.remove('bg-beautage-purple', 'text-white', 'shadow-sm');
                tab.classList.add('bg-purple-50', 'text-beautage-purple');
            });
            const activeBtn = document.getElementById(`showcase-tab-${categorySlug}`);
            if(activeBtn) {
                activeBtn.classList.remove('bg-purple-50', 'text-beautage-purple');
                activeBtn.classList.add('bg-beautage-purple', 'text-white', 'shadow-sm');
            }
            renderCategoryShowcase(categorySlug);
        }

        function renderCategoryShowcase(categorySlug) {
            const container = document.getElementById('showcase-grid');
            const items = products
                .filter((product) => productMatchesCategory(product, categorySlug))
                .slice(0, HOME_CATEGORY_PRODUCT_LIMIT);

            if (items.length === 0) {
                container.innerHTML = '<div class="col-span-full py-12 text-center text-slate-500 text-sm">No products in this category yet. Browse other tabs or check back soon.</div>';
                return;
            }

            container.innerHTML = items.map(p => productCardTemplate(p)).join('');
            lucide.createIcons();
        }

        function filterFlashDeals(categorySlug) {
            currentFlashFilter = categorySlug;
            document.querySelectorAll('.flash-filter-btn').forEach(btn => {
                btn.classList.remove('bg-beautage-pink', 'text-white', 'shadow-md', 'shadow-pink-500/30');
                btn.classList.add('bg-purple-900/80', 'text-purple-200', 'border', 'border-purple-700/60');
            });
            const activeBtn = document.getElementById(`flash-tab-${categorySlug}`);
            if(activeBtn) {
                activeBtn.classList.remove('bg-purple-900/80', 'text-purple-200', 'border', 'border-purple-700/60');
                activeBtn.classList.add('bg-beautage-pink', 'text-white', 'shadow-md', 'shadow-pink-500/30');
            }
            renderFlashDeals(categorySlug);
        }

        function renderFlashDeals(categorySlug) {
            const container = document.getElementById('flash-deals-grid-container');
            let flashItems = products.filter(p => p.flash);
            if(categorySlug !== 'all') {
                flashItems = flashItems.filter((product) => productMatchesCategory(product, categorySlug));
            }
            const spotlightId = HOME_SPOTLIGHT_PRODUCT_ID;
            const gridItems = flashItems
                .filter(p => !spotlightId || p.id !== spotlightId)
                .slice(0, 6);

            if(gridItems.length === 0) {
                container.innerHTML = `<div class="col-span-full py-12 text-center text-purple-200 text-xs font-semibold">No flash deals currently available in this category. Check back soon!</div>`;
                return;
            }

            container.innerHTML = gridItems.map(p => {
                const isWishlisted = wishlist.some(item => item.id === p.id);
                return `
                    <div class="bg-purple-950/80 rounded-2xl p-4 border border-purple-800 hover:border-pink-500 transition-all duration-300 shadow-xl flex flex-col justify-between group backdrop-blur-md">
                        <div class="relative rounded-xl overflow-hidden bg-purple-900 mb-3 aspect-square cursor-pointer" onclick="openQuickView(${p.id})">
                            <img src="${p.image}" alt="${p.name}" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0">
                            <img src="${p.hoverImage}" alt="${p.name} Alt" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100 group-hover:scale-105">
                            
                            <span class="absolute top-2.5 left-2.5 bg-gradient-to-r from-beautage-pink to-amber-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full uppercase tracking-wider shadow z-10">
                                ${p.discount}
                            </span>
                            <button onclick="event.stopPropagation(); toggleWishlist(${p.id})" class="absolute top-2.5 right-2.5 p-2 rounded-xl bg-purple-950/80 text-purple-200 hover:text-pink-300 shadow transition z-10">
                                <i data-lucide="heart" class="w-4 h-4 ${isWishlisted ? 'fill-beautage-pink text-beautage-pink' : ''}"></i>
                            </button>
                        </div>

                        <div>
                            <div class="flex items-center justify-between text-[10px] font-bold text-pink-300 uppercase tracking-widest mb-1">
                                <span>${p.category}</span>
                                <span class="text-amber-300 flex items-center gap-1">⭐ ${p.rating}</span>
                            </div>
                            <h3 class="text-xs font-bold text-white line-clamp-1 group-hover:text-pink-300 transition">${p.name}</h3>
                            
                            <!-- Scarcity Bar -->
                            <div class="mt-3 space-y-1">
                                <div class="flex justify-between text-[10px] font-semibold text-purple-200">
                                    <span class="text-amber-300">${p.claimedPercent}% Claimed</span>
                                    <span>${p.claimedText}</span>
                                </div>
                                <div class="w-full h-1.5 rounded-full bg-purple-900 overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-beautage-pink to-amber-400 rounded-full" style="width: ${p.claimedPercent}%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-purple-900 flex items-center justify-between">
                            <div>
                                <div class="flex items-baseline gap-1.5">
                                    <span class="text-base font-black text-white">${BeautageMoney.formatDecimal(p.price)}</span>
                                    <span class="text-[11px] text-purple-400 line-through">${BeautageMoney.formatDecimal(p.oldPrice)}</span>
                                </div>
                            </div>
                            <button onclick="addToCart(${p.id})" class="px-3 py-2 rounded-xl bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-extrabold transition shadow-md shadow-pink-500/20 flex items-center gap-1">
                                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i> Claim
                            </button>
                        </div>
                    </div>
                `;
            }).join('');
            lucide.createIcons();
        }

        function renderProducts(items) {
            const container = document.getElementById('product-grid');
            const limited = items.slice(0, HOME_CATEGORY_PRODUCT_LIMIT);
            if (limited.length === 0) {
                container.innerHTML = `<div class="col-span-full py-12 text-center text-slate-500 text-sm">No products found matching your search or filter.</div>`;
                return;
            }
            container.innerHTML = limited.map(p => productCardTemplate(p)).join('');
            lucide.createIcons();
        }

        function productCardTemplate(p) {
            const isWishlisted = wishlist.some(item => item.id === p.id);
            const productUrl = p.slug ? `/products/${p.slug}` : '#';
            return `
                <div class="bg-white rounded-2xl p-4 border border-purple-100 hover:border-beautage-pink transition-all duration-300 shadow-sm hover:shadow-2xl hover:-translate-y-1.5 flex flex-col justify-between group">
                    <div class="relative rounded-xl overflow-hidden bg-purple-50 mb-4 aspect-square cursor-pointer" onclick="openQuickView(${p.id})">
                        <img src="${p.image}" alt="${p.name}" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 ease-in-out group-hover:opacity-0" onerror="this.onerror=null; this.src='https://placehold.co/400x400/5E1F70/FFFFFF?text=Quick+Bazar';">
                        <img src="${p.hoverImage}" alt="${p.name} Alternate View" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 ease-in-out group-hover:opacity-100 group-hover:scale-105" onerror="this.onerror=null; this.src='https://placehold.co/400x400/3B0764/FFFFFF?text=Quick+Bazar+Alt';">
                        
                        <span class="absolute top-2.5 left-2.5 bg-beautage-purple text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase tracking-wider shadow z-10">
                            ${p.badge}
                        </span>
                        <button onclick="event.stopPropagation(); toggleWishlist(${p.id})" class="absolute top-2.5 right-2.5 p-2 rounded-xl bg-white/90 backdrop-blur-sm text-slate-700 hover:text-beautage-pink shadow transition z-10">
                            <i data-lucide="heart" class="w-4 h-4 ${isWishlisted ? 'fill-beautage-pink text-beautage-pink' : ''}"></i>
                        </button>
                    </div>

                    <div>
                        <span class="text-[10px] uppercase font-bold text-beautage-pink tracking-widest">${p.category}</span>
                        <h3 class="text-sm font-bold text-beautage-purple mt-1 line-clamp-1 group-hover:text-beautage-pink transition"><a href="${productUrl}" class="hover:text-beautage-pink" onclick="event.stopPropagation()">${p.name}</a></h3>
                        <p class="text-xs text-slate-500 mt-1 line-clamp-2">${p.desc}</p>
                    </div>

                    <div class="mt-4 pt-3 border-t border-purple-50 flex items-center justify-between">
                        <div>
                            <div class="flex items-baseline gap-1.5">
                                <span class="text-base font-black text-slate-800">${BeautageMoney.formatDecimal(p.price)}</span>
                                <span class="text-xs text-slate-400 line-through">${BeautageMoney.formatDecimal(p.oldPrice)}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="openQuickView(${p.id})" class="p-2 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple transition" title="Quick View">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </button>
                            <button onclick="addToCart(${p.id})" class="px-3.5 py-2 rounded-xl bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-bold transition shadow-sm shadow-pink-500/20 flex items-center gap-1">
                                <i data-lucide="shopping-bag" class="w-3.5 h-3.5"></i> Add
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function filterCategory(categorySlug) {
            currentFilter = categorySlug;
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('bg-beautage-purple', 'text-white');
                btn.classList.add('bg-white', 'text-slate-700', 'border', 'border-purple-100');
            });
            const activeTab = document.getElementById(`tab-${categorySlug}`);
            if(activeTab) {
                activeTab.classList.remove('bg-white', 'text-slate-700', 'border', 'border-purple-100');
                activeTab.classList.add('bg-beautage-purple', 'text-white');
            }

            if(categorySlug === 'all') {
                renderProducts(products);
            } else {
                const filtered = products.filter((product) => productMatchesCategory(product, categorySlug));
                renderProducts(filtered);
            }
        }

        function setFilterTab(categorySlug) {
            filterCategory(categorySlug);
            const section = document.getElementById('trending-section');
            section.scrollIntoView({ behavior: 'smooth' });
        }

        function toggleMobileSearch() {
            const searchContainer = document.getElementById('mobile-search-container');
            searchContainer.classList.toggle('hidden');
            if(!searchContainer.classList.contains('hidden')) {
                document.getElementById('mobile-search-input').focus();
            }
        }

        function setupSearch() {
            const input = document.getElementById('main-search-input');
            const mobileInput = document.getElementById('mobile-search-input');
            const clearBtn = document.getElementById('clear-search');
            const resultsBox = document.getElementById('search-results');
            const categorySelect = document.getElementById('search-category');

            function handleSearchQuery(query) {
                if(query.length > 0) {
                    if(clearBtn) clearBtn.classList.remove('hidden');
                    let matches = products.filter(p => p.name.toLowerCase().includes(query) || p.desc.toLowerCase().includes(query));
                    if(categorySelect && categorySelect.value !== 'all') {
                        matches = matches.filter((product) => productMatchesCategory(product, categorySelect.value));
                    }

                    if(matches.length > 0) {
                        resultsBox.classList.remove('hidden');
                        resultsBox.innerHTML = matches.map(p => `
                            <div onclick="selectSearchResult(${p.id})" class="flex items-center gap-3 p-2 rounded-xl hover:bg-purple-50 cursor-pointer transition">
                                <img src="${p.image}" class="w-10 h-10 object-cover rounded-lg">
                                <div>
                                    <h5 class="text-xs font-bold text-beautage-purple">${p.name}</h5>
                                    <span class="text-[10px] text-slate-500">${BeautageMoney.formatDecimal(p.price)} • ${p.category}</span>
                                </div>
                            </div>
                        `).join('');
                    } else {
                        resultsBox.classList.remove('hidden');
                        resultsBox.innerHTML = `<div class="p-3 text-xs text-slate-500 text-center">No beauty items found for "${query}".</div>`;
                    }
                } else {
                    if(clearBtn) clearBtn.classList.add('hidden');
                    resultsBox.classList.add('hidden');
                }
            }

            if(input) input.addEventListener('input', (e) => handleSearchQuery(e.target.value.toLowerCase().trim()));
            if(mobileInput) mobileInput.addEventListener('input', (e) => handleSearchQuery(e.target.value.toLowerCase().trim()));

            if(clearBtn) {
                clearBtn.addEventListener('click', () => {
                    if(input) input.value = '';
                    if(mobileInput) mobileInput.value = '';
                    clearBtn.classList.add('hidden');
                    resultsBox.classList.add('hidden');
                    renderProducts(products);
                });
            }

            document.addEventListener('click', (e) => {
                if(input && !input.contains(e.target) && mobileInput && !mobileInput.contains(e.target) && !resultsBox.contains(e.target)) {
                    resultsBox.classList.add('hidden');
                }
            });
        }

        function selectSearchResult(id) {
            document.getElementById('search-results').classList.add('hidden');
            document.getElementById('mobile-search-container').classList.add('hidden');
            openQuickView(id);
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
                return;
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
            renderProducts(currentFilter === 'all' ? products : products.filter((product) => productMatchesCategory(product, currentFilter)));
            renderFlashDeals(currentFlashFilter);
            renderCategoryShowcase(currentShowcaseCategory);
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

            const productUrl = product.slug ? `/products/${product.slug}` : '#';
            content.innerHTML = `
                <div class="aspect-square rounded-2xl overflow-hidden bg-purple-50">
                    <img src="${product.image}" class="w-full h-full object-cover">
                </div>
                <div class="space-y-4">
                    <span class="px-2.5 py-1 bg-purple-100 text-beautage-purple text-[10px] font-bold rounded-full uppercase">${product.category}</span>
                    <h2 class="text-xl font-serif font-bold text-beautage-purple"><a href="${productUrl}" class="hover:text-beautage-pink">${product.name}</a></h2>
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
                        <a href="${productUrl}" class="flex-1 text-center bg-purple-50 hover:bg-purple-100 text-beautage-purple font-bold py-3 rounded-xl transition text-xs uppercase inline-flex items-center justify-center">
                            View Product
                        </a>
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
            let flashSeconds = 5 * 3600 + 42 * 60 + 18;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                if(flashSeconds > 0) flashSeconds--;

                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                
                const fh = Math.floor(flashSeconds / 3600).toString().padStart(2, '0');
                const fm = Math.floor((flashSeconds % 3600) / 60).toString().padStart(2, '0');
                const fs = (flashSeconds % 60).toString().padStart(2, '0');

                const ann = document.getElementById('announcement-timer');
                if (ann) ann.innerText = `${h}:${m}:${s}`;

                const flashHours = document.getElementById('flash-timer-hours');
                const flashMinutes = document.getElementById('flash-timer-minutes');
                const flashSeconds = document.getElementById('flash-timer-seconds');
                if (flashHours) flashHours.innerText = fh;
                if (flashMinutes) flashMinutes.innerText = fm;
                if (flashSeconds) flashSeconds.innerText = fs;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
