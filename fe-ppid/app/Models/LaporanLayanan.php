<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaporanLayanan extends Model
{
    use SoftDeletes;

    protected $table = 'laporan_layanan';

    protected $casts = [
        'tahun'                  => 'integer',
        'jumlah_permohonan_masuk' => 'integer',
        'jumlah_dikabulkan'       => 'integer',
        'jumlah_ditolak'          => 'integer',
        'jumlah_ditolak_sebagian' => 'integer',
        'jumlah_keberatan'        => 'integer',
        'rata_rata_hari_respon'   => 'float',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe_laporan', $tipe);
    }

    /**
     * Petugas yang menerbitkan laporan; namanya tampil di situs publik sebagai
     * pengunggah, sejajar dengan kolom `uploaded_by` pada modul Regulasi.
     */
    public function penerbit(): BelongsTo
    {
        return $this->belongsTo(PenggunaPanel::class, 'published_by');
    }
}
