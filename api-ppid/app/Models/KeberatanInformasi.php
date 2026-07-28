<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KeberatanInformasi extends Model
{
    protected $table = 'keberatan_informasi';

    protected $fillable = [
        'permohonan_id',
        'pemohon_id',
        'jenis_keberatan',
        'alasan_keberatan',
        'status',
        'tanggapan_atasan_ppid',
        'ditangani_oleh',
        'tanggal_tanggapan',
    ];

    protected $casts = [
        'tanggal_keberatan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function files(): HasMany
    {
        return $this->hasMany(KeberatanFile::class, 'keberatan_id');
    }
}
