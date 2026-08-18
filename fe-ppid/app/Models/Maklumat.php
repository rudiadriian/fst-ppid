<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Maklumat Pelayanan Informasi Publik.
 *
 * Isi maklumat tidak ditulis di template: yang tayang adalah berkas
 * (PDF/gambar) yang diunggah petugas lewat modul Maklumat di be-ppid.
 */
class Maklumat extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'maklumat';

    protected $casts = [
        'tanggal_terbit' => 'date',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /** Petugas yang menerbitkan maklumat; namanya tampil di situs publik. */
    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(PenggunaPanel::class, 'published_by');
    }
}
