#!/bin/sh
set -e

cd /var/www/html

if [ -f .env ] && [ "${SKIP_ENV_FILE:-false}" != "true" ]; then
    set -a
    . ./.env
    set +a
fi

if [ -z "${APP_KEY:-}" ] && [ "${GENERATE_APP_KEY:-false}" = "true" ]; then
    php artisan key:generate --force
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    echo "Running migrations..."
    php artisan migrate --force
fi

if [ "${RUN_SEEDERS:-false}" = "true" ]; then
    echo "Running seeders..."
    php artisan db:seed --force
fi

if [ "${OPTIMIZE:-false}" = "true" ]; then
    php artisan optimize
fi

exec "$@"
