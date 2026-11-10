@extends('layouts.app')

@section('title', ($pageSeo['title'] ?? null) ?: ($product->name.' | Quick Bazar'))

@section('body_attrs')
class="lumiere-v2 qh-pdp bg-gray-50 text-gray-800 font-sans antialiased min-h-screen flex flex-col selection:bg-emerald-100 selection:text-slate-900"
@endsection

@push('head-preload')
    @php
        $lcpImage = $galleryImages[0] ?? $product->image;
    @endphp
    @if (filled($lcpImage))
    <link rel="preload" as="image" href="{{ \Illuminate\Support\Str::startsWith($lcpImage, ['http://', 'https://']) ? $lcpImage : url($lcpImage) }}" fetchpriority="high">
    @endif
@endpush

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/pdp.css') }}?v=2026111044">
@endpush

@push('structured-data')
@include('partials.product-json-ld', ['product' => $product, 'pageSeo' => $pageSeo, 'galleryImages' => $galleryImages ?? []])
@php
    $breadcrumbCrumbs = [
        ['name' => 'Home', 'url' => route('home')],
        ['name' => 'Collections & Categories', 'url' => route('collections')],
    ];
    if ($product->productCategory) {
        $breadcrumbCrumbs[] = [
            'name' => $product->productCategory->displayNavLabel(),
            'url' => $product->productCategory->collectionsUrl(),
        ];
    } elseif (filled($product->category)) {
        $breadcrumbCrumbs[] = ['name' => $product->category, 'url' => route('collections')];
    }
    $breadcrumbCrumbs[] = ['name' => $product->name, 'url' => route('product.show', $product)];
    $breadcrumbJsonLd = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => collect($breadcrumbCrumbs)->values()->map(fn ($crumb, $index) => [
            '@type' => 'ListItem',
            'position' => $index + 1,
            'name' => $crumb['name'],
            'item' => $crumb['url'],
        ])->all(),
    ];
@endphp
@include('partials.json-ld-script', ['jsonLd' => $breadcrumbJsonLd])
@endpush

@section('content')
@php
    use App\Support\MoneyFormat;
    $productSave = max(0, (float) $product->old_price - (float) $product->price);
    $freeShipAt = (float) \App\Support\CheckoutSettings::all()['free_shipping_threshold'];
    $whatsappOrder = \App\Support\LiveChatSettings::whatsappUrl('Hi, I want to order '.$product->name.' from Quick Bazar.');
    $deliveryStart = now('Asia/Karachi')->addDays(2);
    $deliveryEnd = now('Asia/Karachi')->addDays(4);
