<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TautanTerkait extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'tautan_terkait';

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
