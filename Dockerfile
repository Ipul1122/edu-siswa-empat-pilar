# ==========================================
# Tahap 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS frontend

WORKDIR /app

# Copy package files dan install dependencies frontend
COPY package*.json ./
RUN npm ci || npm install

# Copy source code dan jalankan build Vite
COPY . .
RUN npm run build

# ==========================================
# Tahap 2: Runtime PHP 8.4 & Web Server Nginx
# ==========================================
FROM php:8.4-fpm-alpine

# Install Nginx dan curl untuk runtime web server
RUN apk add --no-cache nginx curl

# Gunakan PHP Extension Installer resmi (Solusi terbaik & stabil di Alpine)
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
RUN install-php-extensions pdo_mysql bcmath mbstring opcache gd zip pcntl redis

# Ambil Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh kode aplikasi
COPY . .

# Salin hasil build Vite dari tahap 1
COPY --from=frontend /app/public/build ./public/build

# Install dependensi PHP (production mode, tanpa dev package & tanpa artisan script)
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# Setup direktori penyimpanan dan permission
RUN mkdir -p storage/framework/cache/data \
             storage/framework/sessions \
             storage/framework/views \
             storage/logs \
             bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Konfigurasi Nginx & Entrypoint
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
COPY docker/entrypoint.sh /entrypoint.sh

# Pastikan line-endings UNIX (LF) dan permission execute untuk entrypoint
RUN sed -i 's/\r$//' /entrypoint.sh && chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
