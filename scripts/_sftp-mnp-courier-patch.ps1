$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'config/courier.php',
    'app/Services/Courier/MnpCourierClient.php',
    'app/Services/Courier/CourierRateService.php',
    'app/Services/Courier/CourierBookingService.php',
    'app/Support/Courier/CourierPlatforms.php',
    'app/Support/Courier/CourierApiCredentialStore.php',
    'app/Support/Courier/CourierCredentialResolver.php',
    'app/Support/Courier/PakistanZoneRates.php',
    'app/Filament/Pages/CourierApiCredentials.php',
    'resources/views/checkout.blade.php'
)
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan optimize:clear" -TimeOut 120
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
