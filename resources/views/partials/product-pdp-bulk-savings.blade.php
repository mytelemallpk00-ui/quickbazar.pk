@php
    use App\Support\MoneyFormat;
    $bulkUnit = (float) $product->price;
    $bulkSixTotal = $bulkUnit * 6 * 0.9;
    $bulkTwelveTotal = $bulkUnit * 12 * 0.8;
@endphp
<div class="space-y-3 pt-2 border-t border-purple-100" aria-labelledby="pdp-bulk-heading">
    <div class="flex items-center justify-between gap-2 flex-wrap">
        <h2 id="pdp-bulk-heading" class="text-xs font-extrabold uppercase tracking-wider text-[color:var(--beautage-purple-dark,#3b0764)]">
            Bulk savings
        </h2>
        <span class="text-[10px] font-semibold pdp-text-muted">Single pieces: use quantity below</span>
    </div>
    <div class="grid grid-cols-2 gap-3" role="group" aria-label="Bulk purchase options">
        <button type="button" onclick="selectBulkTier(6, 0.10)" data-bulk-tier="6" aria-pressed="false" class="pdp-bulk-card">
            <span class="pdp-bulk-card__title">6 Pieces</span>
            <span class="pdp-bulk-card__badge">10% off</span>
            <span class="pdp-bulk-card__total">{{ MoneyFormat::pkr($bulkSixTotal) }}</span>
        </button>
        <button type="button" onclick="selectBulkTier(12, 0.20)" data-bulk-tier="12" aria-pressed="false" class="pdp-bulk-card">
            <span class="pdp-bulk-card__title">12 Pieces</span>
            <span class="pdp-bulk-card__badge">20% off</span>
            <span class="pdp-bulk-card__total">{{ MoneyFormat::pkr($bulkTwelveTotal) }}</span>
        </button>
    </div>
</div>
