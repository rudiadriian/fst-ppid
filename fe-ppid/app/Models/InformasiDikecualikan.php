<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiDikecualikan extends Model
{
    use SoftDeletes;

    protected $table = 'informasi_dikecualikan';

    protected $casts = [
        'tanggal_penetapan' => 'date',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
