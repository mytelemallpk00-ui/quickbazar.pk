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
    'app/Support/AdminStoreAnalytics.php',
    'app/Support/StoreTraffic.php',
    'app/Http/Middleware/RecordStoreTraffic.php',
    'app/Filament/Pages/AdminDashboard.php',
    'app/Filament/Widgets/DashboardWelcomeWidget.php',
    'resources/views/filament/widgets/dashboard-welcome.blade.php',
    'app/Filament/Widgets/ManageContentLinksWidget.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'app/Filament/Widgets/StorePerformanceChartsWidget.php',
    'resources/views/filament/widgets/store-performance-charts.blade.php',
    'routes/web.php',
    'database/migrations/2026_10_27_160000_create_store_traffic_daily_table.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'public/css/filament-beautage-admin.css',
    'database/migrations/2026_10_27_170000_create_store_live_presence_table.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Widgets $remoteRoot/app/Http/Middleware $remoteRoot/resources/views/filament/widgets" | Out-Null

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

$cmd = "cd $remoteRoot && php artisan migrate --force && php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
