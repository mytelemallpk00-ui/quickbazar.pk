<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\ProductTrustSettings as ProductTrustSettingsStore;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ProductTrustSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';

    protected static ?string $navigationLabel = 'Trust & Authenticity';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 92;

    protected static ?string $title = 'Trust & Authenticity';

    protected static ?string $slug = 'product-trust-settings';

    protected static string $view = 'filament.pages.product-trust-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(ProductTrustSettingsStore::all());
    }

    public function form(Form $form): Form
    {
        $trustItemSchema = [
            TextInput::make('icon')
                ->label('Lucide icon name')
                ->helperText('Examples: shield-check, badge-check, gem, truck')
                ->required()
                ->maxLength(80),
            TextInput::make('title')
                ->label('Title')
                ->required()
                ->maxLength(120),
            Textarea::make('description')
                ->label('Description')
                ->rows(2)
                ->maxLength(500)
                ->columnSpanFull(),
        ];

        return $form
            ->schema([
                Section::make('Product page sidebar')
                    ->description('Third column on desktop product pages — icons use Lucide (same as the storefront).')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Show trust column on product pages')
                            ->default(true),
                    ]),
                Tabs::make('ProductTrustTabs')
                    ->tabs([
                        Tabs\Tab::make('Trust & Authenticity')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                TextInput::make('trust_section.badge')
                                    ->label('Top badge')
                                    ->maxLength(80),
                                TextInput::make('trust_section.heading')
                                    ->label('Heading')
                                    ->required()
                                    ->maxLength(160)
                                    ->columnSpanFull(),
                                Textarea::make('trust_section.intro')
                                    ->label('Intro text')
                                    ->rows(3)
                                    ->maxLength(600)
                                    ->columnSpanFull(),
                                Repeater::make('trust_section.items')
                                    ->label('Trust points')
                                    ->schema($trustItemSchema)
                                    ->columns(2)
                                    ->minItems(1)
                                    ->columnSpanFull(),
                            ])->columns(2),
                        Tabs\Tab::make('Quality Promise')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                TextInput::make('quality_section.badge')
                                    ->label('Top badge')
                                    ->maxLength(80),
                                TextInput::make('quality_section.heading')
                                    ->label('Heading')
                                    ->required()
                                    ->maxLength(160)
                                    ->columnSpanFull(),
                                Textarea::make('quality_section.intro')
                                    ->label('Intro text')
                                    ->rows(3)
                                    ->maxLength(600)
                                    ->columnSpanFull(),
                                Repeater::make('quality_section.items')
                                    ->label('Quality promise points')
                                    ->schema($trustItemSchema)
                                    ->columns(2)
                                    ->minItems(1)
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        ProductTrustSettingsStore::save($this->form->getState());

        Notification::make()
            ->title('Trust & Authenticity settings saved')
            ->success()
            ->send();
    }
}
