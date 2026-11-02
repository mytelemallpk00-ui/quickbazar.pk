$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path 'c:\xampp\htdocs\beautage\resources\views\home.blade.php' -Destination "$remoteRoot/resources/views" -AcceptKey -Force
Write-Host 'OK home.blade.php'
& (Join-Path $PSScriptRoot '_ssh-optimize-clear.ps1')
