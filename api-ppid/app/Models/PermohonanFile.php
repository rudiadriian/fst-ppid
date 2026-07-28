<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanFile extends Model
{
    protected $table = 'permohonan_files';

    public const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_id',
        'nama_file',
        'path_file',
        'tipe_file',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }
}
