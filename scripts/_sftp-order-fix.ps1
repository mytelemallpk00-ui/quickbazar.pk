$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path 'c:\xampp\htdocs\beautage\app\Models\Order.php' -Destination "$remoteRoot/app/Models" -AcceptKey -Force
Write-Host 'OK Order.php'
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan optimize:clear" -TimeOut 120
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
