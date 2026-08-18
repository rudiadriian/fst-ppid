<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HalamanStatis extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'halaman_statis';

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
