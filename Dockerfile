# ==========================================
# STAGE 1: Base Environment
# ==========================================
FROM php:8.4-cli-alpine AS base

# Install runtime & build dependencies
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    sqlite-dev \
    mysql-client

# Install required PHP extensions
RUN docker-php-ext-install \
    pdo_mysql \
    pdo_sqlite \
    bcmath \
    zip \
    intl \
    opcache

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# ==========================================
# STAGE 2: Vendor Dependencies (Dev + Prod)
# ==========================================
FROM base AS dependencies

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts

# ==========================================
# STAGE 3: Lint Quality Gate (DevOps K06)
# ==========================================
FROM dependencies AS lint

COPY . .
# Stage lint wajib gagal jika ada pelanggaran gaya kode
RUN ./vendor/bin/pint --test

# ==========================================
# STAGE 4: Automated Testing Quality Gate
# ==========================================
FROM dependencies AS test

COPY . .

ENV APP_ENV=testing \
    DB_CONNECTION=sqlite \
    DB_DATABASE=:memory:

# Jalankan seluruh unit & feature test otomatis
RUN php artisan test

# ==========================================
# STAGE 5: Production Runner (Final Image)
# ==========================================
FROM base AS runner

# Install only production dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source code
COPY . .

# Set permissions for Laravel storage & cache
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
