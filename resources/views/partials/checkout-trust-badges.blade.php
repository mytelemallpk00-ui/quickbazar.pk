@php
    use App\Support\ProductTrustSettings;
    use Illuminate\Support\Str;

    $trustConfig = ProductTrustSettings::forStorefront();
    $badgeItems = [];

    if (! empty($trustConfig['enabled'])) {
        foreach (['trust_section', 'quality_section'] as $sectionKey) {
            $section = $trustConfig[$sectionKey] ?? null;
            if (! is_array($section)) {
                continue;
            }
            foreach ($section['items'] ?? [] as $item) {
                if (! is_array($item) || blank($item['title'] ?? null)) {
                    continue;
                }
                $badgeItems[] = $item;
            }
        }
    }

    if ($badgeItems === []) {
        $badgeItems = [
            ['icon' => 'lock', 'title' => '256-Bit SSL', 'description' => 'Encrypted checkout'],
            ['icon' => 'shield-check', 'title' => '100% Original', 'description' => 'Authorized products only'],
            ['icon' => 'badge-check', 'title' => 'Verified Fulfillment', 'description' => 'Inspected before dispatch'],
            ['icon' => 'truck', 'title' => 'Tracked Delivery', 'description' => 'Trusted couriers nationwide'],
            ['icon' => 'banknote', 'title' => 'COD Available', 'description' => 'Pay when you receive'],
            ['icon' => 'rotate-ccw', 'title' => '30-Day Support', 'description' => 'Returns on eligible items'],
        ];
    }

    $badgeItems = collect($badgeItems)
        ->unique(fn (array $item) => Str::lower(trim((string) ($item['title'] ?? ''))))
        ->take(6)
        ->values()
        ->all();
@endphp

<div class="pt-5 mt-1 border-t border-purple-100 space-y-3" aria-label="Trust and security badges">
    <div class="text-center space-y-1">
        <p class="text-[10px] font-extrabold uppercase tracking-[0.2em] text-beautage-purple">Trust &amp; Security</p>
        <p class="text-[11px] text-slate-500 leading-relaxed">Your payment and personal details are protected on every Beautage order.</p>
    </div>

    <div class="grid grid-cols-2 gap-2.5">
        @foreach ($badgeItems as $badge)
        <div class="flex items-start gap-2.5 p-3 rounded-2xl bg-gradient-to-br from-purple-50/90 to-white border border-purple-100/80 shadow-sm">
            <div class="w-9 h-9 rounded-xl bg-white border border-purple-100 text-beautage-purple flex items-center justify-center shrink-0 shadow-sm">
                <i data-lucide="{{ $badge['icon'] ?? 'shield-check' }}" class="w-4 h-4"></i>
            </div>
            <div class="min-w-0">
                <p class="text-[11px] font-bold text-beautage-purpleDark leading-tight">{{ $badge['title'] }}</p>
                @if (filled($badge['description'] ?? null))
                <p class="text-[10px] text-slate-500 mt-0.5 leading-snug line-clamp-2">{{ $badge['description'] }}</p>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center justify-center gap-3 pt-1 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">
            <i data-lucide="lock" class="w-3.5 h-3.5 text-emerald-600"></i> Secure checkout
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">
            <i data-lucide="shield" class="w-3.5 h-3.5 text-beautage-purple"></i> Privacy protected
        </span>
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 border border-slate-200">
            <i data-lucide="credit-card" class="w-3.5 h-3.5 text-beautage-pink"></i> Safe payments
        </span>
    </div>
</div>
