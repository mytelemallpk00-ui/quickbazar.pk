@if (! empty($bundlePayload))
@php
    use App\Support\MoneyFormat;
    $bundleDiscount = (float) ($bundlePayload['discountValue'] ?? 0);
    $bundleIsPercent = ($bundlePayload['discountType'] ?? 'percent') !== 'fixed';
@endphp
<section class="w-full max-w-[1600px] mx-auto px-4 sm:px-8 mt-12" id="frequently-bought-together">
    <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-sm border border-gray-100">
        <h3 class="text-xl font-extrabold text-[#0B132B] mb-6 flex items-center gap-2">
            <i data-lucide="package" class="w-5 h-5 text-[#D4AF37]"></i> Frequently Bought Together
        </h3>
        @if (! empty($bundlePayload['subtitle']))
        <p class="text-sm text-gray-500 -mt-4 mb-6">{{ $bundlePayload['subtitle'] }}</p>
        @endif
        <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
            <div class="flex flex-col sm:flex-row items-center gap-4 flex-1 w-full">
                @foreach ($bundlePayload['items'] as $index => $item)
                    @if ($index > 0)
                    <span class="text-2xl font-bold text-gray-400" aria-hidden="true">+</span>
                    @endif
                    <div class="flex items-center space-x-3 bg-gray-50 p-3 rounded-2xl border border-gray-100 flex-1 w-full">
                        <input
                            type="checkbox"
                            id="bundle-check-{{ $index }}"
                            data-bundle-index="{{ $index }}"
                            @checked(true)
                            @disabled($item['required'])
                            @if (! $item['required']) onchange="updateBundleTotal()" @endif
                            class="accent-[#0B132B] w-4 h-4 shrink-0 {{ $item['required'] ? '' : 'cursor-pointer' }}"
                        >
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}" width="64" height="64" loading="lazy" decoding="async" class="w-16 h-16 rounded-xl object-cover">
                        <div class="min-w-0">
                            <span class="text-[10px] {{ $index === 0 ? 'text-gray-400' : 'text-[#4361EE]' }} font-bold uppercase block">{{ $index === 0 ? 'This Item' : 'Add-on '.$index }}</span>
                            <h4 class="font-bold text-xs text-[#0B132B] line-clamp-2">{{ $item['name'] }}</h4>
                            <span class="text-xs font-extrabold text-[#0B132B]">{{ MoneyFormat::pkr($item['price']) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="bg-gray-50 p-6 rounded-2xl border border-gray-200 text-center lg:w-72 flex-shrink-0 w-full space-y-3">
                <div>
                    <span class="text-xs text-gray-500 block">Total Bundle Price:</span>
                    <span id="bundle-total-price" class="text-2xl font-black text-[#0B132B]">{{ MoneyFormat::pkr(0) }}</span>
                    <span id="bundle-compare-price" class="text-xs text-gray-400 line-through block">{{ MoneyFormat::pkr(0) }}</span>
                    <span id="bundle-savings-label" class="text-[10px] text-emerald-600 font-bold block">
                        @if ($bundleDiscount > 0)
                            {{ $bundleIsPercent ? 'Save an extra '.(int) $bundleDiscount.'% on this bundle' : 'Bundle savings applied at checkout' }}
                        @endif
                    </span>
                </div>
                <button type="button" id="bundle-add-btn" onclick="addBundleToCart()" class="w-full bg-[#D4AF37] hover:bg-yellow-500 text-[#0B132B] font-bold py-3 rounded-xl text-xs transition shadow">
                    Add All {{ count($bundlePayload['items']) }} Items to Bag
                </button>
            </div>
        </div>
    </div>
</section>
@endif
