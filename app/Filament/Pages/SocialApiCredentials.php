<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Support\GoogleIndexCredentialStore;
use App\Support\Social\SocialApiCredentialStore;
use Filament\Forms\Components\FileUpload;
use App\Support\Social\SocialCredentialResolver;
use App\Support\Social\SocialPlatformGuides;
use App\Support\Social\SocialPlatforms;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class SocialApiCredentials extends Page implements HasForms
{
    use AuthorizesAdminPage;
    use InteractsWithForms;

    protected static function adminPermissionModule(): string
    {
        return AdminPermissions::SETTINGS;
    }

    protected static ?string $navigationIcon = 'heroicon-o-link';

    protected static ?string $navigationLabel = 'Channel connections';

    protected static ?string $navigationGroup = 'Social Automation';

    protected static ?int $navigationSort = 2;

    protected static ?string $title = 'Marketing channel connections';

    protected static ?string $slug = 'social-api-settings';

    protected static string $view = 'filament.pages.social-api-credentials';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(array_merge(SocialApiCredentialStore::formState(), [
            'google_service_account_upload' => null,
        ]));
    }

    public function form(Form $form): Form
    {
        $tabs = [];
        $resolver = app(SocialCredentialResolver::class);

        foreach (SocialPlatforms::all() as $platform) {
            $fields = [];
            $configured = $resolver->isConfigured($platform);
            $guide = SocialPlatformGuides::for($platform);

            foreach (SocialApiCredentialStore::fieldMap()[$platform] ?? [] as $field) {
                $fields[] = TextInput::make("{$platform}.{$field['key']}")
                    ->label($field['label'])
                    ->placeholder($configured ? 'Leave blank to keep current value' : 'Paste here…')
                    ->maxLength(500)
                    ->helperText($configured
                        ? 'Already connected — only type here if you want to replace the saved token.'
                        : 'Required before posts can publish to '.SocialPlatforms::label($platform).'.')
                    ->password($field['secret'])
                    ->revealable($field['secret']);
            }

            $tabs[] = Tabs\Tab::make(SocialPlatforms::label($platform))
                ->badge($configured ? 'Ready' : 'Setup')
                ->schema([
                    Placeholder::make("{$platform}_guide")
                        ->label('')
                        ->content(new HtmlString(
                            '<p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">'.$guide.'</p>'
                        ))
                        ->columnSpanFull(),
                    Placeholder::make("{$platform}_status")
                        ->label('Connection status')
                        ->content(new HtmlString(
                            $configured
                                ? '<span class="inline-flex items-center gap-1 text-success-600 font-semibold"><span aria-hidden="true">✓</span> Connected — posts can go live</span>'
                                : '<span class="inline-flex items-center gap-1 text-warning-600 font-semibold"><span aria-hidden="true">!</span> Not ready — fill required fields below</span>'
                        )),
                    Section::make('Login details')
                        ->schema($fields)
                        ->columns(1),
                ]);
        }

        $summary = $resolver->connectionSummary();

        return $form
            ->schema([
                Placeholder::make('platform_summary')
                    ->label('')
                    ->content(new HtmlString(
                        '<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 p-4 mb-2">'
                        .'<p class="text-sm font-semibold text-gray-900 dark:text-gray-100">'
                        .$summary['ready_count'].' / '.$summary['total'].' platforms ready</p>'
                        .'<p class="text-sm text-gray-600 dark:text-gray-400 mt-1">'
                        .($summary['ready_count'] === $summary['total']
                            ? 'Sab set hai — ab Create &amp; schedule se posts bana sakte hain.'
                            : 'Jo tabs par <strong>Setup</strong> likha hai, un par neeche token paste karein aur Save dabayein.')
                        .'</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Naye channels ya tab names: '
                        .'<a class="text-primary-600 underline font-semibold" href="'.route('filament.admin.resources.social-marketing-channels.index').'">Marketing channels</a>.</p></div>'
                    ))
                    ->columnSpanFull(),
                Tabs::make('Marketing channels')->tabs(array_merge($tabs, [
                    Tabs\Tab::make('Google Search Indexing API')
                        ->badge(GoogleIndexCredentialStore::isConfigured() ? 'Ready' : 'Setup')
                        ->icon('heroicon-o-rocket-launch')
                        ->schema([
                            Placeholder::make('google_index_guide')
                                ->label('')
                                ->content(new HtmlString(
                                    '<div class="prose prose-sm dark:prose-invert max-w-none">'
                                    .'<p><strong>Google Indexing API</strong> — instant index request for product &amp; content URLs (quota applies).</p>'
                                    .'<ol class="list-decimal pl-5 space-y-1">'
                                    .'<li>Create a service account in Google Cloud → enable <em>Web Search Indexing API</em>.</li>'
                                    .'<li>Download JSON key and upload below (saved to <code>storage/app/google/service-account.json</code>).</li>'
                                    .'<li>In Search Console → Settings → Users, add the service account email as <strong>Owner</strong>.</li>'
                                    .'<li>Open <a class="text-primary-600 underline" href="'.route('filament.admin.pages.social-google-indexing').'">Google Index</a> to push URLs.</li>'
                                    .'</ol></div>'
                                ))
                                ->columnSpanFull(),
                            Placeholder::make('google_index_status')
                                ->label('Status')
                                ->content(new HtmlString(
                                    GoogleIndexCredentialStore::isConfigured()
                                        ? '<span class="text-success-600 font-semibold">✓ Service account loaded — '.e((string) GoogleIndexCredentialStore::clientEmail()).'</span>'
                                        : '<span class="text-warning-600 font-semibold">! Upload JSON to enable indexing</span>'
                                )),
                            FileUpload::make('google_service_account_upload')
                                ->label('Service account JSON')
                                ->acceptedFileTypes(['application/json'])
                                ->maxSize(512)
                                ->helperText('Upload only when adding or replacing credentials.')
                                ->columnSpanFull(),
                        ]),
                ]))->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        SocialApiCredentialStore::saveFromForm($state);

        $upload = $state['google_service_account_upload'] ?? null;
        if (filled($upload)) {
            $path = is_array($upload) ? reset($upload) : $upload;
            if (is_string($path) && $path !== '') {
                $absolute = storage_path('app/private/'.$path);
                if (! is_file($absolute)) {
                    $absolute = storage_path('app/'.$path);
                }
                GoogleIndexCredentialStore::storeFromUpload($absolute);
            }
        }

        $this->form->fill(array_merge(SocialApiCredentialStore::formState(), [
            'google_service_account_upload' => null,
        ]));

        Notification::make()
            ->title('Connections saved')
            ->body('Social tokens and Google Indexing credentials updated.')
            ->success()
            ->send();
    }

    public function getSubheading(): ?string
    {
        return 'Tokens encrypted save hote hain — sirf naya value likhein jab update karna ho';
    }
}
