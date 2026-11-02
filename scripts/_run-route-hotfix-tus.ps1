$ErrorActionPreference = 'Stop'
$authPath = Join-Path $PSScriptRoot '_tus-auth-once.json'
$c = Get-Content $authPath -Raw | ConvertFrom-Json

& (Join-Path $PSScriptRoot '_hostinger-tus-deploy.ps1') `
    -BaseUrl $c.baseUrl `
    -Auth $c.auth `
    -Rest $c.rest `
    -Files @(
        'app/Http/Controllers/ProgrammaticSeoLandingController.php',
        'app/Models/ProgrammaticSeoLanding.php',
        'app/Http/Controllers/Store/AbandonedCartSyncController.php',
        'app/Services/StoreAutomation/AbandonedCartSyncService.php',
        'app/Support/StoreAutomationSettings.php',
        'app/Models/AbandonedCart.php'
    )
