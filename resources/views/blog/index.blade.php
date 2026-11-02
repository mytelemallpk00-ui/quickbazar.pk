@extends('layouts.app')

@section('title', 'Flagship Blog & Journal — Beautage Store')

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
                <span class="text-beautage-purple font-bold">Flagship Blog & Journal</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        BEAUTAGE EXPERT JOURNAL
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Science, Luxury & Skincare Insights</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Discover expert guides on photon light therapy, botanical organic elixirs, and celebrity skincare routines directly from our dermatological research team.
                    </p>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-center shadow-lg">
                        <span id="article-count-badge" class="text-2xl sm:text-3xl font-black text-amber-300 block">6</span>
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-bold">Expert Articles</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. FEATURED HERO ARTICLE ("The Science of 7-Spectrum LED Light Therapy...") -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-10">
            <div onclick="openArticleModal('featured')" class="bg-white rounded-3xl border-2 border-purple-200 hover:border-beautage-pink shadow-xl hover:shadow-2xl transition-all duration-300 overflow-hidden cursor-pointer group grid grid-cols-1 lg:grid-cols-12 items-center">
                <div class="lg:col-span-7 relative h-72 sm:h-96 overflow-hidden bg-purple-50">
                    <img src="https://images.unsplash.com/photo-1598440947619-2c35fc9aa908?auto=format&fit=crop&q=80&w=1000" alt="Featured Article LED Mask" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null; this.src='https://placehold.co/800x600/5E1F70/FFFFFF?text=LED+Light+Therapy';">
                    <span class="absolute top-4 left-4 bg-beautage-pink text-white text-xs font-extrabold px-3.5 py-1.5 rounded-full uppercase tracking-wider shadow-lg">
                        FEATURED COVER STORY
                    </span>
                </div>
                <div class="lg:col-span-5 p-6 sm:p-10 space-y-4">
                    <div class="flex items-center gap-3 text-xs font-bold text-beautage-purple">
                        <span class="px-3 py-1 bg-purple-50 rounded-full text-beautage-pink uppercase tracking-widest text-[10px]">Beauty Tech</span>
                        <span class="text-slate-400">•</span>
                        <span class="text-slate-500 flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> 6 min read</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl lg:text-3xl font-serif font-bold text-beautage-purple group-hover:text-beautage-pink transition">
                        The Science of 7-Spectrum LED Light Therapy for Cellular Rejuvenation
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Explore how NASA-originated photon wavelength technology penetrates dermal layers to stimulate collagen synthesis, eliminate acne-causing bacteria, and reverse oxidative skin stress.
                    </p>
                    <div class="flex items-center justify-between pt-4 border-t border-purple-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-beautage-purple text-white font-bold flex items-center justify-center text-xs">Dr</div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-800">Dr. Aliza Thorne</h4>
                                <span class="text-[10px] text-slate-400">Chief Dermatological Researcher</span>
                            </div>
                        </div>
                        <span class="text-xs font-bold text-beautage-pink flex items-center gap-1 group-hover:translate-x-1 transition">
                            Read Story <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
            </div>
        </section>

        <!-- 3. SEARCH & CATEGORY FILTER TABS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-8">
            <div class="bg-white rounded-2xl p-4 sm:p-6 border border-purple-100 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
                
                <!-- Category Filter Tabs -->
                <div class="flex items-center gap-2 overflow-x-auto no-scrollbar w-full md:w-auto pb-2 md:pb-0">
                    <button onclick="filterArticles('All', this)" class="blog-tab px-4 py-2 rounded-xl bg-beautage-purple text-white text-xs font-bold uppercase tracking-wider transition shrink-0 shadow-sm">All Articles</button>
                    <button onclick="filterArticles('Skincare Secrets', this)" class="blog-tab px-4 py-2 rounded-xl bg-purple-50 text-slate-700 hover:bg-purple-100 text-xs font-bold uppercase tracking-wider transition shrink-0">Skincare Secrets</button>
                    <button onclick="filterArticles('Beauty Tech', this)" class="blog-tab px-4 py-2 rounded-xl bg-purple-50 text-slate-700 hover:bg-purple-100 text-xs font-bold uppercase tracking-wider transition shrink-0">Beauty Tech</button>
                    <button onclick="filterArticles('Hair Care & Wellness', this)" class="blog-tab px-4 py-2 rounded-xl bg-purple-50 text-slate-700 hover:bg-purple-100 text-xs font-bold uppercase tracking-wider transition shrink-0">Hair Care & Wellness</button>
                    <button onclick="filterArticles('Celebrity Routines', this)" class="blog-tab px-4 py-2 rounded-xl bg-purple-50 text-slate-700 hover:bg-purple-100 text-xs font-bold uppercase tracking-wider transition shrink-0">Celebrity Routines</button>
                </div>

                <!-- Search Bar -->
                <div class="relative w-full md:w-72">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </span>
                    <input type="text" id="blog-search-input" oninput="searchArticles()" placeholder="Search articles..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-purple-50/60 border border-purple-200 rounded-xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple transition font-medium">
                </div>

            </div>
        </section>

        <!-- 4. GRID OF ENGAGING ARTICLES -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div id="blog-grid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>
        </section>

        <!-- 5. NEWSLETTER SUBSCRIPTION BOX -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-12">
            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 border border-purple-600/70 p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8 text-white">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-xl space-y-2 relative z-10 text-center lg:text-left">
                    <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/40">
                        WEEKLY EXPERT DIGEST
                    </span>
                    <h3 class="text-2xl sm:text-3xl font-bold tracking-tight font-serif">Never Miss a Dermatological Breakthrough</h3>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed font-semibold">
                        Get weekly clinical skincare advice, product release notifications, and celebrity beauty routine tips delivered directly to your inbox.
                    </p>
                </div>
                <form id="blog-newsletter-form" onsubmit="handleBlogNewsletter(event)" class="w-full lg:w-auto relative z-10 flex flex-col sm:flex-row items-center gap-3">
                    <div class="relative w-full sm:w-80">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-purple-200">
                            <i data-lucide="mail" class="w-4 h-4"></i>
                        </span>
                        <input type="email" id="blog-newsletter-email" required placeholder="Enter your email address..." 
                               class="w-full pl-10 pr-4 py-3.5 bg-purple-950 border border-purple-500 rounded-xl text-xs text-white placeholder-purple-300 focus:outline-none focus:border-emerald-400 transition shadow-inner font-medium">
                    </div>
                    <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-emerald-900/35 shrink-0 flex items-center justify-center gap-2">
                        <span>Subscribe</span>
                        <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>
        </section>

    </main>
