# Beautage Laravel

Laravel storefront for the Beautage static HTML theme. All theme pages are Blade views with a shared layout, public assets, and named GET routes.

## Setup

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan serve
```

Catalog products and journal articles are seeded into the database and exposed to the theme JavaScript as JSON from controllers (home, collections, product detail, blog).

Visit `http://127.0.0.1:8000`.

Replace `public/images/beautagelogosquare2.png` with your brand logo (the HTML theme referenced this file locally; a tiny placeholder is committed until you add the real asset).

## Theme conversion

Static HTML sources can be re-imported with:

```bash
python3 scripts/convert_html_to_blade.py
```

Place source HTML files in the uploads directory configured in that script.

## Hostinger deploy

On the server, from the project root (your domain folder):

```bash
bash scripts/deploy-hostinger.sh
```

Skip migrations if this release has none:

```bash
bash scripts/deploy-hostinger.sh --no-migrate
```

Override binaries if needed: `PHP_BIN=/usr/bin/php COMPOSER_BIN=/usr/local/bin/composer bash scripts/deploy-hostinger.sh`

The script pulls `main`, runs Composer, copies `public/*` into `public_html/`, links `public_html/storage`, caches config/routes/views, and sets permissions. Do not edit `public_html/index.php` paths; Laravel’s default `../vendor` paths are correct.

## Routes

| URL | View |
|-----|------|
| `/` | `home` |
| `/collections` | `collections` |
| `/products/{slug}` | `product.show` (e.g. `/products/lumina-pro-led-mask`) |
| `/checkout` | `checkout` |
| `/blog` | `blog.index` |
| `/blog/{slug}` | `blog.show` (e.g. `/blog/7-spectrum-led-therapy-routine`) |
| `/our-story` | `pages.our-story` |
| `/careers` | `pages.careers` |
| `/press` | `pages.press` |
| `/help` | `pages.help` |
| `/returns` | `pages.returns` |
| `/shipping` | `pages.shipping` |
| `/size-guide` | `pages.size-guide` |
| `/track-order` | `pages.track-order` |

## Structure

- `resources/views/layouts/app.blade.php` — shared header, footer, cart/wishlist drawers, and global scripts
- `resources/views/*.blade.php` — page content extending the layout
- `public/css/beautage.css` — theme custom styles
- `public/js/tailwind-config.js` — Tailwind theme extension (CDN Tailwind loads this file)
- `routes/web.php` — GET route definitions
