@extends('layouts.app')

@section('title', $post->title . ' — Beautage Journal')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-6">

        <!-- 1. BREADCRUMB NAVIGATION -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-6 overflow-x-auto no-scrollbar">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <a href="{{ route('blog.index') }}" class="hover:text-beautage-purple transition">Flagship Journal</a>
                <i data-lucide="chevron-right" class="w-3 h-3 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">{{ $post->title }}</span>
            </nav>
        </div>

        <!-- 2. ARTICLE HERO HEADER -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-8">
            <div class="max-w-4xl mx-auto space-y-4 text-center">
                <span class="inline-block px-3.5 py-1 rounded-full text-xs font-extrabold bg-pink-100 text-beautage-pink uppercase tracking-widest border border-pink-200 shadow-xs">
                    {{ $post->category }}{{ $post->is_featured ? ' • Cover Story' : '' }}
                </span>
                <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-beautage-purple tracking-tight leading-tight">
                    {{ $post->title }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed max-w-2xl mx-auto">
                    {{ $post->snippet }}
                </p>

                <div class="flex items-center justify-center gap-4 pt-2 text-xs font-semibold text-slate-500 flex-wrap">
                    <div class="flex items-center gap-2">
                        <div class="w-9 h-9 rounded-full bg-beautage-purple text-white font-bold flex items-center justify-center text-xs">{{ $post->author_avatar }}</div>
                        <div class="text-left">
                            <h4 class="font-bold text-slate-800">{{ $post->author }}</h4>
                            <span class="text-[10px] text-slate-400">{{ $post->author_role }}</span>
                        </div>
                    </div>
                    <span class="text-slate-300">•</span>
                    <span>{{ $post->published_at->format('F j, Y') }}</span>
                    <span class="text-slate-300">•</span>
                    <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> {{ $post->read_time }}</span>
                </div>
            </div>
        </section>

        <!-- 3. FEATURED BANNER IMAGE WITH HOVER ZOOM -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-10">
            <div class="max-w-5xl mx-auto rounded-3xl overflow-hidden bg-purple-50 border border-purple-100 shadow-xl aspect-[16/9] relative group">
                <img src="{{ $post->image }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                <div class="absolute bottom-4 left-4 bg-black/70 backdrop-blur-md text-white text-[11px] font-bold px-3.5 py-1.5 rounded-xl">
                    Photo: Lumina Pro 7-Spectrum Flagship Engine in Operation
                </div>
            </div>
        </section>

        <!-- 4. ARTICLE BODY CONTENT & SIDEBAR WIDGETS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
                
                <!-- MAIN ARTICLE CONTENT (SPAN 8) -->
                <article class="lg:col-span-8 bg-white rounded-3xl p-6 sm:p-10 border border-purple-100 shadow-xl space-y-6 text-slate-700 leading-relaxed text-sm sm:text-base">
                    
                    <div class="space-y-4 text-slate-700 leading-relaxed">
                        {!! $post->content !!}
                    </div>

                    <div class="mt-8 pt-6 border-t border-purple-100 flex flex-col sm:flex-row items-center gap-4 bg-purple-50/50 p-6 rounded-2xl">
                        <div class="w-16 h-16 rounded-full bg-beautage-purple text-white font-bold flex items-center justify-center text-lg shrink-0">{{ $post->author_avatar }}</div>
                        <div class="text-center sm:text-left space-y-1">
                            <h4 class="font-bold text-beautage-purple text-sm">About {{ $post->author }}</h4>
                            <p class="text-xs text-slate-600">{{ $post->author_role }}</p>
                        </div>
                    </div>

                    <!-- Article Interaction Toolbar (Likes & Share) -->
                    <div class="flex items-center justify-between pt-6 border-t border-purple-100">
                        <button onclick="likeArticle()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-pink-50 text-beautage-pink hover:bg-pink-100 transition text-xs font-bold shadow-xs">
                            <i data-lucide="heart" class="w-4 h-4 fill-current"></i> Like Article (<span id="article-like-count">{{ $post->likes }}</span>)
                        </button>
                        <button onclick="shareArticle()" class="flex items-center gap-2 px-5 py-2.5 rounded-xl bg-purple-50 text-beautage-purple hover:bg-purple-100 transition text-xs font-bold shadow-xs">
                            <i data-lucide="share-2" class="w-4 h-4"></i> Share Story
                        </button>
                    </div>

                </article>

                <!-- SIDEBAR WIDGETS (SPAN 4) -->
                <aside class="lg:col-span-4 space-y-6 lg:sticky lg:top-28">
                    
                    <!-- Featured Product Card Widget -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                        <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-full border border-pink-200">Featured Device</span>
                        <div class="aspect-square rounded-2xl overflow-hidden bg-purple-50 border border-purple-100 relative group">
                            <img src="https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=400" alt="Lumina Pro Mask" class="w-full h-full object-cover group-hover:scale-105 transition">
                            <span class="absolute top-2.5 left-2.5 bg-beautage-purple text-white text-[10px] font-extrabold px-2.5 py-1 rounded-full uppercase">Save 30%</span>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-beautage-purple">Lumina Pro 7-Spectrum LED Mask</h4>
                            <div class="flex items-baseline gap-2 mt-1">
                                <span class="text-lg font-black text-slate-800">$249.00</span>
                                <span class="text-xs text-slate-400 line-through">$349.00</span>
                            </div>
                        </div>
                        <a href="{{ route('product.show', ['product' => 'lumina-pro-led-mask']) }}" class="block w-full text-center py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/20">
                            Shop Flagship Device
                        </a>
                    </div>

                    <!-- Related Articles Widget -->
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                        <h4 class="text-xs font-extrabold text-beautage-purple uppercase tracking-wider pb-2 border-b border-purple-100">Related Articles</h4>
                        
                        @foreach($relatedPosts as $related)
                        <a href="{{ route('blog.show', $related) }}" class="flex items-center gap-3 group">
                            <img src="{{ $related->image }}" alt="{{ $related->title }}" class="w-16 h-16 rounded-xl object-cover shrink-0">
                            <div>
                                <h5 class="text-xs font-bold text-beautage-purple group-hover:text-beautage-pink transition line-clamp-2">{{ $related->title }}</h5>
                                <span class="text-[10px] text-slate-400 mt-1 block">{{ $related->read_time }}</span>
                            </div>
                        </a>
                        @endforeach
                    </div>

                </aside>

            </div>
        </section>

    </main>
@endsection

@section('below_main')

@endsection

@push('scripts')
<script>let likes = {{ $post->likes }};</script>
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

        function likeArticle() {
            likes++;
            document.getElementById('article-like-count').innerText = likes;
            showToast('Thank you for liking this article!');
        }

        function shareArticle() {
            if(navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href);
                showToast('Article link copied to clipboard!');
            } else {
                showToast('Article link ready to share!');
            }
        }

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            if(emailInput && emailInput.value) {
                showToast(`Thank you for subscribing! A 15% off coupon has been sent to ${emailInput.value}.`);
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
