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
    'app/Services/AiSeo/AiSeoManager.php',
    'app/Services/AiSeo/AiSeoDriver.php',
    'app/Services/AiSeo/AiSeoPayload.php',
    'app/Services/AiSeo/OpenAiSeoDriver.php',
    'app/Services/AiSeo/GeminiSeoDriver.php',
    'app/Services/AiSeo/Concerns/BuildsAiSeoPrompt.php',
    'app/Http/Controllers/Admin/AiSeoController.php',
    'app/Filament/Pages/AiSeoOptimizer.php',
    'app/Models/Product.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Support/AiSettings.php',
    'resources/views/admin/ai-seo/index.blade.php',
    'public/css/filament-beautage-admin.css',
    'config/services.php',
    'routes/web.php',
    'database/migrations/2026_10_27_220000_add_ai_seo_columns_to_products_table.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command @"
mkdir -p $remoteRoot/app/Services/AiSeo/Concerns $remoteRoot/app/Http/Controllers/Admin $remoteRoot/app/Filament/Pages $remoteRoot/resources/views/admin/ai-seo $remoteRoot/database/migrations
"@ -TimeOut 60 | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && php artisan migrate --force --no-interaction" -TimeOut 120
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
