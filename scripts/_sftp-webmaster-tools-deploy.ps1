$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Support/WebmasterTools.php',
    'app/Http/Controllers/IndexNowKeyController.php',
    'app/Filament/Pages/AnalyticsPerformanceTracking.php',
    'routes/web.php',
    'resources/views/partials/site-analytics-head.blade.php',
    'resources/views/filament/forms/analytics-connection-status.blade.php',
    'resources/views/filament/pages/analytics-performance-tracking.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Support $remoteRoot/app/Http/Controllers" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        throw "Missing local file: $local"
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred
Remove-SSHSession -SessionId $ssh.SessionId -ErrorAction SilentlyContinue | Out-Null
Write-Host 'Webmaster tools deploy complete.'
