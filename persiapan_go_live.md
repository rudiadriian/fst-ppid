# Persiapan GO LIVE — PPID Food Station

## Konteks (yang sudah disiapkan tim)

1. Domain + SSL `ppid.foodstation.co.id` → folder `fe-ppid` (portal publik + portal pemohon)
2. Domain + SSL `api-ppid.foodstation.co.id` → folder `api-ppid` (REST API JWT)
3. Domain + SSL `adm-ppid.foodstation.co.id` → folder `be-ppid` (panel admin React/Vite)
4. GitLab CI/CD — `git@gitlab.com:fs-group4/fst-ppid.git`
5. Server Ubuntu `192.168.1.21:22`, Reverse Proxy `192.168.1.17`, Nginx, PostgreSQL

---

## 0. Peta arsitektur (wajib dipahami sebelum deploy)

| Domain | Folder | Jenis | Cara dilayani |
|---|---|---|---|
| `ppid.foodstation.co.id` | `fe-ppid` | Laravel 10 + Blade | Nginx + PHP-FPM |
| `api-ppid.foodstation.co.id` | `api-ppid` | Laravel 10 + JWT (`tymon/jwt-auth`) | Nginx + PHP-FPM |
| `adm-ppid.foodstation.co.id` | `be-ppid` | React 19 + Vite (SPA statis) | Nginx serve folder `build/` |

Tiga hal yang **wajib** diketahui karena menentukan seluruh langkah di bawah:

1. **`fe-ppid` dan `api-ppid` memakai database yang sama (`ppiddb`) secara langsung.**
   Tidak ada panggilan HTTP antar keduanya. `fe-ppid` membaca/menulis tabel yang skemanya
   dimiliki `api-ppid`.
2. **Skema database hanya dimiliki `api-ppid`** (27 migration, termasuk tabel `users`).
   `fe-ppid` masih membawa 4 migration bawaan Laravel yang juga membuat tabel `users` →
   **jangan pernah menjalankan `php artisan migrate` di `fe-ppid`.** Lihat Langkah 1.5.
3. **Berkas media dibagi pakai (shared filesystem).** `api-ppid` menulis unggahan CMS
   langsung ke folder `storage/app/public` milik `fe-ppid` (disk `media`, env `MEDIA_ROOT`),
   dan dokumen unduhan-terbatas ke disk `dokumen_terbatas` (env `DOKUMEN_TERBATAS_ROOT`)
   yang **harus menunjuk folder yang sama** dari kedua aplikasi.
   Konsekuensinya: `fe-ppid` dan `api-ppid` harus berada di **satu server yang sama**
   (atau berbagi NFS/S3). Tidak bisa dipisah server tanpa mengubah konfigurasi disk.

---

## 1. BLOCKER — harus dibereskan sebelum deploy

Ini temuan dari kode saat ini. Semuanya menghentikan atau merusak produksi kalau dilewat.

### 1.1 `be-ppid/.env` ikut ter-commit ke Git — rahasia bocor

```
$ git ls-files | grep '\.env$'
be-ppid/.env
```

File itu berisi `VITE_MAP_KEY` dan `VITE_FIREBASE_API_KEY`. `be-ppid/.gitignore` hanya
mengabaikan `.env.local`, `.env.development.local`, dst. — **bukan `.env`**.

> **Peringatan keamanan.** Kunci yang pernah masuk riwayat Git harus dianggap bocor.
> Menghapus file pada commit berikutnya **tidak** menghapusnya dari riwayat. Urutan yang benar:
> 1. **Rotasi/regenerate** `VITE_MAP_KEY` (Google Maps API key) dan kunci Firebase di
>    console masing-masing, lalu batasi key Maps dengan HTTP referrer
>    `https://adm-ppid.foodstation.co.id/*`.
> 2. Baru kemudian bersihkan dari repo.

Perbaikan di repo:

```bash
cd be-ppid
printf '\n.env\n.env.production\n' >> .gitignore
git rm --cached .env
cp .env .env.example   # lalu kosongkan nilai rahasianya di .env.example
git add .gitignore .env.example
git commit -m "chore(be-ppid): stop tracking .env, add .env.example"
```

Karena repo GitLab masih kosong, ini kesempatan terbaik untuk membersihkan riwayat
sekalian (opsional tapi disarankan) sebelum push pertama — lihat Langkah 10.1.

### 1.2 `VITE_API_BASE_URL` di `be-ppid` salah arah

Sekarang: `VITE_API_BASE_URL=http://localhost:3000` — itu alamat panel itu sendiri, bukan API.

`be-ppid/src/utils/api.ts:13` menyusun `prefixUrl` sebagai `${API_BASE_URL}/api`, lalu
request memakai path `v1/...`. Jadi nilai produksi harus **tanpa** `/api` dan **tanpa**
garis miring di akhir:

```
VITE_API_BASE_URL=https://api-ppid.foodstation.co.id
```

Ini variabel build-time Vite — **harus sudah benar saat `npm run build`**. Mengubahnya
setelah build tidak berpengaruh apa pun.

### 1.3 `TrustProxies` belum diisi → HTTPS akan pecah di belakang reverse proxy

`api-ppid/app/Http/Middleware/TrustProxies.php:15` dan file yang sama di `fe-ppid`:

```php
protected $proxies;   // null
```

