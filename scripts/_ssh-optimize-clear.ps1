$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null

if (-not $env:BEAUTAGE_SSH_PASSWORD) {
    throw 'Set BEAUTAGE_SSH_PASSWORD'
}

$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'

function Invoke-BeautageRemote {
    param([string] $Command, [int] $TimeOutSec = 45)

    $ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
    try {
        $r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $Command -TimeOut $TimeOutSec
        if ($r.ExitStatus -ne 0 -and $r.Error) {
            Write-Warning ($r.Error -join "`n")
        }
        return $r
    }
    finally {
        Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
    }
}

$steps = @(
    "cd $remoteRoot; rm -f bootstrap/cache/*.php 2>/dev/null; echo bootstrap_ok",
    "cd $remoteRoot; /usr/bin/php artisan view:clear --no-ansi 2>&1 | tail -1",
    "cd $remoteRoot; /usr/bin/php artisan config:clear --no-ansi 2>&1 | tail -1"
)

foreach ($step in $steps) {
    $r = Invoke-BeautageRemote -Command $step -TimeOutSec 60
    $r.Output
}

# Long optimize in background so SSH is not held open.
$r = Invoke-BeautageRemote -Command "cd $remoteRoot; nohup /usr/bin/php artisan optimize:clear --no-ansi >/tmp/beautage-optimize-clear.log 2>&1 & echo optimize_dispatched" -TimeOutSec 20
$r.Output

Write-Host 'SSH cache steps finished'
