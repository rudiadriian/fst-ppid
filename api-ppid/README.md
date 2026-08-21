# api-ppid

API backend PPID PT Food Station Tjipinang Jaya (Perseroda).

Melayani panel admin `be-ppid` (Fuse React). Situs publik `fe-ppid` (Laravel/Blade)
membaca `ppiddb` secara langsung, jadi kalau API ini mati, halaman publik tetap bisa diakses.

## Stack

- Laravel 10, PHP 8.1 (lihat catatan Keamanan di bawah)
- PostgreSQL 18, database `ppiddb` (skema dibuat lewat DDL di `start_project.md`, bukan lewat migration)
- Autentikasi JWT (`tymon/jwt-auth`) di atas tabel `users` + `roles` + `role_modul_akses`

## Menjalankan

```bash
cp .env.example .env       # isi DB_PASSWORD dan ADMIN_ORIGINS
php artisan key:generate
php artisan jwt:secret
php artisan migrate        # hanya kolom tambahan; tabel inti sudah ada dari DDL
php artisan db:seed        # modul sistem + role default
php artisan ppid:set-password admin@foodstation.co.id
composer serve             # server dev; 8001 kecuali disebut lain
```

### Kenapa `composer serve`, bukan `php artisan serve`

`php artisan serve` polos membayar ~340 ms per permintaan hanya untuk membaca
ulang dan mengompilasi seluruh berkas Laravel, sebelum satu baris kode aplikasi
jalan. `composer serve` menjalankan `dev/serve.php`, yang menyalakan OPcache
(lewat `PHP_INI_SCAN_DIR` — php.ini XAMPP tidak disentuh) lalu membangun cache
konfigurasi dan rute, dan membuangnya lagi saat server berhenti.

Terukur di mesin pengembangan Windows/XAMPP, permintaan yang sudah panas:

| Endpoint | `artisan serve` | `composer serve` |
|---|---|---|
| `GET /api/v1/health` | 340 ms | 37 ms |
| `GET /api/v1/me/navigation` | 1.104 ms | 31 ms |
| `GET /api/v1/permohonan` (15 baris) | 1.101 ms | 35 ms |
| `GET /api/v1/pemohon` (15 baris) | 1.076 ms | 33 ms |
| `GET /api/v1/dashboard/ringkasan` | 1.250 ms | 34 ms |
| `GET /api/v1/dashboard/analitik` | 1.186 ms | 52 ms |
| `GET /api/v1/notifikasi` | 1.464 ms | 28 ms |
| 5 permintaan bersamaan | 4.203 ms | 563 ms |

Sisa selisihnya datang dari `DB_PERSISTENT` (lihat `.env.example`): PostgreSQL
melahirkan satu proses backend per sambungan, dan di Windows itu ~120 ms yang
dibayar ulang setiap permintaan.

Ubah `.env`, `config/*.php`, atau `routes/*.php`? Hentikan server lalu jalankan
lagi — cache dibangun ulang tiap kali `composer serve` dipanggil.

Panel admin memanggil `/api/v1/*`. Saat development, `be-ppid` mem-proxy path itu
ke `http://127.0.0.1:8001` (lihat `server.proxy` di `be-ppid/vite.config.mts`),
jadi tidak ada request lintas origin dari browser.

## Endpoint

| Method | Path | Auth | Keterangan |
|---|---|---|---|
| GET | `/api/v1/health` | - | Cek API hidup |
| POST | `/api/v1/auth/sign-in` | - | Login. Wajib captcha; kunci bertingkat sampai suspend |
| GET | `/api/v1/auth/captcha` | - | Kode captcha baru: `{ id, gambar }` (data URI PNG) |
| POST | `/api/v1/auth/lupa-password` | - | Kirim tautan atur ulang password ke email terdaftar |
| POST | `/api/v1/auth/reset-password` | - | Pasang password baru memakai token dari email |
| GET | `/api/v1/auth/sign-in-with-token` | Bearer | Auto-login, token baru dikirim di header `New-Access-Token` |
| POST | `/api/v1/auth/refresh` | Bearer | Perpanjang token |
| POST | `/api/v1/auth/sign-out` | Bearer | Logout, token masuk blacklist |
| PUT | `/api/v1/auth/user/{id}` | Bearer | Update preferensi user yang sedang login |

## Hak akses

Middleware `akses` membaca tabel `role_modul_akses` pada tiap request, bukan dari
klaim di dalam token — jadi pencabutan hak langsung berlaku tanpa menunggu token lama habis.

```php
Route::get('informasi-publik', [InformasiPublikController::class, 'index'])
    ->middleware(['auth:api', 'akses:informasi-publik,view']);
```

