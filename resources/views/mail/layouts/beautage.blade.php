@php
    $preheader = $preheader ?? '';
    $theme = $theme ?? 'store';
    $headerGradient = $theme === 'partner'
        ? 'linear-gradient(135deg,#3b0764 0%,#5d267d 50%,#2e1065 100%)'
        : 'linear-gradient(135deg,#5d267d 0%,#831843 55%,#5d267d 100%)';
    $accent = $theme === 'partner' ? '#22d3ee' : '#ec4899';
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Beautage' }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f5f0fa;font-family:'Segoe UI',Tahoma,Geneva,Verdana,sans-serif;color:#1e293b;">
@if(filled($preheader))
<div style="display:none;max-height:0;overflow:hidden;opacity:0;color:transparent;">{{ $preheader }}</div>
@endif
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color:#f5f0fa;padding:24px 12px;">
<tr><td align="center">
<table role="presentation" width="600" cellspacing="0" cellpadding="0" border="0" style="max-width:600px;width:100%;background:#ffffff;border-radius:20px;overflow:hidden;box-shadow:0 12px 40px rgba(59,7,100,0.12);">
<tr>
<td style="background:{{ $headerGradient }};padding:28px 32px;text-align:center;">
    <div style="font-size:11px;letter-spacing:0.18em;text-transform:uppercase;color:rgba(255,255,255,0.75);font-weight:700;">Beautage</div>
    <div style="font-family:Georgia,'Times New Roman',serif;font-size:26px;line-height:1.25;color:#ffffff;font-weight:700;margin-top:8px;">{{ $headline ?? 'Updates for you' }}</div>
    @if(!empty($badge))
    <div style="display:inline-block;margin-top:12px;padding:6px 12px;border-radius:999px;border:1px solid rgba(255,255,255,0.35);font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:{{ $accent }};">{{ $badge }}</div>
    @endif
</td>
</tr>
<tr>
<td style="padding:32px;">
    @yield('content')
</td>
</tr>
<tr>
<td style="padding:20px 32px 28px;background:#faf5ff;border-top:1px solid #ede9fe;text-align:center;font-size:11px;line-height:1.6;color:#64748b;">
    <p style="margin:0 0 8px;">© {{ date('Y') }} Beautage PVT Ltd.</p>
    <p style="margin:0;">{{ $footerNote ?? 'You subscribed on beautage.com.' }} · <a href="{{ url('/') }}" style="color:#5d267d;font-weight:600;text-decoration:none;">Visit website</a></p>
</td>
</tr>
</table>
</td></tr>
</table>
</body>
</html>
