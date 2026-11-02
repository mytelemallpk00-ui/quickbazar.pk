$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'routes/console.php',
    'config/social.php',
    'app/Providers/AppServiceProvider.php',
    'app/Providers/SocialAutomationServiceProvider.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Support/AdminPermissions.php',
    'app/Support/Social/SocialPlatforms.php',
    'app/Support/Social/SocialCredentialResolver.php',
    'app/Support/Social/SocialApiCredentialStore.php',
    'app/Models/SocialApiSetting.php',
    'app/Models/ScheduledPost.php',
    'app/Services/Social/SocialContentGenerator.php',
    'app/Services/Social/SocialScheduleBuilder.php',
    'app/Services/Social/SocialPostPublisher.php',
    'app/Services/Social/FacebookService.php',
    'app/Services/Social/InstagramService.php',
    'app/Services/Social/LinkedInService.php',
    'app/Services/Social/YouTubeService.php',
    'app/Services/Social/TelegramService.php',
    'app/Services/Social/Contracts/SocialPublisher.php',
    'app/Services/Social/Concerns/InteractsWithSocialCredentials.php',
    'app/Jobs/PublishScheduledPostJob.php',
    'app/Console/Commands/PublishSocialPostsCommand.php',
    'app/Http/Controllers/Admin/SocialAutomationController.php',
    'app/Filament/Pages/SocialPostScheduler.php',
    'app/Filament/Pages/SocialApiCredentials.php',
    'resources/views/filament/pages/social-post-scheduler.blade.php',
    'resources/views/filament/pages/social-api-credentials.blade.php',
    'resources/views/filament/partials/social-post-preview.blade.php',
    'app/Support/Social/SocialPlatformGuides.php',
    'app/Support/Social/SocialChannelCatalog.php',
    'app/Models/SocialMarketingChannel.php',
    'app/Filament/Pages/SocialGoogleIndexing.php',
    'app/Filament/Resources/SocialMarketingChannelResource.php',
    'app/Filament/Resources/SocialMarketingChannelResource/Pages/ListSocialMarketingChannels.php',
    'app/Filament/Resources/SocialMarketingChannelResource/Pages/CreateSocialMarketingChannel.php',
    'app/Filament/Resources/SocialMarketingChannelResource/Pages/EditSocialMarketingChannel.php',
    'database/migrations/2026_10_29_120000_create_social_automation_tables.php',
    'database/migrations/2026_10_31_100000_create_social_marketing_channels.php',
    'database/migrations/2026_10_31_100001_widen_social_api_settings_platform.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$dirs = @(
    "$remoteRoot/app/Console/Commands",
    "$remoteRoot/app/Jobs",
    "$remoteRoot/app/Http/Controllers/Admin",
    "$remoteRoot/app/Services/Social/Contracts",
    "$remoteRoot/app/Services/Social/Concerns",
    "$remoteRoot/app/Support/Social",
    "$remoteRoot/app/Filament/Pages",
    "$remoteRoot/app/Filament/Resources/SocialMarketingChannelResource/Pages",
    "$remoteRoot/app/Models",
    "$remoteRoot/resources/views/filament/pages",
    "$remoteRoot/resources/views/filament/partials",
    "$remoteRoot/database/migrations"
)
foreach ($d in $dirs) {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $d" | Out-Null
}
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        Write-Host "SKIP missing $f"
        continue
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = @"
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_31_100000_create_social_marketing_channels.php --path=database/migrations/2026_10_31_100001_widen_social_api_settings_platform.php && /usr/bin/php artisan optimize:clear
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
