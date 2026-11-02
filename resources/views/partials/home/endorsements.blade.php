@php
    $block = $homepage['endorsements'] ?? [];
    $cards = $block['cards'] ?? [];
@endphp
<section id="celebrity-endorsement" class="w-full px-6 sm:px-12 xl:px-20 py-16 bg-gradient-to-b from-purple-50/50 via-white to-purple-50/30 border-t border-purple-100">
    <div class="text-center max-w-2xl mx-auto mb-12">
        @if(filled($block['badge'] ?? null))
        <span class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-extrabold bg-pink-100 text-beautage-pink uppercase tracking-widest border border-pink-200 mb-3 shadow-xs">
            <i data-lucide="award" class="w-3.5 h-3.5"></i> {{ $block['badge'] }}
        </span>
        @endif
        @if(filled($block['heading'] ?? null))
        <h2 class="text-3xl sm:text-4xl font-serif font-bold text-beautage-purple tracking-tight">{{ $block['heading'] }}</h2>
        @endif
        @if(filled($block['description'] ?? null))
        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed">{{ $block['description'] }}</p>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-6">
        @foreach($cards as $card)
            @php
                $img = \App\Support\HomepageContent::imageUrl($card['image_url'] ?? null, $card['name'] ?? 'Star');
                $videoUrl = trim((string) ($card['video_url'] ?? ''));
                $productId = (int) ($card['product_id'] ?? 0);
                $productSlug = $productId > 0 ? ($endorsementProductSlugs[$productId] ?? null) : null;
                $cardHref = $videoUrl !== ''
                    ? $videoUrl
                    : ($productSlug ? route('product.show', $productSlug) : route('collections'));
                $isExternal = $videoUrl !== '';
            @endphp
            <div class="bg-white rounded-2xl overflow-hidden border border-purple-100 shadow-md hover:shadow-xl hover:border-beautage-pink transition duration-300 flex flex-col justify-between group">
                <a href="{{ $cardHref }}" @if($isExternal) target="_blank" rel="noopener noreferrer" @endif class="relative aspect-[3/4] bg-slate-900 overflow-hidden block">
                    <img src="{{ $img }}" alt="{{ $card['name'] ?? '' }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 opacity-90">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-transparent to-transparent"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-12 h-12 rounded-full bg-beautage-pink/90 text-white flex items-center justify-center shadow-lg group-hover:scale-110 transition">
                            <i data-lucide="play" class="w-5 h-5 fill-current ml-0.5"></i>
                        </div>
                    </div>
                    @if(filled($card['badge'] ?? null))
                    <span class="absolute top-3 left-3 bg-beautage-purple text-white text-[9px] font-extrabold px-2 py-0.5 rounded uppercase tracking-wider">{{ $card['badge'] }}</span>
                    @endif
                </a>
                <div class="p-4 bg-white flex-1 flex flex-col justify-between text-center">
                    <div>
                        @if(filled($card['name'] ?? null))
                        <h4 class="text-xs font-bold text-beautage-purple">{{ $card['name'] }}</h4>
                        @endif
                        @if(filled($card['quote'] ?? null))
                        <p class="text-[11px] text-slate-600 mt-1.5 leading-snug">{{ $card['quote'] }}</p>
                        @endif
                    </div>
                    <div class="mt-4 pt-2 border-t border-purple-50 flex items-center justify-center gap-1 text-[10px] font-bold text-beautage-pink">
                        <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i>
                        <span>Verified Review</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
