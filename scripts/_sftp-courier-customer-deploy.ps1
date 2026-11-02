$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'config/courier.php',
    'database/migrations/2026_10_29_150000_courier_integration_and_customer_orders.php',
    'app/Models/Order.php',
    'app/Models/User.php',
    'app/Models/CourierApiSetting.php',
    'app/Support/PromoCodes.php',
    'app/Support/Courier/CourierPlatforms.php',
    'app/Support/Courier/PakistanZoneRates.php',
    'app/Support/Courier/CourierApiCredentialStore.php',
    'app/Support/Courier/CourierCredentialResolver.php',
    'app/Services/Courier/Contracts/CourierClient.php',
    'app/Services/Courier/Concerns/InteractsWithCourierCredentials.php',
    'app/Services/Courier/AbstractCourierClient.php',
    'app/Services/Courier/TcsCourierClient.php',
    'app/Services/Courier/LeopardsCourierClient.php',
    'app/Services/Courier/BlueExCourierClient.php',
    'app/Services/Courier/MnpCourierClient.php',
    'app/Services/Courier/CourierRateService.php',
    'app/Services/Courier/CourierBookingService.php',
    'app/Jobs/BookCourierShipmentJob.php',
    'app/Http/Controllers/OrderController.php',
    'app/Http/Controllers/CheckoutController.php',
    'app/Http/Controllers/CheckoutShippingController.php',
    'app/Http/Controllers/TrackOrderController.php',
    'app/Http/Requests/StoreOrderRequest.php',
    'app/Http/Requests/ShippingRatesRequest.php',
    'app/Http/Requests/StoreCustomerRegisterRequest.php',
    'app/Http/Controllers/Customer/AccountController.php',
    'app/Http/Controllers/Customer/Auth/LoginController.php',
    'app/Http/Controllers/Customer/Auth/RegisterController.php',
    'app/Filament/Pages/CourierApiCredentials.php',
    'app/Filament/Resources/OrderResource.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'resources/views/checkout.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/auth/login.blade.php',
    'resources/views/auth/register.blade.php',
    'resources/views/account/index.blade.php',
    'resources/views/account/order-show.blade.php',
    'resources/views/pages/track-order.blade.php',
    'resources/views/filament/pages/courier-api-credentials.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$dirs = @(
    "$remoteRoot/app/Support/Courier",
    "$remoteRoot/app/Services/Courier/Contracts",
    "$remoteRoot/app/Services/Courier/Concerns",
    "$remoteRoot/app/Jobs",
    "$remoteRoot/app/Http/Controllers/Customer/Auth",
    "$remoteRoot/app/Http/Controllers/Customer",
    "$remoteRoot/app/Http/Requests",
    "$remoteRoot/app/Filament/Pages",
    "$remoteRoot/resources/views/auth",
    "$remoteRoot/resources/views/account",
    "$remoteRoot/resources/views/filament/pages",
    "$remoteRoot/config",
    "$remoteRoot/database/migrations"
)
foreach ($d in $dirs) {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $d" | Out-Null
}
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    if (-not (Test-Path $local)) {
        Write-Host "SKIP $f"
        continue
    }
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = @"
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_29_150000_courier_integration_and_customer_orders.php && /usr/bin/php artisan optimize:clear
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
