<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\Courier\CourierApiCredentialStore;
use App\Support\Courier\CourierCredentialResolver;
use App\Support\Courier\CourierPlatforms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class CourierApiCredentials extends Page implements HasForms
{
    use AuthorizesAdminPage;

    public static function canAccess(): bool
    {
        return false;
    }
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-truck';

    protected static ?string $navigationLabel = 'Courier APIs';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 15;

    protected static ?string $title = 'Courier API connections';

    protected static ?string $slug = 'courier-api-settings';

    protected static string $view = 'filament.pages.courier-api-credentials';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(CourierApiCredentialStore::formState());
    }

    public function form(Form $form): Form
    {
        $resolver = app(CourierCredentialResolver::class);
        $tabs = [];

        foreach (CourierPlatforms::all() as $platform) {
            $fields = [];
            $configured = $resolver->isConfigured($platform);

            foreach (CourierApiCredentialStore::fieldMap()[$platform] ?? [] as $field) {
                $fields[] = TextInput::make("{$platform}.{$field['key']}")
                    ->label($field['label'])
                    ->placeholder($configured ? 'Leave blank to keep saved value' : 'Paste from courier portal…')
                    ->password($field['secret'])
                    ->revealable($field['secret'])
                    ->maxLength(500);
            }

            $tabs[] = Tabs\Tab::make(CourierPlatforms::label($platform))
                ->badge($configured ? 'Ready' : 'Setup')
                ->schema([
                    Placeholder::make("{$platform}_hint")
                        ->label('')
                        ->content(new HtmlString(
                            $platform === 'mnp'
                                ? '<p class="text-sm text-gray-600 dark:text-gray-400">'
                                .'M&amp;P merchant login: <a class="text-primary-600 underline" href="https://mnpcourier.com/cplight/login" target="_blank" rel="noopener">mnpcourier.com/cplight/login</a>. '
                                .'API username, password, account number aur pickup location ID yahan save karein. '
                                .'<a class="text-primary-600 underline" href="https://mnpcourier.com/mycodapi/swagger/ui/index" target="_blank" rel="noopener">Swagger API</a>.</p>'
                                : '<p class="text-sm text-gray-600 dark:text-gray-400">Merchant portal se API keys save karein. Optional URLs <code>.env</code> mein (RATE_URL / BOOK_URL).</p>'
                        ))
                        ->columnSpanFull(),
                    Toggle::make("{$platform}.enabled")
                        ->label('Enable this courier at checkout'),
                    Placeholder::make("{$platform}_status")
                        ->label('Status')
                        ->content(new HtmlString(
                            $configured
                                ? '<span class="text-success-600 font-semibold">✓ Ready for live rates &amp; auto booking</span>'
                                : '<span class="text-warning-600 font-semibold">⚠ Add credentials — zone estimates only</span>'
                        )),
                    Section::make('API credentials')->schema($fields)->columns(1),
                ]);
        }

        return $form
            ->schema([
                Tabs::make('Couriers')->tabs($tabs)->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        CourierApiCredentialStore::saveFromForm($this->form->getState());
        $this->form->fill(CourierApiCredentialStore::formState());

        Notification::make()
            ->title('Courier settings saved')
            ->success()
            ->send();
    }

    public function getSubheading(): ?string
    {
        return 'TCS, Leopards, BlueEx, M&P — checkout rates + order booking';
    }
}
