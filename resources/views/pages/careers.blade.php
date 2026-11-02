@extends('layouts.app')

@section('title', 'Careers & Opportunities — Beautage Flagship Store')

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
                <span class="text-beautage-purple font-bold">Careers & Opportunities</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-950 via-beautage-purple to-purple-900 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        SHAPE THE FUTURE OF BEAUTY
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Build Your Career at the Intersection of Luxury & Tech</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Join an elite team of innovators, beauty experts, engineers, and brand strategists dedicated to redefining skincare, wellness, and experiential retail.
                    </p>
                </div>
                <div class="relative z-10 flex flex-col sm:flex-row gap-3">
                    <a href="#open-positions" class="px-6 py-3.5 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-bold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-pink-500/30 text-center">
                        View Openings
                    </a>
                    <a href="#culture" class="px-6 py-3.5 bg-white/10 hover:bg-white/25 backdrop-blur-md text-white font-bold text-xs uppercase tracking-wider rounded-xl transition border border-white/20 text-center">
                        Our Culture
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. CULTURE & VALUES HIGHLIGHTS -->
        <section id="culture" class="w-full px-4 sm:px-12 xl:px-20 mb-14">
            <div class="text-center max-w-2xl mx-auto mb-10 space-y-2">
                <span class="text-[10px] uppercase font-extrabold tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200">Why Join Beautage</span>
                <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Where Passion Meets Purpose</h2>
                <p class="text-xs sm:text-sm text-slate-600">We cultivate a culture of excellence, holistic wellness, and continuous personal growth.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <div class="bg-white rounded-3xl border border-purple-100 shadow-sm p-6 sm:p-8 space-y-4 hover:border-beautage-pink transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center font-bold">
                        <i data-lucide="sparkles" class="w-6 h-6 text-beautage-pink"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-beautage-purple">Uncompromising Innovation</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Work with cutting-edge beauty devices, organic botanical formulations, and omnichannel digital retail technologies that set global benchmarks.
                    </p>
                </div>

                <div class="bg-white rounded-3xl border border-purple-100 shadow-sm p-6 sm:p-8 space-y-4 hover:border-beautage-pink transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center font-bold">
                        <i data-lucide="heart" class="w-6 h-6 text-beautage-pink"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-beautage-purple">Holistic Well-being</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Enjoy comprehensive health coverage, mental wellness stipends, complimentary monthly flagship skincare boxes, and flexible hybrid work schedules.
                    </p>
                </div>

                <div class="bg-white rounded-3xl border border-purple-100 shadow-sm p-6 sm:p-8 space-y-4 hover:border-beautage-pink transition-all">
                    <div class="w-12 h-12 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center font-bold">
                        <i data-lucide="trending-up" class="w-6 h-6 text-beautage-pink"></i>
                    </div>
                    <h3 class="text-lg font-serif font-bold text-beautage-purple">Accelerated Growth</h3>
                    <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                        Thrive in a high-growth environment with dedicated mentorship programs, leadership pathways, and continuous professional development grants.
                    </p>
                </div>

            </div>
        </section>

        <!-- 3. OPEN POSITIONS WITH FILTER & SEARCH -->
        <section id="open-positions" class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8 pb-4 border-b border-purple-100">
                <div>
                    <span class="text-[10px] uppercase font-extrabold tracking-widest text-beautage-pink">Join Our Team</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Current Open Positions</h2>
                </div>
                
                <!-- FILTER TABS & SEARCH -->
                <div class="flex flex-wrap items-center gap-2 w-full md:w-auto">
                    <div class="relative flex-1 sm:w-64">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <i data-lucide="search" class="w-4 h-4"></i>
                        </span>
                        <input type="text" id="job-search" placeholder="Search roles (e.g. Manager)..." 
                               oninput="filterJobs()"
                               class="w-full pl-9 pr-4 py-2.5 bg-white border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                    </div>
                    <select id="dept-filter" onchange="filterJobs()" class="py-2.5 px-4 bg-white border border-purple-200 rounded-xl text-xs font-bold text-beautage-purple focus:outline-none focus:border-beautage-purple cursor-pointer">
                        <option value="all">All Departments</option>
                        <option value="Retail">Retail & Client Experience</option>
                        <option value="Corporate">Corporate & Marketing</option>
                        <option value="Digital">Digital & E-Commerce</option>
                        <option value="Operations">Operations & Supply</option>
                    </select>
                </div>
            </div>

            <!-- JOB LISTINGS CONTAINER -->
            <div id="job-list-container" class="space-y-4">
                
                <!-- Job Item 1 -->
                <div class="job-card bg-white rounded-3xl border border-purple-100 p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-xl hover:border-beautage-pink transition-all duration-300" data-dept="Retail" data-title="Flagship Store Assistant Manager">
                    <div class="space-y-2 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 bg-purple-50 text-beautage-purple rounded-full text-[10px] uppercase tracking-wider font-extrabold">Retail & Client Experience</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] uppercase tracking-wider font-extrabold">Full-Time</span>
                            <span class="text-xs text-slate-400 font-medium">Flagship Store (Lahore / Karachi)</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Flagship Store Assistant Manager</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Lead our luxury customer experience advisors, oversee VIP skincare consultations, and drive flagship retail sales excellence. Minimum 3+ years in luxury retail.
                        </p>
                    </div>
                    <button onclick="openApplyModal('Flagship Store Assistant Manager', 'Retail & Client Experience')" class="w-full md:w-auto px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/20 shrink-0 text-center">
                        Apply Now
                    </button>
                </div>

                <!-- Job Item 2 -->
                <div class="job-card bg-white rounded-3xl border border-purple-100 p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-xl hover:border-beautage-pink transition-all duration-300" data-dept="Corporate" data-title="Senior Brand & Influencer Marketing Specialist">
                    <div class="space-y-2 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 bg-pink-50 text-beautage-pink rounded-full text-[10px] uppercase tracking-wider font-extrabold">Corporate & Marketing</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] uppercase tracking-wider font-extrabold">Full-Time</span>
                            <span class="text-xs text-slate-400 font-medium">Headquarters / Hybrid</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Senior Brand & Influencer Marketing Specialist</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Manage celebrity endorsements, high-profile beauty influencer partnerships, and experiential PR campaigns across South Asia and international markets.
                        </p>
                    </div>
                    <button onclick="openApplyModal('Senior Brand & Influencer Marketing Specialist', 'Corporate & Marketing')" class="w-full md:w-auto px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/20 shrink-0 text-center">
                        Apply Now
                    </button>
                </div>

                <!-- Job Item 3 -->
                <div class="job-card bg-white rounded-3xl border border-purple-100 p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-xl hover:border-beautage-pink transition-all duration-300" data-dept="Digital" data-title="E-Commerce Full-Stack Growth Engineer">
                    <div class="space-y-2 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 bg-purple-50 text-beautage-purple rounded-full text-[10px] uppercase tracking-wider font-extrabold">Digital & E-Commerce</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] uppercase tracking-wider font-extrabold">Remote / Hybrid</span>
                            <span class="text-xs text-slate-400 font-medium">Global Tech Team</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">E-Commerce Full-Stack Growth Engineer</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Optimize our flagship web application, lightning-fast product filtering engines, secure checkout workflows, and immersive 3D product previews.
                        </p>
                    </div>
                    <button onclick="openApplyModal('E-Commerce Full-Stack Growth Engineer', 'Digital & E-Commerce')" class="w-full md:w-auto px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/20 shrink-0 text-center">
                        Apply Now
                    </button>
                </div>

                <!-- Job Item 4 -->
                <div class="job-card bg-white rounded-3xl border border-purple-100 p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 hover:shadow-xl hover:border-beautage-pink transition-all duration-300" data-dept="Operations" data-title="Global Supply Chain & Logistics Quality Lead">
                    <div class="space-y-2 max-w-2xl">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] uppercase tracking-wider font-extrabold">Operations & Supply</span>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[10px] uppercase tracking-wider font-extrabold">Full-Time</span>
                            <span class="text-xs text-slate-400 font-medium">Logistics Hub</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-serif font-bold text-beautage-purple">Global Supply Chain & Logistics Quality Lead</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Ensure pristine warehousing, temperature-controlled botanical serum storage, and lightning-fast express dispatch for worldwide orders.
                        </p>
                    </div>
                    <button onclick="openApplyModal('Global Supply Chain & Logistics Quality Lead', 'Operations & Supply')" class="w-full md:w-auto px-6 py-3 bg-beautage-pink hover:bg-beautage-pinkHover text-white text-xs font-extrabold uppercase tracking-wider rounded-xl transition shadow-md shadow-pink-500/20 shrink-0 text-center">
                        Apply Now
                    </button>
                </div>

            </div>
        </section>

    </main>
