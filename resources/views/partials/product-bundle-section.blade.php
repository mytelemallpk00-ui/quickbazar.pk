@if (! empty($bundlePayload))
<section class="w-full px-4 pt-8 sm:px-6 lg:px-8" id="frequently-bought-together">
    <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
        <div class="border-b border-slate-100 px-5 py-5 sm:px-8">
            <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#b45309]">{{ $bundlePayload['badgeLabel'] }}</p>
            <h2 class="mt-1 text-xl font-bold text-[#0b132b] sm:text-2xl">Frequently Bought Together</h2>
            @if (! empty($bundlePayload['subtitle']))
            <p class="mt-1 text-sm text-slate-500">{{ $bundlePayload['subtitle'] }}</p>
            @endif
        </div>

        <div class="grid grid-cols-1 items-stretch gap-5 p-5 sm:p-8 lg:grid-cols-12">
            <div class="flex flex-col items-center gap-4 rounded-2xl border border-slate-100 bg-[#f8f5ea] p-4 sm:flex-row lg:col-span-8">
                <div class="flex w-full items-center justify-center gap-2 overflow-x-auto py-1 sm:w-auto sm:gap-3" id="bundle-image-row">
                    @foreach ($bundlePayload['items'] as $index => $item)
                        @if ($index > 0)
                        <span class="text-base font-bold text-slate-400">+</span>
                        @endif
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" width="72" height="72" loading="lazy" decoding="async" class="h-16 w-16 shrink-0 rounded-xl border border-white object-cover shadow-sm sm:h-[4.5rem] sm:w-[4.5rem]" onerror="this.onerror=null; this.src='https://placehold.co/100x100/0b132b/d4af37?text=Item';">
                    @endforeach
                </div>

                <div class="w-full flex-1 space-y-2.5 text-left" id="bundle-checkbox-list">
                    @foreach ($bundlePayload['items'] as $index => $item)
                    <div class="flex items-center gap-2">
                        <input
                            type="checkbox"
                            id="bundle-check-{{ $index }}"
                            data-bundle-index="{{ $index }}"
                            @checked(true)
                            @disabled($item['required'])
                            @if (! $item['required']) onchange="updateBundleTotal()" @endif
                            class="h-4 w-4 shrink-0 accent-[#0b132b] {{ $item['required'] ? '' : 'cursor-pointer' }}"
                        >
                        <img src="{{ $item['image'] }}" alt="" width="64" height="64" loading="lazy" decoding="async" class="h-16 w-16 shrink-0 rounded-xl border border-white object-cover shadow-sm" onerror="this.onerror=null; this.src='https://placehold.co/100x100/0b132b/d4af37?text=Item';">
                        <label for="bundle-check-{{ $index }}" class="cursor-pointer text-sm leading-snug {{ $item['required'] ? 'font-bold text-[#0b132b]' : 'font-medium text-slate-600' }}">
                            {{ $item['name'] }}
                            <span class="font-semibold text-[#0b132b]">({{ \App\Support\MoneyFormat::pkr($item['price']) }})</span>
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="flex flex-col justify-center rounded-2xl bg-[#0b132b] p-6 text-center text-white lg:col-span-4 lg:items-end lg:text-right">
                <span class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#d4af37]">Bundle total</span>
                <div class="mt-1 flex flex-wrap items-baseline justify-center gap-2 lg:justify-end">
                    <span id="bundle-total-price" class="text-3xl font-extrabold">Rs. 0</span>
                    <span id="bundle-compare-price" class="text-xs text-slate-400 line-through">Rs. 0</span>
                </div>
                <p id="bundle-savings-label" class="mt-1 hidden text-xs font-bold text-[#d4af37]"></p>
                <button type="button" id="bundle-add-btn" onclick="addBundleToCart()" class="mt-4 w-full rounded-xl bg-[#d4af37] px-6 py-3 text-sm font-bold text-[#0b132b] transition hover:bg-[#c4a033]">
                    Add all to bag
                </button>
            </div>
        </div>
    </div>
</section>
@endif
