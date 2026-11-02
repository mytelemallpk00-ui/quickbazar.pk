@php
    use App\Support\GoogleAnalytics;
    use App\Support\WebmasterTools;

    $gscToken = GoogleAnalytics::siteVerificationToken();
    $bingToken = WebmasterTools::bingVerificationToken();
    $yandexToken = WebmasterTools::yandexVerificationToken();
    $pinterestToken = WebmasterTools::pinterestVerificationToken();
@endphp
@if (filled($gscToken))
    <meta name="google-site-verification" content="{{ $gscToken }}">
@endif
@if (filled($bingToken))
    <meta name="msvalidate.01" content="{{ $bingToken }}">
@endif
@if (filled($yandexToken))
    <meta name="yandex-verification" content="{{ $yandexToken }}">
@endif
@if (filled($pinterestToken))
    <meta name="p:domain_verify" content="{{ $pinterestToken }}">
@endif
