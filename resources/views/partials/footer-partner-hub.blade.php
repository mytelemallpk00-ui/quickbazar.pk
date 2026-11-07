@php
    use App\Support\FooterSettings;

    $partnerButtons = collect($footer['partner_hub_buttons'] ?? [])
        ->filter(fn ($btn) => ! empty($btn['enabled']));
@endphp

@if (! empty($footer['partner_hub_enabled']) && ($partnerButtons->isNotEmpty() || ! empty($footer['partner_newsletter_enabled'])))
<section class="site-partner-hub relative w-full overflow-hidden border-b border-slate-800 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 px-4 py-10 sm:px-12 sm:py-12 xl:px-20" aria-labelledby="partner-hub-heading">
    <div class="absolute -left-24 top-0 w-72 h-72 bg-cyan-400/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>
    <div class="absolute -right-16 bottom-0 w-64 h-64 bg-emerald-400/10 rounded-full blur-3xl pointer-events-none" aria-hidden="true"></div>

    <div class="max-w-7xl mx-auto relative z-10">
        <div class="text-center max-w-2xl mx-auto mb-8 sm:mb-10">
            @if (filled($footer['partner_hub_badge'] ?? null))
            <span class="inline-block px-3 py-1 rounded-full text-[10px] uppercase font-extrabold tracking-widest bg-white/10 text-cyan-200 border border-cyan-400/30 mb-3">
                {{ $footer['partner_hub_badge'] }}
            </span>
            @endif
            <h2 id="partner-hub-heading" class="text-2xl sm:text-3xl font-serif font-bold text-white tracking-tight">
                {{ $footer['partner_hub_title'] ?? 'Beautage PVT Ltd. Partner' }}
            </h2>
            @if (filled($footer['partner_hub_subtitle'] ?? null))
            <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">{{ $footer['partner_hub_subtitle'] }}</p>
            @endif
        </div>

        @if ($partnerButtons->isNotEmpty())
        <div class="site-partner-hub-actions max-w-4xl mx-auto">
            @foreach ($partnerButtons as $button)
                @php
                    $btnHref = FooterSettings::resolvePartnerButtonHref($button);
                    $btnClass = FooterSettings::partnerButtonClass($button['style'] ?? 'outline');
                @endphp
                @if ($btnHref)
                <a href="{{ $btnHref }}" @if (FooterSettings::isExternalUrl($btnHref)) target="_blank" rel="noopener noreferrer" @endif class="{{ $btnClass }}">
                    <i data-lucide="{{ $button['icon'] ?? 'circle' }}" class="w-4 h-4 shrink-0" aria-hidden="true"></i>
                    <span>{{ $button['label'] ?? '' }}</span>
                </a>
                @endif
            @endforeach
        </div>
        @endif

        @if (! empty($footer['partner_newsletter_enabled']))
        <div class="site-partner-hub-newsletter max-w-3xl mx-auto mt-8 sm:mt-10 rounded-2xl border border-white/15 bg-white/5 backdrop-blur-sm p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4 text-center sm:text-left">
                <div class="min-w-0">
                    @if (filled($footer['partner_newsletter_badge'] ?? null))
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-200">{{ $footer['partner_newsletter_badge'] }}</span>
                    @endif
                    @if (filled($footer['partner_newsletter_heading'] ?? null))
                    <p class="text-sm sm:text-base font-bold text-white mt-1">{{ $footer['partner_newsletter_heading'] }}</p>
                    @endif
                    @if (filled($footer['partner_newsletter_description'] ?? null))
                    <p class="text-[11px] text-slate-300 mt-1 leading-relaxed">{{ $footer['partner_newsletter_description'] }}</p>
                    @endif
                </div>
            </div>
            <form class="site-partner-hub-newsletter-form" data-newsletter-list="partner" onsubmit="handleNewsletterSubmit(event)">
                @csrf
                <label for="partner-newsletter-email" class="sr-only">Partner newsletter email</label>
                <input type="email" id="partner-newsletter-email" name="email" required autocomplete="email"
                       placeholder="{{ $footer['partner_newsletter_placeholder'] ?? 'Partner email…' }}"
                       class="site-partner-hub-newsletter-input">
                <button type="submit" class="site-partner-hub-newsletter-btn">
                    {{ $footer['partner_newsletter_button'] ?? 'Subscribe' }}
                </button>
            </form>
        </div>
        @endif
    </div>
</section>
@endif
