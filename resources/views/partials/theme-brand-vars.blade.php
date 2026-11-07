@php
    $brand = \App\Support\SiteSettings::themeColors();
@endphp
<style id="beautage-brand-vars">
    :root {
        --qb-navy: {{ $brand['purple'] }};
        --qb-navy-deep: {{ $brand['purpleDark'] }};
        --qb-orange: {{ $brand['pink'] }};
        --qb-orange-hover: {{ $brand['pinkHover'] }};
        --qb-highlight: {{ $brand['amber'] }};
        --qb-soft: color-mix(in srgb, var(--qb-orange) 12%, white);
        --qb-line: color-mix(in srgb, var(--qb-orange) 28%, white);
        --beautage-purple: var(--qb-navy);
        --beautage-purple-dark: var(--qb-navy-deep);
        --beautage-pink: var(--qb-orange);
        --beautage-pink-hover: var(--qb-orange-hover);
        --beautage-amber: var(--qb-highlight);
        --beautage-gold: var(--qb-highlight);
        --beautage-ink: var(--qb-navy);
        --beautage-muted: #475569;
        --beautage-surface: var(--qb-soft);
        --beautage-border: var(--qb-line);
    }
</style>
