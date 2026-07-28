<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KeberatanFile extends Model
{
    protected $table = 'keberatan_files';

    public const UPDATED_AT = null;

    protected $fillable = [
        'keberatan_id',
        'nama_file',
        'path_file',
    ];

    public function keberatan(): BelongsTo
    {
        return $this->belongsTo(KeberatanInformasi::class, 'keberatan_id');
    }
}
