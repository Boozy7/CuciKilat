# Dokumen Skenario & Hasil Pengujian QA — CuciKilat

- **QA & Tech Writer:** Savira Desti Erinanda
- **Repository:** `Boozy7/CuciKilat`
- **Lingkungan Uji:** Local / Staging Deployment (Ngrok)
- **Status Dokumen:** Rencana & Skenario Pengujian Awal (Menunggu Eksekusi Akhir)

---

## 1. Cakupan Pengujian Fungsional & Aturan Bisnis

Pengujian difokuskan pada validasi aturan bisnis utama serta alur operasional aplikasi CuciKilat:

| No | Modul / Skenario | Kondisi Uji | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Health Check | Akses endpoint `/health` setelah deployment | Mengembalikan respons status HTTP 200 (Healthy) | Pending |
| 2 | Berat Minimum | Input pesanan dengan berat di bawah batas minimum | Sistem menerapkan tarif berdasarkan ketentuan berat minimum | Pending |
| 3 | Pembulatan Berat | Input pesanan dengan angka berat desimal | Sistem membulatkan berat sesuai aturan pembulatan tarif | Pending |
| 4 | Biaya & Estimasi Express | Pilih layanan dengan opsi *Express* | Sistem menghitung biaya tambahan dan estimasi waktu selesai *express* | Pending |
| 5 | Penolakan Perubahan Order | Ubah data/detail order yang statusnya sudah diambil | Sistem menolak perubahan data order sesuai aturan bisnis | Pending |
| 6 | Alur Order & Penimbangan | Buat order baru, lakukan penimbangan, dan perbarui status | Status order berubah berurutan dan kalkulasi tagihan sesuai | Pending |
| 7 | Pelacakan Publik | Cek status pesanan melalui halaman/fitur pelacakan publik | Menampilkan status terkini pesanan secara akurat | Pending |
| 8 | Laporan Pendapatan Harian | Buka halaman laporan pendapatan pada tanggal transaksi berjalan | Total pendapatan harian sesuai dengan akumulasi transaksi | Pending |

---

## 2. Verifikasi Automated Test (Laravel)

- **Target Verifikasi:** 24 tests, 195 assertions
- **Perintah Eksekusi:** `php artisan test`
- **Catatan Aktual:** *(Akan diisi setelah eksekusi konsol tes pada build terbaru)*

---

## 3. Rencana Uji Skenario Gagal–Pulih CI/CD (Pipeline Jenkins)

Koordinasi bersama tim DevOps & Backend untuk memvalidasi ketahanan pipeline:

1. **Skenario Gagal di Lint (Laravel Pint):**
   - Memicu pelanggaran *code style* agar tahap **Lint** gagal di Jenkins.
   - Memperbaiki format kode dan memverifikasi pipeline kembali hijau.
2. **Skenario Gagal di Test:**
   - Memicu kegagalan pada pengujian aturan berat minimum agar tahap **Test** gagal.
   - Memastikan container/aplikasi versi lama tetap berjalan normal selama *build* gagal.
   - Mengembalikan logika kode dan memverifikasi *build* kembali hijau.
