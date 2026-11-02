$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'config/investor-payments.php',
    'database/migrations/2026_10_29_170000_create_investor_finance_tables.php',
    'database/migrations/2026_10_29_180000_add_application_fields_to_investors_table.php',
    'app/Http/Requests/InvestorApplicationRequest.php',
    'app/Mail/InvestorApplicationSubmittedMail.php',
    'app/Support/InvestorApplicationNotify.php',
    'resources/views/mail/investor-application-submitted.blade.php',
    'app/Models/Investor.php',
    'app/Models/Investment.php',
    'app/Models/ProfitDistribution.php',
    'app/Models/InvestorTransaction.php',
    'app/Models/User.php',
    'app/Support/AdminPermissions.php',
    'app/Contracts/InvestorPaymentGateway.php',
    'app/Services/InvestorFinance/InvestorFinanceService.php',
    'app/Services/InvestorFinance/InvestorProfitCalculator.php',
    'app/Support/InvestorPayoutSettings.php',
    'app/Filament/Pages/InvestorMonthlyPayouts.php',
    'resources/views/filament/pages/investor-monthly-payouts.blade.php',
    'resources/views/partials/investor-profit-calculator.blade.php',
    'app/Services/InvestorPayments/InvestorPaymentManager.php',
    'app/Services/InvestorPayments/ManualInvestorPaymentGateway.php',
    'app/Services/InvestorPayments/StripeInvestorPaymentGateway.php',
    'app/Providers/InvestorPaymentServiceProvider.php',
    'app/Providers/AppServiceProvider.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Http/Middleware/EnsureApprovedInvestor.php',
    'app/Http/Controllers/Customer/Auth/LoginController.php',
    'app/Http/Controllers/Customer/Auth/RegisterController.php',
    'app/Http/Controllers/Investor/Auth/Concerns/RedirectsAfterInvestorAuth.php',
    'app/Http/Controllers/Investor/Auth/InvestorLoginController.php',
    'app/Http/Controllers/Investor/Auth/InvestorRegisterController.php',
    'resources/views/auth/login.blade.php',
    'resources/views/auth/register.blade.php',
    'resources/views/investor/auth/login.blade.php',
    'resources/views/investor/auth/register.blade.php',
    'app/Http/Controllers/Investor/InvestorApplicationController.php',
    'app/Http/Controllers/Investor/InvestorPortalController.php',
    'app/Http/Controllers/Investor/InvestorStatementController.php',
    'app/Filament/Pages/InvestorFinances.php',
    'app/Filament/Widgets/InvestorFinanceStatsWidget.php',
    'app/Filament/Resources/InvestorResource.php',
    'app/Filament/Resources/InvestorResource/Pages/ListInvestors.php',
    'app/Filament/Resources/InvestorResource/Pages/CreateInvestor.php',
    'app/Filament/Resources/InvestorResource/Pages/EditInvestor.php',
    'resources/views/filament/pages/investor-finances.blade.php',
    'resources/views/filament/widgets/investor-finance-stats.blade.php',
    'resources/views/investor/apply.blade.php',
    'resources/views/investor/submitted.blade.php',
    'resources/views/investor/dashboard.blade.php',
    'resources/views/investor/ledger.blade.php',
    'resources/views/investor/receipt.blade.php',
    'resources/views/partials/become-investor-cta.blade.php',
    'resources/views/account/index.blade.php',
    'routes/web.php'
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
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_29_170000_create_investor_finance_tables.php --path=database/migrations/2026_10_29_180000_add_application_fields_to_investors_table.php && /usr/bin/php artisan view:clear && /usr/bin/php artisan optimize:clear" | Out-Null
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
