$path = 'c:\xampp\htdocs\beautage\resources\views\layouts\app.blade.php'
$lines = Get-Content $path
$start = 0
$end = 0
for ($i = 0; $i -lt $lines.Length; $i++) {
    if ($lines[$i] -match 'CATEGORY STRIP WITH FULL MEGA MENU') { $start = $i }
    if ($lines[$i] -match 'Mobile Navigation Drawer' -and $end -eq 0 -and $i -gt $start) { $end = $i - 1; break }
}
$before = $lines[0..($start - 1)]
$newBlock = @(
    "        @include('partials.storefront-header-nav', ['storefrontNav' => `$storefrontNav ?? []])",
    ''
)
$after = $lines[$end..($lines.Length - 1)]
$combined = $before + $newBlock + $after
Set-Content -Path $path -Value $combined -Encoding UTF8
Write-Host "Patched layout nav: removed lines $($start + 1)-$($end + 1)"
