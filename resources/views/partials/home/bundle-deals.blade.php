@if(isset($bundleDeals) && $bundleDeals->isNotEmpty())
<section id="bundle-deals" class="w-full px-6 sm:px-12 xl:px-20 py-8">
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-6">
        <div>
            <span class="text-[10px] font-extrabold uppercase tracking-widest text-beautage-pink">Bundle deals</span>
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple mt-1">Shop &amp; Save Together</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-xl">Curated sets from your catalog — extra discount jab aap combo complete karte hain.</p>
        </div>
        <a href="{{ route('exclusive-offers') }}" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-beautage-purple hover:text-beautage-pink transition shrink-0">
            All exclusive offers
            <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-5">
        @foreach ($bundleDeals as $deal)
        <a href="{{ $deal['url'] }}" class="group bg-white rounded-2xl border border-purple-100 shadow-md hover:shadow-xl hover:border-pink-200 transition overflow-hidden flex flex-col">
            <div class="aspect-[5/4] bg-purple-50 relative overflow-hidden">
                @if ($deal['image'])
                <img src="{{ $deal['image'] }}" alt="{{ $deal['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                @endif
                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md text-[8px] font-extrabold uppercase tracking-wider bg-beautage-pink text-white">
                    {{ $deal['badge'] }}
                </span>
            </div>
            <div class="p-4 flex-1 flex flex-col">
                <h3 class="text-xs font-bold text-slate-800 line-clamp-2 group-hover:text-beautage-purple transition">{{ $deal['title'] }}</h3>
                <p class="text-[10px] text-slate-500 mt-1 line-clamp-2">{{ $deal['subtitle'] }}</p>
                <div class="mt-auto pt-3 flex items-baseline gap-2">
                    <span class="text-lg font-black text-beautage-purple">{{ $deal['discountedFormatted'] }}</span>
                    @if ($deal['savings'] > 0)
                    <span class="text-[10px] text-slate-400 line-through">{{ $deal['subtotalFormatted'] }}</span>
                    @endif
                </div>
                @if ($deal['savings'] > 0)
                <span class="mt-1 text-[10px] font-bold text-emerald-600">Save {{ $deal['savingsFormatted'] }}</span>
                @endif
            </div>
        </a>
        @endforeach
    </div>
</section>
@endif
