@php
    $promo = $homepage['featured_promo'] ?? [];
    $product = $featuredPromoProduct;
    $promoImage = filled($promo['image_url'] ?? null)
        ? \App\Support\HomepageContent::imageUrl($promo['image_url'], 'Featured')
        : ($product?->image);
@endphp
<section id="featured-section" class="w-full px-6 sm:px-12 xl:px-20 py-8">
    <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-8 sm:p-12 xl:p-16 border border-purple-800 shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8">
        <div class="absolute -top-24 -left-24 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-xl z-10">
            @if(filled($promo['eyebrow'] ?? null))
            <span class="text-xs font-bold uppercase tracking-widest text-pink-300">{{ $promo['eyebrow'] }}</span>
            @endif
            <h2 class="text-3xl sm:text-4xl font-serif font-bold mt-2 leading-tight">
                @if($product)
                <a href="{{ route('product.show', $product) }}" class="hover:text-pink-200 transition">{{ $product->name }}</a>
                @elseif(filled($promo['title'] ?? null))
                {{ $promo['title'] }}
                @endif
            </h2>
            <p class="text-purple-100 text-xs sm:text-sm mt-3 leading-relaxed">
                {{ $product?->short_description ?: ($product?->description ?: ($promo['description'] ?? '')) }}
            </p>
            <div class="mt-6 flex items-center gap-4">
                @if($product)
                <span class="text-3xl font-extrabold text-white">Rs. {{ number_format($product->price, 0) }}</span>
                @if($product->old_price)
                <span class="text-sm text-purple-300 line-through">Rs. {{ number_format($product->old_price, 0) }}</span>
                @endif
                @endif
            </div>
            @if($product)
            <div class="mt-8 flex flex-wrap items-center gap-4">
                <button type="button" onclick="addToCart({{ $product->id }})" class="px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30">
                    Add To Bag
                </button>
                <a href="{{ route('product.show', $product) }}" class="px-6 py-3 bg-white/10 hover:bg-white/20 text-white font-bold text-xs uppercase tracking-wider rounded-xl transition border border-white/20 inline-block">
                    View Product
                </a>
            </div>
            @elseif(filled($promo['title'] ?? null))
            <div class="mt-8">
                <a href="{{ route('collections') }}" class="px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30 inline-block">
                    Shop Collections
                </a>
            </div>
            @endif
        </div>
        <div class="relative z-10 w-full lg:w-1/2 flex justify-center">
            @if($promoImage)
            <div class="w-full max-w-md rounded-2xl overflow-hidden border border-white/20 shadow-2xl">
                <img src="{{ $promoImage }}" alt="{{ $promo['visual_title'] ?? 'Featured' }}" class="w-full h-auto object-cover">
            </div>
            @else
            <div class="w-full max-w-md h-60 rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 flex flex-col items-center justify-center shadow-2xl relative p-6 text-center">
                <i data-lucide="sparkles" class="w-16 h-16 text-pink-300 mb-3"></i>
                @if(filled($promo['visual_title'] ?? null))
                <span class="text-sm font-serif font-bold text-white">{{ $promo['visual_title'] }}</span>
                @endif
                @if(filled($promo['visual_subtitle'] ?? null))
                <span class="text-xs text-purple-200 mt-1">{{ $promo['visual_subtitle'] }}</span>
                @endif
                @if(filled($promo['visual_badge'] ?? null))
                <span class="absolute bottom-4 right-4 text-[10px] bg-beautage-pink text-white px-2.5 py-1 rounded-full font-bold shadow">{{ $promo['visual_badge'] }}</span>
                @endif
            </div>
            @endif
        </div>
    </div>
</section>
