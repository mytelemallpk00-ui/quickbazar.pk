$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$cmd = "ls -la $remoteRoot/storage/app/public/site-branding/logo 2>/dev/null; ls -la $remoteRoot/storage/app/public/site-branding/favicon 2>/dev/null; ls -la $remoteRoot/public/storage 2>/dev/null | head -3; test -L $remoteRoot/storage && echo symlink_storage_ok || echo no_storage_link"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 60
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
