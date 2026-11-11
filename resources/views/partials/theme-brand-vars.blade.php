@php
    $brand = \App\Support\SiteSettings::themeColors();
@endphp
<style id="beautage-brand-vars">
    :root {
        --qb-blue: {{ $brand['purple'] }};
        --qb-blue-deep: {{ $brand['purpleDark'] }};
        --qb-red: {{ $brand['pink'] }};
        --qb-red-hover: {{ $brand['pinkHover'] }};
        --qb-green: {{ $brand['amber'] }};
        --qb-navy: var(--qb-blue);
        --qb-navy-deep: var(--qb-blue-deep);
        --qb-orange: var(--qb-red);
        --qb-orange-hover: var(--qb-red-hover);
        --qb-highlight: var(--qb-green);
        --qb-chrome-text: color-mix(in srgb, white 86%, var(--qb-blue));
        --qb-chrome-muted: color-mix(in srgb, white 74%, var(--qb-blue));
        --qb-soft: color-mix(in srgb, var(--qb-blue) 8%, white);
        --qb-line: color-mix(in srgb, var(--qb-blue) 22%, white);
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
