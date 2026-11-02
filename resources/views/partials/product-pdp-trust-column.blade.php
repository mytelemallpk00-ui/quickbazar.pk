@php
    $pdpTrust = $pdpTrust ?? \App\Support\ProductTrustSettings::forStorefront();
@endphp
@if (! empty($pdpTrust['enabled']))
<div id="pdp-trust-column" class="col-span-1 lg:col-span-3 min-w-0">
    <div class="hidden lg:block lg:sticky lg:top-[5.75rem] lg:z-[1] space-y-5">
        @foreach (['trust_section', 'quality_section'] as $sectionKey)
            @php
                $section = $pdpTrust[$sectionKey] ?? null;
                if (! is_array($section)) {
                    continue;
                }
            @endphp
            <div class="rounded-3xl border border-purple-100 bg-white shadow-lg shadow-purple-900/5 overflow-hidden">
                <div class="px-5 py-4 bg-gradient-to-r from-purple-50 to-pink-50/80 border-b border-purple-100">
                    @if (filled($section['badge'] ?? null))
                    <span class="inline-block text-[10px] font-extrabold uppercase tracking-widest text-beautage-pink mb-1.5">{{ $section['badge'] }}</span>
                    @endif
                    <h2 class="text-base font-serif font-bold text-beautage-purpleDark leading-snug">{{ $section['heading'] ?? '' }}</h2>
                    @if (filled($section['intro'] ?? null))
                    <p class="text-[11px] text-slate-600 mt-2 leading-relaxed">{{ $section['intro'] }}</p>
                    @endif
                </div>
                <ul class="divide-y divide-purple-50">
                    @foreach ($section['items'] ?? [] as $item)
                    <li class="flex gap-3 p-4 hover:bg-purple-50/40 transition">
                        <div class="w-10 h-10 rounded-2xl bg-purple-50 text-beautage-purple flex items-center justify-center shrink-0 border border-purple-100">
                            <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}" class="w-5 h-5"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-xs font-bold text-beautage-purpleDark leading-tight">{{ $item['title'] ?? '' }}</h3>
                            @if (filled($item['description'] ?? null))
                            <p class="text-[11px] text-slate-600 mt-1 leading-relaxed">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>

    <div class="lg:hidden space-y-4">
        @foreach (['trust_section', 'quality_section'] as $sectionKey)
            @php $section = $pdpTrust[$sectionKey] ?? null; @endphp
            @if (! is_array($section))
                @continue
            @endif
            <div class="rounded-2xl border border-purple-100 bg-white shadow-sm overflow-hidden">
                <div class="px-4 py-3 bg-purple-50/70 border-b border-purple-100">
                    @if (filled($section['badge'] ?? null))
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-beautage-pink">{{ $section['badge'] }}</span>
                    @endif
                    <h2 class="text-sm font-serif font-bold text-beautage-purple mt-0.5">{{ $section['heading'] ?? '' }}</h2>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-px bg-purple-50">
                    @foreach ($section['items'] ?? [] as $item)
                    <div class="flex gap-2.5 p-3 bg-white">
                        <i data-lucide="{{ $item['icon'] ?? 'shield-check' }}" class="w-4 h-4 text-beautage-pink shrink-0 mt-0.5"></i>
                        <div>
                            <p class="text-[11px] font-bold text-beautage-purple">{{ $item['title'] ?? '' }}</p>
                            @if (filled($item['description'] ?? null))
                            <p class="text-[10px] text-slate-600 mt-0.5 leading-snug">{{ $item['description'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>
@endif
