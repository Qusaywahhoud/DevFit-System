# ---------- Stage 1: build frontend assets ----------
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY . .
RUN npm run build

# ---------- Stage 2: PHP app ----------
FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev \
    && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr \
    && docker-php-ext-install pdo_pgsql pcntl \
    && curl -sS https://getcomposer.org/installer \
        | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN rm -rf bootstrap/cache/*.php storage/framework/views/*.php

RUN composer install --no-dev --optimize-autoloader

# إنشاء مجلدات الجلسات والتخزين ومنحها الصلاحيات الكاملة
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

RUN if [ -f start.sh ]; then sed -i 's/\r$//' start.sh && chmod +x start.sh; fi

ENV APP_NAME=DevFit \
    APP_ENV=production \
    APP_DEBUG=true \
    DB_CONNECTION=pgsql

EXPOSE 8000

RUN printf '#!/bin/sh\ncd /app\nphp artisan optimize:clear\nphp artisan storage:link\nphp artisan migrate --force\nexec php artisan serve --host=0.0.0.0 --port=8000\n' > /usr/local/bin/devfit-start.sh && chmod +x /usr/local/bin/devfit-start.sh

ENTRYPOINT ["/usr/local/bin/devfit-start.sh"]
