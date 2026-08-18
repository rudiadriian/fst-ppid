<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catatan setiap pengiriman tautan verifikasi pendaftaran / lupa password.
 *
 * Barisnya tidak pernah diubah — hanya ditambah dan dibaca — jadi tabelnya
 * cukup punya `created_at`.
 */
class PengirimanTautanAkun extends Model
{
    protected $table = 'pengiriman_tautan_akun';

    public const UPDATED_AT = null;

    public const JENIS_REGISTRASI = 'registrasi';

    public const JENIS_LUPA_PASSWORD = 'lupa_password';

    protected $fillable = [
        'jenis',
        'email',
        'ip_address',
        'penanda_perangkat',
        'user_agent',
    ];
}