Dengan `null`, Laravel mengabaikan header `X-Forwarded-Proto` dari reverse proxy
`192.168.1.17`. Akibatnya `url()`, `asset()`, tautan verifikasi email, dan redirect
setelah login memakai skema `http://` — di browser jadi mixed content atau redirect loop.

Perbaiki di **kedua** aplikasi:

```php
// api-ppid/app/Http/Middleware/TrustProxies.php
// fe-ppid/app/Http/Middleware/TrustProxies.php
protected $proxies = ['192.168.1.17', '127.0.0.1'];
```

Tambahkan IP lain kalau reverse proxy punya lebih dari satu alamat. Hindari `'*'` kecuali
Anda yakin Nginx di 192.168.1.21 tidak bisa dijangkau langsung dari luar.

### 1.4 `MEDIA_ROOT` masih path Windows, dan `DOKUMEN_TERBATAS_ROOT` belum ada sama sekali

`api-ppid/.env` saat ini:

```
MEDIA_ROOT="D:/Project/ppid/fe-ppid/storage/app/public"
MEDIA_URL=http://192.168.10.250:8000/storage
```

Path Windows jelas tidak berlaku di Ubuntu. Yang lebih berbahaya:
**`DOKUMEN_TERBATAS_ROOT` tidak ada di `.env` maupun `.env.example` kedua aplikasi.**
Kalau dibiarkan, nilainya jatuh ke default `storage_path('app/dokumen-terbatas')` milik
masing-masing aplikasi → `api-ppid` menulis dokumen terbatas ke foldernya sendiri,
`fe-ppid` mencarinya di folder lain, dan **setiap unduhan dokumen terbatas akan 404**.

Nilai produksi yang benar ada di Langkah 5.

### 1.5 Jangan jalankan migration dari `fe-ppid`

`fe-ppid/database/migrations` masih berisi 4 migration bawaan Laravel
(`2014_10_12_000000_create_users_table.php` dkk). Tabel `users` sudah dibuat oleh
`api-ppid/database/migrations/2026_07_20_000000_create_skema_dasar_ppid.php:91`.

Menjalankan `php artisan migrate` di `fe-ppid` pada DB yang sama akan gagal — atau lebih
buruk, merusak tabel. Aturan tetap: **semua `migrate` dan `db:seed` dijalankan dari
`api-ppid` saja.** Skrip deploy di Langkah 10 sudah mengikuti aturan ini.

Disarankan hapus juga 4 file migration tersebut dari `fe-ppid` agar tidak ada yang tergoda
menjalankannya.

### 1.6 Konfigurasi Vercel sudah tidak relevan

`fe-ppid/vercel.json`, `fe-ppid/api/index.php`, `fe-ppid/DEPLOY.md`, dan
`be-ppid/vercel.json` menyasar deploy Vercel. Target sekarang VPS sendiri. Boleh dibiarkan
(tidak mengganggu Nginx), tapi `DEPLOY.md` sebaiknya ditandai usang supaya tidak diikuti
orang lain. Rujukan resmi adalah dokumen ini.

### 1.7 Remote Git masih GitHub

```
$ git remote -v
origin  https://github.com/rudiadriian/fst-ppid.git
```

Belum ada remote GitLab dan belum ada `.gitlab-ci.yml`. Lihat Langkah 10.

### 1.8 Belum ada akun DB khusus

`.env` masih memakai superuser `postgres`. Sesuai permintaan, buat role khusus — Langkah 3.

---

## 2. Siapkan server Ubuntu (192.168.1.21)

```bash
sudo apt update && sudo apt upgrade -y

# PHP 8.2 (Laravel 10 butuh >= 8.1; repo Ondrej dipakai bila bawaan Ubuntu masih 8.1)
sudo apt install -y software-properties-common
sudo add-apt-repository -y ppa:ondrej/php
sudo apt update
sudo apt install -y php8.2-fpm php8.2-cli php8.2-pgsql php8.2-mbstring \
  php8.2-xml php8.2-curl php8.2-zip php8.2-bcmath php8.2-gd php8.2-intl

# Composer
curl -sS https://getcomposer.org/installer | sudo php -- \
  --install-dir=/usr/local/bin --filename=composer

# Node 20 LTS (untuk build panel admin & aset Vite fe-ppid)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install -y nodejs

# Nginx, PostgreSQL, Supervisor, Git
sudo apt install -y nginx postgresql postgresql-contrib supervisor git unzip rsync

php -v && composer -V && node -v && nginx -v
```

Setelan PHP produksi — `/etc/php/8.2/fpm/php.ini`:

```ini
memory_limit = 512M
upload_max_filesize = 25M
post_max_size = 30M
max_execution_time = 120
expose_php = Off
date.timezone = Asia/Jakarta
```

> `upload_max_filesize` disesuaikan dengan berkas terbesar yang boleh diunggah pemohon
> (scan KTP, lampiran permohonan) dan petugas (dokumen informasi publik). Nilai ini harus
> sejalan dengan `client_max_body_size` di Nginx (Langkah 6) dan di reverse proxy (Langkah 7).

```bash
sudo systemctl restart php8.2-fpm
```

---

## 3. Database PostgreSQL + akun khusus

```bash
sudo -u postgres psql
```

