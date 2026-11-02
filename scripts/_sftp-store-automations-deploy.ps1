$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'routes/console.php',
    'app/Support/StoreAutomationSettings.php',
    'app/Models/AbandonedCart.php',
    'app/Models/StoreAutomationDispatch.php',
    'app/Models/ReorderReminderSchedule.php',
    'app/Models/Product.php',
    'app/Services/StoreAutomation/StoreAutomationRunner.php',
    'app/Services/StoreAutomation/OrderAutomationPlanner.php',
    'app/Services/StoreAutomation/AbandonedCartSyncService.php',
    'app/Mail/StoreAutomationMail.php',
    'app/Http/Controllers/Store/AbandonedCartSyncController.php',
    'app/Http/Controllers/OrderController.php',
    'app/Console/Commands/RunStoreAutomationsCommand.php',
    'app/Filament/Pages/StoreAutomations.php',
    'app/Filament/Resources/ProductResource.php',
    'resources/views/filament/pages/store-automations.blade.php',
    'resources/views/mail/store-automation.blade.php',
    'public/js/beautage-cart.js',
    'database/migrations/2026_10_31_120000_create_store_automation_tables.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$dirs = @(
    "$remoteRoot/app/Services/StoreAutomation",
    "$remoteRoot/app/Http/Controllers/Store",
    "$remoteRoot/app/Console/Commands",
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
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_31_120000_create_store_automation_tables.php && /usr/bin/php artisan optimize:clear
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
