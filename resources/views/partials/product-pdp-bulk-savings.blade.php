@php
    use App\Support\MoneyFormat;
    $bulkUnit = (float) $product->price;
    $bulkSixTotal = $bulkUnit * 6 * 0.9;
    $bulkTwelveTotal = $bulkUnit * 12 * 0.8;
@endphp
<div class="space-y-3 border-t border-slate-100 pt-4" aria-labelledby="pdp-bulk-heading">
    <div class="flex flex-wrap items-center justify-between gap-2">
        <h2 id="pdp-bulk-heading" class="text-xs font-bold uppercase tracking-wide text-slate-900">
            Pack size
        </h2>
        <span class="text-[11px] font-semibold text-slate-500">Or set your own quantity below</span>
    </div>
    <div class="grid grid-cols-3 gap-2 sm:gap-3" role="group" aria-label="Pack size">
        <button type="button" onclick="selectBulkTier(1, 0)" data-bulk-tier="1" aria-pressed="true" class="pdp-bulk-card is-active">
            <span class="pdp-bulk-card__title">1 Piece</span>
            <span class="pdp-bulk-card__badge">Standard</span>
            <span class="pdp-bulk-card__total">{{ MoneyFormat::pkr($bulkUnit) }}</span>
        </button>
        <button type="button" onclick="selectBulkTier(6, 0.10)" data-bulk-tier="6" aria-pressed="false" class="pdp-bulk-card">
            <span class="pdp-bulk-card__title">6 Pieces</span>
            <span class="pdp-bulk-card__badge">Save 10%</span>
            <span class="pdp-bulk-card__total">{{ MoneyFormat::pkr($bulkSixTotal) }}</span>
        </button>
        <button type="button" onclick="selectBulkTier(12, 0.20)" data-bulk-tier="12" aria-pressed="false" class="pdp-bulk-card">
            <span class="pdp-bulk-card__title">12 Pieces</span>
            <span class="pdp-bulk-card__badge">Save 20%</span>
            <span class="pdp-bulk-card__total">{{ MoneyFormat::pkr($bulkTwelveTotal) }}</span>
        </button>
    </div>
</div>
