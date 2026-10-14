FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git unzip zip curl libpq-dev libicu-dev libzip-dev \
    && docker-php-ext-configure pdo_pgsql --with-pdo-pgsql=/usr \
    && docker-php-ext-install pdo_pgsql pcntl intl zip \
    && curl -sS https://getcomposer.org/installer \
    | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY . .

# السماح للـ Composer بالعمل بدون قيود ذاكرة أو تفاعلات
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader || composer install --no-interaction

RUN chown -R www-data:www-data storage bootstrap/cache public

ENV APP_NAME=DevFit \
    APP_ENV=production \
    APP_DEBUG=true \
    DB_CONNECTION=pgsql

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]