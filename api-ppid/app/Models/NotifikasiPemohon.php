<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Notifikasi lonceng Portal Pemohon.
 *
 * Ditulis dari sini (panel admin), dibaca fe-ppid. Bandingkan dengan
 * {@see Notifikasi} yang arahnya kebalikan: penerimanya petugas.
 */
class NotifikasiPemohon extends Model
{
    protected $table = 'notifikasi_pemohon';

    /** Tabelnya tidak punya kolom `updated_at`. */
    public const UPDATED_AT = null;

    protected $fillable = [
        'pemohon_id',
        'type',
        'message',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }
}
