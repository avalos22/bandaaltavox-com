#!/bin/sh
set -e

cd /var/www/html

echo "→ Optimizing Laravel for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

echo "→ Running migrations..."
php artisan migrate --force

echo "→ Linking storage..."
php artisan storage:link --force 2>/dev/null || true

echo "→ Starting services..."
exec "$@"
