<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel partisi per bulan (PARTITION BY RANGE visited_at).
 * Hanya dibaca dari panel admin; penulisan dilakukan situs publik.
 */
class StatistikKunjungan extends Model
{
    protected $table = 'statistik_kunjungan';

    public $timestamps = false;

    protected $fillable = [
        'halaman',
        'ip_address',
        'user_agent',
        'referrer',
        'visited_at',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];
}
