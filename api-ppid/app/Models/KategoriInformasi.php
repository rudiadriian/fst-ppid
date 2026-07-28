<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriInformasi extends Model
{
    protected $table = 'kategori_informasi';

    protected $fillable = [
        'parent_id',
        'nama',
        'slug',
        'deskripsi',
        'urutan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'urutan' => 'integer',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function informasiPublik(): HasMany
    {
        return $this->hasMany(InformasiPublik::class, 'kategori_id');
    }
}
