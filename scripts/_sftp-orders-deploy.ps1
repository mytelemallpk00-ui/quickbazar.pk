$ErrorActionPreference = 'Stop'
Import-Module (Join-Path $env:TEMP 'PoshSSHManual\extract\Posh-SSH.psd1') -Force
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'public/js/beautage-money.js',
    'public/js/beautage-cart.js',
    'app/Support/MoneyFormat.php',
    'app/Models/Order.php',
    'app/Models/OrderItem.php',
    'app/Http/Controllers/OrderController.php',
    'app/Http/Requests/StoreOrderRequest.php',
    'app/Mail/OrderInvoiceMail.php',
    'app/Filament/Resources/OrderResource.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/OrderResource/Pages/ListOrders.php',
    'app/Filament/Resources/OrderResource/Pages/ViewOrder.php',
    'database/migrations/2026_10_26_210000_create_orders_table.php',
    'resources/views/mail/order-invoice.blade.php',
    'resources/views/layouts/app.blade.php',
    'resources/views/checkout.blade.php',
    'resources/views/home.blade.php',
    'resources/views/collections.blade.php',
    'resources/views/product-detail.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Resources/OrderResource/Pages $remoteRoot/app/Http/Requests $remoteRoot/app/Mail $remoteRoot/app/Support $remoteRoot/resources/views/mail $remoteRoot/public/js" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = "cd $remoteRoot && /usr/bin/php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
