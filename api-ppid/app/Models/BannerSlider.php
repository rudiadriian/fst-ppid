<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannerSlider extends Model
{
    protected $table = 'banner_slider';

    public $timestamps = false;

    protected $fillable = [
        'judul',
        'gambar',
        'link',
        'urutan',
        'is_active',
        'tanggal_mulai',
        'tanggal_selesai',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];
}
