@php
    $item = $product ?? [];
    $name = (string) ($item['name'] ?? 'Product');
    $category = (string) ($item['category'] ?? '');
    $price = (float) ($item['price'] ?? 0);
    $old = (float) ($item['oldPrice'] ?? 0);
    $rating = max(0, min(5, (float) ($item['rating'] ?? 0)));
    $fullStars = (int) floor($rating);
    $halfStar = ($rating - $fullStars) >= 0.25;
    $reviewCount = (int) ($item['reviews'] ?? 0);
    $slug = (string) ($item['slug'] ?? '');
    $productUrl = $slug !== '' ? route('product.show', $slug) : route('collections');
    $image = $imageUrl($item['image'] ?? '', $name);
    $desc = \Illuminate\Support\Str::limit(trim(strip_tags((string) ($item['desc'] ?? ''))), 140);
    $sku = 'ID: '.(int) ($item['id'] ?? 0);
    $isFeature = ($variant ?? 'compact') === 'feature';
    $badgeText = $badge ?? null;
    $badgeClass = $badgeClass ?? 'bg-red-500 text-white';
@endphp

@if ($isFeature)
<div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100 flex flex-col justify-between">
    <div>
        <div class="flex justify-between items-center mb-4">
            @if (filled($badgeText))
            <span class="{{ $badgeClass }} text-[10px] font-bold px-3 py-1 rounded-full uppercase">{{ $badgeText }}</span>
            @else
            <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-3 py-1 rounded-full uppercase">In catalog</span>
            @endif
            <span class="text-xs text-gray-400">{{ $sku }}</span>
        </div>
        <a href="{{ $productUrl }}">
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-56 object-cover rounded-2xl mb-4 shadow" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/400x300/1C2541/F4E8C1?text=Beautage';">
        </a>
        <h3 class="font-bold text-lg text-brand-navy"><a href="{{ $productUrl }}" class="hover:text-brand-accent">{{ $name }}</a></h3>
        @if ($desc !== '')
        <p class="text-xs text-gray-500 mt-2">{{ $desc }}</p>
        @endif
    </div>
    <div class="mt-6 pt-4 border-t border-gray-100 flex items-center justify-between gap-3">
        <div>
            <span class="text-xl font-black text-brand-navy">{{ $money($price) }}</span>
            @if ($old > $price)
            <span class="text-xs text-gray-400 line-through ml-1">{{ $money($old) }}</span>
            @endif
        </div>
        <button type="button" onclick="addToCart({{ (int) ($item['id'] ?? 0) }})" class="bg-brand-gold hover:bg-yellow-500 text-brand-navy font-bold px-6 py-3 rounded-xl text-xs transition shadow">
            Add to Cart
        </button>
    </div>
</div>
@else
<div class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition border border-gray-100 p-3 sm:p-4 flex flex-col justify-between group">
    <div class="relative">
        @if (filled($badgeText))
        <span class="absolute top-2 left-2 {{ $badgeClass }} text-[9px] sm:text-[10px] font-bold px-2 py-0.5 sm:px-2.5 sm:py-1 rounded-full shadow">{{ $badgeText }}</span>
        @endif
        <button type="button" onclick="toggleWishlist({{ (int) ($item['id'] ?? 0) }})" data-wishlist-for="{{ (int) ($item['id'] ?? 0) }}" class="absolute top-2 right-2 w-7 h-7 sm:w-8 sm:h-8 rounded-full bg-white/80 hover:bg-white text-gray-600 hover:text-red-500 flex items-center justify-center shadow transition" aria-label="Save {{ $name }}">
            <i class="fa-regular fa-heart text-xs sm:text-sm"></i>
        </button>
        <a href="{{ $productUrl }}">
            <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-40 sm:h-60 object-cover rounded-xl mb-3 sm:mb-4 group-hover:scale-105 transition duration-300" loading="lazy" onerror="this.onerror=null; this.src='https://placehold.co/300x300/f8f9fa/1C2541?text=Beautage';">
        </a>
    </div>
    <div>
        <span class="text-[10px] sm:text-xs font-semibold text-gray-400 uppercase">{{ $category }}</span>
        <h3 class="font-bold text-gray-800 text-xs sm:text-sm mt-0.5 line-clamp-1"><a href="{{ $productUrl }}" class="hover:text-brand-accent">{{ $name }}</a></h3>
        <div class="flex items-center space-x-1 text-amber-400 text-[10px] sm:text-xs my-1">
            @for ($i = 1; $i <= 5; $i++)
                @if ($i <= $fullStars)
                <i class="fa-solid fa-star"></i>
                @elseif ($halfStar && $i === $fullStars + 1)
                <i class="fa-solid fa-star-half-stroke"></i>
                @else
                <i class="fa-regular fa-star"></i>
                @endif
            @endfor
            <span class="text-gray-500 text-[10px] sm:text-xs ml-1">({{ $reviewCount }})</span>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between mt-3 gap-2">
            <div>
                <span class="text-sm sm:text-lg font-extrabold text-brand-navy">{{ $money($price) }}</span>
                @if ($old > $price)
                <span class="text-[10px] sm:text-xs text-gray-400 line-through sm:ml-1.5 block sm:inline">{{ $money($old) }}</span>
                @endif
            </div>
            <button type="button" onclick="addToCart({{ (int) ($item['id'] ?? 0) }})" class="bg-brand-navy hover:bg-brand-accent text-white px-3 py-2 sm:px-4 sm:py-2.5 rounded-xl text-[10px] sm:text-xs font-bold transition shadow w-full sm:w-auto text-center">
                <i class="fa-solid fa-plus mr-1"></i> Add
            </button>
        </div>
    </div>
</div>
@endif
