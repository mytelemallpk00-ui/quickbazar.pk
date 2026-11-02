<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Resources\ProgrammaticSeoLandingResource\Pages;
use App\Models\Category;
use App\Models\ProgrammaticSeoLanding;
use App\Support\AdminPermissions;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ProgrammaticSeoLandingResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATALOG;
    }

    protected static ?string $model = ProgrammaticSeoLanding::class;

    protected static ?string $navigationIcon = 'heroicon-o-map-pin';

    protected static ?string $navigationGroup = 'AI SEO';

    protected static ?string $navigationLabel = 'pSEO landing pages';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'pSEO landing page';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Targeting')
                ->schema([
                    Forms\Components\TextInput::make('slug')->required()->maxLength(120)->unique(ignoreRecord: true),
                    Forms\Components\Select::make('category_id')
                        ->label('Category filter')
                        ->options(fn () => Category::query()->orderBy('name')->pluck('name', 'id'))
                        ->searchable(),
                    Forms\Components\TextInput::make('location')->placeholder('Karachi')->maxLength(120),
                    Forms\Components\TextInput::make('use_case')->placeholder('Bridal glow')->maxLength(120),
                    Forms\Components\Toggle::make('is_published')->default(true),
                    Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                ])->columns(2),
            Forms\Components\Section::make('On-page SEO')
                ->description('Tokens: {location}, {category}, {use_case}, {brand}')
                ->schema([
                    Forms\Components\TextInput::make('headline')->required()->columnSpanFull(),
                    Forms\Components\TextInput::make('meta_title')->maxLength(70)->columnSpanFull(),
                    Forms\Components\Textarea::make('meta_description')->rows(2)->maxLength(320)->columnSpanFull(),
                    Forms\Components\RichEditor::make('intro_html')->columnSpanFull(),
                    Forms\Components\TextInput::make('robots')->default('index,follow'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('headline')->searchable()->limit(40),
                Tables\Columns\TextColumn::make('slug')->copyable(),
                Tables\Columns\TextColumn::make('location')->toggleable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')->toggleable(),
                Tables\Columns\IconColumn::make('is_published')->boolean(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->defaultSort('sort_order')
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Live')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (ProgrammaticSeoLanding $record): string => route('pseo.show', $record))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProgrammaticSeoLandings::route('/'),
            'create' => Pages\CreateProgrammaticSeoLanding::route('/create'),
            'edit' => Pages\EditProgrammaticSeoLanding::route('/{record}/edit'),
        ];
    }
}
