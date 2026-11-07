<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Forms\AdminImageUpload;
use App\Support\AdminPermissions;
use App\Filament\Forms\SeoSection;
use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATEGORIES;
    }

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $navigationGroup = 'Categories';

    protected static ?string $navigationLabel = 'All categories';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Category')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Details')
                            ->schema([
                                Forms\Components\TextInput::make('name')->required()->live(onBlur: true),
                                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                                Forms\Components\Textarea::make('description')->rows(4)->columnSpanFull(),
                                AdminImageUpload::make('image_url', AdminImageUpload::PRESET_CATEGORY)
                                    ->label('Category image'),
                                Forms\Components\Toggle::make('is_featured')->label('Featured category'),
                                Forms\Components\Toggle::make('is_active')->default(true),
                                Forms\Components\TextInput::make('sort_order')->numeric()->default(0),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Header menu')
                            ->icon('heroicon-o-bars-3')
                            ->schema([
                                Forms\Components\Placeholder::make('nav_hint')
                                    ->label('')
                                    ->content('These links fill this category’s column inside the Collections mega menu. Add the category as a column under Categories → Header menu.')
                                    ->columnSpanFull(),
                                Forms\Components\TextInput::make('nav_label')
                                    ->label('Menu label override')
                                    ->helperText('Optional. Leave blank to use category name.'),
                                Forms\Components\TextInput::make('icon')
                                    ->label('Lucide icon')
                                    ->placeholder('droplet')
                                    ->helperText('Examples: droplet, zap, scissors, flower-2, heart-pulse, sparkles'),
                                Forms\Components\TextInput::make('nav_icon_color')
                                    ->label('Icon color class')
                                    ->placeholder('text-beautage-pink')
                                    ->helperText('Tailwind text color class for the icon in the menu.'),
                                Forms\Components\Repeater::make('nav_links')
                                    ->label('Quick links (small dropdown)')
                                    ->schema([
                                        Forms\Components\TextInput::make('label')->required(),
                                        Forms\Components\TextInput::make('url')
                                            ->label('URL or collection slug')
                                            ->helperText('Full URL, /collections, or category slug for ?cat=')
                                            ->required(),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->defaultItems(0)
                                    ->collapsible(),
                                Forms\Components\Section::make('Collections mega menu promo')
                                    ->schema([
                                        Forms\Components\TextInput::make('mega_promo_badge'),
                                        Forms\Components\TextInput::make('mega_promo_title'),
                                        Forms\Components\Textarea::make('mega_promo_text')->rows(2)->columnSpanFull(),
                                        Forms\Components\TextInput::make('mega_promo_button_label'),
                                        Forms\Components\TextInput::make('mega_promo_button_url'),
                                        Forms\Components\Select::make('mega_promo_style')
                                            ->options([
                                                'purple' => 'Purple gradient',
                                                'pink' => 'Pink gradient',
                                                'dark' => 'Dark purple',
                                                'emerald' => 'Emerald',
                                                'rose' => 'Rose',
                                            ])
                                            ->default('purple'),
                                    ])->columns(2),
                            ]),
                        SeoSection::tab(),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('slug')->searchable()->copyable(),
                Tables\Columns\TextColumn::make('icon')->toggleable(),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->sortable(),
            ])
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
