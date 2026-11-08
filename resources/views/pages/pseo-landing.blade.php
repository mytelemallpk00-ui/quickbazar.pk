@extends('layouts.app')

@section('title', $pageSeo['title'] ?? $headline)

@section('content')
<main class="w-full flex-1 py-8 px-4 sm:px-12 xl:px-20">
    <nav class="text-xs font-semibold text-slate-500 mb-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('home') }}" class="hover:text-beautage-purple">Home</a>
        <span class="text-purple-300">/</span>
        <a href="{{ route('collections') }}" class="hover:text-beautage-purple">Shop</a>
        <span class="text-purple-300">/</span>
        <span class="text-beautage-purple font-bold">{{ $headline }}</span>
    </nav>

    <div class="rounded-3xl bg-gradient-to-br from-purple-950 via-beautage-purple to-purple-900 text-white p-8 sm:p-12 shadow-2xl relative overflow-hidden mb-8">
        <div class="absolute -right-16 -top-16 w-72 h-72 bg-beautage-pink/25 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 max-w-3xl">
            @if(filled($landing->location) || filled($landing->use_case))
            <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-white/10 text-pink-100 border border-white/20 mb-3">
                @if(filled($landing->location)){{ $landing->location }}@endif
                @if(filled($landing->location) && filled($landing->use_case)) · @endif
                @if(filled($landing->use_case)){{ $landing->use_case }}@endif
            </span>
            @endif
            <h1 class="text-2xl sm:text-4xl font-serif font-bold tracking-tight">{{ $headline }}</h1>
            @if(filled($introHtml))
            <div class="mt-4 text-sm text-purple-100/95 leading-relaxed">{!! $introHtml !!}</div>
            @endif
        </div>
    </div>

    <section>
        <div class="flex items-center justify-between gap-4 mb-4">
            <h2 class="text-lg font-bold text-beautage-purple">Shop this collection</h2>
            <a href="{{ route('collections', $activeCategorySlug ? ['cat' => $activeCategorySlug] : []) }}" class="text-xs font-bold text-beautage-pink hover:underline">View all →</a>
        </div>
        <div class="lv2-grid lv2-grid-4">
            @forelse($featuredProducts as $product)
            <a href="{{ route('product.show', $product) }}" class="group bg-white rounded-2xl border border-purple-100 p-3 shadow-sm hover:shadow-md transition">
                <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full aspect-square object-cover rounded-xl mb-2" loading="lazy">
                <h3 class="text-xs font-bold text-beautage-purple line-clamp-2 group-hover:text-beautage-pink transition">{{ $product->name }}</h3>
                <p class="text-sm font-extrabold text-slate-800 mt-1">Rs. {{ number_format((float) $product->price, 0) }}</p>
            </a>
            @empty
            <p class="col-span-full text-sm text-slate-500">Products coming soon — browse the <a class="text-beautage-pink font-bold" href="{{ route('collections') }}">full catalog</a>.</p>
            @endforelse
        </div>
    </section>
</main>
@endsection
