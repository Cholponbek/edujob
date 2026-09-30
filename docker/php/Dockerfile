# resources/js/app.js imports Ziggy directly from the composer package
# (vendor/tightenco/ziggy), so the frontend build needs vendor/ installed
# before it runs — hence a dedicated vendor stage shared by both the
# frontend build and the final image, instead of running composer install
# twice.
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist

# Стадия сборки фронтенда отдельная — node/npm не нужны в конечном
# php-fpm образе, только собранный public/build. Debian-based (glibc), не
# alpine: rollup's optional native binaries have a known npm-ci bug on musl
# where the platform-specific package silently fails to install.
FROM node:22-slim AS frontend
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY vite.config.js tailwind.config.js postcss.config.js jsconfig.json ./
COPY --from=vendor /app/vendor/tightenco ./vendor/tightenco
COPY resources ./resources
RUN npm run build

FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
        postgresql-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        $PHPIZE_DEPS \
    && docker-php-ext-install pdo pdo_pgsql pgsql bcmath zip intl opcache pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del $PHPIZE_DEPS

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
COPY --from=vendor /app/vendor ./vendor

COPY . .
COPY --from=frontend /app/public/build ./public/build
# --no-scripts: composer.json's post-autoload-dump hook runs `artisan
# package:discover`, which needs storage/framework/views (doesn't exist
# yet) and a real DB connection (doesn't exist at build time). entrypoint.sh
# runs package:discover for real once the container actually starts.
RUN composer dump-autoload --optimize --no-scripts

# .dockerignore исключает эти каталоги из контекста сборки, поэтому их нет
# в образе даже как пустых директорий — Laravel падает на первом же
# обращении (сессии, скомпилированные view, логи).
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker/php/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

CMD ["php-fpm"]
