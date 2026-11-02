$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
if (-not $env:BEAUTAGE_SSH_PASSWORD) { throw 'Set BEAUTAGE_SSH_PASSWORD' }
$sec = ConvertTo-SecureString $env:BEAUTAGE_SSH_PASSWORD -AsPlainText -Force
$cred = New-Object System.Management.Automation.PSCredential('u662960912', $sec)
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'app/Models/User.php',
    'app/Providers/Filament/AdminPanelProvider.php',
    'app/Filament/Forms/AdminImageUpload.php',
    'app/Filament/Pages/Auth/EditProfile.php',
    'app/Filament/Resources/UserResource.php',
    'app/Filament/Resources/UserResource/Pages/ListUsers.php',
    'app/Filament/Resources/UserResource/Pages/CreateUser.php',
    'app/Filament/Resources/UserResource/Pages/EditUser.php',
    'database/migrations/2026_10_26_270000_add_profile_fields_to_users_table.php',
    'database/migrations/2026_10_27_140000_add_admin_roles_to_users_table.php',
    'database/migrations/2026_10_27_150000_add_address_cnic_to_users_table.php',
    'resources/views/filament/hooks/visit-store-button.blade.php',
    'app/Support/GoogleAnalytics.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $remoteRoot/app/Filament/Pages/Auth $remoteRoot/app/Filament/Resources/UserResource/Pages $remoteRoot/resources/views/filament/hooks" | Out-Null
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = "cd $remoteRoot && php artisan migrate --force && php artisan optimize:clear"
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 120
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
