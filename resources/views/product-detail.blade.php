@extends('layouts.app')

@section('title', $product->name . ' — Beautage Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-emerald-200 selection:text-slate-900"
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
    <link rel="stylesheet" href="{{ asset('css/pdp.css') }}?v=2026110805">
@endpush

@push('structured-data')
@include('partials.product-json-ld', ['product' => $product, 'pageSeo' => $pageSeo, 'galleryImages' => $galleryImages ?? []])
@endpush

@section('content')
@php
    use App\Support\MoneyFormat;
    $productPrice = (float) $product->price;
    $productOld = (float) $product->old_price;
    $productSave = max(0, $productOld - $productPrice);
    $savePercent = ($productOld > $productPrice && $productOld > 0) ? (int) round(($productSave / $productOld) * 100) : 0;
    $categoryName = $product->productCategory?->name ?? ($product->category ?: 'Shop');
    $skuLabel = $product->external_sku ?: strtoupper($product->slug);
    $benefitLines = collect($product->detail_highlights ?? [])
        ->map(fn ($row) => trim((string) ($row['title'] ?? '')))
        ->filter()
        ->take(3)
        ->values();
    if ($benefitLines->isEmpty() && filled($product->short_description)) {
        $benefitLines = collect(preg_split('/\r\n|\n|•/', (string) $product->short_description))
            ->map(fn ($line) => trim($line, " \t\n\r\0\x0B-"))
            ->filter()
            ->take(3)
            ->values();
    }
    $pdpTrust = \App\Support\ProductTrustSettings::forStorefront();
    $trustBoxes = collect();
    if (! empty($pdpTrust['enabled'])) {
        foreach (['quality_section', 'trust_section'] as $trustKey) {
            foreach ($pdpTrust[$trustKey]['items'] ?? [] as $trustItem) {
                $trustBoxes->push($trustItem);
            }
        }
    }
    $trustBoxes = $trustBoxes->filter(fn ($item) => filled($item['title'] ?? null))->take(3)->values();
    if ($trustBoxes->isEmpty()) {
        $trustBoxes = collect([
            ['icon' => 'truck', 'title' => 'Free Shipping', 'description' => 'Orders over Rs 7,500'],
            ['icon' => 'rotate-ccw', 'title' => '30-Day Returns', 'description' => 'Hassle-free guarantee'],
            ['icon' => 'shield-check', 'title' => 'Authorized Store', 'description' => '100% original products'],
        ]);
    }
    $highlightIcons = ['leaf', 'zap', 'shield-check', 'sparkles', 'droplet', 'award'];
    $routineSteps = collect(preg_split('/\r\n|\n/', (string) ($product->routine_body ?? '')))
        ->map(fn ($line) => trim($line))
        ->filter()
        ->values();
    $inStock = (bool) ($product->in_stock ?? true);
@endphp
<main class="pdp-page w-full flex-1 flex flex-col pb-8">

        <div class="w-full max-w-[1600px] mx-auto px-4 sm:px-8 py-4">
            <nav class="flex text-xs font-medium text-gray-500 space-x-2 items-center overflow-x-auto no-scrollbar" aria-label="Breadcrumb">
                <a href="{{ route('home') }}" class="hover:text-[#0B132B] transition whitespace-nowrap">Home</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-gray-400 shrink-0"></i>
                <a href="{{ route('collections') }}" class="hover:text-[#0B132B] transition whitespace-nowrap">{{ $categoryName }}</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-gray-400 shrink-0"></i>
                <span class="text-[#0B132B] font-semibold truncate max-w-[200px] sm:max-w-none">{{ $product->name }}</span>
            </nav>
        </div>

        <section id="pdp-main-section" class="w-full max-w-[1600px] mx-auto px-4 sm:px-8">
            <div id="pdp-main-grid" class="grid grid-cols-1 lg:grid-cols-12 gap-10 bg-white p-6 sm:p-10 rounded-3xl shadow-sm border border-gray-100">

                <div id="pdp-gallery-col" class="lg:col-span-6 space-y-4 min-w-0">
                    <div id="pdp-gallery-stage" class="pdp-gallery-main relative w-full h-[380px] sm:h-[540px] bg-gray-50 rounded-2xl overflow-hidden border border-gray-100 group shadow-inner">
                        @if ($product->badge)
                        <span class="absolute top-4 left-4 z-10 bg-[#D4AF37] text-[#0B132B] text-xs font-extrabold px-3 py-1 rounded-full shadow">{{ $product->badge }}</span>
                        @elseif ($savePercent > 0)
                        <span class="absolute top-4 left-4 z-10 bg-[#D4AF37] text-[#0B132B] text-xs font-extrabold px-3 py-1 rounded-full shadow">SAVE {{ $savePercent }}%</span>
                        @endif
                        @if ($product->promo_label)
                        <span class="absolute top-4 right-4 z-10 bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">{{ $product->promo_label }}</span>
                        @elseif ($inStock)
                        <span class="absolute top-4 right-4 z-10 bg-emerald-600 text-white text-xs font-bold px-3 py-1 rounded-full shadow">IN STOCK</span>
                        @endif

                        <img id="main-product-img" src="{{ $galleryImages[0] ?? $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover transition duration-500 group-hover:scale-105 cursor-zoom-in" width="800" height="800" fetchpriority="high" decoding="async" onclick="openLightbox()">

                        <button type="button" onclick="openLightbox()" class="absolute bottom-4 right-4 bg-white/90 hover:bg-white text-[#0B132B] p-3 rounded-xl shadow-md transition flex items-center gap-2 text-xs font-bold">
                            <i data-lucide="expand" class="w-4 h-4"></i> Click to Zoom
                        </button>
                    </div>

                    <div class="grid grid-cols-4 gap-3 sm:gap-4">
                        @foreach ($galleryImages as $index => $galleryUrl)
                        <button type="button" onclick="switchMainImage({{ $index }})" aria-label="Show product image {{ $index + 1 }}" aria-pressed="{{ $index === 0 ? 'true' : 'false' }}" class="thumb-btn {{ $index === 0 ? 'is-active' : '' }} border-2 border-transparent rounded-xl overflow-hidden h-20 sm:h-24 bg-gray-50 shadow-sm transition">
                            <img src="{{ $galleryUrl }}" alt="{{ $product->name }} — view {{ $index + 1 }}" class="w-full h-full object-cover" width="150" height="150" loading="lazy" decoding="async">
                        </button>
                        @endforeach
                        @if ($product->video_url)
                        <button type="button" onclick="openVideoModal()" aria-label="Play product video" class="border-2 border-dashed border-[#D4AF37] rounded-xl overflow-hidden h-20 sm:h-24 bg-gray-50 flex flex-col items-center justify-center text-[#0B132B] transition">
                            <i data-lucide="play-circle" class="w-6 h-6 text-[#D4AF37]"></i>
                            <span class="text-[10px] font-bold uppercase tracking-wider mt-1">Video</span>
                        </button>
                        @endif
                    </div>

                    <div class="grid grid-cols-3 gap-3 sm:gap-4 pt-4 border-t border-gray-100 text-center">
                        @foreach ($trustBoxes as $trustBox)
                        <div class="p-3 bg-gray-50 rounded-xl">
                            <i data-lucide="{{ $trustBox['icon'] ?? 'shield-check' }}" class="w-5 h-5 text-[#D4AF37] mx-auto mb-1"></i>
                            <h5 class="text-xs font-bold text-gray-800">{{ $trustBox['title'] ?? '' }}</h5>
                            @if (filled($trustBox['description'] ?? null))
                            <p class="text-[10px] text-gray-400 line-clamp-2">{{ $trustBox['description'] }}</p>
                            @endif
                        </div>
                        @endforeach
                    </div>
                </div>

                <div id="pdp-buy-column" class="lg:col-span-6 space-y-6 min-w-0">
                    <div>
                        <span class="text-xs font-bold text-[#4361EE] uppercase tracking-wider">{{ $categoryName }}</span>
                        <h1 class="text-2xl sm:text-4xl font-extrabold text-[#0B132B] mt-1 leading-tight">{{ $product->name }}</h1>
                        <div class="flex items-center justify-between mt-3 gap-3 flex-wrap">
                            <div class="flex items-center space-x-2">
                                @include('partials.product-star-rating', ['rating' => $product->rating, 'size' => 'w-4 h-4'])
                                <a href="#customer-reviews" onclick="switchTab('reviews')" class="text-xs font-semibold text-[#4361EE] hover:underline">({{ number_format((int) $product->reviews) }} Customer Reviews)</a>
                            </div>
                            <span class="text-xs text-gray-400">SKU: {{ $skuLabel }}</span>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-4 rounded-2xl border border-gray-100 flex items-center justify-between gap-3">
                        <div class="flex items-baseline space-x-3 flex-wrap">
                            <span class="text-3xl sm:text-4xl font-black text-[#0B132B]">{{ MoneyFormat::pkr($product->price) }}</span>
                            @if ($productOld > $productPrice)
                            <span class="text-base text-gray-400 line-through">{{ MoneyFormat::pkr($product->old_price) }}</span>
                            <span class="bg-red-500 text-white text-xs font-extrabold px-2.5 py-1 rounded-full shadow">SAVE {{ $savePercent }}%</span>
                            @endif
                        </div>
                        <div class="text-right shrink-0">
                            @if ($inStock)
                            <span class="text-xs text-emerald-600 font-bold flex items-center gap-1 justify-end"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> In Stock</span>
                            @else
                            <span class="text-xs text-rose-600 font-bold flex items-center gap-1 justify-end"><i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Out of Stock</span>
                            @endif
                            <span class="text-[10px] text-gray-500 block">
                                @if ($product->units_left)
                                Only {{ $product->units_left }} left · ships soon
                                @else
                                Cash on delivery nationwide
                                @endif
                            </span>
                        </div>
                    </div>

                    @if ($benefitLines->isNotEmpty())
                    <div class="space-y-2 text-xs sm:text-sm text-gray-600">
                        @foreach ($benefitLines as $benefit)
                        <p class="flex items-start gap-2.5"><i data-lucide="check" class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5"></i> <span>{{ $benefit }}</span></p>
                        @endforeach
                    </div>
                    @elseif ($product->short_description)
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $product->short_description }}</p>
                    @endif

                    @include('partials.product-pdp-bulk-savings')

                    <div id="pdp-buy-actions" class="flex flex-col sm:flex-row items-center gap-4 pt-2">
                        <div class="flex items-center border-2 border-gray-200 rounded-xl bg-white p-1">
                            <button type="button" onclick="adjustQty(-1)" aria-label="Decrease quantity" class="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center font-bold text-gray-600 transition">−</button>
                            <span id="product-qty" class="w-12 text-center font-extrabold text-[#0B132B] text-base" aria-live="polite">1</span>
                            <button type="button" onclick="adjustQty(1)" aria-label="Increase quantity" class="w-10 h-10 rounded-lg hover:bg-gray-100 flex items-center justify-center font-bold text-gray-600 transition">+</button>
                        </div>
                        <button type="button" onclick="addCurrentProductToCart()" class="flex-1 w-full bg-[#0B132B] hover:bg-[#4361EE] text-white font-bold py-4 px-6 rounded-xl shadow-lg transition flex items-center justify-center gap-2 text-sm sm:text-base">
                            <i data-lucide="shopping-bag" class="w-5 h-5 text-[#D4AF37]"></i>
                            <span>Add to Bag — <span id="btn-total-price">{{ MoneyFormat::pkr($product->price) }}</span></span>
                        </button>
                    </div>

                    <button type="button" onclick="buyNow()" class="w-full bg-[#1C2541] hover:bg-[#4361EE] text-white font-bold py-3.5 rounded-xl transition shadow flex items-center justify-center gap-2 text-sm">
                        <i data-lucide="zap" class="w-4 h-4 text-[#D4AF37]"></i>
                        <span>Buy Now with Express Checkout</span>
                    </button>

                    <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between text-xs text-gray-400 gap-2">
                        <span class="flex items-center gap-1 text-gray-500"><i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i> Guaranteed Safe &amp; Secure Checkout</span>
                        <div class="flex items-center gap-2 text-[11px] font-bold text-gray-500">
                            <span class="px-2 py-1 rounded-md bg-gray-50 border border-gray-200">Visa</span>
                            <span class="px-2 py-1 rounded-md bg-gray-50 border border-gray-200">Mastercard</span>
                            <span class="px-2 py-1 rounded-md bg-gray-50 border border-gray-200">COD</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <label for="postal-input" class="block text-[11px] font-extrabold text-gray-700 uppercase tracking-wide">Check Delivery / Postal Code</label>
                        <div class="flex items-center gap-2">
                            <input type="text" id="postal-input" name="postal_code" inputmode="numeric" autocomplete="postal-code" placeholder="e.g. 54000 (Lahore / Karachi)" class="flex-1 py-2.5 px-3 bg-gray-50 border border-gray-200 rounded-xl text-xs text-gray-800 placeholder-gray-400 focus:outline-none focus:border-[#0B132B]">
                            <button type="button" onclick="checkDelivery()" class="px-4 py-2.5 bg-[#0B132B] text-white text-xs font-bold rounded-xl hover:bg-[#4361EE] transition min-h-[44px]">Check</button>
                        </div>
                        <div id="delivery-result" class="text-xs font-semibold text-emerald-600 hidden"></div>
                    </div>
                </div>
            </div>
        </section>

        @include('partials.product-bundle-section', ['bundlePayload' => $bundlePayload ?? null])

        @include('partials.recently-viewed-section', [
            'recentlyVariant' => 'pdp',
            'excludeProductId' => $product->id,
            'recentlyLimit' => 5,
        ])

        <section class="w-full max-w-[1600px] mx-auto px-4 sm:px-8 mt-12" aria-labelledby="pdp-details-heading">
            <h2 id="pdp-details-heading" class="sr-only">Product details, specifications, and reviews</h2>
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="flex flex-wrap border-b border-gray-100 bg-gray-50/50" role="tablist" aria-label="Product information tabs">
                    <button type="button" role="tab" aria-selected="true" aria-controls="tab-content-desc" onclick="switchTab('desc')" id="tab-btn-desc" class="tab-btn is-active flex-1 min-w-[150px] py-4 px-4 sm:px-6 text-center font-bold text-xs sm:text-sm border-b-2 transition">
                        1. Description &amp; Benefits
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-specs" onclick="switchTab('specs')" id="tab-btn-specs" class="tab-btn flex-1 min-w-[150px] py-4 px-4 sm:px-6 text-center font-bold text-xs sm:text-sm border-b-2 transition">
                        2. Ingredients &amp; Formula
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-routine" onclick="switchTab('routine')" id="tab-btn-routine" class="tab-btn flex-1 min-w-[150px] py-4 px-4 sm:px-6 text-center font-bold text-xs sm:text-sm border-b-2 transition">
                        3. How to Use Guide
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-reviews" onclick="switchTab('reviews')" id="tab-btn-reviews" class="tab-btn flex-1 min-w-[150px] py-4 px-4 sm:px-6 text-center font-bold text-xs sm:text-sm border-b-2 transition">
                        4. Customer Reviews ({{ number_format((int) $product->reviews) }})
                    </button>
                </div>

                <div class="p-6 sm:p-10">
                    <div id="tab-content-desc" role="tabpanel" aria-labelledby="tab-btn-desc" class="tab-panel space-y-6">
                        <h3 class="text-xl font-extrabold text-[#0B132B]">{{ $product->detail_heading ?: $product->name }}</h3>
                        @if ($product->detail_body)
                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $product->detail_body }}</p>
                        @elseif ($product->short_description)
                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed whitespace-pre-line">{{ $product->short_description }}</p>
                        @elseif ($product->description)
                        <p class="text-gray-600 text-sm sm:text-base leading-relaxed">{{ $product->description }}</p>
                        @else
                        <p class="text-sm text-gray-400">Detailed description coming soon.</p>
                        @endif
                        @if (filled($product->detail_highlights))
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-4">
                            @foreach ($product->detail_highlights as $highlightIndex => $highlight)
                            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <i data-lucide="{{ $highlightIcons[$highlightIndex % count($highlightIcons)] }}" class="w-6 h-6 text-[#D4AF37] mb-3"></i>
                                <h4 class="font-bold text-sm text-[#0B132B] mb-1">{{ $highlight['title'] ?? '' }}</h4>
                                <p class="text-xs text-gray-500">{{ $highlight['description'] ?? '' }}</p>
                            </div>
                            @endforeach
                        </div>
                        @endif

                        @php
                            $productFaqs = collect($product->faq_items ?? [])->filter(fn ($row) => filled($row['question'] ?? null));
                        @endphp
                        @if ($productFaqs->isNotEmpty())
                        <div class="pt-6 mt-2 border-t border-gray-100 space-y-4">
                            <h3 class="text-xl font-extrabold text-[#0B132B]">Frequently Asked Questions</h3>
                            <div class="space-y-3">
                                @foreach ($productFaqs as $faqIndex => $faq)
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 overflow-hidden">
                                    <button type="button" onclick="toggleProductFaq({{ $faqIndex }})" class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 hover:bg-gray-100 transition focus:outline-none">
                                        <span class="text-xs sm:text-sm font-bold text-[#0B132B]">{{ $faq['question'] ?? '' }}</span>
                                        <i data-lucide="chevron-down" id="product-faq-chevron-{{ $faqIndex }}" class="w-4 h-4 text-gray-400 shrink-0 transition-transform duration-300"></i>
                                    </button>
                                    <div id="product-faq-answer-{{ $faqIndex }}" class="hidden px-5 sm:px-6 pb-5 pt-0 text-xs sm:text-sm text-gray-600 leading-relaxed border-t border-gray-100 bg-white">
                                        <p class="pt-3">{{ $faq['answer'] ?? '' }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div id="tab-content-specs" role="tabpanel" aria-labelledby="tab-btn-specs" hidden class="tab-panel hidden space-y-6">
                        <h3 class="text-xl font-extrabold text-[#0B132B]">{{ $product->specs_heading ?: 'Specifications & Formula' }}</h3>
                        @if (filled($product->specs_rows))
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($product->specs_rows as $row)
                            <div class="flex items-start space-x-3 bg-gray-50 p-4 rounded-2xl border border-gray-100">
                                <i data-lucide="droplet" class="w-5 h-5 text-[#4361EE] mt-0.5 shrink-0"></i>
                                <div>
                                    <h4 class="font-bold text-sm text-[#0B132B]">{{ $row['label'] ?? '' }}</h4>
                                    <p class="text-xs text-gray-500 mt-1">{{ $row['value'] ?? '' }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400">Specifications will be added soon.</p>
                        @endif
                    </div>

                    <div id="tab-content-routine" role="tabpanel" aria-labelledby="tab-btn-routine" hidden class="tab-panel hidden space-y-6">
                        <h3 class="text-xl font-extrabold text-[#0B132B]">{{ $product->routine_heading ?: 'Step-by-Step Application Guide' }}</h3>
                        @if ($routineSteps->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($routineSteps as $stepIndex => $step)
                            <div class="flex items-start space-x-4 bg-gray-50 p-5 rounded-2xl border border-gray-100">
                                <div class="w-8 h-8 rounded-full bg-[#0B132B] text-[#D4AF37] font-bold flex items-center justify-center flex-shrink-0 text-sm">{{ $stepIndex + 1 }}</div>
                                <p class="text-sm text-gray-600 leading-relaxed">{{ $step }}</p>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-sm text-gray-400">Routine instructions coming soon.</p>
                        @endif
                    </div>

                    <div id="tab-content-reviews" role="tabpanel" aria-labelledby="tab-btn-reviews" hidden class="tab-panel hidden space-y-6">
                        <div id="customer-reviews" class="scroll-mt-28"></div>
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-gray-100">
                            <div>
                                <h3 class="text-xl font-extrabold text-[#0B132B]">Customer Reviews &amp; Ratings</h3>
                                <div class="flex items-center space-x-2 mt-1 flex-wrap">
                                    @include('partials.product-star-rating', ['rating' => $product->rating, 'size' => 'w-4 h-4'])
                                    <span class="text-sm font-bold text-[#0B132B]">{{ number_format((float) $product->rating, 1) }} out of 5.0</span>
                                    <span class="text-xs text-gray-400">(Based on {{ number_format((int) $product->reviews) }} verified reviews)</span>
                                </div>
                            </div>
                            <button type="button" onclick="openReviewModal()" class="bg-[#0B132B] hover:bg-[#4361EE] text-white font-bold px-6 py-3 rounded-xl text-xs transition shadow">
                                Write a Review
                            </button>
                        </div>

                        <form id="product-review-form" class="p-5 rounded-2xl border border-gray-100 bg-gray-50 space-y-4" onsubmit="handleReviewSubmit(event)">
                            <h4 class="text-sm font-bold text-[#0B132B]">Share your experience</h4>
                            <div>
                                <span id="review-rating-label" class="text-[11px] font-bold text-gray-600 uppercase tracking-wide">Your rating</span>
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
                                    <label for="review-author-name" class="text-[11px] font-bold text-gray-600">Name</label>
                                    <input id="review-author-name" name="author_name" type="text" required maxlength="120" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-[#0B132B] focus:outline-none">
                                </div>
                                <div>
                                    <label for="review-author-email" class="text-[11px] font-bold text-gray-600">Email (optional)</label>
                                    <input id="review-author-email" name="author_email" type="email" maxlength="255" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-[#0B132B] focus:outline-none">
                                </div>
                            </div>
                            <div>
                                <label for="review-title" class="text-[11px] font-bold text-gray-600">Review title (optional)</label>
                                <input id="review-title" name="title" type="text" maxlength="200" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-[#0B132B] focus:outline-none">
                            </div>
                            <div>
                                <label for="review-body" class="text-[11px] font-bold text-gray-600">Your review</label>
                                <textarea id="review-body" name="body" required minlength="10" rows="4" class="mt-1 w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-[#0B132B] focus:outline-none" placeholder="Tell others what you loved about this product…"></textarea>
                            </div>
                            <div>
                                <label for="review-photos" class="text-[11px] font-bold text-gray-600 uppercase tracking-wide">Add photos (optional)</label>
                                <p class="text-[10px] text-gray-500 mt-0.5 mb-2">Up to 3 images — JPG, PNG, or WebP, max 4 MB each.</p>
                                <label for="review-photos" class="flex flex-col items-center justify-center gap-2 w-full rounded-2xl border-2 border-dashed border-gray-200 bg-white px-4 py-6 cursor-pointer hover:border-[#0B132B] transition">
                                    <i data-lucide="image-plus" class="w-8 h-8 text-[#0B132B]"></i>
                                    <span class="text-xs font-bold text-[#0B132B]">Tap to upload photos</span>
                                </label>
                                <input id="review-photos" name="photos[]" type="file" accept="image/jpeg,image/png,image/webp" multiple class="sr-only" onchange="previewReviewPhotos(this)">
                                <div id="review-photo-preview" class="grid grid-cols-3 gap-2 mt-3 empty:hidden"></div>
                            </div>
                            <button type="submit" id="review-submit-btn" class="w-full sm:w-auto px-6 py-2.5 bg-[#D4AF37] hover:bg-yellow-500 text-[#0B132B] font-bold text-xs uppercase tracking-wider rounded-xl transition shadow">
                                Submit Review
                            </button>
                        </form>

                        <div id="product-reviews-list" class="space-y-4">
                            @forelse ($publishedReviews as $review)
                            <article class="bg-gray-50 p-6 rounded-2xl border border-gray-100">
                                <div class="flex justify-between items-center mb-2 gap-3">
                                    <div class="flex items-center space-x-2 min-w-0">
                                        <span class="font-bold text-sm text-[#0B132B] truncate">{{ $review->author_name }}</span>
                                        @if ($review->is_verified_buyer)
                                        <span class="text-xs text-emerald-600 flex items-center gap-1 shrink-0"><i data-lucide="check-circle" class="w-3.5 h-3.5"></i> Verified Buyer</span>
                                        @endif
                                    </div>
                                    <time class="text-xs text-gray-400 shrink-0" datetime="{{ $review->created_at->toIso8601String() }}">{{ $review->created_at->diffForHumans() }}</time>
                                </div>
                                @include('partials.product-star-rating', ['rating' => $review->rating, 'size' => 'w-3.5 h-3.5'])
                                @if ($review->title)
                                <p class="text-xs font-bold text-[#0B132B] mt-2">{{ $review->title }}</p>
                                @endif
                                <p class="text-gray-600 text-sm italic mt-2">"{{ $review->body }}"</p>
                                @if (count($review->photoUrls()) > 0)
                                <div class="flex flex-wrap gap-2 pt-3">
                                    @foreach ($review->photoUrls() as $photoUrl)
                                    <a href="{{ $photoUrl }}" target="_blank" rel="noopener" class="block w-20 h-20 sm:w-24 sm:h-24 rounded-xl overflow-hidden border border-gray-200">
                                        <img src="{{ $photoUrl }}" alt="Review photo" class="w-full h-full object-cover">
                                    </a>
                                    @endforeach
                                </div>
                                @endif
                            </article>
                            @empty
                            <div class="text-center py-8 text-gray-400 text-sm border border-dashed border-gray-200 rounded-2xl">
                                No reviews yet. Be the first to share your experience.
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="w-full max-w-[1600px] mx-auto px-4 sm:px-8 mt-16">
            <div class="flex items-center justify-between mb-8 gap-4">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-[#0B132B]">You May Also Like</h2>
                    <p class="text-sm text-gray-500">Pair with our top-rated complementary formulas</p>
                </div>
                <a href="{{ route('collections') }}" class="text-xs font-bold text-[#4361EE] hover:underline flex items-center gap-1 shrink-0">View All <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
            </div>
            <div id="related-product-grid" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-6"></div>
        </section>

    </main>
@endsection


@section('below_main')
    <!-- Sticky checkout — slides up when inline buy buttons leave the viewport -->
    <div id="pdp-sticky-checkout" class="fixed inset-x-0 z-[45] max-md:bottom-[3.65rem] md:bottom-0 translate-y-full opacity-0 pointer-events-none transition-all duration-300 ease-[cubic-bezier(0.22,1,0.36,1)]" aria-hidden="true">
        <div class="border-t border-gray-200 bg-white/95 backdrop-blur-xl shadow-[0_-12px_40px_rgba(11,19,43,0.12)]">
            <div class="w-full px-4 sm:px-8 py-3 md:py-3.5 max-w-[1600px] mx-auto">
                <div class="flex flex-col gap-2.5 md:flex-row md:items-center md:gap-4">
                    <div class="flex items-center gap-3 min-w-0 md:flex-1">
                        <img src="{{ $galleryImages[0] ?? $product->image }}" alt="" class="hidden sm:block w-11 h-11 rounded-xl object-cover border border-gray-100 shadow-sm shrink-0">
                        <div class="min-w-0 flex-1">
                            <p class="text-[10px] uppercase tracking-wider font-bold text-[#D4AF37] leading-none mb-0.5">Ready to order</p>
                            <p class="text-xs sm:text-sm font-bold text-[#0B132B] truncate">{{ $product->name }}</p>
                            <p class="text-sm font-black text-[#0B132B] mt-0.5"><span id="sticky-btn-total-price">{{ MoneyFormat::pkr($product->price) }}</span></p>
                        </div>
                    </div>
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3 w-full md:w-auto md:shrink-0">
                        <div class="flex items-center gap-2 sm:gap-3">
                            <div class="flex items-center border-2 border-gray-200 rounded-xl bg-white overflow-hidden shadow-sm shrink-0">
                                <button type="button" onclick="adjustQty(-1)" class="px-3 py-2.5 text-sm font-extrabold text-gray-600 hover:bg-gray-50 transition" aria-label="Decrease quantity">−</button>
                                <span id="sticky-product-qty" class="w-8 text-center text-sm font-bold text-[#0B132B]">1</span>
                                <button type="button" onclick="adjustQty(1)" class="px-3 py-2.5 text-sm font-extrabold text-gray-600 hover:bg-gray-50 transition" aria-label="Increase quantity">+</button>
                            </div>
                            <button type="button" onclick="addCurrentProductToCart()" class="flex-1 sm:flex-none sm:min-w-[11rem] bg-[#0B132B] hover:bg-[#4361EE] text-white font-bold py-3 px-4 sm:px-5 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-[11px] sm:text-xs uppercase tracking-wider">
                                <i data-lucide="shopping-bag" class="w-4 h-4 text-[#D4AF37] shrink-0"></i>
                                <span class="truncate">Add to Bag</span>
                            </button>
                        </div>
                        <button type="button" onclick="buyNow()" class="w-full sm:w-auto sm:min-w-[14rem] bg-[#1C2541] hover:bg-[#4361EE] text-white font-bold py-3 px-5 rounded-xl transition shadow-lg flex items-center justify-center gap-2 text-[11px] sm:text-xs uppercase tracking-wider">
                            <i data-lucide="zap" class="w-4 h-4 text-[#D4AF37] shrink-0"></i>
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
<script src="{{ asset('js/pdp-pricing.js') }}?v=20261107pdp1" defer></script>
<script>
    const BEAUTAGE_REVIEW_STORE_URL = @json(route('product.reviews.store', $product));
    const BEAUTAGE_PRODUCT_VIDEO_URL = @json($product->video_url);
    const BEAUTAGE_BUNDLE = @json($bundlePayload ?? null);
    window.BEAUTAGE_PRODUCT_SLUG = @json($product->slug);
    window.galleryImages = galleryImages;
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
            const images = window.galleryImages || (typeof galleryImages !== 'undefined' ? galleryImages : []);
            if (!images[index]) {
                return;
            }
            mainImg.src = images[index];
            document.querySelectorAll('.thumb-btn').forEach((btn, idx) => {
                const active = idx === index;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-pressed', active ? 'true' : 'false');
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
                const btn = document.getElementById(`tab-btn-${k}`);
                if (!panel || !btn) {
                    return;
                }
                const active = k === tabKey;
                panel.classList.toggle('hidden', !active);
                panel.hidden = !active;
                btn.classList.toggle('is-active', active);
                btn.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        }

        function renderRelatedProducts() {
            const container = document.getElementById('related-product-grid');
            if (!container) {
                return;
            }
            const items = (relatedProducts || []).slice(0, 4);
            if (items.length === 0) {
                container.innerHTML = '<p class="col-span-full text-center text-sm text-gray-500 py-8">More products in this category coming soon.</p>';
                return;
            }
            container.innerHTML = items.map(p => {
                const productUrl = p.slug ? `/products/${encodeURIComponent(p.slug)}` : '/collections';
                const safeName = (p.name || '').replace(/\\/g, '\\\\').replace(/'/g, "\\'");
                const safeImage = (p.image || '').replace(/'/g, '');
                const compare = Number(p.oldPrice || 0) > Number(p.price || 0)
                    ? `<span class="pdp-related-compare">${BeautageMoney.formatDecimal(p.oldPrice)}</span>`
                    : '';
                const badge = p.badge || (Number(p.oldPrice || 0) > Number(p.price || 0) ? 'SALE' : 'POPULAR');
                return `
                <article class="pdp-related-card group">
                    <a href="${productUrl}" class="pdp-related-media">
                        <span class="pdp-related-badge">${badge}</span>
                        <img src="${safeImage}" alt="${safeName}">
                    </a>
                    <div>
                        <span class="pdp-related-cat">${p.category || ''}</span>
                        <h3 class="line-clamp-2"><a href="${productUrl}">${p.name || ''}</a></h3>
                        <div class="pdp-related-row">
                            <div>
                                <span class="pdp-related-price">${BeautageMoney.formatDecimal(p.price)}</span>
                                ${compare}
                            </div>
                            <button type="button" onclick="addToCartDirect({id: ${Number(p.id)}, name: '${safeName}', price: ${Number(p.price)}, image: '${safeImage}', qty: 1}); showToast('Added to bag');" class="pdp-related-add">+ Add</button>
                        </div>
                    </div>
                </article>
            `;
            }).join('');
        }

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                const announcement = document.getElementById('announcement-timer');
                if (announcement) {
                    announcement.innerText = `${h}:${m}:${s}`;
                }
                const countdown = document.getElementById('pdp-countdown');
                if (countdown) {
                    countdown.innerText = `${h}:${m}:${s}`;
                }
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
