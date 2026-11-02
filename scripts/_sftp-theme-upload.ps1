$ErrorActionPreference = 'Stop'
Import-Module (Join-Path $env:TEMP 'PoshSSHManual\extract\Posh-SSH.psd1') -Force

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
    'app/Http/Controllers/HomeController.php',
    'app/Filament/Forms/AdminImageUpload.php',
    'app/Filament/Pages/ThemeSettings.php',
    'app/Filament/Forms/SeoSection.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/CategoryResource.php',
    'app/Filament/Resources/BlogPostResource.php',
    'resources/views/filament/pages/theme-settings.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/home.blade.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Support $remoteRoot/app/Filament/Forms $remoteRoot/app/Filament/Pages $remoteRoot/app/Filament/Resources $remoteRoot/resources/views/filament/pages $remoteRoot/resources/views/layouts" | Out-Null

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

try {
    $cmd = @"
cd $remoteRoot && (test -L public/storage || /usr/bin/php artisan storage:link) && /usr/bin/php artisan optimize:clear
"@
    $r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
    $r.Output
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}
