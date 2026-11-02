@php
    $flash = $homepage['flash_deals'] ?? [];
    $spotlight = $flashSpotlightProduct;
@endphp
<section id="flash-deals" class="w-full px-6 sm:px-12 xl:px-20 py-16 bg-gradient-to-b from-purple-900 via-beautage-purpleDark to-slate-950 text-white relative overflow-hidden my-6 shadow-2xl border-y border-pink-500/20">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-beautage-pink/20 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-[100px] pointer-events-none"></div>

    <div class="relative z-10 max-w-7xl mx-auto">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 mb-10 pb-6 border-b border-purple-800/80">
            <div class="space-y-2">
                @if(filled($flash['badge'] ?? null))
                <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-xs font-black bg-gradient-to-r from-beautage-pink to-amber-500 text-white shadow-lg animate-pulseGlow">
                    <i data-lucide="flame" class="w-4 h-4 fill-current text-amber-200"></i>
                    <span>{{ $flash['badge'] }}</span>
                </div>
                @endif
                <h2 class="text-3xl sm:text-4xl font-serif font-bold tracking-tight text-white flex items-center gap-3 flex-wrap">
                    @if(filled($flash['title'] ?? null))<span>{{ $flash['title'] }}</span>@endif
                    @if(filled($flash['title_badge'] ?? null))
                    <span class="text-xs font-sans font-extrabold px-3 py-1 rounded-lg bg-white/10 text-pink-200 border border-white/20">{{ $flash['title_badge'] }}</span>
                    @endif
                </h2>
                @if(filled($flash['description'] ?? null))
                <p class="text-purple-200 text-xs sm:text-sm font-medium max-w-xl">{{ $flash['description'] }}</p>
                @endif
            </div>
            <div class="flex items-center gap-4 bg-purple-950/90 border border-purple-600/60 p-4 rounded-2xl shadow-xl backdrop-blur-md">
                <div class="w-10 h-10 rounded-xl bg-beautage-pink/20 text-beautage-pink flex items-center justify-center shrink-0">
                    <i data-lucide="timer" class="w-5 h-5 text-pink-300 animate-spin" style="animation-duration: 12s;"></i>
                </div>
                <div>
                    <span class="text-[10px] text-purple-300 uppercase font-extrabold tracking-widest block">Flash Sale Closes In</span>
                    <div class="flex items-center gap-2 font-mono mt-0.5">
                        <div class="bg-purple-900 text-white px-2.5 py-1 rounded-lg text-sm font-black border border-purple-700 shadow-inner" id="flash-timer-hours">05</div>
                        <span class="font-bold text-pink-400">:</span>
                        <div class="bg-purple-900 text-white px-2.5 py-1 rounded-lg text-sm font-black border border-purple-700 shadow-inner" id="flash-timer-minutes">42</div>
                        <span class="font-bold text-pink-400">:</span>
                        <div class="bg-purple-900 text-white px-2.5 py-1 rounded-lg text-sm font-black border border-purple-700 shadow-inner" id="flash-timer-seconds">18</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2.5 overflow-x-auto pb-4 no-scrollbar mb-8">
            <button type="button" data-flash-tab="all" id="flash-tab-all" class="flash-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold bg-beautage-pink text-white transition shadow-md shadow-pink-500/30 whitespace-nowrap flex items-center gap-2">
                <i data-lucide="sparkles" class="w-3.5 h-3.5"></i> All Flash Deals
            </button>
            @foreach($shopCategories as $category)
            <button type="button" data-flash-tab="{{ $category->slug }}" id="flash-tab-{{ $category->slug }}" class="flash-filter-btn px-5 py-2.5 rounded-xl text-xs font-bold bg-purple-900/80 text-purple-200 hover:bg-purple-800 transition border border-purple-700/60 whitespace-nowrap flex items-center gap-2">
                <i data-lucide="{{ $category->iconName() }}" class="w-3.5 h-3.5 text-pink-300"></i> {{ $category->displayNavLabel() }}
            </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            @if($spotlight)
            <div class="lg:col-span-4 rounded-3xl bg-gradient-to-b from-purple-950 via-purple-900 to-slate-900 border-2 border-pink-500/40 p-6 shadow-2xl relative flex flex-col justify-between group">
                @if(filled($flash['spotlight_badge'] ?? null))
                <div class="absolute -top-3 right-6 bg-gradient-to-r from-beautage-pink to-amber-500 text-white text-[10px] font-black uppercase tracking-widest px-3.5 py-1 rounded-full shadow-lg">
                    {{ $flash['spotlight_badge'] }}@if($spotlight->discount) • {{ $spotlight->discount }}@endif
                </div>
                @endif
                <div>
                    <div class="relative rounded-2xl overflow-hidden bg-purple-950 aspect-square mb-5 border border-purple-700/60 shadow-inner">
                        <img src="{{ $spotlight->image }}" alt="{{ $spotlight->name }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null; this.src='https://placehold.co/500x500/3B0764/FFFFFF?text=Flash';">
                        <span class="absolute bottom-3 left-3 bg-black/70 backdrop-blur-md text-white text-[11px] font-extrabold px-3 py-1 rounded-lg">
                            ⭐ {{ $spotlight->rating }} ({{ $spotlight->reviews }} Reviews)
                        </span>
                    </div>
                    <span class="text-[11px] font-extrabold uppercase tracking-widest text-pink-300 block">{{ $spotlight->badge ?: $spotlight->category }}</span>
                    <h3 class="text-xl font-serif font-bold text-white mt-1 group-hover:text-pink-300 transition">
                        <a href="{{ route('product.show', $spotlight) }}" class="hover:text-pink-300">{{ $spotlight->name }}</a>
                    </h3>
                    @if($spotlight->description)
                    <p class="text-xs text-purple-200 mt-2 leading-relaxed line-clamp-3">{{ $spotlight->description }}</p>
                    @endif
                    @if($spotlight->claimed_percent)
                    <div class="mt-5 space-y-1.5">
                        <div class="flex justify-between text-[11px] font-bold">
                            <span class="text-amber-300 flex items-center gap-1"><i data-lucide="flame" class="w-3 h-3 fill-current"></i> {{ $spotlight->claimed_percent }}% Claimed</span>
                            @if($spotlight->claimed_text)<span class="text-purple-300">{{ $spotlight->claimed_text }}</span>@endif
                        </div>
                        <div class="w-full h-2 rounded-full bg-purple-950 overflow-hidden border border-purple-800">
                            <div class="h-full bg-gradient-to-r from-beautage-pink to-amber-400 rounded-full animate-pulse" style="width: {{ min(100, (int) $spotlight->claimed_percent) }}%"></div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="mt-6 pt-4 border-t border-purple-800/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs text-purple-300 uppercase block font-semibold">Flash Price</span>
                        <div class="flex items-baseline gap-2">
                            <span class="text-2xl font-black text-white">Rs. {{ number_format($spotlight->price, 0) }}</span>
                            @if($spotlight->old_price)
                            <span class="text-xs text-purple-400 line-through">Rs. {{ number_format($spotlight->old_price, 0) }}</span>
                            @endif
                        </div>
                    </div>
                    <button type="button" onclick="addToCart({{ $spotlight->id }})" class="px-5 py-3 bg-gradient-to-r from-beautage-pink to-pink-600 hover:from-pink-600 hover:to-beautage-pink text-white font-black text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/40 flex items-center justify-center gap-2">
                        <i data-lucide="shopping-bag" class="w-4 h-4"></i> Claim Deal
                    </button>
                </div>
            </div>
            @endif
            <div class="{{ $spotlight ? 'lg:col-span-8' : 'lg:col-span-12' }} grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6" id="flash-deals-grid-container"></div>
        </div>
    </div>
</section>
