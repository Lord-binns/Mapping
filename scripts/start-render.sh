#!/usr/bin/env sh
set -eu

# Ensure Laravel cache and storage directories exist at runtime.
mkdir -p storage/framework/cache storage/framework/sessions storage/framework/testing storage/framework/views storage/logs bootstrap/cache

# Clear stale caches so runtime env values (APP_URL, ASSET_URL, etc.) are always respected.
php artisan optimize:clear

php artisan migrate --force

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
