@php
    $heroTitle = $page->hero_title ?: $page->title;
    $breadcrumb = $page->title;
@endphp
<div class="w-full px-4 sm:px-12 xl:px-20 mb-8">
    <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap mb-4 overflow-x-auto no-scrollbar">
        <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
            <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
        </a>
        <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-purple-300"></i>
        <span class="text-beautage-purple font-bold">{{ $breadcrumb }}</span>
    </nav>

    <div class="rounded-3xl bg-gradient-to-r from-purple-950 via-beautage-purple to-purple-900 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
            @if($page->hero_badge)
                <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                    {{ $page->hero_badge }}
                </span>
            @endif
            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">{{ $heroTitle }}</h1>
            @if($page->hero_subtitle)
                <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">{{ $page->hero_subtitle }}</p>
            @endif
            @php $buttons = $page->hero_buttons ?? []; @endphp
            @if(count($buttons))
                <div class="relative z-10 flex flex-col sm:flex-row gap-3 pt-2">
                    @foreach($buttons as $button)
                        @if(! empty($button['label']))
                            <a href="{{ $button['url'] ?? '#' }}"
                               class="px-6 py-3.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30 text-center">
                                {{ $button['label'] }}
                            </a>
                        @endif
                    @endforeach
                </div>
            @endif
        </div>
        @if($page->hero_stat_value || $page->hero_stat_label)
            <div class="relative z-10 w-full md:w-auto shrink-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-center shadow-lg min-w-[140px]">
                    @if($page->hero_stat_value)
                        <span class="text-2xl sm:text-3xl font-black text-amber-300 block">{{ $page->hero_stat_value }}</span>
                    @endif
                    @if($page->hero_stat_label)
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-bold">{{ $page->hero_stat_label }}</span>
                    @endif
                </div>
            </div>
        @endif
    </div>
</div>
