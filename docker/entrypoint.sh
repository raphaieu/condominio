#!/bin/sh
set -e

if [ -z "$THREADS_REDIRECT_URI" ] && [ -n "$APP_URL" ]; then
    export THREADS_REDIRECT_URI="${APP_URL}/auth/threads/callback"
fi

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

php /var/www/html/artisan storage:link --force 2>/dev/null || true

exec "$@"
