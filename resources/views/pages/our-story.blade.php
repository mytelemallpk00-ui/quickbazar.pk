@extends('layouts.app')

@section('title', 'Our Story & Heritage — Beautage Flagship Store')

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
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">Our Story & Heritage</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-14 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-96 h-96 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-4 text-center md:text-left">
                    <span class="inline-block px-4 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        ELEVATING NATURAL ELEGANCE SINCE 2018
                    </span>
                    <h1 class="text-3xl sm:text-5xl font-serif font-bold text-white tracking-tight">The Beautage Philosophy</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed font-medium">
                        We bridge the gap between advanced dermatological science and pure botanical luxury. Discover how our passion for uncompromising quality created Pakistan’s most trusted flagship beauty destination.
                    </p>
                </div>
                <div class="relative z-10 flex items-center gap-4">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-6 rounded-2xl text-center shadow-xl">
                        <span class="text-3xl font-black text-amber-300 block">100%</span>
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-bold">Verified Original</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. BRAND MISSION & VISION SECTION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center max-w-7xl mx-auto">
                <div class="lg:col-span-6 space-y-6">
                    <span class="text-xs font-extrabold text-beautage-pink uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-full border border-pink-200 inline-block">
                        Our Core Purpose
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-serif font-bold text-beautage-purple leading-tight">
                        Empowering Confidence Through Science & Botanical Purity
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-medium">
                        At Beautage, we believe that true elegance is timeless and deeply personal. Founded by a coalition of dermatologists and organic chemists, our mission is to formulate and curate luxury skin elixirs and photon beauty devices that deliver visible, clinical results without compromise.
                    </p>
                    <div class="grid grid-cols-2 gap-6 pt-4 border-t border-purple-100">
                        <div>
                            <span class="text-2xl sm:text-3xl font-black text-beautage-purple block">50K+</span>
                            <span class="text-xs text-slate-500 font-semibold mt-1 block">Delighted Glowing Customers</span>
                        </div>
                        <div>
                            <span class="text-2xl sm:text-3xl font-black text-beautage-pink block">100%</span>
                        <span class="text-xs text-slate-500 font-semibold mt-1 block">Cruelty-Free & Dermatologist Tested</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-6 relative">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl border-2 border-purple-100 aspect-[4/3] bg-purple-50 group">
                        <img src="https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?auto=format&fit=crop&q=80&w=800" alt="Beautage Laboratory & Ingredients" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-beautage-purpleDark/60 via-transparent to-transparent"></div>
                        <div class="absolute bottom-6 left-6 right-6 text-white">
                            <span class="text-[10px] font-extrabold uppercase tracking-widest text-amber-300 block">State of the Art</span>
                        <h4 class="text-base font-serif font-bold">Research & Botanical Formulation Studio</h4>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. BRAND MILESTONES TIMELINE -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-16 bg-purple-50/60 border-y border-purple-100">
            <div class="max-w-5xl mx-auto space-y-12">
                <div class="text-center space-y-2">
                    <span class="text-xs font-extrabold text-beautage-purple uppercase tracking-widest bg-purple-100 px-3 py-1 rounded-full border border-purple-200 inline-block">
                        Our Journey
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-serif font-bold text-beautage-purple">Milestones of Excellence</h2>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto">From a modest botanical research lab to Pakistan’s leading flagship beauty destination.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl transition relative group">
                        <span class="text-2xl font-black text-beautage-pink font-mono block mb-2">2018</span>
                        <h3 class="text-sm font-bold text-beautage-purple">The Inception</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Founded in Lahore with a singular focus on organic rose-infused botanical serums.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl transition relative group">
                        <span class="text-2xl font-black text-beautage-pink font-mono block mb-2">2021</span>
                        <h3 class="text-sm font-bold text-beautage-purple">Beauty Tech Era</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Introduced the landmark Lumina Pro 7-Spectrum LED mask to clinical acclaim.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl transition relative group">
                        <span class="text-2xl font-black text-beautage-pink font-mono block mb-2">2023</span>
                        <h3 class="text-sm font-bold text-beautage-purple">Celebrity Partnerships</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Partnered with Pakistan's top icons including Dananeer Mobeen and Saboor Aly.</p>
                    </div>
                    <div class="bg-white rounded-2xl p-6 border border-purple-100 shadow-sm hover:shadow-xl transition relative group">
                        <span class="text-2xl font-black text-beautage-pink font-mono block mb-2">2026</span>
                        <h3 class="text-sm font-bold text-beautage-purple">Flagship Expansion</h3>
                        <p class="text-xs text-slate-500 mt-2 leading-relaxed">Launching our omnichannel digital store with express delivery across Pakistan.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 4. MEET THE LEADERSHIP / RESEARCH TEAM -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-16">
            <div class="max-w-7xl mx-auto space-y-12">
                <div class="text-center space-y-2">
                    <span class="text-xs font-extrabold text-beautage-pink uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-full border border-pink-200 inline-block">
                        Our Experts
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-serif font-bold text-beautage-purple">Meet the Minds Behind Beautage</h2>
                    <p class="text-xs sm:text-sm text-slate-600 max-w-xl mx-auto">Dedicated scientists, dermatologists, and aestheticians driving skincare innovation.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    
                    <div class="bg-white rounded-3xl overflow-hidden border border-purple-100 shadow-md hover:shadow-xl transition group">
                        <div class="aspect-[4/3] overflow-hidden bg-purple-50 relative">
                            <img src="https://images.unsplash.com/photo-1594824813575-b6d3632e2c56?auto=format&fit=crop&q=80&w=600" alt="Dr. Aliza Thorne" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-6 space-y-2 text-center">
                            <h3 class="text-base font-bold text-beautage-purple">Dr. Aliza Thorne</h3>
                            <span class="text-xs text-beautage-pink font-bold block">Chief Dermatological Researcher</span>
                            <p class="text-xs text-slate-500 leading-relaxed pt-2">Specializing in optical wavelengths and collagen biosynthesis with over 15 years of clinical practice.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl overflow-hidden border border-purple-100 shadow-md hover:shadow-xl transition group">
                        <div class="aspect-[4/3] overflow-hidden bg-purple-50 relative">
                            <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&q=80&w=600" alt="Sarah Jenkins" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-6 space-y-2 text-center">
                            <h3 class="text-base font-bold text-beautage-purple">Sarah Jenkins</h3>
                            <span class="text-xs text-beautage-pink font-bold block">Senior Botanical Formulator</span>
                        <p class="text-xs text-slate-500 leading-relaxed pt-2">Pioneering sustainable organic extractions and multi-molecular hyaluronic hydration complexes.</p>
                        </div>
                    </div>

                    <div class="bg-white rounded-3xl overflow-hidden border border-purple-100 shadow-md hover:shadow-xl transition group">
                        <div class="aspect-[4/3] overflow-hidden bg-purple-50 relative">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&q=80&w=600" alt="Fahad Sheikh" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </div>
                        <div class="p-6 space-y-2 text-center">
                            <h3 class="text-base font-bold text-beautage-purple">Fahad Sheikh</h3>
                            <span class="text-xs text-beautage-pink font-bold block">Head of Quality & Brand Trust</span>
                            <p class="text-xs text-slate-500 leading-relaxed pt-2">Ensuring 100% original verified authenticity across every single flagship product in our catalog.</p>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <!-- 5. SUSTAINABILITY & CRUELTY FREE PROMISE -->
        <section class="w-full px-4 sm:px-12 xl:px-20 py-16 bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white rounded-3xl max-w-7xl mx-auto mb-12 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-20 -top-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                <div class="lg:col-span-8 space-y-4">
                    <span class="px-3 py-1 bg-emerald-500/20 text-emerald-300 text-[10px] uppercase font-extrabold tracking-widest rounded-full border border-emerald-400/40 inline-block">
                        ECO-RESPONSIBLE PLEDGE
                    </span>
                    <h2 class="text-2xl sm:text-4xl font-serif font-bold text-white">Sustainability at Our Core</h2>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed font-medium">
                        We believe beautiful skin should never cost the Earth. All Beautage packaging is fully recyclable, cruelty-free certified, and sourced from ethically managed botanical reserves.
                    </p>
                </div>
                <div class="lg:col-span-4 flex justify-lg-end">
                    <a href="{{ route('collections') }}" class="inline-flex items-center gap-2 bg-beautage-pink hover:bg-beautage-pinkHover text-white px-6 py-3.5 rounded-xl font-bold text-xs uppercase tracking-wider transition shadow-lg shadow-pink-500/30">
                        <span>Explore Flagship Products</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </a>
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
