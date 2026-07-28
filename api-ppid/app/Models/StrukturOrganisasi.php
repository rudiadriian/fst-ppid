<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StrukturOrganisasi extends Model
{
    protected $table = 'struktur_organisasi';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jabatan',
        'foto',
        'urutan',
        'deskripsi',
        'is_active',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];
}
