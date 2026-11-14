#!/bin/sh
# Khoi dong container PHP. Chi service `app` (RUN_MIGRATIONS=true) chay migrate --seed;
# queue/scheduler cho `app` healthy (xem docker-compose.yml) nen luon thay schema moi nhat.
set -e

cd /var/www/html

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist
fi

if [ -z "${APP_KEY}" ] && ! grep -q '^APP_KEY=base64:' .env 2>/dev/null; then
    php artisan key:generate --force
fi

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    rm -f storage/framework/.ready
    php artisan migrate --force --seed
    php artisan storage:link 2>/dev/null || true
    touch storage/framework/.ready
fi

exec "$@"
