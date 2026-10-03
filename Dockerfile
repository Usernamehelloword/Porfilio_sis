# ==========================================
# Stage 1: Build Frontend Assets (Vite)
# ==========================================
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package definitions
COPY package*.json ./

# Install npm dependencies
RUN npm ci --prefer-offline --no-audit || npm install

# Copy application source for frontend compilation
COPY . .

# Compile production assets with Vite into public/build
RUN npm run build

# ==========================================
# Stage 2: Production PHP Application
# ==========================================
FROM php:8.4-fpm-alpine

# Set environment variables for non-interactive builds
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Install required system packages
RUN apk add --no-cache \
    nginx \
    curl \
    bash \
    sqlite \
    sqlite-dev \
    libzip-dev \
    zip \
    unzip \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    dos2unix

# Install official PHP extension installer helper
COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install essential PHP extensions for Laravel
RUN install-php-extensions \
    pdo \
    pdo_sqlite \
    pdo_mysql \
    pdo_pgsql \
    bcmath \
    ctype \
    curl \
    dom \
    fileinfo \
    json \
    mbstring \
    openssl \
    pcre \
    tokenizer \
    xml \
    zip \
    opcache \
    intl \
    pcntl \
    gd

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# Copy composer files and install production dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --ignore-platform-req=php

# Copy application code
COPY . .

# Copy compiled frontend assets from the node-builder stage
COPY --from=node-builder /app/public/build ./public/build

# Generate optimized Composer autoloader
RUN composer dump-autoload --optimize --no-dev --classmap-authoritative

# Copy custom Nginx and PHP configurations
COPY docker/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom-php.ini

# Set up entrypoint script and fix line endings for Linux
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN dos2unix /usr/local/bin/entrypoint.sh && chmod +x /usr/local/bin/entrypoint.sh

# Ensure storage, cache, and database directories exist with correct permissions
RUN mkdir -p storage/framework/sessions \
             storage/framework/views \
             storage/framework/cache \
             storage/logs \
             database \
    && chown -R www-data:www-data storage bootstrap/cache database \
    && chmod -R 775 storage bootstrap/cache database

# Render sets $PORT dynamically (defaults to 10000)
EXPOSE 10000

# Execute entrypoint
ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
