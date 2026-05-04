# syntax=docker/dockerfile:1
# Production-oriented image for DigitalOcean App Platform (or any container host).
# Serves the app with `php artisan serve` on the platform-provided $PORT (default 8080).
#
# -----------------------------------------------------------------------------
# Production deploy sequence (DigitalOcean App Platform — see .do/app.yaml):
#   Build: composer install --no-dev (below), npm ci + npm run build (assets stage), copy public/build.
#   Pre-deploy job: php artisan migrate --force && php artisan db:seed --class=RolePermissionSeeder --force
#   Web/worker boot: optimize:clear && config:cache && route:cache && view:cache (same env as runtime).
# Do not bake config/route/view caches into the image (APP_KEY / DB only exist at run time).
#
# -----------------------------------------------------------------------------
# APP_KEY belongs in runtime environment variables (DigitalOcean Secrets), NOT in the image build
#
# Laravel uses APP_KEY for encryption (sessions, cookies, etc.). Generating it inside `docker build`
# would bake a key into the image layer history, tie all replicas to one leaked/static key, and
# prevent independent rotation per environment. DigitalOcean injects env at runtime; set APP_KEY there.
#
# Generate locally once per environment: `php artisan key:generate --show`
# Paste the full `base64:...` value into App Platform as an encrypted SECRET named APP_KEY.
#
# Never run `php artisan` before `composer install`: artisan bootstraps via vendor/autoload.php.
# -----------------------------------------------------------------------------
#
FROM node:20-bookworm-slim AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.3-cli-bookworm

# exif: required by spatie/image / spatie/laravel-medialibrary (orientation/metadata).
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    libicu-dev \
    libpng-dev \
    libzip-dev \
    zip \
    && docker-php-ext-configure intl \
    && docker-php-ext-install -j"$(nproc)" intl pdo_mysql opcache zip gd exif \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

ENV COMPOSER_ALLOW_SUPERUSER=1

# 1) Install Composer dependencies first so vendor/autoload.php exists before any artisan usage.
#    --no-scripts skips post-install hooks that call `php artisan` before the full app tree is copied.
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction \
    --prefer-dist \
    --no-scripts

# 2) Application source (see .dockerignore: vendor/, node_modules/, public/build excluded).
COPY . .

# 3) Vite production assets from the Node stage.
COPY --from=assets /app/public/build ./public/build

# 4) Regenerate optimized autoload now that app/, routes/, etc. are present (still no artisan).
RUN composer dump-autoload --optimize --no-dev --no-interaction --no-scripts

RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R ug+rwx storage bootstrap/cache

EXPOSE 8080
ENV PORT=8080

CMD ["sh", "-c", "php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache && (php artisan storage:link --force >/dev/null 2>&1 || true) && php artisan serve --host=0.0.0.0 --port ${PORT:-8080}"]
