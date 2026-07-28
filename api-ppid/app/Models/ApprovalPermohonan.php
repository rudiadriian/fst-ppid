<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalPermohonan extends Model
{
    protected $table = 'approval_permohonan';

    public const UPDATED_AT = null;

    protected $fillable = [
        'permohonan_id',
        'disiapkan_oleh',
        'tanggal_diajukan',
        'disetujui_oleh',
        'status_approval',
        'catatan_approval',
        'tanggal_approval',
    ];

    protected $casts = [
        'tanggal_diajukan' => 'datetime',
        'tanggal_approval' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }

    public function penyiap(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disiapkan_oleh');
    }

    public function penyetuju(): BelongsTo
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
