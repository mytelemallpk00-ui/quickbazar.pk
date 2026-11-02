@extends('layouts.app')

@section('title', 'Returns & Exchanges — Beautage Flagship Store')

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
                <span class="text-beautage-purple font-bold">Returns & Exchanges Center</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-emerald-500/30 text-emerald-200 border border-emerald-400/40">
                        HASSLE-FREE 30-DAY GUARANTEE
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Returns & Exchanges Portal</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        We want you to adore your Beautage luxury purchases. If anything isn't quite right, our streamlined portal makes returns, exchanges, or store credits effortless.
                    </p>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <a href="#initiate-return-form" class="inline-flex items-center justify-center gap-2 px-6 py-4 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition shadow-xl shadow-pink-500/30">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i> Start Return Request
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. QUICK ASSURANCE STATS CARDS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-12">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-lg flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                        <i data-lucide="calendar-check" class="w-6 h-6 text-beautage-pink"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Return Window</h4>
                        <p class="text-sm font-extrabold text-beautage-purple mt-0.5">30-Day Money Back</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-lg flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                        <i data-lucide="truck" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Return Shipping</h4>
                        <p class="text-sm font-extrabold text-beautage-purple mt-0.5">Free Drop-Off & Pickup</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-lg flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                        <i data-lucide="refresh-cw" class="w-6 h-6 text-amber-500"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Exchange Processing</h4>
                        <p class="text-sm font-extrabold text-beautage-purple mt-0.5">Instant Store Credit</p>
                    </div>
                </div>

                <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-lg flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                        <i data-lucide="headphones" class="w-6 h-6 text-cyan-600"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Support Assistance</h4>
                        <p class="text-sm font-extrabold text-beautage-purple mt-0.5">24/7 Concierge Desk</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. INTERACTIVE RETURN & EXCHANGE SUBMISSION WIZARD -->
        <section id="initiate-return-form" class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-purple-100 shadow-2xl p-6 sm:p-12 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-64 h-64 bg-purple-50 rounded-bl-full pointer-events-none -z-0 opacity-60"></div>
                
                <div class="relative z-10 space-y-6">
                    <div class="border-b border-purple-100 pb-6">
                        <span class="text-xs font-extrabold uppercase tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200 inline-block mb-2">Automated Portal</span>
                        <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Initiate a Return or Exchange</h2>
                        <p class="text-xs sm:text-sm text-slate-600 mt-1">Enter your order details below to instantly generate a prepaid return label or exchange request.</p>
                    </div>

                    <form id="return-exchange-form" onsubmit="handleReturnSubmit(event)" class="space-y-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Order Number *</label>
                                <input type="text" id="return-order-id" required placeholder="e.g. BT-98421" 
                                       class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Email Address *</label>
                                <input type="email" id="return-email" required placeholder="Enter order confirmation email" 
                                       class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Request Type *</label>
                                <select id="return-type" onchange="toggleExchangeOptions()" class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-bold text-beautage-purple focus:outline-none focus:border-beautage-purple cursor-pointer">
                                    <option value="return">Return for Full Refund</option>
                                    <option value="exchange">Exchange for Different Item/Size</option>
                                    <option value="store-credit">Store Credit (+ 10% Bonus)</option>
                                </select>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Reason for Return *</label>
                                <select id="return-reason" class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-bold text-beautage-purple focus:outline-none focus:border-beautage-purple cursor-pointer">
                                    <option value="defective">Item Damaged or Defective</option>
                                    <option value="wrong-item">Received Incorrect Item</option>
                                    <option value="not-satisfied">Not Satisfied with Results</option>
                                    <option value="changed-mind">Changed My Mind / Ordered by Mistake</option>
                                </select>
                            </div>
                        </div>

                        <!-- Conditional Exchange Item Selector -->
                        <div id="exchange-options-container" class="hidden space-y-2 p-4 rounded-2xl bg-purple-50 border border-purple-200">
                            <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Select Replacement Item</label>
                            <select id="exchange-item-select" class="w-full py-3 px-4 bg-white border border-purple-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-beautage-purple">
                                <option value="Lumina Pro Mask">Lumina Pro 7-Spectrum LED Mask ($249.00)</option>
                                <option value="Botanical Youth Serum">Botanical Youth Renewal Serum ($89.00)</option>
                                <option value="Sculpting Microcurrent Wand">Sculpting Microcurrent Wand ($129.00)</option>
                                <option value="Thermal Cryo Wand">Thermal Cryo Sculpting Wand ($179.00)</option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-bold text-beautage-purple uppercase tracking-wider">Additional Notes / Defect Description (Optional)</label>
                            <textarea id="return-notes" rows="3" placeholder="Please provide details regarding the item condition or reason..." 
                                      class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium"></textarea>
                        </div>

                        <button type="submit" class="w-full py-4 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2">
                            <span>Submit Request & Generate Prepaid Label</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- 4. DETAILED POLICY & GUIDELINES ACCORDION SECTIONS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-4xl mx-auto space-y-6">
                <div class="text-center space-y-2 mb-8">
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Return & Exchange Guidelines</h2>
                    <p class="text-xs text-slate-500">Everything you need to know about our inspection standards, refund processing times, and eligibility.</p>
                </div>

                <div class="space-y-4">
                    <!-- Policy Item 1 -->
                    <div class="bg-white rounded-2xl border border-purple-100 shadow-md overflow-hidden transition">
                        <button onclick="toggleAccordion('acc-1')" class="w-full p-6 text-left flex items-center justify-between font-bold text-beautage-purple text-sm sm:text-base">
                            <span class="flex items-center gap-3"><i data-lucide="shield-check" class="w-5 h-5 text-beautage-pink"></i> What items are eligible for return?</span>
                            <i data-lucide="chevron-down" id="acc-1-icon" class="w-5 h-5 text-purple-400 transition-transform"></i>
                        </button>
                        <div id="acc-1-content" class="hidden px-6 pb-6 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-50 pt-4">
                            All unused or lightly tested skincare formulations, beauty devices in original packaging with unbroken seals, and unopened fragrances are eligible for a full refund within 30 days of purchase. Beauty tech devices come with a 1-year manufacturer warranty covering technical defects.
                        </div>
                    </div>

                    <!-- Policy Item 2 -->
                    <div class="bg-white rounded-2xl border border-purple-100 shadow-md overflow-hidden transition">
                        <button onclick="toggleAccordion('acc-2')" class="w-full p-6 text-left flex items-center justify-between font-bold text-beautage-purple text-sm sm:text-base">
                            <span class="flex items-center gap-3"><i data-lucide="clock" class="w-5 h-5 text-beautage-pink"></i> How long do refunds take to process?</span>
                            <i data-lucide="chevron-down" id="acc-2-icon" class="w-5 h-5 text-purple-400 transition-transform"></i>
                        </button>
                        <div id="acc-2-content" class="hidden px-6 pb-6 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-50 pt-4">
                            Once your returned item is received at our flagship inspection center, our quality assurance team verifies the product condition within 48 hours. Refunds are then instantly credited back to your original payment method (or bank account for Cash on Delivery orders), typically reflecting within 3 to 5 business days.
                        </div>
                    </div>

                    <!-- Policy Item 3 -->
                    <div class="bg-white rounded-2xl border border-purple-100 shadow-md overflow-hidden transition">
                        <button onclick="toggleAccordion('acc-3')" class="w-full p-6 text-left flex items-center justify-between font-bold text-beautage-purple text-sm sm:text-base">
                            <span class="flex items-center gap-3"><i data-lucide="refresh-cw" class="w-5 h-5 text-beautage-pink"></i> Can I exchange an item for a different product?</span>
                            <i data-lucide="chevron-down" id="acc-3-icon" class="w-5 h-5 text-purple-400 transition-transform"></i>
                        </button>
                        <div id="acc-3-content" class="hidden px-6 pb-6 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-50 pt-4">
                            Yes! You can choose an instant exchange through our online portal or opt for Beautage Store Credit, which includes an additional 10% bonus credit applied immediately to your account for future purchases.
                        </div>
                    </div>

                    <!-- Policy Item 4 -->
                    <div class="bg-white rounded-2xl border border-purple-100 shadow-md overflow-hidden transition">
                        <button onclick="toggleAccordion('acc-4')" class="w-full p-6 text-left flex items-center justify-between font-bold text-beautage-purple text-sm sm:text-base">
                            <span class="flex items-center gap-3"><i data-lucide="truck" class="w-5 h-5 text-beautage-pink"></i> Do I have to pay for return shipping?</span>
                            <i data-lucide="chevron-down" id="acc-4-icon" class="w-5 h-5 text-purple-400 transition-transform"></i>
                        </button>
                        <div id="acc-4-content" class="hidden px-6 pb-6 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-50 pt-4">
                            Return shipping is 100% free across Pakistan for all defective or incorrect items. For change-of-mind returns, a nominal courier handling fee of $4.99 (or equivalent local currency) will be deducted from your refund total.
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main>
@endsection

