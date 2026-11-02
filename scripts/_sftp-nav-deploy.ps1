$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Models/Category.php',
    'app/Support/SiteSettings.php',
    'app/Support/StorefrontNavigation.php',
    'app/Http/Controllers/ExclusiveOffersController.php',
    'routes/web.php',
    'resources/views/pages/exclusive-offers.blade.php',
    'app/Providers/AppServiceProvider.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Filament/Pages/HeaderNavigationSettings.php',
    'app/Filament/Resources/CategoryResource.php',
    'database/migrations/2026_10_26_260000_create_categories_table.php',
    'database/seeders/StorefrontNavigationSeeder.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/home.blade.php',
    'resources/views/partials/storefront-header-nav.blade.php',
    'public/css/beautage.css',
    'public/js/beautage-header-nav.js',
    'resources/views/filament/pages/header-navigation-settings.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Pages $remoteRoot/resources/views/partials $remoteRoot/resources/views/filament/pages $remoteRoot/database/seeders" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = "cd $remoteRoot; /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_26_260000_create_categories_table.php; /usr/bin/php artisan db:seed --class=StorefrontNavigationSeeder --force; /usr/bin/php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
