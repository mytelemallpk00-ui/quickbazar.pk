# Restores site after ThemeSettings PHP fatal (trait $adminPermission conflict).
# Requires: $env:BEAUTAGE_SSH_PASSWORD
$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null

if (-not $env:BEAUTAGE_SSH_PASSWORD) {
    throw 'Set BEAUTAGE_SSH_PASSWORD (Hostinger SSH password) then run this script again.'
}

$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'

$files = @(
    'app/Filament/Pages/ThemeSettings.php',
    'app/Filament/Concerns/AuthorizesAdminPage.php',
    'app/Filament/Concerns/AuthorizesAdminAccess.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'resources/views/filament/hooks/visit-store-button.blade.php',
    'app/Filament/Pages/Auth/EditProfile.php',
    'app/Filament/Resources/UserResource.php',
    'app/Models/User.php',
    'database/migrations/2026_10_27_150000_add_address_cnic_to_users_table.php',
    'app/Support/GoogleAnalytics.php',
    'app/Support/GoogleMerchant.php',
    'app/Http/Controllers/GoogleMerchantFeedController.php',
    'resources/views/feeds/google-merchant.blade.php',
    'routes/web.php',
    'app/Filament/Pages/AnalyticsPerformanceTracking.php',
    'resources/views/filament/pages/analytics-performance-tracking.blade.php',
    'resources/views/filament/forms/analytics-connection-status.blade.php',
    'resources/views/partials/site-analytics.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'app/Filament/Forms/AdminImageUpload.php',
    'app/Support/SiteSettings.php',
    'app/Filament/Concerns/AuthorizesAdminPage.php',
    'app/Filament/Concerns/AuthorizesAdminAccess.php',
    'app/Filament/Pages/HomepageSettings.php',
    'app/Filament/Pages/HeaderNavigationSettings.php',
    'app/Filament/Pages/ReviewSettings.php',
    'app/Filament/Pages/InvestorRelations.php',
    'app/Filament/Pages/BulkProductImport.php',
    'app/Filament/Pages/AdminDashboard.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/BlogPostResource.php',
    'app/Filament/Resources/CategoryResource.php',
    'app/Filament/Resources/OrderResource.php',
    'app/Filament/Resources/ProductBundleResource.php',
    'app/Filament/Resources/ProductReviewResource.php',
    'app/Filament/Resources/CareerOpeningResource.php',
    'app/Filament/Resources/CmsPageResource.php'
)

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        Write-Warning "Skip missing $f"
        continue
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
Write-Host 'Done. Hard-refresh https://beige-ape-286733.hostingersite.com/'
