$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'app/Models/CmsPage.php',
    'app/Http/Controllers/Investor/InvestorPortalLandingController.php',
    'app/Filament/Pages/InvestorPortalLanding.php',
    'app/Support/FooterSettings.php',
    'public/css/investor-portal.css',
    'resources/views/pages/cms/investor-portal-landing.blade.php',
    'resources/views/filament/pages/investor-portal-landing.blade.php',
    'resources/views/layouts/investor-portal.blade.php',
    'resources/views/investor/auth/login.blade.php',
    'resources/views/investor/auth/register.blade.php',
    'resources/views/partials/become-investor-cta.blade.php',
    'database/seeders/CmsPageSeeder.php'
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
$seed = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan db:seed --class=Database\\Seeders\\CmsPageSeeder --force --no-interaction 2>&1"
Write-Host $seed.Output
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
