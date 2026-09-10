<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Role petugas panel (tabel `roles` milik be-ppid).
 *
 * Situs publik hanya membacanya untuk satu keperluan: menyebut **jabatan**
 * pengunggah dokumen, bukan nama orangnya. Karena itu isinya sengaja minim,
 * sama seperti {@see PenggunaPanel}.
 */
class RolePanel extends Model
{
    use SoftDeletes;

    protected $table = 'roles';
}