```sql
-- Ganti sandi di bawah dengan sandi acak panjang (minimal 24 karakter).
CREATE ROLE ppid_app WITH LOGIN PASSWORD 'GANTI_DENGAN_SANDI_KUAT';

CREATE DATABASE ppiddb OWNER ppid_app ENCODING 'UTF8'
  LC_COLLATE='en_US.UTF-8' LC_CTYPE='en_US.UTF-8' TEMPLATE=template0;

\c ppiddb
GRANT ALL PRIVILEGES ON DATABASE ppiddb TO ppid_app;

-- PostgreSQL 15+ mencabut hak CREATE pada schema public dari PUBLIC.
-- Tanpa dua baris ini, `php artisan migrate` gagal:
--   "permission denied for schema public"
GRANT ALL ON SCHEMA public TO ppid_app;
ALTER SCHEMA public OWNER TO ppid_app;

ALTER ROLE ppid_app SET timezone TO 'Asia/Jakarta';
\q
```

Zona waktu penting: `config/app.php` kedua aplikasi memakai `Asia/Jakarta`, dan ada
migration `2026_08_19_000002_geser_waktu_lama_ke_jakarta.php` yang mengandalkan itu.

Uji koneksi dengan akun baru:

```bash
psql "host=127.0.0.1 port=5432 dbname=ppiddb user=ppid_app password=SANDI" -c '\conninfo'
```

Karena DB satu mesin dengan aplikasi, biarkan PostgreSQL hanya mendengar di localhost
(`listen_addresses = 'localhost'` pada `postgresql.conf`) — jangan dibuka ke LAN.

---

## 4. Tata letak folder deploy

```
/var/www/ppid/
├── api/        ← isi folder api-ppid
├── fe/         ← isi folder fe-ppid
├── adm/        ← hasil build be-ppid (isi folder build/)
└── shared/
    └── dokumen-terbatas/   ← dipakai bersama api & fe
```

Media publik tidak ditaruh di `shared/` karena desain aplikasi memang menjadikan
`fe/storage/app/public` sebagai sumber tunggal; `api-ppid` menulis ke sana lewat `MEDIA_ROOT`.

```bash
sudo mkdir -p /var/www/ppid/{api,fe,adm,shared/dokumen-terbatas}
sudo chown -R www-data:www-data /var/www/ppid
```

Setelah kode ada di tempatnya:

```bash
# Laravel: hanya dua folder ini yang perlu bisa ditulis
sudo chown -R www-data:www-data /var/www/ppid/api/storage /var/www/ppid/api/bootstrap/cache
sudo chown -R www-data:www-data /var/www/ppid/fe/storage  /var/www/ppid/fe/bootstrap/cache
sudo chmod -R 775 /var/www/ppid/api/storage /var/www/ppid/fe/storage
sudo chmod -R 775 /var/www/ppid/shared/dokumen-terbatas

# Tautan storage publik untuk fe-ppid (WAJIB — kalau tidak, semua gambar 404)
sudo -u www-data php /var/www/ppid/fe/artisan storage:link

# Berkas .env jangan terbaca user lain
sudo chmod 640 /var/www/ppid/api/.env /var/www/ppid/fe/.env
```

> `api-ppid` **tidak** perlu `storage:link` — disk publiknya diarahkan ke milik `fe-ppid`.
> Yang wajib `storage:link` hanya `fe-ppid`.

---

## 5. Berkas `.env` produksi

### 5.1 `/var/www/ppid/api/.env`

```ini
APP_NAME="PPID API"
APP_ENV=production
APP_KEY=            # isi hasil: php artisan key:generate --show
APP_DEBUG=false
APP_URL=https://api-ppid.foodstation.co.id
APP_LOCALE=id
APP_TIMEZONE=Asia/Jakarta

# CORS: hanya panel admin yang boleh memanggil API dari browser.
# Jangan tambah domain lain, dan jangan pernah pakai '*' (API ini menerima Bearer token).
ADMIN_ORIGINS=https://adm-ppid.foodstation.co.id

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ppiddb
DB_USERNAME=ppid_app
DB_PASSWORD=SANDI_DARI_LANGKAH_3
DB_PERSISTENT=true
DB_SSLMODE=prefer

# JWT — WAJIB baru, jangan pakai nilai development.
JWT_SECRET=            # isi hasil: php artisan jwt:secret --show
JWT_TTL=60
JWT_REFRESH_TTL=20160
JWT_BLACKLIST_ENABLED=true

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Berkas dibagi pakai dengan fe-ppid — lihat Langkah 1.4
MEDIA_ROOT=/var/www/ppid/fe/storage/app/public
MEDIA_URL=https://ppid.foodstation.co.id/storage
DOKUMEN_TERBATAS_ROOT=/var/www/ppid/shared/dokumen-terbatas

MAIL_MAILER=smtp
MAIL_HOST=srv179.niagahoster.com
MAIL_PORT=465
MAIL_USERNAME=noreply-ppid@foodstation.co.id
MAIL_PASSWORD=SANDI_EMAIL
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply-ppid@foodstation.co.id"
MAIL_FROM_NAME="PPID Food Station"

PPID_SITUS_URL=https://ppid.foodstation.co.id
PPID_PANEL_URL=https://adm-ppid.foodstation.co.id
PPID_KONTAK_EMAIL=ppid@foodstation.co.id
PPID_BERITAHU_EMAIL_ASING=true
PPID_CAPTCHA_AKTIF=true
```

