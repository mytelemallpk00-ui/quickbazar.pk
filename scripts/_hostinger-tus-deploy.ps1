param(
    [Parameter(Mandatory = $true)]
    [string] $BaseUrl,

    [Parameter(Mandatory = $true)]
    [string] $Auth,

    [Parameter(Mandatory = $true)]
    [string] $Rest,

    [Parameter(Mandatory = $true)]
    [string[]] $Files,

    [string] $Root = 'c:\xampp\htdocs\beautage',

    [switch] $RunMigrations
)

$ErrorActionPreference = 'Stop'

function Upload-File($remote, $local) {
    $size = (Get-Item $local).Length
    $target = "$BaseUrl/$remote"
    $post = curl.exe -s -i -X POST "${target}?override=true" -H "X-Auth: $Auth" -H "X-Auth-Rest: $Rest" -H 'Tus-Resumable: 1.0.0' -H "Upload-Length: $size" -H 'Upload-Offset: 0'
    if (-not ($post -match '201')) { throw "POST failed $remote`n$post" }
    $patch = curl.exe -s -i -X PATCH "${target}?override=true" -H "X-Auth: $Auth" -H "X-Auth-Rest: $Rest" -H 'Tus-Resumable: 1.0.0' -H 'Content-Type: application/offset+octet-stream' -H 'Upload-Offset: 0' --data-binary "@$local"
    if (-not ($patch -match '204')) { throw "PATCH failed $remote`n$patch" }
    Write-Host "OK $remote"
}

foreach ($f in $Files) {
    Upload-File $f (Join-Path $Root ($f -replace '/','\'))
}

$token = [guid]::NewGuid().ToString()
$tokenPath = Join-Path $env:TEMP 'deploy-flush.token'
Set-Content -Path $tokenPath -Value $token -NoNewline -Encoding ascii
Upload-File 'storage/app/deploy-flush.token' $tokenPath
Remove-Item $tokenPath -Force

if ($RunMigrations) {
    $flagPath = Join-Path $env:TEMP 'deploy-run-migrations.flag'
    Set-Content -Path $flagPath -Value '1' -NoNewline
    Upload-File 'storage/app/deploy-run-migrations.flag' $flagPath
    Remove-Item $flagPath -Force
}

& (Join-Path $PSScriptRoot '_deploy-flush-cache.ps1') -Token $token
