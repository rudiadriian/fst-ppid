<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Galeri extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'galeri';

    protected $fillable = [
        'judul',
        'tipe',
        'path_file',
        'deskripsi',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];
}