`PPID_SITUS_URL` dan `PPID_PANEL_URL` dipakai menyusun tautan di email notifikasi. Kalau
masih menunjuk `192.168.10.250`, email yang diterima pemohon berisi tautan yang tidak bisa
dibuka dari luar.

### 5.2 `/var/www/ppid/fe/.env`

```ini
APP_NAME=PPID
APP_ENV=production
APP_KEY=            # isi hasil: php artisan key:generate --show (BERBEDA dari api)
APP_DEBUG=false
APP_URL=https://ppid.foodstation.co.id
APP_TIMEZONE=Asia/Jakarta

LOG_CHANNEL=stack
LOG_LEVEL=warning

DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=ppiddb
DB_USERNAME=ppid_app
DB_PASSWORD=SANDI_DARI_LANGKAH_3
DB_SSLMODE=prefer

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_SECURE_COOKIE=true
SESSION_DOMAIN=ppid.foodstation.co.id

# HARUS sama persis dengan nilai di api-ppid
DOKUMEN_TERBATAS_ROOT=/var/www/ppid/shared/dokumen-terbatas

MAIL_MAILER=smtp
MAIL_HOST=srv179.niagahoster.com
MAIL_PORT=465
MAIL_USERNAME=noreply-ppid@foodstation.co.id
MAIL_PASSWORD=SANDI_EMAIL
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="noreply-ppid@foodstation.co.id"
MAIL_FROM_NAME="PPID Food Station"

PPID_INSTANSI="PT Food Station Tjipinang Jaya (Perseroda)"
PPID_KONTAK_EMAIL=ppid@foodstation.co.id
PPID_SITUS_URL=https://ppid.foodstation.co.id
PPID_BAHASA_EMAIL=id
PPID_ZONA_WAKTU=Asia/Jakarta
PPID_WAJIB_VERIFIKASI_EMAIL=true
PPID_CAPTCHA_AKTIF=true
PPID_PERISAI_FORMULIR=true
```

`SESSION_SECURE_COOKIE=true` hanya berfungsi kalau `TrustProxies` sudah diperbaiki
(Langkah 1.3). Kalau tidak, Laravel mengira request masih HTTP, cookie sesi tidak pernah
terkirim, dan **pemohon tidak bisa login**.

### 5.3 `be-ppid` — variabel build

Panel admin adalah SPA statis: variabelnya dibaca **saat build**, bukan saat runtime. Set
sebagai variabel CI (Langkah 10.3), atau tulis di `.env.production` pada folder sumber:

```ini
VITE_API_BASE_URL=https://api-ppid.foodstation.co.id
VITE_MEDIA_BASE_URL=https://ppid.foodstation.co.id/storage
VITE_MAP_KEY=KUNCI_BARU_HASIL_ROTASI
```

Variabel Firebase/AWS Cognito di `.env` sekarang masih berisi nilai contoh
(`your-dev-app...`). Kosongkan saja kalau fitur tersebut tidak dipakai — jangan bawa nilai
palsu ke produksi.

---

## 6. Nginx di 192.168.1.21

Server ini berada **di belakang** reverse proxy 192.168.1.17 yang menangani SSL, jadi
Nginx di sini cukup mendengar port 80 pada IP internal.

### 6.1 `/etc/nginx/sites-available/ppid-fe`

```nginx
server {
    listen 80;
    server_name ppid.foodstation.co.id;
    root /var/www/ppid/fe/public;

    index index.php;
    charset utf-8;
    client_max_body_size 25M;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header Referrer-Policy "strict-origin-when-cross-origin" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    # /storage adalah symlink hasil `artisan storage:link`
    location /storage/ {
        expires 7d;
        access_log off;
        try_files $uri =404;
    }

    location ~ /\.(?!well-known).* { deny all; }

    error_log  /var/log/nginx/ppid-fe.error.log;
    access_log /var/log/nginx/ppid-fe.access.log;
}
```

### 6.2 `/etc/nginx/sites-available/ppid-api`

```nginx
server {
    listen 80;
    server_name api-ppid.foodstation.co.id;
    root /var/www/ppid/api/public;

    index index.php;
    charset utf-8;
    client_max_body_size 25M;

    add_header X-Content-Type-Options "nosniff" always;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 120;
    }

    location ~ /\.(?!well-known).* { deny all; }

    error_log  /var/log/nginx/ppid-api.error.log;
    access_log /var/log/nginx/ppid-api.access.log;
}
```

Header CORS **tidak** ditulis di Nginx — sudah ditangani `api-ppid/config/cors.php` lewat
`ADMIN_ORIGINS`. Menambahkannya di Nginx membuat header ganda dan browser justru menolak.

### 6.3 `/etc/nginx/sites-available/ppid-adm`

```nginx
server {
    listen 80;
    server_name adm-ppid.foodstation.co.id;
    root /var/www/ppid/adm;

    index index.html;
    charset utf-8;

    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "SAMEORIGIN" always;

    # SPA: semua rute dikembalikan ke index.html
    location / {
        try_files $uri $uri/ /index.html;
    }

    location /assets/ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # index.html tidak boleh di-cache, supaya rilis baru langsung terpakai
    location = /index.html {
        add_header Cache-Control "no-cache, must-revalidate";
    }

    error_log  /var/log/nginx/ppid-adm.error.log;
    access_log /var/log/nginx/ppid-adm.access.log;
}
```

