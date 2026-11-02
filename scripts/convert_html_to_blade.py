#!/usr/bin/env python3
"""Convert Beautage static HTML uploads into Laravel Blade views."""

from __future__ import annotations

import re
import shutil
from pathlib import Path

UPLOADS = Path("/home/ubuntu/.cursor/projects/workspace/uploads")
VIEWS = Path("/workspace/resources/views")
PUBLIC = Path("/workspace/public")
LAYOUT = VIEWS / "layouts" / "app.blade.php"
PARTIALS = VIEWS / "partials"

# Source HTML filename -> view path (without .blade.php)
PAGE_MAP: dict[str, str] = {
    "index_3799.html": "home",
    "collections_category_page_4189.html": "collections",
    "product_detail_9bea.html": "product-detail",
    "checkout_page_8ab2.html": "checkout",
    "blog_461c.html": "blog/index",
    "blog_detail_d357.html": "blog/show",
    "beautage_our_story_b2d4.html": "pages/our-story",
    "beautage_careers_opportunities_e38c.html": "pages/careers",
    "beautage_press_media_c7c3.html": "pages/press",
    "beautage_help_center_faq_5db3.html": "pages/help",
    "returns_exchanges_60ed.html": "pages/returns",
    "shipping_delivery_3cf6.html": "pages/shipping",
    "size_guide_40b8.html": "pages/size-guide",
    "track_your_order_page_d88c.html": "pages/track-order",
}

LAYOUT_SOURCE = "beautage_help_center_faq_5db3.html"
SHARED_AFTER_FOOTER_SOURCE = "index_3799.html"


def read(name: str) -> str:
    return (UPLOADS / name).read_text(encoding="utf-8")


def extract_title(html: str) -> str:
    m = re.search(r"<title>(.*?)</title>", html, re.DOTALL | re.IGNORECASE)
    return m.group(1).strip() if m else "Beautage"


def extract_body_open(html: str) -> str:
    m = re.search(r"<body([^>]*)>", html, re.IGNORECASE)
    return m.group(1).strip() if m else ""


def extract_main(html: str) -> str:
    m = re.search(r"(<main[\s\S]*?</main>)", html, re.IGNORECASE)
    if not m:
        raise ValueError("No <main> block found")
    return m.group(1).strip()


def extract_below_main(html: str) -> str:
    m_main = re.search(r"</main>", html, re.IGNORECASE)
    if not m_main:
        return ""
    rest = html[m_main.end() :]
    footer = re.search(r"<!--\s*FOOTER|<footer", rest, re.IGNORECASE)
    if not footer:
        return ""
    chunk = rest[: footer.start()].strip()
    return chunk


def extract_page_scripts(html: str) -> str:
    """Scripts after </footer> that belong to the page (not shared layout)."""
    m_footer = re.search(r"</footer>", html, re.IGNORECASE)
    if not m_footer:
        return ""
    rest = html[m_footer.end() :]
    m_body = re.search(r"</body>", rest, re.IGNORECASE)
    if not m_body:
        return ""
    chunk = rest[: m_body.start()].strip()
    # Remove shared drawers/modals if duplicated (keep only script tags for page)
    shared_markers = [
        "<!-- CART DRAWER -->",
        "<!-- WISHLIST DRAWER -->",
        "<!-- QUICK VIEW MODAL -->",
    ]
    for marker in shared_markers:
        if marker in chunk:
            idx = chunk.index(marker)
            chunk = chunk[idx:]
            break
    # If chunk is only shared UI, page-specific scripts may be before markers
    # Re-scan full tail for script blocks unique to page
    scripts = re.findall(r"<script[\s\S]*?</script>", rest[: m_body.start()], re.IGNORECASE)
    if not scripts:
        return ""
    return "\n".join(scripts).strip()


def extract_header(html: str) -> str:
    m_body = re.search(r"<body[^>]*>", html, re.IGNORECASE)
    m_main = re.search(r"<main", html, re.IGNORECASE)
    if not m_body or not m_main:
        raise ValueError("Cannot locate header region")
    return html[m_body.end() : m_main.start()].strip()


def extract_footer(html: str) -> str:
    m = re.search(r"(<!--\s*FOOTER[\s\S]*?</footer>)", html, re.IGNORECASE)
    if not m:
        m = re.search(r"(<footer[\s\S]*?</footer>)", html, re.IGNORECASE)
    if not m:
        raise ValueError("Cannot locate footer")
    return m.group(1).strip()


