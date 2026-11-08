@php
    $card = $card ?? [];
    $mode = $mode ?? 'quick';
    $image = $card['image'] ?: ($placeholder ?? '');
@endphp
@if ($mode === 'add') data-card-add @else data-quick-view @endif
data-id="{{ (int) ($card['id'] ?? 0) }}"
data-name="{{ $card['name'] ?? '' }}"
data-price="{{ $card['price'] ?? 0 }}"
data-old="{{ $card['old_price'] ?? '' }}"
data-image="{{ $image }}"
data-category="{{ $card['category'] ?? '' }}"
data-desc="{{ $card['short'] ?? '' }}"
data-url="{{ $card['url'] ?? route('collections') }}"
data-stock="{{ ! empty($card['in_stock']) ? '1' : '0' }}"
