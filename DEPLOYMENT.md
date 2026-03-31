# Deployment Guide

Panduan ini menyiapkan TokoApp untuk hosting online, baik di shared hosting yang mendukung Laravel maupun VPS.

## Kebutuhan Server

- PHP 8.2 atau lebih baru
- Composer
- MySQL atau MariaDB
- Ekstensi PHP umum Laravel:
  - `bcmath`
  - `ctype`
  - `fileinfo`
  - `json`
  - `mbstring`
  - `openssl`
  - `pdo`
  - `pdo_mysql`
  - `tokenizer`
  - `xml`
- Akses terminal atau SSH sangat membantu untuk deploy dan maintenance

## File Environment Production

1. Salin [.env.production.example](/d:/laragon/www/tokoapp/.env.production.example) menjadi `.env`
2. Isi nilai berikut:
   - `APP_KEY`
   - `APP_URL`
   - `DB_*`
   - `MAIL_*`
3. Pastikan nilai berikut aman untuk production:
   - `APP_ENV=production`
   - `APP_DEBUG=false`
   - `SESSION_SECURE_COOKIE=true`

## Langkah Deploy Dasar

1. Upload source code ke server
2. Jalankan:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Pastikan document root mengarah ke folder `public/`

## Shared Hosting

- Jika memakai cPanel, arahkan domain atau subdomain ke folder `public`
- Jika document root tidak bisa diubah, pindahkan isi folder `public` ke `public_html` dengan hati-hati dan sesuaikan `index.php`
- Gunakan database MySQL, jangan SQLite untuk hosting publik

## VPS atau Cloud Server

- Gunakan Nginx atau Apache
- Jalankan PHP-FPM sesuai versi PHP server
- Aktifkan HTTPS dengan SSL
- Siapkan worker untuk queue bila nanti fitur background job dipakai

## Perintah Maintenance Setelah Update

```bash
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## Checklist Sebelum Go Live

- `APP_DEBUG=false`
- `APP_URL` sudah domain final
- database production sudah benar
- `php artisan test` lulus di lokal sebelum upload
- folder `storage` dan `bootstrap/cache` writable
- `public/storage` sudah linked
- akun default seeder diganti password-nya atau dihapus
- SSL aktif

## Catatan

- Project ini memakai timezone `Asia/Makassar`
- Jika asset tidak muncul di production, pastikan hasil `npm run build` ikut ter-upload
