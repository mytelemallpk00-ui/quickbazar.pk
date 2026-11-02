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
    'app/Services/ProductAi/ProductAiService.php',
    'app/Services/ProductAi/ProductAiContent.php',
    'app/Services/ProductAi/ProductAiException.php',
    'app/Services/ProductAi/ProductSeoFallbackGenerator.php',
    'app/Support/AiSettings.php',
    'app/Filament/Pages/AiIntegrationSettings.php',
    'resources/views/filament/pages/ai-integration-settings.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'app/Http/Controllers/Admin/ProductAiController.php',
    'app/Http/Requests/GenerateProductAiRequest.php',
    'app/Filament/Concerns/GeneratesProductSeoWithAi.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/ProductResource/Pages/CreateProduct.php',
    'app/Filament/Resources/ProductResource/Pages/EditProduct.php',
    'app/Filament/Pages/BulkProductImport.php',
    'app/Services/ProductImport/ProductImportService.php',
    'config/services.php',
    'routes/web.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command @"
mkdir -p $remoteRoot/app/Services/ProductAi $remoteRoot/app/Http/Controllers/Admin $remoteRoot/app/Filament/Concerns $remoteRoot/app/Filament/Resources/ProductResource/Pages
"@ -TimeOut 60 | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
