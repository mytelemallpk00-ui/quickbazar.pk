@php
    $showcase = $homepage['showcase'] ?? [];
@endphp
<section id="category-showcase" class="w-full px-6 sm:px-12 xl:px-20 py-12 bg-white border-y border-purple-100 my-4 shadow-xs">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
        <div>
            @if(filled($showcase['badge'] ?? null))
            <span class="text-xs font-extrabold uppercase tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200 inline-block mb-2">{{ $showcase['badge'] }}</span>
            @endif
            @if(filled($showcase['heading'] ?? null))
            <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">{{ $showcase['heading'] }}</h2>
            @endif
            @if(filled($showcase['description'] ?? null))
            <p class="text-xs text-slate-500 mt-1">{{ $showcase['description'] }}</p>
            @endif
        </div>
        @if($shopCategories->isNotEmpty())
        <div class="flex items-center gap-2 overflow-x-auto pb-2 md:pb-0 no-scrollbar">
            @foreach($shopCategories as $index => $category)
            <button type="button" data-showcase-tab="{{ $category->slug }}" id="showcase-tab-{{ $category->slug }}" class="showcase-tab px-4 py-2 rounded-xl text-xs font-bold whitespace-nowrap {{ $index === 0 ? 'bg-beautage-purple text-white shadow-sm' : 'bg-purple-50 text-beautage-purple hover:bg-purple-100' }} transition">
                {{ $category->displayNavLabel() }}
            </button>
            @endforeach
        </div>
        @endif
    </div>
    <div id="showcase-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-4 sm:gap-6"></div>
</section>