Aktifkan:

```bash
sudo ln -sf /etc/nginx/sites-available/ppid-fe  /etc/nginx/sites-enabled/
sudo ln -sf /etc/nginx/sites-available/ppid-api /etc/nginx/sites-enabled/
sudo ln -sf /etc/nginx/sites-available/ppid-adm /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t && sudo systemctl reload nginx
```

---

## 7. Reverse Proxy 192.168.1.17 (terminasi SSL)

Untuk **setiap** dari tiga domain, blok proxy harus meneruskan header berikut. Tanpa
`X-Forwarded-Proto`, Laravel tidak akan tahu request aslinya HTTPS.

```nginx
server {
    listen 443 ssl http2;
    server_name ppid.foodstation.co.id;   # ulangi blok ini untuk api-ppid & adm-ppid

    ssl_certificate     /etc/ssl/ppid/fullchain.pem;
    ssl_certificate_key /etc/ssl/ppid/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;

    client_max_body_size 25M;

    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    location / {
        proxy_pass http://192.168.1.21:80;
        proxy_http_version 1.1;
        proxy_set_header Host              $host;
        proxy_set_header X-Real-IP         $remote_addr;
        proxy_set_header X-Forwarded-For   $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;   # <-- wajib
        proxy_set_header X-Forwarded-Host  $host;
        proxy_set_header X-Forwarded-Port  $server_port;
        proxy_read_timeout 120s;
    }
}

server {
    listen 80;
    server_name ppid.foodstation.co.id;
    return 301 https://$host$request_uri;
}
```

`client_max_body_size` di reverse proxy harus **≥** nilai di Nginx app server; kalau tidak,
unggahan besar ditolak 413 sebelum sampai ke aplikasi.

Pastikan juga DNS ketiga domain menunjuk ke IP publik reverse proxy, bukan ke 192.168.1.21.

---

## 8. Migrasi, seed, dan akun awal

Semua dijalankan dari `api-ppid` (lihat Langkah 1.5).

```bash
cd /var/www/ppid/api

sudo -u www-data php artisan key:generate --force
sudo -u www-data php artisan jwt:secret --force

# tabel antrean, karena QUEUE_CONNECTION=database
sudo -u www-data php artisan queue:table
sudo -u www-data php artisan migrate --force
```

Lalu isi data awal. **Sebelum go live, periksa dulu isi `database/seeders/DatabaseSeeder.php`:**
`db:seed` polos akan ikut memuat data contoh (`PemohonDemoSeeder`, konten demo). Untuk
produksi bersih, jalankan hanya seeder yang memang wajib:

```bash
sudo -u www-data php artisan db:seed --class=ModulSistemSeeder   --force
sudo -u www-data php artisan db:seed --class=PenamaanModulSeeder --force
sudo -u www-data php artisan db:seed --class=PenggunaPpidSeeder  --force
sudo -u www-data php artisan db:seed --class=AlurApprovalSeeder  --force
sudo -u www-data php artisan db:seed --class=AlurProsedurAwalSeeder --force
```

**Ganti semua sandi bawaan** yang dibuat `PenggunaPpidSeeder` sebelum domain dibuka ke publik.

Optimasi cache produksi — jalankan di `api` **dan** `fe`, setiap selesai deploy:

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

> Setelah `config:cache`, pemanggilan `env()` di luar file `config/*.php` mengembalikan
> `null`. Jadi setiap kali `.env` berubah, **wajib** jalankan ulang `config:cache`.

---

## 9. Antrean & penjadwal

`QUEUE_CONNECTION` di repo saat ini `sync` — artinya email verifikasi, notifikasi status
permohonan (`StatusLayananMail`), dan laporan unduhan (`DownloadReportMail`) dikirim di
dalam request. Kalau SMTP Niagahoster lambat, pemohon ikut menunggu dan bisa timeout.
Karena itu Langkah 5 menyetelnya ke `database`, dan itu butuh worker.

`/etc/supervisor/conf.d/ppid-queue.conf`:

```ini
[program:ppid-api-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ppid/api/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/ppid-api-queue.log
stopwaitsecs=3600

[program:ppid-fe-queue]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/ppid/fe/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/log/ppid-fe-queue.log
stopwaitsecs=3600
```

```bash
sudo supervisorctl reread && sudo supervisorctl update && sudo supervisorctl status
```

Penjadwal Laravel (`app/Console/Kernel.php`) masih kosong di kedua aplikasi, jadi cron belum
wajib hari ini. Tetap pasang sekarang supaya tugas terjadwal di kemudian hari langsung jalan —
`sudo crontab -u www-data -e`:

```cron
* * * * * php /var/www/ppid/api/artisan schedule:run >> /dev/null 2>&1
* * * * * php /var/www/ppid/fe/artisan schedule:run >> /dev/null 2>&1
```

---

## 10. GitLab CI/CD

### 10.1 Pindahkan repo ke GitLab

```bash
cd /d/Project/Ppid
git remote rename origin github
git remote add origin git@gitlab.com:fs-group4/fst-ppid.git
git push -u origin main
```