@endphp
<main class="pdp">
        <nav class="pdp-crumb" aria-label="Breadcrumb">
            <a href="{{ route('home') }}">Home</a>
            <span aria-hidden="true">/</span>
            <a href="{{ route('collections') }}">Collections</a>
            @if ($product->productCategory)
                <span aria-hidden="true">/</span>
                <a href="{{ $product->productCategory->collectionsUrl() }}">{{ $product->productCategory->displayNavLabel() }}</a>
            @elseif (filled($product->category))
                <span aria-hidden="true">/</span>
                <a href="{{ route('collections') }}">{{ $product->category }}</a>
            @endif
            <span aria-hidden="true">/</span>
            <span>{{ $product->name }}</span>
        </nav>

        <section id="pdp-main-section" class="pdp-stage">
            <div id="pdp-main-grid" class="pdp-grid">
                <div id="pdp-gallery-col" class="pdp-gallery">
                    <div id="pdp-gallery-pin" class="pdp-gallery-pin">
                        <div class="pdp-thumbs">
                            @foreach ($galleryImages as $index => $galleryUrl)
                            <button type="button" onclick="switchMainImage({{ $index }})" aria-label="Show product image {{ $index + 1 }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}" class="thumb-btn {{ $index === 0 ? 'is-on' : '' }}">
                                <img src="{{ $galleryUrl }}" alt="{{ $product->name }} — view {{ $index + 1 }}" width="72" height="72" loading="lazy" decoding="async">
                            </button>
                            @endforeach
                            @if ($product->video_url)
                            <button type="button" onclick="openVideoModal()" aria-label="Play product video" class="pdp-video">
                                <i data-lucide="play-circle" class="w-5 h-5"></i>
                                <span>Video</span>
                            </button>
                            @endif
                        </div>
                        <div id="pdp-gallery-stage" class="pdp-gallery-main">
                            <img id="main-product-img" src="{{ $galleryImages[0] ?? $product->image }}" alt="{{ $product->name }}" width="800" height="800" fetchpriority="high" decoding="async" onclick="openLightbox()">
                            @if ($product->badge)
                            <span class="pdp-badge">{{ $product->badge }}</span>
                            @endif
                            <button type="button" onclick="openLightbox()" class="pdp-zoom">Zoom</button>
                        </div>
                    </div>
                </div>

                <div id="pdp-buy-column" class="pdp-panel">
                    <p class="pdp-kicker">{{ $product->productCategory?->displayNavLabel() ?: ($product->category ?: 'Quick Bazar') }}</p>
                    <div class="pdp-flags">
                        <span>100% original</span>
                        @if ($product->promo_label)
                        <span>{{ $product->promo_label }}</span>
                        @endif
                        <span>{{ $product->in_stock ? 'In stock' : 'Out of stock' }}</span>
                    </div>
                    <h1>{{ $product->name }}</h1>
                    @if ($product->short_description)
                    <p class="pdp-lead">{{ $product->short_description }}</p>
                    @elseif ($product->description)
                    <p class="pdp-lead">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 220) }}</p>
                    @endif
                    <div class="pdp-rating">
                        @include('partials.product-star-rating', ['rating' => $product->rating, 'size' => 'w-4 h-4', 'showValue' => true])
                        <a href="#customer-reviews" onclick="switchTab('reviews')">{{ number_format((int) $product->reviews) }} verified reviews</a>
                    </div>

                    <div class="pdp-price">
                        <div>
                            <span>Price</span>
                            <strong>{{ MoneyFormat::pkr($product->price) }}</strong>
                            @if ($productSave > 0)
                            <s>{{ MoneyFormat::pkr($product->old_price) }}</s>
                            @endif
                        </div>
                        @if ($productSave > 0)
                        <em>Save {{ MoneyFormat::pkr($productSave) }}</em>
                        @endif
                    </div>

                    <p class="pdp-offer">Offer ends in <strong id="pdp-countdown">04:32:19</strong></p>

                    <div id="pdp-buy-actions" class="pdp-actions">
                        <div class="pdp-qty">
                            <button type="button" onclick="adjustQty(-1)" aria-label="Decrease quantity">−</button>
                            <span id="product-qty" aria-live="polite" aria-label="Quantity">1</span>
                            <button type="button" onclick="adjustQty(1)" aria-label="Increase quantity">+</button>
                        </div>
                        <button type="button" onclick="addCurrentProductToCart()" class="pdp-add">Add to bag · <span id="btn-total-price">{{ MoneyFormat::pkr($product->price) }}</span></button>
                        <button type="button" onclick="buyNow()" class="pdp-buy-now">Buy now</button>
                    </div>

                    <ul class="pdp-points">
                        <li>Cash on delivery across Pakistan</li>
                        <li>{{ $freeShipAt > 0 ? 'Free delivery over '.MoneyFormat::pkr($freeShipAt) : 'Delivery shown at checkout' }}</li>
                        <li>30-day returns on eligible items</li>
                    </ul>

                    <div class="pdp-delivery">
                        <label for="postal-input">Check delivery</label>
                        <div>
                            <input type="text" id="postal-input" name="postal_code" inputmode="numeric" autocomplete="postal-code" placeholder="Postal code, e.g. 54000">
                            <button type="button" onclick="checkDelivery()">Check</button>
                        </div>
                        <p id="delivery-result" hidden></p>
                    </div>

                    <div class="pdp-eta">
                        <div>
                            <span>Estimated home delivery</span>
                            <strong>{{ $deliveryStart->format('j M') }} – {{ $deliveryEnd->format('j M') }}</strong>
                            <small>Cash on delivery available across Pakistan</small>
                        </div>
                        @if ($whatsappOrder)
                        <a class="pdp-wa" href="{{ $whatsappOrder }}" target="_blank" rel="noopener">Order on WhatsApp</a>
                        @endif
                    </div>
                </div>
            </div>
        </section>

        <section class="pdp-know" aria-label="Good to know">
            <details open>
                <summary>Product overview</summary>
                <p>{{ $product->short_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $product->description), 280) }}</p>
            </details>
            <details>
                <summary>How to place your order</summary>
                <p>Add {{ $product->name }} to the bag, or choose Buy now. Enter your name, city, and phone at checkout. Cash on delivery is available when your city is eligible.</p>
            </details>
            <details>
                <summary>7-day easy returns</summary>
                <p>Unused items in original packaging can be returned. Keep the order number and follow the returns page.</p>
                <a href="{{ route('returns') }}">Returns policy</a>
            </details>
        </section>

        @include('partials.product-pdp-tabs')

        @include('partials.product-bundle-section', ['bundlePayload' => $bundlePayload ?? null])

        <section class="pdp-related" aria-labelledby="pdp-related-title">
            <div class="pdp-related-head">
                <p class="pdp-kicker">More from this shelf</p>
                <h2 id="pdp-related-title">You may also like</h2>
            </div>

            <div id="related-product-grid" class="lv2-grid pdp-related-grid"></div>
        </section>

    </main>
