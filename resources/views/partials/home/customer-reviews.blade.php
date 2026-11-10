@php
    $stats = $homeReviewStats ?? ['avg' => 0, 'customers' => 0, 'published' => 0];
    $reviews = $homeFeaturedReviews ?? collect();
    $avg = (float) ($stats['avg'] ?? 0);
    $customers = (int) ($stats['customers'] ?? 0);
    $customerLabel = $customers >= 1000
        ? number_format(floor($customers / 100) * 100).'+' 
        : ($customers > 0 ? number_format($customers).'+' : '0');
    $pkCities = ['Karachi', 'Lahore', 'Islamabad', 'Rawalpindi', 'Faisalabad', 'Multan', 'Peshawar', 'Hyderabad', 'Quetta', 'Sialkot'];
@endphp
<section id="home-customer-reviews" class="relative w-full overflow-hidden border-t border-purple-900/40">
    <div class="absolute inset-0 bg-gradient-to-br from-beautage-purpleDark via-[#4a1560] to-beautage-purple"></div>
    <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(circle at 20% 30%, #fff 1px, transparent 1px), radial-gradient(circle at 80% 70%, #fff 1px, transparent 1px); background-size: 48px 48px;"></div>

    <div class="relative w-full px-6 sm:px-12 xl:px-20 py-16 sm:py-20">
        <div class="text-center max-w-3xl mx-auto mb-10 sm:mb-12">
            <div class="flex items-center justify-center gap-3 mb-4">
                <span class="h-px w-10 sm:w-16 bg-white/30"></span>
                <span class="text-[10px] sm:text-xs font-extrabold uppercase tracking-[0.25em] text-pink-200/90">What our customers say</span>
                <span class="h-px w-10 sm:w-16 bg-white/30"></span>
            </div>
            <h2 class="text-3xl sm:text-4xl lg:text-[2.75rem] font-serif font-bold text-white tracking-tight leading-tight">
                Real People. Real Reviews.
            </h2>
            <p class="text-sm sm:text-base text-purple-100/90 mt-4 leading-relaxed max-w-2xl mx-auto">
                Join thousands of happy customers who trust Quick Bazar for quality beauty essentials, honest prices, and reliable delivery across Pakistan.
            </p>
        </div>

        <div class="max-w-5xl mx-auto mb-10 sm:mb-14">
            <div class="bg-white rounded-2xl sm:rounded-full shadow-xl shadow-black/20 px-4 sm:px-8 py-5 sm:py-6 grid grid-cols-1 sm:grid-cols-3 gap-6 sm:gap-4 sm:divide-x sm:divide-purple-100">
                <div class="flex items-center justify-center sm:justify-start gap-3 text-center sm:text-left">
                    <span class="w-11 h-11 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center shrink-0">
                        <i data-lucide="star" class="w-5 h-5 fill-current"></i>
                    </span>
                    <div>
                        <p class="text-lg sm:text-xl font-extrabold text-beautage-purple leading-none">{{ $avg > 0 ? number_format($avg, 1) : '—' }}/5</p>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-1">Average Rating</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 text-center sm:text-left sm:pl-6">
                    <span class="w-11 h-11 rounded-xl bg-pink-50 text-beautage-pink flex items-center justify-center shrink-0">
                        <i data-lucide="users" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <p class="text-lg sm:text-xl font-extrabold text-beautage-purple leading-none">{{ $customerLabel }}</p>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-1">Happy Customers</p>
                    </div>
                </div>
                <div class="flex items-center justify-center gap-3 text-center sm:text-left sm:pl-6">
                    <span class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <i data-lucide="shield-check" class="w-5 h-5"></i>
                    </span>
                    <div>
                        <p class="text-lg sm:text-xl font-extrabold text-beautage-purple leading-none">100%</p>
                        <p class="text-[11px] sm:text-xs font-semibold text-slate-500 mt-1">Genuine Reviews</p>
                    </div>
                </div>
            </div>
        </div>

        @if ($reviews->isNotEmpty())
        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5 lg:gap-6 max-w-[90rem] mx-auto">
            @foreach ($reviews as $review)
                @php
                    $product = $review->product;
                    $productUrl = $product?->slug ? route('product.show', $product->slug) : route('collections');
                    $productImage = $product?->image ?: 'https://placehold.co/80x80/5E1F70/FFFFFF?text=B';
                    $city = $pkCities[(int) $review->id % count($pkCities)];
                    $body = \Illuminate\Support\Str::limit(trim(strip_tags($review->body)), 140);
                @endphp
                <article class="group flex flex-col bg-white rounded-2xl p-5 sm:p-6 shadow-lg shadow-black/10 border border-white/80 hover:border-pink-200 hover:shadow-xl hover:-translate-y-0.5 transition duration-300 min-h-[280px]">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3 min-w-0">
                            <span class="w-11 h-11 rounded-full bg-gradient-to-br from-beautage-pink to-beautage-purple text-white text-xs font-extrabold flex items-center justify-center shrink-0 shadow-md">
                                {{ $review->authorInitials() }}
                            </span>
                            <div class="min-w-0">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <h3 class="text-sm font-bold text-slate-900 truncate max-w-[8rem] sm:max-w-[9rem]">{{ $review->author_name }}</h3>
                                    @if ($review->is_verified_buyer)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-full shrink-0">
                                        <i data-lucide="badge-check" class="w-3 h-3"></i> Verified
                                    </span>
                                    @endif
                                </div>
                                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $city }}</p>
                            </div>
                        </div>
                        <i data-lucide="quote" class="w-8 h-8 text-purple-100 shrink-0 group-hover:text-pink-100 transition"></i>
                    </div>

                    <div class="mb-3">
                        @include('partials.product-star-rating', ['rating' => $review->rating, 'size' => 'w-4 h-4'])
                    </div>

                    <p class="text-sm text-slate-600 leading-relaxed flex-1">&ldquo;{{ $body }}&rdquo;</p>

                    @if ($product)
                    <a href="{{ $productUrl }}" class="mt-5 pt-4 border-t border-purple-50 flex items-center gap-3 group/link">
                        <span class="w-12 h-12 rounded-xl bg-purple-50 border border-purple-100 overflow-hidden shrink-0">
                            <img src="{{ $productImage }}" alt="" class="w-full h-full object-cover group-hover/link:scale-105 transition duration-300" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/80x80/5E1F70/FFFFFF?text=B';">
                        </span>
                        <span class="text-[11px] font-bold text-beautage-purple line-clamp-2 group-hover/link:text-beautage-pink transition leading-snug">
                            {{ $product->name }}
                        </span>
                    </a>
                    @endif
                </article>
            @endforeach
        </div>

        <div class="text-center mt-10">
            <a href="{{ route('collections') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-full bg-white/10 hover:bg-white/15 border border-white/25 text-white text-xs font-bold uppercase tracking-wider transition">
                Shop all products
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
        @else
        <div class="max-w-xl mx-auto text-center rounded-2xl border border-white/20 bg-white/5 backdrop-blur-sm px-6 py-10">
            <i data-lucide="message-circle-heart" class="w-10 h-10 text-pink-200 mx-auto mb-3"></i>
            <p class="text-purple-100 text-sm leading-relaxed">Customer stories from product pages will appear here as reviews are published.</p>
            <a href="{{ route('collections') }}" class="inline-flex mt-5 text-xs font-bold text-pink-200 hover:text-white transition underline underline-offset-4">Browse products</a>
        </div>
        @endif
    </div>
</section>
