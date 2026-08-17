#!/usr/bin/env bash
set -e

mkdir -p /var/data
: > /var/data/database.sqlite

for i in 1 2 3 4 5; do
    composer install --no-dev --no-interaction --prefer-dist --no-progress --optimize-autoloader && break
    if [ "$i" -lt 5 ]; then
        echo "Composer install failed (attempt $i/5), retrying..."
        sleep 5
    else
        exit 1
    fi
done

npm ci
npm run build

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