@endsection

@section('below_main')
    <!-- Sticky checkout — slides up when inline buy buttons leave the viewport -->
    <div id="pdp-sticky-checkout" class="fixed inset-x-0 z-[45] max-md:bottom-[3.65rem] md:bottom-0 translate-y-full opacity-0 pointer-events-none transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" aria-hidden="true">
        <div class="border-t border-purple-200/80 bg-white/95 backdrop-blur-xl shadow-[0_-12px_40px_rgba(94,31,112,0.14)]">
            <div class="w-full px-4 py-3 sm:px-6 md:py-3.5 lg:px-8">
                <div class="flex flex-col gap-2.5 md:flex-row md:items-center md:gap-4">
                    <div class="flex items-center gap-3 min-w-0 md:flex-1">
                        <img src="{{ $galleryImages[0] ?? $product->image }}" alt="{{ $product->name }}" class="hidden sm:block w-11 h-11 rounded-xl object-cover border border-purple-100 shadow-sm shrink-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase tracking-wider font-bold text-beautage-pink leading-none mb-0.5">Ready to order</p>
                            <p class="text-xs sm:text-sm font-bold text-beautage-purple truncate">{{ $product->name }}</p>
                            <p class="text-sm font-black text-slate-900 mt-0.5"><span id="sticky-btn-total-price">{{ MoneyFormat::pkr($product->price) }}</span></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3 w-full md:w-auto md:shrink-0">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex items-center border-2 border-purple-200 rounded-xl bg-white overflow-hidden shadow-sm shrink-0">
                                <button type="button" onclick="adjustQty(-1)" class="px-3 py-2.5 text-sm font-extrabold text-slate-600 hover:bg-purple-50 transition" aria-label="Decrease quantity">−</button>
                                <span id="sticky-product-qty" class="w-8 text-center text-sm font-bold text-slate-800">1</span>
                                <button type="button" onclick="adjustQty(1)" class="px-3 py-2.5 text-sm font-extrabold text-slate-600 hover:bg-purple-50 transition" aria-label="Increase quantity">+</button>
                            </div>
                            <button type="button" onclick="addCurrentProductToCart()" class="flex-1 sm:flex-none sm:min-w-[11rem] bg-beautage-pink hover:bg-beautage-pinkHover text-white font-black py-3 px-4 sm:px-5 rounded-xl transition shadow-lg shadow-pink-500/25 flex items-center justify-center gap-2 text-[11px] sm:text-xs uppercase tracking-wider">
                                <i data-lucide="shopping-bag" class="w-4 h-4 shrink-0"></i>
                                <span class="truncate">Add To Bag</span>
                            </button>
                        </div>
                        <button type="button" onclick="buyNow()" class="w-full sm:w-auto sm:min-w-[14rem] bg-gradient-to-r from-beautage-purple to-beautage-purpleDark hover:from-beautage-purpleDark hover:to-beautage-purple text-white font-extrabold py-3 px-5 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-[11px] sm:text-xs uppercase tracking-wider">
                            <i data-lucide="zap" class="w-4 h-4 text-amber-300 shrink-0"></i>
                            <span class="truncate">Buy Now — Express</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="lightbox-modal" class="hidden fixed inset-0 z-[60] bg-slate-900/90 flex items-center justify-center p-4" onclick="closeLightbox()">
        <button type="button" class="absolute top-4 right-4 text-white p-2 rounded-full bg-white/10 hover:bg-white/20 min-h-[44px] min-w-[44px]" onclick="closeLightbox()" aria-label="Close zoomed image">
            <i data-lucide="x" class="w-6 h-6" aria-hidden="true"></i>
        </button>
        <img id="lightbox-img" src="" alt="{{ $product->name }}" width="800" height="800" class="max-w-full max-h-[85vh] rounded-2xl shadow-2xl object-contain" onclick="event.stopPropagation()">
    </div>

    @if ($product->video_url)
    <div id="video-modal" class="hidden fixed inset-0 z-[60] bg-slate-900/90 flex items-center justify-center p-4" onclick="closeVideoModal()">
        <button type="button" class="absolute top-4 right-4 text-white p-2 rounded-full bg-white/10 hover:bg-white/20" onclick="closeVideoModal()">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
        <div class="w-full max-w-3xl aspect-video rounded-2xl overflow-hidden shadow-2xl bg-black" onclick="event.stopPropagation()">
            <iframe id="video-iframe" src="" title="Product video" class="w-full h-full" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
        </div>
    </div>
    @endif
