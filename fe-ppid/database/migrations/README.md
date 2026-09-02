# Sengaja kosong

Seluruh skema database PPID dimiliki dan dijalankan oleh **`api-ppid`**.
`fe-ppid` memakai database yang sama (`ppiddb`) tetapi hanya membaca/menulis
barisnya — tidak pernah membentuk tabelnya.

Empat migration bawaan Laravel (`create_users_table`,
`create_password_reset_tokens_table`, `create_failed_jobs_table`,
`create_personal_access_tokens_table`) dihapus dari sini karena tabel yang sama
sudah dibuat oleh `api-ppid`:

- `users` → `api-ppid/database/migrations/2026_07_20_000000_create_skema_dasar_ppid.php`
- `password_reset_tokens` → `api-ppid/database/migrations/2014_10_12_100000_create_password_reset_tokens_table.php`
- `failed_jobs` → `api-ppid/database/migrations/2019_08_19_000000_create_failed_jobs_table.php`

Menjalankan `php artisan migrate` dari folder ini akan bentrok dengan tabel
milik `api-ppid`. **Jalankan semua `migrate` dan `db:seed` dari `api-ppid`.**

Lihat `persiapan_go_live.md` bagian 1.5.
