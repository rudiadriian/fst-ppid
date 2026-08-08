<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HalamanStatis extends Model
{
    protected $table = 'halaman_statis';

    /** Tabel halaman_statis hanya punya updated_at. */
    const CREATED_AT = null;

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
