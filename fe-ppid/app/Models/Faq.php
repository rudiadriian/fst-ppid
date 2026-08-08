<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faq';

    public $timestamps = false;

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
