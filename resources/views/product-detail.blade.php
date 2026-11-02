@extends('layouts.app')

@section('title', $product->name . ' — Beautage Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0"
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
    <link rel="stylesheet" href="{{ asset('css/pdp.css') }}?v=2026102921">
@endpush

@push('structured-data')
@include('partials.product-json-ld', ['product' => $product, 'pageSeo' => $pageSeo, 'galleryImages' => $galleryImages ?? []])
@endpush

@section('content')
@php
    use App\Support\MoneyFormat;
    $productSave = max(0, (float) $product->old_price - (float) $product->price);
@endphp
<main class="w-full flex-1 flex flex-col py-6">

        <!-- 1. BREADCRUMB NAVIGATION -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-4 sm:mb-6 overflow-x-auto no-scrollbar">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-600 whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <a href="{{ route('collections') }}" class="hover:text-beautage-purple transition">{{ $product->category ?: 'Shop' }}</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">{{ $product->name }}</span>
            </nav>
        </div>

        <!-- PRODUCT MAIN SECTION (GALLERY + BUY BOX) -->
        <section id="pdp-main-section" class="w-full px-4 sm:px-12 xl:px-20">
            <div id="pdp-main-grid" class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-start lg:items-stretch">
                
                <!-- 2. IMAGE GALLERY (SPAN 5) — square; pins on desktop while buy column scrolls -->
                <div id="pdp-gallery-col" class="lg:col-span-5 min-w-0 lg:self-stretch">
                    <div id="pdp-gallery-pin" class="flex flex-col-reverse sm:flex-row gap-4 w-full lg:sticky lg:top-[5.75rem] lg:z-[1]">
                    <div class="flex sm:flex-col gap-3 overflow-x-auto sm:overflow-y-auto no-scrollbar shrink-0 py-1 max-h-[360px] sm:max-h-[420px] lg:max-h-[min(520px,calc(100vh-12rem))]">
                        @foreach ($galleryImages as $index => $galleryUrl)
                        <button type="button" onclick="switchMainImage({{ $index }})" aria-label="Show product image {{ $index + 1 }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}" class="thumb-btn w-14 h-14 sm:w-16 sm:h-16 lg:w-[4.5rem] lg:h-[4.5rem] rounded-2xl border-2 {{ $index === 0 ? 'border-beautage-purple' : 'border-transparent hover:border-purple-300' }} overflow-hidden bg-purple-50 shadow-sm shrink-0 transition">
                            <img src="{{ $galleryUrl }}" alt="{{ $product->name }} — view {{ $index + 1 }}" class="w-full h-full object-cover" width="72" height="72" loading="lazy" decoding="async">
                        </button>
                        @endforeach
                        @if ($product->video_url)
                        <button type="button" onclick="openVideoModal()" aria-label="Play product video" class="w-14 h-14 sm:w-16 sm:h-16 lg:w-[4.5rem] lg:h-[4.5rem] rounded-2xl border-2 border-dashed border-pink-400 bg-pink-50 hover:bg-pink-100 flex flex-col items-center justify-center text-beautage-pink shrink-0 transition shadow-sm group">
                            <i data-lucide="play-circle" class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition"></i>
                            <span class="text-[8px] sm:text-[9px] font-black uppercase tracking-wider mt-0.5">Video</span>
                        </button>
                        @endif
                    </div>

                    <div id="pdp-gallery-stage" class="pdp-gallery-main flex-1 relative rounded-3xl overflow-hidden bg-purple-50/50 border border-purple-100 shadow-xl w-full min-w-0 aspect-square">
                        <img id="main-product-img" src="{{ $galleryImages[0] ?? $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover cursor-zoom-in" width="800" height="800" fetchpriority="high" decoding="async" onclick="openLightbox()">
                        @if ($product->badge)
                        <div class="absolute top-4 left-4 bg-beautage-purple text-white text-[10px] sm:text-xs font-black uppercase tracking-widest px-3 py-1 sm:px-3.5 sm:py-1.5 rounded-full shadow-lg">
                            {{ $product->badge }}
                        </div>
                        @endif
                        <button type="button" onclick="openLightbox()" class="absolute bottom-4 right-4 p-2.5 sm:p-3 rounded-2xl bg-white/90 backdrop-blur-md text-beautage-purple hover:bg-white shadow-lg transition flex items-center gap-2 text-xs font-bold">
                            <i data-lucide="maximize-2" class="w-4 h-4"></i> Zoom
                        </button>
                    </div>
                    </div>
                </div>

                <!-- 3. PRODUCT HEADER, PRICING & BUY (SPAN 4) -->
                <div id="pdp-buy-column" class="lg:col-span-4 space-y-5 sm:space-y-6 min-w-0 lg:max-h-[calc(100svh-5.75rem)] lg:overflow-y-auto lg:overscroll-y-contain no-scrollbar [scrollbar-gutter:stable]">
                    
                    <div>
                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                            <span class="inline-flex items-center gap-1.5 bg-emerald-50 text-emerald-700 border border-emerald-200 px-3 py-1 rounded-full text-xs font-extrabold">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> 100% Original • Authorized Store
                            </span>
                            @if ($product->promo_label)
                            <span class="bg-pink-50 text-beautage-pink border border-pink-200 px-3 py-1 rounded-full text-xs font-extrabold uppercase">
                                {{ $product->promo_label }}
                            </span>
                            @endif
                        </div>
                        <h1 class="text-2xl sm:text-3xl xl:text-4xl font-serif font-bold text-beautage-purple tracking-tight leading-tight">
                            {{ $product->name }}
                        </h1>
                        @if ($product->short_description)
                        <p class="text-sm text-slate-700 mt-2 leading-relaxed line-clamp-6">{{ $product->short_description }}</p>
                        @elseif ($product->description)
                        <p class="text-sm text-slate-700 mt-2 leading-relaxed line-clamp-4">{{ \Illuminate\Support\Str::limit(strip_tags($product->description), 280) }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-3 flex-wrap">
                            @include('partials.product-star-rating', ['rating' => $product->rating, 'size' => 'w-4 h-4', 'showValue' => true])
                            <span class="text-slate-300">•</span>
                            <a href="#customer-reviews" onclick="switchTab('reviews')" class="text-xs font-semibold text-beautage-purple hover:underline">{{ number_format((int) $product->reviews) }} Verified Reviews</a>
                        </div>
                    </div>

                    <!-- Pricing Row -->
                    <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-50 to-pink-50 border border-purple-200 flex items-center justify-between">
                        <div>
                            <span class="text-[10px] text-slate-600 uppercase font-extrabold tracking-wider block">Special Introductory Price</span>
                            <div class="flex items-baseline gap-3 mt-0.5">
                                <span class="text-3xl font-black text-slate-900">{{ MoneyFormat::pkr($product->price) }}</span>
                                <span class="text-base text-slate-400 line-through font-bold">{{ MoneyFormat::pkr($product->old_price) }}</span>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-full">You Save {{ MoneyFormat::pkr($productSave) }}</span>
                        </div>
                    </div>

                    <!-- Urgency & Scarcity Tracker -->
                    <div class="p-4 rounded-2xl bg-amber-50/80 border border-amber-200 space-y-2">
                        <div class="flex items-center justify-between text-xs font-extrabold text-amber-900">
                            @if ($product->units_left)
                            <span class="flex items-center gap-1.5"><i data-lucide="flame" class="w-4 h-4 text-amber-600 fill-current"></i> Only {{ $product->units_left }} units left in stock</span>
                            @endif
                            @if ($product->claimed_percent)
                            <span class="text-beautage-pink">{{ (int) $product->claimed_percent }}% claimed today</span>
                            @endif
                        </div>
                        <div class="w-full h-2 rounded-full bg-amber-200 overflow-hidden">
                            <div class="pdp-no-motion-pulse h-full bg-gradient-to-r from-beautage-pink to-amber-500 rounded-full animate-pulse" style="width: {{ min(100, max(0, (int) ($product->claimed_percent ?? 0))) }}%"></div>
                        </div>
                        <div class="flex items-center gap-2 text-[11px] text-amber-800 pt-1">
                            <i data-lucide="timer" class="w-3.5 h-3.5"></i>
                            <span>Flash deal timer: <strong id="pdp-countdown" class="font-mono text-beautage-purple">04:32:19</strong> (Order soon)</span>
                        </div>
                    </div>

                    @include('partials.product-pdp-bulk-savings')

                    <!-- Quantity Selector & Add to Bag -->
                    <div id="pdp-buy-actions" class="space-y-3 pt-2">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border-2 border-purple-200 rounded-2xl bg-white overflow-hidden shadow-sm">
                                <button type="button" onclick="adjustQty(-1)" aria-label="Decrease quantity" class="px-4 py-3.5 text-sm font-extrabold text-slate-600 hover:bg-purple-50 transition min-h-[44px] min-w-[44px]">-</button>
                                <span id="product-qty" class="w-10 text-center text-sm font-bold text-slate-800" aria-live="polite" aria-label="Quantity">1</span>
                                <button type="button" onclick="adjustQty(1)" aria-label="Increase quantity" class="px-4 py-3.5 text-sm font-extrabold text-slate-600 hover:bg-purple-50 transition min-h-[44px] min-w-[44px]">+</button>
                            </div>
                            <button onclick="addCurrentProductToCart()" class="flex-1 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-black py-4 px-6 rounded-2xl transition shadow-xl shadow-pink-500/30 flex items-center justify-center gap-3 text-xs uppercase tracking-wider">
                                <i data-lucide="shopping-bag" class="w-5 h-5"></i> Add To Bag — <span id="btn-total-price">{{ MoneyFormat::pkr($product->price) }}</span>
                            </button>
                        </div>
                        <button onclick="buyNow()" class="w-full bg-beautage-purple hover:bg-beautage-purpleDark text-white font-extrabold py-3.5 px-6 rounded-2xl transition shadow-lg flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                            <i data-lucide="zap" class="w-4 h-4 text-beautage-amber"></i> Buy Now with Express Checkout
                        </button>
                    </div>

                    <!-- Trust Badges & Delivery Checker -->
                    <div class="p-5 rounded-2xl bg-white border border-purple-100 shadow-sm space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                                <i data-lucide="truck" class="w-4 h-4"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="text-xs font-bold text-beautage-purple">Cash on Delivery Available Across Pakistan</h4>
                                <p class="text-[11px] text-slate-600">Free Express Shipping on all orders over Rs 7,500</p>
                            </div>
                        </div>

                        <div class="pt-3 border-t border-purple-50 space-y-2">
                            <label for="postal-input" class="block text-[11px] font-extrabold text-slate-700 uppercase">Check Delivery / Postal Code:</label>
                            <div class="flex items-center gap-2">
                                <input type="text" id="postal-input" name="postal_code" inputmode="numeric" autocomplete="postal-code" placeholder="e.g. 54000 (Lahore / Karachi)" class="flex-1 py-2.5 px-3 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple">
                                <button type="button" onclick="checkDelivery()" class="px-4 py-2.5 bg-beautage-purple text-white text-xs font-bold rounded-xl hover:bg-beautage-purpleDark transition min-h-[44px]">Check</button>
                            </div>
                            <div id="delivery-result" class="text-xs font-semibold text-emerald-600 hidden"></div>
                        </div>

                        <div class="grid grid-cols-3 gap-2 pt-2 text-center text-[10px] font-bold text-slate-600 border-t border-purple-50">
                            <div class="p-2 rounded-xl bg-purple-50/50 flex flex-col items-center gap-1">
                                <i data-lucide="shield-check" class="w-4 h-4 text-beautage-pink"></i>
                                <span>100% Original</span>
                            </div>
                            <div class="p-2 rounded-xl bg-purple-50/50 flex flex-col items-center gap-1">
                                <i data-lucide="rotate-ccw" class="w-4 h-4 text-emerald-600"></i>
                                <span>30-Day Returns</span>
                            </div>
                            <div class="p-2 rounded-xl bg-purple-50/50 flex flex-col items-center gap-1">
                                <i data-lucide="headphones" class="w-4 h-4 text-amber-600"></i>
                                <span>24/7 Support</span>
                            </div>
                        </div>
                    </div>

                </div>

                @include('partials.product-pdp-trust-column')

            </div>
        </section>

        @include('partials.product-bundle-section', ['bundlePayload' => $bundlePayload ?? null])

        @include('partials.recently-viewed-section', [
            'recentlyVariant' => 'pdp',
            'excludeProductId' => $product->id,
            'recentlyLimit' => 5,
        ])

        <!-- COMPREHENSIVE TABS / ACCORDIONS FOR PRODUCT DETAILS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-8" aria-labelledby="pdp-details-heading">
            <h2 id="pdp-details-heading" class="sr-only">Product details, specifications, and reviews</h2>
            <div class="w-full bg-white rounded-3xl border border-purple-100 shadow-xl overflow-hidden">
                <div class="grid grid-cols-2 md:grid-cols-4 bg-purple-50/70 border-b border-purple-100" role="tablist" aria-label="Product information tabs">
                    <button type="button" role="tab" aria-selected="true" aria-controls="tab-content-desc" onclick="switchTab('desc')" id="tab-btn-desc" class="p-3.5 sm:p-4 text-xs font-bold text-beautage-purple border-b-2 border-beautage-purple bg-white transition flex items-center justify-center gap-2 text-center">
                        <i data-lucide="file-text" class="w-4 h-4 text-beautage-pink shrink-0"></i> <span>Description</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-specs" onclick="switchTab('specs')" id="tab-btn-specs" class="p-3.5 sm:p-4 text-xs font-semibold text-slate-600 hover:text-beautage-purple transition flex items-center justify-center gap-2 text-center">
                        <i data-lucide="cpu" class="w-4 h-4 text-purple-600 shrink-0"></i> <span>Specs</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-routine" onclick="switchTab('routine')" id="tab-btn-routine" class="p-3.5 sm:p-4 text-xs font-semibold text-slate-600 hover:text-beautage-purple transition flex items-center justify-center gap-2 text-center">
                        <i data-lucide="sparkles" class="w-4 h-4 text-amber-500 shrink-0"></i> <span>Routine</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-reviews" onclick="switchTab('reviews')" id="tab-btn-reviews" class="p-3.5 sm:p-4 text-xs font-semibold text-slate-600 hover:text-beautage-purple transition flex items-center justify-center gap-2 text-center">
                        <i data-lucide="star" class="w-4 h-4 text-amber-400 fill-current shrink-0"></i> <span>Reviews ({{ number_format((int) $product->reviews) }})</span>
                    </button>
                </div>

                <div id="tab-content-desc" role="tabpanel" aria-labelledby="tab-btn-desc" class="p-6 sm:p-8 space-y-6 tab-pane">
                    @if ($product->detail_heading)
                    <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">{{ $product->detail_heading }}</h3>
                    @endif
                    @if ($product->detail_body)
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->detail_body }}</p>
                    @elseif ($product->short_description)
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->short_description }}</p>
                    @elseif ($product->description)
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">{{ $product->description }}</p>
                    @else
                    <p class="text-xs text-slate-400">Detailed description coming soon.</p>
                    @endif
                    @if (filled($product->detail_highlights))
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-4">
                        @foreach ($product->detail_highlights as $highlight)
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <i data-lucide="check-circle-2" class="w-5 h-5 text-beautage-pink shrink-0 mt-0.5"></i>
                            <div>
                                <h4 class="text-xs font-bold text-beautage-purple">{{ $highlight['title'] ?? '' }}</h4>
                                <p class="text-[11px] text-slate-600 mt-1">{{ $highlight['description'] ?? '' }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif

                    @php
                        $productFaqs = collect($product->faq_items ?? [])->filter(fn ($row) => filled($row['question'] ?? null));
                    @endphp
                    @if ($productFaqs->isNotEmpty())
                    <div class="pt-6 mt-2 border-t border-purple-100 space-y-4">
                        <div class="flex items-center gap-2">
                            <i data-lucide="help-circle" class="w-5 h-5 text-beautage-pink shrink-0"></i>
                            <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Frequently Asked Questions</h3>
                        </div>
                        <div class="space-y-3">
                            @foreach ($productFaqs as $faqIndex => $faq)
                            <div class="rounded-2xl border border-purple-100 bg-purple-50/30 overflow-hidden shadow-sm">
                                <button type="button" onclick="toggleProductFaq({{ $faqIndex }})" class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 hover:bg-purple-50/80 transition focus:outline-none">
                                    <span class="text-xs sm:text-sm font-bold text-beautage-purple flex items-start gap-3">
                                        <span class="w-6 h-6 rounded-full bg-white border border-purple-100 text-beautage-pink flex items-center justify-center text-[10px] font-extrabold shrink-0 mt-0.5">Q</span>
                                        <span>{{ $faq['question'] ?? '' }}</span>
                                    </span>
                                    <i data-lucide="chevron-down" id="product-faq-chevron-{{ $faqIndex }}" class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-300"></i>
                                </button>
                                <div id="product-faq-answer-{{ $faqIndex }}" class="hidden px-5 sm:px-6 pb-5 pt-0 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/80 bg-white/70">
                                    <p class="pt-3">{{ $faq['answer'] ?? '' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                <div id="tab-content-specs" role="tabpanel" aria-labelledby="tab-btn-specs" hidden class="p-6 sm:p-8 space-y-6 tab-pane hidden">
                    <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">{{ $product->specs_heading ?: 'Specifications' }}</h3>
                    @if (filled($product->specs_rows))
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <tbody class="divide-y divide-purple-100">
                                @foreach ($product->specs_rows as $row)
                                <tr>
                                    <td class="py-3 font-bold text-beautage-purple w-1/3">{{ $row['label'] ?? '' }}</td>
                                    <td class="py-3 text-slate-600">{{ $row['value'] ?? '' }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-xs text-slate-400">Specifications will be added soon.</p>
                    @endif
                </div>

                <div id="tab-content-routine" role="tabpanel" aria-labelledby="tab-btn-routine" hidden class="p-6 sm:p-8 space-y-6 tab-pane hidden">
                    <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">{{ $product->routine_heading ?: 'How to use' }}</h3>
                    @if ($product->routine_body)
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed whitespace-pre-line">{{ $product->routine_body }}</p>
                    @else
                    <p class="text-xs text-slate-400">Routine instructions coming soon.</p>
                    @endif
                </div>

                <div id="tab-content-reviews" role="tabpanel" aria-labelledby="tab-btn-reviews" hidden class="p-6 sm:p-8 space-y-6 tab-pane hidden">
                    <div id="customer-reviews" class="scroll-mt-28"></div>
                    <div class="flex flex-col lg:flex-row gap-8">
                        <div class="lg:w-1/3 space-y-4">
                            <div>
                                <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Customer Reviews</h3>
                                <div class="flex items-center gap-2 mt-2">
                                    @include('partials.product-star-rating', ['rating' => $product->rating, 'size' => 'w-5 h-5'])
                                </div>
                                <p class="text-xs font-bold text-slate-700 mt-1">{{ number_format((float) $product->rating, 1) }} out of 5 · {{ number_format((int) $product->reviews) }} reviews</p>
                            </div>
                            <button type="button" onclick="openReviewModal()" class="w-full px-5 py-2.5 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md">
                                Write a Review
                            </button>
                        </div>

                        <div class="lg:w-2/3 space-y-6">
                            <form id="product-review-form" class="p-5 rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50/80 to-white space-y-4 shadow-sm" onsubmit="handleReviewSubmit(event)">
                                <h4 class="text-sm font-bold text-beautage-purple">Share your experience</h4>
                                <div>
                                    <span id="review-rating-label" class="text-[11px] font-bold text-slate-600 uppercase tracking-wide">Your rating</span>
                                    <div class="flex gap-1 mt-1" id="review-star-picker" role="group" aria-labelledby="review-rating-label">
                                        @for ($s = 1; $s <= 5; $s++)
                                        <button type="button" data-review-star="{{ $s }}" onclick="setReviewRating({{ $s }})" aria-label="Rate {{ $s }} out of 5 stars" class="review-star-btn p-1 text-amber-300 hover:text-amber-400 transition min-h-[44px] min-w-[44px]">
                                            <i data-lucide="star" class="w-6 h-6" aria-hidden="true"></i>
                                        </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="review-rating-input" value="5" required>
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <div>
                                        <label for="review-author-name" class="text-[11px] font-bold text-slate-600">Name</label>
                                        <input id="review-author-name" name="author_name" type="text" required maxlength="120" class="mt-1 w-full rounded-xl border border-purple-200 px-3 py-2 text-xs focus:border-beautage-purple focus:ring-beautage-purple">
                                    </div>
                                    <div>
                                        <label for="review-author-email" class="text-[11px] font-bold text-slate-600">Email (optional)</label>
                                        <input id="review-author-email" name="author_email" type="email" maxlength="255" class="mt-1 w-full rounded-xl border border-purple-200 px-3 py-2 text-xs focus:border-beautage-purple focus:ring-beautage-purple">
                                    </div>
                                </div>
                                <div>
                                    <label for="review-title" class="text-[11px] font-bold text-slate-600">Review title (optional)</label>
                                    <input id="review-title" name="title" type="text" maxlength="200" class="mt-1 w-full rounded-xl border border-purple-200 px-3 py-2 text-xs focus:border-beautage-purple focus:ring-beautage-purple">
                                </div>
                                <div>
                                    <label for="review-body" class="text-[11px] font-bold text-slate-600">Your review</label>
                                    <textarea id="review-body" name="body" required minlength="10" rows="4" class="mt-1 w-full rounded-xl border border-purple-200 px-3 py-2 text-xs focus:border-beautage-purple focus:ring-beautage-purple" placeholder="Tell others what you loved about this product…"></textarea>
                                </div>
                                <div>
                                    <label for="review-photos" class="text-[11px] font-bold text-slate-600 uppercase tracking-wide">Add photos (optional)</label>
                                    <p class="text-[10px] text-slate-500 mt-0.5 mb-2">Up to 3 images — JPG, PNG, or WebP, max 4 MB each.</p>
                                    <label for="review-photos" class="flex flex-col items-center justify-center gap-2 w-full rounded-2xl border-2 border-dashed border-purple-200 bg-white/80 px-4 py-6 cursor-pointer hover:border-beautage-pink hover:bg-pink-50/30 transition">
                                        <i data-lucide="image-plus" class="w-8 h-8 text-beautage-purple"></i>
                                        <span class="text-xs font-bold text-beautage-purple">Tap to upload photos</span>
                                        <span class="text-[10px] text-slate-500">Show your results or product in use</span>
                                    </label>
                                    <input id="review-photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" onchange="previewReviewPhotos(this)">
                                    <div id="review-photo-preview" class="grid grid-cols-3 gap-2 mt-3 empty:hidden"></div>
                                </div>
                                <button type="submit" id="review-submit-btn" class="w-full sm:w-auto px-6 py-2.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md">
                                    Submit Review
                                </button>
                            </form>

                            <div id="product-reviews-list" class="space-y-4">
                                @forelse ($publishedReviews as $review)
                                <article class="p-4 rounded-2xl bg-purple-50/40 border border-purple-100 space-y-2">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <span class="w-9 h-9 rounded-full bg-beautage-pink text-white font-bold text-xs flex items-center justify-center shrink-0">{{ $review->authorInitials() }}</span>
                                            <div class="min-w-0">
                                                <h4 class="text-xs font-bold text-beautage-purple truncate">
                                                    {{ $review->author_name }}
                                                    @if ($review->is_verified_buyer)
                                                    <span class="text-[10px] text-emerald-600 font-extrabold ml-1">✓ Verified Buyer</span>
                                                    @endif
                                                </h4>
                                                @include('partials.product-star-rating', ['rating' => $review->rating, 'size' => 'w-3 h-3'])
                                            </div>
                                        </div>
                                        <time class="text-[10px] text-slate-400 shrink-0" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->diffForHumans() }}</time>
                                    </div>
                                    @if ($review->title)
                                    <p class="text-xs font-bold text-slate-800">{{ $review->title }}</p>
                                    @endif
                                    <p class="text-xs text-slate-600 leading-relaxed">"{{ $review->body }}"</p>
                                    @if (count($review->photoUrls()) > 0)
                                    <div class="flex flex-wrap gap-2 pt-1">
                                        @foreach ($review->photoUrls() as $photoUrl)
                                        <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="block w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border border-purple-100 shadow-sm hover:ring-2 hover:ring-beautage-pink transition">
                                            <img src="{{ $photoUrl }}" alt="Review photo" class="w-full h-full object-cover">
                                        </a>
                                        @endforeach
                                    </div>
                                    @endif
                                </article>
                                @empty
                                <div class="text-center py-8 text-slate-400 text-xs border border-dashed border-purple-200 rounded-2xl">
                                    No reviews yet. Be the first to share your experience!
                                </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- RELATED / YOU MAY ALSO LIKE PRODUCTS GRID -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-12">
            <div class="text-center max-w-2xl mx-auto mb-8">
                <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-purple-100 text-beautage-purple border border-purple-200 mb-2">
                    COMPLETE YOUR ROUTINE
                </span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">You May Also Like</h2>
                <p class="text-xs text-slate-500 mt-1">Explore complementary flagship elixirs and dermal devices.</p>
            </div>

            <div id="related-product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-3 sm:gap-4 lg:gap-5"></div>
        </section>

    </main>
@endsection

@section('below_main')
    <!-- Sticky checkout — slides up when inline buy buttons leave the viewport -->
    <div id="pdp-sticky-checkout" class="fixed inset-x-0 z-[45] max-md:bottom-[3.65rem] md:bottom-0 translate-y-full opacity-0 pointer-events-none transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" aria-hidden="true">
        <div class="border-t border-purple-200/80 bg-white/95 backdrop-blur-xl shadow-[0_-12px_40px_rgba(94,31,112,0.14)]">
            <div class="w-full px-4 sm:px-12 xl:px-20 py-3 md:py-3.5 max-w-[90rem] mx-auto">
                <div class="flex flex-col gap-2.5 md:flex-row md:items-center md:gap-4">
                    <div class="flex items-center gap-3 min-w-0 md:flex-1">
                        <img src="{{ $galleryImages[0] ?? $product->image }}" alt="" class="hidden sm:block w-11 h-11 rounded-xl object-cover border border-purple-100 shadow-sm shrink-0">
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

    <!-- MOBILE BOTTOM NAVIGATION -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-purple-100 shadow-2xl px-3 py-2 flex items-center justify-around">
        <a href="{{ route('home') }}" onclick="setActiveBottomNav(this)" class="bottom-nav-item flex flex-col items-center justify-center text-slate-500 hover:text-beautage-purple transition py-1 px-2">
            <i data-lucide="home" class="w-5 h-5"></i>
            <span class="text-[10px] font-semibold mt-1">Home</span>
        </a>
        <a href="{{ route('collections') }}" onclick="setActiveBottomNav(this)" class="bottom-nav-item flex flex-col items-center justify-center text-slate-500 hover:text-beautage-purple transition py-1 px-2">
            <i data-lucide="layout-grid" class="w-5 h-5" aria-hidden="true"></i>
            <span class="text-[10px] font-semibold mt-1">Categories</span>
        </a>
        
        <div class="relative -top-3">
            <button type="button" onclick="toggleMobileNav()" aria-label="Open menu" class="w-14 h-14 rounded-full bg-gradient-to-tr from-beautage-purpleDark via-beautage-purple to-beautage-pink shadow-xl shadow-purple-900/30 flex items-center justify-center border-4 border-white transition transform hover:scale-105 active:scale-95 group">
                <img src="{{ \App\Support\SiteSettings::logoUrl() }}" alt="" width="32" height="32" class="w-8 h-8 object-contain brightness-200 invert drop-shadow-md group-hover:rotate-12 transition" onerror="this.onerror=null; this.src='https://placehold.co/100x100/5E1F70/FFFFFF?text=B';">
            </button>
        </div>

        <button type="button" onclick="toggleWishlistDrawer(); setActiveBottomNav(this);" aria-label="Open wishlist" class="bottom-nav-item relative flex flex-col items-center justify-center text-slate-500 hover:text-beautage-purple transition py-1 px-2 bg-transparent border-0">
            <i data-lucide="heart" class="w-5 h-5" aria-hidden="true"></i>
            <span class="text-[10px] font-semibold mt-1">Wishlist</span>
            <span id="mobile-wishlist-badge" class="absolute top-0 right-2 bg-beautage-pink text-white text-[9px] font-bold w-3.5 h-3.5 rounded-full flex items-center justify-center shadow" aria-hidden="true">0</span>
        </button>
        <button type="button" onclick="toggleCartDrawer(); setActiveBottomNav(this);" aria-label="Open shopping bag" class="bottom-nav-item relative flex flex-col items-center justify-center text-slate-500 hover:text-beautage-purple transition py-1 px-2 bg-transparent border-0">
            <i data-lucide="shopping-bag" class="w-5 h-5" aria-hidden="true"></i>
            <span class="text-[10px] font-semibold mt-1">Bag</span>
            <span id="mobile-cart-badge" class="absolute top-0 right-2 bg-beautage-purple text-white text-[9px] font-bold w-3.5 h-3.5 rounded-full flex items-center justify-center shadow" aria-hidden="true">0</span>
        </button>
    </nav>

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
<script src="{{ asset('js/pdp-pricing.js') }}?v=2026102823" defer></script>
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
                if (idx === index) {
                    btn.classList.add('border-beautage-purple');
                    btn.classList.remove('border-transparent');
                } else {
                    btn.classList.remove('border-beautage-purple');
                    btn.classList.add('border-transparent');
                }
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
                btn.classList.remove('border-b-2', 'border-beautage-purple', 'bg-white', 'text-beautage-purple', 'font-bold');
                btn.classList.add('text-slate-600', 'font-semibold');
                btn.setAttribute('aria-selected', 'false');
            });
            const activePanel = document.getElementById(`tab-content-${tabKey}`);
            activePanel.classList.remove('hidden');
            activePanel.hidden = false;
            const activeBtn = document.getElementById(`tab-btn-${tabKey}`);
            activeBtn.classList.add('border-b-2', 'border-beautage-purple', 'bg-white', 'text-beautage-purple', 'font-bold');
            activeBtn.classList.remove('text-slate-600', 'font-semibold');
            activeBtn.setAttribute('aria-selected', 'true');
        }

        function renderRelatedProducts() {
            const container = document.getElementById('related-product-grid');
            const items = (relatedProducts || []).slice(0, 6);
            if (items.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-sm text-slate-500 py-8">More products in this category coming soon.</p>';
                return;
            }
            container.innerHTML = items.map(p => {
                const productUrl = p.slug ? `/products/${p.slug}` : '/collections';
                const safeName = (p.name || '').replace(/'/g, "\\'");
                return `
                <div class="bg-white rounded-2xl p-3 sm:p-4 border border-purple-100 hover:border-beautage-pink transition-all duration-300 shadow-sm hover:shadow-xl hover:-translate-y-1 flex flex-col justify-between group">
                    <a href="${productUrl}" class="relative rounded-xl overflow-hidden bg-purple-50 mb-3 aspect-square block">
                        <img src="${p.image}" alt="" class="absolute inset-0 w-full h-full object-cover transition-opacity duration-500 group-hover:opacity-0">
                        <img src="${p.hoverImage || p.image}" alt="" class="absolute inset-0 w-full h-full object-cover opacity-0 transition-opacity duration-500 group-hover:opacity-100 group-hover:scale-105">
                        <span class="absolute top-2 left-2 bg-beautage-purple text-white text-[9px] font-extrabold px-2 py-0.5 rounded-full uppercase">${p.badge || 'Flagship'}</span>
                    </a>
                    <div>
                        <span class="text-[9px] uppercase font-bold text-beautage-pink tracking-widest">${p.category}</span>
                        <h3 class="text-xs sm:text-sm font-bold text-beautage-purple mt-1 line-clamp-2 min-h-[2.5rem]"><a href="${productUrl}" class="hover:text-beautage-pink transition">${p.name}</a></h3>
                    </div>
                    <div class="mt-3 pt-3 border-t border-purple-50 flex items-center justify-between gap-2">
                        <span class="text-sm font-black text-slate-800">${BeautageMoney.formatDecimal(p.price)}</span>
                        <button type="button" onclick="addToCartDirect({id: ${p.id}, name: '${safeName}', price: ${p.price}, image: '${p.image}', qty: 1}); showToast('Added to bag');" class="px-3 py-2 rounded-xl bg-beautage-pink hover:bg-beautage-pinkHover text-white text-[11px] font-bold transition shadow-sm shrink-0">Add</button>
                    </div>
                </div>
            `;
            }).join('');
            lucide.createIcons();
        }

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                document.getElementById('announcement-timer').innerText = `${h}:${m}:${s}`;
                document.getElementById('pdp-countdown').innerText = `${h}:${m}:${s}`;
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
