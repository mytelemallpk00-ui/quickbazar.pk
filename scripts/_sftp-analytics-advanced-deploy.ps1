$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Support/MetaPixel.php',
    'app/Services/Meta/MetaConversionsApi.php',
    'app/Filament/Pages/AnalyticsPerformanceTracking.php',
    'app/Http/Controllers/OrderController.php',
    'public/js/beautage-analytics.js',
    'public/js/beautage-cart.js',
    'resources/views/partials/site-analytics.blade.php',
    'resources/views/checkout.blade.php',
    'resources/views/filament/forms/analytics-connection-status.blade.php',
    'resources/views/filament/pages/analytics-performance-tracking.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Services/Meta $remoteRoot/public/js" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan optimize:clear" -TimeOut 120
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
