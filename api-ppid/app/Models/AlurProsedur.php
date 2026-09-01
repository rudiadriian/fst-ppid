<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Satu gambar alur pada halaman Standar Layanan.
 *
 * Isinya infografis resmi; `judul` dan `keterangan` hanya pengiring gambar,
 * bukan salinan isinya.
 */
class AlurProsedur extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'alur_prosedur';

    protected $fillable = [
        'halaman',
        'judul',
        'judul_en',
        'keterangan',
        'keterangan_en',
        'gambar',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];
}
