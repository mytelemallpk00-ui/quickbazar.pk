@extends('layouts.app')

@section('title', 'Track Your Order — Quick Bazar Flagship Store')

@section('body_attrs')
class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col selection:bg-beautage-pink selection:text-white pb-36 md:pb-0 overflow-x-hidden"
@endsection

@section('content')
<main class="w-full flex-1 flex flex-col py-8">

        <!-- 1. BREADCRUMB & HERO BANNER -->
        <div class="w-full px-4 sm:px-12 xl:px-20 mb-8">
            <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500 whitespace-nowrap mb-4 overflow-x-auto no-scrollbar">
                <a href="{{ route('home') }}" class="hover:text-beautage-purple transition flex items-center gap-1">
                    <i data-lucide="home" class="w-3.5 h-3.5"></i> Home
                </a>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-purple-300"></i>
                <span class="text-beautage-purple font-bold">Track Your Order</span>
            </nav>

            <div class="rounded-3xl bg-gradient-to-r from-purple-900 via-beautage-purple to-purple-950 text-white p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col md:flex-row items-center justify-between gap-8">
                <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
                <div class="max-w-xl relative z-10 space-y-3 text-center md:text-left">
                    <span class="inline-block px-3.5 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-pink-500/30 text-pink-200 border border-pink-400/40">
                        EXPRESS CONCIERGE TRACKING
                    </span>
                    <h1 class="text-2xl sm:text-4xl font-serif font-bold text-white tracking-tight">Real-Time Shipment Status</h1>
                    <p class="text-xs sm:text-sm text-purple-100 leading-relaxed">
                        Enter your order tracking number or email address below to inspect live carrier progress, estimated delivery dates, and package dispatch updates.
                    </p>
                </div>
                <div class="relative z-10 flex items-center gap-4 bg-white/10 backdrop-blur-md border border-white/20 p-5 rounded-2xl shadow-lg">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <i data-lucide="truck" class="w-6 h-6"></i>
                    </div>
                    <div>
                        <span class="text-xs font-extrabold text-white block">Express Courier Partner</span>
                        <span class="text-[11px] text-purple-200">Leopards • TCS • DHL Global</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 2. TRACKING INPUT CARD SECTION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-10">
            <div class="max-w-3xl mx-auto bg-white rounded-3xl p-6 sm:p-10 border border-purple-100 shadow-2xl space-y-6">
                
                <div class="text-center space-y-1">
                    <h3 class="text-lg sm:text-xl font-bold text-beautage-purple font-serif">Lookup Your Package</h3>
                    <p class="text-xs text-slate-500">Provide your order ID (e.g. <span class="font-mono font-bold text-beautage-pink">BTG-89421</span>) or registered email.</p>
                </div>

                <form id="track-form" onsubmit="handleTrackOrder(event)" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-8 relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-purple-400">
                                <i data-lucide="package" class="w-4 h-4"></i>
                            </span>
                            <input type="text" id="tracking-input" required placeholder="Enter Order ID or Tracking Number (e.g. BTG-89421)" 
                                   class="w-full pl-11 pr-4 py-3.5 bg-purple-50/70 border border-purple-200 rounded-2xl text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:border-beautage-purple font-medium transition shadow-inner">
                        </div>
                        <button type="submit" class="sm:col-span-4 bg-beautage-pink hover:bg-beautage-pinkHover text-white font-extrabold text-xs uppercase tracking-wider py-3.5 px-6 rounded-2xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2">
                            <span>Track Package</span>
                            <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </button>
                    </div>
                </form>

                <!-- Quick Sample Lookup Pills -->
                <div class="pt-2 border-t border-purple-50 flex items-center justify-center gap-2 flex-wrap text-xs">
                    <span class="text-slate-400 font-semibold text-[11px]">Quick Demo Orders:</span>
                    <button onclick="autofillTracking('BTG-89421')" class="px-3 py-1 rounded-full bg-purple-50 hover:bg-purple-100 text-beautage-purple font-bold text-[11px] transition">BTG-89421 (In Transit)</button>
                    <button onclick="autofillTracking('BTG-74102')" class="px-3 py-1 rounded-full bg-purple-50 hover:bg-purple-100 text-beautage-purple font-bold text-[11px] transition">BTG-74102 (Delivered)</button>
                </div>

            </div>
        </section>

        <!-- 3. DYNAMIC ORDER STATUS RESULT CONTAINER (Hidden by default until search) -->
        <section id="tracking-result-section" class="w-full px-4 sm:px-12 xl:px-20 mb-16 hidden">
            <div class="max-w-4xl mx-auto bg-white rounded-3xl border border-purple-100 shadow-2xl overflow-hidden">
                
                <!-- Result Header Summary -->
                <div class="bg-gradient-to-r from-purple-900 to-beautage-purple text-white p-6 sm:p-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span id="result-badge" class="px-3 py-1 rounded-full text-[10px] uppercase font-black tracking-widest bg-amber-400 text-slate-900">IN TRANSIT</span>
                            <span class="text-xs text-purple-200 font-mono" id="result-order-id">Order #BTG-89421</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-serif font-bold text-white" id="result-product-title">Lumina Pro 7-Spectrum LED Mask</h2>
                    </div>
                    <div class="text-left sm:text-right">
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 block font-semibold">Estimated Delivery</span>
                        <span class="text-sm sm:text-base font-extrabold text-amber-300" id="result-delivery-date">Tomorrow, Oct 24 by 6:00 PM</span>
                    </div>
                </div>

                <!-- Detailed Status Body -->
                <div class="p-6 sm:p-10 space-y-8">
                    
                    <!-- Courier & Meta Info Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 rounded-2xl bg-purple-50/60 border border-purple-100 text-xs">
                        <div>
                            <span class="text-slate-400 block font-semibold">Courier Service</span>
                            <span class="font-bold text-beautage-purple mt-0.5 block" id="result-courier">Leopards Express (COD)</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Tracking Number</span>
                            <span class="font-mono font-bold text-slate-800 mt-0.5 block" id="result-tracking-no">LEO-99482103PK</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Shipping Destination</span>
                            <span class="font-bold text-slate-800 mt-0.5 block" id="result-destination">Bahawalpur, Pakistan</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block font-semibold">Total Amount</span>
                            <span class="font-extrabold text-beautage-pink mt-0.5 block" id="result-amount">$249.00</span>
                        </div>
                    </div>

                    <!-- Visual Progress Timeline Tracker -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-extrabold text-beautage-purple uppercase tracking-wider">Shipment Milestone Progress</h3>
                        
                        <div class="relative pl-6 sm:pl-8 space-y-6 before:absolute before:left-2.5 sm:before:left-3.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-purple-200" id="timeline-container">
                            <!-- Populated via JS -->
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="pt-6 border-t border-purple-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2 text-xs text-slate-500">
                            <i data-lucide="headphones" class="w-4 h-4 text-beautage-pink"></i>
                            <span>Need assistance with this shipment? <a href="tel:+18008886678" class="text-beautage-purple font-bold hover:underline">Contact Concierge</a></span>
                        </div>
                        <button onclick="resetTrackingView()" class="px-5 py-2.5 rounded-xl bg-purple-50 hover:bg-purple-100 text-beautage-purple font-bold text-xs transition">
                            Track Another Order
                        </button>
                    </div>

                </div>

            </div>
        </section>

        <!-- 4. FAQ / HELP ACCORDION SECTION -->
        <section class="w-full px-4 sm:px-12 xl:px-20 mb-16">
            <div class="max-w-3xl mx-auto space-y-6">
                <div class="text-center space-y-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-widest text-beautage-pink bg-pink-50 px-3 py-1 rounded-full border border-pink-200">Help & Support</span>
                    <h3 class="text-2xl font-serif font-bold text-beautage-purple">Frequently Asked Shipping Questions</h3>
                </div>

                <div class="space-y-3">
                    <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                        <h4 class="text-xs font-bold text-beautage-purple flex items-center justify-between cursor-pointer">
                            <span>When will my order ship after placing it?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            All verified flagship orders are processed within 24 hours. Express shipments are dispatched the same day if ordered before 3:00 PM PST.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                        <h4 class="text-xs font-bold text-beautage-purple flex items-center justify-between cursor-pointer">
                            <span>Do you offer Cash on Delivery (COD) across Pakistan?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Yes! Cash on Delivery is fully supported across all major cities and towns in Pakistan with verified secure inspection upon receipt.
                        </p>
                    </div>

                    <div class="bg-white rounded-2xl p-5 border border-purple-100 shadow-sm">
                        <h4 class="text-xs font-bold text-beautage-purple flex items-center justify-between cursor-pointer">
                            <span>What should I do if my tracking number isn't updating?</span>
                            <i data-lucide="chevron-down" class="w-4 h-4 text-slate-400"></i>
                        </h4>
                        <p class="text-xs text-slate-600 mt-2 leading-relaxed">
                            Courier scanning syncs every 6–12 hours. If your tracking status has not changed after 24 hours, our 24/7 care hotline is ready to assist you instantly.
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
<script>
const TRACK_ORDER_API = @json(route('orders.track'));
</script>
@verbatim
<script>
        const mockDatabase = {
            "BTG-89421": {
                orderId: "BTG-89421",
                title: "Lumina Pro 7-Spectrum LED Mask",
                status: "IN TRANSIT",
                badgeBg: "bg-amber-400 text-slate-900",
                deliveryDate: "Tomorrow, Oct 24 by 6:00 PM",
                courier: "Leopards Express (COD)",
                trackingNo: "LEO-99482103PK",
                destination: "Bahawalpur, Pakistan",
                amount: "$249.00",
                milestones: [
                    { title: "Order Confirmed & Verified", date: "October 21, 2026 - 10:15 AM", desc: "Your payment and order details have been successfully verified by Quick Bazar Concierge.", completed: true },
                    { title: "Dispatched from Flagship Hub", date: "October 22, 2026 - 02:30 PM", desc: "Package packed securely in tamper-evident branded box and handed over to Leopards Courier.", completed: true },
                    { title: "In Transit at Regional Sorting Facility", date: "October 23, 2026 - 08:00 AM", desc: "Shipment has arrived at destination sorting hub and is out for local delivery route.", completed: true },
                    { title: "Out for Final Delivery", date: "Expected Oct 24, 2026", desc: "Courier rider assigned for doorstep delivery with Cash on Delivery option.", completed: false }
                ]
            },
            "BTG-74102": {
                orderId: "BTG-74102",
                title: "Botanical Youth Renewal Serum + Ubtan Glow Mask",
                status: "DELIVERED",
                badgeBg: "bg-emerald-500 text-white",
                deliveryDate: "Delivered on Oct 21, 2026",
                courier: "TCS Express Courier",
                trackingNo: "TCS-44109283PK",
                destination: "Lahore, Pakistan",
                amount: "$154.00",
                milestones: [
                    { title: "Order Confirmed & Verified", date: "October 18, 2026 - 11:00 AM", desc: "Order details verified successfully.", completed: true },
                    { title: "Dispatched from Flagship Hub", date: "October 19, 2026 - 04:00 PM", desc: "Handed over to TCS courier service.", completed: true },
                    { title: "Out for Final Delivery", date: "October 21, 2026 - 09:30 AM", desc: "Out for delivery with courier agent.", completed: true },
                    { title: "Successfully Delivered", date: "October 21, 2026 - 02:14 PM", desc: "Package handed over to recipient. Thank you for shopping with Quick Bazar!", completed: true }
                ]
            }
        };

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

        function autofillTracking(orderId) {
            document.getElementById('tracking-input').value = orderId;
            handleTrackOrder(new Event('submit'));
        }

        async function handleTrackOrder(event) {
            event.preventDefault();
            const raw = document.getElementById('tracking-input').value.trim();
            const query = raw.toUpperCase();

            try {
                const response = await fetch(`${TRACK_ORDER_API}?q=${encodeURIComponent(raw)}`, {
                    headers: { 'Accept': 'application/json' },
                });
                const live = await response.json();
                if (response.ok && live.success && live.order) {
                    const o = live.order;
                    const data = {
                        orderId: o.order_number,
                        title: 'Quick Bazar order',
                        status: (o.tracking_number ? 'IN TRANSIT' : (o.fulfillment_status || 'PROCESSING')).toUpperCase(),
                        badgeBg: o.tracking_number ? 'bg-cyan-600 text-white' : 'bg-purple-600 text-white',
                        deliveryDate: o.shipping_city ? `Destination: ${o.shipping_city}` : 'Pakistan',
                        courier: o.courier || 'Assigned courier',
                        trackingNo: o.tracking_number || 'Pending booking',
                        destination: o.shipping_city || 'Pakistan',
                        amount: `Rs. ${Number(o.total || 0).toLocaleString()}`,
                        milestones: buildMilestones(o),
                    };
                    renderTrackingResult(data);
                    return;
                }
                if (!response.ok && live.message) {
                    showToast(live.message);
                    return;
                }
            } catch (e) {
                /* fall through to demo */
            }

            const data = mockDatabase[query] || {
                orderId: query,
                title: "Custom Flagship Order Package",
                status: "PROCESSING",
                badgeBg: "bg-purple-600 text-white",
                deliveryDate: "Estimated within 2-3 Business Days",
                courier: "Leopards Express",
                trackingNo: "LEO-CUSTOM-PK",
                destination: "Pakistan",
                amount: "$120.00",
                milestones: [
                    { title: "Order Received", date: "Just now", desc: "We have received your tracking inquiry and are syncing with courier logs.", completed: true },
                    { title: "Warehouse Preparation", date: "Pending", desc: "Items are undergoing quality assurance and packaging.", completed: false },
                    { title: "Courier Dispatch", date: "Upcoming", desc: "Handover to express delivery network.", completed: false }
                ]
            };

            renderTrackingResult(data);
        }

        function buildMilestones(o) {
            const steps = [
                { title: 'Order placed', date: 'Confirmed', desc: `Order ${o.order_number} received.`, completed: true },
                { title: 'Payment', date: o.payment_status, desc: 'Payment status from store.', completed: o.payment_status === 'paid' },
                { title: 'Courier booking', date: o.courier_booking_status || 'pending', desc: o.tracking_number ? `Tracking: ${o.tracking_number}` : 'Booking in progress.', completed: !!o.tracking_number },
                { title: 'Delivery', date: o.fulfillment_status, desc: 'Out for delivery / delivered.', completed: o.status === 'completed' },
            ];
            return steps;
        }

        function renderTrackingResult(data) {
            document.getElementById('result-badge').innerText = data.status;
            document.getElementById('result-badge').className = `px-3 py-1 rounded-full text-[10px] uppercase font-black tracking-widest ${data.badgeBg}`;
            document.getElementById('result-order-id').innerText = `Order #${data.orderId}`;
            document.getElementById('result-product-title').innerText = data.title;
            document.getElementById('result-delivery-date').innerText = data.deliveryDate;
            document.getElementById('result-courier').innerText = data.courier;
            document.getElementById('result-tracking-no').innerText = data.trackingNo;
            document.getElementById('result-destination').innerText = data.destination;
            document.getElementById('result-amount').innerText = data.amount;

            const timelineContainer = document.getElementById('timeline-container');
            timelineContainer.innerHTML = data.milestones.map((m, idx) => `
                <div class="relative pl-6 sm:pl-8">
                    <div class="absolute -left-[17px] sm:-left-[21px] top-0 w-6 h-6 rounded-full ${m.completed ? 'bg-emerald-500 text-white' : 'bg-purple-200 text-slate-500'} flex items-center justify-center text-xs font-bold shadow">
                        ${m.completed ? '✓' : idx + 1}
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 font-semibold block">${m.date}</span>
                        <h4 class="text-xs sm:text-sm font-bold text-beautage-purple mt-0.5">${m.title}</h4>
                        <p class="text-xs text-slate-600 mt-1">${m.desc}</p>
                    </div>
                </div>
            `).join('');

            const resultSection = document.getElementById('tracking-result-section');
            resultSection.classList.remove('hidden');
            resultSection.scrollIntoView({ behavior: 'smooth' });
            showToast(`Found tracking record for #${data.orderId}!`);
            lucide.createIcons();
        }

        function resetTrackingView() {
            document.getElementById('tracking-result-section').classList.add('hidden');
            document.getElementById('tracking-input').value = '';
            window.scrollTo({ top: 0, behavior: 'smooth' });
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
