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
php artisan serve --port=8001
```

Panel admin memanggil `/api/v1/*`. Saat development, `be-ppid` mem-proxy path itu
ke `http://127.0.0.1:8001` (lihat `server.proxy` di `be-ppid/vite.config.mts`),
jadi tidak ada request lintas origin dari browser.

## Endpoint

| Method | Path | Auth | Keterangan |
|---|---|---|---|
| GET | `/api/v1/health` | - | Cek API hidup |
| POST | `/api/v1/auth/sign-in` | - | Login, dibatasi 5 percobaan/menit per email+IP |
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

- Pesan login gagal tidak membedakan email salah vs password salah (anti enumerasi akun).
- Semua percobaan login (berhasil/gagal) dicatat ke `audit_log`.
- CORS dibatasi ke origin di `ADMIN_ORIGINS`. Jangan diisi `*`.
- **Wajib sebelum production:** PHP 8.1 dan Laravel 10 sudah habis masa dukungan keamanan.
  Naikkan PHP ke 8.3/8.4 lalu `composer require laravel/framework:^12`, karena beberapa
  advisory (mis. CVE-2026-48019) hanya ditambal di Laravel >= 12.60.
- Set `APP_DEBUG=false` di production.
