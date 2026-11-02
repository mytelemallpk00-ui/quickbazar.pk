@php
    use App\Support\MoneyFormat;
    $payload = $payload ?? [];
    $saleText = $payload['sale_text'] ?? null;
    $introHtml = $broadcast->body_html ?: ($payload['intro_html'] ?? '<p>Fresh picks, bundle savings, and limited offers — curated for our newsletter community.</p>');
    $ctaLabel = $payload['cta_label'] ?? 'Shop new arrivals';
    $ctaUrl = $payload['cta_url'] ?? url('/collections');
@endphp
@extends('mail.layouts.beautage', [
    'theme' => 'store',
    'preheader' => $broadcast->preheader,
    'headline' => $broadcast->headline ?: 'This week at Beautage',
    'badge' => $payload['badge'] ?? 'STORE · PRODUCTS & SALES',
    'title' => $broadcast->subject,
    'footerNote' => 'Store newsletter — products, bundles & promotions.',
])

@section('content')
@if($saleText)
<div style="margin:0 0 20px;padding:14px 16px;border-radius:12px;background:linear-gradient(90deg,#fdf2f8,#faf5ff);border:1px solid #f9a8d4;">
    <div style="font-size:10px;font-weight:800;letter-spacing:0.14em;text-transform:uppercase;color:#db2777;margin-bottom:4px;">Flash &amp; sales</div>
    <div style="font-size:14px;font-weight:700;color:#5d267d;">{{ $saleText }}</div>
</div>
@endif

<div style="font-size:14px;line-height:1.7;color:#334155;">{!! $introHtml !!}</div>

@if(!empty($bundleHighlight))
<div style="margin:24px 0;padding:18px;border-radius:16px;border:2px dashed #c4b5fd;background:#faf5ff;">
    <div style="font-size:10px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#7c3aed;margin-bottom:6px;">Bundle deal</div>
    @if(!empty($bundleHighlight['badge']))
    <div style="font-size:11px;font-weight:700;color:#db2777;margin-bottom:4px;">{{ $bundleHighlight['badge'] }}</div>
    @endif
    <div style="font-size:16px;font-weight:800;color:#5d267d;">{{ $bundleHighlight['title'] ?? 'Bundle offer' }}</div>
    @if(!empty($bundleHighlight['subtitle']))
    <div style="font-size:13px;color:#64748b;margin-top:6px;">{{ $bundleHighlight['subtitle'] }}</div>
    @endif
    @if(!empty($bundleHighlight['url']))
    <div style="margin-top:12px;"><a href="{{ $bundleHighlight['url'] }}" style="font-size:12px;font-weight:800;color:#5d267d;text-decoration:none;">View bundle →</a></div>
    @endif
</div>
@endif

@if(count($products) > 0)
<div style="margin-top:8px;">
    <div style="font-size:11px;font-weight:800;letter-spacing:0.12em;text-transform:uppercase;color:#5d267d;margin-bottom:12px;">Featured products</div>
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
        <div style="margin-top:8px;font-size:14px;font-weight:800;color:#5d267d;">
            {{ MoneyFormat::pkr($product['price']) }}
            @if(!empty($product['old_price']) && $product['old_price'] > $product['price'])
            <span style="font-size:12px;color:#94a3b8;text-decoration:line-through;font-weight:600;margin-left:6px;">{{ MoneyFormat::pkr($product['old_price']) }}</span>
            @endif
        </div>
        <div style="margin-top:10px;"><a href="{{ $product['url'] }}" style="font-size:12px;font-weight:800;color:#ec4899;text-decoration:none;">Shop now →</a></div>
    </td>
    </tr>
    </table>
    @endforeach
</div>
@endif

<div style="text-align:center;margin-top:28px;">
    <a href="{{ $ctaUrl }}" style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#ec4899,#db2777);color:#ffffff;font-size:12px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;">{{ $ctaLabel }}</a>
</div>
@endsection