def extract_shared_after_footer(html: str) -> str:
    m_footer = re.search(r"</footer>", html, re.IGNORECASE)
    m_body = re.search(r"</body>", html, re.IGNORECASE)
    if not m_footer or not m_body:
        return ""
    chunk = html[m_footer.end() : m_body.start()].strip()
    # Stop before page-specific script if we only want HTML partials
    script_start = chunk.find("<!-- JAVASCRIPT")
    if script_start == -1:
        script_start = chunk.find("<script")
    if script_start != -1:
        chunk = chunk[:script_start].strip()
    return chunk


def extract_head_assets(html: str) -> tuple[str, str]:
    """Return (inline_css, tailwind_config_js)."""
    css_m = re.search(r"<style>([\s\S]*?)</style>", html, re.IGNORECASE)
    css = css_m.group(1).strip() if css_m else ""
    cfg_m = re.search(
        r"<script>\s*(tailwind\.config[\s\S]*?)\s*</script>",
        html,
        re.IGNORECASE,
    )
    cfg = cfg_m.group(1).strip() if cfg_m else ""
    return css, cfg


def apply_blade_transforms(text: str) -> str:
    rules: list[tuple[str, str]] = [
        (r'src="beautagelogosquare2\.png"', 'src="{{ asset(\'images/beautagelogosquare2.png\') }}"'),
        (r'href="index\.html#([^"]+)"', 'href="{{ route(\'home\') }}#\1"'),
        (r'href="index\.html"', 'href="{{ route(\'home\') }}"'),
        (r'href="beautage\.pk\.html#([^"]+)"', 'href="{{ route(\'home\') }}#\1"'),
        (r'href="beautage\.pk\.html"', 'href="{{ route(\'home\') }}"'),
        (r'href="collections\.html\?cat=([^"]+)"', 'href="{{ route(\'collections\', [\'cat\' => \'\1\']) }}"'),
        (r'href="collections\.html"', 'href="{{ route(\'collections\') }}"'),
        (r'href="collections_category_page\.html\?cat=([^"]+)"', 'href="{{ route(\'collections\', [\'cat\' => \'\1\']) }}"'),
        (r'href="collections_category_page\.html"', 'href="{{ route(\'collections\') }}"'),
        (r'href="detail\.html"', 'href="{{ route(\'product.show\', [\'product\' => \'lumina-pro-led-mask\']) }}"'),
        (r'href="product_detail\.html"', 'href="{{ route(\'product.show\', [\'product\' => \'lumina-pro-led-mask\']) }}"'),
        (r'href="checkout_page\.html"', 'href="{{ route(\'checkout\') }}"'),
        (r'href="checkout\.html"', 'href="{{ route(\'checkout\') }}"'),
        (r'href="blog\.html"', 'href="{{ route(\'blog.index\') }}"'),
        (r'href="blog_detail\.html"', 'href="{{ route(\'blog.show\') }}"'),
        (r'href="help_center\.html"', 'href="{{ route(\'help\') }}"'),
        (r'href="beautage_help_center_faq\.html"', 'href="{{ route(\'help\') }}"'),
        (r'href="beautage_our_story\.html"', 'href="{{ route(\'our-story\') }}"'),
        (r'href="beautage_careers_opportunities\.html"', 'href="{{ route(\'careers\') }}"'),
        (r'href="beautage_press_media\.html"', 'href="{{ route(\'press\') }}"'),
        (r'href="returns_exchanges\.html"', 'href="{{ route(\'returns\') }}"'),
        (r'href="shipping_delivery\.html"', 'href="{{ route(\'shipping\') }}"'),
        (r'href="size_guide\.html"', 'href="{{ route(\'size-guide\') }}"'),
        (r'href="track_your_order_page\.html"', 'href="{{ route(\'track-order\') }}"'),
        (r'href="our_story\.html"', 'href="{{ route(\'our-story\') }}"'),
        (r'href="careers\.html"', 'href="{{ route(\'careers\') }}"'),
        (r'href="press\.html"', 'href="{{ route(\'press\') }}"'),
        (r"window\.location\.href='index\.html'", "window.location.href='/'"),
        (r'window\.location\.href="index\.html"', 'window.location.href="/"'),
        (r"window\.location\.href='checkout\.html'", "window.location.href='/checkout'"),
        (r'<a href="index\.html"', '<a href="/"'),
        (r"href='index\.html'", "href='/'"),
        (r'href="detail\.html"', 'href="/products/lumina-pro-led-mask"'),
    ]
    out = text
    for pattern, repl in rules:
        out = re.sub(pattern, repl, out, flags=re.IGNORECASE)
    # Logo home links
    out = re.sub(
        r'(<a href=")#(" class="flex items-center justify-center focus:outline-none)',
        r"\1{{ route('home') }}\2",
        out,
    )
    out = re.sub(
        r'(<a href=")#(" class="focus:outline-none py-1")',
        r"\1{{ route('home') }}\2",
        out,
    )
    out = re.sub(
        r'(<a href=")#(" class="inline-flex items-center gap-2")',
        r"\1{{ route('home') }}\2",
        out,
    )
    # Footer customer-care links (help center source uses explicit .html names)
    footer_link_fixes = [
        ("help_center.html", "help"),
        ("track_order.html", "track-order"),
        ("returns_exchanges.html", "returns"),
        ("shipping_delivery.html", "shipping"),
        ("size_guide.html", "size-guide"),
        ("our_story.html", "our-story"),
        ("careers.html", "careers"),
        ("press_media.html", "press"),
    ]
    for html_name, route_name in footer_link_fixes:
        out = out.replace(
            f'href="{html_name}"',
            f"href=\"{{{{ route('{route_name}') }}}}\"",
        )
    # Normalize Blade quote escaping from Python string literals
    out = out.replace("\\'", "'")
    out = out.replace('\\"', '"')
    return out


