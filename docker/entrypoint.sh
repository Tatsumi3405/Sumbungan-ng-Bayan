#!/bin/sh
# Container start: bind to Render's $PORT, prepare APP_KEY, migrate, seed, serve.
set -e

PORT="${PORT:-10000}"
sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf
sed -ri "s/<VirtualHost \*:[0-9]+>/<VirtualHost *:${PORT}>/" /etc/apache2/sites-available/000-default.conf

cd /var/www/html

# Laravel needs a 32-byte key. Render can generate a random string for us
# (APP_KEY_SEED); derive a valid, stable key from it so sessions survive restarts.
if [ -z "${APP_KEY:-}" ]; then
    if [ -z "${APP_KEY_SEED:-}" ]; then
        echo "ERROR: set APP_KEY (php artisan key:generate --show) or APP_KEY_SEED." >&2
        exit 1
    fi
    APP_KEY="$(php -r 'echo "base64:".base64_encode(hash("sha256", getenv("APP_KEY_SEED"), true));')"
    export APP_KEY
fi

# Free Render web services have no shell, so migrations run on every boot.
php artisan config:clear >/dev/null 2>&1 || true
php artisan migrate --force
php artisan db:seed --force

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec apache2-foreground
