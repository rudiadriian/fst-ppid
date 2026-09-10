<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Alamat email pendaftar belum dipakai akun lain di sistem PPID.
 *
 * Dua tabel menyimpan akun: `pemohon` (pengunjung situs) dan `users` (petugas
 * panel be-ppid). Keduanya punya indeks unik sendiri-sendiri, tetapi tidak ada
 * batasan yang menghubungkannya — tanpa aturan ini alamat petugas bisa dipakai
 * mendaftar sebagai pemohon, sehingga satu email menjawab dua jalur masuk
 * dengan dua kata sandi berbeda.
 *
 * Yang diperiksa di sini hanya dua keadaan yang tidak ditangani
 * `RegisterController` sendiri:
 *
 *  - email milik **petugas panel**, terhapus maupun tidak;
 *  - email milik baris `pemohon` yang sudah **dihapus** petugas.
 *
 * Baris `pemohon` yang masih aktif sengaja dilewatkan: pendaftaran atas email
 * itu punya jalannya sendiri — diklaim bila belum berpassword, ditolak dengan
 * pesan "silakan masuk" bila sudah.
 *
 * Baris terhapus ikut dihitung karena indeks unik PostgreSQL tidak mengenal
 * `deleted_at`: emailnya tetap menempati tempatnya. Melewatkannya berarti
 * pendaftar melihat 500 dari galat SQL, bukan pesan pada isian emailnya.
 */
class EmailBelumTerpakai implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            return; // biarkan aturan `required`/`email` yang bicara
        }

        $email = Str::lower(trim($value));

        // Query mentah: model `Pemohon` memasang global scope soft delete,
        // sedangkan yang dicari justru termasuk baris terhapus.
        if (DB::table('users')->whereRaw('lower(email) = ?', [$email])->exists()) {
            $fail(__('Email ini sudah terdaftar sebagai akun petugas PPID sehingga tidak bisa dipakai mendaftar. Gunakan alamat email lain.'));

            return;
        }

        $terhapus = DB::table('pemohon')
            ->whereRaw('lower(email) = ?', [$email])
            ->whereNotNull('deleted_at')
            ->exists();

        if ($terhapus) {
            $fail(__('Email ini pernah dipakai akun yang sudah dihapus sehingga tidak bisa didaftarkan lagi. Hubungi petugas PPID atau gunakan alamat email lain.'));
        }
    }
}
