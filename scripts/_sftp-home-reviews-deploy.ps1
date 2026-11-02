$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')

$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'

$files = @(
    'app/Http/Controllers/HomeController.php',
    'resources/views/home.blade.php',
    'resources/views/partials/home/customer-reviews.blade.php'
)

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/', '\')
    if (-not (Test-Path -LiteralPath $local)) {
        throw "Missing local file: $local"
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
Write-Host 'Homepage reviews deploy complete.'
