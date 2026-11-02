$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$migrate = "cd $remoteRoot; /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_26_230000_add_review_photos_column.php; /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_26_240000_add_short_description_to_products.php; /usr/bin/php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $migrate -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
