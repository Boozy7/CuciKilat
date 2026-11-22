#!/bin/sh
set -e

# Buat direktori cache jika belum ada
mkdir -p /app/storage/framework/cache/data \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs \
         /app/bootstrap/cache

# Generate application key if missing
if [ -z "$APP_KEY" ]; then
    echo "[CuciKilat] Generating application key..."
    php artisan key:generate --force
fi

# Jalankan migrasi dan seeder bila terhubung ke database
if [ "$RUN_MIGRATIONS" = "true" ] || [ -n "$DB_HOST" ]; then
    echo "[CuciKilat] Menjalankan migrasi dan seeder database..."
    php artisan migrate --force --seed || true
fi

exec "$@"
