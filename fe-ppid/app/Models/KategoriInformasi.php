<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriInformasi extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'kategori_informasi';

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    public function informasiPublik(): HasMany
    {
        return $this->hasMany(InformasiPublik::class, 'kategori_id');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }
}
