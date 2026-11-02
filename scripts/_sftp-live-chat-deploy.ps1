$ErrorActionPreference = 'Stop'
. (Join-Path $PSScriptRoot '_ensure-posh-ssh.ps1') | Out-Null
. (Join-Path $PSScriptRoot '_resolve-hostinger-credential.ps1')
$cred = Get-BeautageHostingerCredential
$remoteRoot = '/home/u662960912/domains/beige-ape-286733.hostingersite.com/public_html'
$localRoot = 'c:\xampp\htdocs\beautage'
$files = @(
    'routes/web.php',
    'database/migrations/2026_10_29_160000_create_live_chat_tables.php',
    'database/seeders/ChatbotRuleSeeder.php',
    'app/Models/Chat.php',
    'app/Models/Message.php',
    'app/Models/ChatbotRule.php',
    'app/Support/LiveChatSettings.php',
    'app/Services/Chat/ChatbotResponder.php',
    'app/Services/Chat/SimpleSupportChatbot.php',
    'app/Services/Chat/ChatOrderLookup.php',
    'app/Services/Chat/ChatProductLookup.php',
    'app/Services/Chat/ChatCheckoutFacts.php',
    'app/Services/Courier/CourierTrackingService.php',
    'app/Services/Chat/ProductChatCatalog.php',
    'app/Services/Chat/SiteKnowledgeChat.php',
    'app/Http/Controllers/ChatController.php',
    'app/Http/Requests/SendChatMessageRequest.php',
    'app/Filament/Resources/ChatbotRuleResource.php',
    'app/Filament/Resources/ChatbotRuleResource/Pages/ListChatbotRules.php',
    'app/Filament/Resources/ChatbotRuleResource/Pages/CreateChatbotRule.php',
    'app/Filament/Resources/ChatbotRuleResource/Pages/EditChatbotRule.php',
    'resources/views/partials/live-chat-widget.blade.php',
    'resources/views/layouts/app.blade.php'
)
$ssh = New-SSHSession -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -AcceptKey -Force
$dirs = @(
    "$remoteRoot/app/Services/Chat",
    "$remoteRoot/app/Http/Requests",
    "$remoteRoot/app/Filament/Resources/ChatbotRuleResource/Pages",
    "$remoteRoot/resources/views/partials",
    "$remoteRoot/database/seeders"
)
foreach ($d in $dirs) {
    Invoke-SSHCommand -SessionId $ssh.SessionId -Command "mkdir -p $d" | Out-Null
}
foreach ($f in $files) {
    $local = Join-Path $localRoot ($f -replace '/','\')
    $remoteDir = "$remoteRoot/" + ($f -replace '/[^/]+$','')
    Set-SCPItem -ComputerName '187.124.193.196' -Port 65002 -Credential $cred -Path $local -Destination $remoteDir -AcceptKey -Force
    Write-Host "OK $f"
}
$cmd = @"
cd $remoteRoot && /usr/bin/php artisan migrate --force --path=database/migrations/2026_10_29_160000_create_live_chat_tables.php && /usr/bin/php artisan db:seed --class=ChatbotRuleSeeder --force && /usr/bin/php artisan optimize:clear
"@
$r = Invoke-SSHCommand -SessionId $ssh.SessionId -Command $cmd -TimeOut 180
$r.Output
Remove-SSHSession -SessionId $ssh.SessionId | Out-Null
