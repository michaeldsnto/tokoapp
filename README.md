# TokoApp POS

TokoApp adalah aplikasi kasir berbasis Laravel untuk kebutuhan toko harian. Project ini mendukung transaksi POS, nota manual, cetak receipt, export laporan CSV, dan role admin atau kasir.

## Fitur Utama

- Login dengan role `admin` dan `cashier`
- POS dengan perhitungan total, bayar, dan kembalian
- Nota manual untuk transaksi yang belum lunas
- Receipt web dan export PDF
- Laporan penjualan harian dan bulanan
- Kelola kategori dan produk

## Satuan Harga

Struktur harga produk yang dipakai di project ini:

- `satuan`: harga eceran
- `lusin`: harga per 12 item
- `pak`: harga per dus atau kardus

Karena `pak` di toko ini berarti dus atau kardus, harga `pak` memang lebih tinggi daripada `lusin`.

## Teknologi

- PHP 8.2
- Laravel 12
- SQLite untuk pengembangan lokal
- Vite untuk asset frontend
- DomPDF untuk export receipt PDF

## Setup Lokal

1. Install dependency backend.

```bash
composer install
```

2. Install dependency frontend.

```bash
npm install
```

3. Siapkan file environment.

```bash
copy .env.example .env
php artisan key:generate
```

4. Jalankan migrasi dan seed data awal.

```bash
php artisan migrate --seed
```

5. Jalankan aplikasi.

```bash
php artisan serve
npm run dev
```

## Akun Default

Seeder bawaan membuat dua akun:

- `admin@toko.test` / `password`
- `kasir@toko.test` / `password`

## Menjalankan Test

```bash
php artisan test
```

Test saat ini mencakup:

- redirect halaman awal ke login
- proses login user
- akurasi `average_order` pada laporan
- transaksi POS
- nota manual dan status lunas
- perhitungan harga unit produk

## Akses Dari HP

Jika ingin membuka project dari HP di jaringan Wi-Fi yang sama:

```bash
php artisan serve --host=0.0.0.0 --port=8000
npm run dev -- --host
```

Lalu buka dari HP dengan format:

```text
http://IP-LAPTOP:8000
```

## Struktur Fitur

- `app/Http/Controllers/PosController.php`: transaksi POS
- `app/Http/Controllers/ManualInvoiceController.php`: nota manual
- `app/Http/Controllers/ReceiptController.php`: tampil dan export nota
- `app/Http/Controllers/ReportController.php`: laporan dan export CSV
- `resources/views/`: seluruh tampilan web

## Siap Hosting

File untuk production sudah saya siapkan:

- [.env.production.example](/d:/laragon/www/tokoapp/.env.production.example): template environment production
- [DEPLOYMENT.md](/d:/laragon/www/tokoapp/DEPLOYMENT.md): langkah deploy ke hosting atau VPS

Checklist singkat sebelum online:

1. set `APP_ENV=production`
2. set `APP_DEBUG=false`
3. ganti database ke MySQL atau MariaDB
4. jalankan `php artisan migrate --force`
5. jalankan `npm run build`
6. arahkan document root ke folder `public`
7. aktifkan SSL dan cookie secure

## Catatan Pengembangan

- Timezone aplikasi memakai `APP_TIMEZONE` dengan default `Asia/Makassar`
- Untuk penggunaan produksi, ganti kredensial default seeder
- Jika asset tidak tampil, pastikan Vite berjalan
