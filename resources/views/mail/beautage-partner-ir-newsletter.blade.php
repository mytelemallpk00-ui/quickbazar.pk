@php
    $payload = $payload ?? [];
    $bodyHtml = $broadcast->body_html ?: ($payload['body_html'] ?? '<p>Latest partner update from Beautage PVT Ltd.</p>');
    $ctaLabel = $payload['cta_label'] ?? 'Read on beautage.com';
    $ctaUrl = $payload['cta_url'] ?? url('/press-releases');
    $sectionLabel = match ($payload['cms_page_type'] ?? null) {
        'press-releases' => 'Press release',
        'faq' => 'FAQ update',
        'events' => 'Events',
        default => 'Partner briefing',
    };
@endphp
@extends('mail.layouts.beautage', [
    'theme' => 'partner',
    'preheader' => $broadcast->preheader,
    'headline' => $broadcast->headline ?: 'Partner update',
    'badge' => $payload['badge'] ?? 'PARTNER UPDATES',
    'title' => $broadcast->subject,
    'footerNote' => 'Partner briefing list — corporate updates only.',
])

@section('content')
<div style="margin:0 0 16px;padding:10px 14px;border-radius:10px;background:#f5f3ff;border-left:4px solid #22d3ee;font-size:11px;font-weight:800;letter-spacing:0.1em;text-transform:uppercase;color:#5d267d;">
    {{ $sectionLabel }}
</div>

<div style="font-size:14px;line-height:1.75;color:#334155;">{!! $bodyHtml !!}</div>

@if(!empty($payload['highlights']) && is_array($payload['highlights']))
<div style="margin-top:22px;">
    @foreach($payload['highlights'] as $item)
    <div style="padding:12px 0;border-bottom:1px solid #ede9fe;">
        <div style="font-size:13px;font-weight:800;color:#5d267d;">{{ $item['title'] ?? '' }}</div>
        @if(!empty($item['text']))
        <div style="font-size:12px;color:#64748b;margin-top:4px;">{{ $item['text'] }}</div>
        @endif
    </div>
    @endforeach
</div>
@endif

<div style="text-align:center;margin-top:28px;">
    <a href="{{ $ctaUrl }}" style="display:inline-block;padding:14px 28px;border-radius:999px;background:linear-gradient(135deg,#5d267d,#3b0764);color:#ffffff;font-size:12px;font-weight:800;letter-spacing:0.08em;text-transform:uppercase;text-decoration:none;border:1px solid rgba(255,255,255,0.2);">{{ $ctaLabel }}</a>
</div>
@endsection
