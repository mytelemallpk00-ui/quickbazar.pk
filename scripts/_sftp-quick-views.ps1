$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'resources/views/pages/cms/investor-portal-landing.blade.php',
    'resources/views/partials/investor-portal-header.blade.php',
    'resources/views/partials/investor-portal-footer.blade.php',
    'resources/views/layouts/investor-portal-app.blade.php',
    'resources/views/layouts/investor-portal.blade.php',
    'app/Support/InvestorPortalCms.php',
    'app/Filament/Pages/InvestorPortalLanding.php',
    'app/Models/CmsPage.php',
    'public/css/investor-portal.css'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
