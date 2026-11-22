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

RUN rm -f bootstrap/cache/*.php \
    && mkdir -p storage/framework/cache/data \
                storage/framework/sessions \
                storage/framework/views \
                storage/logs \
                bootstrap/cache \
    && touch .env \
    && php artisan package:discover --ansi

# Jalankan seluruh unit & feature test otomatis pada SQLite in-memory dengan dummy key khusus CI
RUN APP_ENV=testing \
    APP_KEY=base64:AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA= \
    DB_CONNECTION=sqlite \
    DB_DATABASE=:memory: \
    SESSION_DRIVER=array \
    CACHE_STORE=array \
    QUEUE_CONNECTION=sync \
    php artisan test

# ==========================================
# STAGE 5: Production Runner (Final Image)
# ==========================================
FROM base AS runner

# Install only production dependencies
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader --no-scripts

# Copy application source code
COPY . .

# Prepare Laravel directories, clean stale cache, discover prod packages, and set permissions
RUN rm -f /app/bootstrap/cache/*.php \
    && mkdir -p /app/storage/framework/cache/data \
                /app/storage/framework/sessions \
                /app/storage/framework/views \
                /app/storage/logs \
                /app/bootstrap/cache \
    && php artisan package:discover --ansi \
    && chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache

# Copy entrypoint script (pastikan LF agar aman dari checkout Windows)
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN sed -i 's/\r$//' /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
