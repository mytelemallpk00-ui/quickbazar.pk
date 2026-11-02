$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'config/google-indexing.php',
    'config/services.php',
    'database/migrations/2026_10_29_150000_create_google_index_logs_table.php',
    'app/Models/GoogleIndexLog.php',
    'app/Support/GoogleIndexCredentialStore.php',
    'app/Services/GoogleIndex/GoogleIndexService.php',
    'app/Services/GoogleIndex/GoogleIndexException.php',
    'app/Services/GoogleIndex/GoogleIndexUrl.php',
    'app/Services/GoogleIndex/ClaudeSeoPreflightService.php',
    'app/Filament/Concerns/InteractsWithGoogleIndex.php',
    'app/Filament/Pages/SocialGoogleIndexing.php',
    'app/Filament/Pages/SocialApiCredentials.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/ProductResource/Pages/EditProduct.php',
    'app/Http/Controllers/Admin/GoogleIndexController.php',
    'app/Http/Requests/Admin/GoogleIndexPublishRequest.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'resources/views/filament/pages/social-google-indexing.blade.php',
    'routes/web.php',
    'storage/app/google/.gitignore'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) { Write-Host "SKIP $f"; continue }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteDir" | Out-Null
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan migrate --force && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
