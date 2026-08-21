<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Catatan setiap tautan akun yang benar-benar dikirim ke petugas.
 *
 * Selain jadi dasar jeda "satu tautan per sekian menit", isinya juga jejak bila
 * suatu saat perlu ditelusuri siapa yang menghabiskan kuota kirim email.
 */
class PengirimanTautanAdmin extends Model
{
    public const JENIS_LUPA_PASSWORD = 'lupa_password';

    protected $table = 'pengiriman_tautan_admin';

    public $timestamps = false;

    protected $fillable = [
        'jenis',
        'email',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];
}