Bereskan dulu Langkah 1.1 (`be-ppid/.env`) sebelum push pertama.

### 10.2 Runner

Runner SaaS GitLab.com **tidak bisa** menjangkau `192.168.1.21` (IP privat). Jadi pasang
runner sendiri di server aplikasi, executor `shell`:

```bash
curl -L "https://packages.gitlab.com/install/repositories/runner/gitlab-runner/script.deb.sh" | sudo bash
sudo apt install -y gitlab-runner

sudo gitlab-runner register \
  --url https://gitlab.com/ \
  --token GLRT_TOKEN_DARI_SETTINGS_CICD_RUNNERS \
  --executor shell \
  --description "ppid-prod-192.168.1.21" \
  --tag-list "ppid-prod"
```

Beri hak deploy terbatas ke user `gitlab-runner`:

```bash
sudo usermod -aG www-data gitlab-runner

sudo tee /etc/sudoers.d/gitlab-runner >/dev/null <<'SUDOERS'
gitlab-runner ALL=(www-data) NOPASSWD: /usr/bin/php, /usr/local/bin/composer
gitlab-runner ALL=(root) NOPASSWD: /usr/bin/supervisorctl, /bin/chown
SUDOERS

sudo chmod 440 /etc/sudoers.d/gitlab-runner
sudo visudo -c
```

### 10.3 Variabel CI/CD

Settings → CI/CD → Variables (semua **Protected** + **Masked**):

| Variabel | Nilai |
|---|---|
| `VITE_API_BASE_URL` | `https://api-ppid.foodstation.co.id` |
| `VITE_MEDIA_BASE_URL` | `https://ppid.foodstation.co.id/storage` |
| `VITE_MAP_KEY` | kunci Maps hasil rotasi |

`.env` Laravel **tidak** disimpan di CI. Biarkan tetap di server dan tidak pernah ditimpa
pipeline — itulah sebabnya skrip di bawah meng-exclude `.env`.

### 10.4 `.gitlab-ci.yml` (taruh di root repo)

```yaml
stages: [build, deploy]

variables:
  DEPLOY_ROOT: /var/www/ppid

# ---------- BUILD ----------

build:adm:
  stage: build
  tags: [ppid-prod]
  script:
    - cd be-ppid
    - npm ci
    - npm run build
  artifacts:
    paths: [be-ppid/build/]
    expire_in: 1 week
  rules:
    - if: $CI_COMMIT_BRANCH == "main"

build:fe-assets:
  stage: build
  tags: [ppid-prod]
  script:
    - cd fe-ppid
    - npm ci
    - npm run build
  artifacts:
    paths: [fe-ppid/public/build/]
    expire_in: 1 week
  rules:
    - if: $CI_COMMIT_BRANCH == "main"

# ---------- DEPLOY ----------

deploy:api:
  stage: deploy
  tags: [ppid-prod]
  needs: []
  script:
    - rsync -a --delete
        --exclude='.env' --exclude='storage/' --exclude='vendor/'
        api-ppid/ $DEPLOY_ROOT/api/
    - cd $DEPLOY_ROOT/api
    - sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction
    - sudo -u www-data php artisan migrate --force
    - sudo -u www-data php artisan config:cache
    - sudo -u www-data php artisan route:cache
    - sudo -u www-data php artisan view:cache
    - sudo supervisorctl restart ppid-api-queue:*
  environment:
    name: production
    url: https://api-ppid.foodstation.co.id
  rules:
    - if: $CI_COMMIT_BRANCH == "main"
      when: manual        # rilis produksi selalu dipicu manusia

deploy:fe:
  stage: deploy
  tags: [ppid-prod]
  needs: ["build:fe-assets"]
  script:
    - rsync -a --delete
        --exclude='.env' --exclude='storage/' --exclude='vendor/'
        --exclude='public/storage'
        fe-ppid/ $DEPLOY_ROOT/fe/
    - cd $DEPLOY_ROOT/fe
    - sudo -u www-data composer install --no-dev --optimize-autoloader --no-interaction
    # TIDAK ADA `artisan migrate` di sini — skema milik api-ppid saja (Langkah 1.5).
    - sudo -u www-data php artisan config:cache
    - sudo -u www-data php artisan route:cache
    - sudo -u www-data php artisan view:cache
    - test -L public/storage || sudo -u www-data php artisan storage:link
    - sudo supervisorctl restart ppid-fe-queue:*
  environment:
    name: production
    url: https://ppid.foodstation.co.id
  rules:
    - if: $CI_COMMIT_BRANCH == "main"
      when: manual

deploy:adm:
  stage: deploy
  tags: [ppid-prod]
  needs: ["build:adm"]
  script:
    - rsync -a --delete be-ppid/build/ $DEPLOY_ROOT/adm/
    - sudo chown -R www-data:www-data $DEPLOY_ROOT/adm
  environment:
    name: production
    url: https://adm-ppid.foodstation.co.id
  rules:
    - if: $CI_COMMIT_BRANCH == "main"
      when: manual
```

Catatan penting soal `rsync --delete`:

- `--exclude='.env'` dan `--exclude='storage/'` bersifat **kritis**. Tanpa keduanya,
  `--delete` akan menghapus `.env` produksi dan **seluruh berkas unggahan pemohon**.
  Periksa dua baris itu setiap kali menyunting pipeline.
