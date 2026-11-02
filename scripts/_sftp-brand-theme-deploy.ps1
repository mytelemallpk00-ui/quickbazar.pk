$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'public/images/beautage-logo-round.png',
    'public/js/tailwind-config.js',
    'public/css/beautage.css',
    'app/Support/BrandPalette.php',
    'app/Support/SiteSettings.php',
    'resources/views/partials/theme-brand-vars.blade.php',
    'resources/views/layouts/app.blade.php',
    'app/Filament/Pages/ThemeSettings.php',
    'app/Providers/Filament/AdminPanelProvider.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteDir" | Out-Null
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$sync = @'
cd REMOTE && /usr/bin/php artisan tinker --execute="
\$d = App\Support\BrandPalette::themeDefaults();
App\Support\SiteSettings::set('theme_color_purple', \$d['purple'], 'theme');
App\Support\SiteSettings::set('theme_color_purple_dark', \$d['purpleDark'], 'theme');
App\Support\SiteSettings::set('theme_color_pink', \$d['pink'], 'theme');
App\Support\SiteSettings::set('theme_color_pink_hover', \$d['pinkHover'], 'theme');
App\Support\SiteSettings::set('theme_color_amber', \$d['amber'], 'theme');
echo 'theme-sync-ok';
"
'@ -replace 'REMOTE', $remoteRoot
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $sync -TimeOut 120
$r.Output
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
