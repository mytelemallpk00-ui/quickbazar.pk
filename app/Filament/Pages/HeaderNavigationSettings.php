<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\Category;
use App\Support\AdminPermissions;
use App\Support\SiteSettings;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
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
use Illuminate\Support\HtmlString;

class HeaderNavigationSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::CATEGORIES;
    }

    protected static ?string $navigationIcon = 'heroicon-o-bars-3-center-left';

    protected static ?string $navigationLabel = 'Header menu';

    protected static ?string $navigationGroup = 'Categories';

    protected static ?int $navigationSort = 0;

    protected static ?string $title = 'Storefront header menu';

    protected static ?string $slug = 'header-navigation';

    protected static string $view = 'filament.pages.header-navigation-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $nav = SiteSettings::headerNavigation();
        $this->form->fill([
            'collections_button_label' => $nav['collections_button_label'] ?? 'Collections & categories',
            'topbar_text' => $nav['topbar_text'] ?? 'Free nationwide delivery on orders over Rs. 2,500',
            'collections_category_ids' => collect($nav['collections_category_ids'] ?? [])
                ->map(fn ($id) => ['category_id' => (int) $id])
                ->values()
                ->all(),
            'collections_featured' => $nav['collections_featured'] ?? [],
            'main_nav_items' => $nav['main_nav_items'] ?? [],
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('How this works')
                    ->description('This page controls the storefront header bar and the category mega menu.')
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('help')
                            ->label('')
                            ->content(new HtmlString(
                                '<ul class="text-sm text-gray-600 dark:text-gray-400 list-disc ps-4 space-y-1">'
                                .'<li><strong>Collections button</strong> opens the mega menu. Each column is a category you pick below.</li>'
                                .'<li><strong>Bar links</strong> sit beside that button. Use Hot deals, categories, or any custom URL.</li>'
                                .'<li>Each category’s links and icon are edited under <strong>Categories → Header menu</strong>.</li>'
                                .'<li>Homepage blocks (hero, reviews, journal, and the rest) are switched under <strong>Settings → Homepage banners</strong>.</li>'
                                .'<li>Colors for the header, buttons, and footer are under <strong>Settings → Theme & site</strong>.</li>'
                                .'</ul>'
                            )),
                    ]),
                Section::make('Header bar')
                    ->schema([
                        TextInput::make('topbar_text')
                            ->label('Top bar message')
                            ->helperText('Shown next to the HOT badge. Example: Free nationwide delivery on orders over Rs. 2,500')
                            ->maxLength(160)
                            ->columnSpanFull(),
                        TextInput::make('collections_button_label')
                            ->label('Collections button label')
                            ->default('Collections & categories')
                            ->maxLength(48)
                            ->required(),
                    ]),
                Section::make('Collections mega menu')
                    ->description('Choose which categories appear as columns inside the Collections dropdown.')
                    ->schema([
                        Repeater::make('collections_category_ids')
                            ->label('Columns (drag to reorder)')
                            ->schema([
                                Select::make('category_id')
                                    ->label('Category')
                                    ->options(fn () => Category::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->required()
                                    ->disableOptionsWhenSelectedInSiblingRepeaterItems(),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
                Section::make('Collections featured card')
                    ->description('Optional highlight card on the right side of the Collections mega menu.')
                    ->schema([
                        TextInput::make('collections_featured.badge')->label('Badge'),
                        TextInput::make('collections_featured.title')->label('Title'),
                        Textarea::make('collections_featured.text')->label('Description')->rows(2),
                        TextInput::make('collections_featured.button_label')->label('Button label'),
                        TextInput::make('collections_featured.button_url')->label('Button URL')->url(),
                    ])->columns(2),
                Section::make('Main navigation bar')
                    ->description('Links shown to the right of the Collections button.')
                    ->schema([
                        Repeater::make('main_nav_items')
                            ->label('Menu items')
                            ->schema([
                                Select::make('type')
                                    ->options([
                                        'home' => 'Home',
                                        'category' => 'Category (link + optional small dropdown)',
                                        'bundles' => 'Exclusive Offers (bundle deals page)',
                                        'custom' => 'Custom link',
                                    ])
                                    ->required()
                                    ->live()
                                    ->native(false),
                                Select::make('category_id')
                                    ->label('Category')
                                    ->options(fn () => Category::query()->where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                                    ->searchable()
                                    ->visible(fn (Get $get): bool => $get('type') === 'category')
                                    ->required(fn (Get $get): bool => $get('type') === 'category'),
                                Toggle::make('show_dropdown')
                                    ->label('Show quick links dropdown')
                                    ->helperText('Uses links from the category Header menu tab — not a mega menu.')
                                    ->default(true)
                                    ->visible(fn (Get $get): bool => $get('type') === 'category'),
                                TextInput::make('label')
                                    ->visible(fn (Get $get): bool => $get('type') === 'custom')
                                    ->required(fn (Get $get): bool => $get('type') === 'custom'),
                                TextInput::make('icon')
                                    ->helperText('Lucide icon name, e.g. sparkles, flame, droplet')
                                    ->visible(fn (Get $get): bool => $get('type') === 'custom'),
                                TextInput::make('icon_color')
                                    ->placeholder('text-beautage-pink')
                                    ->visible(fn (Get $get): bool => $get('type') === 'custom'),
                                TextInput::make('url')
                                    ->label('Link URL')
                                    ->helperText('Use /collections or full https:// URL')
                                    ->visible(fn (Get $get): bool => $get('type') === 'custom')
                                    ->required(fn (Get $get): bool => $get('type') === 'custom'),
                                Toggle::make('highlight')
                                    ->label('Highlight this link')
                                    ->visible(fn (Get $get): bool => $get('type') === 'custom'),
                            ])
                            ->defaultItems(0)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        SiteSettings::setHeaderNavigation([
            'collections_button_label' => (string) ($state['collections_button_label'] ?? 'Collections & categories'),
            'topbar_text' => (string) ($state['topbar_text'] ?? ''),
            'collections_category_ids' => collect($state['collections_category_ids'] ?? [])
                ->pluck('category_id')
                ->filter()
                ->map(fn ($id) => (int) $id)
                ->values()
                ->all(),
            'collections_featured' => $state['collections_featured'] ?? [],
            'main_nav_items' => collect($state['main_nav_items'] ?? [])
                ->values()
                ->all(),
        ]);

        Notification::make()
            ->title('Header menu saved')
            ->body('Refresh the storefront to see changes.')
            ->success()
            ->send();
    }
}