- Deploy pertama sebaiknya dilakukan manual (Langkah 4–8). Pipeline dipakai untuk rilis
  berikutnya.

---

## 11. Uji sebelum diumumkan (smoke test)

Jalankan dari luar jaringan kantor.

```bash
# 1. API hidup
curl -i https://api-ppid.foodstation.co.id/api/v1/health

# 2. HTTPS terdeteksi Laravel (tidak boleh ada redirect balik ke http://)
curl -sI https://ppid.foodstation.co.id | head -5

# 3. CORS preflight dari panel admin — harus 204 + Access-Control-Allow-Origin
curl -i -X OPTIONS https://api-ppid.foodstation.co.id/api/v1/auth/sign-in \
  -H "Origin: https://adm-ppid.foodstation.co.id" \
  -H "Access-Control-Request-Method: POST" \
  -H "Access-Control-Request-Headers: content-type"

# 4. Origin asing HARUS ditolak (tidak boleh muncul Access-Control-Allow-Origin)
curl -i -X OPTIONS https://api-ppid.foodstation.co.id/api/v1/auth/sign-in \
  -H "Origin: https://situs-asing.example" \
  -H "Access-Control-Request-Method: POST"
```

Uji manual yang harus lulus semua:

- [ ] Beranda `ppid.foodstation.co.id` tampil; banner dan gambar muncul (bukan 404)
- [ ] Login panel `adm-ppid.foodstation.co.id` berhasil, menu navigasi terisi
- [ ] Unggah berkas dari panel (CMS) → berkasnya **tampil di situs publik**
      (membuktikan `MEDIA_ROOT` + `storage:link` benar)
- [ ] Registrasi pemohon → email verifikasi masuk, tautannya `https://` dan bisa diklik
- [ ] Ajukan permohonan informasi → muncul di panel admin
- [ ] Ubah status permohonan dari panel → email notifikasi sampai ke pemohon
- [ ] Unduh dokumen **unduhan terbatas** setelah disetujui → berhasil
      (membuktikan `DOKUMEN_TERBATAS_ROOT` sama di kedua aplikasi — Langkah 1.4)
- [ ] Akses langsung URL berkas terbatas tanpa login → ditolak
- [ ] Ajukan keberatan → alur approval berjalan
- [ ] Captcha muncul di formulir publik
- [ ] Salah sandi 5x → akun terkunci sesuai `PPID_BATAS_PERCOBAAN_LOGIN`
- [ ] `APP_DEBUG=false`: buka URL ngawur → halaman 404 biasa, **bukan** stack trace Laravel
- [ ] Ganti bahasa ke Inggris → konten terjemahan tampil (`TerjemahanInggrisSeeder`)

---

## 12. Pengamanan & cadangan

```bash
# Firewall: hanya SSH dari LAN + HTTP dari reverse proxy
sudo ufw allow from 192.168.1.0/24 to any port 22 proto tcp
sudo ufw allow from 192.168.1.17 to any port 80 proto tcp
sudo ufw enable

# PostgreSQL tidak boleh terjangkau dari LAN — pastikan hanya localhost
sudo ss -lntp | grep 5432
```

Backup harian database + media, simpan 14 hari — `/usr/local/bin/ppid-backup.sh`:

```bash
#!/usr/bin/env bash
set -euo pipefail
TANGGAL=$(date +%F)
TUJUAN=/var/backups/ppid
mkdir -p "$TUJUAN"

PGPASSWORD='SANDI' pg_dump -h 127.0.0.1 -U ppid_app -Fc ppiddb \
  > "$TUJUAN/ppiddb-$TANGGAL.dump"

tar czf "$TUJUAN/media-$TANGGAL.tar.gz" \
  -C /var/www/ppid/fe/storage/app public \
  -C /var/www/ppid/shared dokumen-terbatas

find "$TUJUAN" -type f -mtime +14 -delete
```

```bash
sudo chmod 700 /usr/local/bin/ppid-backup.sh
# crontab root — 02:00 setiap hari
echo '0 2 * * * /usr/local/bin/ppid-backup.sh' | sudo crontab -
```

> Backup belum bernilai sampai pernah **dipulihkan**. Uji `pg_restore` ke database
> percobaan minimal sekali sebelum go live.

Lainnya:

- Set `LOG_CHANNEL=daily` bila `storage/logs/laravel.log` cepat membesar.
- Salin `.env` produksi ke password manager tim, jangan hanya ada di server.
- Aktifkan `fail2ban` untuk SSH.

---

## 13. Ringkasan urutan eksekusi

Legenda: ☑ selesai · ◐ sebagian · ☐ belum

