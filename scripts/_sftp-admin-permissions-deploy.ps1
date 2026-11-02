$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null

if (-not $env:BEAUTAGE_SSH_PASSWORD) {
    throw 'Set BEAUTAGE_SSH_PASSWORD before running this script.'
}

$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'

$files = @(
    'app/Models/User.php',
    'app/Support/AdminPermissions.php',
    'app/Filament/Concerns/AuthorizesAdminAccess.php',
    'app/Filament/Concerns/AuthorizesAdminPage.php',
    'app/Filament/Pages/AdminDashboard.php',
    'app/Filament/Resources/UserResource.php',
    'app/Filament/Resources/UserResource/Pages/ListUsers.php',
    'app/Filament/Resources/UserResource/Pages/CreateUser.php',
    'app/Filament/Resources/UserResource/Pages/EditUser.php',
    'app/Filament/Resources/ProductResource.php',
    'app/Filament/Resources/CategoryResource.php',
    'app/Filament/Resources/ProductBundleResource.php',
    'app/Filament/Resources/OrderResource.php',
    'app/Filament/Resources/ProductReviewResource.php',
    'app/Filament/Resources/BlogPostResource.php',
    'app/Filament/Resources/CmsPageResource.php',
    'app/Filament/Resources/CareerOpeningResource.php',
    'app/Filament/Pages/BulkProductImport.php',
    'app/Filament/Pages/HeaderNavigationSettings.php',
    'app/Filament/Pages/HomepageSettings.php',
    'app/Filament/Pages/ThemeSettings.php',
    'app/Filament/Pages/ReviewSettings.php',
    'app/Filament/Pages/InvestorRelations.php',
    'database/migrations/2026_10_27_140000_add_admin_roles_to_users_table.php'
)

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
try {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Models $remoteRoot/app/Support $remoteRoot/app/Filament/Concerns $remoteRoot/app/Filament/Pages $remoteRoot/app/Filament/Resources/UserResource/Pages $remoteRoot/app/Filament/Resources $remoteRoot/database/migrations" -TimeOut 60 | Out-Null
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}

foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}

$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
try {
    $cmd = "cd $remoteRoot; /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_27_140000_add_admin_roles_to_users_table.php"
    $r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 120
    $r.Output
}
finally {
    Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
}

& (Join-Path $PSScriptRoot '_deploy-post-upload.ps1') -Credential $cred -RemoteRoot $remoteRoot -LocalRoot $localRoot
