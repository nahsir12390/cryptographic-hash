#!/usr/bin/env sh
set -eu

mkdir -p /var/data
touch /var/data/database.sqlite

if [ ! -f .env ]; then
    cp .env.example .env
fi

if [ -z "${APP_KEY:-}" ]; then
    php artisan key:generate --force --no-interaction
fi

php artisan config:clear
php artisan route:clear
php artisan config:cache
php artisan route:cache
php artisan migrate --force

exec php artisan serve --host 0.0.0.0 --port "${PORT:-8000}"