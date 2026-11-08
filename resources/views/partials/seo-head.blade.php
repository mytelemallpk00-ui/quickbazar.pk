@php
    use App\Support\SiteSettings;
    use Illuminate\Support\Str;

    $seo = is_array($seo ?? null) ? $seo : [];

    $yieldedTitle = trim($__env->yieldContent('title'));
    $title = filled($seo['title'] ?? null)
        ? (string) $seo['title']
        : ($yieldedTitle !== '' ? $yieldedTitle : SiteSettings::get('default_meta_title', 'Beautage — Elevate Your Natural Elegance'));

    $yieldedDescription = trim($__env->yieldContent('meta_description'));
    $description = filled($seo['description'] ?? null)
        ? (string) $seo['description']
        : ($yieldedDescription !== '' ? $yieldedDescription : SiteSettings::get('default_meta_description', ''));

    if (blank($description)) {
        $description = 'Shop original products on Shop Pakistan with PKR prices, cash on delivery, nationwide shipping, and verified customer reviews.';
    }

    $description = Str::limit(strip_tags($description), 160, '');

    $robots = filled($seo['robots'] ?? null)
        ? (string) $seo['robots']
        : SiteSettings::get('default_robots', 'index,follow');

    $canonical = filled($seo['canonical'] ?? null)
        ? (string) $seo['canonical']
        : url()->current();

    $ogTitle = filled($seo['og_title'] ?? null) ? (string) $seo['og_title'] : $title;
    $ogDescription = filled($seo['og_description'] ?? null) ? (string) $seo['og_description'] : $description;
    $ogImage = $seo['og_image'] ?? SiteSettings::get('default_og_image');
    if (is_array($ogImage)) {
        $ogImage = $ogImage[0] ?? null;
    }
    if (filled($ogImage) && ! str_starts_with((string) $ogImage, 'http')) {
        $ogImage = asset(ltrim((string) $ogImage, '/'));
    }
@endphp
<meta name="description" content="{{ e($description) }}">
<meta name="robots" content="{{ e($robots) }}">
<link rel="canonical" href="{{ e($canonical) }}">
<meta property="og:type" content="{{ e($seo['og_type'] ?? 'website') }}">
<meta property="og:site_name" content="{{ e(SiteSettings::get('site_name', 'Shop Pakistan')) }}">
<meta property="og:title" content="{{ e($ogTitle) }}">
<meta property="og:description" content="{{ e($ogDescription) }}">
<meta property="og:url" content="{{ e($canonical) }}">
@if (filled($ogImage))
<meta property="og:image" content="{{ e($ogImage) }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ e($ogTitle) }}">
<meta name="twitter:description" content="{{ e($ogDescription) }}">
@if (filled($ogImage))
<meta name="twitter:image" content="{{ e($ogImage) }}">
@endif
