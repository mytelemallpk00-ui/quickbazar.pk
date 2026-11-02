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
    'app/Support/SiteSettings.php',
    'app/Support/HomepageContent.php',
    'app/Filament/Forms/AdminImageUpload.php',
    'app/Filament/Forms/HomepageContentForm.php',
    'app/Filament/Pages/ThemeSettings.php',
    'app/Filament/Pages/HomepageSettings.php',
    'app/Http/Controllers/HomeController.php',
    'app/Http/Controllers/DeployFlushController.php',
    'routes/web.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'resources/views/filament/pages/theme-settings.blade.php',
    'resources/views/filament/pages/homepage-settings.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/home.blade.php',
    'resources/views/partials/home/showcase.blade.php',
    'resources/views/partials/home/flash-deals.blade.php',
    'resources/views/partials/home/aesthetics-gallery.blade.php',
    'resources/views/partials/home/featured-promo.blade.php',
    'resources/views/partials/home/trending.blade.php',
    'resources/views/partials/home/endorsements.blade.php',
    'resources/views/product-detail.blade.php',
    'public/images/beautagelogosquare2.png',
    'public/images/beautage-logo.svg'
)

$publicAssets = @(
    'public/images/beautagelogosquare2.png',
    'public/images/beautage-logo.svg'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
try {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Support $remoteRoot/app/Filament/Forms $remoteRoot/app/Filament/Pages $remoteRoot/app/Http/Controllers $remoteRoot/routes $remoteRoot/app/Providers/Filament $remoteRoot/resources/views/filament/pages $remoteRoot/resources/views/filament/widgets $remoteRoot/resources/views/layouts $remoteRoot/resources/views/partials/home $remoteRoot/images $remoteRoot/storage/app/public/site-branding" -TimeOut 60 | Out-Null
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if ($publicAssets -contains $f) {
        $remoteDir = "$remoteRoot/images"
    } else {
        $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
        if ($f -like 'public/*') {
            $remoteDir = "$remoteRoot/" + ($f -replace '^public/','' -replace '/[^/]+$','')
        }
    }
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f -> $remoteDir"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
