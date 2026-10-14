FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr \
    && docker-php-ext-install pdo_pgsql pcntl \
    && curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

RUN composer install --no-dev --optimize-autoloader

# 1. بناء ملفات التصميم أولاً لتتولد على القرص
RUN npm install
RUN npm run build

# 2. مسح الكاش وإعادة بناءه بعد توليد المانيفست لضمان قراءته بشكل صحيح
RUN php artisan config:clear \
    && php artisan view:clear \
    && php artisan route:clear \
    && php artisan config:cache

RUN chown -R www-data:www-data storage bootstrap/cache public

ENV APP_NAME=DevFit \
    APP_ENV=production \
    APP_DEBUG=true \
    DB_CONNECTION=pgsql

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]