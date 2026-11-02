$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'public/css/beautage.css',
    'public/css/investor-portal.css',
    'resources/css/beautage-storefront.css',
    'routes/web.php',
    'app/Http/Controllers/Investor/Auth/InvestorLoginController.php',
    'app/Http/Controllers/Investor/Auth/InvestorRegisterController.php',
    'app/Http/Controllers/Investor/Auth/Concerns/RedirectsAfterInvestorAuth.php',
    'resources/views/layouts/investor-portal.blade.php',
    'resources/views/investor/auth/login.blade.php',
    'resources/views/investor/auth/register.blade.php',
    'resources/views/partials/become-investor-cta.blade.php',
    'resources/views/partials/storefront-footer.blade.php',
    'resources/views/partials/storefront-header-nav.blade.php',
    'resources/views/layouts/app.blade.php'
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
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
