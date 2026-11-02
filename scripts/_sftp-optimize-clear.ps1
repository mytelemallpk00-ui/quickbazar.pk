$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null

if (-not $env:BEAUTAGE_SSH_PASSWORD) {
    throw 'Set BEAUTAGE_SSH_PASSWORD before running this script.'
}

$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'

$token = [guid]::NewGuid().ToString()
$tokenFile = Join-Path $env:TEMP ("beautage-flush-{0}.token" -f $token)
Set-Content -Path $tokenFile -Value $token -NoNewline -Encoding ascii

$flushRouteReady = $false
foreach ($f in @(
    'app/Http/Controllers/DeployFlushController.php',
    'routes/web.php'
)) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        continue
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
    $flushRouteReady = $true
}

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
try {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/storage/app" -TimeOut 30 | Out-Null
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}

$uploadCopy = Join-Path $env:TEMP 'deploy-flush.token'
Copy-Item -Path $tokenFile -Destination $uploadCopy -Force
Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $uploadCopy -Destination "$remoteRoot/storage/app" -AcceptKey -Force
Remove-Item $tokenFile, $uploadCopy -Force -ErrorAction SilentlyContinue
Write-Host 'OK storage/app/deploy-flush.token'

if ($flushRouteReady) {
    $ok = & (Join-Path $PSScriptRoot '_deploy-flush-cache.ps1') -Token $token
    if ($ok) {
        exit 0
    }
}

& (Join-Path $PSScriptRoot '_ssh-optimize-clear.ps1')