@endsection

@section('below_main')
<!-- FULL ARTICLE READING MODAL -->
    <div id="article-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-beautage-purpleDark/50 backdrop-blur-md hidden">
        <div class="bg-white border border-purple-100 rounded-3xl max-w-3xl w-full max-h-[90vh] overflow-y-auto p-6 sm:p-10 relative shadow-2xl space-y-6">
            <button onclick="closeArticleModal()" class="absolute top-6 right-6 text-slate-400 hover:text-slate-600 p-2"><i data-lucide="x" class="w-6 h-6"></i></button>
            <div id="modal-article-content" class="space-y-6"></div>
        </div>
    </div>
@endsection

@push('scripts')
@include('partials.scripts.blog-articles')
@verbatim
<script>
        
        let currentCategory = 'All';
        let searchQuery = '';
        let cart = [];
        let wishlist = [];

        document.addEventListener('DOMContentLoaded', () => {
            renderBlogGrid();
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

        function filterArticles(category, btnElement) {
            currentCategory = category;
            document.querySelectorAll('.blog-tab').forEach(b => {
                b.className = 'blog-tab px-4 py-2 rounded-xl bg-purple-50 text-slate-700 hover:bg-purple-100 text-xs font-bold uppercase tracking-wider transition shrink-0';
            });
            btnElement.className = 'blog-tab px-4 py-2 rounded-xl bg-beautage-purple text-white text-xs font-bold uppercase tracking-wider transition shrink-0 shadow-sm';
            renderBlogGrid();
        }

        function searchArticles() {
            searchQuery = document.getElementById('blog-search-input').value.toLowerCase();
            renderBlogGrid();
        }

        function renderBlogGrid() {
            const container = document.getElementById('blog-grid');
            
            let filtered = articles.filter(a => a.id !== 'featured'); // Featured is shown in hero

            if(currentCategory !== 'All') {
                filtered = filtered.filter(a => a.category === currentCategory);
            }

            if(searchQuery) {
                filtered = filtered.filter(a => a.title.toLowerCase().includes(searchQuery) || a.snippet.toLowerCase().includes(searchQuery));
            }

            document.getElementById('article-count-badge').innerText = filtered.length + 1; // +1 for featured

            if(filtered.length === 0) {
                container.innerHTML = `<div class="col-span-full py-16 text-center text-slate-400 text-sm bg-white rounded-3xl border border-purple-100">No articles match your search criteria.</div>`;
                return;
            }

            container.innerHTML = filtered.map(a => `
                <div class="bg-white rounded-3xl border border-purple-100 hover:border-beautage-pink shadow-sm hover:shadow-2xl transition-all duration-300 overflow-hidden flex flex-col justify-between group">
                    <div class="relative aspect-video overflow-hidden bg-purple-50 cursor-pointer" onclick="openArticleModal('${a.id}')">
                        <img src="${a.image}" alt="${a.title}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" onerror="this.onerror=null; this.src='https://placehold.co/600x400/5E1F70/FFFFFF?text=Beautage+Journal';">
                        <span class="absolute top-3 left-3 bg-beautage-purple text-white text-[10px] font-extrabold px-3 py-1 rounded-full uppercase tracking-wider shadow">
                            ${a.category}
                        </span>
                    </div>

                    <div class="p-6 space-y-3 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-3 text-[11px] text-slate-400 font-semibold mb-2">
                                <span>${a.date}</span>
                                <span>•</span>
                                <span class="flex items-center gap-1"><i data-lucide="clock" class="w-3 h-3"></i> ${a.readTime}</span>
                            </div>
                            <h3 onclick="openArticleModal('${a.id}')" class="text-base sm:text-lg font-serif font-bold text-beautage-purple group-hover:text-beautage-pink transition cursor-pointer line-clamp-2">
                                ${a.title}
                            </h3>
                            <p class="text-xs text-slate-600 mt-2 line-clamp-3 leading-relaxed">${a.snippet}</p>
                        </div>

                        <div class="pt-4 border-t border-purple-50 flex items-center justify-between">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-purple-100 text-beautage-purple font-bold flex items-center justify-center text-[10px]">${a.authorAvatar}</div>
                                <div>
                                    <h4 class="text-xs font-bold text-slate-800">${a.author}</h4>
                                    <span class="text-[9px] text-slate-400 block">${a.authorRole}</span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <button onclick="likeArticle(event, '${a.id}')" class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-pink-50 text-beautage-pink hover:bg-pink-100 transition text-xs font-bold">
                                    <i data-lucide="heart" class="w-3.5 h-3.5 fill-current"></i> <span id="like-count-${a.id}">${a.likes}</span>
                                </button>
                                <button onclick="shareArticle(event, '${a.title}')" class="p-2 rounded-xl bg-purple-50 text-beautage-purple hover:bg-purple-100 transition" title="Share Article">
                                    <i data-lucide="share-2" class="w-3.5 h-3.5"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            lucide.createIcons();
        }

        function openArticleModal(articleId) {
            const article = articles.find(a => a.id == articleId);

            if(!article) return;

            if (article.slug) {
                window.location.href = `/blog/${article.slug}`;
                return;
            }

            const modal = document.getElementById('article-modal');
            const content = document.getElementById('modal-article-content');

            content.innerHTML = `
                <div class="flex items-center justify-between border-b border-purple-100 pb-4">
                    <span class="px-3 py-1 bg-purple-100 text-beautage-purple text-xs font-bold rounded-full uppercase">${article.category}</span>
                    <span class="text-xs text-slate-400">${article.date} • ${article.readTime}</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">${article.title}</h2>
                
                <div class="flex items-center gap-3 py-2 border-y border-purple-50">
                    <div class="w-10 h-10 rounded-full bg-beautage-purple text-white font-bold flex items-center justify-center text-xs">${article.authorAvatar}</div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-800">${article.author}</h4>
                        <span class="text-[10px] text-slate-400">${article.authorRole}</span>
                    </div>
                </div>

                <div class="rounded-2xl overflow-hidden aspect-video bg-purple-50">
                    <img src="${article.image}" class="w-full h-full object-cover">
                </div>

                <div class="space-y-4 pt-2">
                    ${article.content}
                </div>

                <div class="flex items-center justify-between pt-6 border-t border-purple-100">
                    <button onclick="likeArticle(event, '${article.id}')" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-pink-50 text-beautage-pink hover:bg-pink-100 transition text-xs font-bold">
                        <i data-lucide="heart" class="w-4 h-4 fill-current"></i> Like Article (<span id="modal-like-${article.id}">${article.likes}</span>)
                    </button>
                    <button onclick="shareArticle(event, '${article.title}')" class="flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-50 text-beautage-purple hover:bg-purple-100 transition text-xs font-bold">
                        <i data-lucide="share-2" class="w-4 h-4"></i> Share Story
                    </button>
                </div>
            `;
            modal.classList.remove('hidden');
            lucide.createIcons();
        }

        function closeArticleModal() {
            document.getElementById('article-modal').classList.add('hidden');
        }

        function likeArticle(event, id) {
            event.stopPropagation();
            const art = id === 'featured' ? null : articles.find(a => a.id == id);
            if(art) {
                art.likes += 1;
                const el = document.getElementById(`like-count-${id}`);
                const modalEl = document.getElementById(`modal-like-${id}`);
                if(el) el.innerText = art.likes;
                if(modalEl) modalEl.innerText = art.likes;
            }
            showToast('Thank you for liking this article!');
        }

        function shareArticle(event, title) {
            event.stopPropagation();
            if(navigator.clipboard) {
                navigator.clipboard.writeText(window.location.href);
                showToast(`Link copied for "${title}"!`);
            } else {
                showToast(`Article link ready to share!`);
            }
        }

        function handleBlogNewsletter(event) {
            event.preventDefault();
            const email = document.getElementById('blog-newsletter-email').value.trim();
            if(email) {
                showToast(`Successfully subscribed ${email} to Beautage Expert Journal!`);
                document.getElementById('blog-newsletter-email').value = '';
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
