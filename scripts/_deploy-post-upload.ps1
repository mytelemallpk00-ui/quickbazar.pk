param(
    [Parameter(Mandatory = $true)]
    [System.Management.Automation.PSCredential] $Credential,

    [string] $RemoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html',

    [string] $LocalRoot = 'c:\xampp\htdocs\beautage',

    [switch] $RunMigrations
)

$ErrorActionPreference = 'Stop'

$token = [guid]::NewGuid().ToString()
$tokenFile = Join-Path $env:TEMP ("beautage-flush-{0}.token" -f $token)
Set-Content -Path $tokenFile -Value $token -NoNewline -Encoding ascii

$flushRouteReady = $false
foreach ($f in @(
    'app/Http/Controllers/DeployFlushController.php',
    'routes/web.php'
)) {
    $local = Join-Path $LocalRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        continue
    }
    $remoteDir = "$RemoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $Credential -Path $local -Destination $remoteDir -AcceptKey -Force
    $flushRouteReady = $true
}

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $Credential -AcceptKey -Force
try {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $RemoteRoot/storage/app" -TimeOut 30 | Out-Null
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}

$uploadCopy = Join-Path $env:TEMP 'deploy-flush.token'
Copy-Item -Path $tokenFile -Destination $uploadCopy -Force
Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $Credential -Path $uploadCopy -Destination "$RemoteRoot/storage/app" -AcceptKey -Force
Remove-Item $tokenFile, $uploadCopy -Force -ErrorAction SilentlyContinue

if ($RunMigrations) {
    $flagPath = Join-Path $env:TEMP 'deploy-run-migrations.flag'
    Set-Content -Path $flagPath -Value '1' -NoNewline
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $Credential -Path $flagPath -Destination "$RemoteRoot/storage/app" -AcceptKey -Force
    Remove-Item $flagPath -Force -ErrorAction SilentlyContinue
}

if ($flushRouteReady) {
    $ok = & (Join-Path $PSScriptRoot '_deploy-flush-cache.ps1') -Token $token
    if ($ok) {
        Write-Host 'Post-upload cache flush OK'
        return
    }
}

& (Join-Path $PSScriptRoot '_ssh-optimize-clear.ps1')
