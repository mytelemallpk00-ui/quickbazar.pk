# Hostinger SFTP/SSH — use session env only (never written to disk).
$ErrorActionPreference = 'Stop'

function Get-BeautageHostingerCredential {
    [CmdletBinding()]
    param()

    $plain = $env:BEAUTAGE_SSH_PASSWORD
    if (-not $plain) {
        throw 'Set BEAUTAGE_SSH_PASSWORD in this terminal session before running deploy scripts.'
    }

    $sec = ConvertTo-SecureString $plain -AsPlainText -Force
    return [PSCredential]::new('u662960912', $sec)
}
