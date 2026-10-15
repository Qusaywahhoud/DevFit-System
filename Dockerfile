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

RUN chown -R www-data:www-data storage bootstrap/cache

RUN if [ -f start.sh ]; then sed -i 's/\r$//' start.sh && chmod +x start.sh; fi

ENV APP_NAME=DevFit \
    APP_ENV=production \
    APP_DEBUG=true \
    DB_CONNECTION=pgsql

EXPOSE 8000

CMD ["sh", "-c", "php artisan optimize:clear; php artisan migrate --force; php artisan serve --host=0.0.0.0 --port=8000"]
