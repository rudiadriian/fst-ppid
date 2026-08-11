<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Petugas panel admin (tabel `users` milik be-ppid).
 *
 * Situs publik tidak pernah memakai tabel ini untuk masuk — akun pengunjung
 * memakai model `Pemohon`. Model ini hanya untuk membaca nama petugas, mis.
 * "Diunggah oleh" pada halaman Regulasi. Karena itu isinya sengaja minim dan
 * tidak mewarisi Authenticatable.
 */
class PenggunaPanel extends Model
{
    use SoftDeletes;

    protected $table = 'users';

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
