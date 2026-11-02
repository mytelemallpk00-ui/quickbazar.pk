@extends('layouts.app')

@section('title', 'Help Center & FAQ — Beautage Flagship Store')

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
                <span class="text-beautage-purple font-bold">Help Center & Frequently Asked Questions</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-8 sm:p-14 shadow-2xl relative overflow-hidden flex flex-col items-center justify-center text-center gap-6">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="max-w-2xl relative z-10 space-y-3">
                    <span class="inline-block px-4 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        24/7 FLAGSHIP CONCIERGE SUPPORT
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">How Can We Assist Your Elegance Today?</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Search our knowledge base for instant answers regarding shipping, tracking, warranty, returns, and organic skincare formulations.
                    </p>
                </div>

                <!-- Instant Search Bar -->
                <div class="w-full max-w-xl relative z-10">
                    <div class="relative flex items-center bg-white rounded-2xl overflow-hidden shadow-2xl border-2 border-purple-200 focus-within:border-beautage-pink transition">
                        <span class="pl-4 text-slate-400"><i data-lucide="search" class="w-5 h-5 text-beautage-purple"></i></span>
                        <input type="text" id="faq-search-input" oninput="filterFaq()" placeholder="Search questions (e.g. tracking, COD, return policy, LED warranty)..." 
                               class="w-full py-4 px-4 text-xs sm:text-sm text-slate-800 placeholder-slate-400 bg-transparent focus:outline-none font-medium">
                        <button class="bg-beautage-pink hover:bg-beautage-pinkHover text-white px-6 py-4 text-xs font-bold uppercase transition">
                            Search
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. QUICK HELP TOPIC CARDS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-12">
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                
                <div onclick="filterByCategory('shipping')" class="help-card bg-white p-6 rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer text-center group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple group-hover:bg-beautage-pink group-hover:text-white transition flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-beautage-purple group-hover:text-beautage-pink transition">Shipping & Delivery</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Delivery times, COD & tracking orders.</p>
                </div>

                <div onclick="filterByCategory('returns')" class="help-card bg-white p-6 rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer text-center group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple group-hover:bg-beautage-pink group-hover:text-white transition flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i data-lucide="rotate-ccw" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-beautage-purple group-hover:text-beautage-pink transition">Returns & Exchanges</h3>
                    <p class="text-[11px] text-slate-500 mt-1">30-day money-back & refund status.</p>
                </div>

                <div onclick="filterByCategory('orders')" class="help-card bg-white p-6 rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer text-center group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple group-hover:bg-beautage-pink group-hover:text-white transition flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i data-lucide="credit-card" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-beautage-purple group-hover:text-beautage-pink transition">Orders & Payments</h3>
                    <p class="text-[11px] text-slate-500 mt-1">Secure payments, coupons & invoices.</p>
                </div>

                <div onclick="filterByCategory('products')" class="help-card bg-white p-6 rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 cursor-pointer text-center group">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple group-hover:bg-beautage-pink group-hover:text-white transition flex items-center justify-center mx-auto mb-4 shadow-sm">
                        <i data-lucide="zap" class="w-6 h-6"></i>
                    </div>
                    <h3 class="text-sm font-bold text-beautage-purple group-hover:text-beautage-pink transition">Product Warranties</h3>
                    <p class="text-[11px] text-slate-600 mt-1">LED mask usage & device warranties.</p>
                </div>

            </div>
        </section>

        <!-- 3. ACCORDION FAQ SECTION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-4xl mx-auto space-y-6">
                
                <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pb-4 border-b border-purple-100">
                    <div>
                        <h2 class="text-2xl font-serif font-bold text-beautage-purple">Frequently Asked Questions</h2>
                        <p class="text-xs text-slate-500 mt-0.5">Click any question to view the comprehensive verified answer.</p>
                    </div>
                    <button onclick="resetFaqFilter()" class="text-xs font-bold text-beautage-pink hover:underline">Show All Topics</button>
                </div>

                <!-- FAQ Accordion Container -->
                <div id="faq-accordion-container" class="space-y-4"></div>

                <!-- No Results Notice -->
                <div id="faq-no-results" class="hidden py-16 text-center text-slate-400 text-sm bg-white rounded-3xl border border-purple-100 shadow-sm">
                    No matching questions found in our knowledge base. Feel free to contact our support team below!
                </div>

            </div>
        </section>

        <!-- 4. STILL NEED HELP? CONTACT CONCIERGE SECTION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-5xl mx-auto rounded-3xl bg-white border-2 border-purple-200 p-8 sm:p-12 shadow-xl grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                <div class="space-y-4">
                    <span class="px-3 py-1 bg-purple-100 text-beautage-purple text-xs font-bold rounded-full uppercase">Direct Concierge</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Still Need Help? Send Us a Message</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Our dedicated skincare specialists and customer support concierges are available 24/7 to resolve any inquiries. Expect a response within 2 hours.
                    </p>
                    
                    <div class="space-y-3 pt-2 text-xs font-semibold text-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                                <i data-lucide="headphones" class="w-4 h-4"></i>
                            </div>
                            <span>Hotline: <a href="tel:+18008886678" class="text-beautage-pink font-bold">+1 (800) BEAUTAGE</a></span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0">
                                <i data-lucide="mail" class="w-4 h-4"></i>
                            </div>
                            <span>Email: <a href="mailto:support@beautage.store" class="text-beautage-pink font-bold">support@beautage.store</a></span>
                        </div>
                    </div>
                </div>

                <form id="contact-support-form" onsubmit="handleSupportSubmit(event)" class="space-y-4 bg-purple-50/50 p-6 sm:p-8 rounded-3xl border border-purple-100">
                    <h3 class="text-sm font-extrabold text-beautage-purple uppercase tracking-wider mb-2">Instant Support Ticket</h3>
                    
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Your Full Name</label>
                        <input type="text" id="support-name" required placeholder="Enter your name..." 
                               class="w-full px-4 py-3 bg-white border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Email Address</label>
                        <input type="email" id="support-email" required placeholder="Enter your email..." 
                               class="w-full px-4 py-3 bg-white border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Inquiry Topic</label>
                        <select id="support-topic" class="w-full px-4 py-3 bg-white border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-semibold cursor-pointer">
                            <option value="shipping">Order Tracking & Shipping</option>
                            <option value="returns">Returns & Refunds</option>
                            <option value="product">Product Usage / LED Warranty</option>
                            <option value="other">General Inquiry</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Message Details</label>
                        <textarea id="support-message" rows="3" required placeholder="How can we help you today?" 
                                  class="w-full px-4 py-3 bg-white border border-purple-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-beautage-purple font-medium"></textarea>
                    </div>

                    <button type="submit" class="w-full py-3.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/20 flex items-center justify-center gap-2">
                        <span>Submit Support Ticket</span>
                        <i data-lucide="send" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </section>

    </main>
