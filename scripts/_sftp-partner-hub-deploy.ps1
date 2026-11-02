$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'public/css/beautage.css',
    'public/css/investor-portal.css',
    'public/js/beautage-newsletter.js',
    'resources/css/beautage-storefront.css',
    'routes/web.php',
    'app/Support/FooterSettings.php',
    'app/Models/CmsPage.php',
    'app/Models/NewsletterSubscriber.php',
    'app/Http/Controllers/NewsletterSubscribeController.php',
    'app/Http/Controllers/PageController.php',
    'app/Filament/Forms/FooterSettingsForm.php',
    'app/Filament/Concerns/ManagesStorefrontCmsPage.php',
    'app/Filament/Pages/PartnerPressReleases.php',
    'app/Filament/Pages/PartnerFaq.php',
    'app/Filament/Pages/PartnerEvents.php',
    'app/Filament/Pages/InvestorRelations.php',
    'app/Filament/Resources/InvestorResource.php',
    'app/Filament/Resources/CmsPageResource.php',
    'app/Filament/Resources/NewsletterSubscriberResource.php',
    'app/Filament/Resources/NewsletterSubscriberResource/Pages/ListNewsletterSubscribers.php',
    'database/migrations/2026_10_30_120000_create_newsletter_subscribers_table.php',
    'database/seeders/CmsPageSeeder.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/partials/storefront-footer.blade.php',
    'resources/views/partials/footer-partner-hub.blade.php',
    'resources/views/filament/pages/cms-page-editor.blade.php'
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
$seed = Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan db:seed --class=Database\\Seeders\\CmsPageSeeder --force --no-interaction 2>&1"
Write-Host $seed.Output
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
