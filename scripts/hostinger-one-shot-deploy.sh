#!/usr/bin/env bash
#
# Full Hostinger setup when the app was uploaded incorrectly (e.g. resources/ inside public_html).
# Run on the server from domain root:
#   bash scripts/hostinger-one-shot-deploy.sh
#
set -euo pipefail

DOMAIN_ROOT="${DOMAIN_ROOT:-/home/u662960912/domains/beige-ape-286733.hostingersite.com}"
GITHUB_REPO="${GITHUB_REPO:-https://github.com/mytelemallpk00-ui/beautage-laravel.git}"
PHP_BIN="${PHP_BIN:-/usr/bin/php}"
COMPOSER_BIN="${COMPOSER_BIN:-/usr/local/bin/composer}"

cd "$DOMAIN_ROOT"

echo "==> Domain root: $DOMAIN_ROOT"

test -f .env && cp .env ".env.backup.$(date +%Y%m%d_%H%M)"

if [[ ! -f artisan ]]; then
  echo "==> Cloning Laravel repo into domain root (first-time or broken layout)"
  rm -rf tmp_clone
  git clone --depth 1 --branch main "$GITHUB_REPO" tmp_clone
  shopt -s dotglob
  mv tmp_clone/* ./
  rm -rf tmp_clone
elif [[ -d .git ]]; then
  echo "==> Updating from GitHub"
  git pull origin main
else
  echo "ERROR: artisan missing and this folder is not a git repo. Move or rename public_html, then re-run." >&2
  exit 1
fi

if [[ ! -x "$COMPOSER_BIN" ]] && [[ ! -f "$COMPOSER_BIN" ]]; then
  echo "ERROR: Composer not found at $COMPOSER_BIN" >&2
  exit 1
fi

echo "==> Composer install"
"$PHP_BIN" "$COMPOSER_BIN" install --optimize-autoloader --no-dev --no-interaction

if [[ ! -f .env ]]; then
  cp .env.example .env
fi
"$PHP_BIN" artisan key:generate --force

echo "==> Ensure .env uses MySQL (hPanel) and APP_URL before continuing if this is first deploy."
echo "    APP_DEBUG=false, DB_CONNECTION=mysql, DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD"

echo "==> Migrate & seed catalog"
"$PHP_BIN" artisan migrate --force
"$PHP_BIN" artisan db:seed --force

echo "==> public_html = Laravel public/ only"
rm -rf public_html
mkdir -p public_html
cp -r public/* public_html/
ln -sfn "$DOMAIN_ROOT/storage/app/public" "$DOMAIN_ROOT/public_html/storage"

for bad in resources vendor app bootstrap config database routes storage artisan .env .git; do
  rm -rf "public_html/$bad"
done

echo "==> Cache & permissions"
"$PHP_BIN" artisan config:clear
"$PHP_BIN" artisan config:cache
"$PHP_BIN" artisan route:cache
"$PHP_BIN" artisan view:cache
chmod -R 755 storage bootstrap/cache public_html

APP_URL=$(grep -E '^APP_URL=' .env | cut -d= -f2- | tr -d '"' | tr -d "'")
echo "==> DONE. Visit: ${APP_URL:-https://beige-ape-286733.hostingersite.com}"
echo "    File Manager: public_html should contain index.php, css/, js/ — NOT resources/"
