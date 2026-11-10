<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Filament\Concerns\InteractsWithGoogleIndex;
use App\Models\GoogleIndexLog;
use App\Models\Product;
use App\Services\GoogleIndex\ClaudeSeoPreflightService;
use App\Services\GoogleIndex\GoogleIndexService;
use App\Services\GoogleIndex\GoogleIndexUrl;
use App\Support\AdminPermissions;
use App\Support\GoogleIndexCredentialStore;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class SocialGoogleIndexing extends Page implements HasForms, HasTable
{
    use AuthorizesAdminPage;

    public static function canAccess(): bool
    {
        return false;
    }
    use InteractsWithForms;
    use InteractsWithGoogleIndex;
    use InteractsWithTable;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SOCIAL;
    }

    protected static ?string $navigationIcon = 'heroicon-o-rocket-launch';

    protected static ?string $navigationLabel = 'Google Search Indexing';

    protected static ?string $navigationGroup = 'Social Automation';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Force index to Google';

    protected static ?string $slug = 'social-google-indexing';

    protected static string $view = 'filament.pages.social-google-indexing';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public ?array $claudePreview = null;

    public bool $bulkRunning = false;

    /** @var array{success: int, failed: int, skipped: int, stopped_reason: ?string}|null */
    public ?array $bulkSummary = null;

    public function mount(): void
    {
        $this->form->fill([
            'custom_url' => '',
            'custom_action' => GoogleIndexLog::ACTION_UPDATED,
            'bulk_scope' => 'never_pushed',
            'bulk_limit' => 25,
            'bulk_only_in_stock' => false,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Push any URL')
                    ->description('Use for blog posts, CMS pages, or a single product URL.')
                    ->schema([
                        TextInput::make('custom_url')
                            ->label('Page URL')
                            ->placeholder('https://yoursite.com/products/slug or /help')
                            ->maxLength(2048)
                            ->columnSpanFull(),
                        Select::make('custom_action')
                            ->label('Action')
                            ->options([
                                GoogleIndexLog::ACTION_UPDATED => 'URL updated (request indexing)',
                                GoogleIndexLog::ACTION_DELETED => 'URL deleted (remove from index)',
                            ])
                            ->native(false),
                    ])
                    ->columns(2),
                Section::make('Bulk indexing')
                    ->description('Push many product URLs in one run. Google daily quota applies — use batches of 10–25.')
                    ->schema([
                        Select::make('bulk_scope')
                            ->label('Which products')
                            ->options([
                                'never_pushed' => 'Never successfully pushed',
                                'failed' => 'Last attempt failed',
                                'all' => 'All products (newest first)',
                            ])
                            ->native(false)
                            ->required(),
                        TextInput::make('bulk_limit')
                            ->label('Max URLs this run')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(200)
                            ->default(25)
                            ->required(),
                        Toggle::make('bulk_only_in_stock')
                            ->label('Only in-stock products')
                            ->default(false),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query()->orderByDesc('updated_at'))
            ->heading('Catalog products')
            ->description('Push product pages after SEO is complete. Status reflects the latest API attempt for that URL.')
            ->columns([
                TextColumn::make('name')->searchable()->limit(40),
                TextColumn::make('slug')->toggleable()->copyable(),
                TextColumn::make('google_index_status')
                    ->label('Index status')
                    ->badge()
                    ->state(function (Product $record): string {
                        $log = app(GoogleIndexService::class)->latestLogForUrl(GoogleIndexUrl::forProduct($record));

                        if ($log === null) {
                            return 'Never pushed';
                        }

                        return $log->isSuccess()
                            ? 'Pushed '.$log->created_at?->diffForHumans()
                            : 'Failed '.$log->created_at?->diffForHumans();
                    })
                    ->color(fn (string $state): string => match (true) {
                        str_starts_with($state, 'Pushed') => 'success',
                        str_starts_with($state, 'Failed') => 'danger',
                        default => 'gray',
                    }),
            ])
            ->selectable()
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('bulkPushSelected')
                        ->label('Push selected to Google')
                        ->icon('heroicon-o-rocket-launch')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Bulk push selected products?')
                        ->modalDescription('Each URL is sent to the Indexing API with a short delay. Quota limits may stop the batch early.')
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records): void {
                            $this->bulkRunning = true;
                            $summary = $this->bulkPushProductsToGoogle($records);
                            $this->bulkSummary = $summary;
                            $this->bulkRunning = false;
                            $this->notifyGoogleIndexBulkSummary($summary);
                        }),
                ]),
            ])
            ->actions([
                Action::make('claudeReview')
                    ->label('AI SEO check')
                    ->icon('heroicon-o-sparkles')
                    ->color('info')
                    ->visible(fn (): bool => app(ClaudeSeoPreflightService::class)->isConfigured())
                    ->action(function (Product $record): void {
                        $this->claudePreview = $this->analyzeProductSeoWithClaude($record);
                        if ($this->claudePreview !== null) {
                            Notification::make()
                                ->title('Claude SEO score: '.$this->claudePreview['score'].'/100')
                                ->body($this->claudePreview['summary'])
                                ->{$this->claudePreview['ready'] ? 'success' : 'warning'}()
                                ->duration(10000)
                                ->send();
                        }
                    }),
                Action::make('pushGoogle')
                    ->label('Push to Google')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Send URL to Google Indexing API?')
                    ->modalDescription(fn (Product $record): string => GoogleIndexUrl::forProduct($record))
                    ->action(fn (Product $record) => $this->pushProductToGoogle($record)),
                Action::make('removeGoogle')
                    ->label('Notify deleted')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Product $record) => $this->pushProductToGoogle($record, GoogleIndexLog::ACTION_DELETED)),
            ])
            ->paginated([10, 25, 50]);
    }

    public function runBulkIndexing(): void
    {
        if (! GoogleIndexCredentialStore::isConfigured()) {
            Notification::make()
                ->title('Setup required')
                ->body('Upload the Google service account JSON under Connect accounts → Google Indexing.')
                ->warning()
                ->send();

            return;
        }

        $state = $this->form->getState();
        $limit = min(200, max(1, (int) ($state['bulk_limit'] ?? 25)));
        $scope = (string) ($state['bulk_scope'] ?? 'never_pushed');
        $inStockOnly = (bool) ($state['bulk_only_in_stock'] ?? false);

        $query = Product::query()->orderByDesc('updated_at');
        if ($inStockOnly) {
            $query->where('in_stock', true);
        }

        $candidates = $query->limit($scope === 'all' ? $limit : 500)->get();
        $service = app(GoogleIndexService::class);

        $filtered = $candidates->filter(function (Product $product) use ($scope, $service): bool {
            $log = $service->latestLogForUrl(GoogleIndexUrl::forProduct($product));

            return match ($scope) {
                'failed' => $log !== null && ! $log->isSuccess(),
                'never_pushed' => $log === null || ! $log->isSuccess(),
                default => true,
            };
        })->take($limit);

        if ($filtered->isEmpty()) {
            Notification::make()
                ->title('Nothing to push')
                ->body('No products matched this bulk filter.')
                ->warning()
                ->send();

            return;
        }

        $this->bulkRunning = true;
        $this->bulkSummary = $this->bulkPushProductsToGoogle($filtered);
        $this->bulkRunning = false;
        $this->notifyGoogleIndexBulkSummary($this->bulkSummary);
    }

    public function pushCustomUrl(): void
    {
        $state = $this->form->getState();
        $url = trim((string) ($state['custom_url'] ?? ''));
        if ($url === '') {
            Notification::make()->title('Enter a URL first')->warning()->send();

            return;
        }

        $this->pushUrlToGoogle($url, (string) ($state['custom_action'] ?? GoogleIndexLog::ACTION_UPDATED));
    }

    public function getSetupStatusProperty(): array
    {
        return [
            'service_account' => GoogleIndexCredentialStore::isConfigured(),
            'client_email' => GoogleIndexCredentialStore::clientEmail(),
            'claude' => app(ClaudeSeoPreflightService::class)->isConfigured(),
        ];
    }

    /** @return \Illuminate\Database\Eloquent\Collection<int, GoogleIndexLog> */
    public function getRecentLogsProperty()
    {
        return GoogleIndexLog::query()->latest('id')->limit(8)->get();
    }

    public function getSubheading(): ?string
    {
        return 'Indexing API for job postings & live URLs — service account must be Owner in Search Console';
    }

    public function getHeaderWidgetsProperty(): HtmlString
    {
        $ready = GoogleIndexCredentialStore::isConfigured();

        return new HtmlString(
            '<div class="rounded-xl border p-4 text-sm '.($ready ? 'border-success-300 bg-success-50 text-success-900' : 'border-warning-300 bg-warning-50 text-warning-900').'">'
            .($ready
                ? '<strong>API ready.</strong> Service account: '.e((string) GoogleIndexCredentialStore::clientEmail())
                : '<strong>Setup required.</strong> Upload JSON under <em>Connect accounts → Google Indexing</em> tab, then add that email as Owner in Google Search Console.')
            .'</div>'
        );
    }
}
