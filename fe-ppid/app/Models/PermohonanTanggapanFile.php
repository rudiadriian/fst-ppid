<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Berkas tanggapan petugas atas satu permohonan.
 *
 * Barisnya ditulis panel admin (api-ppid); portal hanya membacanya, dan hanya
 * setelah permohonannya diserahkan — lihat
 * {@see PermohonanInformasi::tanggapanTerbukaUntukPemohon()}.
 */
class PermohonanTanggapanFile extends Model
{
    protected $table = 'permohonan_tanggapan_files';

    public const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_id',
        'nama_file',
        'path_file',
        'uploaded_by',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }
}
