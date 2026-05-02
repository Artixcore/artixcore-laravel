# syntax=docker/dockerfile:1
# Production-oriented image for DigitalOcean App Platform (or any container host).
# Serves the app with `php artisan serve` on the platform-provided $PORT (default 8080).

FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql opcache zip gd \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1
COPY . .
COPY --from=assets /app/public/build ./public/build

RUN cp .env.example .env \
    && php artisan key:generate --force --no-interaction

RUN composer install --no-dev --no-interaction --prefer-dist

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 8080
ENV PORT=8080

CMD ["sh", "-c", "php artisan serve --host=0.0.0.0 --port ${PORT:-8080}"]
