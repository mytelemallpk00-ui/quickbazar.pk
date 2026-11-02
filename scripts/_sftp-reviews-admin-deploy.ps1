$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Support/SiteSettings.php',
    'app/Models/ProductReview.php',
    'app/Http/Controllers/ProductReviewController.php',
    'app/Http/Requests/StoreProductReviewRequest.php',
    'app/Filament/Forms/AdminImageUpload.php',
    'app/Filament/Pages/ReviewSettings.php',
    'app/Filament/Resources/ProductReviewResource.php',
    'app/Filament/Resources/ProductReviewResource/Pages/ListProductReviews.php',
    'app/Filament/Resources/ProductReviewResource/Pages/CreateProductReview.php',
    'app/Filament/Resources/ProductReviewResource/Pages/EditProductReview.php',
    'app/Filament/Resources/ProductResource/RelationManagers/ProductReviewsRelationManager.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'database/migrations/2026_10_26_230000_add_review_photos_column.php',
    'resources/views/product-detail.blade.php',
    'resources/views/filament/pages/review-settings.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Resources/ProductReviewResource/Pages $remoteRoot/resources/views/filament/pages" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = "cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_26_230000_add_review_photos_column.php && /usr/bin/php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
