# ============================================================
# Stage 1 – Node: compile frontend assets (client + SSR)
# ============================================================
FROM node:22-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --prefer-offline

COPY resources/ resources/
COPY public/ public/
COPY vite.config.js jsconfig.json tailwind.config.js postcss.config.js ./

RUN npm run build


# ============================================================
# Stage 2 – Composer: install PHP deps (no dev)
# ============================================================
FROM composer:2.8 AS vendor

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev


# ============================================================
# Stage 3 – Runtime: PHP 8.4-FPM + Nginx (production)
# ============================================================
FROM php:8.4-fpm-alpine AS runtime

LABEL maintainer="Banda Alta Vox"

# System deps + PHP extensions
RUN apk add --no-cache \
    nginx \
    supervisor \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        gd \
        zip \
        bcmath \
        intl \
        opcache \
        pcntl \
    && pecl install redis \
    && docker-php-ext-enable redis

# PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf

# Nginx config
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Supervisor config (PHP-FPM + Nginx + queue worker)
COPY docker/supervisor/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

WORKDIR /var/www/html

# Copy application
COPY --chown=www-data:www-data --from=vendor /app /var/www/html
COPY --chown=www-data:www-data --from=frontend /app/public/build /var/www/html/public/build
COPY --chown=www-data:www-data --from=frontend /app/bootstrap/ssr /var/www/html/bootstrap/ssr

# Writable dirs
RUN mkdir -p storage/logs storage/framework/{cache,sessions,views,testing} bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
