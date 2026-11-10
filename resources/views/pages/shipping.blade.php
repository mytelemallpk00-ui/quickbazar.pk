@extends('layouts.app')

@section('title', 'Pakistan Domestic Shipping & Delivery — Quick Bazar Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-6">

        <!-- 1. BREADCRUMB & HERO BANNER -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap mb-4 overflow-x-auto no-scrollbar">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">Domestic Shipping & Delivery (Pakistan)</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-emerald-500/30 text-emerald-200 border border-emerald-400/40">
                        PAKISTAN NATIONWIDE FULFILLMENT
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Domestic Shipping & Delivery Across Pakistan</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Fast, reliable, and secure Cash on Delivery (COD) shipping across Lahore, Karachi, Islamabad, Faisalabad, Rawalpindi, Multan, Bahawalpur, Quetta, Peshawar, Gilgit, and all cities and towns in Pakistan.
                    </p>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-center shadow-lg">
                        <span class="text-2xl sm:text-3xl font-black text-amber-300 block">FREE</span>
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-bold">Over PKR {{ number_format($freeShippingThreshold ?? 5000, 0) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. INTERACTIVE PAKISTAN DELIVERY COST & TIME CALCULATOR -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-12">
            <div class="bg-white rounded-3xl border-2 border-purple-200 shadow-xl p-6 sm:p-10 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-50 rounded-full blur-2xl pointer-events-none -mr-20 -mt-20"></div>
                
                <div class="max-w-3xl mx-auto space-y-6 relative z-10">
                    <div class="text-center space-y-2">
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200 inline-block">Pakistan Transit & Rate Calculator</span>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple">Check City Delivery Time & Courier Charges</h2>
                        <p class="text-xs text-slate-500">Select your destination city in Pakistan and order subtotal to view exact courier fees and delivery windows.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-2">
                        <div>
                            <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider mb-1.5">Select City / Region</label>
                            <select id="calc-city" class="w-full py-3 px-3.5 bg-purple-50/80 border border-purple-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-beautage-purple cursor-pointer">
                                <option value="major-metro">Lahore, Karachi, Islamabad, Rawalpindi</option>
                                <option value="faisalabad-multan">Faisalabad, Multan, Bahawalpur, Gujranwala</option>
                                <option value="quetta-peshawar">Peshawar, Quetta, Hyderabad, Sukkur</option>
                                <option value="remote-gilgit">Gilgit-Baltistan, AJK & Remote Towns</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider mb-1.5">Order Value (PKR)</label>
                            <input type="number" id="calc-amount" value="3500" min="100" class="w-full py-3 px-3.5 bg-purple-50/80 border border-purple-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-beautage-purple">
                        </div>
                        <div class="flex items-end">
                            <button onclick="calculatePakistanShipping()" class="w-full py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/25 flex items-center justify-center gap-2">
                                <i data-lucide="calculator" class="w-4 h-4"></i> Estimate Delivery
                            </button>
                        </div>
                    </div>

                    <!-- Calculator Result Box -->
                    <div id="calc-result" class="hidden rounded-2xl bg-gradient-to-r from-purple-900 to-beautage-purple text-white p-6 shadow-lg space-y-3 transition-all duration-300">
                        <div class="flex items-center justify-between border-b border-purple-800 pb-3">
                            <span class="text-xs uppercase tracking-wider text-purple-200 font-extrabold">Delivery Fee (PKR)</span>
                            <span id="result-fee" class="text-lg font-black text-amber-300">₨0 (FREE SHIPPING)</span>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="clock" class="w-4 h-4 text-cyan-300 shrink-0"></i>
                                <span>Estimated Transit: <strong id="result-time" class="text-white">1 - 2 Business Days</strong></span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <i data-lucide="truck" class="w-4 h-4 text-emerald-300 shrink-0"></i>
                                <span>Courier Partner: <strong id="result-courier" class="text-white">TCS / Leopards / Trax</strong></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. PAKISTAN COURIER PARTNERS & ZONES -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-7xl mx-auto space-y-10">
                
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest">Nationwide Logistics Network</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Our Trusted Pakistan Courier Partners</h2>
                    <p class="text-xs text-slate-600">We collaborate with Pakistan's leading courier services to ensure prompt and secure delivery right to your doorstep.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    
                    <!-- TCS -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4 flex flex-col justify-between hover:border-beautage-pink transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-red-100 text-red-600 flex items-center justify-center font-black text-xs shadow-sm">TCS</div>
                            <h3 class="text-base font-serif font-bold text-beautage-purple">TCS Express</h3>
                            <p class="text-xs text-slate-600">Primary delivery partner for major metropolitan hubs across Pakistan.</p>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-bold">1-2 Working Days</span>
                        </div>
                    </div>

                    <!-- Leopards -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4 flex flex-col justify-between hover:border-beautage-pink transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black text-xs shadow-sm">LCS</div>
                            <h3 class="text-base font-serif font-bold text-beautage-purple">Leopards Courier</h3>
                        <p class="text-xs text-slate-600">Extensive nationwide coverage reaching Tier-2 and Tier-3 cities.</p>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-bold">2-3 Working Days</span>
                        </div>
                    </div>

                    <!-- M&P -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4 flex flex-col justify-between hover:border-beautage-pink transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-cyan-100 text-cyan-700 flex items-center justify-center font-black text-xs shadow-sm">M&P</div>
                            <h3 class="text-base font-serif font-bold text-beautage-purple">M&P (Muller & Phipps)</h3>
                            <p class="text-xs text-slate-600">Trusted logistics handling sensitive beauty tech and cosmetics safely.</p>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-bold">2-4 Working Days</span>
                        </div>
                    </div>

                    <!-- Trax -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4 flex flex-col justify-between hover:border-beautage-pink transition">
                        <div class="space-y-3">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-xs shadow-sm">TRX</div>
                            <h3 class="text-base font-serif font-bold text-beautage-purple">Trax Logistics</h3>
                            <p class="text-xs text-slate-600">Reliable fulfillment partner offering real-time SMS alerts.</p>
                            <span class="inline-block px-2.5 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-bold">2-3 Working Days</span>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 4. ORDER TRACKING SIMULATOR (PAKISTAN) -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-10 shadow-2xl relative overflow-hidden">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="max-w-3xl mx-auto space-y-6 relative z-10 text-center">
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-emerald-500/30 text-emerald-200 border border-emerald-400/40">
                        PAKISTAN PARCEL TRACKING
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-white">Track Your Pakistan Shipment</h2>
                    <p class="text-xs sm:text-sm text-purple-100 max-w-xl mx-auto">
                        Enter your Quick Bazar tracking ID (e.g. BTG-PK-9821) below to view live courier status.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center gap-3 max-w-xl mx-auto pt-2">
                        <div class="relative w-full">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-purple-300">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </span>
                            <input type="text" id="tracking-input" placeholder="e.g. BTG-PK-9821" 
                                   class="w-full pl-10 pr-4 py-3.5 bg-purple-950 border border-purple-500 rounded-xl text-xs text-white placeholder-purple-300 focus:outline-none focus:border-emerald-400 transition font-mono font-bold shadow-inner">
                        </div>
                        <button onclick="simulateTracking()" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shrink-0 flex items-center justify-center gap-2">
                            <span>Track Order</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>

                    <!-- Tracking Result Box -->
                    <div id="tracking-result-box" class="hidden rounded-2xl bg-white/10 backdrop-blur-md border border-white/20 p-6 text-left space-y-4">
                        <div class="flex items-center justify-between border-b border-white/20 pb-3">
                            <div>
                                <span class="text-[10px] text-purple-200 uppercase tracking-wider block">Tracking ID: <strong id="track-id-disp" class="font-mono text-amber-300">BTG-PK-9821</strong></span>
                                <h4 class="text-sm font-bold text-white mt-0.5">Status: <span id="track-status-disp" class="text-emerald-400 font-extrabold">Out for Delivery</span></h4>
                            </div>
                            <span class="px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-[10px] font-bold border border-emerald-400/30">In Transit</span>
                        </div>
                        <div class="space-y-3 text-xs text-purple-100">
                            <div class="flex items-start gap-3">
                                <div class="w-2.5 h-2.5 rounded-full bg-emerald-400 mt-1 shrink-0 shadow"></div>
                                <div>
                                    <strong class="text-white block">Out for Delivery with Rider</strong>
                                    <span class="text-[11px] text-purple-200">Delivery expected today by 6:00 PM • Local City Hub</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 opacity-80">
                                <div class="w-2.5 h-2.5 rounded-full bg-purple-400 mt-1 shrink-0"></div>
                                <div>
                                    <strong class="text-white block">Arrived at Destination Hub</strong>
                                    <span class="text-[11px] text-purple-200">Sorting Facility, Pakistan</span>
                                </div>
                            </div>
                            <div class="flex items-start gap-3 opacity-60">
                                <div class="w-2.5 h-2.5 rounded-full bg-purple-400 mt-1 shrink-0"></div>
                                <div>
                                    <strong class="text-white block">Dispatched from Quick Bazar Flagship Warehouse</strong>
                                    <span class="text-[11px] text-purple-200">Order Confirmed via Phone Verification</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. DELIVERY GUIDELINES & COD POLICIES -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="bg-white rounded-3xl border border-purple-100 p-8 sm:p-12 shadow-xl space-y-8">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest">Secure & Transparent</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">COD & Delivery Policies in Pakistan</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="space-y-3 p-6 rounded-2xl bg-purple-50/50 border border-purple-100">
                        <div class="w-10 h-10 rounded-xl bg-beautage-purple text-white flex items-center justify-center font-bold text-sm">1</div>
                        <h3 class="font-bold text-beautage-purple text-sm">Order Verification Call</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            For all Cash on Delivery (COD) orders within Pakistan, our support team may call or WhatsApp you to confirm your shipping address and items before dispatch.
                        </p>
                    </div>

                    <div class="space-y-3 p-6 rounded-2xl bg-purple-50/50 border border-purple-100">
                        <div class="w-10 h-10 rounded-xl bg-beautage-purple text-white flex items-center justify-center font-bold text-sm">2</div>
                        <h3 class="font-bold text-beautage-purple text-sm">Tamper-Proof Packaging</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            All parcels are securely sealed in branded, tamper-evident Quick Bazar polybags and boxes to ensure your luxury cosmetics and tech devices arrive pristine.
                        </p>
                    </div>

                    <div class="space-y-3 p-6 rounded-2xl bg-purple-50/50 border border-purple-100">
                        <div class="w-10 h-10 rounded-xl bg-beautage-purple text-white flex items-center justify-center font-bold text-sm">3</div>
                        <h3 class="font-bold text-beautage-purple text-sm">Inspection at Delivery</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            You may inspect your outer parcel condition upon delivery before making cash payment to the courier rider. Unboxing video is recommended for electronics.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 6. FREQUENTLY ASKED QUESTIONS (FAQ) ACCORDION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-4xl mx-auto space-y-6">
                
                <div class="text-center space-y-2">
                    <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest">Customer Assistance</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Pakistan Shipping FAQs</h2>
                </div>

                <div class="space-y-4">
                    
                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>How long does delivery take across Pakistan?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            Major cities like Lahore, Karachi, Islamabad, and Rawalpindi typically receive orders within 1 to 2 business days. Other cities and remote towns across Punjab, Sindh, KPK, Balochistan, and Gilgit-Baltistan take 3 to 5 business days.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>Is Cash on Delivery available for all cities in Pakistan?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            Yes! Cash on Delivery (COD) is available nationwide across all districts and towns in Pakistan with zero extra charges.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>Can I change my delivery address after placing an order?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            Yes, you can update your delivery address before dispatch by contacting our WhatsApp or telephone support helpline (+92 300 1234567).
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>What should I do if my parcel arrives damaged?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            If your shipment is damaged or tampered with, please notify us within 48 hours with photographic proof or an unboxing video, and we will arrange an immediate replacement.
                        </p>
                    </div>

                </div>
            </div>
        </section>

    </main>
@endsection

@section('below_main')

@endsection

@push('scripts')
<script>const FREE_SHIPPING_THRESHOLD = @json((float) ($freeShippingThreshold ?? 5000));</script>
@verbatim
<script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
            startCountdownTimer();
        });

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

        function calculatePakistanShipping() {
            const cityGroup = document.getElementById('calc-city').value;
            const amount = parseFloat(document.getElementById('calc-amount').value) || 0;
            const resultBox = document.getElementById('calc-result');
            const feeDisp = document.getElementById('result-fee');
            const timeDisp = document.getElementById('result-time');
            const courierDisp = document.getElementById('result-courier');

            let fee = 200;
            let time = '1 - 2 Business Days';
            let courier = 'TCS / Leopards / Trax Express';

            if (amount >= FREE_SHIPPING_THRESHOLD) {
                fee = 0;
            } else {
                if (cityGroup === 'major-metro') {
                    fee = 200;
                    time = '1 - 2 Business Days';
                } else if (cityGroup === 'faisalabad-multan') {
                    fee = 220;
                    time = '2 - 3 Business Days';
                } else if (cityGroup === 'quetta-peshawar') {
                    fee = 250;
                    time = '3 - 4 Business Days';
                } else {
                    fee = 300;
                    time = '4 - 5 Business Days';
                }
            }

            if (fee === 0) {
                feeDisp.innerHTML = `₨0 (FREE SHIPPING QUALIFIED!)`;
            } else {
                feeDisp.innerHTML = `₨${fee}`;
            }
            timeDisp.innerText = time;
            courierDisp.innerText = courier;

            resultBox.classList.remove('hidden');
            showToast('Pakistan delivery estimation calculated successfully!');
        }

        function simulateTracking() {
            const input = document.getElementById('tracking-input').value.trim();
            const box = document.getElementById('tracking-result-box');
            if(input) {
                document.getElementById('track-id-disp').innerText = input.toUpperCase();
                box.classList.remove('hidden');
                showToast(`Tracking status retrieved for ${input.toUpperCase()}`);
            } else {
                showToast('Please enter a valid Pakistan tracking ID.');
            }
        }

        function toggleFaq(headerEl) {
            const p = headerEl.nextElementSibling;
            const icon = headerEl.querySelector('i');
            if(p.classList.contains('hidden')) {
                p.classList.remove('hidden');
                icon.style.transform = 'rotate(180deg)';
            } else {
                p.classList.add('hidden');
                icon.style.transform = 'rotate(0deg)';
            }
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value.trim();
            if(email) {
                showToast(`Thank you! 15% off discount voucher sent to ${email}.`);
                emailInput.value = '';
            }
        }

        function toggleCartDrawer() {
            const drawer = document.getElementById('cart-drawer');
            const backdrop = document.getElementById('cart-backdrop');
            const panel = document.getElementById('cart-panel');

            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('translate-x-full');
                }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => { drawer.classList.add('hidden'); }, 300);
            }
        }

        function toggleWishlistDrawer() {
            const drawer = document.getElementById('wishlist-drawer');
            const backdrop = document.getElementById('wishlist-backdrop');
            const panel = document.getElementById('wishlist-panel');

            if(drawer.classList.contains('hidden')) {
                drawer.classList.remove('hidden');
                setTimeout(() => {
                    backdrop.classList.remove('opacity-0');
                    panel.classList.remove('translate-x-full');
                }, 10);
            } else {
                backdrop.classList.add('opacity-0');
                panel.classList.add('translate-x-full');
                setTimeout(() => { drawer.classList.add('hidden'); }, 300);
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

        function startCountdownTimer() {
            let totalSeconds = 4 * 3600 + 32 * 60 + 19;
            setInterval(() => {
                if(totalSeconds > 0) totalSeconds--;
                const h = Math.floor(totalSeconds / 3600).toString().padStart(2, '0');
                const m = Math.floor((totalSeconds % 3600) / 60).toString().padStart(2, '0');
                const s = (totalSeconds % 60).toString().padStart(2, '0');
                const el = document.getElementById('announcement-timer');
                if(el) el.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
