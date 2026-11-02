$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$builtCss = Get-ChildItem (Join-Path $localRoot 'public\build\assets\storefront-*.css') -ErrorAction SilentlyContinue | Select-Object -First 1
if (-not $builtCss) {
    throw 'Run npm run build first (public/build/assets/storefront-*.css missing).'
}
$files = @(
    'public/.htaccess',
    'public/build/manifest.json',
    ('public/build/assets/' + $builtCss.Name),
    'app/Support/StorefrontAssets.php',
    'public/js/beautage-boot.js',
    'public/js/beautage-analytics.js',
    'resources/views/layouts/app.blade.php',
    'resources/views/product-detail.blade.php',
    'resources/views/partials/site-analytics.blade.php',
    'resources/views/partials/site-analytics-head.blade.php',
    'resources/views/partials/recently-viewed-section.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        Write-Host "SKIP missing $f"
        continue
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteDir" | Out-Null
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
