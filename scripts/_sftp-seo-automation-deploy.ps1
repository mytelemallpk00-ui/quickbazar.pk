$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'routes/console.php',
    'public/robots.txt',
    'app/Providers/AppServiceProvider.php',
    'app/Support/SeoAutomationSettings.php',
    'app/Support/Seo/FacetedSeoResolver.php',
    'app/Services/Seo/SitemapGenerator.php',
    'app/Models/ProgrammaticSeoLanding.php',
    'app/Http/Controllers/CollectionController.php',
    'app/Http/Controllers/ProgrammaticSeoLandingController.php',
    'app/Console/Commands/GenerateSitemapCommand.php',
    'app/Jobs/GenerateSitemapJob.php',
    'app/Jobs/PushProductGoogleIndexJob.php',
    'app/Jobs/AutoGenerateProductSeoJob.php',
    'app/Observers/ProductSeoObserver.php',
    'app/Filament/Pages/SeoAutomationSettings.php',
    'app/Filament/Resources/ProgrammaticSeoLandingResource.php',
    'app/Filament/Resources/ProgrammaticSeoLandingResource/Pages/ListProgrammaticSeoLandings.php',
    'app/Filament/Resources/ProgrammaticSeoLandingResource/Pages/CreateProgrammaticSeoLanding.php',
    'app/Filament/Resources/ProgrammaticSeoLandingResource/Pages/EditProgrammaticSeoLanding.php',
    'resources/views/pages/pseo-landing.blade.php',
    'resources/views/partials/product-json-ld.blade.php',
    'resources/views/product-detail.blade.php',
    'resources/views/collections.blade.php',
    'resources/views/filament/pages/seo-automation-settings.blade.php',
    'database/migrations/2026_10_31_140000_create_programmatic_seo_landings.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$dirs = @(
    "$remoteRoot/app/Support/Seo",
    "$remoteRoot/app/Services/Seo",
    "$remoteRoot/app/Observers",
    "$remoteRoot/app/Jobs",
    "$remoteRoot/app/Filament/Resources/ProgrammaticSeoLandingResource/Pages",
    "$remoteRoot/resources/views/pages"
)
foreach ($d in $dirs) {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $d" | Out-Null
}
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = @"
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_31_140000_create_programmatic_seo_landings.php && /usr/bin/php artisan seo:generate-sitemap && /usr/bin/php artisan optimize:clear
"@
Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180 | ForEach-Object { $_.Output }
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
