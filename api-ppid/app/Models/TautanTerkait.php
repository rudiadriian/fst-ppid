<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TautanTerkait extends Model
{
    protected $table = 'tautan_terkait';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'url',
        'logo',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];
}
