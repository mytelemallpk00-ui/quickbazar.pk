<?php

namespace App\Filament\Resources;

use App\Filament\Concerns\AuthorizesAdminAccess;
use App\Filament\Forms\AdminImageUpload;
use App\Support\AdminPermissions;
use App\Filament\Forms\SeoSection;
use App\Filament\Resources\BlogPostResource\Pages;
use App\Models\BlogPost;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BlogPostResource extends Resource
{
    use AuthorizesAdminAccess;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CONTENT;
    }

    protected static ?string $model = BlogPost::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Blog posts';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('Blog post')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Article')
                            ->schema([
                                Forms\Components\TextInput::make('title')->required()->live(onBlur: true),
                                Forms\Components\TextInput::make('slug')->required()->unique(ignoreRecord: true),
                                Forms\Components\TextInput::make('category'),
                                Forms\Components\TextInput::make('read_time')->label('Read time')->placeholder('5 min read'),
                                Forms\Components\DatePicker::make('published_at'),
                                Forms\Components\TextInput::make('author'),
                                Forms\Components\TextInput::make('author_role'),
                                Forms\Components\TextInput::make('author_avatar')->label('Author initials'),
                                Forms\Components\TextInput::make('likes')->numeric()->default(0),
                                Forms\Components\Toggle::make('is_featured'),
                                Forms\Components\Textarea::make('snippet')->rows(3)->columnSpanFull(),
                                Forms\Components\RichEditor::make('content')->required()->columnSpanFull(),
                            ])->columns(2),
                        Forms\Components\Tabs\Tab::make('Cover image')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                AdminImageUpload::make('image', AdminImageUpload::PRESET_BLOG_HERO)
                                    ->label('Hero image')
                                    ->columnSpanFull(),
                            ]),
                        SeoSection::tab('title', 'slug', 'snippet'),
                    ])->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')->square(),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('category')->badge(),
                Tables\Columns\TextColumn::make('published_at')->date()->sortable(),
                Tables\Columns\IconColumn::make('is_featured')->boolean(),
                Tables\Columns\TextColumn::make('meta_title')->limit(28)->toggleable(),
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
            'index' => Pages\ListBlogPosts::route('/'),
            'create' => Pages\CreateBlogPost::route('/create'),
            'edit' => Pages\EditBlogPost::route('/{record}/edit'),
        ];
    }
}
