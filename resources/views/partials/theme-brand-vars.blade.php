@php
    $brand = \App\Support\SiteSettings::themeColors();
@endphp
<style id="beautage-brand-vars">
    :root {
        --beautage-purple: {{ $brand['purple'] }};
        --beautage-purple-dark: {{ $brand['purpleDark'] }};
        --beautage-pink: {{ $brand['pink'] }};
        --beautage-pink-hover: {{ $brand['pinkHover'] }};
        --beautage-amber: {{ $brand['amber'] }};
        --beautage-gold: {{ $brand['gold'] ?? $brand['amber'] }};
        --beautage-ink: #1e1b4b;
        --beautage-muted: #475569;
        --beautage-surface: #faf5ff;
        --beautage-border: #e9cce8;
    }
</style>
