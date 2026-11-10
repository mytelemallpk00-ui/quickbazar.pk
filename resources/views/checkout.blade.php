@extends('layouts.app')

@section('title', 'Secure Checkout — Quick Bazar Flagship Store')

@section('body_attrs')
class="qh-checkout bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-emerald-100 selection:text-slate-900 pb-36 md:pb-0 overflow-x-hidden"
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/checkout.css') }}?v=2026111040">
@endpush

@section('content')
<main class="w-full flex-1 flex flex-col py-6 sm:py-10">

        <!-- BREADCRUMB -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-6 overflow-x-auto no-scrollbar">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <a href="{{ route('collections') }}" class="hover:text-beautage-purple transition">Catalog</a>
                <i data-lucide="chevron-right" class="w-3 h-3"></i>
                <span class="font-bold">Secure Checkout</span>
            </nav>
        </div>

        <div class="w-full px-4 sm:px-12 xl:px-20">
            <div class="w-full">
                @php
                    $shippingMethods = $checkoutConfig['shipping_methods'] ?? [];
                    $paymentMethods = $checkoutConfig['payment_methods'] ?? [];
                @endphp
                <div class="co-hero mb-8">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-50 text-beautage-pink border border-pink-200 mb-2">
                        {{ $checkoutConfig['page_badge'] ?? 'Secure checkout' }}
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-serif font-bold text-beautage-purple tracking-tight">{{ $checkoutConfig['page_title'] ?? 'Checkout' }}</h1>
                    <p class="text-xs sm:text-sm text-slate-600 mt-1 font-medium">{{ $checkoutConfig['page_subtitle'] ?? '' }}</p>
                </div>

                <!-- 2-COLUMN CHECKOUT LAYOUT -->
                <form id="checkout-form" onsubmit="handleCompleteOrder(event)" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- LEFT COLUMN: FORMS & OPTIONS (SPAN 7) -->
                    <div class="lg:col-span-7 space-y-6">
                        
                        @if($checkoutConfig['show_express_checkout'] ?? false)
                        <!-- EXPRESS CHECKOUT BUTTONS -->
                        <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                            <span class="text-xs font-extrabold uppercase tracking-wider text-slate-500 block">Express Instant Checkout</span>
                            <div class="grid grid-cols-3 gap-3">
                                <button type="button" onclick="expressCheckoutAction('Shop Pay')" class="py-3 px-3 rounded-xl bg-purple-900 hover:bg-purple-950 text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                    <i data-lucide="zap" class="w-4 h-4 text-pink-300"></i> Shop Pay
                                </button>
                                <button type="button" onclick="expressCheckoutAction('PayPal')" class="py-3 px-3 rounded-xl bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                    <i data-lucide="shield-check" class="w-4 h-4"></i> PayPal
                                </button>
                                <button type="button" onclick="expressCheckoutAction('Apple Pay')" class="py-3 px-3 rounded-xl bg-slate-900 hover:bg-black text-white font-bold text-xs flex items-center justify-center gap-2 transition shadow-sm">
                                    <i data-lucide="smartphone" class="w-4 h-4"></i> Apple Pay
                                </button>
                            </div>
                            <div class="relative flex py-1 items-center">
                                <div class="flex-grow border-t border-purple-100"></div>
                                <span class="flex-shrink mx-4 text-slate-400 text-[11px] font-semibold uppercase tracking-wider">Or manual secure entry</span>
                                <div class="flex-grow border-t border-purple-100"></div>
                            </div>
                        </div>
                        @endif

                        <!-- 1. CONTACT & SHIPPING ADDRESS -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xl space-y-6">
                            <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                                <h3 class="text-base font-serif font-bold text-beautage-purple flex items-center gap-2">
                                    <i data-lucide="user" class="w-5 h-5 text-beautage-pink"></i> 1. Contact & Shipping Address
                                </h3>
                                <span class="text-xs text-slate-400 font-semibold">
                                    @auth
                                        <a href="{{ route('account.index') }}" class="text-beautage-pink font-bold hover:underline">My orders</a>
                                    @else
                                        <a href="{{ route('login') }}" class="text-beautage-pink font-bold hover:underline">Sign in</a>
                                        · <a href="{{ route('register') }}" class="text-beautage-purple font-bold hover:underline">Register</a>
                                    @endauth
                                </span>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Email Address (for order receipt & tracking)</label>
                                    <input type="email" id="cust-email" required placeholder="you@email.com" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">First Name</label>
                                        <input type="text" id="cust-firstname" required placeholder="Saboor" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Last Name</label>
                                        <input type="text" id="cust-lastname" required placeholder="Aly" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Street Address</label>
                                    <input type="text" id="cust-address" required placeholder="House # 12, Main Boulevard, DHA Phase 5" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">City / Region</label>
                                        <input type="text" id="cust-city" required placeholder="Lahore / Karachi" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Postal Code</label>
                                        <input type="text" id="cust-postal" required placeholder="54000" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Phone Number</label>
                                        <input type="tel" id="cust-phone" required placeholder="+92 300 9876543" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. SHIPPING METHOD -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xl space-y-4">
                            <h3 class="text-base font-serif font-bold text-beautage-purple flex items-center gap-2 border-b border-purple-100 pb-4">
                                <i data-lucide="truck" class="w-5 h-5 text-emerald-600"></i> 2. Delivery Method
                            </h3>

                            <p id="shipping-rates-hint" class="text-[11px] text-slate-500">Enter your city to load TCS, Leopards, BlueEx, and M&amp;P rates.</p>
                            <div id="shipping-methods-container" class="space-y-3">
                                @forelse($shippingMethods as $idx => $ship)
                                <label class="flex items-center justify-between p-4 rounded-2xl border-2 {{ $idx === 0 ? 'border-beautage-purple bg-purple-50/70' : 'border-purple-200 bg-white hover:border-purple-300' }} cursor-pointer transition">
                                    <div class="flex items-center gap-3">
                                        <input type="radio" name="shipping-method" value="{{ $ship['id'] }}" {{ $idx === 0 ? 'checked' : '' }} class="accent-beautage-pink w-4 h-4" onchange="onShippingMethodChange('{{ $ship['id'] }}')">
                                        <div>
                                            <span class="text-xs font-extrabold {{ $idx === 0 ? 'text-beautage-purple' : 'text-slate-800' }} block">{{ $ship['label'] }}</span>
                                            @if(! empty($ship['description']))
                                            <span class="text-[11px] text-slate-500">{{ $ship['description'] }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if((float) ($ship['price'] ?? 0) === 0.0)
                                    <span class="text-xs font-black text-emerald-600 uppercase shipping-price-label" data-ship-id="{{ $ship['id'] }}">FREE</span>
                                    @else
                                    <span class="text-xs font-black text-slate-800 shipping-price-label" data-ship-id="{{ $ship['id'] }}">Rs. {{ number_format((float) $ship['price'], 0) }}</span>
                                    @endif
                                </label>
                                @empty
                                <p class="text-xs text-slate-500">No delivery options configured. Contact support.</p>
                                @endforelse
                            </div>
                        </div>

                        <!-- 3. PAYMENT METHOD (COD, MOBILE WALLET, CARD, BANK TRANSFER) -->
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-xl space-y-6">
                            <h3 class="text-base font-serif font-bold text-beautage-purple flex items-center gap-2 border-b border-purple-100 pb-4">
                                <i data-lucide="credit-card" class="w-5 h-5 text-beautage-pink"></i> 3. Payment Method
                            </h3>

                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 sm:gap-3">
                                @foreach($paymentMethods as $idx => $pay)
                                <button type="button" onclick="switchPaymentTab('{{ $pay['id'] }}')" id="pay-tab-{{ $pay['id'] }}" data-pay-tab="{{ $pay['id'] }}" class="py-3 px-2 rounded-2xl border-2 {{ $idx === 0 ? 'border-beautage-purple bg-purple-50 text-beautage-purple' : 'border-purple-200 bg-white text-slate-700 hover:border-purple-300' }} font-bold text-[11px] sm:text-xs transition flex flex-col items-center justify-center gap-1 shadow-xs">
                                    <i data-lucide="{{ $pay['icon'] ?? 'credit-card' }}" class="w-4 h-4 shrink-0"></i>
                                    <span>{{ $pay['short_label'] ?? $pay['label'] }}</span>
                                </button>
                                @endforeach
                            </div>

                            @foreach($paymentMethods as $idx => $pay)
                            @if($pay['id'] === 'cod')
                            <div id="payment-panel-cod" class="p-5 rounded-2xl bg-purple-50/50 border border-purple-200 space-y-2 {{ $idx === 0 ? '' : 'hidden' }}">
                                <div class="flex items-center gap-2 text-xs font-bold text-beautage-purple">
                                    <i data-lucide="check-circle-2" class="w-4 h-4 text-emerald-600"></i>
                                    <span>{{ $pay['label'] }}</span>
                                </div>
                                @if(! empty($pay['instructions']))
                                <p class="text-[11px] text-slate-600 leading-relaxed">{{ $pay['instructions'] }}</p>
                                @endif
                            </div>
                            @elseif($pay['id'] !== 'wallet' && $pay['id'] !== 'card')
                            <div id="payment-panel-{{ $pay['id'] }}" class="p-5 rounded-2xl bg-amber-50/60 border border-amber-200 space-y-2.5 {{ $idx === 0 ? '' : 'hidden' }}">
                                <div class="flex items-center gap-2 text-xs font-bold text-amber-900">
                                    <i data-lucide="{{ $pay['icon'] ?? 'landmark' }}" class="w-4 h-4 text-amber-600"></i>
                                    <span>{{ $pay['label'] }}</span>
                                </div>
                                @if(! empty($pay['instructions']))
                                <p class="text-[11px] text-slate-700 leading-relaxed">{{ $pay['instructions'] }}</p>
                                @endif
                            </div>
                            @endif
                            @endforeach

                            <!-- MOBILE WALLET PANEL -->
                            @if(collect($paymentMethods)->contains(fn ($m) => ($m['id'] ?? '') === 'wallet'))
                            <div id="payment-panel-wallet" class="p-5 rounded-2xl bg-purple-50/70 border border-purple-200 space-y-4 {{ ($paymentMethods[0]['id'] ?? '') === 'wallet' ? '' : 'hidden' }}">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-extrabold text-beautage-purple uppercase tracking-wider">Select Mobile Wallet</span>
                                    <span class="text-[10px] text-pink-600 font-bold bg-pink-50 px-2 py-0.5 rounded-full border border-pink-200">Instant Verification</span>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-beautage-purple bg-white cursor-pointer transition shadow-xs" id="wallet-easypaisa-label" onclick="selectWalletProvider('Easypaisa')">
                                        <input type="radio" name="wallet-provider" checked class="accent-beautage-pink w-4 h-4">
                                        <div>
                                            <span class="text-xs font-bold text-emerald-700 block">Easypaisa</span>
                                            <span class="text-[10px] text-slate-500">Telenor Microfinance</span>
                                        </div>
                                    </label>
                                    <label class="flex items-center gap-3 p-3 rounded-xl border-2 border-purple-200 bg-white cursor-pointer transition shadow-xs" id="wallet-jazzcash-label" onclick="selectWalletProvider('JazzCash')">
                                        <input type="radio" name="wallet-provider" class="accent-beautage-pink w-4 h-4">
                                        <div>
                                            <span class="text-xs font-bold text-rose-600 block">JazzCash</span>
                                            <span class="text-[10px] text-slate-500">Mobilink Microfinance</span>
                                        </div>
                                    </label>
                                </div>
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1"><span id="wallet-name-label">Easypaisa</span> Account / Mobile Number</label>
                                    <div class="relative flex items-center">
                                        <input type="tel" id="wallet-number" placeholder="0300 1234567" class="w-full py-3 pl-4 pr-12 bg-white border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-mono">
                                        <span class="absolute right-3 text-beautage-pink"><i data-lucide="smartphone" class="w-4 h-4"></i></span>
                                    </div>
                                    <p class="text-[10px] text-slate-500 mt-1">An automated USSD push notification or OTP prompt will be sent to your mobile number upon order confirmation.</p>
                                </div>
                            </div>
                            @endif

                            <!-- CARD PANEL -->
                            @if(collect($paymentMethods)->contains(fn ($m) => ($m['id'] ?? '') === 'card'))
                            <div id="payment-panel-card" class="space-y-4 {{ ($paymentMethods[0]['id'] ?? '') === 'card' ? '' : 'hidden' }}">
                                <div>
                                    <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Card Number</label>
                                    <div class="relative flex items-center">
                                        <input type="text" id="card-number" placeholder="4532 •••• •••• 8892" class="w-full py-3 pl-4 pr-10 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-mono">
                                        <span class="absolute right-3 text-slate-400"><i data-lucide="credit-card" class="w-4 h-4"></i></span>
                                    </div>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Expiration Date</label>
                                        <input type="text" id="card-expiry" placeholder="MM / YY" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-extrabold text-slate-700 uppercase mb-1">Security Code (CVV)</label>
                                        <input type="text" id="card-cvv" placeholder="CVC" class="w-full py-3 px-4 bg-purple-50/50 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-mono">
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                    </div>

                    <!-- RIGHT COLUMN: ORDER SUMMARY & TOTALS (SPAN 5) -->
                    <div class="lg:col-span-5 space-y-6 lg:sticky lg:top-28">
                        
                        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-purple-100 shadow-2xl space-y-6">
                            <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                                <h3 class="text-base font-serif font-bold text-beautage-purple flex items-center gap-2">
                                    <i data-lucide="shopping-bag" class="w-5 h-5 text-beautage-pink"></i> Order Summary
                                </h3>
                                <a href="{{ route('collections') }}" class="text-xs font-bold text-beautage-pink hover:underline">Continue shopping</a>
                            </div>

                            <!-- Cart Items List container -->
                            <div id="checkout-cart-items" class="space-y-4 max-h-[320px] overflow-y-auto pr-1">
                                <!-- Populated via JS -->
                            </div>

                            <!-- Promo Code Input -->
                            <div class="pt-3 border-t border-purple-100 space-y-2">
                                <label class="block text-xs font-extrabold text-slate-700 uppercase">Promo / Discount Code:</label>
                                <div class="flex items-center gap-2">
                                    <input type="text" id="promo-input" placeholder="e.g. WELCOME" class="flex-1 py-2.5 px-3 bg-purple-50/50 border border-purple-200 rounded-xl text-xs uppercase placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-bold">
                                    <button type="button" onclick="applyPromoCode()" class="px-4 py-2.5 bg-beautage-purple hover:bg-beautage-purpleDark text-white text-xs font-bold rounded-xl transition">Apply</button>
                                </div>
                                <div id="promo-message" class="text-xs font-semibold hidden"></div>
                            </div>

                            <!-- Calculation Totals -->
                            <div class="pt-4 border-t border-purple-100 space-y-3">
                                <div class="flex justify-between text-xs font-medium text-slate-600">
                                    <span>Bag Subtotal</span>
                                    <span id="summary-subtotal" class="font-bold text-beautage-purple">Rs. 0</span>
                                </div>
                                <div id="discount-row" class="flex justify-between text-xs font-medium text-emerald-600 hidden">
                                    <span>Discount (<span id="discount-code-name"></span>)</span>
                                    <span id="summary-discount" class="font-bold">- Rs. 0</span>
                                </div>
                                <div class="flex justify-between text-xs font-medium text-slate-600">
                                    <span>Estimated Shipping</span>
                                    <span id="summary-shipping" class="font-bold text-emerald-600">FREE</span>
                                </div>
                                <div class="flex justify-between text-xs font-medium text-slate-600">
                                    <span>Estimated Tax ({{ $checkoutConfig['tax_percent_label'] ?? 8 }}%)</span>
                                    <span id="summary-tax" class="font-bold text-beautage-purple">Rs. 0</span>
                                </div>
                                <div class="flex justify-between text-base font-black text-beautage-purple pt-3 border-t border-purple-200">
                                    <span>Grand Total</span>
                                    <span id="summary-grandtotal" class="text-beautage-pink text-xl">Rs. 0</span>
                                </div>
                            </div>

                            <!-- COMPLETE ORDER SUBMIT BUTTON -->
                            <button type="submit" class="w-full bg-beautage-pink hover:bg-beautage-pinkHover text-white font-black py-4 px-6 rounded-2xl transition shadow-xl shadow-pink-500/30 flex items-center justify-center gap-3 text-xs uppercase tracking-wider">
                                <i data-lucide="shield-check" class="w-5 h-5"></i> Complete Secure Order
                            </button>

                            <div class="flex items-center justify-center gap-2 text-[11px] text-slate-500 pt-1">
                                <i data-lucide="lock" class="w-3.5 h-3.5 text-beautage-purple"></i>
                                <span>Encrypted checkout. Your details stay on this order.</span>
                            </div>
                        </div>

                    </div>

                </form>
            </div>
        </div>

        @php
            $alsoProducts = \App\Models\Product::query()
                ->with('productCategory')
                ->orderBy('sort_order')
                ->orderBy('id')
                ->limit(4)
                ->get();
            $alsoPlaceholder = 'https://placehold.co/640x640/0e6b5c/ffffff?text=Quick+Bazar';
        @endphp
        @if ($alsoProducts->isNotEmpty())
        <section class="co-also" aria-labelledby="checkout-also-title">
            <div class="co-also-head">
                <div>
                    <p class="co-kicker">From the catalog</p>
                    <h2 id="checkout-also-title">You may also like</h2>
                </div>
                <a href="{{ route('collections') }}">Shop all →</a>
            </div>
            <div class="co-also-grid">
                @foreach ($alsoProducts as $also)
                    @php
                        $alsoName = (string) $also->name;
                        $alsoUrl = route('product.show', ['product' => $also->slug]);
                        $alsoImage = $also->image ?: $alsoPlaceholder;
                        $alsoCategory = trim((string) ($also->productCategory->name ?? $also->category ?? ''));
                        $alsoShort = $also->cardExcerpt();
                        $alsoPrice = (float) $also->price;
                        $alsoOld = (float) ($also->old_price ?? 0);
                        $alsoSave = ($alsoOld > $alsoPrice && $alsoPrice > 0) ? (int) round((1 - ($alsoPrice / $alsoOld)) * 100) : 0;
                        $alsoStars = max(0, min(5, (int) round((float) ($also->rating ?? 0))));
                        $alsoReviews = (int) ($also->reviews ?? 0);
                        $alsoStock = $also->in_stock ? '1' : '0';
                    @endphp
                    <article class="lv2-product">
                        <a href="{{ $alsoUrl }}" class="lv2-product-media">
                            <img class="lv2-card-photo" src="{{ $alsoImage }}" alt="{{ $alsoName }}" width="640" height="640" loading="lazy" decoding="async" onerror="this.onerror=null;this.src='{{ $alsoPlaceholder }}';">
                            @if ($alsoSave > 0)
                                <span class="lv2-save">SAVE {{ $alsoSave }}%</span>
                            @endif
                        </a>
                        <div class="lv2-product-copy">
                            @if ($alsoCategory !== '')
                                <p class="lv2-muted">{{ $alsoCategory }}</p>
                            @endif
                            <h3><a href="{{ $alsoUrl }}">{{ $alsoName }}</a></h3>
                            @if ($alsoShort !== '')
                                <p class="lv2-card-desc">{{ $alsoShort }}</p>
                            @endif
                            @if ($alsoReviews > 0)
                                <p class="lv2-stars" aria-label="Rated {{ number_format((float) $also->rating, 1) }} out of 5">{{ str_repeat('★', $alsoStars) }}{{ str_repeat('☆', 5 - $alsoStars) }} <span class="lv2-muted">({{ number_format($alsoReviews) }})</span></p>
                            @endif
                            <p class="lv2-price-row">
                                <span class="lv2-price">{{ \App\Support\MoneyFormat::pkr($alsoPrice) }}</span>
                                @if ($alsoOld > $alsoPrice)
                                    <span class="lv2-old">{{ \App\Support\MoneyFormat::pkr($alsoOld) }}</span>
                                @endif
                            </p>
                        </div>
                        <div class="lv2-card-actions">
                            <button type="button" class="lv2-card-quick" data-quick-view data-id="{{ $also->id }}" data-name="{{ $alsoName }}" data-price="{{ $alsoPrice }}" data-old="{{ $alsoOld }}" data-image="{{ $alsoImage }}" data-category="{{ $alsoCategory }}" data-desc="{{ $alsoShort }}" data-url="{{ $alsoUrl }}" data-stock="{{ $alsoStock }}">Quick view</button>
                            @if ($alsoStock === '1')
                                <button type="button" class="lv2-btn-navy" data-card-add data-id="{{ $also->id }}" data-name="{{ $alsoName }}" data-price="{{ $alsoPrice }}" data-old="{{ $alsoOld }}" data-image="{{ $alsoImage }}" data-category="{{ $alsoCategory }}" data-desc="{{ $alsoShort }}" data-url="{{ $alsoUrl }}" data-stock="1">Add to cart</button>
                            @else
                                <button type="button" class="lv2-btn-navy" disabled>Out of stock</button>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
        @endif

    </main>
@endsection

@section('below_main')
<!-- ORDER SUCCESS CONFIRMATION MODAL -->
    <div id="success-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-beautage-purpleDark/70 backdrop-blur-md hidden">
        <div class="bg-white border border-purple-100 rounded-3xl max-w-xl w-full p-6 sm:p-10 relative shadow-2xl text-center space-y-6">
            <div class="w-20 h-20 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto shadow-inner">
                <i data-lucide="check-circle-2" class="w-10 h-10"></i>
            </div>
            <div>
                <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-200 mb-2">
                    ORDER PLACED SUCCESSFULLY
                </span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Thank You for Your Order!</h2>
                <p class="text-xs sm:text-sm text-slate-600 mt-2">Your luxury order <strong id="success-order-id" class="text-beautage-pink font-mono">#BTG-98421</strong> has been successfully confirmed and queued for priority dispatch.</p>
            </div>

            <div class="p-4 rounded-2xl bg-purple-50/70 border border-purple-200 text-left space-y-2 text-xs">
                <div class="flex justify-between font-bold text-beautage-purple">
                    <span>Recipient:</span>
                    <span id="receipt-name" class="font-normal text-slate-700">-</span>
                </div>
                <div class="flex justify-between font-bold text-beautage-purple">
                    <span>Destination:</span>
                    <span id="receipt-address" class="font-normal text-slate-700">-</span>
                </div>
                <div class="flex justify-between font-bold text-beautage-purple">
                    <span>Payment Method:</span>
                    <span id="receipt-payment" class="font-normal text-slate-700">-</span>
                </div>
                <div class="flex justify-between font-bold text-beautage-purple border-t border-purple-200 pt-2">
                    <span>Total Paid:</span>
                    <span id="receipt-total" class="font-black text-beautage-pink">-</span>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button onclick="window.location.href='/'" class="flex-1 py-3.5 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg">
                    Return to Store Home
                </button>
            </div>
        </div>
    </div>

    <!-- CART & WISHLIST DRAWERS -->
    <div id="cart-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="cart-backdrop" onclick="toggleCartDrawer()" class="absolute inset-0 bg-beautage-purpleDark/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="cart-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-purple-100 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                    <h2 class="text-lg font-bold text-beautage-purple flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-5 h-5 text-beautage-pink"></i> Your Shopping Bag
                    </h2>
                    <button onclick="toggleCartDrawer()" class="text-slate-400 hover:text-beautage-purple p-2"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div id="cart-items" class="mt-4 space-y-4 max-h-[60vh] overflow-y-auto pr-1"></div>
            </div>
            <div class="border-t border-purple-100 pt-4 space-y-3">
                <div class="flex justify-between text-sm text-slate-600"><span>Subtotal</span><span id="cart-subtotal" class="text-beautage-purple font-semibold">Rs. 0</span></div>
                <div class="flex justify-between text-sm text-slate-600"><span>Estimated Tax ({{ $checkoutConfig['tax_percent_label'] ?? 8 }}%)</span><span id="cart-tax" class="text-beautage-purple font-semibold">Rs. 0</span></div>
                <div class="flex justify-between text-base text-beautage-purple font-bold pt-2 border-t border-purple-100"><span>Total</span><span id="cart-total" class="text-beautage-pink">Rs. 0</span></div>
                <button onclick="window.location.href='/checkout'" class="w-full bg-beautage-pink hover:bg-beautage-pinkHover text-white font-semibold py-3.5 rounded-xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Proceed to Checkout
                </button>
            </div>
        </div>
    </div>

    <div id="wishlist-drawer" class="fixed inset-0 z-50 pointer-events-none hidden">
        <div id="wishlist-backdrop" onclick="toggleWishlistDrawer()" class="absolute inset-0 bg-beautage-purpleDark/40 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
        <div id="wishlist-panel" class="absolute right-0 top-0 bottom-0 w-full max-w-md bg-white border-l border-purple-100 p-6 flex flex-col justify-between translate-x-full transition-transform duration-300 pointer-events-auto shadow-2xl">
            <div>
                <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                    <h2 class="text-lg font-bold text-beautage-purple flex items-center gap-2">
                        <i data-lucide="heart" class="w-5 h-5 text-beautage-pink"></i> Your Saved Wishlist
                    </h2>
                    <button onclick="toggleWishlistDrawer()" class="text-slate-400 hover:text-beautage-purple p-2"><i data-lucide="x" class="w-5 h-5"></i></button>
                </div>
                <div id="wishlist-items" class="mt-4 space-y-4 max-h-[70vh] overflow-y-auto pr-1"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
@include('partials.scripts.products-catalog')
<script>
const BEAUTAGE_ORDER_STORE_URL = @json(route('orders.store'));
const BEAUTAGE_SHIPPING_RATES_URL = @json(route('checkout.shipping-rates'));
const CHECKOUT_CONFIG = @json($checkoutConfig ?? []);
const CHECKOUT_PREFILL = @json($prefillCustomer ?? null);
</script>
@verbatim
<script>
        let checkoutCart = [];

        let shippingCost = 0.00;
        let discountPercent = 0;
        let selectedShippingMethodId = (CHECKOUT_CONFIG.shipping_methods && CHECKOUT_CONFIG.shipping_methods[0]) ? CHECKOUT_CONFIG.shipping_methods[0].id : 'standard';
        let shippingRatesTimer = null;
        let selectedPaymentMethodId = (CHECKOUT_CONFIG.payment_methods && CHECKOUT_CONFIG.payment_methods[0]) ? CHECKOUT_CONFIG.payment_methods[0].id : 'cod';
        let selectedPaymentMethod = '';
        let selectedWalletProvider = "Easypaisa";

        const PAY_TAB_ACTIVE = "py-3 px-2 rounded-2xl border-2 border-beautage-purple bg-purple-50 text-beautage-purple font-bold text-[11px] sm:text-xs transition flex flex-col items-center justify-center gap-1 shadow-xs";
        const PAY_TAB_INACTIVE = "py-3 px-2 rounded-2xl border-2 border-purple-200 bg-white text-slate-700 hover:border-purple-300 font-bold text-[11px] sm:text-xs transition flex flex-col items-center justify-center gap-1 shadow-xs";

        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            loadCartFromStorage();
            if (CHECKOUT_CONFIG.payment_methods && CHECKOUT_CONFIG.payment_methods.length) {
                switchPaymentTab(CHECKOUT_CONFIG.payment_methods[0].id);
            }
            if (CHECKOUT_PREFILL) {
                const map = { email: 'cust-email', first_name: 'cust-firstname', last_name: 'cust-lastname', phone: 'cust-phone', address: 'cust-address' };
                Object.keys(map).forEach((key) => {
                    const el = document.getElementById(map[key]);
                    if (el && CHECKOUT_PREFILL[key]) {
                        el.value = CHECKOUT_PREFILL[key];
                    }
                });
            }

            const cityInput = document.getElementById('cust-city');
            if (cityInput) {
                cityInput.addEventListener('input', () => scheduleShippingRatesRefresh());
                cityInput.addEventListener('blur', () => refreshShippingRates());
            }

            shippingCost = resolveShippingCost();
            updateShippingDisplay();
            renderCheckoutSummary();
            startCountdownTimer();

            document.addEventListener('click', (event) => {
                if (!event.target.closest('[data-card-add]')) {
                    return;
                }
                setTimeout(() => {
                    loadCartFromStorage();
                    renderCheckoutSummary();
                }, 40);
            });
        });

        function scheduleShippingRatesRefresh() {
            clearTimeout(shippingRatesTimer);
            shippingRatesTimer = setTimeout(refreshShippingRates, 600);
        }

        async function refreshShippingRates() {
            const city = document.getElementById('cust-city')?.value?.trim();
            if (!city || city.length < 2) {
                return;
            }
            loadCartFromStorage();
            const promoInput = document.getElementById('promo-input');
            const promoCode = promoInput ? promoInput.value.trim() : '';
            const hint = document.getElementById('shipping-rates-hint');
            if (hint) {
                hint.innerText = 'Updating courier rates…';
            }

            try {
                const response = await fetch(BEAUTAGE_SHIPPING_RATES_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify({
                        city,
                        promo_code: promoCode,
                        discount_percent: discountPercent,
                        items: checkoutCart.map((item) => ({ price: item.price, qty: item.qty })),
                    }),
                });
                const result = await response.json();
                if (!response.ok || !result.success || !Array.isArray(result.shipping_methods)) {
                    if (hint) {
                        hint.innerText = 'Could not load live rates — using default options.';
                    }
                    return;
                }
                CHECKOUT_CONFIG.shipping_methods = result.shipping_methods;
                if (result.free_shipping_threshold) {
                    CHECKOUT_CONFIG.free_shipping_threshold = result.free_shipping_threshold;
                }
                renderShippingMethods(result.shipping_methods);
                if (hint) {
                    hint.innerText = 'Live courier rates for ' + city + '.';
                }
            } catch (e) {
                if (hint) {
                    hint.innerText = 'Network error — default shipping options shown.';
                }
            }
        }

        function renderShippingMethods(methods) {
            const container = document.getElementById('shipping-methods-container');
            if (!container || !methods.length) {
                return;
            }
            selectedShippingMethodId = methods[0].id;
            container.innerHTML = methods.map((ship, idx) => {
                const price = Number(ship.price || 0);
                const priceLabel = price === 0 ? 'FREE' : money(price);
                const priceClass = price === 0 ? 'text-emerald-600' : 'text-slate-800';
                const border = idx === 0 ? 'border-beautage-purple bg-purple-50/70' : 'border-purple-200 bg-white hover:border-purple-300';
                const titleClass = idx === 0 ? 'text-beautage-purple' : 'text-slate-800';
                const desc = ship.description ? `<span class="text-[11px] text-slate-500 block">${ship.description}</span>` : '';
                return `<label class="flex items-center justify-between p-4 rounded-2xl border-2 ${border} cursor-pointer transition">
                    <div class="flex items-center gap-3">
                        <input type="radio" name="shipping-method" value="${ship.id}" ${idx === 0 ? 'checked' : ''} class="accent-beautage-pink w-4 h-4" onchange="onShippingMethodChange('${ship.id}')">
                        <div>
                            <span class="text-xs font-extrabold ${titleClass} block">${ship.label}</span>
                            ${desc}
                        </div>
                    </div>
                    <span class="text-xs font-black uppercase ${priceClass}">${priceLabel}</span>
                </label>`;
            }).join('');
            shippingCost = resolveShippingCost();
            updateShippingDisplay();
            renderCheckoutSummary();
        }

        function showToast(message) {
            const container = document.getElementById('toast-container');
            const toast = document.createElement('div');
            toast.className = 'bg-beautage-purple text-white px-4 py-3 rounded-2xl shadow-2xl text-xs font-semibold flex items-center gap-3 border border-purple-500/40 pointer-events-auto transform translate-y-4 opacity-0 transition-all duration-300';
            toast.innerHTML = `<i data-lucide="sparkles" class="w-4 h-4 text-beautage-amber shrink-0"></i><span>${message}</span>`;
            container.appendChild(toast);
            lucide.createIcons();

            setTimeout(() => toast.classList.remove('translate-y-4', 'opacity-0'), 10);
            setTimeout(() => {
                toast.classList.add('translate-y-4', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function money(amount) {
            return window.BeautageMoney ? BeautageMoney.formatDecimal(amount) : `Rs. ${Number(amount || 0).toFixed(0)}`;
        }

        function lineTotal(item) {
            return window.BeautageMoney
                ? BeautageMoney.lineTotal(item)
                : Number(item.price || 0) * Number(item.qty || 1);
        }

        function loadCartFromStorage() {
            if (window.BeautageCart) {
                checkoutCart = BeautageCart.readStorage();
            } else {
                try {
                    const stored = localStorage.getItem('beautage_cart');
                    checkoutCart = stored ? JSON.parse(stored) : [];
                } catch (e) {
                    checkoutCart = [];
                }
            }
            checkoutCart = (checkoutCart || []).map((item) => ({
                ...item,
                price: Number(item.price || 0),
                qty: Number(item.qty || 1),
            }));
        }

        function getDiscountedSubtotal() {
            const subtotal = checkoutCart.reduce((sum, item) => sum + lineTotal(item), 0);
            return subtotal - subtotal * (discountPercent / 100);
        }

        function resolveShippingCost() {
            const methods = CHECKOUT_CONFIG.shipping_methods || [];
            const method = methods.find((m) => m.id === selectedShippingMethodId);
            if (!method) {
                return 0;
            }
            let price = Number(method.price || 0);
            const discounted = getDiscountedSubtotal();
            const threshold = Number(CHECKOUT_CONFIG.free_shipping_threshold || 0);
            if (method.free_over_threshold && discounted >= threshold) {
                return 0;
            }
            return Math.max(0, price);
        }

        function updateShippingDisplay() {
            const shipEl = document.getElementById('summary-shipping');
            if (!shipEl) {
                return;
            }
            if (shippingCost === 0) {
                shipEl.innerText = "FREE";
                shipEl.className = "font-bold text-emerald-600";
            } else {
                shipEl.innerText = money(shippingCost);
                shipEl.className = "font-bold text-slate-800";
            }
        }

        function onShippingMethodChange(methodId) {
            selectedShippingMethodId = methodId;
            shippingCost = resolveShippingCost();
            updateShippingDisplay();
            renderCheckoutSummary();
        }

        function paymentMethodLabel(methodId) {
            const method = (CHECKOUT_CONFIG.payment_methods || []).find((m) => m.id === methodId);
            if (!method) {
                return '';
            }
            if (methodId === 'wallet') {
                return `Mobile Wallet (${selectedWalletProvider})`;
            }
            return method.label || '';
        }

        function switchPaymentTab(methodId) {
            selectedPaymentMethodId = methodId;
            selectedPaymentMethod = paymentMethodLabel(methodId);

            document.querySelectorAll('[data-pay-tab]').forEach((btn) => {
                btn.className = PAY_TAB_INACTIVE;
                if (btn.dataset.payTab === methodId) {
                    btn.className = PAY_TAB_ACTIVE;
                }
            });

            document.querySelectorAll('[id^="payment-panel-"]').forEach((panel) => panel.classList.add('hidden'));
            const panel = document.getElementById('payment-panel-' + methodId);
            if (panel) {
                panel.classList.remove('hidden');
            }
        }

        function selectWalletProvider(provider) {
            selectedWalletProvider = provider;
            selectedPaymentMethod = paymentMethodLabel('wallet');
            document.getElementById('wallet-name-label').innerText = provider;

            const easyLabel = document.getElementById('wallet-easypaisa-label');
            const jazzLabel = document.getElementById('wallet-jazzcash-label');

            if(provider === 'Easypaisa') {
                easyLabel.className = "flex items-center gap-3 p-3 rounded-xl border-2 border-beautage-purple bg-white cursor-pointer transition shadow-xs";
                easyLabel.querySelector('input').checked = true;
                jazzLabel.className = "flex items-center gap-3 p-3 rounded-xl border-2 border-purple-200 bg-white cursor-pointer transition shadow-xs";
                jazzLabel.querySelector('input').checked = false;
            } else {
                jazzLabel.className = "flex items-center gap-3 p-3 rounded-xl border-2 border-beautage-purple bg-white cursor-pointer transition shadow-xs";
                jazzLabel.querySelector('input').checked = true;
                easyLabel.className = "flex items-center gap-3 p-3 rounded-xl border-2 border-purple-200 bg-white cursor-pointer transition shadow-xs";
                easyLabel.querySelector('input').checked = false;
            }
        }

        function applyPromoCode() {
            const input = document.getElementById('promo-input');
            const msg = document.getElementById('promo-message');
            const code = input.value.trim().toUpperCase();

            if(code === 'BEAUTAGE15' || code === 'SAVE15') {
                discountPercent = 15;
                msg.classList.remove('hidden');
                msg.className = 'text-xs font-semibold text-emerald-600';
                msg.innerText = 'Promo code ' + code + ' applied (15% off).';
                document.getElementById('discount-row').classList.remove('hidden');
                document.getElementById('discount-code-name').innerText = code;
                showToast('15% discount applied successfully!');
                refreshShippingRates();
                renderCheckoutSummary();
            } else if(code === 'WELCOME') {
                discountPercent = 10;
                msg.classList.remove('hidden');
                msg.className = 'text-xs font-semibold text-emerald-600';
                msg.innerText = 'Promo code WELCOME applied (10% off).';
                document.getElementById('discount-row').classList.remove('hidden');
                document.getElementById('discount-code-name').innerText = code;
                showToast('10% discount applied successfully!');
                refreshShippingRates();
                renderCheckoutSummary();
            } else {
                msg.classList.remove('hidden');
                msg.className = 'text-xs font-semibold text-rose-600';
                msg.innerText = 'Invalid promo code. Try WELCOME.';
                showToast('Invalid promo code');
            }
        }

        function renderCheckoutSummary() {
            const container = document.getElementById('checkout-cart-items');
            const subtotalEl = document.getElementById('summary-subtotal');
            const discountEl = document.getElementById('summary-discount');
            const taxEl = document.getElementById('summary-tax');
            const grandEl = document.getElementById('summary-grandtotal');

            if (!container || !subtotalEl || !taxEl || !grandEl) {
                return;
            }

            if (checkoutCart.length === 0) {
                container.innerHTML = `<div class="text-center py-6 text-slate-400 text-xs">Your bag is empty. <a href="/" class="text-beautage-pink font-bold">Shop now</a></div>`;
                subtotalEl.innerText = money(0);
                if (discountEl) {
                    discountEl.innerText = `- ${money(0)}`;
                }
                taxEl.innerText = money(0);
                grandEl.innerText = money(0);
                return;
            }

            container.innerHTML = checkoutCart.map((item) => `
                <div class="flex items-center gap-3 p-2.5 rounded-2xl bg-purple-50/50 border border-purple-100">
                    <img src="${item.image}" class="w-14 h-14 object-cover rounded-xl shrink-0" onerror="this.onerror=null; this.src='https://placehold.co/100x100/5E1F70/FFFFFF?text=Item';">
                    <div class="flex-1 min-w-0">
                        <h4 class="text-xs font-bold text-beautage-purple truncate">${item.name}</h4>
                        <span class="text-[11px] text-slate-500">Qty: ${item.qty} × ${money(item.price)}</span>
                    </div>
                    <span class="text-xs font-extrabold text-slate-800">${money(lineTotal(item))}</span>
                </div>
            `).join('');

            const subtotal = checkoutCart.reduce((sum, item) => sum + lineTotal(item), 0);
            const discountAmt = subtotal * (discountPercent / 100);
            const discountedSubtotal = subtotal - discountAmt;
            shippingCost = resolveShippingCost();
            updateShippingDisplay();
            const taxRate = Number(CHECKOUT_CONFIG.tax_rate || 0);
            const tax = discountedSubtotal * taxRate;
            const grandTotal = discountedSubtotal + tax + Number(shippingCost || 0);

            subtotalEl.innerText = money(subtotal);
            if (discountPercent > 0 && discountEl) {
                discountEl.innerText = `- ${money(discountAmt)}`;
            }
            taxEl.innerText = money(tax);
            grandEl.innerText = money(grandTotal);
        }

        window.renderCheckoutSummary = renderCheckoutSummary;

        function expressCheckoutAction(provider) {
            showToast(`Connecting securely to ${provider} express gateway...`);
            setTimeout(() => {
                document.getElementById('cust-email').value = 'you@email.com';
                document.getElementById('cust-firstname').value = 'Valued';
                document.getElementById('cust-lastname').value = 'Customer';
                document.getElementById('cust-address').value = 'Express Portal Verified Address';
                document.getElementById('cust-city').value = 'Lahore';
                document.getElementById('cust-postal').value = '54000';
                document.getElementById('cust-phone').value = '+92 300 1234567';
                showToast(`${provider} verified successfully! Review and complete order.`);
            }, 1000);
        }

        async function handleCompleteOrder(e) {
            e.preventDefault();
            loadCartFromStorage();

            if (checkoutCart.length === 0) {
                showToast('Your bag is empty. Add products before checkout.');
                return;
            }

            const email = document.getElementById('cust-email').value.trim();
            const fname = document.getElementById('cust-firstname').value.trim();
            const lname = document.getElementById('cust-lastname').value.trim();
            const address = document.getElementById('cust-address').value.trim();
            const city = document.getElementById('cust-city').value.trim();
            const postal = document.getElementById('cust-postal')?.value?.trim() || '';
            const phone = document.getElementById('cust-phone')?.value?.trim() || '';

            if(!email || !fname || !address || !city) {
                showToast('Please fill in all required shipping fields.');
                return;
            }

            const subtotal = checkoutCart.reduce((sum, item) => sum + (item.price * item.qty), 0);
            const discountAmt = subtotal * (discountPercent / 100);
            const discountedSubtotal = subtotal - discountAmt;
            shippingCost = resolveShippingCost();
            const taxRate = Number(CHECKOUT_CONFIG.tax_rate || 0);
            const tax = discountedSubtotal * taxRate;
            const grandTotal = discountedSubtotal + tax + shippingCost;

            const promoInput = document.getElementById('promo-input');
            const promoCode = promoInput ? promoInput.value.trim().toUpperCase() : '';

            const payload = {
                customer: {
                    email,
                    first_name: fname,
                    last_name: lname,
                    phone,
                },
                shipping: {
                    address,
                    city,
                    postal,
                    country: 'PK',
                },
                payment_method: selectedPaymentMethod,
                shipping_method_id: selectedShippingMethodId,
                promo_code: promoCode || null,
                discount_percent: discountPercent,
                shipping_total: shippingCost,
                items: checkoutCart.map(item => ({
                    id: item.id,
                    name: item.name,
                    price: item.price,
                    qty: item.qty,
                    image: item.image,
                })),
            };

            try {
                const response = await fetch(BEAUTAGE_ORDER_STORE_URL, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();
                if (!response.ok || !result.success) {
                    showToast(result.message || 'Could not place order. Please try again.');
                    return;
                }

                document.getElementById('success-order-id').innerText = result.order_number;
                document.getElementById('receipt-name').innerText = `${fname} ${lname}`;
                document.getElementById('receipt-address').innerText = `${address}, ${city}`;
                document.getElementById('receipt-payment').innerText = selectedPaymentMethod;
                document.getElementById('receipt-total').innerText = money(result.total);

                if (window.BeautageCart) {
                    BeautageCart.clearCart();
                } else {
                    localStorage.removeItem('beautage_cart');
                }

                if (window.BeautageAnalytics) {
                    BeautageAnalytics.trackPurchase({
                        transaction_id: result.order_number,
                        value: result.total,
                        items: payload.items,
                    });
                }

                document.getElementById('success-modal').classList.remove('hidden');
                showToast('Order placed successfully!');
            } catch (err) {
                showToast('Network error while placing order.');
            }
        }

        function toggleMobileNav() {
            const overlay = document.getElementById('mobile-nav-overlay');
            const drawer = document.getElementById('mobile-nav-drawer');
            if(drawer.classList.contains('-translate-x-full')) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                drawer.classList.remove('-translate-x-full');
            } else {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                drawer.classList.add('-translate-x-full');
            }
        }

        function toggleMobileSearch() {
            const searchContainer = document.getElementById('mobile-search-container');
            searchContainer.classList.toggle('hidden');
        }

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-backdrop');
            const panel = document.getElementById('cart-panel');
            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.remove('opacity-0'); panel.classList.remove('translate-x-full'); }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => drawer.classList.add('hidden'), 300);
            }
        }

        function toggleWishlistDrawer() {
            const drawer = document.getElementById('wishlist-drawer');
            const backdrop = document.getElementById('wishlist-backdrop');
            const panel = document.getElementById('wishlist-panel');
            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => { backdrop.classList.remove('opacity-0'); panel.classList.remove('translate-x-full'); }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => drawer.classList.add('hidden'), 300);
            }
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            if(emailInput.value.trim()) {
                showToast(`Thank you for subscribing! 15% discount code sent.`);
                emailInput.value = '';
            }
        }

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                const timerEl = document.getElementById('announcement-timer');
                if(timerEl) timerEl.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
<script>
if (window.BeautageCart && window.renderCheckoutSummary) {
    BeautageCart.updateCartUI();
}
</script>
@endpush
