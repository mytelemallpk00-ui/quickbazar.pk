$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Support/ProductFrequentlyBoughtTogether.php',
    'app/Http/Controllers/ProductController.php',
    'app/Models/Product.php',
    'app/Models/ProductBundle.php',
    'public/js/beautage-recently-viewed.js',
    'resources/views/partials/recently-viewed-section.blade.php',
    'resources/views/partials/product-bundle-section.blade.php',
    'resources/views/product-detail.blade.php',
    'resources/views/home.blade.php',
    'resources/views/collections.blade.php',
    'resources/views/layouts/app.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteDir" | Out-Null
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" -TimeOut 120
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
