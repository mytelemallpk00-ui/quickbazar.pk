<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Forms\AdminImageUpload;
use App\Support\AdminPermissions;
use App\Filament\Resources\ProductReviewResource\Pages;
use App\Models\Product;
use App\Models\ProductReview;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ProductReviewResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::REVIEWS;
    }

    protected static ?string $model = ProductReview::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Customer Reviews';

    protected static ?string $navigationLabel = 'All reviews';

    protected static ?int $navigationSort = 1;

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::query()->where('is_published', false)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Product')
                    ->options(fn () => Product::query()->orderBy('name')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('author_name')->required()->maxLength(120),
                Forms\Components\TextInput::make('author_email')->email()->maxLength(255),
                Forms\Components\Select::make('rating')
                    ->options([
                        5 => '5 stars',
                        4 => '4 stars',
                        3 => '3 stars',
                        2 => '2 stars',
                        1 => '1 star',
                    ])
                    ->required()
                    ->native(false),
                Forms\Components\TextInput::make('title')->maxLength(200)->columnSpanFull(),
                Forms\Components\Textarea::make('body')->required()->rows(4)->columnSpanFull(),
                AdminImageUpload::make('photos', AdminImageUpload::PRESET_REVIEW_PHOTO, multiple: true)
                    ->label('Review photos')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_verified_buyer')->label('Verified buyer'),
                Forms\Components\Toggle::make('is_published')
                    ->label('Published on storefront')
                    ->helperText('Only published reviews are visible to shoppers.'),
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('product.name')
                    ->label('Product')
                    ->searchable()
                    ->sortable()
                    ->limit(28),
                Tables\Columns\TextColumn::make('author_name')->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->label('Stars')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state)),
                Tables\Columns\ImageColumn::make('photos')
                    ->label('Photo')
                    ->getStateUsing(fn (ProductReview $record): ?string => $record->photoUrls()[0] ?? null)
                    ->square()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('body')->limit(40)->toggleable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Live')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Published')
                    ->placeholder('All reviews')
                    ->trueLabel('Published only')
                    ->falseLabel('Pending approval'),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (ProductReview $record): bool => ! $record->is_published)
                    ->action(function (ProductReview $record): void {
                        $record->update(['is_published' => true]);
                        Notification::make()->title('Review approved')->success()->send();
                    }),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approveSelected')
                        ->label('Approve selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(fn (ProductReview $review) => $review->update(['is_published' => true]));
                            Notification::make()
                                ->title('Selected reviews approved')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\BulkAction::make('unpublishSelected')
                        ->label('Unpublish selected')
                        ->icon('heroicon-o-eye-slash')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function (Collection $records): void {
                            $records->each(fn (ProductReview $review) => $review->update(['is_published' => false]));
                            Notification::make()
                                ->title('Selected reviews unpublished')
                                ->success()
                                ->send();
                        }),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProductReviews::route('/'),
            'create' => Pages\CreateProductReview::route('/create'),
            'edit' => Pages\EditProductReview::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('product');
    }
}
