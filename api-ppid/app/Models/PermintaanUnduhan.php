<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermintaanUnduhan extends Model
{
    protected $table = 'permintaan_unduhan';

    public const UPDATED_AT = null;

    protected $fillable = [
        'informasi_publik_file_id',
        'nama',
        'email',
        'telepon',
        'token_expired_at',
    ];

    /**
     * Token unduhan adalah kredensial akses berkas — tidak pernah ikut
     * response daftar di panel admin.
     */
    protected $hidden = ['token_unduhan'];

    protected $casts = [
        'token_expired_at' => 'datetime',
        'downloaded_at' => 'datetime',
    ];

    public function file(): BelongsTo
    {
        return $this->belongsTo(InformasiPublikFile::class, 'informasi_publik_file_id');
    }
}
