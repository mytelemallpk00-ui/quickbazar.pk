$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$cmd = @"
test -f $remoteRoot/index.php && echo index_at_root
test -f $remoteRoot/public/index.php && echo public_index
test -f $remoteRoot/css/beautage.css && echo css_at_root
test -f $remoteRoot/public/css/beautage.css && echo css_in_public
test -L $remoteRoot/public/storage && echo public_storage_link_ok || echo no_public_storage_link
test -L $remoteRoot/storage && echo root_storage_link || true
ls $remoteRoot/public/storage/site-branding/logo 2>/dev/null | tail -2
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 60
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
