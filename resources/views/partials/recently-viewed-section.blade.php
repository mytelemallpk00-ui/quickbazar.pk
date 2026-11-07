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
    class="hidden w-full px-4 py-8 sm:px-6 lg:px-8"
    aria-label="Recently viewed products"
>
    <div class="mb-5 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <p class="inline-flex items-center gap-1.5 text-[11px] font-bold uppercase tracking-wide text-emerald-700">
                <i data-lucide="history" class="h-3.5 w-3.5"></i> Recently viewed
            </p>
            <h2 class="mt-1 text-xl font-bold text-slate-900 sm:text-2xl">Pick up where you left off</h2>
            <p class="mt-1 max-w-xl text-sm text-slate-500">Products you browsed recently.</p>
        </div>
    </div>
    <div data-recently-viewed-grid class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3 sm:gap-5"></div>
</section>
