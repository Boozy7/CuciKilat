# CuciKilat (K06) — Tugas Besar Komputasi Awan

Aplikasi manajemen laundry antar-jemput berbasis **Laravel + MySQL**.

## Prasyarat

- PHP 8.3+ dengan ekstensi `pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, `curl`
- Composer
- MySQL / MariaDB

## Setup Lokal

1. Install dependensi PHP:
   ```bash
   composer install
   ```
2. Salin file environment contoh dan sesuaikan kredensial MySQL lokal (jangan commit `.env` ke Git):
   ```bash
   cp .env.example .env
   ```
   Sesuaikan variabel berikut di `.env`:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=cucikilat
   DB_USERNAME=your_mysql_username
   DB_PASSWORD=your_mysql_password
   ```
3. Generate application key:
   ```bash
   php artisan key:generate
   ```
4. Buat database MySQL untuk aplikasi dan database terpisah untuk testing:
   ```sql
   CREATE DATABASE IF NOT EXISTS cucikilat;
   CREATE DATABASE IF NOT EXISTS cucikilat_testing;
   ```

## Migration & Seeder

Jalankan migration dan seeder master layanan awal:

```bash
php artisan migrate
php artisan db:seed
```

Seeder `ServiceSeeder` mengisi tiga layanan awal dengan **tarif contoh (data demo)** yang dapat diubah dari master layanan:
- **Reguler** (`per_kg`, tarif demo Rp7.000/kg, estimasi 3 hari)
- **Express** (`per_kg`, tarif demo Rp7.000/kg + tambahan 50%, estimasi 1 hari)
- **Satuan** (`per_item`, tarif demo Rp15.000/item, estimasi 2 hari)

> **Catatan keputusan kelompok:**
> - Aturan layanan **Satuan** sementara memakai asumsi awal `jumlah item × harga per item` dengan estimasi yang dapat dikonfigurasi dari master layanan. Konfirmasi kembali bersama kelompok sebelum finalisasi.
> - Status pembayaran terpisah dari status cucian (`unpaid`, `pending`, `paid`) dan sementara dicatat manual oleh petugas. Integrasi **Midtrans Sandbox** menunggu keputusan kelompok.

### Akun Petugas Demo (Seeder)

`StaffUserSeeder` membuat akun petugas demo idempoten untuk pengembangan lokal dan pengujian:
- Email: `admin@cucikilat.local`
- Password: `password`

## Endpoint Backend (Tahap 2)

### Publik (Pelanggan)
- `GET /services` — Daftar layanan aktif (`is_active = true`)
- `POST /orders` — Buat permintaan jemput (`service_id`, `customer_name`, `customer_phone`, `pickup_address`)
- `GET /track/{orderCode}` — Lacak order berdasarkan `order_code` (tanpa mengekspos nama, telepon, atau alamat pelanggan)

### Autentikasi Petugas
- `POST /login` — Login petugas (`email`, `password`)
- `POST /logout` — Logout petugas (memerlukan sesi login)

### Petugas / Admin (Dilindungi Middleware `auth`)
- `GET /staff/orders` — Daftar order (opsional filter `status`, `payment_status`, `search`)
- `GET /staff/orders/{order}` — Detail order lengkap
- `PATCH /staff/orders/{order}/status` — Update status berurutan (`waiting_pickup` → `picked_up` → `processing` → `ready_for_delivery` → `delivered`)
- `POST /staff/orders/{order}/weigh` — Catat berat aktual gram (`actual_weight_grams`) atau jumlah item (`item_count`), hitung tagihan & estimasi, dan simpan snapshot tarif
- `PATCH /staff/orders/{order}/payment` — Catat status pembayaran manual (`unpaid`, `pending`, `paid`)
- `GET|POST /staff/services`, `GET|PUT|PATCH|DELETE /staff/services/{service}` — CRUD master layanan (layanan yang sudah dipakai order otomatis dinonaktifkan `is_active = false` saat dihapus)
- `GET /staff/reports/daily-revenue?date=YYYY-MM-DD` — Laporan pendapatan harian terkonfirmasi (`paid`) berdasarkan tanggal bayar WIB (`Asia/Jakarta`)

## Cek Endpoint `/health`

Jalankan server lokal:
```bash
php artisan serve
```

Lalu cek endpoint health check:
```bash
curl -i http://127.0.0.1:8000/health
```

- **HTTP 200** saat aplikasi dan koneksi MySQL sehat:
  ```json
  {"status":"ok","database":"ok"}
  ```
- **HTTP 503** saat koneksi database gagal (tanpa menampilkan kredensial atau detail exception):
  ```json
  {"status":"error","database":"unavailable"}
  ```

## Test & Lint

Jalankan automated test (menggunakan database terpisah `cucikilat_testing` sesuai `phpunit.xml`):
```bash
php artisan test
```

Jalankan pemeriksaan format kode dengan Laravel Pint:
```bash
./vendor/bin/pint --test
```
