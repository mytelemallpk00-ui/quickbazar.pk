param(
    [Parameter(Mandatory = $true)]
    [string] $Token,

    [string] $SiteUrl = 'https://beige-ape-286733.hostingersite.com'
)

$ErrorActionPreference = 'Stop'

$uri = '{0}/_deploy/flush-cache?token={1}' -f $SiteUrl.TrimEnd('/'), [uri]::EscapeDataString($Token)

try {
    $response = Invoke-WebRequest -Uri $uri -UseBasicParsing -TimeoutSec 180 -Method Get
    if ($response.StatusCode -eq 200 -and $response.Content -match 'OK') {
        Write-Host 'Cache flush OK (HTTP)'
        return $true
    }
    Write-Warning "Unexpected flush response: $($response.StatusCode)"
}
catch {
    Write-Warning "HTTP cache flush failed: $($_.Exception.Message)"
}

return $false
