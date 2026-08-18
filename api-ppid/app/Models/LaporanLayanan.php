<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Laporan Pelayanan Informasi — berkas laporan per tahun.
 *
 * Kolom angka rekap (`jumlah_*`, `rata_rata_hari_respon`) sengaja tidak lagi
 * fillable: pemakainya cuma Laporan Statistik Informasi Publik yang dihapus
 * pada langkah 68. Kolomnya sendiri masih ada di tabel dan boleh dilepas
 * lewat migrasi tersendiri bila memang tidak akan dipakai lagi.
 */
class LaporanLayanan extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'laporan_layanan';

    protected $fillable = [
        'tipe_laporan',
        'judul',
        'tahun',
        'periode',
        'ringkasan',
        'file_laporan',
        'status',
        'published_by',
    ];

    protected $casts = [
        'tahun' => 'integer',
    ];

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }
}
