<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BannerSlider extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'banner_slider';

    protected $fillable = [
        'judul',
        'judul_en',
        'ringkasan',
        'ringkasan_en',
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