@section('below_main')

@endsection

@push('scripts')
@verbatim
<script>
        let cart = [];
        let wishlist = [];

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

        function toggleExchangeOptions() {
            const typeSelect = document.getElementById('return-type').value;
            const container = document.getElementById('exchange-options-container');
            if(typeSelect === 'exchange') {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }

        function handleReturnSubmit(event) {
            event.preventDefault();
            const orderId = document.getElementById('return-order-id').value.trim();
            const email = document.getElementById('return-email').value.trim();
            const type = document.getElementById('return-type').value;

            if(orderId && email) {
                showToast(`Return request successfully submitted for order ${orderId}! Prepaid shipping label sent to ${email}.`);
                document.getElementById('return-exchange-form').reset();
                document.getElementById('exchange-options-container').classList.add('hidden');
            }
        }

        function toggleAccordion(id) {
            const content = document.getElementById(`${id}-content`);
            const icon = document.getElementById(`${id}-icon`);
            if(content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon.classList.add('rotate-180');
            } else {
                content.classList.add('hidden');
                icon.classList.remove('rotate-180');
            }
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value.trim();
            if(email) {
                showToast(`Thank you for subscribing! A 15% off coupon has been sent to ${email}.`);
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
                const timerEl = document.getElementById('announcement-timer');
                if(timerEl) timerEl.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
