<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\CheckoutSettings as CheckoutSettingsStore;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class CheckoutSettings extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?string $navigationLabel = 'Checkout settings';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 94;

    protected static ?string $title = 'Checkout settings';

    protected static ?string $slug = 'checkout-settings';

    protected static string $view = 'filament.pages.checkout-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(CheckoutSettingsStore::all());
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('CheckoutSettingsTabs')
                    ->tabs([
                        Tabs\Tab::make('Checkout page')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                TextInput::make('page_badge')->maxLength(120),
                                TextInput::make('page_title')->required()->maxLength(200),
                                Textarea::make('page_subtitle')->rows(2)->maxLength(500)->columnSpanFull(),
                                Toggle::make('show_express_checkout')
                                    ->label('Show express checkout buttons (Shop Pay, PayPal, Apple Pay)')
                                    ->helperText('Demo buttons only — disable for a cleaner Pakistan checkout.'),
                            ])->columns(2),
                        Tabs\Tab::make('Delivery charges')
                            ->icon('heroicon-o-truck')
                            ->schema([
                                TextInput::make('free_shipping_threshold')
                                    ->label('Free shipping when order subtotal is at least')
                                    ->numeric()
                                    ->prefix('Rs')
                                    ->helperText('Matches your Shipping policy page messaging when you use the same value.'),
                                Repeater::make('shipping_methods')
                                    ->label('Delivery options at checkout')
                                    ->schema([
                                        TextInput::make('id')
                                            ->label('Method ID')
                                            ->helperText('Internal key, e.g. standard or express')
                                            ->required()
                                            ->maxLength(40),
                                        TextInput::make('label')->required()->maxLength(160),
                                        Textarea::make('description')->rows(2)->maxLength(300)->columnSpanFull(),
                                        TextInput::make('price')
                                            ->label('Charge (PKR)')
                                            ->numeric()
                                            ->minValue(0)
                                            ->default(0),
                                        Toggle::make('free_over_threshold')
                                            ->label('Waive this fee when free-shipping threshold is met')
                                            ->default(true),
                                        Toggle::make('enabled')->default(true),
                                    ])
                                    ->columns(2)
                                    ->collapsible()
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Payment methods')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Placeholder::make('cod_only_notice')
                                    ->label('Active payment method')
                                    ->content('Cash on Delivery is the only payment method. Mobile wallet, card, and bank transfer are turned off.')
                                    ->dehydrated(false),
                            ]),
                        Tabs\Tab::make('Tax & currency')
                            ->icon('heroicon-o-calculator')
                            ->schema([
                                TextInput::make('currency')
                                    ->default('PKR')
                                    ->maxLength(8)
                                    ->helperText('Displayed with totals (orders store PKR by default).'),
                                TextInput::make('tax_rate')
                                    ->label('Tax rate (decimal)')
                                    ->numeric()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->helperText('Example: 0.08 = 8% estimated tax on checkout.'),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        CheckoutSettingsStore::save($this->form->getState());

        Notification::make()
            ->title('Checkout settings saved')
            ->body('Delivery charges and payment methods are updated on the storefront checkout page.')
            ->success()
            ->send();
    }
}
