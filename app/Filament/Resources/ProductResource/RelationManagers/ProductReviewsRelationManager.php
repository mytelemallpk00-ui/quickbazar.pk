<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use App\Filament\Forms\AdminImageUpload;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class ProductReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'productReviews';

    protected static ?string $title = 'Customer reviews';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('author_name')->required()->maxLength(120),
                Forms\Components\TextInput::make('author_email')->email()->maxLength(255),
                Forms\Components\Select::make('rating')
                    ->options([
                        5 => '5 — Excellent',
                        4 => '4 — Good',
                        3 => '3 — Average',
                        2 => '2 — Poor',
                        1 => '1 — Bad',
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
                    ->default(true)
                    ->helperText('Only published reviews appear on the product page and count toward the star rating.'),
            ])->columns(2);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('author_name')
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('author_name')->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state).str_repeat('☆', 5 - $state)),
                Tables\Columns\TextColumn::make('body')->limit(50),
                Tables\Columns\IconColumn::make('is_verified_buyer')->boolean()->label('Verified'),
                Tables\Columns\IconColumn::make('is_published')->boolean()->label('Live'),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
