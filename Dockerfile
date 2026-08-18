# syntax=docker/dockerfile:1

# ============================================================
# Stage 1 — Build aset frontend (Vite + Bootstrap)
# ============================================================
FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json vite.config.js ./
RUN npm ci
COPY resources ./resources
RUN npm run build

# ============================================================
# Stage 2 — Runtime: PHP 8.4 + Apache + Python 3 (engine AHP)
# ============================================================
FROM php:8.4-apache AS app

# Ekstensi PHP + tools sistem. python3 dipakai engine AHP (dipanggil via Process).
RUN apt-get update && apt-get install -y --no-install-recommends \
        python3 \
        git unzip \
        libonig-dev libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring zip bcmath \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Virtual host Apache -> public/
COPY docker/vhost.conf /etc/apache2/sites-available/000-default.conf

WORKDIR /var/www/html

# Dependensi PHP dulu (memaksimalkan cache layer)
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --no-progress

# Kode aplikasi (vendor & aset dikecualikan lewat .dockerignore)
COPY . .

# Aset hasil build dari stage 1
COPY --from=assets /app/public/build ./public/build

# Autoloader optimal + izin tulis storage
RUN composer dump-autoload --optimize --no-dev \
    && chown -R www-data:www-data storage bootstrap/cache

# Entrypoint: tunggu DB, migrate, seed (first run), cache, lalu jalankan Apache
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80
ENTRYPOINT ["entrypoint.sh"]
CMD ["apache2-foreground"]
