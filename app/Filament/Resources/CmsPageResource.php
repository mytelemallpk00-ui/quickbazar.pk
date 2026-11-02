<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\CmsPageResource\Pages;
use App\Support\AdminPermissions;
use App\Filament\Resources\CmsPageResource\RelationManagers;
use App\Models\CmsPage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CmsPageResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $model = CmsPage::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'All CMS pages';

    protected static ?int $navigationSort = 99;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('slug')
                    ->required(),
                Forms\Components\TextInput::make('title')
                    ->required(),
                Forms\Components\Select::make('page_type')
                    ->options(CmsPage::pageTypeLabels())
                    ->required(),
                Forms\Components\TextInput::make('hero_badge'),
                Forms\Components\TextInput::make('hero_title'),
                Forms\Components\Textarea::make('hero_subtitle')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('hero_stat_value'),
                Forms\Components\TextInput::make('hero_stat_label'),
                Forms\Components\Textarea::make('hero_buttons')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('content_html')
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('sections')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('meta_title'),
                Forms\Components\Textarea::make('meta_description')
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_published')
                    ->required(),
                Forms\Components\TextInput::make('sort_order')
                    ->required()
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('slug')
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('page_type')
                    ->formatStateUsing(fn (string $state): string => CmsPage::pageTypeLabels()[$state] ?? $state)
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hero_badge')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hero_title')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hero_stat_value')
                    ->searchable(),
                Tables\Columns\TextColumn::make('hero_stat_label')
                    ->searchable(),
                Tables\Columns\TextColumn::make('meta_title')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('page_type')
                    ->options(CmsPage::pageTypeLabels()),
            ])
            ->actions([
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCmsPages::route('/'),
            'create' => Pages\CreateCmsPage::route('/create'),
            'edit' => Pages\EditCmsPage::route('/{record}/edit'),
        ];
    }
}
