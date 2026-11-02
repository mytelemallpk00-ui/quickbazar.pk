$zip = Join-Path $env:TEMP 'PoshSSHManual\Posh-SSH.zip'
$moduleRoot = Join-Path $env:TEMP 'PoshSSHManual\module'
if (-not (Test-Path $zip)) {
    throw "Missing $zip"
}
if (-not (Test-Path (Join-Path $moduleRoot 'Posh-SSH.psd1'))) {
    Remove-Item $moduleRoot -Recurse -Force -ErrorAction SilentlyContinue
    New-Item -ItemType Directory -Path $moduleRoot -Force | Out-Null
    Add-Type -AssemblyName System.IO.Compression.FileSystem
    [System.IO.Compression.ZipFile]::ExtractToDirectory($zip, $moduleRoot)
}
Import-Module (Join-Path $moduleRoot 'Posh-SSH.psd1') -Force
Write-Output (Join-Path $moduleRoot 'Posh-SSH.psd1')
