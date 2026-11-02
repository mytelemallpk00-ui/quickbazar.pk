@extends('layouts.app')

@section('title', 'Professional Size & Specification Guide — Beautage Flagship Store')

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
                <span class="text-beautage-purple font-bold">Size, Volume & Specification Guide</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-2xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        PRECISION MEASUREMENT & FIT SPECIFICATIONS
                    </span>
                    <h1 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-bold text-white tracking-tight">Beautage Size & Specification Guide</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Find exact volume dimensions for luxury skincare elixirs, ergonomic specs for LED photon masks & microcurrent wands, and apparel fit guides for our celebrity robes & spa wear.
                    </p>
                </div>
                <div class="relative z-10 w-full md:w-auto">
                    <div class="bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl text-center shadow-lg">
                        <span class="text-2xl sm:text-3xl font-black text-amber-300 block">100%</span>
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-bold">Accurate Specs</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. INTERACTIVE PRODUCT TYPE SELECTOR TABS -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-10">
            <div class="flex items-center justify-center gap-2 sm:gap-4 overflow-x-auto no-scrollbar py-2">
                <button onclick="switchTab('skincare')" id="tab-skincare" class="tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-beautage-purple text-white shadow-md flex items-center gap-2 shrink-0">
                    <i data-lucide="droplet" class="w-4 h-4 text-beautage-pink"></i> Skincare & Serums
                </button>
                <button onclick="switchTab('tech')" id="tab-tech" class="tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-white text-slate-700 hover:bg-purple-50 border border-purple-200 flex items-center gap-2 shrink-0">
                    <i data-lucide="zap" class="w-4 h-4 text-purple-600"></i> Beauty Tech Devices
                </button>
                <button onclick="switchTab('apparel')" id="tab-apparel" class="tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-white text-slate-700 hover:bg-purple-50 border border-purple-200 flex items-center gap-2 shrink-0">
                    <i data-lucide="shirt" class="w-4 h-4 text-amber-500"></i> Luxury Robes & Apparel
                </button>
                <button onclick="switchTab('hair')" id="tab-hair" class="tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-white text-slate-700 hover:bg-purple-50 border border-purple-200 flex items-center gap-2 shrink-0">
                    <i data-lucide="scissors" class="w-4 h-4 text-rose-500"></i> Hair Styling Barrels
                </button>
            </div>
        </section>

        <!-- 3. DYNAMIC CONTENT PANELS -->

        <!-- A. SKINCARE & SERUMS PANEL -->
        <div id="panel-skincare" class="content-panel w-full px-4 sm:px-12 xl:px-20 mb-16 space-y-8">
            <div class="bg-white rounded-3xl border border-purple-100 p-6 sm:p-10 shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-purple-100 pb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest bg-pink-50 px-3 py-1 rounded-full border border-pink-200">Volume & Usage Guide</span>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple mt-1">Skincare Bottle Capacity & Application Chart</h2>
                    </div>
                    <div class="text-xs text-slate-500 bg-purple-50 px-4 py-2 rounded-xl font-medium">
                        Standard daily application: 3-4 drops per session
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-purple-900 text-white rounded-xl">
                                <th class="p-3.5 rounded-l-xl font-bold">Product Category</th>
                                <th class="p-3.5 font-bold">Standard Volume (ml / oz)</th>
                                <th class="p-3.5 font-bold">Estimated Duration</th>
                                <th class="p-3.5 font-bold">Dispenser Type</th>
                                <th class="p-3.5 rounded-r-xl font-bold">Recommended Dosage</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100 text-slate-700 font-medium">
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple flex items-center gap-2"><i data-lucide="droplet" class="w-4 h-4 text-beautage-pink"></i> Botanical Youth Serum</td>
                                <td class="p-3.5 font-mono">30 ml (1.0 fl. oz)</td>
                                <td class="p-3.5">60 - 75 Days (Daily AM/PM)</td>
                                <td class="p-3.5">Glass Pipette Dropper</td>
                                <td class="p-3.5">3-4 drops per face/neck</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple flex items-center gap-2"><i data-lucide="droplet" class="w-4 h-4 text-amber-500"></i> Hyaluronic Acid Hydration Boost</td>
                                <td class="p-3.5 font-mono">50 ml (1.7 fl. oz)</td>
                                <td class="p-3.5">45 - 60 Days</td>
                                <td class="p-3.5">Airless Pump Bottle</td>
                                <td class="p-3.5">1 full pump</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple flex items-center gap-2"><i data-lucide="droplet" class="w-4 h-4 text-purple-600"></i> Gold Infused Eye Elixir</td>
                                <td class="p-3.5 font-mono">15 ml (0.5 fl. oz)</td>
                                <td class="p-3.5">90 Days</td>
                                <td class="p-3.5">Precision Needle-Nose Tip</td>
                                <td class="p-3.5">1 tiny drop for both eyes</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple flex items-center gap-2"><i data-lucide="droplet" class="w-4 h-4 text-emerald-500"></i> Collagen Boosting Night Cream</td>
                                <td class="p-3.5 font-mono">60 g (2.1 oz)</td>
                                <td class="p-3.5">60 Days</td>
                                <td class="p-3.5">Luxury Jar with Spatula</td>
                                <td class="p-3.5">Dime-sized amount</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- B. BEAUTY TECH DEVICES PANEL -->
        <div id="panel-tech" class="content-panel w-full px-4 sm:px-12 xl:px-20 mb-16 space-y-8 hidden">
            <div class="bg-white rounded-3xl border border-purple-100 p-6 sm:p-10 shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-purple-100 pb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-purple-600 uppercase tracking-widest bg-purple-50 px-3 py-1 rounded-full border border-purple-200">Ergonomic Fit & Dimensions</span>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple mt-1">Beauty Device Ergonomic Specifications</h2>
                    </div>
                    <div class="text-xs text-slate-500 bg-purple-50 px-4 py-2 rounded-xl font-medium">
                        Universal adjustable head & face contours
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-600 text-white flex items-center justify-center font-bold">
                            <i data-lucide="zap" class="w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold text-beautage-purple text-base">Lumina Pro 7-Spectrum Mask</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Medical-grade silicone contour mask with adjustable medical head strap. Fits all adult facial structures securely with zero pressure on eyes.
                        </p>
                        <div class="pt-2 text-[11px] font-mono font-bold text-purple-700">
                            Dimensions: 210mm × 165mm × 120mm<br>Weight: 280 grams
                        </div>
                    </div>

                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-beautage-pink text-white flex items-center justify-center font-bold">
                            <i data-lucide="cpu" class="w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold text-beautage-purple text-base">Sculpting Microcurrent Wand</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dual-sphere ergonomic titanium wand engineered for precise jawline, cheekbone, and brow contouring with 3 intensity levels.
                        </p>
                        <div class="pt-2 text-[11px] font-mono font-bold text-purple-700">
                            Dimensions: 145mm × 45mm × 30mm<br>Weight: 110 grams
                        </div>
                    </div>

                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center font-bold">
                            <i data-lucide="sparkles" class="w-5 h-5"></i>
                        </div>
                        <h3 class="font-bold text-beautage-purple text-base">Thermal Cryo Roller</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Dual-sided thermal heating (42°C) and cryo-cooling (6°C) aluminum alloy rolling head with comfortable anti-slip silicone grip.
                        </p>
                        <div class="pt-2 text-[11px] font-mono font-bold text-purple-700">
                            Dimensions: 170mm × 65mm × 40mm<br>Weight: 195 grams
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- C. LUXURY ROBES & APPAREL PANEL -->
        <div id="panel-apparel" class="content-panel w-full px-4 sm:px-12 xl:px-20 mb-16 space-y-8 hidden">
            <div class="bg-white rounded-3xl border border-purple-100 p-6 sm:p-10 shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-purple-100 pb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-amber-600 uppercase tracking-widest bg-amber-50 px-3 py-1 rounded-full border border-amber-200">Apparel & Spa Wear Sizing</span>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple mt-1">Celebrity Robe & Lounge Wear Size Chart</h2>
                    </div>
                    <div class="flex items-center gap-2">
                        <button onclick="setUnit('cm')" id="unit-cm" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-beautage-purple text-white transition">CM</button>
                        <button onclick="setUnit('inch')" id="unit-inch" class="px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-slate-700 transition">Inches</button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="bg-purple-900 text-white rounded-xl">
                                <th class="p-3.5 rounded-l-xl font-bold">Size</th>
                                <th class="p-3.5 font-bold">Bust / Chest</th>
                                <th class="p-3.5 font-bold">Waist</th>
                                <th class="p-3.5 font-bold">Hips</th>
                                <th class="p-3.5 rounded-r-xl font-bold">Recommended Fit Style</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-purple-100 text-slate-700 font-medium" id="apparel-table-body">
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple">S (Small)</td>
                                <td class="p-3.5 font-mono data-val" data-cm="34 - 36 cm" data-inch="34 - 36 in">34 - 36 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="26 - 28 cm" data-inch="26 - 28 in">26 - 28 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="36 - 38 cm" data-inch="36 - 38 in">36 - 38 cm</td>
                                <td class="p-3.5 text-beautage-pink font-semibold">Slim / Tailored Fit</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple">M (Medium)</td>
                                <td class="p-3.5 font-mono data-val" data-cm="38 - 40 cm" data-inch="38 - 40 in">38 - 40 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="30 - 32 cm" data-inch="30 - 32 in">30 - 32 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="40 - 42 cm" data-inch="40 - 42 in">40 - 42 cm</td>
                                <td class="p-3.5 text-beautage-purple font-semibold">Regular Comfort Fit</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple">L (Large)</td>
                                <td class="p-3.5 font-mono data-val" data-cm="42 - 44 cm" data-inch="42 - 44 in">42 - 44 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="34 - 36 cm" data-inch="34 - 36 in">34 - 36 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="44 - 46 cm" data-inch="44 - 46 in">44 - 46 cm</td>
                                <td class="p-3.5 text-amber-600 font-semibold">Relaxed Oversized Fit</td>
                            </tr>
                            <tr class="hover:bg-purple-50/50 transition">
                                <td class="p-3.5 font-bold text-beautage-purple">XL (Extra Large)</td>
                                <td class="p-3.5 font-mono data-val" data-cm="46 - 48 cm" data-inch="46 - 48 in">46 - 48 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="38 - 40 cm" data-inch="38 - 40 in">38 - 40 cm</td>
                                <td class="p-3.5 font-mono data-val" data-cm="48 - 50 cm" data-inch="48 - 50 in">48 - 50 cm</td>
                                <td class="p-3.5 text-emerald-600 font-semibold">Loose Spa Fit</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- D. HAIR STYLING BARRELS PANEL -->
        <div id="panel-hair" class="content-panel w-full px-4 sm:px-12 xl:px-20 mb-16 space-y-8 hidden">
            <div class="bg-white rounded-3xl border border-purple-100 p-6 sm:p-10 shadow-xl space-y-6">
                <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 border-b border-purple-100 pb-6">
                    <div>
                        <span class="text-[10px] font-extrabold text-rose-600 uppercase tracking-widest bg-rose-50 px-3 py-1 rounded-full border border-rose-200">Hair Tool Barrel Guide</span>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-beautage-purple mt-1">Hair Styler Barrel Sizes & Curls</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-bold font-mono">25mm</div>
                        <h3 class="font-bold text-beautage-purple text-base">Tight Defining Barrel</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Ideal for creating bouncy spiral curls, defined waves, and resilient styling on short to medium hair lengths.
                        </p>
                    </div>

                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-beautage-pink text-white flex items-center justify-center font-bold font-mono">32mm</div>
                        <h3 class="font-bold text-beautage-purple text-base">Voluminous Beach Waves</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            The celebrity favorite for effortless, soft voluminous waves and everyday salon-quality blowouts.
                        </p>
                    </div>

                    <div class="bg-purple-50/60 p-6 rounded-2xl border border-purple-100 space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-purple-800 text-white flex items-center justify-center font-bold font-mono">40mm</div>
                        <h3 class="font-bold text-beautage-purple text-base">Smooth Blowout Volume</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Designed for long hair to add gorgeous bounce, root lift, and polished smoothness with zero frizz.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 4. FIT GUIDE SECTION (SLIM, REGULAR, OVERSIZED) -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-7xl mx-auto space-y-8">
                <div class="text-center max-w-2xl mx-auto space-y-2">
                    <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest">Understand Our Cuts</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Fit Guide Breakdown</h2>
                    <p class="text-xs text-slate-600">Choose the silhouette that matches your personal comfort and style.</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                        <span class="inline-block px-3 py-1 rounded-full bg-pink-50 text-beautage-pink text-[10px] font-extrabold uppercase">Silhouette 01</span>
                        <h3 class="text-lg font-serif font-bold text-beautage-purple">Slim Fit</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Tailored closely to the body contours for a sleek, contemporary profile. Recommended if you prefer a sharp, body-hugging aesthetic.
                        </p>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                        <span class="inline-block px-3 py-1 rounded-full bg-purple-50 text-beautage-purple text-[10px] font-extrabold uppercase">Silhouette 02</span>
                        <h3 class="text-lg font-serif font-bold text-beautage-purple">Regular Fit</h3>
                        <p class="text-xs text-slate-600 leading-relaxed">
                            Standard comfort cut with balanced breathing room through the chest, waist, and sleeves. True to standard Pakistan sizing.
                        </p>
                    </div>

                    <div class="bg-white rounded-3xl p-6 border border-purple-100 shadow-xl space-y-4">
                        <span class="inline-block px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase">Silhouette 03</span>
                        <h3 class="text-lg font-serif font-bold text-beautage-purple">Oversized Lounge</h3>
                        <p class="text-xs text-slate-600 leading-relaxed>
                            Deliberately generous drop-shoulder design engineered for ultimate relaxation and supreme spa lounging comfort.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- 5. FREQUENTLY ASKED QUESTIONS (FAQ) -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-4xl mx-auto space-y-6">
                <div class="text-center space-y-2">
                    <span class="text-[10px] font-extrabold text-beautage-pink uppercase tracking-widest">Expert Assistance</span>
                    <h2 class="text-2xl sm:text-3xl font-serif font-bold text-beautage-purple">Size & Fit FAQs</h2>
                </div>

                <div class="space-y-4">
                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>Should I size up for beauty tech devices?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            No, all Beautage beauty devices such as the Lumina Pro Mask feature adjustable straps and universal ergonomic contours designed to fit all adult face shapes.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>Are the clothing sizes true to standard Pakistan retail?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            Yes, our robes and lounge apparel adhere strictly to standard regional measurements. If you prefer a looser spa fit, we recommend ordering one size up.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl border border-purple-100 p-6 shadow-sm">
                        <h3 class="text-sm font-bold text-beautage-purple flex items-center justify-between cursor-pointer" onclick="toggleFaq(this)">
                            <span>What if my item doesn't fit correctly?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-beautage-pink transition-transform"></i>
                        </h3>
                        <p class="text-xs text-slate-600 mt-3 leading-relaxed">
                            We offer hassle-free size exchanges within 30 days across Pakistan. Contact our helpline at +92 300 1234567 to initiate an instant exchange.
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
@verbatim
<script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
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

        function switchTab(tabName) {
            // Hide all panels
            document.querySelectorAll('.content-panel').forEach(p => p.classList.add('hidden'));
            // Show requested panel
            document.getElementById('panel-' + tabName).classList.remove('hidden');

            // Reset tab buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.className = 'tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-white text-slate-700 hover:bg-purple-50 border border-purple-200 flex items-center gap-2 shrink-0';
            });

            // Highlight active tab button
            const activeBtn = document.getElementById('tab-' + tabName);
            activeBtn.className = 'tab-btn px-5 py-3 rounded-2xl text-xs font-bold uppercase tracking-wider transition bg-beautage-purple text-white shadow-md flex items-center gap-2 shrink-0';

            showToast(`Switched to ${tabName.toUpperCase()} specifications guide.`);
        }

        function setUnit(unitType) {
            const btnCm = document.getElementById('unit-cm');
            const btnInch = document.getElementById('unit-inch');

            if(unitType === 'cm') {
                btnCm.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-beautage-purple text-white transition';
                btnInch.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-slate-700 transition';
            } else {
                btnInch.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-beautage-purple text-white transition';
                btnCm.className = 'px-3 py-1.5 rounded-lg text-xs font-bold bg-purple-100 text-slate-700 transition';
            }

            document.querySelectorAll('.data-val').forEach(td => {
                td.innerText = td.getAttribute('data-' + unitType);
            });

            showToast(`Converted measurements to ${unitType.toUpperCase()}.`);
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
    </script>
@endverbatim
@endpush
