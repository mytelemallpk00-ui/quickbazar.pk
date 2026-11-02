param(
    [string]$BaseUrl,
    [string]$Auth,
    [string]$Rest,
    [string[]]$Files,
    [string]$Root = 'c:\xampp\htdocs\beautage'
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
