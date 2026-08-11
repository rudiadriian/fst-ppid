<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Penilaian pemohon atas layanan informasi.
 *
 * Rata-rata `rating` menjadi angka "Kepuasan" pada halaman Laporan Statistik
 * Informasi Publik. Baris hanya boleh dibuat oleh pemilik permohonannya
 * (lihat SurveiController) atau petugas lewat modul CMS di be-ppid.
 */
class SurveyKepuasan extends Model
{
    protected $table = 'survey_kepuasan';

    public const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_id',
        'rating',
        'komentar',
    ];

    protected $casts = [
        'rating' => 'integer',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }
}
