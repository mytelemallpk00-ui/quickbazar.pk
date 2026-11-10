<?php

namespace App\Providers\Filament;

use App\Filament\Pages\AdminDashboard;
use App\Filament\Pages\AnalyticsPerformanceTracking;
use App\Filament\Pages\CourierApiCredentials;
use App\Filament\Pages\SocialApiCredentials;
use App\Filament\Pages\SocialGoogleIndexing;
use App\Filament\Pages\SocialPostScheduler;
use App\Support\GoogleAnalytics;
use App\Support\SiteSettings;
use Filament\View\PanelsRenderHook;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        FilamentAsset::register([
            Css::make(
                'beautage-admin-theme',
                asset('css/filament-beautage-admin.css?v=2026103112'),
            ),
            Css::make(
                'shop-pakistan-admin-login',
                asset('css/filament-admin-login.css?v=2026110816'),
            ),
            Js::make(
                'chartjs',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js',
            ),
        ]);
    }

    public function panel(Panel $panel): Panel
    {
        $panel = $panel
            ->default()
            ->id('admin')
            ->path('admin');

        $panel = class_exists(Login::class)
            ? $panel->login(Login::class)
            : $panel->login();

        return $panel
            ->profile(EditProfile::class, isSimple: false)
            ->brandName(fn (): string => (string) SiteSettings::get('site_name', 'Quick Bazar'))
            ->brandLogo(fn (): string => SiteSettings::logoUrl())
            ->brandLogoHeight('2rem')
            ->favicon(fn (): string => SiteSettings::faviconUrl())
            ->colors([
                'primary' => Color::hex('#5D267D'),
                'danger' => Color::Rose,
                'gray' => Color::Zinc,
                'info' => Color::hex('#6366F1'),
                'success' => Color::hex('#10B981'),
                'warning' => Color::hex('#F59E0B'),
            ])
            ->darkMode(true)
            ->defaultThemeMode(ThemeMode::Light)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarWidth('16.5rem')
            ->maxContentWidth(MaxWidth::Full)
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => view('filament.hooks.visit-store-button', [
                    'storeUrl' => GoogleAnalytics::storefrontUrl(),
                ])->render(),
            )
            ->navigationGroups([
                NavigationGroup::make()->label('Overview')->icon('heroicon-o-home'),
                NavigationGroup::make()->label('Categories')->icon('heroicon-o-tag'),
                NavigationGroup::make()->label('AI SEO')->icon('heroicon-o-sparkles'),
                NavigationGroup::make()->label('Catalog')->icon('heroicon-o-shopping-bag'),
                NavigationGroup::make()->label('Bundle deals')->icon('heroicon-o-squares-plus'),
                NavigationGroup::make()->label('Content')->icon('heroicon-o-document-text'),
                NavigationGroup::make()->label('Orders')->icon('heroicon-o-shopping-cart'),
                NavigationGroup::make()->label('Customer Reviews')->icon('heroicon-o-star'),
                NavigationGroup::make()->label('Settings')->icon('heroicon-o-cog-6-tooth'),
                NavigationGroup::make()->label('Social Automation')->icon('heroicon-o-share'),
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                AdminDashboard::class,
                AnalyticsPerformanceTracking::class,
                SocialPostScheduler::class,
                SocialApiCredentials::class,
                SocialGoogleIndexing::class,
                CourierApiCredentials::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
