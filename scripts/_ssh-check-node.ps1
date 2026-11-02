$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command 'which node; which npm; node -v 2>&1; npm -v 2>&1'
Write-Host $r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
