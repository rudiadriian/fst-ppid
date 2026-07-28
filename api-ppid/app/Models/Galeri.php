<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    public const UPDATED_AT = null;

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
