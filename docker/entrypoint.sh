#!/bin/sh
set -e

cd /var/www/html

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --force
fi

if [ ! -f vendor/autoload.php ]; then
    composer install --no-interaction --prefer-dist --optimize-autoloader
else
    composer install --no-interaction --prefer-dist --optimize-autoloader --no-scripts 2>/dev/null || true
fi

php artisan config:clear
php artisan migrate --force --no-interaction

exec php artisan serve --host=0.0.0.0 --port=8000
