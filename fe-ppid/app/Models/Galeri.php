<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Galeri extends Model
{
    protected $table = 'galeri';

    const UPDATED_AT = null;

    protected $casts = [
        'tanggal' => 'date',
    ];
}
