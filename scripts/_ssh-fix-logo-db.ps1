$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$php = @'
cd REMOTE_ROOT
/usr/bin/php -r '
require "vendor/autoload.php";
$app = require_once "bootstrap/app.php";
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
App\Support\SiteSettings::set("logo_url", "/storage/site-branding/logo/9xXOm5UyQwU7iVvcBN6o2jTkl83lIZTv6qIPslRH.png", "site");
App\Support\SiteSettings::set("favicon_url", "/storage/site-branding/favicon/xiBb6yWqVeVg68HqS2JqnFfzDmBByPsYO4kQnIXG.png", "site");
foreach (["logo_url","favicon_url"] as $k) {
  echo $k . "=" . json_encode(App\Support\SiteSettings::get($k)) . PHP_EOL;
}
'
'@ -replace 'REMOTE_ROOT', $remoteRoot
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $php -TimeOut 60
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
