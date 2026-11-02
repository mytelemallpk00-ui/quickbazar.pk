@php
    use App\Support\GoogleAnalytics;
    use App\Support\GoogleMerchant;
    use App\Support\MetaPixel;

    $gaId = GoogleAnalytics::measurementId();
    $metaPixelId = MetaPixel::pixelId();
    $metaEnabled = MetaPixel::isEnabled();
    $beautageAnalyticsConfig = [
        'gaId' => $gaId,
        'metaPixelId' => $metaEnabled ? $metaPixelId : null,
        'currency' => GoogleMerchant::currencyCode(),
    ];
@endphp
<script>
    window.BEAUTAGE_ANALYTICS = @json($beautageAnalyticsConfig);
</script>
<script src="{{ asset('js/beautage-analytics.js') }}?v=2026102921" defer></script>
