$ErrorActionPreference = 'Stop'
$authPath = Join-Path $PSScriptRoot '_tus-auth-once.json'
if (-not (Test-Path $authPath)) {
    throw @"
Missing $authPath
Generate upload URL via Hostinger MCP (hosting_files_generate-upload-url) and save baseUrl, auth, rest to that JSON file, then re-run.
"@
}
$c = Get-Content $authPath -Raw | ConvertFrom-Json

& (Join-Path $PSScriptRoot '_hostinger-tus-deploy.ps1') `
    -BaseUrl $c.baseUrl `
    -Auth $c.auth `
    -Rest $c.rest `
    -Files @(
        'app/Support/WebmasterTools.php',
        'app/Http/Controllers/IndexNowKeyController.php',
        'app/Filament/Pages/AnalyticsPerformanceTracking.php',
        'routes/web.php',
        'resources/views/partials/site-analytics-head.blade.php',
        'resources/views/filament/forms/analytics-connection-status.blade.php',
        'resources/views/filament/pages/analytics-performance-tracking.blade.php',
        'resources/views/filament/widgets/manage-content-links.blade.php',
        'app/Http/Controllers/ProgrammaticSeoLandingController.php',
        'app/Models/ProgrammaticSeoLanding.php',
        'app/Http/Controllers/Store/AbandonedCartSyncController.php',
        'app/Services/StoreAutomation/AbandonedCartSyncService.php',
        'app/Support/StoreAutomationSettings.php',
        'app/Models/AbandonedCart.php'
    )
