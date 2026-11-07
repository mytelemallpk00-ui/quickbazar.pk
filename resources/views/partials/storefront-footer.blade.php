@php
    use App\Support\FooterSettings;

    $footer = $footerSettings ?? FooterSettings::forStorefront();
@endphp
<footer class="site-storefront-footer mt-auto w-full border-t border-slate-200 bg-white text-sm text-slate-700">

    @if (! empty($footer['trust_enabled']))
    <div class="w-full border-b border-slate-200 bg-slate-50 px-4 py-6 sm:px-8">
        <div class="mx-auto grid max-w-7xl grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-4">
            @foreach ($footer['trust_badges'] ?? [] as $badge)
                @if (! empty($badge['enabled']))
                <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                        <i data-lucide="{{ $badge['icon'] ?? 'shield-check' }}" class="h-5 w-5" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">{{ $badge['title'] ?? '' }}</h4>
                        @if (filled($badge['subtitle'] ?? null))
                        <p class="mt-0.5 text-[11px] font-medium text-slate-500">{{ $badge['subtitle'] }}</p>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    @if (! empty($footer['newsletter_enabled']))
    <div class="w-full bg-emerald-700 px-4 py-8 text-white sm:px-8">
        <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-6 lg:flex-row">
            <div class="max-w-xl space-y-1 text-center lg:text-left">
                @if (filled($footer['newsletter_badge'] ?? null))
                <span class="mb-1 inline-block rounded-full bg-white/15 px-3 py-1 text-[10px] font-bold uppercase tracking-wider text-emerald-50">
                    {{ $footer['newsletter_badge'] }}
                </span>
                @endif
                @if (filled($footer['newsletter_heading'] ?? null))
                <h3 class="text-2xl font-extrabold tracking-tight text-white">{{ $footer['newsletter_heading'] }}</h3>
                @endif
                @if (filled($footer['newsletter_description'] ?? null))
                <p class="text-sm leading-relaxed text-emerald-50">{{ $footer['newsletter_description'] }}</p>
                @endif
            </div>
            <form id="newsletter-form" data-newsletter-list="store" onsubmit="handleNewsletterSubmit(event)" class="flex w-full flex-col items-center gap-3 sm:flex-row lg:w-auto">
                <div class="relative w-full sm:w-80">
                    <label for="newsletter-email" class="sr-only">Email address for newsletter</label>
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-slate-400" aria-hidden="true">
                        <i data-lucide="mail" class="h-4 w-4"></i>
                    </span>
                    <input type="email" id="newsletter-email" name="email" required autocomplete="email"
                           placeholder="{{ $footer['newsletter_placeholder'] ?? 'Enter your email address...' }}"
                           class="w-full rounded-full border-0 bg-white py-3.5 pl-10 pr-4 text-sm text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-4 focus:ring-emerald-300">
                </div>
                <button type="submit" class="flex w-full shrink-0 items-center justify-center gap-2 rounded-full bg-slate-900 px-6 py-3.5 text-xs font-bold uppercase tracking-wider text-white transition hover:bg-slate-800 sm:w-auto">
                    <span>{{ $footer['newsletter_button'] ?? 'Subscribe' }}</span>
                    <i data-lucide="arrow-right" class="h-4 w-4" aria-hidden="true"></i>
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
    <div class="w-full border-b border-slate-800 bg-slate-900 px-4 py-4 sm:px-8">
        <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-6 gap-y-3 text-xs font-semibold lg:justify-between">
            @foreach ($policyLinks as $link)
                @php
                    $policyHref = FooterSettings::resolveHref($link['href'] ?? null);
                @endphp
                @if ($policyHref)
                <a href="{{ $policyHref }}" @if (FooterSettings::isExternalUrl($policyHref)) target="_blank" rel="noopener noreferrer" @endif class="flex items-center gap-2 text-slate-300 hover:text-white transition group py-1">
                    <i data-lucide="{{ $link['icon'] ?? 'link' }}" class="w-4 h-4 text-emerald-400 group-hover:scale-110 transition" aria-hidden="true"></i>
                    <span>{{ $link['label'] ?? '' }}</span>
                </a>
                @endif
            @endforeach
        </div>
    </div>
    @endif

    <div class="w-full bg-slate-950 text-slate-300">
    <div class="mx-auto grid w-full max-w-7xl grid-cols-1 gap-8 px-4 py-12 sm:px-8 md:grid-cols-2 lg:grid-cols-5">
        <div class="w-full min-w-0 max-w-sm space-y-4 lg:col-span-1 lg:max-w-none">
            <a href="{{ route('home') }}" class="group inline-flex flex-col items-start gap-2">
                <img
                    src="{{ $footerLogoUrl }}"
                    alt="QuickBazar — {{ $siteTagline }}"
                    width="160"
                    height="56"
                    class="h-12 w-auto object-contain"
                    loading="lazy"
                    decoding="async"
                >
                @if (! empty($footer['show_tagline_under_logo']))
                <span class="text-[11px] font-serif italic text-slate-300/95 tracking-wide pl-1">{{ $siteTagline }}</span>
                @endif
            </a>
            @if (filled($footer['about_text'] ?? null))
            <p class="text-xs text-slate-300 leading-relaxed font-semibold">{{ $footer['about_text'] }}</p>
            @endif
            <div class="flex items-center gap-2 pt-1 flex-wrap">
                @foreach ($footer['social_links'] ?? [] as $social)
                    @if (empty($social['enabled']) || blank($social['href'] ?? null))
                        @continue
                    @endif
                    @php $socialHref = FooterSettings::resolveHref($social['href']); @endphp
                    @if ($socialHref)
                    <a href="{{ $socialHref }}" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-xl border border-slate-700 bg-slate-800 hover:bg-emerald-600 text-white flex items-center justify-center transition shadow-sm" aria-label="QuickBazar on {{ $social['platform'] ?? 'Social' }}">
                        <i data-lucide="{{ $social['icon'] ?? 'link' }}" class="w-4 h-4" aria-hidden="true"></i>
                    </a>
                    @endif
                @endforeach
            </div>
        </div>

        @foreach ($footer['link_columns'] ?? [] as $column)
        <div>
            @if (filled($column['title'] ?? null))
            <h4 class="text-white font-extrabold uppercase text-xs tracking-wider mb-4 border-b border-slate-800 pb-2">{{ $column['title'] }}</h4>
            @endif
            <ul class="space-y-3 text-xs text-slate-300 font-semibold">
                @foreach ($column['links'] ?? [] as $link)
                    @if (empty($link['enabled']))
                        @continue
                    @endif
                    @php $linkHref = FooterSettings::resolveHref($link['href'] ?? null); @endphp
                    @if ($linkHref)
                    <li>
                        <a href="{{ $linkHref }}" @if (FooterSettings::isExternalUrl($linkHref)) target="_blank" rel="noopener noreferrer" @endif class="hover:text-white hover:underline transition flex items-center gap-2">
                            <i data-lucide="{{ $link['icon'] ?? 'chevron-right' }}" class="w-3.5 h-3.5 text-emerald-400" aria-hidden="true"></i>
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
            <h4 class="text-white font-extrabold uppercase text-xs tracking-wider mb-4 border-b border-slate-800 pb-2">{{ $footer['mobile_app_title'] }}</h4>
            @endif
            @if (filled($footer['mobile_app_description'] ?? null))
            <p class="text-xs text-slate-300 font-semibold leading-relaxed">{{ $footer['mobile_app_description'] }}</p>
            @endif
            <div class="space-y-2.5 pt-1">
                @if (filled($footer['mobile_app_ios_href'] ?? null))
                <a href="{{ $footer['mobile_app_ios_href'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 border border-slate-700 bg-slate-800 hover:bg-slate-700 p-2.5 rounded-xl transition text-white shadow-sm">
                    <i data-lucide="apple" class="w-5 h-5 shrink-0" aria-hidden="true"></i>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">Download on</span>
                        <span class="text-xs font-bold text-white block mt-0.5">App Store</span>
                    </div>
                </a>
                @endif
                @if (filled($footer['mobile_app_android_href'] ?? null))
                <a href="{{ $footer['mobile_app_android_href'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 border border-slate-700 bg-slate-800 hover:bg-slate-700 p-2.5 rounded-xl transition text-white shadow-sm">
                    <i data-lucide="play" class="w-5 h-5 shrink-0 fill-current" aria-hidden="true"></i>
                    <div class="text-left leading-none">
                        <span class="text-[9px] uppercase tracking-wider text-slate-400 block">Get it on</span>
                        <span class="text-xs font-bold text-white block mt-0.5">Google Play</span>
                    </div>
                </a>
                @endif
            </div>
        </div>
        @endif
    </div>

    @if (filled($footer['copyright_line'] ?? null))
    <div class="border-t border-slate-800 py-5 text-center text-xs font-semibold text-slate-400">
        <p>{{ $footer['copyright_line'] }}</p>
    </div>
    @endif
    </div>
</footer>
