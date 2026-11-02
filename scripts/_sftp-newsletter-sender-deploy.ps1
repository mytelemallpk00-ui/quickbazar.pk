$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'database/migrations/2026_10_30_130000_create_newsletter_broadcasts_table.php',
    'app/Models/NewsletterBroadcast.php',
    'app/Services/Newsletter/NewsletterBroadcastService.php',
    'app/Jobs/SendNewsletterBroadcastJob.php',
    'app/Mail/StoreNewsletterMail.php',
    'app/Mail/PartnerIrNewsletterMail.php',
    'app/Filament/Pages/NewsletterSendCenter.php',
    'app/Filament/Concerns/ManagesStorefrontCmsPage.php',
    'app/Filament/Pages/InvestorRelations.php',
    'resources/views/mail/layouts/beautage.blade.php',
    'resources/views/mail/beautage-store-newsletter.blade.php',
    'resources/views/mail/beautage-partner-ir-newsletter.blade.php',
    'resources/views/filament/pages/newsletter-send-center.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) { Write-Host "SKIP $f"; continue }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteDir" | Out-Null
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$migrate = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan migrate --force --no-interaction 2>&1"
Write-Host $migrate.Output
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
