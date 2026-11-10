@php
    use App\Models\Category;
    use App\Support\CheckoutSettings;
    use App\Support\FooterSettings;
    use App\Support\SiteSettings;

    $footer = $footerSettings ?? FooterSettings::forStorefront();
    $supportEmail = SiteSettings::get('support_email', 'support@quickbazar.pk');
    if (! is_string($supportEmail) || $supportEmail === '' || str_contains(strtolower($supportEmail), 'beautage')) {
        $supportEmail = 'support@quickbazar.pk';
    }
    $supportPhone = SiteSettings::get('support_phone', '+92 300 5550199');
    if (! is_string($supportPhone) || $supportPhone === '' || str_contains(strtolower($supportPhone), 'beautage')) {
        $supportPhone = '+92 300 5550199';
    }
    $supportPhoneTel = preg_replace('/[^\d+]/', '', (string) $supportPhone) ?: '+923005550199';
    $departments = Category::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get();
    $payments = collect(CheckoutSettings::all()['payment_methods'] ?? [])->filter(fn ($method) => ! empty($method['enabled']))->values();
    $quickLinks = [
        ['label' => 'About us', 'href' => route('our-story')],
        ['label' => 'Contact us', 'href' => route('help')],
        ['label' => 'Careers', 'href' => route('careers')],
        ['label' => 'Track your order', 'href' => route('track-order')],
        ['label' => 'Shipping & delivery', 'href' => route('shipping')],
        ['label' => 'Returns & exchanges', 'href' => route('returns')],
        ['label' => 'Journal', 'href' => route('blog.index')],
    ];
