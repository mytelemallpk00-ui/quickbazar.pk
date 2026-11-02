<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\ProductBundleResource\Pages;
use App\Support\AdminPermissions;
use App\Models\Product;
use App\Models\ProductBundle;
use App\Support\MoneyFormat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\HtmlString;

class ProductBundleResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::BUNDLES;
    }

    protected static ?string $model = ProductBundle::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-plus';

    protected static ?string $navigationGroup = 'Bundle deals';

    protected static ?string $navigationLabel = 'All bundle deals';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Placeholder::make('storefront_visibility')
                    ->label('Where this shows on the site')
                    ->content(new HtmlString(
                        '<ul class="text-sm text-gray-600 dark:text-gray-400 list-disc ps-4 space-y-1">'
                        .'<li><strong>Shown:</strong> the main product\'s detail page only (<code>/products/{slug}</code>), below the buy box, when <em>Active</em> is on and at least one companion is set.</li>'
                        .'<li><strong>Not shown:</strong> home, collections, checkout, cart, blog, or companion products\' pages (unless that product is the main host).</li>'
                        .'</ul>'
                    ))
                    ->columnSpanFull(),
                Forms\Components\Section::make('Host product')
                    ->description('One bundle per main product. Customers see this block on that product\'s page.')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Main product (host)')
                            ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                            ->searchable()
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->live(),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Active on storefront')
                            ->default(true)
                            ->helperText('Inactive bundles are hidden on the product page.'),
                        Forms\Components\TextInput::make('badge_label')
                            ->label('Badge text')
                            ->placeholder('SAVE WITH BUNDLE DEALS')
                            ->maxLength(120),
                        Forms\Components\Textarea::make('subtitle')
                            ->label('Subtitle under heading')
                            ->rows(2)
                            ->placeholder('Combine this item with our bestselling companions.')
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Bundle discount')
                    ->schema([
                        Forms\Components\Select::make('discount_type')
                            ->label('Discount type')
                            ->options(ProductBundle::discountTypeOptions())
                            ->required()
                            ->native(false)
                            ->live(),
                        Forms\Components\TextInput::make('discount_value')
                            ->label(fn (Get $get): string => $get('discount_type') === ProductBundle::DISCOUNT_FIXED
                                ? 'Discount amount (Rs off total)'
                                : 'Discount percent (%)')
                            ->numeric()
                            ->minValue(0)
                            ->required()
                            ->suffix(fn (Get $get): ?string => $get('discount_type') === ProductBundle::DISCOUNT_FIXED ? 'Rs' : '%'),
                    ])->columns(2),
                Forms\Components\Section::make('Companion products')
                    ->description('The main product is always included and cannot be unchecked on the storefront.')
                    ->schema([
                        Forms\Components\Repeater::make('bundleItems')
                            ->relationship()
                            ->schema([
                                Forms\Components\Select::make('product_id')
                                    ->label('Companion product')
                                    ->options(function (Get $get): array {
                                        $hostId = $get('../../product_id');

                                        return Product::query()
                                            ->when($hostId, fn ($q) => $q->whereKeyNot($hostId))
                                            ->orderBy('name')
                                            ->pluck('name', 'id')
                                            ->all();
                                    })
                                    ->searchable()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->orderColumn('sort_order')
                            ->reorderable()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('updated_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('hostProduct.name')
                    ->label('Main product')
                    ->searchable()
                    ->sortable()
                    ->description(fn (ProductBundle $record): ?string => $record->hostProduct?->slug),
                Tables\Columns\TextColumn::make('bundleItems_count')
                    ->counts('bundleItems')
                    ->label('Companions')
                    ->alignCenter(),
                Tables\Columns\TextColumn::make('discount_type')
                    ->label('Discount')
                    ->formatStateUsing(fn (string $state, ProductBundle $record): string => $record->discount_type === ProductBundle::DISCOUNT_FIXED
                        ? 'Rs '.number_format($record->discount_value, 0).' off'
                        : number_format($record->discount_value, 0).'% off'),
                Tables\Columns\TextColumn::make('estimated_savings')
                    ->label('Est. savings (full bundle)')
                    ->state(fn (ProductBundle $record): string => MoneyFormat::pkr(
                        $record->calculateSavings($record->estimatedFullBundleSubtotal())
                    )),
                Tables\Columns\IconColumn::make('is_active')->boolean()->label('Live'),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active on storefront'),
                Tables\Filters\SelectFilter::make('product_id')
                    ->label('Main product')
                    ->relationship('hostProduct', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('viewOnStore')
                    ->label('View on site')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ProductBundle $record): ?string => $record->storefrontUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (ProductBundle $record): bool => $record->is_active && $record->storefrontUrl() !== null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('activate')
                        ->label('Activate selected')
                        ->icon('heroicon-o-eye')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(fn (ProductBundle $b) => $b->update(['is_active' => true]))),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('Deactivate selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each(fn (ProductBundle $b) => $b->update(['is_active' => false]))),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No bundle deals yet')
            ->emptyStateDescription('Create a deal to show “Frequently Bought Together” on a product detail page.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductBundles::route('/'),
            'create' => Pages\CreateProductBundle::route('/create'),
            'edit' => Pages\EditProductBundle::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['hostProduct', 'bundleItems']);
    }
}
