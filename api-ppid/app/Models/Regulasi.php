<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Regulasi extends Model
{
    protected $table = 'regulasi';

    public const UPDATED_AT = null;

    protected $fillable = [
        'kategori',
        'judul',
        'ringkasan',
        'nomor_peraturan',
        'jenis_peraturan',
        'tahun',
        'file_path',
        'uploaded_by',
        'tanggal_berlaku',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal_berlaku' => 'date',
    ];

    /** Petugas yang mengunggah berkasnya; ditampilkan di situs publik. */
    public function pengunggah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