@endsection

@section('below_main')
<!-- INTERACTIVE JOB APPLICATION MODAL -->
    <div id="apply-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-beautage-purpleDark/60 backdrop-blur-sm opacity-0 pointer-events-none transition-all duration-300">
        <div id="apply-modal-panel" class="bg-white rounded-3xl border border-purple-100 shadow-2xl max-w-lg w-full p-6 sm:p-8 transform scale-95 transition-all duration-300 relative max-h-[90vh] overflow-y-auto">
            
            <button onclick="closeApplyModal()" class="absolute top-6 right-6 text-slate-400 hover:text-beautage-purple p-1 rounded-full bg-purple-50 transition">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>

            <div class="space-y-1 mb-6">
                <span id="modal-dept" class="text-[10px] uppercase font-extrabold tracking-widest text-beautage-pink bg-pink-50 px-2.5 py-1 rounded-full border border-pink-200 inline-block">Department</span>
                <h3 id="modal-role-title" class="text-xl font-serif font-bold text-beautage-purple mt-2">Apply for Role</h3>
                <p class="text-xs text-slate-500">Please submit your credentials to join the Beautage Flagship talent network.</p>
            </div>

            <form id="job-application-form" onsubmit="submitJobApplication(event)" class="space-y-4">
                <div>
                    <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" id="applicant-name" required placeholder="e.g. Alishba Khan" 
                           class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Email Address</label>
                        <input type="email" id="applicant-email" required placeholder="alishba@gmail.com" 
                               class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Phone Number</label>
                        <input type="tel" id="applicant-phone" required placeholder="+92 300 1234567" 
                               class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">LinkedIn / Portfolio URL</label>
                    <input type="url" id="applicant-portfolio" required placeholder="https://linkedin.com/in/username" 
                           class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple">
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-beautage-purple uppercase tracking-wider mb-1">Cover Note / Why Beautage?</label>
                    <textarea id="applicant-cover" required rows="3" placeholder="Tell us why you're a great fit for our luxury flagship store..." 
                              class="w-full py-3 px-4 bg-purple-50/60 border border-purple-200 rounded-xl text-xs font-medium text-slate-800 focus:outline-none focus:border-beautage-purple"></textarea>
                </div>

                <button type="submit" class="w-full py-3.5 bg-beautage-purple hover:bg-beautage-purpleDark text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-purple-900/30 flex items-center justify-center gap-2">
                    <span>Submit Application</span>
                    <i data-lucide="send" class="w-4 h-4"></i>
                </button>
            </form>

        </div>
    </div>
