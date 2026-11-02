@php
    $previewTitle = \Illuminate\Support\Str::limit(strip_tags((string) ($title ?? 'Page title')), 70, '…');
    $previewDescription = \Illuminate\Support\Str::limit(strip_tags((string) ($description ?? '')), 160, '…');
    $previewUrl = (string) ($url ?? url('/'));
    $host = parse_url($previewUrl, PHP_URL_HOST) ?: parse_url(config('app.url'), PHP_URL_HOST);
    $path = parse_url($previewUrl, PHP_URL_PATH) ?: '/';
    $focusKeyword = filled($focusKeyword ?? null) ? (string) $focusKeyword : null;
@endphp
<div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
    <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Google preview</p>
    @if($focusKeyword)
        <p class="mb-2 text-[11px] text-gray-500 dark:text-gray-400">Focus keyword: <span class="font-medium text-primary-600">{{ $focusKeyword }}</span></p>
    @endif
    <div class="max-w-2xl font-[Arial,sans-serif]">
        <div class="mb-0.5 flex items-center gap-2 text-sm leading-snug text-[#202124] dark:text-gray-200">
            <span class="inline-flex h-[26px] w-[26px] shrink-0 items-center justify-center rounded-full bg-gray-100 text-[11px] font-bold text-gray-600 dark:bg-gray-800 dark:text-gray-300">B</span>
            <div>
                <div class="text-sm text-[#202124] dark:text-gray-100">{{ $host }}</div>
                <div class="text-xs text-[#4d5156] dark:text-gray-400">{{ $path }}</div>
            </div>
        </div>
        <div class="text-xl leading-snug text-[#1a0dab] dark:text-blue-400">{{ $previewTitle }}</div>
        <p class="mt-1 text-sm leading-relaxed text-[#4d5156] dark:text-gray-400">{{ $previewDescription }}</p>
    </div>
</div>
