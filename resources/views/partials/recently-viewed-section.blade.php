@php
    $recentlyVariant = $recentlyVariant ?? 'default';
    $excludeProductId = $excludeProductId ?? null;
    $recentlyLimit = (int) ($recentlyLimit ?? 8);
@endphp
<section
    id="recently-viewed-section-{{ $recentlyVariant }}"
    data-recently-viewed-section
    data-exclude-id="{{ $excludeProductId ?? '' }}"
    data-limit="{{ $recentlyLimit }}"
    class="hidden w-full px-4 sm:px-12 xl:px-20 py-10 sm:py-12 min-h-[420px]"
    aria-label="Recently viewed products"
>
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3 mb-6 sm:mb-8">
        <div>
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-50 text-beautage-pink border border-pink-200">
                <i data-lucide="history" class="w-3.5 h-3.5"></i> Recently viewed
            </span>
            <h2 class="text-xl sm:text-3xl font-serif font-bold text-beautage-purple mt-2">Pick Up Where You Left Off</h2>
            <p class="text-xs text-slate-500 mt-1 max-w-xl">Products you browsed recently — quick add for impulse-friendly checkout.</p>
        </div>
    </div>
    <div data-recently-viewed-grid class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-5"></div>
</section>
