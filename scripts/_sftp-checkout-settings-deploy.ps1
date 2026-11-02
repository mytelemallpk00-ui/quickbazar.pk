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
    'app/Support/CheckoutSettings.php',
    'app/Filament/Pages/CheckoutSettings.php',
    'app/Http/Controllers/CheckoutController.php',
    'app/Http/Controllers/OrderController.php',
    'app/Http/Requests/StoreOrderRequest.php',
    'app/Providers/AppServiceProvider.php',
    'resources/views/filament/pages/checkout-settings.blade.php',
    'resources/views/filament/widgets/manage-content-links.blade.php',
    'resources/views/checkout.blade.php',
    'resources/views/pages/shipping.blade.php'
)

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
