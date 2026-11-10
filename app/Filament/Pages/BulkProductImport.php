<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\Category;
use App\Support\AdminPermissions;
use App\Models\ProductImportBatch;
use App\Services\ProductImport\CategoryListingFetcher;
use App\Services\ProductImport\ImportSchemaSetup;
use App\Services\ProductImport\ProductImportService;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class BulkProductImport extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATALOG;
    }

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-tray';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Bulk import';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Bulk product import';

    protected static ?string $slug = 'bulk-product-import';

    protected static string $view = 'filament.pages.bulk-product-import';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    /** @var array<string, mixed>|null */
    public ?array $lastResult = null;

    public function mount(): void
    {
        try {
            ImportSchemaSetup::ensure();
        } catch (\Throwable $e) {
            report($e);
        }

        $this->form->fill([
            'source' => ProductImportBatch::SOURCE_SHOPIFY,
            'mode' => 'csv',
            'category_id' => Category::query()->orderBy('name')->value('id') ?? null,
            'update_existing' => true,
            'ai_seo' => true,
            'ai_provider' => 'smart',
            'currency' => 'pkr',
            'category_limit_mode' => 'auto',
            'product_limit' => 100,
            'url_limit_mode' => 'auto',
            'url_product_limit' => 100,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('source')
                    ->label('Marketplace')
                    ->options(ProductImportBatch::sourceLabels())
                    ->required()
                    ->native(false)
                    ->live(),
                Radio::make('mode')
                    ->label('Import method')
                    ->options([
                        'csv' => 'CSV / Excel export file',
                        'url' => 'Product URLs (one per line)',
                        'category' => 'Category / collection page URL',
                    ])
                    ->columns(1)
                    ->live(),
                FileUpload::make('csv_file')
                    ->label('Upload CSV')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel', '.csv'])
                    ->maxSize(8192)
                    ->directory('imports')
                    ->visibility('private')
                    ->visible(fn (Get $get): bool => $get('mode') === 'csv')
                    ->required(fn (Get $get): bool => $get('mode') === 'csv'),
                Textarea::make('urls')
                    ->label('Product URLs')
                    ->rows(8)
                    ->placeholder("https://www.daraz.pk/products/...\nhttps://www.amazon.com/dp/...\nhttps://your-store.myshopify.com/products/...\nhttps://www.walmart.com/ip/...")
                    ->helperText('One product URL per line. Shopify links use the public products.json API when available.')
                    ->visible(fn (Get $get): bool => $get('mode') === 'url')
                    ->required(fn (Get $get): bool => $get('mode') === 'url'),
                Radio::make('url_limit_mode')
                    ->label('How many URLs to import')
                    ->options([
                        'auto' => 'All URLs in the box (auto)',
                        'custom' => 'Custom number',
                    ])
                    ->default('auto')
                    ->inline()
                    ->live()
                    ->visible(fn (Get $get): bool => $get('mode') === 'url'),
                TextInput::make('url_product_limit')
                    ->label('Custom URL count')
                    ->numeric()
                    ->minValue(1)
                    ->default(100)
                    ->helperText('Process only this many URLs from the top of your list.')
                    ->visible(fn (Get $get): bool => $get('mode') === 'url' && $get('url_limit_mode') === 'custom')
                    ->required(fn (Get $get): bool => $get('mode') === 'url' && $get('url_limit_mode') === 'custom'),
                TextInput::make('category_list_url')
                    ->label('Category or collection page URL')
                    ->url()
                    ->placeholder('https://www.daraz.pk/beauty-health/  ·  https://www.daraz.pk/catalog/?q=serum  ·  https://store.myshopify.com/collections/skincare')
                    ->helperText('Daraz: paste the full category or catalog search URL from your browser (e.g. beauty-health or catalog/?q=…). Shopify: use /collections/…')
                    ->visible(fn (Get $get): bool => $get('mode') === 'category')
                    ->required(fn (Get $get): bool => $get('mode') === 'category')
                    ->columnSpanFull(),
                Radio::make('category_limit_mode')
                    ->label('How many products to import')
                    ->options([
                        'auto' => 'All products on this link (auto)',
                        'custom' => 'Custom number',
                    ])
                    ->default('auto')
                    ->inline()
                    ->live()
                    ->helperText('Auto crawls every page of the collection/category until no more products are found (server safety cap: '.number_format(CategoryListingFetcher::maxLimit()).' per run).')
                    ->visible(fn (Get $get): bool => $get('mode') === 'category'),
                TextInput::make('product_limit')
                    ->label('Custom product count')
                    ->numeric()
                    ->minValue(1)
                    ->default(100)
                    ->helperText('Example: 6850 — imports up to that many products from the listing.')
                    ->visible(fn (Get $get): bool => $get('mode') === 'category' && $get('category_limit_mode') === 'custom')
                    ->required(fn (Get $get): bool => $get('mode') === 'category' && $get('category_limit_mode') === 'custom'),
                Select::make('category_id')
                    ->label('Default category')
                    ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->helperText('Applied when the file/URL has no category column.'),
                Select::make('currency')
                    ->label('Prices (PKR — Rs.)')
                    ->options([
                        'pkr' => 'PKR — same amounts as source (Rozzana, Daraz, etc.)',
                        'usd' => 'USD on source → convert to PKR (×'.number_format((float) config('services.product_import.usd_to_pkr', 280), 0).')',
                    ])
                    ->default('pkr')
                    ->native(false)
                    ->helperText('Store shows Rs. Prices are saved as whole rupees (no paisa).'),
                Toggle::make('update_existing')
                    ->label('Update existing products (match by SKU or source URL)')
                    ->default(true),
                Toggle::make('ai_seo')
                    ->label('Auto SEO & rewrite copy after import (recommended)')
                    ->helperText('Rewrites title, meta, description and detail HTML for Quick Bazar, then saves the product live. Smart SEO works without API keys; OpenAI/Gemini need keys under Settings → AI integration. Slower on large batches.')
                    ->default(true),
                TextInput::make('ai_target_keyword')
                    ->label('Default target SEO keyword (optional)')
                    ->maxLength(80)
                    ->visible(fn (Get $get): bool => (bool) $get('ai_seo'))
                    ->helperText('Applied to each imported product when generating SEO copy.'),
                Select::make('ai_provider')
                    ->label('SEO fill mode')
                    ->options([
                        'smart' => 'Smart SEO (no API key)',
                        'gemini_flash' => 'Gemini Flash (free API tier)',
                        'gemini_pro' => 'Gemini Pro',
                        'openai' => 'OpenAI',
                    ])
                    ->default('smart')
                    ->native(false)
                    ->visible(fn (Get $get): bool => (bool) $get('ai_seo')),
            ])
            ->statePath('data');
    }

    public function runImport(ProductImportService $importer): void
    {
        try {
            $this->runImportInternal($importer);
        } catch (\Throwable $e) {
            report($e);
            Notification::make()
                ->title('Import failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function runImportInternal(ProductImportService $importer): void
    {
        $state = $this->form->getState();
        $source = $state['source'];
        $options = [
            'category_id' => $state['category_id'] ?? null,
            'update_existing' => (bool) ($state['update_existing'] ?? true),
            'currency' => $state['currency'] ?? 'keep',
            'ai_seo' => (bool) ($state['ai_seo'] ?? true),
            'ai_target_keyword' => trim((string) ($state['ai_target_keyword'] ?? '')) ?: null,
            'ai_provider' => (string) ($state['ai_provider'] ?? 'auto'),
        ];

        if ($options['ai_seo']) {
            @set_time_limit(max(600, min(7200, 900)));
        }

        if ($state['mode'] === 'csv') {
            $path = is_array($state['csv_file'] ?? null) ? ($state['csv_file'][0] ?? null) : ($state['csv_file'] ?? null);
            if (! $path) {
                Notification::make()->title('Upload a CSV file first.')->danger()->send();

                return;
            }

            $absolute = Storage::disk('local')->path($path);
            $result = $importer->importCsv($source, $absolute, basename($path), $options);
        } elseif ($state['mode'] === 'category') {
            $listingUrl = trim((string) ($state['category_list_url'] ?? ''));
            if ($listingUrl === '') {
                Notification::make()->title('Paste a category or collection page URL.')->danger()->send();

                return;
            }

            if ($this->looksLikeShopifyCollectionUrl($listingUrl)) {
                $source = ProductImportBatch::SOURCE_SHOPIFY;
            }

            $limit = $this->resolveCategoryLimit($state);
            @set_time_limit(max(600, min(7200, $limit * 4)));
            $autoAll = ($state['category_limit_mode'] ?? 'auto') === 'auto';
            $result = $importer->importCategoryListing($source, $listingUrl, $limit, $options, $autoAll);
        } else {
            $urls = preg_split('/\r\n|\r|\n/', (string) ($state['urls'] ?? '')) ?: [];
            $urls = array_values(array_filter(array_map('trim', $urls)));
            $limit = $this->resolveUrlLimit($state, count($urls));
            if ($limit < count($urls)) {
                $urls = array_slice($urls, 0, $limit);
            }

            if ($urls === []) {
                Notification::make()->title('Paste at least one product URL.')->danger()->send();

                return;
            }

            $result = $importer->importUrls($source, $urls, $options);
        }

        $this->lastResult = [
            'total' => $result->total,
            'created' => $result->created,
            'updated' => $result->updated,
            'failed' => $result->failedCount(),
            'failures' => array_slice($result->failures, 0, 15),
        ];

        $summary = "Created {$result->created}, updated {$result->updated}, failed {$result->failedCount()}.";
        $firstFailure = $result->failures[0]['message'] ?? null;
        if ($firstFailure) {
            $summary .= ' '.$firstFailure;
        }

        $notification = Notification::make()
            ->title('Import finished')
            ->body($summary);

        if ($result->created === 0 && $result->updated === 0 && $result->failedCount() > 0) {
            $notification->warning();
        } else {
            $notification->success();
        }

        $notification->send();

        $clear = [
            'csv_file' => null,
            'urls' => null,
        ];
        if ($result->created > 0 || $result->updated > 0) {
            $clear['category_list_url'] = null;
        }
        $this->form->fill(array_merge($state, $clear));
    }

    private function looksLikeShopifyCollectionUrl(string $url): bool
    {
        return str_contains($url, '/collections/')
            || str_contains($url, 'myshopify.com')
            || preg_match('~^https?://[^/]+/products~i', $url) === 1;
    }

    /** @param  array<string, mixed>  $state */
    private function resolveCategoryLimit(array $state): int
    {
        if (($state['category_limit_mode'] ?? 'auto') === 'auto') {
            return CategoryListingFetcher::maxLimit();
        }

        return CategoryListingFetcher::normalizeLimit((int) ($state['product_limit'] ?? 1));
    }

    /** @param  array<string, mixed>  $state */
    private function resolveUrlLimit(array $state, int $pastedCount): int
    {
        if (($state['url_limit_mode'] ?? 'auto') === 'auto') {
            return max(1, min(CategoryListingFetcher::maxLimit(), $pastedCount));
        }

        return CategoryListingFetcher::normalizeLimit((int) ($state['url_product_limit'] ?? 1));
    }

    public function downloadTemplate(ProductImportService $importer): StreamedResponse
    {
        $source = $this->form->getState()['source'] ?? ProductImportBatch::SOURCE_SHOPIFY;
        $csv = $importer->makeTemplateCsv($source);
        $filename = $source.'-beautage-import-template.csv';

        return response()->streamDownload(function () use ($csv): void {
            echo $csv;
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, ProductImportBatch> */
    public function getRecentBatchesProperty()
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('product_import_batches')) {
            return collect();
        }

        return ProductImportBatch::query()->latest()->limit(8)->get();
    }
}