| # | Langkah | Ref | Status |
|---|---|---|---|
| 1 | Rotasi kunci Maps/Firebase, hapus `be-ppid/.env` dari Git | 1.1 | ◐ |
| 2 | Perbaiki `TrustProxies` di `api-ppid` **dan** `fe-ppid` | 1.3 | ☑ |
| 3 | Hapus 4 migration bawaan di `fe-ppid` | 1.5 | ☑ |
| 4 | Push repo ke GitLab, tambah `.gitlab-ci.yml` | 10.1, 10.4 | ◐ |
| 5 | Pasang PHP 8.2 + ekstensi, Node 20, Composer, Supervisor | 2 | ☐ |
| 6 | Buat role `ppid_app` + database `ppiddb` | 3 | ☐ |
| 7 | Siapkan `/var/www/ppid`, hak akses, `storage:link` di `fe` | 4 | ☐ |
| 8 | Tulis `.env` produksi — termasuk `DOKUMEN_TERBATAS_ROOT` di **kedua** app | 5, 1.4 | ◐ |
| 9 | Pasang 3 server block Nginx | 6 | ☐ |
| 10 | Setel reverse proxy 192.168.1.17 + `X-Forwarded-Proto` | 7 | ☐ |
| 11 | `key:generate`, `jwt:secret`, `migrate`, seed terpilih | 8 | ☐ |
| 12 | Ganti semua sandi admin bawaan | 8 | ☐ |
| 13 | Supervisor queue worker + cron scheduler | 9 | ☐ |
| 14 | Daftarkan GitLab Runner di server | 10.2 | ☐ |
| 15 | Jalankan seluruh smoke test | 11 | ☐ |
| 16 | Firewall, backup harian, uji restore | 12 | ☐ |
| 17 | Arahkan DNS publik ke reverse proxy → **GO LIVE** | 7 | ☐ |

Tiga blocker yang paling sering terlewat dan langsung terasa di produksi:
**`DOKUMEN_TERBATAS_ROOT` (1.4)**, **`TrustProxies` (1.3)**, dan **`VITE_API_BASE_URL` (1.2)**.

---

## 14. Yang sudah dikerjakan di repo

Perubahan berikut sudah diterapkan pada working tree (**belum di-commit**).

| Berkas | Perubahan | Ref |
|---|---|---|
| `api-ppid/app/Http/Middleware/TrustProxies.php` | `$proxies = ['192.168.1.17', '127.0.0.1']` | 1.3 |
| `fe-ppid/app/Http/Middleware/TrustProxies.php` | idem | 1.3 |
| `be-ppid/.gitignore` | tambah `.env` dan `.env.production` | 1.1 |
| `be-ppid/.env` | `git rm --cached` — tidak lagi dilacak Git (berkasnya tetap di disk) | 1.1 |
| `be-ppid/.env.example` | baru; berisi panduan nilai produksi, tanpa rahasia | 1.1, 1.2 |
| `fe-ppid/database/migrations/*` | 4 migration bawaan Laravel dihapus; diganti `README.md` + `.gitkeep` | 1.5 |
| `api-ppid/.env`, `fe-ppid/.env` | tambah `DOKUMEN_TERBATAS_ROOT` menunjuk satu folder yang sama | 1.4 |
| `api-ppid/.env.example` | tambah `MEDIA_ROOT`, `MEDIA_URL`, `DOKUMEN_TERBATAS_ROOT` | 1.4 |
| `fe-ppid/.env.example` | tambah `DOKUMEN_TERBATAS_ROOT` | 1.4 |
| `fe-ppid/DEPLOY.md` | ditandai USANG, diarahkan ke dokumen ini | 1.6 |
| `.gitlab-ci.yml` | baru; 2 job build + 3 job deploy manual | 10.4 |

Verifikasi yang sudah dijalankan:

```
$ php -l api-ppid/app/Http/Middleware/TrustProxies.php
No syntax errors detected in api-ppid/app/Http/Middleware/TrustProxies.php
$ php -l fe-ppid/app/Http/Middleware/TrustProxies.php
No syntax errors detected in fe-ppid/app/Http/Middleware/TrustProxies.php
$ git ls-files | grep '\.env$'
(tidak ada)
```

`.gitlab-ci.yml` sudah lolos parse YAML; 5 job terbaca:
`build:adm`, `build:fe-assets`, `deploy:api`, `deploy:fe`, `deploy:adm`.

### Efek samping yang perlu diketahui

- `DOKUMEN_TERBATAS_ROOT` di `.env` lokal sekaligus memperbaiki bug yang sama di
  lingkungan pengembangan: sebelumnya `api-ppid` dan `fe-ppid` menunjuk folder
  berbeda, sehingga unduhan dokumen terbatas juga gagal saat dites di lokal.
  Folder `fe-ppid/storage/app/dokumen-terbatas` sudah dibuat.
- `VITE_API_BASE_URL` di `be-ppid/.env` **sengaja tidak diubah**. Nilai
  `http://localhost:3000` memang benar untuk pengembangan — `vite.config.mts`
  mem-proxy `/api/v1` ke `127.0.0.1:8001`. Nilai produksi diisi lewat variabel
  CI/CD (bagian 10.3), bukan lewat berkas ini.

### Yang tersisa dan tidak bisa dikerjakan dari repo

| Langkah | Kenapa |
|---|---|
| Rotasi `VITE_MAP_KEY` + kunci Firebase | butuh akses Google Cloud Console / Firebase Console |
| Bersihkan `.env` dari riwayat Git | keputusan tim; sebaiknya sebelum push pertama ke GitLab |
| `git remote add origin` + push GitLab | butuh kredensial SSH GitLab |
| Seluruh bagian 2, 3, 4, 6, 7, 9, 10.2, 11, 12 | butuh akses SSH ke 192.168.1.21 dan 192.168.1.17 |
| `.env` produksi (bagian 5) | berisi sandi DB, JWT, dan SMTP — ditulis langsung di server |