@endsection

@section('below_main')

@endsection

@push('scripts')
@verbatim
<script>
        const faqData = [
            {
                id: 1,
                category: "shipping",
                question: "What are the shipping times and is Cash on Delivery (COD) available?",
                answer: "We offer express worldwide shipping as well as Cash on Delivery (COD) across Pakistan. Standard orders are processed within 24 hours and delivered in 2 to 4 business days. Orders over $75 qualify for complimentary express shipping."
            },
            {
                id: 2,
                category: "shipping",
                question: "How can I track the status of my flagship order?",
                answer: "Once your order is dispatched from our fulfillment center, you will receive an SMS and email containing a live tracking link. You can also view real-time status updates directly in your Beautage account dashboard."
            },
            {
                id: 3,
                category: "returns",
                question: "What is Beautage's return and refund policy?",
                answer: "We offer a hassle-free 30-day money-back guarantee on all unopened skincare elixirs and a 1-year replacement warranty on Beauty Tech hardware devices like our Lumina Pro LED Mask. Simply contact support to initiate your return."
            },
            {
                id: 4,
                category: "orders",
                question: "Can I modify or cancel my order after placing it?",
                answer: "Because our fulfillment center processes orders rapidly to ensure prompt delivery, modifications or cancellations can only be made within 1 hour of placing the order. Please contact our 24/7 hotline immediately."
            },
            {
                id: 5,
                category: "products",
                question: "Are all Beautage products 100% original and verified authentic?",
                answer: "Yes, absolutely! Beautage is an authorized flagship brand store. Every product is 100% original, lab-tested, and imported with official brand warranties directly from our certified international laboratories."
            },
            {
                id: 6,
                category: "products",
                question: "How do I properly clean and maintain my Lumina Pro 7-Spectrum LED Mask?",
                answer: "After each session, gently wipe the silicone inner lining with a microfiber cloth dampened with mild soapy water or alcohol-free sanitizer. Never submerge the device in water or use harsh chemical abrasives."
            }
        ];

        let activeCategory = 'all';

        document.addEventListener('DOMContentLoaded', () => {
            renderFaq(faqData);
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

        function renderFaq(data) {
            const container = document.getElementById('faq-accordion-container');
            const noResults = document.getElementById('faq-no-results');

            if(data.length === 0) {
                container.innerHTML = '';
                noResults.classList.remove('hidden');
                return;
            }

            noResults.classList.add('hidden');
            container.innerHTML = data.map((faq, index) => `
                <div class="bg-white rounded-2xl border border-purple-100 shadow-sm overflow-hidden transition duration-300">
                    <button onclick="toggleAccordion(${faq.id})" class="w-full p-5 text-left flex items-center justify-between gap-4 focus:outline-none bg-white hover:bg-purple-50/50 transition">
                        <span class="text-sm font-bold text-beautage-purple flex items-center gap-3">
                            <span class="w-6 h-6 rounded-full bg-purple-100 text-beautage-pink flex items-center justify-center text-xs font-extrabold shrink-0">Q</span>
                            ${faq.question}
                        </span>
                        <i data-lucide="chevron-down" id="chevron-${faq.id}" class="w-4 h-4 text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div id="answer-${faq.id}" class="hidden px-6 pb-5 pt-2 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-purple-50 bg-purple-50/30">
                        ${faq.answer}
                    </div>
                </div>
            `).join('');
            lucide.createIcons();
        }

        function toggleAccordion(id) {
            const answer = document.getElementById(`answer-${id}`);
            const chevron = document.getElementById(`chevron-${id}`);

            if(answer.classList.contains('hidden')) {
                answer.classList.remove('hidden');
                chevron.classList.add('rotate-180');
            } else {
                answer.classList.add('hidden');
                chevron.classList.remove('rotate-180');
            }
        }

        function filterByCategory(cat) {
            activeCategory = cat;
            const filtered = faqData.filter(f => f.category === cat);
            renderFaq(filtered);
            showToast(`Showing FAQs for category: ${cat}`);
        }

        function resetFaqFilter() {
            activeCategory = 'all';
            document.getElementById('faq-search-input').value = '';
            renderFaq(faqData);
            showToast('Showing all FAQ topics.');
        }

        function filterFaq() {
            const query = document.getElementById('faq-search-input').value.toLowerCase();
            const filtered = faqData.filter(f => f.question.toLowerCase().includes(query) || f.answer.toLowerCase().includes(query));
            renderFaq(filtered);
        }

        function handleSupportSubmit(event) {
            event.preventDefault();
            const name = document.getElementById('support-name').value.trim();
            const email = document.getElementById('support-email').value.trim();
            if(name && email) {
                showToast(`Thank you ${name}! Your support ticket has been submitted successfully. We will reply to ${email} within 2 hours.`);
                document.getElementById('contact-support-form').reset();
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
                document.getElementById('announcement-timer').innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
