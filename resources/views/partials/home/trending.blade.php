@php
    $trending = $homepage['trending'] ?? [];
@endphp
<section id="trending-section" class="w-full px-6 sm:px-12 xl:px-20 py-12">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            @if(filled($trending['badge'] ?? null))
            <div class="flex items-center gap-2 text-beautage-amber font-bold text-sm tracking-wide uppercase mb-1">
                <i data-lucide="sparkles" class="w-4 h-4 fill-current"></i>
                <span>{{ $trending['badge'] }}</span>
            </div>
            @endif
            @if(filled($trending['heading'] ?? null))
            <h2 class="text-2xl sm:text-3xl font-bold text-beautage-purple">{{ $trending['heading'] }}</h2>
            @endif
            @if(filled($trending['description'] ?? null))
            <p class="text-slate-500 text-xs mt-1">{{ $trending['description'] }}</p>
            @endif
        </div>
        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
            <button type="button" data-trend-tab="all" id="tab-all" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold bg-beautage-purple text-white transition flex items-center gap-2 whitespace-nowrap shadow-xs">
                <i data-lucide="layers" class="w-4 h-4"></i> All Items
            </button>
            @foreach($shopCategories as $category)
            <button type="button" data-trend-tab="{{ $category->slug }}" id="tab-{{ $category->slug }}" class="tab-btn px-4 py-2 rounded-xl text-xs font-semibold bg-white hover:bg-purple-50 text-slate-700 border border-purple-100 transition flex items-center gap-2 whitespace-nowrap">
                <i data-lucide="{{ $category->iconName() }}" class="w-4 h-4 text-beautage-pink"></i> {{ $category->displayNavLabel() }}
            </button>
            @endforeach
        </div>
    </div>
    <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 sm:gap-6"></div>
</section>
