#!/bin/sh
set -e

# Pastikan folder cache dan session framework ada
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Pastikan hak akses file dan folder storage
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Jika di production, optimasi config & route cache
if [ "$APP_ENV" = "production" ]; then
    echo "Running in production mode, caching configurations..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan PHP-FPM di background
echo "Starting PHP-FPM..."
php-fpm -D

# Jalankan Nginx di foreground (proses utama container)
echo "Starting Nginx..."
exec nginx -g "daemon off;"