def apply_static_path_transforms(text: str) -> str:
    """Use app URL paths inside @verbatim JavaScript blocks."""
    replacements = [
        ('href="index.html"', 'href="/"'),
        ("href='index.html'", "href='/'"),
        ('href="detail.html"', 'href="/products/lumina-pro-led-mask"'),
        ('href="checkout.html"', 'href="/checkout"'),
        ("window.location.href='index.html'", "window.location.href='/'"),
        ("window.location.href='checkout.html'", "window.location.href='/checkout'"),
    ]
    out = text
    for old, new in replacements:
        out = out.replace(old, new)
    return out


def patch_layout_footer(layout_path: Path) -> None:
    text = layout_path.read_text(encoding="utf-8")
    patches = [
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="package" class="w-3.5 h-3.5 text-amber-300"></i> Track Your Order</a></li>',
            '<li><a href="{{ route(\'track-order\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="package" class="w-3.5 h-3.5 text-amber-300"></i> Track Your Order</a></li>',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-purple-300"></i> Returns & Exchanges</a></li>',
            '<li><a href="{{ route(\'returns\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-purple-300"></i> Returns & Exchanges</a></li>',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="truck" class="w-3.5 h-3.5 text-emerald-400"></i> Shipping & Delivery</a></li>',
            '<li><a href="{{ route(\'shipping\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="truck" class="w-3.5 h-3.5 text-emerald-400"></i> Shipping & Delivery</a></li>',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="ruler" class="w-3.5 h-3.5 text-rose-400"></i> Size Guide</a></li>',
            '<li><a href="{{ route(\'size-guide\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="ruler" class="w-3.5 h-3.5 text-rose-400"></i> Size Guide</a></li>',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="book-open"',
            '<li><a href="{{ route(\'our-story\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="book-open"',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="briefcase"',
            '<li><a href="{{ route(\'careers\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="briefcase"',
        ),
        (
            '<li><a href="#" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="newspaper"',
            '<li><a href="{{ route(\'press\') }}" class="hover:text-white hover:underline transition flex items-center gap-2"><i data-lucide="newspaper"',
        ),
        (
            '<button onclick="showToast(\'Checkout initiated successfully for Beautage PVT Ltd!\')" class="w-full bg-beautage-pink hover:bg-beautage-pinkHover text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">',
            '<a href="{{ route(\'checkout\') }}" class="w-full bg-beautage-pink hover:bg-beautage-pinkHover text-white font-semibold py-3 rounded-xl transition shadow-lg shadow-pink-500/30 flex items-center justify-center gap-2 text-xs uppercase tracking-wider">',
        ),
        (
            "Proceed to Checkout\n                </button>",
            "Proceed to Checkout\n                </a>",
        ),
    ]
    for old, new in patches:
        text = text.replace(old, new)
    text = text.replace("\\'", "'")
    layout_path.write_text(text, encoding="utf-8")