@endsection

@push('scripts')
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

        function filterJobs() {
            const query = document.getElementById('job-search').value.toLowerCase().trim();
            const dept = document.getElementById('dept-filter').value;
            const cards = document.querySelectorAll('.job-card');

            cards.forEach(card => {
                const title = card.getAttribute('data-title').toLowerCase();
                const cardDept = card.getAttribute('data-dept');
                const matchesQuery = title.includes(query);
                const matchesDept = (dept === 'all' || cardDept === dept);

                if(matchesQuery && matchesDept) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function openApplyModal(roleTitle, dept) {
            const modal = document.getElementById('apply-modal');
            const panel = document.getElementById('apply-modal-panel');
            
            document.getElementById('modal-role-title').innerText = roleTitle;
            document.getElementById('modal-dept').innerText = dept;

            modal.classList.remove('opacity-0', 'pointer-events-none');
            panel.classList.remove('scale-95');
        }

        function closeApplyModal() {
            const modal = document.getElementById('apply-modal');
            const panel = document.getElementById('apply-modal-panel');

            modal.classList.add('opacity-0', 'pointer-events-none');
            panel.classList.add('scale-95');
        }

        function submitJobApplication(event) {
            event.preventDefault();
            const name = document.getElementById('applicant-name').value.trim();
            const role = document.getElementById('modal-role-title').innerText;

            showToast(`Thank you ${name}! Your application for "${role}" has been successfully received.`);
            closeApplyModal();
            document.getElementById('job-application-form').reset();
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

        function handleNewsletterSubmit(event) {
            event.preventDefault();
            const emailInput = document.getElementById('newsletter-email');
            const email = emailInput.value.trim();
            if(email) {
                showToast(`Thank you for subscribing! A 15% off coupon has been sent to ${email}.`);
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
                const el = document.getElementById('announcement-timer');
                if(el) el.innerText = `${h}:${m}:${s}`;
            }, 1000);
        }
    </script>
@endverbatim
@endpush
