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
# Tahap 2: Runtime PHP 8.2 & Web Server Nginx
# ==========================================
FROM php:8.2-fpm-alpine

# Install dependencies sistem, ekstensi PHP & Nginx
RUN apk add --no-cache \
    nginx \
    curl \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    oniguruma-dev \
    linux-headers \
    $PHPIZE_DEPS \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_mysql bcmath mbstring opcache gd zip pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

# Ambil Composer resmi
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Salin seluruh kode aplikasi
COPY . .

# Salin hasil build Vite dari tahap 1
COPY --from=frontend /app/public/build ./public/build

# Install dependensi PHP (production mode tanpa dev package)
RUN composer install --no-dev --optimize-autoloader --no-interaction

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
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
