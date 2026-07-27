# Deploy fe-ppid ke Vercel

Frontend publik PPID (Laravel 10 + Blade + Vite) dijalankan di Vercel lewat runtime
`vercel-php`. Repo ini berisi dua aplikasi (`fe-ppid`, `be-ppid`), jadi setiap project
Vercel harus menunjuk ke salah satu subfolder.

## Setting project Vercel (sekali saja)

Settings → General:

| Setting | Nilai |
|---|---|
| Root Directory | `fe-ppid` |
| Framework Preset | `Other` |
| Build Command | kosong (ikut `vercel.json`) |
| Install Command | kosong (ikut `vercel.json`) |
| Output Directory | kosong |

`fe-ppid/vercel.json` sudah menentukan `installCommand: npm install` dan
`buildCommand: npm run build`. Selama Root Directory masih menunjuk root repo,
file itu tidak terbaca dan build gagal dengan `vite: command not found` (exit 127)
karena di root repo tidak ada `package.json` untuk di-install.

## Environment Variables (Settings → Environment Variables)

Wajib:

- `APP_KEY` — ambil dari `php artisan key:generate --show` (format `base64:...`).
  Tanpa ini Laravel balas 500 `No application encryption key has been specified.`

Bila halaman dinamis harus membaca database produksi:

- `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

Postgres di `127.0.0.1` hanya untuk lokal — dari Vercel tidak akan terjangkau.
Kalau DB belum tersedia, situs tetap dapat diakses: query dibungkus
`PpidController::fromDatabase()` sehingga halaman jatuh ke data cadangan dan
menampilkan banner peringatan, bukan error 500.

Nilai lain (`APP_ENV`, `APP_DEBUG=false`, cache path `/tmp`, `SESSION_DRIVER=cookie`,
`LOG_CHANNEL=stderr`) sudah diset di `vercel.json`.

## Catatan build

- `public/build` masuk `.gitignore`, jadi aset Vite dihasilkan saat build di Vercel.
  Setelah mengubah class Tailwind, jalankan `npm run build` juga di lokal agar
  tampilan lokal ikut terbarui.
- `vendor/` juga tidak di-commit; `composer install` dijalankan otomatis oleh
  runtime `vercel-php` selama `composer.json` berada di Root Directory.

## Seed data contoh

```bash
php artisan db:seed --class=PpidDemoSeeder
```

Mengisi contoh data untuk Informasi Dikecualikan, Laporan Layanan, Register
Permohonan, dan Regulasi/Dasar Hukum. Aman dijalankan berulang.
