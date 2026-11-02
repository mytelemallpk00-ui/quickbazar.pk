$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "tail -n 80 /home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html/storage/logs/laravel.log | head -n 40"
Write-Host $r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
