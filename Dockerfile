FROM php:8.2-cli

# تثبيت مكتبات النظام وأدوات PostgreSQL بالإضافة إلى Node.js
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

# تثبيت حزم PHP
RUN composer install --no-dev --optimize-autoloader

# السر هنا: بناء ملفات التصميم داخل السيرفر مباشرة
RUN npm install
RUN npm run build

RUN chown -R www-data:www-data storage bootstrap/cache

ENV APP_NAME=DevFit \
    APP_ENV=production \
    APP_DEBUG=true \
    DB_CONNECTION=pgsql

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]