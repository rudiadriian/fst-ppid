<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PermohonanLogStatus extends Model
{
    protected $table = 'permohonan_log_status';

    public const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_id',
        'status_sebelumnya',
        'status_baru',
        'catatan',
        'changed_by',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
