<?php

namespace App\Filament\Pages;

use App\Filament\Concerns\AuthorizesAdminPage;
use App\Support\AdminPermissions;
use App\Models\Product;
use App\Support\GoogleAnalytics;
use App\Support\GoogleMerchant;
use App\Support\MetaPixel;
use App\Support\SiteSettings;
use App\Support\WebmasterTools;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\HtmlString;
use Illuminate\Validation\ValidationException;

class AnalyticsPerformanceTracking extends Page implements HasForms
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

    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';

    protected static ?string $navigationLabel = 'Analytics & Performance Tracking';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 96;

    protected static ?string $title = 'Analytics & Performance Tracking';

    protected static ?string $slug = 'analytics-performance-tracking';

    protected static string $view = 'filament.pages.analytics-performance-tracking';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'analytics_ga_enabled' => filter_var(SiteSettings::get('analytics_ga_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_ga_measurement_id' => SiteSettings::get('analytics_ga_measurement_id', ''),
            'analytics_gsc_enabled' => filter_var(SiteSettings::get('analytics_gsc_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_gsc_verification' => SiteSettings::get('analytics_gsc_verification', ''),
            'analytics_gsc_property_url' => SiteSettings::get('analytics_gsc_property_url', GoogleAnalytics::storefrontUrl()),
            'analytics_bing_enabled' => filter_var(SiteSettings::get('analytics_bing_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_bing_verification' => SiteSettings::get('analytics_bing_verification', ''),
            'analytics_yandex_enabled' => filter_var(SiteSettings::get('analytics_yandex_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_yandex_verification' => SiteSettings::get('analytics_yandex_verification', ''),
            'analytics_pinterest_enabled' => filter_var(SiteSettings::get('analytics_pinterest_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_pinterest_verification' => SiteSettings::get('analytics_pinterest_verification', ''),
            'analytics_indexnow_enabled' => filter_var(SiteSettings::get('analytics_indexnow_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_indexnow_key' => SiteSettings::get('analytics_indexnow_key', ''),
            'analytics_gmc_feed_enabled' => filter_var(SiteSettings::get('analytics_gmc_feed_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_gmc_merchant_id' => SiteSettings::get('analytics_gmc_merchant_id', ''),
            'analytics_gmc_brand' => SiteSettings::get('analytics_gmc_brand', GoogleMerchant::brandName()),
            'analytics_gmc_currency' => SiteSettings::get('analytics_gmc_currency', 'PKR'),
            'analytics_gmc_country' => SiteSettings::get('analytics_gmc_country', 'PK'),
            'analytics_meta_enabled' => filter_var(SiteSettings::get('analytics_meta_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_meta_pixel_id' => SiteSettings::get('analytics_meta_pixel_id', ''),
            'analytics_meta_capi_enabled' => filter_var(SiteSettings::get('analytics_meta_capi_enabled', false), FILTER_VALIDATE_BOOLEAN),
            'analytics_meta_access_token' => '',
            'analytics_meta_test_event_code' => SiteSettings::get('analytics_meta_test_event_code', ''),
        ]);
    }

    public function form(Form $form): Form
    {
        $storefrontUrl = GoogleAnalytics::storefrontUrl();

        return $form
            ->schema([
                Tabs::make('AnalyticsTabs')
                    ->tabs([
                        Tabs\Tab::make('Overview')
                            ->icon('heroicon-o-signal')
                            ->schema([
                                Placeholder::make('analytics_status_board')
                                    ->label('')
                                    ->content(fn (Get $get): HtmlString => new HtmlString(view('filament.forms.analytics-connection-status', [
                                        'storefrontUrl' => $storefrontUrl,
                                        'gaEnabled' => (bool) $get('analytics_ga_enabled'),
                                        'gaId' => GoogleAnalytics::normalizeMeasurementId($get('analytics_ga_measurement_id')),
                                        'gscEnabled' => (bool) $get('analytics_gsc_enabled'),
                                        'gscToken' => GoogleAnalytics::normalizeSiteVerificationToken($get('analytics_gsc_verification')),
                                        'bingEnabled' => (bool) $get('analytics_bing_enabled'),
                                        'bingToken' => WebmasterTools::normalizeToken($get('analytics_bing_verification')),
                                        'yandexEnabled' => (bool) $get('analytics_yandex_enabled'),
                                        'yandexToken' => WebmasterTools::normalizeToken($get('analytics_yandex_verification')),
                                        'indexNowEnabled' => (bool) $get('analytics_indexnow_enabled'),
                                        'indexNowKey' => WebmasterTools::normalizeIndexNowKey($get('analytics_indexnow_key')),
                                        'sitemapUrl' => WebmasterTools::sitemapUrl(),
                                        'gmcEnabled' => (bool) $get('analytics_gmc_feed_enabled'),
                                        'gmcMerchantId' => GoogleMerchant::normalizeMerchantId($get('analytics_gmc_merchant_id')),
                                        'gmcFeedUrl' => (bool) $get('analytics_gmc_feed_enabled') ? GoogleMerchant::feedUrl() : null,
                                        'metaEnabled' => (bool) $get('analytics_meta_enabled'),
                                        'metaPixelId' => MetaPixel::normalizePixelId($get('analytics_meta_pixel_id')),
                                        'metaCapiEnabled' => (bool) $get('analytics_meta_capi_enabled'),
                                        'ecommerceAuto' => (bool) $get('analytics_ga_enabled') || (bool) $get('analytics_meta_enabled'),
                                    ])->render()))
                                    ->columnSpanFull(),
                                Section::make('Store events (automatic)')
                                    ->description('Jab GA4 ya Meta Pixel on ho, yeh events khud fire hote hain — koi code nahi.')
                                    ->schema([
                                        Placeholder::make('ecommerce_events_list')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<ul class="list-disc pl-5 text-sm text-gray-600 dark:text-gray-300 space-y-1">'
                                                .'<li><strong>View Item</strong> — product page</li>'
                                                .'<li><strong>Add to Cart</strong> — bag mein add</li>'
                                                .'<li><strong>Begin Checkout</strong> — checkout page</li>'
                                                .'<li><strong>Purchase</strong> — order success (+ Meta server-side jab CAPI on ho)</li>'
                                                .'</ul>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),
                                Section::make('How automatic connection works')
                                    ->description('Quick Bazar injects tags + dataLayer on your storefront as soon as you save valid IDs — no theme edits.')
                                    ->schema([
                                        Placeholder::make('analytics_steps')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<ol class="list-decimal space-y-2 pl-5 text-sm text-gray-600 dark:text-gray-300">'
                                                .'<li>Create or open your Google Analytics 4 property and copy the <strong>Measurement ID</strong> (format <code>G-XXXXXXXXXX</code>).</li>'
                                                .'<li>Add your site in <strong>Google Search Console</strong>, <strong>Bing Webmaster Tools</strong> (covers Yahoo search), and optionally <strong>Yandex</strong> — use the <strong>HTML meta tag</strong> method and copy each code.</li>'
                                                .'<li>Paste IDs and verification codes in the tabs below, enable tracking, and click <strong>Save connection settings</strong>.</li>'
                                                .'<li>Return to each webmaster portal and click <strong>Verify</strong> — tags are injected on every public page automatically.</li>'
                                                .'<li>Submit your sitemap URL (Webmaster tools tab) in Google and Bing after verification.</li>'
                                                .'<li>For <strong>Google Merchant Center</strong>, enable the product feed tab and register the feed URL under Products → Feeds (scheduled fetch).</li>'
                                                .'</ol>'
                                            ))
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        Tabs\Tab::make('Google Analytics')
                            ->icon('heroicon-o-chart-bar-square')
                            ->schema([
                                Toggle::make('analytics_ga_enabled')
                                    ->label('Enable Google Analytics 4 on storefront')
                                    ->helperText('When enabled, the GA4 gtag snippet is added to every public page.')
                                    ->live(),
                                TextInput::make('analytics_ga_measurement_id')
                                    ->label('Measurement ID')
                                    ->placeholder('G-XXXXXXXXXX')
                                    ->maxLength(32)
                                    ->helperText('Found in Admin → Data streams → your web stream.')
                                    ->required(fn (Get $get): bool => (bool) $get('analytics_ga_enabled'))
                                    ->live(debounce: 400),
                                Placeholder::make('open_ga')
                                    ->label('')
                                    ->content(new HtmlString(
                                        '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://analytics.google.com/">Open Google Analytics →</a>'
                                    )),
                            ])->columns(1),
                        Tabs\Tab::make('Webmaster tools')
                            ->icon('heroicon-o-magnifying-glass')
                            ->schema([
                                Placeholder::make('webmaster_sitemap')
                                    ->label('Sitemap for all search engines')
                                    ->content(fn (): HtmlString => new HtmlString(
                                        '<p class="text-sm text-gray-600 dark:text-gray-300 break-all"><code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-800">'
                                        .e(WebmasterTools::sitemapUrl())
                                        .'</code></p>'
                                        .'<p class="mt-2 text-xs text-gray-500">Submit this URL in Google Search Console and Bing Webmaster Tools → Sitemaps.</p>'
                                    ))
                                    ->columnSpanFull(),
                                Section::make('Google Search Console')
                                    ->schema([
                                        Toggle::make('analytics_gsc_enabled')
                                            ->label('Enable Google site verification tag')
                                            ->helperText('Injects <code>google-site-verification</code> on the storefront.')
                                            ->live(),
                                        TextInput::make('analytics_gsc_verification')
                                            ->label('Google verification code')
                                            ->placeholder('Paste the content value from HTML tag method')
                                            ->maxLength(120)
                                            ->helperText('Paste only the token from the meta tag content attribute.')
                                            ->required(fn (Get $get): bool => (bool) $get('analytics_gsc_enabled'))
                                            ->live(debounce: 400),
                                        TextInput::make('analytics_gsc_property_url')
                                            ->label('Property URL (reference)')
                                            ->default($storefrontUrl)
                                            ->helperText('URL prefix property in Search Console.')
                                            ->maxLength(255),
                                        Placeholder::make('open_gsc')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://search.google.com/search-console">Open Google Search Console →</a>'
                                            )),
                                    ])->columns(1),
                                Section::make('Bing Webmaster Tools (Yahoo search)')
                                    ->description('Yahoo Search uses Bing’s index — verify once in Bing Webmaster Tools; no separate Yahoo tag is required.')
                                    ->schema([
                                        Toggle::make('analytics_bing_enabled')
                                            ->label('Enable Bing site verification tag')
                                            ->helperText('Injects <code>msvalidate.01</code> meta tag.')
                                            ->live(),
                                        TextInput::make('analytics_bing_verification')
                                            ->label('Bing verification code')
                                            ->placeholder('Paste msvalidate.01 content value')
                                            ->maxLength(120)
                                            ->required(fn (Get $get): bool => (bool) $get('analytics_bing_enabled'))
                                            ->live(debounce: 400),
                                        Placeholder::make('open_bing')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://www.bing.com/webmasters">Open Bing Webmaster Tools →</a>'
                                            )),
                                    ])->columns(1),
                                Section::make('Yandex Webmaster')
                                    ->schema([
                                        Toggle::make('analytics_yandex_enabled')
                                            ->label('Enable Yandex verification tag')
                                            ->live(),
                                        TextInput::make('analytics_yandex_verification')
                                            ->label('Yandex verification code')
                                            ->maxLength(120)
                                            ->required(fn (Get $get): bool => (bool) $get('analytics_yandex_enabled'))
                                            ->live(debounce: 400),
                                        Placeholder::make('open_yandex')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://webmaster.yandex.com/">Open Yandex Webmaster →</a>'
                                            )),
                                    ])->columns(1),
                                Section::make('Pinterest domain claim (optional)')
                                    ->schema([
                                        Toggle::make('analytics_pinterest_enabled')
                                            ->label('Enable Pinterest domain verify tag')
                                            ->live(),
                                        TextInput::make('analytics_pinterest_verification')
                                            ->label('Pinterest verification code')
                                            ->maxLength(120)
                                            ->required(fn (Get $get): bool => (bool) $get('analytics_pinterest_enabled'))
                                            ->live(debounce: 400),
                                        Placeholder::make('open_pinterest')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://www.pinterest.com/settings/claim/">Claim website on Pinterest →</a>'
                                            )),
                                    ])->columns(1),
                                Section::make('IndexNow (Bing, Yandex, Seznam)')
                                    ->description('Generate a key in Bing Webmaster Tools or create your own 32+ character hex key. We host the key file at /{key}.txt on your domain.')
                                    ->schema([
                                        Toggle::make('analytics_indexnow_enabled')
                                            ->label('Enable IndexNow key file')
                                            ->live(),
                                        TextInput::make('analytics_indexnow_key')
                                            ->label('IndexNow key')
                                            ->placeholder('32–128 lowercase hex characters')
                                            ->maxLength(128)
                                            ->helperText('32–128 hex characters (a–f, 0–9). Bing Webmaster Tools can generate a key for you.')
                                            ->required(fn (Get $get): bool => (bool) $get('analytics_indexnow_enabled'))
                                            ->live(debounce: 400),
                                        Placeholder::make('indexnow_url_hint')
                                            ->label('Key file URL')
                                            ->content(function (Get $get): HtmlString {
                                                $key = WebmasterTools::normalizeIndexNowKey($get('analytics_indexnow_key'));
                                                if ($key === null) {
                                                    return new HtmlString('<p class="text-sm text-gray-500">Enter a valid key to preview the hosted URL.</p>');
                                                }
                                                $url = e(rtrim($storefrontUrl, '/').'/'.$key.'.txt');

                                                return new HtmlString(
                                                    '<p class="text-sm break-all"><code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-800">'.$url.'</code></p>'
                                                );
                                            }),
                                        Placeholder::make('open_indexnow')
                                            ->label('')
                                            ->content(new HtmlString(
                                                '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://www.indexnow.org/documentation">IndexNow documentation →</a>'
                                            )),
                                    ])->columns(1),
                            ])->columns(1),
                        Tabs\Tab::make('Meta Ads')
                            ->icon('heroicon-o-megaphone')
                            ->schema([
                                Toggle::make('analytics_meta_enabled')
                                    ->label('Enable Meta Pixel (Facebook / Instagram)')
                                    ->helperText('Retargeting aur ads conversion tracking ke liye browser pixel.')
                                    ->live(),
                                TextInput::make('analytics_meta_pixel_id')
                                    ->label('Pixel ID')
                                    ->placeholder('123456789012345')
                                    ->maxLength(20)
                                    ->helperText('Events Manager → Data sources → your Pixel → ID copy karein.')
                                    ->required(fn (Get $get): bool => (bool) $get('analytics_meta_enabled'))
                                    ->live(debounce: 400),
                                Toggle::make('analytics_meta_capi_enabled')
                                    ->label('Enable Conversions API (server-side Purchase)')
                                    ->helperText('Order place par server se Purchase event — ad blockers se bachne ke liye recommended.')
                                    ->live(),
                                TextInput::make('analytics_meta_access_token')
                                    ->label('Conversions API access token')
                                    ->password()
                                    ->revealable()
                                    ->placeholder('Leave blank to keep saved token')
                                    ->helperText('Events Manager → Pixel → Settings → Conversions API → Generate token.')
                                    ->required(fn (Get $get): bool => (bool) $get('analytics_meta_capi_enabled') && ! filled(SiteSettings::get('analytics_meta_access_token', ''))),
                                TextInput::make('analytics_meta_test_event_code')
                                    ->label('Test event code (optional)')
                                    ->placeholder('TEST12345')
                                    ->helperText('Events Manager → Test events — debugging ke liye.'),
                                Placeholder::make('open_meta')
                                    ->label('')
                                    ->content(new HtmlString(
                                        '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="https://business.facebook.com/events_manager2">Open Meta Events Manager →</a>'
                                    )),
                            ])->columns(1),
                        Tabs\Tab::make('Google Merchant')
                            ->icon('heroicon-o-shopping-bag')
                            ->schema([
                                Toggle::make('analytics_gmc_feed_enabled')
                                    ->label('Enable Google Merchant product feed')
                                    ->helperText('Publishes an XML product feed URL for Merchant Center scheduled fetch.')
                                    ->live(),
                                TextInput::make('analytics_gmc_merchant_id')
                                    ->label('Merchant Center ID (optional)')
                                    ->placeholder('123456789')
                                    ->maxLength(20)
                                    ->helperText('Numeric ID from Merchant Center → Settings. Used for quick links only.')
                                    ->live(debounce: 400),
                                TextInput::make('analytics_gmc_brand')
                                    ->label('Default brand')
                                    ->maxLength(70)
                                    ->helperText('Sent as g:brand for every product (defaults to your site name).'),
                                TextInput::make('analytics_gmc_currency')
                                    ->label('Currency code')
                                    ->placeholder('PKR')
                                    ->maxLength(3)
                                    ->helperText('ISO 4217 code used in g:price (example: PKR, USD).'),
                                TextInput::make('analytics_gmc_country')
                                    ->label('Target country')
                                    ->placeholder('PK')
                                    ->maxLength(2)
                                    ->helperText('ISO country code for g:target_country.'),
                                Placeholder::make('gmc_product_count')
                                    ->label('Catalog in feed')
                                    ->content(fn (): HtmlString => new HtmlString(
                                        '<p class="text-sm font-semibold text-gray-800 dark:text-gray-100">'
                                        .number_format(Product::query()->count()).' products</p>'
                                        .'<p class="text-xs text-gray-500 mt-1">Active catalog items XML feed mein jate hain jab feed enabled ho.</p>'
                                    )),
                                Placeholder::make('gmc_feed_url')
                                    ->label('Product feed URL')
                                    ->content(function (Get $get): HtmlString {
                                        if (! (bool) $get('analytics_gmc_feed_enabled')) {
                                            return new HtmlString('<p class="text-sm text-gray-500">Enable the feed and save to generate a secure URL.</p>');
                                        }

                                        $url = e(GoogleMerchant::feedUrl());

                                        return new HtmlString(
                                            '<p class="text-sm text-gray-600 dark:text-gray-300 break-all"><code class="rounded bg-gray-100 px-1 py-0.5 dark:bg-gray-800">'.$url.'</code></p>'
                                            .'<p class="mt-2 text-xs text-gray-500">Merchant Center: Products → Feeds → <strong>Scheduled fetch</strong> → daily → paste URL. Shopping ads + free listings ke liye.</p>'
                                        );
                                    })
                                    ->columnSpanFull(),
                                Placeholder::make('open_gmc')
                                    ->label('')
                                    ->content(fn (Get $get): HtmlString => new HtmlString(
                                        '<a class="text-primary-600 hover:underline text-sm font-semibold" target="_blank" rel="noopener noreferrer" href="'.e(GoogleMerchant::merchantCenterUrl()).'">Open Google Merchant Center →</a>'
                                    )),
                            ])->columns(1),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $gaEnabled = (bool) ($state['analytics_ga_enabled'] ?? false);
        $gscEnabled = (bool) ($state['analytics_gsc_enabled'] ?? false);
        $bingEnabled = (bool) ($state['analytics_bing_enabled'] ?? false);
        $yandexEnabled = (bool) ($state['analytics_yandex_enabled'] ?? false);
        $pinterestEnabled = (bool) ($state['analytics_pinterest_enabled'] ?? false);
        $indexNowEnabled = (bool) ($state['analytics_indexnow_enabled'] ?? false);
        $gmcFeedEnabled = (bool) ($state['analytics_gmc_feed_enabled'] ?? false);
        $metaEnabled = (bool) ($state['analytics_meta_enabled'] ?? false);
        $metaCapiEnabled = (bool) ($state['analytics_meta_capi_enabled'] ?? false);

        $gaId = GoogleAnalytics::normalizeMeasurementId($state['analytics_ga_measurement_id'] ?? null);
        $metaPixelId = MetaPixel::normalizePixelId($state['analytics_meta_pixel_id'] ?? null);
        $gscToken = GoogleAnalytics::normalizeSiteVerificationToken($state['analytics_gsc_verification'] ?? null);
        $bingToken = WebmasterTools::normalizeToken($state['analytics_bing_verification'] ?? null);
        $yandexToken = WebmasterTools::normalizeToken($state['analytics_yandex_verification'] ?? null);
        $pinterestToken = WebmasterTools::normalizeToken($state['analytics_pinterest_verification'] ?? null);
        $indexNowKey = WebmasterTools::normalizeIndexNowKey($state['analytics_indexnow_key'] ?? null);
        $merchantId = GoogleMerchant::normalizeMerchantId($state['analytics_gmc_merchant_id'] ?? null);
        $currency = strtoupper(trim((string) ($state['analytics_gmc_currency'] ?? 'PKR')));
        $country = strtoupper(trim((string) ($state['analytics_gmc_country'] ?? 'PK')));

        if ($gaEnabled && $gaId === null) {
            throw ValidationException::withMessages([
                'data.analytics_ga_measurement_id' => 'Enter a valid GA4 Measurement ID (example: G-ABC123XYZ).',
            ]);
        }

        if ($gscEnabled && $gscToken === null) {
            throw ValidationException::withMessages([
                'data.analytics_gsc_verification' => 'Enter the verification code from Search Console (HTML tag method).',
            ]);
        }

        if ($bingEnabled && $bingToken === null) {
            throw ValidationException::withMessages([
                'data.analytics_bing_verification' => 'Enter the Bing HTML meta tag verification code.',
            ]);
        }

        if ($yandexEnabled && $yandexToken === null) {
            throw ValidationException::withMessages([
                'data.analytics_yandex_verification' => 'Enter the Yandex verification code.',
            ]);
        }

        if ($pinterestEnabled && $pinterestToken === null) {
            throw ValidationException::withMessages([
                'data.analytics_pinterest_verification' => 'Enter the Pinterest domain verification code.',
            ]);
        }

        if ($indexNowEnabled && $indexNowKey === null) {
            throw ValidationException::withMessages([
                'data.analytics_indexnow_key' => 'Enter a valid IndexNow key (32–128 hex characters).',
            ]);
        }

        if ($gmcFeedEnabled && ! preg_match('/^[A-Z]{3}$/', $currency)) {
            throw ValidationException::withMessages([
                'data.analytics_gmc_currency' => 'Use a 3-letter currency code (example: PKR).',
            ]);
        }

        if ($gmcFeedEnabled && ! preg_match('/^[A-Z]{2}$/', $country)) {
            throw ValidationException::withMessages([
                'data.analytics_gmc_country' => 'Use a 2-letter country code (example: PK).',
            ]);
        }

        if ($metaEnabled && $metaPixelId === null) {
            throw ValidationException::withMessages([
                'data.analytics_meta_pixel_id' => 'Enter a valid Meta Pixel ID (numbers only, 10+ digits).',
            ]);
        }

        $existingMetaToken = trim((string) SiteSettings::get('analytics_meta_access_token', ''));
        $metaTokenInput = trim((string) ($state['analytics_meta_access_token'] ?? ''));
        if ($metaCapiEnabled && $metaTokenInput === '' && $existingMetaToken === '') {
            throw ValidationException::withMessages([
                'data.analytics_meta_access_token' => 'Paste a Conversions API access token or disable server-side tracking.',
            ]);
        }

        SiteSettings::set('analytics_ga_enabled', $gaEnabled, 'analytics');
        SiteSettings::set('analytics_ga_measurement_id', $gaId ?? '', 'analytics');
        SiteSettings::set('analytics_gsc_enabled', $gscEnabled, 'analytics');
        SiteSettings::set('analytics_gsc_verification', $gscToken ?? '', 'analytics');
        SiteSettings::set('analytics_gsc_property_url', trim((string) ($state['analytics_gsc_property_url'] ?? GoogleAnalytics::storefrontUrl())), 'analytics');
        SiteSettings::set('analytics_bing_enabled', $bingEnabled, 'analytics');
        SiteSettings::set('analytics_bing_verification', $bingToken ?? '', 'analytics');
        SiteSettings::set('analytics_yandex_enabled', $yandexEnabled, 'analytics');
        SiteSettings::set('analytics_yandex_verification', $yandexToken ?? '', 'analytics');
        SiteSettings::set('analytics_pinterest_enabled', $pinterestEnabled, 'analytics');
        SiteSettings::set('analytics_pinterest_verification', $pinterestToken ?? '', 'analytics');
        SiteSettings::set('analytics_indexnow_enabled', $indexNowEnabled, 'analytics');
        SiteSettings::set('analytics_indexnow_key', $indexNowKey ?? '', 'analytics');
        SiteSettings::set('analytics_gmc_feed_enabled', $gmcFeedEnabled, 'analytics');
        SiteSettings::set('analytics_gmc_merchant_id', $merchantId ?? '', 'analytics');
        SiteSettings::set('analytics_gmc_brand', trim((string) ($state['analytics_gmc_brand'] ?? GoogleMerchant::brandName())), 'analytics');
        SiteSettings::set('analytics_gmc_currency', $currency, 'analytics');
        SiteSettings::set('analytics_gmc_country', $country, 'analytics');
        SiteSettings::set('analytics_meta_enabled', $metaEnabled, 'analytics');
        SiteSettings::set('analytics_meta_pixel_id', $metaPixelId ?? '', 'analytics');
        SiteSettings::set('analytics_meta_capi_enabled', $metaCapiEnabled, 'analytics');
        if ($metaTokenInput !== '') {
            SiteSettings::set('analytics_meta_access_token', $metaTokenInput, 'analytics');
        }
        SiteSettings::set('analytics_meta_test_event_code', trim((string) ($state['analytics_meta_test_event_code'] ?? '')), 'analytics');

        if ($gmcFeedEnabled) {
            GoogleMerchant::ensureFeedToken();
        }

        $body = 'E-commerce events (view, cart, checkout, purchase) ab storefront par automatic hain jab GA4 ya Meta on ho.';
        if ($gmcFeedEnabled) {
            $body .= ' Merchant feed URL Merchant Center mein add karein.';
        }

        Notification::make()
            ->title('Analytics settings saved')
            ->body($body)
            ->success()
            ->send();
    }
}
