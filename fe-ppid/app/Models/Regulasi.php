<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulasi extends Model
{
    protected $table = 'regulasi';

    /** Tabel regulasi hanya punya created_at. */
    const UPDATED_AT = null;

    protected $casts = [
        'tahun'           => 'integer',
        'tanggal_berlaku' => 'date',
    ];

    /** kategori: dasar_hukum_ppid (kanal Profil) | regulasi | pedoman (kanal Layanan) */
    public function scopeKategori($query, $kategori)
    {
        return $query->whereIn('kategori', (array) $kategori);
    }
}
