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
    'app/Filament/Pages/BulkProductImport.php',
    'app/Services/ProductImport/ProductImportService.php',
    'app/Services/ProductImport/CategoryListingFetcher.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Pages $remoteRoot/app/Services/ProductImport $remoteRoot/database/migrations" | Out-Null

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

try {
    $cmd = @"
cd $remoteRoot && /usr/bin/php artisan optimize:clear
"@
    $r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
    $r.Output
    if ($r.Error) { $r.Error }
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}
