        <section class="pdp-tabs" aria-labelledby="pdp-details-heading">
            <h2 id="pdp-details-heading" class="sr-only">Product details, specifications, and reviews</h2>
            <div class="pdp-tabs-card">
                <div class="pdp-tabs-bar" role="tablist" aria-label="Product information tabs">
                    <button type="button" role="tab" aria-selected="true" aria-controls="tab-content-desc" onclick="switchTab('desc')" id="tab-btn-desc" class="is-on">
                        <i data-lucide="file-text" class="w-4 h-4 shrink-0"></i> <span>Description</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-specs" onclick="switchTab('specs')" id="tab-btn-specs">
                        <i data-lucide="cpu" class="w-4 h-4 shrink-0"></i> <span>Specs</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-routine" onclick="switchTab('routine')" id="tab-btn-routine">
                        <i data-lucide="sparkles" class="w-4 h-4 shrink-0"></i> <span>Routine</span>
                    </button>
                    <button type="button" role="tab" aria-selected="false" aria-controls="tab-content-reviews" onclick="switchTab('reviews')" id="tab-btn-reviews">
                        <i data-lucide="star" class="w-4 h-4 shrink-0"></i> <span>Reviews ({{ number_format((int) $product->reviews) }})</span>
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
                            @php
                                $faqQuestion = str_ireplace(['Beautage', 'Shop Pakistan'], 'Quick Bazar', (string) ($faq['question'] ?? ''));
                                $faqAnswer = str_ireplace(
                                    ['Beautage', 'Shop Pakistan', 'Rs 7,500', 'Rs. 7,500'],
                                    ['Quick Bazar', 'Quick Bazar', \App\Support\MoneyFormat::pkr($freeShipAt), \App\Support\MoneyFormat::pkr($freeShipAt)],
                                    (string) ($faq['answer'] ?? '')
                                );
                            @endphp
                            <div class="rounded-2xl border border-purple-100 bg-purple-50/30 overflow-hidden shadow-sm">
                                <button type="button" onclick="toggleProductFaq({{ $faqIndex }})" class="w-full p-4 sm:p-5 text-left flex items-center justify-between gap-4 hover:bg-purple-50/80 transition focus:outline-none">
                                    <span class="text-xs sm:text-sm font-bold text-beautage-purple flex items-start gap-3">
                                        <span class="w-6 h-6 rounded-full bg-white border border-purple-100 text-beautage-pink flex items-center justify-center text-[10px] font-extrabold shrink-0 mt-0.5">Q</span>
                                        <span>{{ $faqQuestion }}</span>
                                    </span>
                                    <i data-lucide="chevron-down" id="product-faq-chevron-{{ $faqIndex }}" class="w-4 h-4 text-slate-400 shrink-0 transition-transform duration-300"></i>
                                </button>
                                <div id="product-faq-answer-{{ $faqIndex }}" class="hidden px-5 sm:px-6 pb-5 pt-0 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-100/80 bg-white/70">
                                    <p class="pt-3">{{ $faqAnswer }}</p>
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
                    <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">{{ $product->routine_heading ?: 'Step-by-Step Application Guide' }}</h3>
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
                                <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Customer Reviews &amp; Ratings</h3>
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
