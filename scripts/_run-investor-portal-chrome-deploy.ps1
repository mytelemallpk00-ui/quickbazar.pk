$ErrorActionPreference = 'Stop'
$authPath = Join-Path $PSScriptRoot '_tus-auth-once.json'
if (-not (Test-Path $authPath)) { throw "Missing $authPath" }
$c = Get-Content $authPath -Raw | ConvertFrom-Json

& (Join-Path $PSScriptRoot '_hostinger-tus-deploy.ps1') `
    -BaseUrl $c.baseUrl `
    -Auth $c.auth `
    -Rest $c.rest `
    -Files @(
        'public/css/investor-portal.css',
        'resources/views/layouts/investor-portal-app.blade.php',
        'resources/views/layouts/investor-portal.blade.php',
        'resources/views/partials/investor-portal-header.blade.php',
        'resources/views/partials/investor-portal-menu-script.blade.php',
        'resources/views/partials/investor-cms-hero.blade.php',
        'resources/views/pages/cms/generic.blade.php',
        'resources/views/pages/cms/investor-relations.blade.php',
        'resources/views/investor/apply.blade.php',
        'resources/views/investor/submitted.blade.php',
        'resources/views/investor/ledger.blade.php',
        'resources/views/investor/auth/login.blade.php',
        'resources/views/investor/auth/register.blade.php',
        'resources/views/investor/dashboard.blade.php',
        'resources/views/pages/cms/investor-portal-landing.blade.php',
        'resources/views/partials/investor-cms-engage.blade.php',
        'resources/views/filament/pages/investor-portal-seo-settings.blade.php',
        'app/Support/InvestorPortalSeo.php',
        'app/Filament/Pages/InvestorPortalSeoSettings.php',
        'app/Http/Controllers/PageController.php',
        'app/Http/Controllers/Investor/InvestorApplicationController.php',
        'app/Http/Controllers/Investor/InvestorPortalController.php',
        'app/Http/Controllers/Investor/Auth/InvestorLoginController.php',
        'app/Http/Controllers/Investor/Auth/InvestorRegisterController.php'
    )
