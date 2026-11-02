$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'app/Models/Product.php',
    'app/Models/ProductReview.php',
    'app/Http/Controllers/ProductController.php',
    'app/Support/ProductFaqGenerator.php',
    'app/Services/ProductImport/ProductImportService.php',
    'database/migrations/2026_10_28_160000_add_faq_items_to_products_table.php',
    'app/Http/Controllers/ProductReviewController.php',
    'app/Http/Requests/StoreProductReviewRequest.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/ProductResource/RelationManagers/ProductReviewsRelationManager.php',
    'database/migrations/2026_10_26_220000_add_product_detail_page_fields.php',
    'database/seeders/LuminaProductDetailSeeder.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/partials/theme-brand-vars.blade.php',
    'resources/views/partials/product-pdp-bulk-savings.blade.php',
    'public/css/pdp.css',
    'public/js/pdp-pricing.js',
    'resources/views/product-detail.blade.php',
    'resources/views/partials/product-star-rating.blade.php',
    'resources/views/partials/product-pdp-trust-column.blade.php',
    'app/Support/ProductTrustSettings.php',
    'app/Filament/Pages/ProductTrustSettings.php',
    'resources/views/filament/pages/product-trust-settings.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Resources/ProductResource/RelationManagers $remoteRoot/app/Filament/Pages $remoteRoot/app/Http/Requests $remoteRoot/app/Support $remoteRoot/resources/views/partials $remoteRoot/resources/views/filament/pages $remoteRoot/database/seeders" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = @"
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_28_160000_add_faq_items_to_products_table.php && /usr/bin/php artisan optimize:clear
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