Aksi yang tersedia: `view`, `create`, `edit`, `delete`, `approve`, `export`.
Role `super-admin` melewati pengecekan ini.

## Keamanan

### Pengaman fitur auth

**Captcha.** Gambar GD buatan sendiri, tanpa layanan pihak ketiga — panel tidak boleh
bergantung pada jaringan luar saat orang mencoba masuk. Karena panel adalah SPA tanpa
sesi, kodenya tidak dititipkan di session: tiap kode punya `id` sendiri dan yang disimpan
server hanyalah hash-nya di cache. Sekali diperiksa langsung dibuang, benar atau salah.
Dipasang di tiga titik: masuk, minta tautan lupa password, dan pasang password baru.
Sakelarnya `PPID_CAPTCHA_AKTIF`.

**Kunci bertingkat.** Tiap 3 kegagalan menaikkan satu tahap:

| Kegagalan | Akibat |
|---|---|
| 3 | Tunggu 1 jam |
| 6 | Tunggu 1 hari |
| 9 | Tunggu 14 hari |
| 12 | **Akun disuspend** — hanya administrator yang membukanya |

Berlaku sama untuk percobaan masuk (`percobaan_login_admin`) dan permintaan tautan lupa
password (`percobaan_tautan_admin`), dengan hitungan **terpisah**: orang yang benar-benar
lupa password tidak boleh ikut terkunci dari mencoba masuk hanya karena meminta tautan
beberapa kali. Tangganya sendiri ada di `App\Support\KunciBertingkat` dan diuji di
`tests/Unit/KunciBertingkatTest.php`.

Hitungannya disimpan di basis data, bukan cache: masa kuncinya bisa berminggu-minggu, dan
cache berkas bisa terhapus tanpa sengaja. Kuncinya dipasang per **email + IP**, bukan per
email saja — kalau per email, siapa pun yang tahu alamat seorang petugas bisa menutup
akunnya hanya dengan sengaja salah password. Suspend pada tahap keempat adalah
pengecualiannya, dan memang harus.

Suspend (`users.disuspend_pada`) sengaja dibedakan dari penonaktifan administratif
(`users.is_active`): yang satu hasil pengamanan otomatis, yang satu keputusan orang.

**Lupa password.** Tautan berumur `PPID_UMUR_TAUTAN_MENIT` (bawaan 60 menit), sekali
pakai, dan hanya dikirim ke akun yang terdaftar, aktif, dan tidak disuspend. Setelah
password berhasil diganti, kunci yang sedang berlaku ikut dibersihkan; kalau tidak, orang
yang baru saja membuktikan dirinya pemilik email itu tetap tidak bisa masuk.

Email yang **bukan** akun panel ditolak dengan alasannya (`PPID_BERITAHU_EMAIL_ASING`,
bawaan `true`), dibedakan menjadi tiga: tidak terdaftar, nonaktif, dan disuspend —
karena tindak lanjutnya berbeda.

> **Ini pertukaran.** Jawaban yang membedakan "terdaftar" dan "tidak" membuat endpoint ini
> bisa dipakai memastikan alamat mana yang punya akun panel. Dinyalakan karena panel ini
> bukan layanan terbuka — origin dibatasi `ADMIN_ORIGINS`, akunnya segelintir, alamatnya
> institusional — sementara petugas yang salah ketik alamatnya sendiri sebelumnya disuruh
> memeriksa folder Spam lalu menunggu email yang tidak akan pernah datang.
> Remnya tetap terpasang: permintaan untuk email asing pun ikut dihitung tangga
> bertingkat, jadi penyisiran alamat berhenti sendiri pada percobaan ketiga.
> **Isi `false` bila panel ini suatu saat dapat dijangkau dari internet terbuka.**

### Lain-lain

- Pesan login gagal tidak membedakan email salah vs password salah (anti enumerasi akun).
- Email akun disimpan huruf kecil (mutator di `User`); pencarian jalur auth memakai
  `User::denganEmail()` yang tidak membedakan huruf besar-kecil.
- Semua percobaan login (berhasil/gagal) dicatat ke `audit_log`.
- CORS dibatasi ke origin di `ADMIN_ORIGINS`. Jangan diisi `*`.
- **Wajib sebelum production:** PHP 8.1 dan Laravel 10 sudah habis masa dukungan keamanan.
  Naikkan PHP ke 8.3/8.4 lalu `composer require laravel/framework:^12`, karena beberapa
  advisory (mis. CVE-2026-48019) hanya ditambal di Laravel >= 12.60.
- Set `APP_DEBUG=false` di production.
