param([string]$RemotePath)
$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "head -n 25 $RemotePath" -TimeOut 60
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
