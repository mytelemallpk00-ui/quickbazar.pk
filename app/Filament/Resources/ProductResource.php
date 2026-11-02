<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Forms\AdminImageUpload;
use App\Support\AdminPermissions;
use App\Support\AiSettings;
use App\Filament\Forms\SeoSection;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers\ProductReviewsRelationManager;
use App\Models\Category;
use App\Models\Product;
use App\Models\GoogleIndexLog;
use App\Services\GoogleIndex\GoogleIndexException;
use App\Services\GoogleIndex\GoogleIndexService;
use App\Services\GoogleIndex\GoogleIndexUrl;
use App\Services\ProductAi\ProductAiService;
use Filament\Notifications\Notification;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProductResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATALOG;
    }

    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?string $navigationGroup = 'Catalog';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Product')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Basics')
                            ->icon('heroicon-o-cube')
                            ->schema([
                                Forms\Components\Section::make('AI SEO — auto-fill product & Google fields')
                                    ->description('Smart SEO works with no API key. Gemini Flash (free tier) & Pro need a key from Settings → AI integration.')
                                    ->icon('heroicon-o-sparkles')
                                    ->extraAttributes(['class' => 'beautage-product-ai-panel'])
                                    ->schema([
                                        Forms\Components\Placeholder::make('ai_provider_status')
                                            ->label('Status')
                                            ->content(function (): \Illuminate\Support\HtmlString {
                                                $parts = ['✓ Smart SEO (no key)'];
                                                $parts[] = AiSettings::hasGeminiKey()
                                                    ? '✓ Gemini API key saved'
                                                    : '○ Gemini — add key in <a href="'.route('filament.admin.pages.ai-integration').'" class="text-primary-600 underline">AI integration</a>';
                                                $parts[] = AiSettings::hasOpenAiKey()
                                                    ? '✓ OpenAI key saved'
                                                    : '○ OpenAI optional';

                                                return new \Illuminate\Support\HtmlString(
                                                    '<p class="text-sm text-gray-600 dark:text-gray-400">'.implode(' · ', $parts).'</p>'
                                                );
                                            })
                                            ->columnSpanFull(),
                                        Forms\Components\TextInput::make('ai_target_keyword')
                                            ->label('Target SEO keyword (optional)')
                                            ->maxLength(80)
                                            ->dehydrated(false)
                                            ->helperText('Example: beard growth oil Pakistan')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('ai_specs_context')
                                            ->label('Specs / features for AI (optional)')
                                            ->rows(5)
                                            ->dehydrated(false)
                                            ->placeholder('Paste Daraz/Amazon bullet points or ingredients here.')
                                            ->columnSpanFull(),
                                        Forms\Components\Actions::make([
                                            Forms\Components\Actions\Action::make('formGenerateSmart')
                                                ->label('Smart SEO (Free · no API key)')
                                                ->icon('heroicon-o-check-badge')
                                                ->color('success')
                                                ->extraAttributes(['type' => 'button'])
                                                ->action(fn ($livewire) => $livewire->runProductAiGeneration(ProductAiService::PROVIDER_SMART)),
                                            Forms\Components\Actions\Action::make('formGenerateGeminiFlash')
                                                ->label('Gemini Flash (Free API tier)')
                                                ->icon('heroicon-o-bolt')
                                                ->color('info')
                                                ->extraAttributes(['type' => 'button'])
                                                ->requiresConfirmation()
                                                ->action(fn ($livewire) => $livewire->runProductAiGeneration(ProductAiService::PROVIDER_GEMINI_FLASH)),
                                            Forms\Components\Actions\Action::make('formGenerateGeminiPro')
                                                ->label('Gemini Pro')
                                                ->icon('heroicon-o-sparkles')
                                                ->color('warning')
                                                ->extraAttributes(['type' => 'button'])
                                                ->requiresConfirmation()
                                                ->modalDescription('Requires a Google AI Studio API key (not Gmail login). Add it under Settings → AI integration.')
                                                ->action(fn ($livewire) => $livewire->runProductAiGeneration(ProductAiService::PROVIDER_GEMINI_PRO)),
                                            Forms\Components\Actions\Action::make('formGenerateOpenAi')
                                                ->label('OpenAI')
                                                ->icon('heroicon-o-cpu-chip')
                                                ->color('gray')
                                                ->extraAttributes(['type' => 'button'])
                                                ->requiresConfirmation()
                                                ->action(fn ($livewire) => $livewire->runProductAiGeneration(ProductAiService::PROVIDER_OPENAI)),
                                        ])
                                            ->columnSpanFull()
                                            ->extraAttributes(['class' => 'beautage-product-ai-actions']),
                                    ])
                                    ->columns(1)
                                    ->columnSpanFull(),
                                Forms\Components\Placeholder::make('product_details_divider')
                                    ->label('')
                                    ->content(new \Illuminate\Support\HtmlString('<div class="beautage-product-form-divider"><span>Product details</span></div>'))
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('name')->required()->live(onBlur: true),
                                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                                Forms\Components\Select::make('category_id')
                                    ->label('Category')
                                    ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Textarea::make('short_description')
                                    ->label('Short description (product page)')
                                    ->rows(4)
                                    ->maxLength(600)
                                    ->helperText('Shown under the product title on the detail page only. Keep it to a few lines (not the full spec sheet).')
                                    ->columnSpanFull(),
                                Forms\Components\Textarea::make('description')
                                    ->label('Listing summary')
                                    ->required()
                                    ->rows(3)
                                    ->maxLength(320)
                                    ->helperText('Short blurb for collection grids, search, and quick view cards.')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('promo_label')
                                    ->label('Promo badge')
                                    ->placeholder('Save 30% Today'),
                                Forms\Components\TextInput::make('units_left')
                                    ->label('Units left (urgency)')
                                    ->numeric()
                                    ->minValue(0),
                                Forms\Components\TextInput::make('video_url')
                                    ->label('Product video URL (YouTube embed)')
                                    ->url()
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Detail page')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Section::make('Description tab')
                                    ->schema([
                                        Forms\Components\TextInput::make('detail_heading')
                                            ->label('Heading')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('detail_body')
                                            ->label('Body')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                        Forms\Components\Repeater::make('detail_highlights')
                                            ->label('Feature highlights')
                                            ->schema([
                                                Forms\Components\TextInput::make('title')->required(),
                                                Forms\Components\Textarea::make('description')->required()->rows(2),
                                            ])
                                            ->columns(1)
                                            ->columnSpanFull()
                                            ->defaultItems(0)
                                            ->collapsible(),
                                    ]),
                                Forms\Components\Section::make('Specifications tab')
                                    ->schema([
                                        Forms\Components\TextInput::make('specs_heading')
                                            ->label('Heading')
                                            ->columnSpanFull(),
                                        Forms\Components\Repeater::make('specs_rows')
                                            ->label('Spec rows')
                                            ->schema([
                                                Forms\Components\TextInput::make('label')->required(),
                                                Forms\Components\TextInput::make('value')->required(),
                                            ])
                                            ->columns(2)
                                            ->columnSpanFull()
                                            ->defaultItems(0)
                                            ->collapsible(),
                                    ]),
                                Forms\Components\Section::make('Routine tab')
                                    ->schema([
                                        Forms\Components\TextInput::make('routine_heading')
                                            ->label('Heading')
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('routine_body')
                                            ->label('Instructions')
                                            ->rows(5)
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Forms\Components\Tabs\Tab::make('Pricing & badges')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\TextInput::make('price')->numeric()->required()->prefix('Rs'),
                                Forms\Components\TextInput::make('old_price')->numeric()->required()->prefix('Rs'),
                                Forms\Components\TextInput::make('rating')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false)
                                    ->helperText('Auto-calculated from published reviews.'),
                                Forms\Components\TextInput::make('reviews')
                                    ->label('Review count')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated(false),
                                Forms\Components\TextInput::make('badge'),
                                Forms\Components\Toggle::make('in_stock')->default(true),
                                Forms\Components\TextInput::make('stock_quantity')
                                    ->label('Stock quantity')
                                    ->numeric()
                                    ->minValue(0)
                                    ->helperText('Leave empty to skip low-stock tracking for this SKU.'),
                                Forms\Components\TextInput::make('reorder_cycle_days')
                                    ->label('Re-order cycle (days)')
                                    ->numeric()
                                    ->minValue(7)
                                    ->helperText('Overrides store automation default for this product.'),
                                Forms\Components\Toggle::make('is_flash'),
                                Forms\Components\TextInput::make('discount'),
                                Forms\Components\TextInput::make('claimed_percent')->numeric(),
                                Forms\Components\TextInput::make('claimed_text'),
                            ])->columns(3),
                        Forms\Components\Tabs\Tab::make('Images')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                AdminImageUpload::make('image', AdminImageUpload::PRESET_PRODUCT_MAIN)
                                    ->label('Main product image')
                                    ->required(),
                                AdminImageUpload::make('hover_image', AdminImageUpload::PRESET_PRODUCT_HOVER)
                                    ->label('Hover image')
                                    ->required(),
                                AdminImageUpload::make('gallery_images', AdminImageUpload::PRESET_PRODUCT_GALLERY, multiple: true)
                                    ->label('Gallery images')
                                    ->columnSpanFull(),
                            ]),
                        SeoSection::tab(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('price')
                    ->formatStateUsing(fn ($state): string => \App\Support\MoneyFormat::pkr($state))
                    ->sortable(),
                Tables\Columns\IconColumn::make('in_stock')->boolean()->label('Stock'),
                Tables\Columns\TextColumn::make('meta_title')->label('SEO title')->limit(30)->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\Action::make('pushGoogleIndex')
                    ->label('Google Index')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(fn (Product $record): string => GoogleIndexUrl::forProduct($record))
                    ->action(function (Product $record): void {
                        try {
                            $result = app(GoogleIndexService::class)->publish(
                                GoogleIndexUrl::forProduct($record),
                                GoogleIndexLog::ACTION_UPDATED,
                                $record,
                                auth()->user(),
                            );
                            Notification::make()->title('Sent to Google')->body($result['message'])->success()->send();
                        } catch (GoogleIndexException $e) {
                            Notification::make()->title('Indexing failed')->body($e->getMessage())->danger()->send();
                        }
                    }),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ProductReviewsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
