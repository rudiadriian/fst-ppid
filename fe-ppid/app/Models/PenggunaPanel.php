<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Petugas panel admin (tabel `users` milik be-ppid).
 *
 * Situs publik tidak pernah memakai tabel ini untuk masuk — akun pengunjung
 * memakai model `Pemohon`. Model ini hanya untuk menyebut siapa yang mengunggah
 * sebuah dokumen, mis. "Diunggah oleh" pada halaman Regulasi. Karena itu isinya
 * sengaja minim dan tidak mewarisi Authenticatable.
 */
class PenggunaPanel extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /** Jabatan petugas; inilah yang disebut di situs publik, bukan namanya. */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RolePanel::class, 'role_id');
    }

    /**
     * Sebutan pengunggah dokumen di situs publik.
     *
     * Yang tampil adalah **nama role**, bukan nama petugas. Nama orang tidak
     * menambah apa pun bagi pengunjung — yang dijawabnya adalah "pejabat mana
     * yang menerbitkan dokumen ini" — sementara menampilkannya menyebar nama
     * pegawai ke halaman publik yang terindeks mesin pencari.
     *
     * Role yang kosong (akun tanpa role, atau barisnya sudah dihapus) jatuh ke
     * sebutan umum, sama seperti dokumen yang tidak punya pengunggah tercatat.
     */
    public function labelPublik(): string
    {
        return filled($this->role?->name) ? (string) $this->role->name : __('Petugas PPID');
    }
}
