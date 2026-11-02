$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null

if (-not $env:BEAUTAGE_SSH_PASSWORD) {
    throw 'Set BEAUTAGE_SSH_PASSWORD before running this script.'
}

$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'

$files = @(
    'app/Support/GoogleAnalytics.php',
    'app/Support/GoogleMerchant.php',
    'app/Support/MetaPixel.php',
    'app/Services/Meta/MetaConversionsApi.php',
    'app/Http/Controllers/OrderController.php',
    'public/js/beautage-analytics.js',
    'public/js/beautage-cart.js',
    'resources/views/checkout.blade.php',
    'app/Http/Controllers/GoogleMerchantFeedController.php',
    'resources/views/feeds/google-merchant.blade.php',
    'routes/web.php',
    'app/Filament/Pages/AnalyticsPerformanceTracking.php',
    'app/Filament/Pages/ThemeSettings.php',
    'resources/views/filament/pages/analytics-performance-tracking.blade.php',
    'resources/views/filament/forms/analytics-connection-status.blade.php',
    'resources/views/partials/site-analytics.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'app/Support/AdminPermissions.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Filament/Concerns/AuthorizesAdminPage.php',
    'app/Filament/Concerns/AuthorizesAdminAccess.php'
)

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
