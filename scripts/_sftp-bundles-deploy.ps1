$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Models/Product.php',
    'app/Models/ProductBundle.php',
    'app/Models/ProductBundleItem.php',
    'app/Http/Controllers/ProductController.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Filament/Resources/ProductBundleResource.php',
    'app/Filament/Resources/ProductBundleResource/Pages/EditProductBundle.php',
    'app/Filament/Resources/ProductBundleResource/Pages/ListProductBundles.php',
    'app/Filament/Resources/ProductBundleResource/Pages/ListProductBundles.php',
    'app/Filament/Resources/ProductBundleResource/Pages/CreateProductBundle.php',
    'app/Filament/Resources/ProductBundleResource/Pages/EditProductBundle.php',
    'database/migrations/2026_10_26_250000_create_product_bundles_tables.php',
    'resources/views/product-detail.blade.php',
    'resources/views/partials/product-bundle-section.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Resources/ProductBundleResource/Pages $remoteRoot/resources/views/partials" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$migrate = "cd $remoteRoot; /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_26_250000_create_product_bundles_tables.php; /usr/bin/php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $migrate -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
