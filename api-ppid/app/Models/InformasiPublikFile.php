<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InformasiPublikFile extends Model
{
    protected $table = 'informasi_publik_files';

    public const UPDATED_AT = null;

    protected $fillable = [
        'informasi_publik_id',
        'nama_file',
        'path_file',
        'ukuran_file',
        'tipe_file',
        'urutan',
    ];

    protected $casts = [
        'ukuran_file' => 'integer',
        'urutan' => 'integer',
    ];

    public function informasiPublik(): BelongsTo
    {
        return $this->belongsTo(InformasiPublik::class, 'informasi_publik_id');
    }
}
