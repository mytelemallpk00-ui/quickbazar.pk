<x-filament-panels::page>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="lg:col-span-2 space-y-6">
            <div class="rounded-2xl border border-purple-100 bg-gradient-to-br from-purple-50 to-white p-5">
                <h2 class="text-sm font-bold uppercase tracking-wider text-purple-800">Import catalog from marketplaces</h2>
                <p class="mt-1 text-sm text-slate-600">
                    Pull bulk products from <strong>Daraz.pk</strong>, <strong>Amazon</strong>, <strong>Shopify</strong>, or <strong>Walmart</strong>. For category links choose <strong>All products (auto)</strong> or a <strong>custom number</strong>.
                </p>
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach(\App\Models\ProductImportBatch::sourceLabels() as $key => $label)
                        <span class="rounded-full border border-purple-200 bg-white px-3 py-1 text-xs font-semibold text-purple-900">{{ $label }}</span>
                    @endforeach
                </div>
            </div>

            <form wire:submit="runImport" class="space-y-4">
                {{ $this->form }}

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <x-filament::button type="button" color="gray" wire:click="downloadTemplate" icon="heroicon-o-document-arrow-down">
                        Download CSV template
                    </x-filament::button>
                    <x-filament::button type="submit" icon="heroicon-o-arrow-down-tray" size="lg">
                        Run import
                    </x-filament::button>
                    <a href="{{ route('filament.admin.resources.products.index') }}" class="text-xs font-semibold text-purple-700 hover:underline">
                        View products →
                    </a>
                </div>
            </form>

            @if($lastResult)
                <x-filament::section heading="Last import result" class="mt-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                        <div class="rounded-xl bg-slate-50 p-3 text-center">
                            <p class="text-2xl font-black text-slate-800">{{ $lastResult['total'] }}</p>
                            <p class="text-[10px] uppercase font-bold text-slate-500">Processed</p>
                        </div>
                        <div class="rounded-xl bg-emerald-50 p-3 text-center">
                            <p class="text-2xl font-black text-emerald-700">{{ $lastResult['created'] }}</p>
                            <p class="text-[10px] uppercase font-bold text-emerald-600">Created</p>
                        </div>
                        <div class="rounded-xl bg-indigo-50 p-3 text-center">
                            <p class="text-2xl font-black text-indigo-700">{{ $lastResult['updated'] }}</p>
                            <p class="text-[10px] uppercase font-bold text-indigo-600">Updated</p>
                        </div>
                        <div class="rounded-xl bg-rose-50 p-3 text-center">
                            <p class="text-2xl font-black text-rose-700">{{ $lastResult['failed'] }}</p>
                            <p class="text-[10px] uppercase font-bold text-rose-600">Failed</p>
                        </div>
                    </div>
                    @if(! empty($lastResult['failures']))
                        <ul class="text-xs text-slate-600 space-y-1 max-h-40 overflow-y-auto">
                            @foreach($lastResult['failures'] as $failure)
                                <li>
                                    @if(isset($failure['row']))
                                        <span class="font-semibold">Row {{ $failure['row'] }}:</span>
                                    @endif
                                    {{ $failure['message'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </x-filament::section>
            @endif
        </div>

        <div class="space-y-4">
            <x-filament::section heading="Recent imports">
                @forelse(\Illuminate\Support\Facades\Schema::hasTable('product_import_batches') ? $this->recentBatches : collect() as $batch)
                    <div class="border-b border-purple-50 py-3 last:border-0 text-sm">
                        <p class="font-semibold text-slate-800">
                            {{ \App\Models\ProductImportBatch::sourceLabels()[$batch->source] ?? $batch->source }}
                            <span class="text-xs font-normal text-slate-500">· {{ strtoupper($batch->mode) }}</span>
                        </p>
                        <p class="text-xs text-slate-500 mt-0.5">{{ $batch->created_at?->diffForHumans() }}</p>
                        <p class="text-xs text-emerald-700 mt-1">
                            +{{ $batch->created_count }} / ~{{ $batch->updated_count }} updated
                            @if($batch->failed_count)
                                · <span class="text-rose-600">{{ $batch->failed_count }} failed</span>
                            @endif
                        </p>
                    </div>
                @empty
                    <p class="text-sm text-slate-500">No imports yet. Run your first bulk import.</p>
                @endforelse
            </x-filament::section>

            <x-filament::section heading="Tips">
                <ul class="text-xs text-slate-600 space-y-2 list-disc pl-4">
                    <li>Export CSV from seller center (Daraz, Amazon Seller Central, Shopify Admin → Products → Export, Walmart Seller Center).</li>
                    <li><strong>Category URL:</strong> pick <em>All products on this link (auto)</em> or <em>Custom number</em> (e.g. 6850). Auto keeps paging until the listing ends (max {{ number_format(\App\Services\ProductImport\CategoryListingFetcher::maxLimit()) }} per run).</li>
                    <li>Shopify collections use the public <code>products.json</code> API (most reliable).</li>
                    <li>Amazon/Daraz category pages depend on visible HTML links; use CSV if a run finds zero products.</li>
                    <li><strong>Auto SEO:</strong> leave ON to rewrite product copy + meta tags for Beautage after each import (Smart SEO = no API key).</li>
                    <li><strong>Prices:</strong> default <em>PKR</em> keeps Rozzana/Daraz amounts as Rs. on your store. Only choose USD if the source site prices are in dollars.</li>
                    <li>Image fields must be full HTTPS URLs (same as manual product entry).</li>
                </ul>
            </x-filament::section>
        </div>
    </div>
</x-filament-panels::page>
