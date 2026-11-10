@php
    $pdpTrust = $pdpTrust ?? \App\Support\ProductTrustSettings::forStorefront();
@endphp
@if (! empty($pdpTrust['enabled']))
<section id="pdp-trust-column" class="mt-8 w-full px-4 sm:px-6 lg:px-8" aria-label="Store guarantees">
    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
        @foreach (['trust_section', 'quality_section'] as $sectionKey)
            @php
                $section = $pdpTrust[$sectionKey] ?? null;
            @endphp
            @if (! is_array($section))
                @continue
            @endif
            <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white">
                <div class="border-b border-slate-100 px-5 py-4">
                    @if (filled($section['badge'] ?? null))
                    <p class="text-[11px] font-bold uppercase tracking-wide text-emerald-700">{{ $section['badge'] }}</p>
                    @endif
                    <h2 class="mt-1 text-base font-bold text-slate-900">{{ $section['heading'] ?? '' }}</h2>
                    @if (filled($section['intro'] ?? null))
                    <p class="mt-1 text-xs leading-relaxed text-slate-500">{{ str_ireplace(['Beautage', 'Shop Pakistan'], 'Quick Bazar', (string) $section['intro']) }}</p>
                    @endif
                </div>
                <ul class="divide-y divide-slate-100">
                    @foreach ($section['items'] ?? [] as $item)
                    <li class="flex gap-3 px-5 py-3.5">
                        <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-emerald-700">
                            <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}" class="h-4 w-4"></i>
                        </span>
                        <span class="min-w-0">
                            <span class="block text-sm font-bold text-slate-900">{{ $item['title'] ?? '' }}</span>
                            @if (filled($item['description'] ?? null))
                            <span class="mt-0.5 block text-xs leading-relaxed text-slate-500">{{ $item['description'] }}</span>
                            @endif
                        </span>
                    </li>
                    @endforeach
                </ul>
            </article>
        @endforeach
    </div>
</section>
@endif
