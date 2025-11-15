# ==================================================
# GESTMAT V2 - PRODUCTION DOCKERFILE
# ==================================================
# Multi-stage build for optimized image size
# ==================================================

# ==================================================
# STAGE 1: Node Builder (Frontend Assets)
# ==================================================
FROM node:20-alpine AS node-builder

WORKDIR /app

# Copy package files
COPY package*.json ./

# Install dependencies
RUN npm ci --production=false

# Copy source files
COPY resources ./resources
COPY vite.config.js ./
COPY tailwind.config.js ./

# Build assets
RUN npm run build

# ==================================================
# STAGE 2: Composer Builder (PHP Dependencies)
# ==================================================
FROM composer:2 AS composer-builder

WORKDIR /app

# Copy composer files
COPY composer.json composer.lock ./

# Install dependencies
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-interaction \
    --prefer-dist \
    --optimize-autoloader

# ==================================================
# STAGE 3: Production Image
# ==================================================
FROM php:8.4-fpm-alpine

# Metadata
LABEL maintainer="GestMat Team"
LABEL version="2.0"
LABEL description="GestMat v2 - Asset Management System"

# Install system dependencies
RUN apk add --no-cache \
    nginx \
    supervisor \
    postgresql-client \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    oniguruma-dev \
    icu-dev \
    bash \
    curl \
    git

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        gd \
        zip \
        mbstring \
        bcmath \
        opcache \
        intl \
        exif \
        pcntl

# Install Redis extension
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del pcre-dev $PHPIZE_DEPS

# Configure PHP for production
COPY deployer/docker/php.ini /usr/local/etc/php/conf.d/gestmat.ini
COPY deployer/docker/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY --chown=www-data:www-data . .

# Copy built frontend assets from node-builder
COPY --from=node-builder --chown=www-data:www-data /app/public/build ./public/build

# Copy vendor from composer-builder
COPY --from=composer-builder --chown=www-data:www-data /app/vendor ./vendor

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configure Nginx
COPY deployer/docker/nginx.conf /etc/nginx/nginx.conf
COPY deployer/docker/default.conf /etc/nginx/http.d/default.conf

# Configure Supervisor
COPY deployer/docker/supervisord.conf /etc/supervisord.conf

# Create necessary directories
RUN mkdir -p /var/log/supervisor \
    && mkdir -p /run/nginx

# Health check
HEALTHCHECK --interval=30s --timeout=3s --start-period=40s --retries=3 \
    CMD curl -f http://localhost/health || exit 1

# Expose port
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
