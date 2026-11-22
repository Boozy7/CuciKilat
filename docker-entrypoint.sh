#!/bin/sh
set -e

# Pastikan direktori writable Laravel tersedia
mkdir -p /app/storage/framework/cache/data \
         /app/storage/framework/sessions \
         /app/storage/framework/views \
         /app/storage/logs \
         /app/bootstrap/cache

# Wajib ada APP_KEY dari file .env / environment container (tidak di-hardcode di image)
if [ -z "$APP_KEY" ]; then
    echo "[CuciKilat] ERROR: APP_KEY belum diset. Pastikan file .env berisi APP_KEY yang valid." >&2
    exit 1
fi

# Jalankan migrasi dan seeder idempoten saat container start
if [ "$RUN_MIGRATIONS" = "true" ]; then
    echo "[CuciKilat] Menjalankan migrasi dan seeder database..."
    php artisan migrate --force --seed
fi

exec "$@"
