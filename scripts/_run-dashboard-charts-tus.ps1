$ErrorActionPreference = 'Stop'
$authPath = Join-Path $PSScriptRoot '_tus-auth-once.json'
if (-not (Test-Path $authPath)) { throw "Missing $authPath" }
$c = Get-Content $authPath -Raw | ConvertFrom-Json

& (Join-Path $PSScriptRoot '_hostinger-tus-deploy.ps1') `
    -BaseUrl $c.baseUrl `
    -Auth $c.auth `
    -Rest $c.rest `
    -Files @(
        'resources/views/filament/widgets/store-performance-charts.blade.php',
        'public/css/filament-beautage-admin.css',
        'app/Providers/Filament/AdminPanelProvider.php',
        'app/Filament/Widgets/StorePerformanceChartsWidget.php',
        'app/Support/AdminStoreAnalytics.php'
    )
