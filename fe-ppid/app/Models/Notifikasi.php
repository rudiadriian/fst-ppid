<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Notifikasi panel admin (be-ppid).
 *
 * Tabelnya sama dengan yang dibaca `GET /v1/notifikasi` di api-ppid; situs
 * publik hanya menulis baris baru ke sini, tidak pernah membacanya.
 */
class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    /** Tabelnya tidak punya kolom `updated_at`. */
    public const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'type',
        'message',
        'is_read',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'data' => 'array',
    ];
}
