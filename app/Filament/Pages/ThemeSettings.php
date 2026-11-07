<?php

namespace App\Filament\Pages;

use App\Filament\Forms\AdminImageUpload;
use App\Filament\Forms\FooterSettingsForm;
use App\Support\FooterSettings;
use App\Models\User;
use App\Support\AdminPermissions;
use App\Support\BrandPalette;
use App\Support\SiteSettings;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;

class ThemeSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-paint-brush';

    protected static ?string $navigationLabel = 'Theme & site';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 100;

    protected static ?string $title = 'Theme settings';

    protected static ?string $slug = 'theme-settings';

    protected static string $view = 'filament.pages.theme-settings';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public static function canAccess(): bool
    {
        $user = auth()->user();

        return $user instanceof User
            && $user->is_admin
            && $user->canAccessAdminModule(AdminPermissions::SETTINGS);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }

    public function mount(): void
    {
        $marqueeItems = SiteSettings::get('marquee_items', []);
        if (! is_array($marqueeItems)) {
            $marqueeItems = [];
        }

        $themeColors = SiteSettings::themeColors();

        $this->form->fill([
            'site_name' => SiteSettings::get('site_name', 'Beautage'),
            'site_tagline' => SiteSettings::get('site_tagline', 'Elevate Your Natural Elegance'),
            'support_phone' => SiteSettings::get('support_phone', '+1 (800) BEAUTAGE'),
            'support_email' => SiteSettings::get('support_email', 'hello@beautage.com.pk'),
            'logo_url' => SiteSettings::fileUploadFormState(SiteSettings::get('logo_url')),
            'favicon_url' => SiteSettings::fileUploadFormState(SiteSettings::get('favicon_url')),
            'theme_color_purple' => $themeColors['purple'],
            'theme_color_purple_dark' => $themeColors['purpleDark'],
            'theme_color_pink' => $themeColors['pink'],
            'theme_color_pink_hover' => $themeColors['pinkHover'],
            'theme_color_amber' => $themeColors['amber'],
            'marquee_items' => $marqueeItems,
            'footer_settings' => FooterSettings::forStorefront(),
            'footer_logo_url' => SiteSettings::fileUploadFormState(SiteSettings::get('footer_logo_url')),
            'default_seo_focus_keyword' => SiteSettings::get('default_seo_focus_keyword', ''),
            'default_meta_title' => SiteSettings::get('default_meta_title', 'Beautage — Elevate Your Natural Elegance'),
            'default_meta_description' => SiteSettings::get('default_meta_description', ''),
            'default_meta_keywords' => SiteSettings::get('default_meta_keywords', ''),
            'default_robots' => SiteSettings::get('default_robots', 'index,follow'),
            'default_og_title' => SiteSettings::get('default_og_title', ''),
            'default_og_description' => SiteSettings::get('default_og_description', ''),
            'default_og_image' => SiteSettings::fileUploadFormState(SiteSettings::get('default_og_image')),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('Brand')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                TextInput::make('site_name')->label('Site name')->required()->live(debounce: 400),
                                TextInput::make('site_tagline')->label('Site tagline')->required()->live(debounce: 400),
                                AdminImageUpload::make('logo_url', AdminImageUpload::PRESET_SITE_LOGO)
                                    ->label('Site logo')
                                    ->helperText('Header, footer, and admin panel. Use square PNG/SVG-friendly PNG, 512×512 px.')
                                    ->required(fn (): bool => blank(SiteSettings::get('logo_url'))),
                                AdminImageUpload::make('favicon_url', AdminImageUpload::PRESET_FAVICON)
                                    ->label('Favicon')
                                    ->helperText('Browser tab icon. 64×64 px PNG works best.')
                                    ->required(fn (): bool => blank(SiteSettings::get('favicon_url'))),
                                TextInput::make('support_phone')->label('Support phone'),
                                TextInput::make('support_email')->label('Support email')->email()->required(),
                            ])->columns(2),
                        Tabs\Tab::make('Footer')
                            ->icon('heroicon-o-bars-3-bottom-left')
                            ->schema(FooterSettingsForm::schema()),
                        Tabs\Tab::make('Colors')
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                ColorPicker::make('theme_color_purple')->label('Navy'),
                                ColorPicker::make('theme_color_purple_dark')->label('Deep navy'),
                                ColorPicker::make('theme_color_pink')->label('Accent'),
                                ColorPicker::make('theme_color_pink_hover')->label('Accent hover'),
                                ColorPicker::make('theme_color_amber')->label('Highlight'),
                            ])->columns(2),
                        Tabs\Tab::make('Marquee')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Repeater::make('marquee_items')
                                    ->schema([
                                        TextInput::make('text')->required(),
                                    ])
                                    ->columnSpanFull(),
                            ]),
                        Tabs\Tab::make('Default SEO')
                            ->icon('heroicon-o-magnifying-glass-circle')
                            ->schema([
                                Placeholder::make('homepage_google_preview')
                                    ->label('')
                                    ->content(function (Get $get): HtmlString {
                                        $title = $get('default_meta_title') ?: ($get('site_name') ?: 'Beautage');
                                        $tagline = $get('site_tagline') ?: 'Elevate Your Natural Elegance';
                                        $description = $get('default_meta_description') ?: $tagline;

                                        return new HtmlString(view('filament.forms.google-serp-preview', [
                                            'title' => $title,
                                            'description' => $description,
                                            'url' => url('/'),
                                            'focusKeyword' => $get('default_seo_focus_keyword'),
                                        ])->render());
                                    })
                                    ->columnSpanFull(),
                                Section::make('Homepage search defaults')
                                    ->schema([
                                        TextInput::make('default_seo_focus_keyword')
                                            ->label('Focus keyword')
                                            ->required()
                                            ->maxLength(80)
                                            ->live(debounce: 400),
                                        TextInput::make('default_meta_title')
                                            ->label('SEO title')
                                            ->required()
                                            ->maxLength(70)
                                            ->live(debounce: 400),
                                        Textarea::make('default_meta_description')
                                            ->label('Meta description')
                                            ->required()
                                            ->rows(3)
                                            ->maxLength(320)
                                            ->live(debounce: 400),
                                        TextInput::make('default_meta_keywords')
                                            ->label('Meta keywords')
                                            ->required()
                                            ->helperText('Comma-separated keywords for the homepage.'),
                                        Select::make('default_robots')
                                            ->label('Robots')
                                            ->required()
                                            ->options([
                                                'index,follow' => 'Index, follow (default)',
                                                'index,nofollow' => 'Index, nofollow',
                                                'noindex,follow' => 'Noindex, follow',
                                                'noindex,nofollow' => 'Noindex, nofollow',
                                            ])
                                            ->native(false),
                                    ])->columns(2),
                                Section::make('Social sharing (Open Graph)')
                                    ->schema([
                                        TextInput::make('default_og_title')
                                            ->label('OG title')
                                            ->required()
                                            ->maxLength(70),
                                        Textarea::make('default_og_description')
                                            ->label('OG description')
                                            ->required()
                                            ->rows(2)
                                            ->maxLength(200),
                                        AdminImageUpload::make('default_og_image', AdminImageUpload::PRESET_OG)
                                            ->label('Default OG image')
                                            ->required(fn (): bool => blank(SiteSettings::get('default_og_image')))
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                    ])->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();
        $rawState = $this->form->getRawState();

        $mediaKeys = ['logo_url', 'favicon_url', 'default_og_image', 'footer_logo_url'];
        foreach ($mediaKeys as $mediaKey) {
            $candidate = $state[$mediaKey]
                ?? $rawState[$mediaKey]
                ?? data_get($this->data, $mediaKey);

            $normalized = SiteSettings::normalizeMediaForStorage($candidate);
            if ($normalized !== null) {
                SiteSettings::set($mediaKey, $normalized, 'site');
            }
        }

        foreach ($state as $key => $value) {
            if (in_array($key, $mediaKeys, true)) {
                continue;
            }

            $group = match (true) {
                $key === 'footer_settings' => 'footer',
                str_starts_with($key, 'theme_') => 'theme',
                str_contains($key, 'meta') || str_contains($key, 'og') || str_contains($key, 'seo') || str_contains($key, 'robots') => 'seo',
                default => 'site',
            };
            SiteSettings::set($key, $value, $group);
        }

        foreach ($mediaKeys as $mediaKey) {
            $this->data[$mediaKey] = SiteSettings::fileUploadFormState(SiteSettings::get($mediaKey));
        }

        $this->data['footer_settings'] = FooterSettings::forStorefront();

        Notification::make()
            ->title('Settings saved')
            ->body('Logo, footer, theme, and SEO settings are updated on the storefront.')
            ->success()
            ->send();
    }
}
