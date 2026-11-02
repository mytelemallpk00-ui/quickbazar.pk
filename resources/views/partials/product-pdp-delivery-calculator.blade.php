@php
    use App\Support\CheckoutSettings;
    use App\Support\DeliveryEstimate;
    use App\Support\MoneyFormat;

    $checkoutConfig = $checkoutConfig ?? CheckoutSettings::forStorefront();
    $shippingMethods = $checkoutConfig['shipping_methods'] ?? [];
    $paymentMethods = $checkoutConfig['payment_methods'] ?? [];
    $defaultShip = $shippingMethods[0] ?? ['id' => 'standard', 'label' => 'Standard Delivery', 'description' => '2-3 Business Days'];
    $initialTimeline = DeliveryEstimate::timelineForMethod($defaultShip);
    $courierLabel = DeliveryEstimate::courierLabel($shippingMethods);
    $paymentSummary = DeliveryEstimate::paymentSummary($paymentMethods);
    $showCodBadge = DeliveryEstimate::codEnabled($paymentMethods);
    $freeThreshold = (float) ($checkoutConfig['free_shipping_threshold'] ?? 5000);
    $lineSubtotal = (float) $product->price;
    $qualifiesFreeShip = $lineSubtotal >= $freeThreshold;
@endphp

<div id="pdp-delivery-calculator" class="pt-2 border-t border-purple-100 space-y-4">
    <div class="p-5 rounded-2xl bg-white border border-purple-100 shadow-sm space-y-4">
        <div class="flex items-center gap-2">
            <i data-lucide="truck" class="w-5 h-5 text-amber-500 shrink-0"></i>
            <h3 class="text-sm font-bold text-slate-900">Estimated Delivery Timeline</h3>
        </div>

        <div id="pdp-delivery-timeline" class="relative pt-2 pb-1">
            <div class="absolute left-[12%] right-[12%] top-[1.35rem] h-0.5 bg-slate-200 z-0"></div>
            <div id="pdp-timeline-progress" class="absolute left-[12%] top-[1.35rem] h-0.5 bg-emerald-500 z-0 transition-all duration-300" style="width: 38%;"></div>
            <div class="grid grid-cols-4 gap-1 relative z-[1] text-center">
                @foreach ([
                    ['key' => 'ordered', 'title' => 'Ordered', 'icon' => 'check', 'state' => 'done'],
                    ['key' => 'processed', 'title' => 'Processed', 'icon' => 'check', 'state' => 'done'],
                    ['key' => 'shipped', 'title' => 'Shipped', 'icon' => 'truck', 'state' => 'active'],
                    ['key' => 'delivered', 'title' => 'Delivered', 'icon' => 'home', 'state' => 'pending'],
                ] as $step)
                <div class="flex flex-col items-center gap-1.5 min-w-0 px-0.5">
                    <span class="text-[10px] font-semibold text-slate-500 leading-none pdp-timeline-date" data-step="{{ $step['key'] }}">
                        @if ($step['key'] === 'delivered')
                            {{ $initialTimeline['delivered']['label'] }}
                        @elseif ($step['key'] === 'ordered')
                            {{ $initialTimeline['ordered']['label'] }}
                        @else
                            {{ $initialTimeline[$step['key']]['label'] ?? '' }}
                        @endif
                    </span>
                    <div class="pdp-timeline-node w-9 h-9 rounded-full flex items-center justify-center border-2 transition-colors
                        @if($step['state'] === 'done') bg-emerald-500 border-emerald-500 text-white
                        @elseif($step['state'] === 'active') bg-white border-blue-500 text-amber-500 shadow-md
                        @else bg-slate-100 border-slate-200 text-slate-400 @endif" data-step="{{ $step['key'] }}">
                        <i data-lucide="{{ $step['icon'] }}" class="w-4 h-4"></i>
                    </div>
                    <span class="text-[9px] sm:text-[10px] font-extrabold uppercase tracking-wide text-slate-800">{{ $step['title'] }}</span>
                </div>
                @endforeach
            </div>
        </div>

        <div class="space-y-2 pt-1 border-t border-purple-50">
            <label class="block text-[11px] font-extrabold text-beautage-purple uppercase tracking-wider">Delivery option (same as checkout)</label>
            <div class="space-y-2" id="pdp-shipping-method-list">
                @forelse($shippingMethods as $idx => $ship)
                <label class="flex items-center justify-between gap-2 p-3 rounded-xl border-2 cursor-pointer transition {{ $idx === 0 ? 'border-beautage-purple bg-purple-50/60' : 'border-purple-100 bg-white hover:border-purple-200' }}">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <input type="radio" name="pdp-shipping-method" value="{{ $ship['id'] }}" data-description="{{ $ship['description'] ?? '' }}" {{ $idx === 0 ? 'checked' : '' }} class="accent-beautage-pink shrink-0" onchange="pdpRecalcDeliveryTimeline()">
                        <div class="min-w-0 text-left">
                            <span class="text-[11px] font-bold text-beautage-purple block truncate">{{ $ship['label'] }}</span>
                            @if(! empty($ship['description']))
                            <span class="text-[10px] text-slate-500">{{ $ship['description'] }}</span>
                            @endif
                        </div>
                    </div>
                    @php
                        $shipPrice = \App\Support\CheckoutSettings::shippingPriceFor($ship['id'], $lineSubtotal);
                    @endphp
                    <span class="text-[10px] font-black shrink-0 {{ $shipPrice <= 0 ? 'text-emerald-600' : 'text-slate-700' }}">
                        {{ $shipPrice <= 0 ? 'FREE' : MoneyFormat::pkr($shipPrice) }}
                    </span>
                </label>
                @empty
                <p class="text-xs text-slate-500">Delivery options will appear when configured in Checkout settings.</p>
                @endforelse
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
            <div>
                <label class="block text-[10px] font-extrabold text-slate-600 uppercase mb-1">City / region</label>
                <select id="pdp-delivery-region" onchange="pdpRecalcDeliveryTimeline()" class="w-full py-2.5 px-3 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium">
                    <option value="major-metro">Lahore / Karachi / Islamabad</option>
                    <option value="faisalabad-multan">Faisalabad / Multan / Sialkot</option>
                    <option value="quetta-peshawar">Quetta / Peshawar / Abbottabad</option>
                    <option value="remote">Other cities &amp; towns</option>
                </select>
            </div>
            <div>
                <label class="block text-[10px] font-extrabold text-slate-600 uppercase mb-1">Postal code (optional)</label>
                <input type="text" id="pdp-postal-input" placeholder="e.g. 54000" maxlength="12" class="w-full py-2.5 px-3 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple" oninput="pdpRecalcDeliveryTimeline()">
            </div>
        </div>

        <div class="rounded-xl bg-slate-50 border border-slate-100 px-3 py-2.5 space-y-1.5 text-[11px]">
            <div class="flex flex-wrap items-center gap-2">
                <span class="font-bold text-slate-800">Courier:</span>
                <span id="pdp-courier-label" class="text-slate-600">{{ $courierLabel }}</span>
                @if ($showCodBadge)
                <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-100 text-emerald-800 text-[10px] font-extrabold uppercase">COD</span>
                @endif
            </div>
            <div>
                <span class="font-bold text-slate-800">Payment:</span>
                <span id="pdp-payment-summary" class="text-slate-600">{{ $paymentSummary }}</span>
            </div>
            <p id="pdp-free-shipping-note" class="text-[10px] {{ $qualifiesFreeShip ? 'text-emerald-700 font-semibold' : 'text-slate-500' }}">
                @if ($qualifiesFreeShip)
                    This item qualifies for free express shipping (orders over {{ MoneyFormat::pkr($freeThreshold) }}).
                @else
                    Add {{ MoneyFormat::pkr(max(0, $freeThreshold - $lineSubtotal)) }} more for free shipping on eligible methods.
                @endif
            </p>
        </div>
    </div>

    <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-bold text-slate-600">
        <div class="p-2 rounded-xl bg-purple-50/50 border border-purple-100 flex flex-col items-center gap-1">
            <i data-lucide="shield-check" class="w-4 h-4 text-beautage-pink"></i>
            <span>100% Original</span>
        </div>
        <div class="p-2 rounded-xl bg-purple-50/50 border border-purple-100 flex flex-col items-center gap-1">
            <i data-lucide="rotate-ccw" class="w-4 h-4 text-emerald-600"></i>
            <span>30-Day Returns</span>
        </div>
        <div class="p-2 rounded-xl bg-purple-50/50 border border-purple-100 flex flex-col items-center gap-1">
            <i data-lucide="headphones" class="w-4 h-4 text-amber-600"></i>
            <span>24/7 Support</span>
        </div>
    </div>
</div>
