<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanLayanan extends Model
{
    protected $table = 'laporan_layanan';

    protected $fillable = [
        'tipe_laporan',
        'judul',
        'tahun',
        'periode',
        'jumlah_permohonan_masuk',
        'jumlah_dikabulkan',
        'jumlah_ditolak',
        'jumlah_ditolak_sebagian',
        'jumlah_keberatan',
        'rata_rata_hari_respon',
        'ringkasan',
        'file_laporan',
        'status',
        'published_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'jumlah_permohonan_masuk' => 'integer',
        'jumlah_dikabulkan' => 'integer',
        'jumlah_ditolak' => 'integer',
        'jumlah_ditolak_sebagian' => 'integer',
        'jumlah_keberatan' => 'integer',
        'rata_rata_hari_respon' => 'float',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
