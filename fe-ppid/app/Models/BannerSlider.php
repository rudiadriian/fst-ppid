<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerSlider extends Model
{
    protected $table = 'banner_slider';

    public $timestamps = false;

    protected $casts = [
        'urutan'          => 'integer',
        'is_active'       => 'boolean',
        'tanggal_mulai'   => 'date',
        'tanggal_selesai' => 'date',
    ];

    /**
     * Banner yang aktif dan sedang berada dalam rentang tayang.
     * Tanggal kosong berarti tanpa batas.
     */
    public function scopeTayang($query)
    {
        $hariIni = now()->toDateString();

        return $query->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('tanggal_mulai')->orWhereDate('tanggal_mulai', '<=', $hariIni))
            ->where(fn ($q) => $q->whereNull('tanggal_selesai')->orWhereDate('tanggal_selesai', '>=', $hariIni))
            ->orderBy('urutan');
    }
}
