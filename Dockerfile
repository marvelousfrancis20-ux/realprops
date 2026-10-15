# syntax=docker/dockerfile:1

# ---------------------------------------------------------------------------
# Stage 1: front-end assets (Vite + Tailwind)
# ---------------------------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
RUN npm run build

# ---------------------------------------------------------------------------
# Stage 2: composer dependencies
# ---------------------------------------------------------------------------
FROM composer:2 AS vendor
WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --no-progress

COPY . .
RUN composer dump-autoload --optimize --no-dev

# ---------------------------------------------------------------------------
# Stage 3: runtime (PHP-FPM 8.2)
# ---------------------------------------------------------------------------
FROM php:8.2-fpm-alpine

RUN apk add --no-cache \
        fcgi curl git libpng libjpeg-turbo libwebp freetype libzip icu-libs \
    && docker-php-ext-configure gd \
        --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql mbstring bcmath opcache pcntl zip gd exif intl sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del git

# Reasonable production defaults; fine-tune per environment.
RUN { \
        echo 'opcache.enable=1'; \
        echo 'opcache.memory_consumption=256'; \
        echo 'opcache.max_accelerated_files=20000'; \
        echo 'realpath_cache_size=4096k'; \
        echo 'realpath_cache_ttl=600'; \
    } > /usr/local/etc/php/conf.d/realprops.ini

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY --from=vendor --chown=www-data:www-data /app /var/www/html
COPY --from=assets --chown=www-data:www-data /app/public/build /var/www/html/public/build
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/php-fpm-healthcheck /usr/local/bin/php-fpm-healthcheck

RUN chmod +x /usr/local/bin/entrypoint.sh /usr/local/bin/php-fpm-healthcheck \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production \
    APP_DEBUG=false \
    OCTANE_FORCE_TERMINATE=true

EXPOSE 9000

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD php-fpm-healthcheck || exit 1

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
