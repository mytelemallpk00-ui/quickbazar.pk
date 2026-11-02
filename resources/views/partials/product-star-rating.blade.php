@props(['rating' => 0, 'size' => 'w-4 h-4', 'showValue' => false])
@php
    $value = max(0, min(5, (float) $rating));
    $full = (int) floor($value);
    $partial = $value - $full;
@endphp
<div {{ $attributes->merge(['class' => 'inline-flex items-center text-amber-400 gap-0.5']) }}>
    @for ($i = 1; $i <= 5; $i++)
        @if ($i <= $full)
            <i data-lucide="star" class="{{ $size }} fill-current"></i>
        @elseif ($i === $full + 1 && $partial >= 0.25)
            <i data-lucide="star-half" class="{{ $size }} fill-current"></i>
        @else
            <i data-lucide="star" class="{{ $size }} text-amber-200"></i>
        @endif
    @endfor
    @if ($showValue)
        <span class="text-xs font-bold text-slate-700 ml-1">{{ number_format($value, 1) }} / 5.0</span>
    @endif
</div>
