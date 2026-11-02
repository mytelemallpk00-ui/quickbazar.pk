#!/usr/bin/env python3
"""Replace inline JS catalog arrays with Blade partial includes."""

from __future__ import annotations

import re
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]


def replace_products_array(path: Path, include_line: str) -> bool:
    text = path.read_text(encoding="utf-8")
    pattern = r"const products = \[[\s\S]*?\];\n"
    if not re.search(pattern, text):
        return False
    new_text = re.sub(pattern, "", text, count=1)
    new_text = new_text.replace(
        "@push('scripts')\n@verbatim\n<script>",
        "@push('scripts')\n" + include_line + "\n@verbatim\n<script>",
    )
    path.write_text(new_text, encoding="utf-8")
    return True


def replace_product_detail(path: Path) -> bool:
    text = path.read_text(encoding="utf-8")
    pattern = (
        r"const currentProduct = \{[\s\S]*?\};\n\n"
        r"        const relatedProducts = \[[\s\S]*?\];\n"
    )
    include_line = "@include('partials.scripts.product-detail-data')"
    if not re.search(pattern, text):
        return False
    new_text = re.sub(pattern, "", text, count=1)
    new_text = new_text.replace(
        "@push('scripts')\n@verbatim\n<script>",
        "@push('scripts')\n" + include_line + "\n@verbatim\n<script>",
    )
    path.write_text(new_text, encoding="utf-8")
    return True


def replace_articles_array(path: Path) -> bool:
    text = path.read_text(encoding="utf-8")
    pattern = r"const articles = \[[\s\S]*?\];\n"
    include_line = "@include('partials.scripts.blog-articles')"
    if not re.search(pattern, text):
        return False
    new_text = re.sub(pattern, "", text, count=1)
    new_text = new_text.replace(
        "@push('scripts')\n@verbatim\n<script>",
        "@push('scripts')\n" + include_line + "\n@verbatim\n<script>",
    )
    path.write_text(new_text, encoding="utf-8")
    return True


def main() -> None:
    include = "@include('partials.scripts.products-catalog')"
    for name in ("home.blade.php", "collections.blade.php"):
        path = ROOT / "resources/views" / name
        if replace_products_array(path, include):
            print(f"Patched {path.relative_to(ROOT)}")

    detail = ROOT / "resources/views/product-detail.blade.php"
    if replace_product_detail(detail):
        print(f"Patched {detail.relative_to(ROOT)}")

    blog = ROOT / "resources/views/blog/index.blade.php"
    if replace_articles_array(blog):
        print(f"Patched {blog.relative_to(ROOT)}")


if __name__ == "__main__":
    main()