@endphp
<footer class="qb-footer site-storefront-footer mt-auto w-full">
    @if (! empty($footer['trust_enabled']))
    <div class="qb-footer-trust px-4 py-6 sm:px-8">
        <div class="grid w-full grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-4">
            <div class="qb-trust-card">
                <span class="qb-trust-icon"><i data-lucide="truck" class="h-5 w-5"></i></span>
                <span>
                    <span class="block text-sm font-extrabold text-white">Nationwide delivery</span>
                    <span class="mt-0.5 block text-xs text-slate-400">Fast shipping across Pakistan</span>
                </span>
            </div>
            <div class="qb-trust-card">
                <span class="qb-trust-icon"><i data-lucide="shield-check" class="h-5 w-5"></i></span>
                <span>
                    <span class="block text-sm font-extrabold text-white">Secure checkout</span>
                    <span class="mt-0.5 block text-xs text-slate-400">Cash, wallet, card, and bank transfer</span>
                </span>
            </div>
            <div class="qb-trust-card">
                <span class="qb-trust-icon"><i data-lucide="rotate-ccw" class="h-5 w-5"></i></span>
                <span>
                    <span class="block text-sm font-extrabold text-white">Easy returns</span>
                    <span class="mt-0.5 block text-xs text-slate-400">30-day refund on eligible items</span>
                </span>
            </div>
            <div class="qb-trust-card">
                <span class="qb-trust-icon"><i data-lucide="headphones" class="h-5 w-5"></i></span>
                <span>
                    <span class="block text-sm font-extrabold text-white">24/7 support</span>
                    <span class="mt-0.5 block text-xs text-slate-400">WhatsApp and live chat</span>
                </span>
            </div>
        </div>
    </div>
    @endif

    <div class="grid w-full grid-cols-1 gap-10 px-4 py-12 sm:px-6 md:grid-cols-2 lg:grid-cols-12 lg:px-8">
        <div class="lg:col-span-4">
            <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5">
                <span class="qb-logo-mark" aria-hidden="true">
                    <i data-lucide="shopping-bag" class="h-5 w-5"></i>
                </span>
                <span>
                    <span class="qb-wordmark block text-xl">Quick <span>Bazar</span></span>
                    <span class="qb-kicker">Express shopping</span>
                </span>
            </a>
            <p class="mt-4 max-w-sm text-sm leading-relaxed text-slate-400">Verified products delivered across Pakistan, with cash on delivery and tracked door-to-door shipping.</p>
            <ul class="mt-5 space-y-2 text-sm">
                <li>
                    <a href="tel:{{ $supportPhoneTel }}" class="qb-footer-link">
                        <i data-lucide="phone" class="h-4 w-4"></i>
                        {{ $supportPhone }}
                    </a>
                </li>
                <li>
                    <a href="mailto:{{ $supportEmail }}" class="qb-footer-link">
                        <i data-lucide="mail" class="h-4 w-4"></i>
                        {{ $supportEmail }}
                    </a>
                </li>
                <li class="qb-footer-link">
                    <i data-lucide="map-pin" class="h-4 w-4"></i>
                    Delivery across Pakistan
                </li>
            </ul>
            <div class="mt-5 flex flex-wrap gap-2">
                @foreach ($footer['social_links'] ?? [] as $social)
                    @if (empty($social['enabled']) || blank($social['href'] ?? null))
                        @continue
                    @endif
                    @php $socialHref = FooterSettings::resolveHref($social['href']); @endphp
                    @if ($socialHref)
                    <a href="{{ $socialHref }}" target="_blank" rel="noopener noreferrer" class="qb-footer-social" aria-label="Quick Bazar on {{ $social['platform'] ?? 'social' }}">
                        <i data-lucide="{{ $social['icon'] ?? 'link' }}" class="h-4 w-4"></i>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>

        <div class="lg:col-span-2">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-white">Quick links</h2>
            <ul class="mt-4 space-y-2.5">
                @foreach ($quickLinks as $link)
                <li>
                    <a href="{{ $link['href'] }}" class="qb-footer-link">
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        {{ $link['label'] }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="lg:col-span-3">
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-white">Top categories</h2>
            <ul class="mt-4 space-y-2.5">
                @foreach ($departments as $department)
                <li>
                    <a href="{{ $department->collectionsUrl() }}" class="qb-footer-link">
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        {{ $department->displayNavLabel() }}
                    </a>
                </li>
                @endforeach
                <li>
                    <a href="{{ route('collections') }}" class="qb-footer-link">
                        <i data-lucide="chevron-right" class="h-3.5 w-3.5"></i>
                        Shop all
                    </a>
                </li>
            </ul>
        </div>

        <div class="lg:col-span-3">
            @if (! empty($footer['newsletter_enabled']))
            <h2 class="text-xs font-extrabold uppercase tracking-wider text-white">Newsletter</h2>
            <p class="mt-3 text-sm leading-relaxed text-slate-400">Subscribe for new arrivals, offers, and delivery updates.</p>
            <form id="newsletter-form" data-newsletter-list="store" onsubmit="handleNewsletterSubmit(event)" class="qb-footer-news mt-4">
                <label for="newsletter-email" class="sr-only">Email address for newsletter</label>
                <input type="email" id="newsletter-email" name="email" required autocomplete="email" placeholder="Enter your email address">
                <button type="submit">Subscribe now</button>
            </form>
            @endif
            @if ($payments->isNotEmpty())
            <p class="mb-2 mt-6 text-[11px] font-extrabold uppercase tracking-wider text-slate-400">Accepted payments</p>
            <div class="flex flex-wrap gap-2">
                @foreach ($payments as $method)
                <span class="qb-pay-chip">
                    <i data-lucide="{{ $method['icon'] ?? 'wallet' }}" class="h-3.5 w-3.5 text-orange-400"></i>
                    {{ $method['short_label'] ?? $method['label'] ?? 'Payment' }}
                </span>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    <div class="qb-footer-bar">
        <div class="flex w-full flex-col items-start justify-between gap-3 px-4 py-4 sm:flex-row sm:items-center sm:px-6 lg:px-8">
            <p>© {{ date('Y') }} Quick Bazar. All rights reserved.</p>
            <nav class="flex flex-wrap gap-x-4 gap-y-2" aria-label="Footer policies">
                <a href="{{ route('help') }}">Privacy policy</a>
                <a href="{{ route('help') }}">Terms of use</a>
                <a href="{{ url('/sitemap_index.xml') }}">Sitemap</a>
            </nav>
        </div>
    </div>
</footer>
