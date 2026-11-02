@if (! empty($bundlePayload))
<section class="w-full px-4 sm:px-12 xl:px-20 py-10" id="frequently-bought-together">
    <div class="max-w-6xl mx-auto rounded-3xl bg-gradient-to-r from-purple-950 via-beautage-purple to-purple-900 text-white p-6 sm:p-10 shadow-2xl border border-purple-700 relative overflow-hidden">
        <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10">
            <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40 mb-2">
                {{ $bundlePayload['badgeLabel'] }}
            </span>
            <h2 class="text-xl sm:text-3xl font-serif font-bold text-white">Frequently Bought Together</h2>
            @if (! empty($bundlePayload['subtitle']))
            <p class="text-xs text-purple-50 mt-1">{{ $bundlePayload['subtitle'] }}</p>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-center mt-6 sm:mt-8">
                <div class="lg:col-span-8 flex flex-col sm:flex-row items-center gap-4 bg-purple-900/80 p-4 rounded-2xl border border-purple-700/80 backdrop-blur-md">
                    <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto justify-center overflow-x-auto py-1" id="bundle-image-row">
                        @foreach ($bundlePayload['items'] as $index => $item)
                            @if ($index > 0)
                            <span class="text-base font-bold text-pink-300">+</span>
                            @endif
                            <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" width="64" height="64" loading="lazy" decoding="async" class="w-14 h-14 sm:w-16 sm:h-16 rounded-xl object-cover border border-white/20 shrink-0" onerror="this.onerror=null; this.src='https://placehold.co/100x100/5E1F70/FFFFFF?text=Item';">
                        @endforeach
                    </div>

                    <div class="flex-1 text-center sm:text-left space-y-1.5 w-full" id="bundle-checkbox-list">
                        @foreach ($bundlePayload['items'] as $index => $item)
                        <div class="flex items-center gap-2 justify-start">
                            <input
                                type="checkbox"
                                id="bundle-check-{{ $index }}"
                                data-bundle-index="{{ $index }}"
                                @checked(true)
                                @disabled($item['required'])
                                @if (! $item['required']) onchange="updateBundleTotal()" @endif
                                class="accent-beautage-pink w-4 h-4 shrink-0 {{ $item['required'] ? '' : 'cursor-pointer' }}"
                            >
                            <label for="bundle-check-{{ $index }}" class="text-xs font-{{ $item['required'] ? 'bold text-white' : 'medium text-purple-50' }} cursor-pointer">
                                {{ $item['name'] }}
                                <span class="text-pink-100">({{ \App\Support\MoneyFormat::pkr($item['price']) }})</span>
                            </label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col items-center lg:items-end justify-center bg-purple-950/90 p-6 rounded-2xl border border-purple-700 text-center lg:text-right">
                    <span class="text-[10px] text-purple-100 uppercase font-extrabold">Combined Bundle Total</span>
                    <div class="flex items-baseline gap-2 mt-1 justify-center lg:justify-end flex-wrap">
                        <span id="bundle-total-price" class="text-3xl font-black text-white">Rs. 0</span>
                        <span id="bundle-compare-price" class="text-xs text-pink-300 line-through">Rs. 0</span>
                    </div>
                    <p id="bundle-savings-label" class="text-[11px] font-bold text-emerald-300 mt-1 hidden"></p>
                    <button type="button" id="bundle-add-btn" onclick="addBundleToCart()" class="mt-4 w-full px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30">
                        Add All to Bag
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
