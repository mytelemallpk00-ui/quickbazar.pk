@extends('layouts.app')

@section('title', 'Exclusive Offers & Bundle Deals — Quick Bazar')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-6">
    <div class="w-full px-4 sm:px-12 xl:px-20 mb-6">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap mb-4 overflow-x-auto no-scrollbar">
            <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
            <span class="text-beautage-pink font-bold">Exclusive Offers</span>
        </nav>

        <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-10 shadow-xl relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/25 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl space-y-2 text-center md:text-left">
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                    <i data-lucide="flame" class="w-3.5 h-3.5 fill-current"></i> Bundle deals
                </span>
                <h1 class="text-2xl sm:text-4xl font-serif font-bold tracking-tight">Exclusive Offers</h1>
                <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                    Curated frequently-bought-together sets with extra savings. Open any deal to customize items and add the bundle to your bag on the product page.
                </p>
            </div>
        </div>
    </div>

    <section class="w-full px-4 sm:px-12 xl:px-20 py-4">
        @if ($deals->isEmpty())
            <div class="max-w-xl mx-auto text-center bg-white rounded-3xl border border-purple-100 p-10 shadow-sm">
                <i data-lucide="gift" class="w-12 h-12 text-purple-300 mx-auto mb-4"></i>
                <h2 class="text-lg font-bold text-beautage-purple">New offers coming soon</h2>
                <p class="text-sm text-slate-600 mt-2">Check back shortly or browse our full catalog.</p>
                <a href="{{ route('collections') }}" class="inline-block mt-6 px-6 py-3 bg-beautage-purple text-white text-xs font-bold uppercase tracking-wider rounded-xl hover:bg-beautage-purpleDark transition">
                    Shop collections
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 max-w-6xl mx-auto">
                @foreach ($deals as $deal)
                <a href="{{ $deal['url'] }}" class="group bg-white rounded-3xl border border-purple-100 shadow-lg hover:shadow-xl hover:border-pink-200 transition overflow-hidden flex flex-col">
                    <div class="aspect-[4/3] bg-purple-50 relative overflow-hidden">
                        @if ($deal['image'])
                        <img src="{{ $deal['image'] }}" alt="{{ $deal['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" loading="lazy">
                        @else
                        <div class="w-full h-full flex items-center justify-center text-purple-300">
                            <i data-lucide="image" class="w-12 h-12"></i>
                        </div>
                        @endif
                        <span class="absolute top-3 left-3 px-2.5 py-1 rounded-lg text-[9px] font-extrabold uppercase tracking-wider bg-beautage-pink text-white shadow">
                            {{ $deal['badge'] }}
                        </span>
                    </div>
                    <div class="p-5 flex-1 flex flex-col">
                        <h2 class="text-sm font-bold text-slate-800 group-hover:text-beautage-purple transition line-clamp-2">{{ $deal['title'] }}</h2>
                        <p class="text-[11px] text-slate-500 mt-1.5 line-clamp-2">{{ $deal['subtitle'] }}</p>
                        <p class="text-[10px] text-purple-500 font-semibold mt-2">
                            {{ $deal['companionCount'] }} companion {{ $deal['companionCount'] === 1 ? 'item' : 'items' }} included
                        </p>
                        <div class="mt-auto pt-4 flex items-end justify-between gap-2">
                            <div>
                                <span class="text-[10px] uppercase text-slate-400 font-bold block">Bundle from</span>
                                <span class="text-xl font-black text-beautage-purple">{{ $deal['discountedFormatted'] }}</span>
                                @if ($deal['savings'] > 0)
                                <span class="text-xs text-slate-400 line-through ms-1">{{ $deal['subtotalFormatted'] }}</span>
                                @endif
                            </div>
                            @if ($deal['savings'] > 0)
                            <span class="text-[10px] font-extrabold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg border border-emerald-100">
                                Save {{ $deal['savingsFormatted'] }}
                            </span>
                            @endif
                        </div>
                        <span class="mt-4 w-full text-center py-2.5 rounded-xl bg-purple-50 text-beautage-purple text-xs font-bold group-hover:bg-beautage-pink group-hover:text-white transition">
                            View bundle deal
                        </span>
                    </div>
                </a>
                @endforeach
            </div>
        @endif
    </section>
</main>
@endsection
