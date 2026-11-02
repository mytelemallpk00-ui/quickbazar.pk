<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Models\AbandonedCart;
use App\Models\StoreAutomationDispatch;
use App\Services\StoreAutomation\StoreAutomationRunner;
use App\Support\AdminPermissions;
use App\Support\StoreAutomationSettings;
use Filament\Forms\Components\Placeholder;
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
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\HtmlString;

class StoreAutomations extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::ORDERS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-bolt';

    protected static ?string $navigationLabel = 'Store automations';

    protected static ?string $navigationGroup = 'Orders';

    protected static ?int $navigationSort = 12;

    protected static ?string $title = 'Store email automations';

    protected static ?string $slug = 'store-automations';

    protected static string $view = 'filament.pages.store-automations';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(StoreAutomationSettings::all());
    }

    public function form(Form $form): Form
    {
        $stats = $this->automationStats();

        return $form
            ->schema([
                Placeholder::make('automation_stats')
                    ->label('')
                    ->content(new HtmlString(
                        '<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-2">'
                        .$this->statCard('Abandoned carts (open)', (string) $stats['open_carts'])
                        .$this->statCard('Emails sent (30d)', (string) $stats['dispatches_30d'])
                        .$this->statCard('Recovered carts', (string) $stats['recovered'])
                        .$this->statCard('Scheduler', 'Every 15 min · <code>store:automations</code>')
                        .'</div>'
                    ))
                    ->columnSpanFull(),
                Toggle::make('master_enabled')
                    ->label('Enable all store automations')
                    ->helperText('Master switch — off hone par koi email automation nahi chalegi.')
                    ->columnSpanFull(),
                Tabs::make('StoreAutomationsTabs')
                    ->tabs([
                        Tabs\Tab::make('Abandoned cart recovery')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                Toggle::make('abandoned_cart.enabled')->label('Enabled'),
                                TextInput::make('abandoned_cart.min_subtotal')->label('Minimum bag value (PKR)')->numeric()->prefix('Rs'),
                                TextInput::make('abandoned_cart.checkout_url')->label('Checkout link path')->default('/checkout'),
                                Repeater::make('abandoned_cart.steps')
                                    ->label('Recovery sequence')
                                    ->schema([
                                        TextInput::make('delay_hours')->label('Hours after last activity')->numeric()->required(),
                                        TextInput::make('subject')->label('Email subject')->required()->columnSpanFull(),
                                        TextInput::make('badge')->label('Email badge')->default('CART'),
                                        TextInput::make('headline')->label('Headline')->columnSpanFull(),
                                        Textarea::make('body_html')->label('Body HTML')->rows(3)->columnSpanFull(),
                                        TextInput::make('cta_label')->label('Button label')->default('Complete checkout'),
                                        TextInput::make('discount_code')->label('Optional promo code')->placeholder('COMEBACK5'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->collapsible(),
                            ]),
                        Tabs\Tab::make('Post-purchase & upsell')
                            ->icon('heroicon-o-gift')
                            ->schema([
                                Toggle::make('post_purchase.enabled')->label('Enabled'),
                                Repeater::make('post_purchase.steps')
                                    ->label('Post-purchase sequence')
                                    ->helperText('Step 1 = thank-you (0h). Later steps = upsell emails with product picks.')
                                    ->schema([
                                        TextInput::make('delay_hours')->label('Hours after order')->numeric()->required(),
                                        TextInput::make('subject')->required()->columnSpanFull(),
                                        TextInput::make('badge')->default('ORDER'),
                                        TextInput::make('headline')->columnSpanFull(),
                                        Textarea::make('body_html')->rows(3)->columnSpanFull(),
                                        TextInput::make('cta_label')->default('Shop now'),
                                        TextInput::make('pick_products')
                                            ->label('Product pick mode')
                                            ->helperText('none · same_category · bundles')
                                            ->default('same_category'),
                                    ])
                                    ->columns(2)
                                    ->columnSpanFull()
                                    ->collapsible(),
                            ]),
                        Tabs\Tab::make('Smart re-order reminders')
                            ->icon('heroicon-o-arrow-path')
                            ->schema([
                                Toggle::make('reorder.enabled')->label('Enabled'),
                                TextInput::make('reorder.default_cycle_days')->label('Default replenishment cycle (days)')->numeric(),
                                TextInput::make('reorder.send_days_before')->label('Remind this many days before cycle ends')->numeric(),
                                TextInput::make('reorder.subject')->columnSpanFull(),
                                TextInput::make('reorder.badge')->default('REORDER'),
                                TextInput::make('reorder.headline')->columnSpanFull(),
                                Textarea::make('reorder.body_html')->rows(3)->columnSpanFull(),
                                TextInput::make('reorder.cta_label')->default('Reorder now'),
                                TextInput::make('reorder.shop_url')->default('/collections'),
                            ])->columns(2),
                        Tabs\Tab::make('Low-stock alerts')
                            ->icon('heroicon-o-exclamation-triangle')
                            ->schema([
                                Toggle::make('low_stock.enabled')->label('Enabled'),
                                TextInput::make('low_stock.threshold')->label('Alert when quantity ≤')->numeric(),
                                TextInput::make('low_stock.admin_emails')
                                    ->label('Notify emails (comma-separated)')
                                    ->columnSpanFull(),
                                TextInput::make('low_stock.subject')->columnSpanFull(),
                                TextInput::make('low_stock.badge')->default('INVENTORY'),
                                TextInput::make('low_stock.headline')->columnSpanFull(),
                                Textarea::make('low_stock.body_html')->rows(3)->columnSpanFull(),
                                Placeholder::make('low_stock_hint')
                                    ->label('')
                                    ->content('Set **Stock quantity** on each product in Catalog. One digest email per day when any SKU is low.')
                                    ->columnSpanFull(),
                            ])->columns(2),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        StoreAutomationSettings::save($this->form->getState());

        Notification::make()
            ->title('Store automations saved')
            ->body('Emails use the Beautage store theme. Scheduler runs every 15 minutes.')
            ->success()
            ->send();
    }

    public function runNow(StoreAutomationRunner $runner): void
    {
        $counts = $runner->runAll();

        Notification::make()
            ->title('Automation run complete')
            ->body('Sent: '.json_encode($counts))
            ->success()
            ->send();
    }

    /** @return array{open_carts: int, dispatches_30d: int, recovered: int} */
    protected function automationStats(): array
    {
        if (! Schema::hasTable('abandoned_carts')) {
            return ['open_carts' => 0, 'dispatches_30d' => 0, 'recovered' => 0];
        }

        return [
            'open_carts' => AbandonedCart::query()->whereNull('recovered_at')->whereNull('unsubscribed_at')->count(),
            'recovered' => AbandonedCart::query()->whereNotNull('recovered_at')->count(),
            'dispatches_30d' => StoreAutomationDispatch::query()->where('sent_at', '>=', now()->subDays(30))->count(),
        ];
    }

    protected function statCard(string $label, string $value): string
    {
        return '<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4">'
            .'<div class="text-xs font-semibold text-gray-500 dark:text-gray-400">'.$label.'</div>'
            .'<div class="text-xl font-bold text-gray-900 dark:text-gray-100 mt-1">'.$value.'</div></div>';
    }

    public function getSubheading(): ?string
    {
        return 'Post-purchase, abandoned cart, re-order & inventory alerts — branded like store newsletters.';
    }
}
