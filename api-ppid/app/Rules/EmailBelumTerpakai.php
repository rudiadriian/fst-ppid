<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Satu alamat email hanya boleh dimiliki satu akun di seluruh sistem.
 *
 * Dua tabel menyimpan akun: `users` (petugas panel) dan `pemohon` (pengunjung
 * situs). Masing-masing punya indeks unik sendiri, tetapi tidak ada satu pun
 * batasan yang menghubungkan keduanya — tanpa aturan ini alamat petugas bisa
 * dipakai mendaftar sebagai pemohon dan sebaliknya, sehingga satu email
 * menjawab dua jalur masuk dengan dua kata sandi berbeda.
 *
 * Baris terhapus (soft delete) ikut dihitung. Alasannya bukan kehati-hatian
 * belaka: indeks unik di PostgreSQL tidak mengenal `deleted_at`, jadi email
 * milik baris terhapus tetap menempati tempatnya. Melewatkannya berarti
 * validasi meloloskan data yang kemudian ditolak basis data sebagai galat SQL —
 * 500 di panel, bukan pesan pada isian. Petugas yang ingin memakai ulang alamat
 * itu menghapus permanen barisnya lebih dulu (modul Pengguna → Status data:
 * Terhapus → Hapus permanen).
 */
class EmailBelumTerpakai implements ValidationRule
{
    /**
     * @param  int|null  $abaikanUserId  id baris `users` yang sedang disunting.
     * @param  int|null  $abaikanPemohonId  id baris `pemohon` yang sedang disunting.
     */
    public function __construct(
        private readonly ?int $abaikanUserId = null,
        private readonly ?int $abaikanPemohonId = null,
    ) {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || trim($value) === '') {
            return; // biarkan aturan `required`/`email` yang bicara
        }

        $email = Str::lower(trim($value));

        if ($this->terpakaiDi('users', 'id', $this->abaikanUserId, $email)) {
            $fail('Email ini sudah dipakai akun petugas panel. Gunakan alamat lain.');

            return;
        }

        if ($this->terpakaiDi('pemohon', 'id', $this->abaikanPemohonId, $email)) {
            $fail('Email ini sudah dipakai akun pemohon di situs PPID. Gunakan alamat lain.');
        }
    }

    /**
     * Query mentah, bukan Eloquent: model `User` dan `Pemohon` memasang global
     * scope soft delete, dan yang dicari di sini justru termasuk baris terhapus.
     */
    private function terpakaiDi(string $tabel, string $kunci, ?int $abaikan, string $email): bool
    {
        $query = DB::table($tabel)->whereRaw('lower(email) = ?', [$email]);

        if ($abaikan !== null) {
            $query->where($kunci, '!=', $abaikan);
        }

        return $query->exists();
    }
}
