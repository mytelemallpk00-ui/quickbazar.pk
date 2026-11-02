@php
    $gaReady = $gaEnabled && filled($gaId);
    $gscReady = $gscEnabled && filled($gscToken);
    $bingReady = ($bingEnabled ?? false) && filled($bingToken ?? null);
    $yandexReady = ($yandexEnabled ?? false) && filled($yandexToken ?? null);
    $indexNowReady = ($indexNowEnabled ?? false) && filled($indexNowKey ?? null);
    $gmcReady = ($gmcEnabled ?? false) && filled($gmcFeedUrl ?? null);
    $metaReady = ($metaEnabled ?? false) && filled($metaPixelId ?? null);
    $metaCapiReady = ($metaCapiEnabled ?? false) && $metaReady;
    $ecommerceLive = ($ecommerceAuto ?? false) && ($gaReady || $metaReady);
@endphp
<div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">E-commerce events</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($ecommerceLive)
                        View · Cart · Checkout · Purchase
                    @else
                        GA4 ya Meta Pixel enable karein
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $ecommerceLive,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! $ecommerceLive,
            ])>
                {{ $ecommerceLive ? 'Auto' : 'Off' }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Google Analytics 4</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($gaReady)
                        Connected · {{ $gaId }}
                    @elseif($gaEnabled)
                        Enabled — waiting for valid Measurement ID
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $gaReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => $gaEnabled && ! $gaReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! $gaEnabled,
            ])>
                {{ $gaReady ? 'Live' : ($gaEnabled ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Meta Pixel + CAPI</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($metaReady)
                        Pixel {{ $metaPixelId }}
                        @if($metaCapiReady)
                            · Server Purchase on
                        @endif
                    @elseif($metaEnabled ?? false)
                        Paste Pixel ID
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $metaReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => ($metaEnabled ?? false) && ! $metaReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! ($metaEnabled ?? false),
            ])>
                {{ $metaReady ? 'Live' : (($metaEnabled ?? false) ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Google Search Console</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($gscReady)
                        Verification tag ready on storefront
                    @elseif($gscEnabled)
                        Enabled — paste verification code
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $gscReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => $gscEnabled && ! $gscReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! $gscEnabled,
            ])>
                {{ $gscReady ? 'Ready' : ($gscEnabled ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Bing + Yahoo</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($bingReady)
                        msvalidate.01 tag on storefront
                    @elseif($bingEnabled ?? false)
                        Paste Bing verification code
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $bingReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => ($bingEnabled ?? false) && ! $bingReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! ($bingEnabled ?? false),
            ])>
                {{ $bingReady ? 'Ready' : (($bingEnabled ?? false) ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Yandex Webmaster</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($yandexReady)
                        Verification tag ready
                    @elseif($yandexEnabled ?? false)
                        Paste Yandex code
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $yandexReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => ($yandexEnabled ?? false) && ! $yandexReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! ($yandexEnabled ?? false),
            ])>
                {{ $yandexReady ? 'Ready' : (($yandexEnabled ?? false) ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">IndexNow</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($indexNowReady)
                        Key file hosted (/{key}.txt)
                    @elseif($indexNowEnabled ?? false)
                        Enter valid hex key
                    @else
                        Not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $indexNowReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => ($indexNowEnabled ?? false) && ! $indexNowReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! ($indexNowEnabled ?? false),
            ])>
                {{ $indexNowReady ? 'Live' : (($indexNowEnabled ?? false) ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-900 md:col-span-2">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Google Merchant Center</p>
                <p class="mt-1 text-sm font-medium text-gray-900 dark:text-gray-100">
                    @if($gmcReady)
                        Product feed URL ready
                        @if(filled($gmcMerchantId ?? null))
                            · ID {{ $gmcMerchantId }}
                        @endif
                    @elseif($gmcEnabled ?? false)
                        Enabled — save to generate feed URL
                    @else
                        Feed not enabled
                    @endif
                </p>
            </div>
            <span @class([
                'shrink-0 rounded-full px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide',
                'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-200' => $gmcReady,
                'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-200' => ($gmcEnabled ?? false) && ! $gmcReady,
                'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-300' => ! ($gmcEnabled ?? false),
            ])>
                {{ $gmcReady ? 'Ready' : (($gmcEnabled ?? false) ? 'Pending' : 'Off') }}
            </span>
        </div>
    </div>
</div>
<p class="mt-4 text-xs text-gray-500 dark:text-gray-400">
    Storefront URL for webmaster properties:
    <a href="{{ $storefrontUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary-600 hover:underline">{{ $storefrontUrl }}</a>
    @if(filled($sitemapUrl ?? null))
        · Sitemap:
        <a href="{{ $sitemapUrl }}" target="_blank" rel="noopener noreferrer" class="font-semibold text-primary-600 hover:underline break-all">{{ $sitemapUrl }}</a>
    @endif
</p>
