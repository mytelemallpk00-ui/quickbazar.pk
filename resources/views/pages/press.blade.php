@extends('layouts.app')

@section('title', 'Press & Media Center — Beautage Flagship Store')

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
                <span class="text-beautage-purple font-bold">Press & Media Center</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        OFFICIAL PRESS & MEDIA DESK
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Shaping the Future of Luxury Beauty & Tech</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Welcome to the Beautage Media Center. Explore our official brand press releases, high-resolution media kit assets, media coverage, and direct PR contacts for journalists and editors.
                    </p>
                </div>
                <div class="relative z-10 flex flex-col sm:flex-row gap-3">
                    <a href="#press-releases" class="px-6 py-3.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30 text-center">
                        Latest Releases
                    </a>
                    <a href="#media-kit" class="px-6 py-3.5 bg-white/10 hover:bg-white/20 backdrop-blur-md text-white font-bold text-xs uppercase tracking-wider rounded-xl transition border border-white/20 text-center">
                        Download Media Kit
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. BRAND QUICK STATS GRID -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm text-center">
                    <span class="text-2xl sm:text-3xl font-black text-beautage-purple block font-serif">2023</span>
                    <span class="text-xs text-slate-500 font-semibold mt-1 block uppercase tracking-wider">Flagship Founded</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm text-center">
                    <span class="text-2xl sm:text-3xl font-black text-beautage-pink block font-serif">150K+</span>
                    <span class="text-xs text-slate-500 font-semibold mt-1 block uppercase tracking-wider">Satisfied Customers</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm text-center">
                    <span class="text-2xl sm:text-3xl font-black text-beautage-purple block font-serif">45+</span>
                    <span class="text-xs text-slate-500 font-semibold mt-1 block uppercase tracking-wider">Global Media Features</span>
                </div>
                <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm text-center">
                    <span class="text-2xl sm:text-3xl font-black text-amber-600 block font-serif">100%</span>
                    <span class="text-xs text-slate-500 font-semibold mt-1 block uppercase tracking-wider">Verified Authentic</span>
                </div>
            </div>
        </section>

        <!-- 3. FEATURED PRESS RELEASES SECTION -->
        <section id="press-releases" class="w-full px-4 sm:px-12 xl:px-20 mb-14">
            <div class="flex items-center justify-between mb-6 pb-3 border-b border-purple-100">
                <div>
                    <span class="text-[10px] uppercase font-extrabold tracking-widest text-beautage-pink">Announcements</span>
                    <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple">Latest Press Releases</h2>
                </div>
                <button onclick="showToast('Showing all archived press releases')" class="text-xs font-bold text-beautage-pink hover:underline">View Archive</button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                            <span class="px-2.5 py-1 bg-purple-50 text-beautage-purple rounded-full text-[10px] uppercase tracking-wider font-bold">Product Launch</span>
                            <span>October 18, 2026</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-serif font-bold text-beautage-purple group-hover:text-beautage-pink transition">
                            Beautage Announces Global Flagship Expansion of Lumina Pro 7-Spectrum LED Mask
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            The renowned beauty tech brand introduces its medical-grade photon light therapy engine to international markets, offering clinical skincare rejuvenation at home.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-50 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Press Release #2026-04</span>
                        <button onclick="downloadPressRelease('Beautage Lumina Pro Global Expansion PR')" class="text-xs font-bold text-beautage-pink hover:text-beautage-pinkHover flex items-center gap-1">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download PDF
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                            <span class="px-2.5 py-1 bg-pink-50 text-beautage-pink rounded-full text-[10px] uppercase tracking-wider font-bold">Sustainability</span>
                            <span>September 29, 2026</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-serif font-bold text-beautage-purple group-hover:text-beautage-pink transition">
                            Beautage Commits to 100% Biodegradable Botanical Packaging Across All Serums
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            Reinforcing its luxury sustainability pledge, Beautage eliminates single-use plastics from its entire skincare line, transitioning to recycled amber glass and paper.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-50 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Press Release #2026-03</span>
                        <button onclick="downloadPressRelease('Beautage Sustainability Pledge PR')" class="text-xs font-bold text-beautage-pink hover:text-beautage-pinkHover flex items-center gap-1">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download PDF
                        </button>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-xl transition-all duration-300 p-6 flex flex-col justify-between group">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between text-xs text-slate-400 font-semibold">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] uppercase tracking-wider font-bold">Partnership</span>
                            <span>September 12, 2026</span>
                        </div>
                        <h3 class="text-base sm:text-lg font-serif font-bold text-beautage-purple group-hover:text-beautage-pink transition">
                            Celebrity Endorsement Milestone: Dananeer Mobeen & Fahad Sheikh Join Brand Routines
                        </h3>
                        <p class="text-xs text-slate-600 leading-relaxed line-clamp-3">
                            Leading South Asian icons partner with Beautage to highlight organic hair care and dermatological wellness routines across digital media platforms.
                        </p>
                    </div>
                    <div class="mt-6 pt-4 border-t border-purple-50 flex items-center justify-between">
                        <span class="text-[11px] font-bold text-slate-400">Press Release #2026-02</span>
                        <button onclick="downloadPressRelease('Beautage Celebrity Partnership PR')" class="text-xs font-bold text-beautage-pink hover:text-beautage-pinkHover flex items-center gap-1">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i> Download PDF
                        </button>
                    </div>
                </div>

            </div>
        </section>

        <!-- 4. MEDIA KIT & BRAND ASSETS DOWNLOAD SECTION -->
        <section id="media-kit" class="w-full px-4 sm:px-12 xl:px-20 mb-14">
            <div class="rounded-3xl bg-white border border-purple-100 shadow-xl p-6 sm:p-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-7 space-y-4">
                    <span class="text-[10px] uppercase font-extrabold tracking-widest text-beautage-purple bg-purple-50 px-3 py-1 rounded-full border border-purple-200 inline-block">Brand Assets</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Downloadable Media Kit</h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Journalists, editors, and brand partners can instantly access our official vector logos, high-resolution product photography, brand guidelines, and executive headshots.
                    </p>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <div class="w-10 h-10 rounded-xl bg-beautage-purple text-white flex items-center justify-center shrink-0">
                                <i data-lucide="image" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-beautage-purple">High-Res Product Pack</h4>
                                <span class="text-[10px] text-slate-400">ZIP Archive (48 MB)</span>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-3 rounded-2xl bg-purple-50/50 border border-purple-100">
                            <div class="w-10 h-10 rounded-xl bg-beautage-pink text-white flex items-center justify-center shrink-0">
                                <i data-lucide="file-badge" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-beautage-purple">Official Brand Logos</h4>
                                <span class="text-[10px] text-slate-400">SVG / PNG Kit (12 MB)</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-gradient-to-br from-purple-900 to-beautage-purple text-white rounded-2xl p-6 sm:p-8 flex flex-col justify-between space-y-6 shadow-xl">
                    <div>
                        <span class="text-[10px] uppercase tracking-widest text-pink-300 font-extrabold">Instant Access</span>
                        <h3 class="text-xl font-bold font-serif mt-1">Get Complete Media Package</h3>
                        <p class="text-xs text-purple-100 mt-2">Enter your press credentials to download the complete validated press zip folder instantly.</p>
                    </div>
                    <form onsubmit="handleMediaKitDownload(event)" class="space-y-3">
                        <input type="email" id="mediakit-email" required placeholder="Enter media / press email..." 
                               class="w-full px-4 py-3 bg-purple-950 border border-purple-600 rounded-xl text-xs text-white placeholder-purple-300 focus:outline-none focus:border-pink-400 transition font-medium">
                        <button type="submit" class="w-full py-3.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/30 flex items-center justify-center gap-2">
                            <span>Download Media Kit (68 MB)</span>
                            <i data-lucide="download" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- 5. MEDIA & PRESS INQUIRY CONTACT FORM -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-3xl mx-auto bg-white rounded-3xl border border-purple-100 shadow-xl p-6 sm:p-10 space-y-6">
                <div class="text-center space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200">Get in Touch</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Direct PR & Media Inquiry Desk</h2>
                    <p class="text-xs text-slate-500">Working on a story, review, or interview? Our public relations team responds within 2 business hours.</p>
                </div>

                <form id="pr-inquiry-form" onsubmit="handlePRInquiry(event)" class="space-y-4 pt-2">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Full Name</label>
                            <input type="text" id="pr-name" required placeholder="e.g. Jessica Miller" 
                                   class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Media Publication / Outlet</label>
                            <input type="text" id="pr-outlet" required placeholder="e.g. Vogue / Vanity Fair" 
                                   class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Professional Email</label>
                            <input type="email" id="pr-email" required placeholder="jessica@publication.com" 
                                   class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                        </div>
                        <div>
                            <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Inquiry Type</label>
                            <select id="pr-type" class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-bold text-beautage-purple focus:outline-none focus:border-beautage-purple cursor-pointer">
                                <option value="Interview Request">Executive Interview Request</option>
                                <option value="Product Review Sample">Product Review Sample Request</option>
                                <option value="Press Release Query">Press Release Query</option>
                                <option value="Partnership">Brand Partnership / Collaboration</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Inquiry Details & Deadlines</label>
                        <textarea id="pr-message" required rows="4" placeholder="Please describe your story angle, publication deadline, or sample request..." 
                                  class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple"></textarea>
                    </div>

                    <button type="submit" class="w-full py-4 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-purple-900/30 flex items-center justify-center gap-2">
                        <span>Submit Press Inquiry</span>
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

        function downloadPressRelease(title) {
            showToast(`Downloading official press release: "${title}". PDF file initialized.`);
        }

        function handleMediaKitDownload(event) {
            event.preventDefault();
            const emailInput = document.getElementById('mediakit-email');
            const email = emailInput.value.trim();
            if(email) {
                showToast(`Media kit archive successfully dispatched to ${email}! Check your inbox.`);
                emailInput.value = '';
            }
        }

        function handlePRInquiry(event) {
            event.preventDefault();
            const name = document.getElementById('pr-name').value.trim();
            const outlet = document.getElementById('pr-outlet').value.trim();
            const type = document.getElementById('pr-type').value;

            showToast(`Thank you ${name} (${outlet}). Your ${type} has been sent to our PR Desk.`);
            document.getElementById('pr-inquiry-form').reset();
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
                const el = document.getElementById('announcement-timer');
                if(el) el.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
