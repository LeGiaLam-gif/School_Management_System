# syntax=docker/dockerfile:1

FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts
RUN php artisan package:discover --ansi

FROM node:22-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY app/Modules ./app/Modules
RUN npm run build

FROM php:8.3-fpm-bookworm AS app
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libonig-dev default-mysql-client \
    && docker-php-ext-install pdo_mysql bcmath intl zip opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*
RUN apt-get update && apt-get install -y --no-install-recommends gettext-base nginx supervisor \
    && rm -rf /var/lib/apt/lists/*

COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-sms.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/render/nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/render/supervisord.conf /etc/supervisor/conf.d/sms.conf
COPY docker/render/start.sh /usr/local/bin/render-start.sh
RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/render-start.sh \
    && rm -f /etc/nginx/sites-enabled/default

WORKDIR /var/www/html
COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build
RUN mkdir -p storage/framework/cache/data storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && printf "opcache.validate_timestamps=0\n" > /usr/local/etc/php/conf.d/zz-opcache-prod.ini

ENV APP_ENV=production APP_DEBUG=false PORT=10000
EXPOSE 10000
CMD ["/usr/local/bin/render-start.sh"]
