@php
    use App\Support\MoneyFormat;
    $footerNotes = [
        'abandoned_cart' => 'Cart reminder — Beautage store.',
        'post_purchase' => 'Order updates & curated offers from Beautage.',
        'reorder' => 'Smart reorder reminder — Beautage.',
        'low_stock' => 'Internal inventory alert for Beautage admin.',
    ];
@endphp
@extends('mail.layouts.beautage', [
    'theme' => 'store',
    'preheader' => $headline ?? '',
    'headline' => $headline ?? 'Beautage',
    'badge' => $badge ?? 'STORE',
    'title' => $subject ?? 'Beautage',
    'footerNote' => $footerNotes[$automationType ?? 'post_purchase'] ?? 'Beautage store.',
])

@section('content')
@if(!empty($orderNumber))
<div style="margin:0 0 16px;padding:12px 14px;border-radius:12px;background:#faf5ff;border:1px solid #e9cce8;font-size:13px;color:#5d267d;font-weight:700;">
    Order #{{ $orderNumber }}
</div>
@endif

@if(filled($discountCode ?? null))
<div style="margin:0 0 16px;padding:12px 14px;border-radius:12px;background:linear-gradient(90deg,#fdf2f8,#faf5ff);border:1px dashed #f9a8d4;text-align:center;">
    <div style="font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#db2777;">Your code</div>
    <div style="font-size:18px;font-weight:800;color:#5d267d;margin-top:4px;">{{ $discountCode }}</div>
</div>
@endif

<div style="font-size:14px;line-height:1.7;color:#334155;">{!! $bodyHtml ?? '' !!}</div>

@if(!empty($cartItems))
<div style="margin-top:20px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#5d267d;margin-bottom:10px;">In your bag</div>
    @foreach($cartItems as $item)
    <div style="padding:10px 0;border-bottom:1px solid #ede9fe;font-size:13px;color:#1e293b;">
        <strong>{{ $item['name'] ?? 'Item' }}</strong>
        @if(!empty($item['qty']))
        <span style="color:#64748b;"> × {{ $item['qty'] }}</span>
        @endif
        @if(isset($item['price']))
        <span style="float:right;font-weight:700;color:#5d267d;">{{ MoneyFormat::pkr((float) $item['price']) }}</span>
        @endif
    </div>
    @endforeach
</div>
@endif

@if(!empty($products))
<div style="margin-top:8px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#5d267d;margin-bottom:12px;">Recommended for you</div>
    @foreach($products as $product)
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="border:1px solid #ede9fe;border-radius:14px;overflow:hidden;margin-bottom:14px;">
    <tr>
    @if(!empty($product['image_url']))
    <td width="96" style="width:96px;background:#faf5ff;vertical-align:top;">
        <img src="{{ $product['image_url'] }}" alt="" width="96" height="96" style="display:block;width:96px;height:96px;object-fit:cover;">
    </td>
    @endif
    <td style="padding:14px 16px;vertical-align:top;">
        @if(!empty($product['badge']))
        <div style="font-size:10px;font-weight:800;color:#db2777;text-transform:uppercase;margin-bottom:4px;">{{ $product['badge'] }}</div>
        @endif
        <div style="font-size:14px;font-weight:700;color:#1e293b;line-height:1.35;">{{ $product['name'] }}</div>
        <div style="margin-top:8px;font-size:14px;font-weight:800;color:#5d267d;">{{ MoneyFormat::pkr($product['price']) }}</div>
        <div style="margin-top:10px;"><a href="{{ $product['url'] }}" style="font-size:12px;font-weight:800;color:#ec4899;text-decoration:none;">View →</a></div>
    </td>
    </tr>
    </table>
    @endforeach
</div>
@endif

<div style="text-align:center;margin-top:28px;">
    <a href="{{ $ctaUrl }}" style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#5d267d,#831843);color:#ffffff;font-size:12px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;">{{ $ctaLabel }}</a>
</div>
@endsection
