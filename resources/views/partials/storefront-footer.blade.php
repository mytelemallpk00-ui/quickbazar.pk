@php
    use App\Support\FooterSettings;

    $footer = $footerSettings ?? FooterSettings::forStorefront();
@endphp
<footer class="site-storefront-footer mt-auto bg-beautage-purpleDark border-t border-purple-800 w-full text-white text-sm max-md:pb-[5.75rem] md:pb-0 max-md:mb-0">

    @if (! empty($footer['trust_enabled']))
    <div class="w-full px-4 sm:px-12 xl:px-20 py-8 border-b border-purple-800 bg-purple-950/80">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 max-w-7xl mx-auto">
            @foreach ($footer['trust_badges'] ?? [] as $badge)
                @if (! empty($badge['enabled']))
                <div class="flex items-center gap-4 p-4 rounded-2xl bg-purple-900/90 border border-purple-600/70 shadow-md">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
                        <i data-lucide="{{ $badge['icon'] ?? 'shield-check' }}" class="w-5 h-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-extrabold text-white">{{ $badge['title'] ?? '' }}</h4>
                        @if (filled($badge['subtitle'] ?? null))
                        <p class="text-[11px] text-purple-100 font-medium mt-0.5">{{ $badge['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    @if (! empty($footer['newsletter_enabled']))
    <div class="w-full px-4 sm:px-12 xl:px-20 py-10 border-b border-purple-800">
        <div class="max-w-7xl mx-auto rounded-3xl bg-gradient-to-r from-purple-950 via-purple-900 to-purple-950 border border-purple-600/70 p-6 sm:p-12 shadow-2xl relative overflow-hidden flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="absolute -right-20 -bottom-20 w-80 h-80 bg-beautage-pink/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="max-w-xl space-y-2 relative z-10 text-center lg:text-left">
                @if (filled($footer['newsletter_badge'] ?? null))
                <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-wider bg-emerald-500/20 text-emerald-300 border border-emerald-400/40 mb-1">
                    {{ $footer['newsletter_badge'] }}
                </span>
                @endif
                @if (filled($footer['newsletter_heading'] ?? null))
                <h3 class="text-2xl sm:text-3xl font-bold text-white tracking-tight font-serif">{{ $footer['newsletter_heading'] }}</h3>
                @endif
                @if (filled($footer['newsletter_description'] ?? null))
                <p class="text-xs sm:text-sm text-purple-100 leading-relaxed font-semibold">{{ $footer['newsletter_description'] }}</p>
                @endif
            </div>
            <form id="newsletter-form" data-newsletter-list="store" onsubmit="handleNewsletterSubmit(event)" class="w-full lg:w-auto relative z-10 flex flex-col sm:flex-row items-center gap-3">
                <div class="relative w-full sm:w-80">
                    <label for="newsletter-email" class="sr-only">Email address for newsletter</label>
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-purple-200 pointer-events-none" aria-hidden="true">
                        <i data-lucide="mail" class="w-4 h-4"></i>
                    </span>
                    <input type="email" id="newsletter-email" name="email" required autocomplete="email"
                           placeholder="{{ $footer['newsletter_placeholder'] ?? 'Enter your email address...' }}"
                           class="w-full pl-10 pr-4 py-3.5 bg-purple-950 border border-purple-500 rounded-xl text-xs text-white placeholder-purple-200 focus:outline-none focus:border-emerald-400 transition shadow-inner font-medium">
                </div>
                <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition shadow-lg shadow-emerald-900/30 shrink-0 flex items-center justify-center gap-2">
                    <span>{{ $footer['newsletter_button'] ?? 'Subscribe' }}</span>
                    <i data-lucide="arrow-right" class="w-4 h-4" aria-hidden="true"></i>
                </button>
            </form>
        </div>
    </div>
    @endif

    @include('partials.footer-partner-hub', ['footer' => $footer])

    @php
        $policyLinks = collect($footer['policy_links'] ?? [])->filter(fn ($link) => ! empty($link['enabled']));
    @endphp
    @if ($policyLinks->isNotEmpty())
    <div class="w-full px-4 sm:px-12 xl:px-20 py-5 border-b border-purple-800 bg-purple-950/40">
        <div class="max-w-7xl mx-auto flex flex-wrap items-center justify-center lg:justify-between gap-y-3 gap-x-6 text-xs font-semibold">
            @foreach ($policyLinks as $link)
                @php
                    $policyHref = FooterSettings::resolveHref($link['href'] ?? null);
                @endphp
                @if ($policyHref)
                <a href="{{ $policyHref }}" @if (FooterSettings::isExternalUrl($policyHref)) target="_blank" rel="noopener noreferrer" @endif class="flex items-center gap-2 text-purple-100 hover:text-white transition group py-1">
                    <i data-lucide="{{ $link['icon'] ?? 'link' }}" class="w-4 h-4 text-pink-300 group-hover:scale-110 transition" aria-hidden="true"></i>
                    <span>{{ $link['label'] ?? '' }}</span>
                </a>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <div class="w-full px-4 sm:px-12 xl:px-20 py-12 max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
        <div class="lg:col-span-1 space-y-4 min-w-0 w-full max-w-sm lg:max-w-none">
            <a href="{{ route('home') }}" class="inline-flex flex-col items-start gap-2 group">
                <img
                    src="{{ $footerLogoUrl }}"
                    alt="Beautage — {{ $siteTagline }}"
                    width="128"
                    height="128"
                    class="w-28 h-28 sm:w-32 sm:h-32 object-contain drop-shadow-[0_8px_24px_rgba(0,0,0,0.35)] transition-transform duration-300 group-hover:scale-[1.02]"
                    loading="lazy"
                    decoding="async"
                >
                @if (! empty($footer['show_tagline_under_logo']))
                <span class="text-[11px] font-serif italic text-purple-100/95 tracking-wide pl-1">{{ $siteTagline }}</span>
                @endif
            </a>
            @if (filled($footer['about_text'] ?? null))
            <p class="text-xs text-purple-100 leading-relaxed font-semibold">{{ $footer['about_text'] }}</p>
            @endif
            <div class="flex items-center gap-2 pt-1 flex-wrap">
                @foreach ($footer['social_links'] ?? [] as $social)
                    @if (empty($social['enabled']) || blank($social['href'] ?? null))
                        @continue
                    @endif
                    @php $socialHref = FooterSettings::resolveHref($social['href']); @endphp
                    @if ($socialHref)
                    <a href="{{ $socialHref }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl bg-purple-900 border border-purple-600 hover:bg-beautage-pink text-white flex items-center justify-center transition shadow-sm" aria-label="Beautage on {{ $social['platform'] ?? 'Social' }}">
                        <i data-lucide="{{ $social['icon'] ?? 'link' }}" class="w-4 h-4" aria-hidden="true"></i>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>

        @foreach ($footer['link_columns'] ?? [] as $column)
        <div>
            @if (filled($column['title'] ?? null))
            <h4 class="text-white font-extrabold uppercase text-xs tracking-wider mb-4 border-b border-purple-800 pb-2">{{ $column['title'] }}</h4>
            @endif
            <ul class="space-y-3 text-xs text-purple-50 font-semibold">
                @foreach ($column['links'] ?? [] as $link)
                    @if (empty($link['enabled']))
                        @continue
                    @endif
                    @php $linkHref = FooterSettings::resolveHref($link['href'] ?? null); @endphp
                    @if ($linkHref)
                    <li>
                        <a href="{{ $linkHref }}" @if (FooterSettings::isExternalUrl($linkHref)) target="_blank" rel="noopener noreferrer" @endif class="hover:text-white hover:underline transition flex items-center gap-2">
                            <i data-lucide="{{ $link['icon'] ?? 'chevron-right' }}" class="w-3.5 h-3.5 text-pink-400" aria-hidden="true"></i>
                            {{ $link['label'] ?? '' }}
                        </a>
                    </li>
                    @endif
                @endforeach
            </ul>
        </div>
        @endforeach

        @if (! empty($footer['mobile_app_enabled']))
        <div class="space-y-3">
            @if (filled($footer['mobile_app_title'] ?? null))
            <h4 class="text-white font-extrabold uppercase text-xs tracking-wider mb-4 border-b border-purple-800 pb-2">{{ $footer['mobile_app_title'] }}</h4>
            @endif
            @if (filled($footer['mobile_app_description'] ?? null))
            <p class="text-xs text-purple-100 font-semibold leading-relaxed">{{ $footer['mobile_app_description'] }}</p>
            @endif
            <div class="space-y-2.5 pt-1">
                @if (filled($footer['mobile_app_ios_href'] ?? null))
                <a href="{{ $footer['mobile_app_ios_href'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 bg-purple-900 hover:bg-purple-800 border border-purple-600 p-2.5 rounded-xl transition text-white shadow-sm">
                    <i data-lucide="apple" class="w-5 h-5 shrink-0" aria-hidden="true"></i>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider text-purple-200 block">Download on</span>
                        <span class="text-xs font-bold text-white block mt-0.5">App Store</span>
                    </div>
                </a>
                @endif
                @if (filled($footer['mobile_app_android_href'] ?? null))
                <a href="{{ $footer['mobile_app_android_href'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 bg-purple-900 hover:bg-purple-800 border border-purple-600 p-2.5 rounded-xl transition text-white shadow-sm">
                    <i data-lucide="play" class="w-5 h-5 shrink-0 fill-current" aria-hidden="true"></i>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider text-purple-200 block">Get it on</span>
                        <span class="text-xs font-bold text-white block mt-0.5">Google Play</span>
                    </div>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if (filled($footer['copyright_line'] ?? null))
    <div class="border-t border-purple-800 py-6 text-center text-xs text-purple-100 font-semibold">
        <p>{{ $footer['copyright_line'] }}</p>
    </div>
    @endif
</footer>
