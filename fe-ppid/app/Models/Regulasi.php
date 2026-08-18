<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regulasi extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'regulasi';

    protected $casts = [
        'tahun'           => 'integer',
        'tanggal_berlaku' => 'date',
    ];

    /** kategori: dasar_hukum_ppid (kanal Profil) | regulasi | pedoman (kanal Layanan) */
    public function scopeKategori($query, $kategori)
    {
        return $query->whereIn('kategori', (array) $kategori);
    }

    /**
     * Petugas yang mengunggah berkasnya. Situs publik hanya butuh namanya,
     * jadi relasinya dibuat seringan mungkin — tanpa model User sendiri.
     */
    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(PenggunaPanel::class, 'uploaded_by');
    }
}
