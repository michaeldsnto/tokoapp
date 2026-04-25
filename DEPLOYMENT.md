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
   - `APP_FORCE_HTTPS=true`
   - `SESSION_SECURE_COOKIE=true`
   - `ALLOW_DEMO_SEEDER=false`
4. Jika hosting berada di belakang Cloudflare, reverse proxy, atau load balancer:
   - set `TRUSTED_PROXIES=*`
   - jika tidak, isi dengan IP proxy yang dipakai

## Langkah Deploy Dasar

1. Upload source code ke server
2. Jalankan:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Atau gunakan script yang sudah disiapkan:

```bash
npm install
npm run build
composer run deploy:prod
```

Catatan penting:

- `php artisan db:seed --force` jangan dijalankan di production kecuali memang sengaja ingin memuat data demo.
- Seeder demo sekarang otomatis berhenti di environment production selama `ALLOW_DEMO_SEEDER=false`.
- Setelah deploy pertama, buat admin production sendiri:

```bash
php artisan app:create-admin "Nama Admin" admin@domain.com "password-kuat-sekali"
```

3. Pastikan document root mengarah ke folder `public/`

## Shared Hosting / cPanel

Gunakan **MySQL/MariaDB**, bukan SQLite.

### Opsi A: Document root bisa diarahkan ke `public/`

Ini opsi terbaik.

1. Upload project ke folder di luar `public_html`, contoh:

```text
/home/USERNAME/tokoapp
```

2. Di cPanel:
   - buat domain atau subdomain
   - arahkan document root ke:

```text
/home/USERNAME/tokoapp/public
```

3. Jalankan deploy:

```bash
cd /home/USERNAME/tokoapp
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan storage:link
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

4. Jika server cPanel tidak menyediakan Node.js:
   - jalankan `npm install` dan `npm run build` di lokal
   - upload folder `public/build`

### Opsi B: Document root tidak bisa diarahkan ke `public/`

Pakai ini hanya jika cPanel Anda memaksa domain tetap ke `public_html`.

Struktur yang disarankan:

```text
/home/USERNAME/tokoapp
/home/USERNAME/public_html
```

Langkah:

1. Upload seluruh project Laravel ke:

```text
/home/USERNAME/tokoapp
```

2. Jalankan di folder project:

```bash
cd /home/USERNAME/tokoapp
composer install --no-dev --optimize-autoloader
php artisan key:generate
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Salin aset publik Laravel ke `public_html`:
   - `favicon.ico`
   - `robots.txt`
   - `manifest.webmanifest`
   - `sw.js`
   - folder `build`
   - folder `icons`

4. Gunakan template ini untuk `public_html`:
   - [index.php.example](/d:/laragon/www/tokoapp/deploy/cpanel/public_html/index.php.example)
   - [.htaccess.example](/d:/laragon/www/tokoapp/deploy/cpanel/public_html/.htaccess.example)

5. Rename:
   - `index.php.example` menjadi `index.php`
   - `.htaccess.example` menjadi `.htaccess`

6. Edit path `$appRoot` di file `public_html/index.php` agar menunjuk ke folder Laravel yang benar.

### Storage di cPanel

Ideal:

```bash
php artisan storage:link
```

Jika symlink diblokir hosting:

- upload gambar langsung ke `public/storage`
- atau minta hosting mengaktifkan symlink
- atau gunakan disk publik non-symlink khusus cPanel

Untuk project ini, sebaiknya pilih hosting yang mengizinkan `storage:link`.

### Build asset di cPanel

Jika cPanel punya Node.js:

```bash
npm install
npm run build
```

Jika tidak:

- build di lokal
- upload hasil `public/build`

### Permission minimum

- `storage/` writable
- `bootstrap/cache/` writable
- file `.env` tidak boleh public

### Alur go-live cPanel paling aman

1. upload project ke folder di luar `public_html`
2. buat database MySQL
3. isi `.env`
4. `composer install --no-dev --optimize-autoloader`
5. `php artisan key:generate`
6. `php artisan migrate --force`
7. `php artisan storage:link`
8. upload/build asset production
9. `php artisan app:create-admin "Nama Admin" admin@domain.com "password-kuat"`
10. aktifkan SSL
11. cek login, upload gambar, receipt PDF, dan PWA

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
- `APP_FORCE_HTTPS=true`
- database production sudah benar
- `php artisan test` lulus di lokal sebelum upload
- folder `storage` dan `bootstrap/cache` writable
- `public/storage` sudah linked
- tidak menjalankan seed data demo di production
- admin production sudah dibuat dengan `php artisan app:create-admin`
- SSL aktif

## Smoke Test Setelah Deploy

Uji minimum ini langsung di domain production:

1. buka halaman login
2. login dengan admin production
3. buka dashboard
4. tambah 1 produk atau edit produk
5. buat 1 transaksi POS
6. buka receipt web
7. unduh receipt PDF
8. pastikan asset CSS/JS tampil normal
9. pastikan icon dan manifest PWA bisa dimuat
10. jika pakai upload gambar, cek `public/storage`

## Catatan

- Project ini memakai timezone `Asia/Makassar`
- Jika asset tidak muncul di production, pastikan hasil `npm run build` ikut ter-upload
- Jika login loop atau URL masih `http`, periksa `APP_URL`, `APP_FORCE_HTTPS`, dan `TRUSTED_PROXIES`
- Jika PDF gagal di shared hosting, cek ekstensi PHP yang dibutuhkan DomPDF dan permission folder `storage`
