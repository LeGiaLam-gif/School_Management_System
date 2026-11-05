# syntax=docker/dockerfile:1
# Multi-stage build (muc 5.7): vendor -> assets -> base -> dev | production.
#   docker build --target dev .          (dung boi docker-compose.yml, ma nguon bind-mount)
#   docker build --target production .   (image tu chua day du ma nguon, khong can mount)

# ---- 1. Thu vien PHP (production, khong co dev) ------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist
COPY . .
RUN composer dump-autoload --optimize --no-dev --no-scripts

# ---- 2. Build asset (Vite + Bootstrap) --------------------------------------
FROM node:26-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json* ./
RUN npm install --no-audit --no-fund
COPY vite.config.js ./
COPY resources ./resources
COPY app/Modules ./app/Modules
RUN npm run build

# ---- 3. Nen PHP-FPM ----------------------------------------------------------
FROM php:8.3-fpm-bookworm AS base
RUN apt-get update && apt-get install -y --no-install-recommends \
        git unzip libzip-dev libicu-dev libonig-dev default-mysql-client \
    && docker-php-ext-install pdo_mysql bcmath intl zip opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/zz-sms.ini
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh
WORKDIR /var/www/html
ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]

# ---- 4a. Phat trien: ma nguon duoc mount, vendor cai o lan chay dau ----------
FROM base AS dev
ENV COMPOSER_ALLOW_SUPERUSER=1 APP_ENV=local

# ---- 4b. Production: ma nguon + vendor + asset nam trong image --------------
FROM base AS production
ENV APP_ENV=production APP_DEBUG=false
COPY --from=vendor /app /var/www/html
COPY --from=assets /app/public/build /var/www/html/public/build
RUN printf "opcache.validate_timestamps=0\n" > /usr/local/etc/php/conf.d/zz-opcache-prod.ini \
    && chown -R www-data:www-data storage bootstrap/cache
USER www-data
