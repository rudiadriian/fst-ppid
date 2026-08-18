<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Faq extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'faq';

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