@endsection

@push('scripts')
@include('partials.scripts.product-detail-data')
<script src="{{ asset('js/pdp-pricing.js') }}?v=2026110817" defer></script>
<script>
    const BEAUTAGE_REVIEW_STORE_URL = @json(route('product.reviews.store', $product));
    const BEAUTAGE_PRODUCT_VIDEO_URL = @json($product->video_url);
    const BEAUTAGE_BUNDLE = @json($bundlePayload ?? null);
    window.BEAUTAGE_PRODUCT_SLUG = @json($product->slug);
</script>
@verbatim
<script>
        
        let cart = [];
        let wishlist = [];
        document.addEventListener('DOMContentLoaded', () => {
            if (window.BeautageCart) {
                cart = BeautageCart.readStorage();
                BeautageCart.updateCartUI();
            }
            if (window.BeautageRecentlyViewed && window.currentProduct) {
                BeautageRecentlyViewed.record(window.currentProduct);
                BeautageRecentlyViewed.renderAll();
            }
            renderRelatedProducts();
            lucide.createIcons();
            startCountdownTimer();
            if (BEAUTAGE_BUNDLE) {
                updateBundleTotal();
            }
            setReviewRating(Number(document.getElementById('review-rating-input')?.value || 5));
            initPdpDesktopScroll();
            initPdpStickyCheckout();
            if (window.BeautagePdpPricing) {
                BeautagePdpPricing.init();
            }
        });

        function initPdpStickyCheckout() {
            const anchor = document.getElementById('pdp-buy-actions');
            const bar = document.getElementById('pdp-sticky-checkout');
            if (!anchor || !bar || typeof IntersectionObserver === 'undefined') {
                return;
            }

            const setVisible = (visible) => {
                bar.classList.toggle('translate-y-full', !visible);
                bar.classList.toggle('opacity-0', !visible);
                bar.classList.toggle('pointer-events-none', !visible);
                bar.classList.toggle('translate-y-0', visible);
                bar.classList.toggle('opacity-100', visible);
                bar.setAttribute('aria-hidden', visible ? 'false' : 'true');
                if (visible) {
                    lucide.createIcons();
                }
            };

            const observer = new IntersectionObserver((entries) => {
                const entry = entries[0];
                if (!entry) {
                    return;
                }
                setVisible(!entry.isIntersecting);
            }, {
                root: null,
                threshold: 0,
                rootMargin: '-72px 0px 0px 0px',
            });

            observer.observe(anchor);
        }

        function initPdpDesktopScroll() {
            const mq = window.matchMedia('(min-width: 1024px)');
            const buy = document.getElementById('pdp-buy-column');
            const section = document.getElementById('pdp-main-section');
            const galleryCol = document.getElementById('pdp-gallery-col');
            if (!buy || !section) {
                return;
            }

            const resetBuyScrollMode = () => {
                if (!mq.matches) {
                    buy.style.maxHeight = '';
                    buy.style.overflowY = '';
                }
            };
            resetBuyScrollMode();
            mq.addEventListener('change', resetBuyScrollMode);

            const wheelDelta = (e) => {
                if (e.deltaMode === 1) {
                    return e.deltaY * 20;
                }
                if (e.deltaMode === 2) {
                    return e.deltaY * buy.clientHeight * 0.92;
                }
                return e.deltaY * 1.85;
            };

            const buyScrollMax = () => Math.max(0, buy.scrollHeight - buy.clientHeight);

            const sectionActive = () => {
                const rect = section.getBoundingClientRect();
                return rect.bottom > 96 && rect.top < window.innerHeight - 72;
            };

            window.addEventListener('wheel', (e) => {
                if (!mq.matches || !sectionActive()) {
                    return;
                }

                const max = buyScrollMax();
                if (max <= 1) {
                    return;
                }

                const delta = wheelDelta(e);
                if (delta === 0) {
                    return;
                }

                const goingDown = delta > 0;
                const atBottom = buy.scrollTop >= max - 2;
                const atTop = buy.scrollTop <= 0;
                const overBuy = buy.contains(e.target);
                const overGallery = galleryCol?.contains(e.target);

                if (overBuy) {
                    if ((goingDown && atBottom) || (!goingDown && atTop)) {
                        return;
                    }
                    return;
                }

                if (overGallery || section.contains(e.target)) {
                    if (goingDown && !atBottom) {
                        e.preventDefault();
                        buy.scrollTop = Math.min(max, buy.scrollTop + delta);
                    } else if (!goingDown && !atTop) {
                        e.preventDefault();
                        buy.scrollTop = Math.max(0, buy.scrollTop + delta);
                    }
                }
            }, { passive: false });
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

        function switchMainImage(index) {
            const mainImg = document.getElementById('main-product-img');
            const images = window.galleryImages || [];
            if (!images[index]) {
                return;
            }
            mainImg.src = images[index];
            document.querySelectorAll('.thumb-btn').forEach((btn, idx) => {
                const on = idx === index;
                btn.classList.toggle('is-on', on);
                btn.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
        }

        function openLightbox() {
            const modal = document.getElementById('lightbox-modal');
            const lightboxImg = document.getElementById('lightbox-img');
            lightboxImg.src = document.getElementById('main-product-img').src;
            modal.classList.remove('hidden');
        }

        function closeLightbox() {
            document.getElementById('lightbox-modal').classList.add('hidden');
        }

        function openVideoModal() {
            const modal = document.getElementById('video-modal');
            const iframe = document.getElementById('video-iframe');
            if (!modal || !iframe || !BEAUTAGE_PRODUCT_VIDEO_URL) {
                return;
            }
            iframe.src = BEAUTAGE_PRODUCT_VIDEO_URL;
            modal.classList.remove('hidden');
            lucide.createIcons();
        }

        function closeVideoModal() {
            const modal = document.getElementById('video-modal');
            const iframe = document.getElementById('video-iframe');
            if (modal) {
                modal.classList.add('hidden');
            }
            if (iframe) {
                iframe.src = '';
            }
        }

        function openReviewModal() {
            switchTab('reviews');
            setTimeout(() => {
                document.getElementById('product-review-form')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
                document.getElementById('review-author-name')?.focus();
            }, 150);
        }

        function setReviewRating(value) {
            const rating = Math.max(1, Math.min(5, Number(value)));
            const input = document.getElementById('review-rating-input');
            if (input) {
                input.value = String(rating);
            }
            document.querySelectorAll('.review-star-btn').forEach((btn) => {
                const star = Number(btn.getAttribute('data-review-star'));
                const icon = btn.querySelector('[data-lucide="star"]');
                if (!icon) {
                    return;
                }
                if (star <= rating) {
                    icon.classList.add('fill-current', 'text-amber-400');
                    icon.classList.remove('text-amber-300');
                } else {
                    icon.classList.remove('fill-current', 'text-amber-400');
                    icon.classList.add('text-amber-300');
                }
            });
        }

        function previewReviewPhotos(input) {
            const preview = document.getElementById('review-photo-preview');
            if (!preview || !input?.files) {
                return;
            }
            preview.innerHTML = '';
            const files = Array.from(input.files).slice(0, 3);
            if (files.length !== input.files.length) {
                showToast('Only the first 3 photos will be uploaded.');
            }
            const dt = new DataTransfer();
            files.forEach((file) => dt.items.add(file));
            input.files = dt.files;

            files.forEach((file, index) => {
                const url = URL.createObjectURL(file);
                const wrap = document.createElement('div');
                wrap.className = 'relative aspect-square rounded-xl overflow-hidden border border-purple-100';
                wrap.innerHTML = `
                    <img src="${url}" alt="Preview ${index + 1}" class="w-full h-full object-cover">
                    <button type="button" class="absolute top-1 right-1 w-6 h-6 rounded-full bg-slate-900/70 text-white text-xs flex items-center justify-center" aria-label="Remove photo">×</button>
                `;
                wrap.querySelector('button')?.addEventListener('click', () => {
                    URL.revokeObjectURL(url);
                    const next = Array.from(input.files).filter((_, i) => i !== index);
                    const transfer = new DataTransfer();
                    next.forEach((f) => transfer.items.add(f));
                    input.files = transfer.files;
                    previewReviewPhotos(input);
                });
                preview.appendChild(wrap);
            });
            lucide.createIcons();
        }

        function clearReviewPhotoPreview() {
            const preview = document.getElementById('review-photo-preview');
            const input = document.getElementById('review-photos');
            if (preview) {
                preview.innerHTML = '';
            }
            if (input) {
                input.value = '';
            }
        }

        async function handleReviewSubmit(e) {
            e.preventDefault();
            const form = e.target;
            const btn = document.getElementById('review-submit-btn');
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const formData = new FormData(form);

            if (btn) {
                btn.disabled = true;
                btn.innerText = 'Submitting…';
            }

            try {
                const response = await fetch(BEAUTAGE_REVIEW_STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': token || '',
                    },
                    body: formData,
                });
                const data = await response.json().catch(() => ({}));
                if (!response.ok) {
                    const firstError = data.errors ? Object.values(data.errors).flat()[0] : null;
                    throw new Error(firstError || data.message || 'Could not submit review.');
                }
                form.reset();
                clearReviewPhotoPreview();
                setReviewRating(5);
                showToast(data.message || 'Review submitted successfully.');
                if (data.published) {
                    setTimeout(() => window.location.reload(), 1200);
                }
            } catch (err) {
                showToast(err.message || 'Something went wrong. Please try again.');
            } finally {
                if (btn) {
                    btn.disabled = false;
                    btn.innerText = 'Submit Review';
                }
            }
        }

        function addCurrentProductToCart() {
            const pricing = window.BeautagePdpPricing?.cartPayload?.() || {};
            const item = {
                ...currentProduct,
                name: pricing.name || currentProduct.name,
                price: Number(pricing.price ?? currentProduct.price ?? 0),
                qty: Number(pricing.qty ?? 1),
            };
            if (window.BeautageCart) {
                BeautageCart.addItem(item);
                cart = BeautageCart.readStorage();
            } else {
                const existing = cart.find(i => i.name === item.name);
                if(existing) {
                    existing.qty += item.qty;
                } else {
                    cart.push(item);
                }
                updateCartUI();
            }
            showToast(`Added ${item.qty}x "${currentProduct.name}" to your bag.`);
        }

        function buyNow() {
            addCurrentProductToCart();
            window.location.href = '/checkout';
        }

        function checkDelivery() {
            const code = document.getElementById('postal-input').value.trim();
            const res = document.getElementById('delivery-result');
            if(code.length >= 3) {
                res.classList.remove('hidden');
                res.className = 'text-xs font-semibold text-emerald-600';
                res.innerHTML = `✓ Cash on Delivery available for ${code}. Express delivery in 2-3 business days.`;
            } else {
                res.classList.remove('hidden');
                res.className = 'text-xs font-semibold text-rose-600';
                res.innerHTML = `Please enter a valid postal code.`;
            }
        }

        function applyBundleDiscount(subtotal) {
            if (!BEAUTAGE_BUNDLE || subtotal <= 0) {
                return subtotal;
            }
            const value = Number(BEAUTAGE_BUNDLE.discountValue || 0);
            if (BEAUTAGE_BUNDLE.discountType === 'fixed') {
                return Math.max(0, subtotal - value);
            }
            const percent = Math.min(100, Math.max(0, value));
            return subtotal * (1 - (percent / 100));
        }

        function getSelectedBundleItems() {
            if (!BEAUTAGE_BUNDLE?.items) {
                return [];
            }
            return BEAUTAGE_BUNDLE.items.filter((item, index) => {
                const checkbox = document.getElementById(`bundle-check-${index}`);
                return checkbox && checkbox.checked;
            });
        }

        function updateBundleTotal() {
            if (!BEAUTAGE_BUNDLE?.items?.length) {
                return;
            }
            const selected = getSelectedBundleItems();
            const subtotal = selected.reduce((sum, item) => sum + Number(item.price || 0), 0);
            const total = applyBundleDiscount(subtotal);
            const totalEl = document.getElementById('bundle-total-price');
            const compareEl = document.getElementById('bundle-compare-price');
            const btn = document.getElementById('bundle-add-btn');
            if (totalEl) {
                totalEl.innerText = BeautageMoney.formatDecimal(total);
            }
            if (compareEl) {
                compareEl.innerText = BeautageMoney.formatDecimal(subtotal);
                compareEl.classList.toggle('hidden', total >= subtotal - 0.009);
            }
            const savingsEl = document.getElementById('bundle-savings-label');
            const savings = subtotal - total;
            if (savingsEl) {
                if (savings > 0.009) {
                    savingsEl.innerText = `You save ${BeautageMoney.formatDecimal(savings)} on this bundle`;
                    savingsEl.classList.remove('hidden');
                } else {
                    savingsEl.classList.add('hidden');
                }
            }
            if (btn) {
                btn.innerText = `Add All ${selected.length} to Bag`;
                btn.disabled = selected.length === 0;
            }
        }

        function addBundleToCart() {
            if (!BEAUTAGE_BUNDLE?.items?.length) {
                return;
            }
            const selected = getSelectedBundleItems();
            if (selected.length === 0) {
                showToast('Select at least one item for the bundle.');
                return;
            }
            const subtotal = selected.reduce((sum, item) => sum + Number(item.price || 0), 0);
            const discountedTotal = applyBundleDiscount(subtotal);
            const ratio = subtotal > 0 ? discountedTotal / subtotal : 1;

            selected.forEach((item) => {
                addToCartDirect({
                    id: item.id,
                    name: item.name,
                    price: Math.round(Number(item.price || 0) * ratio * 100) / 100,
                    image: item.image,
                    qty: 1,
                });
            });

            if (typeof toggleCartDrawer === 'function') {
                toggleCartDrawer();
            }
            showToast('Bundle added successfully to your bag!');
        }

        function addToCartDirect(item) {
            if (window.BeautageCart) {
                BeautageCart.addItem(item);
                cart = BeautageCart.readStorage();
                return;
            }
            const existing = cart.find(i => i.id === item.id);
            if(existing) {
                existing.qty += item.qty;
            } else {
                cart.push(item);
            }
            updateCartUI();
        }

        function removeFromCart(id) {
            cart = cart.filter(i => i.id !== id);
            updateCartUI();
        }

        function updateCartQty(id, delta) {
            const item = cart.find(i => i.id === id);
            if(item) {
                item.qty += delta;
                if(item.qty <= 0) removeFromCart(id);
                else updateCartUI();
            }
        }

        function updateCartUI() {
            if (window.BeautageCart) {
                BeautageCart.writeStorage(cart);
                BeautageCart.updateCartUI();
            }
        }

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-backdrop');
            const panel = document.getElementById('cart-panel');
            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.remove('opacity-0'); panel.classList.remove('translate-x-full'); }, 10);
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
            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.remove('opacity-0'); panel.classList.remove('translate-x-full'); }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => drawer.classList.add('hidden'), 300);
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

        function toggleMobileSearch() {
            const searchContainer = document.getElementById('mobile-search-container');
            searchContainer.classList.toggle('hidden');
            if(!searchContainer.classList.contains('hidden')) {
                document.getElementById('mobile-search-input').focus();
            }
        }

        function toggleProductFaq(index) {
            const answer = document.getElementById(`product-faq-answer-${index}`);
            const chevron = document.getElementById(`product-faq-chevron-${index}`);
            if (!answer || !chevron) return;
            const open = answer.classList.toggle('hidden');
            chevron.classList.toggle('rotate-180', !open);
        }

        function switchTab(tabKey) {
            ['desc', 'specs', 'routine', 'reviews'].forEach(k => {
                const panel = document.getElementById(`tab-content-${k}`);
                panel.classList.add('hidden');
                panel.hidden = true;
                const btn = document.getElementById(`tab-btn-${k}`);
                btn.classList.remove('is-on');
                btn.setAttribute('aria-selected', 'false');
            });
            const activePanel = document.getElementById(`tab-content-${tabKey}`);
            activePanel.classList.remove('hidden');
            activePanel.hidden = false;
            const activeBtn = document.getElementById(`tab-btn-${tabKey}`);
            activeBtn.classList.add('is-on');
            activeBtn.setAttribute('aria-selected', 'true');
        }

        function renderRelatedProducts() {
            const container = document.getElementById('related-product-grid');
            const items = (relatedProducts || []).slice(0, 12);
            if (items.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-sm text-slate-500 py-8">More products in this category coming soon.</p>';
                return;
            }
            container.innerHTML = items.map(p => window.ShopProductCard.html(p)).join('');
            lucide.createIcons();
        }

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                const announcement = document.getElementById('announcement-timer');
                const countdown = document.getElementById('pdp-countdown');
                if (announcement) announcement.innerText = `${h}:${m}:${s}`;
                if (countdown) countdown.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }

        function setActiveBottomNav(el) {
            document.querySelectorAll('.bottom-nav-item').forEach(item => {
                item.classList.remove('text-beautage-pink');
                item.classList.add('text-slate-500');
            });
            el.classList.remove('text-slate-500');
            el.classList.add('text-beautage-pink');
        }
    </script>
@endverbatim
@endpush