def blade_escape_at_signs_in_scripts(scripts: str) -> str:
    """Wrap scripts in @verbatim to avoid Blade parsing template literals."""
    if not scripts.strip():
        return ""
    return "@verbatim\n" + scripts + "\n@endverbatim"


def build_layout(
    header: str,
    footer: str,
    shared_after_footer: str,
    css: str,
    tailwind_cfg: str,
) -> str:
    header = apply_blade_transforms(header)
    footer = apply_blade_transforms(footer)
    shared_after_footer = apply_blade_transforms(shared_after_footer)

    return (
        """<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>@yield('title', 'Beautage — Elevate Your Natural Elegance')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="{{ asset('js/tailwind-config.js') }}"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Playfair+Display:ital,wght@0,600;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/beautage.css') }}">
    @stack('styles')
</head>
<body @yield('body_attrs')>
"""
        + header
        + """

@yield('content')

@yield('below_main')

"""
        + footer
        + """

"""
        + shared_after_footer
        + """

<script>
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@stack('scripts')
</body>
</html>
"""
    )


def build_page_view(
    title: str,
    body_attrs: str,
    main: str,
    below_main: str,
    scripts: str,
) -> str:
    main = apply_blade_transforms(main)
    below_main = apply_blade_transforms(below_main)
    scripts = apply_static_path_transforms(scripts)
    scripts_block = ""
    if scripts.strip():
        scripts_block = (
            "@push('scripts')\n"
            + blade_escape_at_signs_in_scripts(scripts)
            + "\n@endpush\n"
        )

    body_line = body_attrs.strip()
    if not body_line:
        body_line = 'class="bg-slate-50 text-slate-800 font-sans antialiased min-h-screen flex flex-col"'

    return f"""@extends('layouts.app')

@section('title', {title!r})

@section('body_attrs')
{body_line}
@endsection

@section('content')
{main}
@endsection

@section('below_main')
{below_main}
@endsection

{scripts_block}"""


def main() -> None:
    layout_html = read(LAYOUT_SOURCE)
    shared_html = read(SHARED_AFTER_FOOTER_SOURCE)
    index_html = read("index_3799.html")

    css, tailwind_cfg = extract_head_assets(index_html)
    (PUBLIC / "css").mkdir(parents=True, exist_ok=True)
    (PUBLIC / "js").mkdir(parents=True, exist_ok=True)
    (PUBLIC / "images").mkdir(parents=True, exist_ok=True)

    (PUBLIC / "css" / "beautage.css").write_text(css + "\n", encoding="utf-8")
    (PUBLIC / "js" / "tailwind-config.js").write_text(
        tailwind_cfg + "\n", encoding="utf-8"
    )

    logo_src = UPLOADS / "beautagelogosquare2.png"
    logo_dst = PUBLIC / "images" / "beautagelogosquare2.png"
    if logo_src.exists():
        shutil.copy(logo_src, logo_dst)
    elif not logo_dst.exists():
        # Minimal 1x1 PNG placeholder; replace with brand asset when available
        logo_dst.write_bytes(
            bytes.fromhex(
                "89504e470d0a1a0a0000000d49484452000000010000000108060000001f15c489"
                "0000000a49444154789c630001000005000108d4a7a4b00000000049454e44ae426082"
            )
        )

    header = extract_header(layout_html)
    footer = extract_footer(layout_html)
    shared_after = extract_shared_after_footer(shared_html)

    LAYOUT.parent.mkdir(parents=True, exist_ok=True)
    LAYOUT.write_text(
        build_layout(header, footer, shared_after, css, tailwind_cfg),
        encoding="utf-8",
    )
    patch_layout_footer(LAYOUT)

    for filename, view_name in PAGE_MAP.items():
        html = read(filename)
        title = extract_title(html)
        body_open = extract_body_open(html)
        main = extract_main(html)
        below = extract_below_main(html)
        scripts = extract_page_scripts(html)

        view_path = VIEWS / f"{view_name}.blade.php"
        view_path.parent.mkdir(parents=True, exist_ok=True)
        view_path.write_text(
            build_page_view(title, body_open, main, below, scripts),
            encoding="utf-8",
        )
        print(f"Wrote {view_path.relative_to(VIEWS)}")


if __name__ == "__main__":
    main()
