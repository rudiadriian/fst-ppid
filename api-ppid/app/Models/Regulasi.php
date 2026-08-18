<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Regulasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'regulasi';

    protected $fillable = [
        'kategori',
        'judul',
        'judul_en',
        'ringkasan',
        'ringkasan_en',
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
