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
$rows = App\Models\SiteSetting::query()->whereIn("key", ["logo_url","favicon_url","site_name"])->get();
foreach ($rows as $r) {
  echo $r->key . "=" . json_encode($r->value) . PHP_EOL;
}
'
'@ -replace 'REMOTE_ROOT', $remoteRoot
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $php -TimeOut 60
$r.Output
$r.Error
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
