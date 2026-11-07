#!/usr/bin/env bash
#
# Hostinger production deploy for Beautage Laravel.
# Run from the project root (domain folder), e.g.:
#   bash scripts/deploy-hostinger.sh
#   bash scripts/deploy-hostinger.sh --no-migrate
#   bash scripts/deploy-hostinger.sh --seed
#
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_ROOT="$(cd "${SCRIPT_DIR}/.." && pwd)"
cd "${APP_ROOT}"

PHP_BIN="${PHP_BIN:-/usr/bin/php}"
COMPOSER_BIN="${COMPOSER_BIN:-/usr/local/bin/composer}"
GIT_REMOTE="${GIT_REMOTE:-origin}"
GIT_BRANCH="${GIT_BRANCH:-main}"
export COMPOSER_NO_INTERACTION=1

RUN_MIGRATE=1
RUN_SEED=0
for arg in "$@"; do
    case "${arg}" in
        --no-migrate) RUN_MIGRATE=0 ;;
        --no-seed) RUN_SEED=0 ;;
        --seed) RUN_SEED=1 ;;
        -h | --help)
            echo "Usage: bash scripts/deploy-hostinger.sh [--no-migrate] [--seed]"
            echo "Environment: PHP_BIN, COMPOSER_BIN, GIT_REMOTE, GIT_BRANCH"
            echo "Seeders are off by default so live products, orders, and admin passwords stay as they are."
            echo "Pass --seed only for an empty database. --no-seed is accepted and changes nothing."
            exit 0
            ;;
        *)
            echo "Unknown option: ${arg}" >&2
            exit 1
            ;;
    esac
done

echo "==> Deploying from ${APP_ROOT}"

if [[ ! -f artisan ]]; then
    echo "Error: artisan not found. Run this script from the Laravel project root." >&2
    exit 1
fi

echo "==> Git pull ${GIT_REMOTE} ${GIT_BRANCH}"
git pull "${GIT_REMOTE}" "${GIT_BRANCH}"

echo "==> Composer install (production)"
"${PHP_BIN}" "${COMPOSER_BIN}" install --optimize-autoloader --no-dev --no-interaction

echo "==> Sync public/ into public_html/ (including .htaccess)"
rm -rf public_html
mkdir -p public_html
cp -a public/. public_html/
if [[ ! -f public_html/.htaccess ]]; then
    echo "Error: public_html/.htaccess is missing. Apache would 404 every route except /index.php." >&2
    exit 1
fi

echo "==> Storage symlink for public_html"
ln -sfn "${APP_ROOT}/storage/app/public" "${APP_ROOT}/public_html/storage"

if [[ "${RUN_MIGRATE}" -eq 1 ]]; then
    echo "==> Database migrations"
    "${PHP_BIN}" artisan migrate --force --no-interaction
fi

if [[ "${RUN_SEED}" -eq 1 ]]; then
    echo "==> Database seeders"
    "${PHP_BIN}" artisan db:seed --force --no-interaction
fi

echo "==> Laravel caches"
"${PHP_BIN}" artisan config:cache
"${PHP_BIN}" artisan route:cache
"${PHP_BIN}" artisan view:cache

echo "==> Permissions"
chmod -R 755 storage bootstrap/cache public_html

echo "==> Deploy finished successfully"
