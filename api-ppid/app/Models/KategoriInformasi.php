<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriInformasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'kategori_informasi';

    protected $fillable = [
        'parent_id',
        'nama',
        'nama_en',
        'slug',
        'deskripsi',
        'deskripsi_en',
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
