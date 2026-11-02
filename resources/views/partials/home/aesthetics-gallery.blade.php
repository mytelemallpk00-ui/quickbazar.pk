@php
    $gallery = $homepage['aesthetics_gallery'] ?? [];
    $cards = $gallery['cards'] ?? [];
@endphp
<section class="w-full px-6 sm:px-12 xl:px-20 py-10">
    <div class="text-center max-w-2xl mx-auto mb-8">
        @if(filled($gallery['badge'] ?? null))
        <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-purple-100 text-beautage-purple border border-purple-200 mb-2">{{ $gallery['badge'] }}</span>
        @endif
        @if(filled($gallery['heading'] ?? null))
        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">{{ $gallery['heading'] }}</h2>
        @endif
        @if(filled($gallery['description'] ?? null))
        <p class="text-xs text-slate-500 mt-1">{{ $gallery['description'] }}</p>
        @endif
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @foreach($cards as $card)
            @php
                $slug = trim((string) ($card['collection_slug'] ?? ''));
                $href = $slug !== '' ? route('collections', ['cat' => $slug]) : route('collections');
                $img = \App\Support\HomepageContent::imageUrl($card['image_url'] ?? null, $card['title'] ?? 'Collection');
            @endphp
            <a href="{{ $href }}" class="relative rounded-3xl overflow-hidden aspect-[4/5] group shadow-lg border border-purple-100 block">
                <img src="{{ $img }}" alt="{{ $card['title'] ?? 'Collection' }}" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
                <div class="absolute inset-0 bg-gradient-to-t from-beautage-purpleDark/90 via-beautage-purple/30 to-transparent"></div>
                <div class="absolute bottom-0 inset-x-0 p-6 text-white text-center transform transition duration-300 group-hover:-translate-y-2">
                    @if(filled($card['badge'] ?? null))
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-pink-300 block mb-1">{{ $card['badge'] }}</span>
                    @endif
                    @if(filled($card['title'] ?? null))
                    <h3 class="text-base font-serif font-bold">{{ $card['title'] }}</h3>
                    @endif
                    @if(filled($card['description'] ?? null))
                    <p class="text-[11px] text-purple-100 mt-1 opacity-90">{{ $card['description'] }}</p>
                    @endif
                </div>
            </a>
        @endforeach
    </div>
</section>
