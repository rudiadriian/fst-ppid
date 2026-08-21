<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiPublik extends Model
{
    use PunyaVersiInggris;

    use SoftDeletes;

    protected $table = 'informasi_publik';

    /** Kolom tsvector tidak pernah dipakai view; jangan ikut di-serialize. */
    protected $hidden = ['search_vector'];

    protected $casts = [
        'unduhan_terbatas' => 'boolean',
        'tanggal_publikasi' => 'date',
        'views_count'       => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInformasi::class, 'kategori_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(InformasiPublikFile::class, 'informasi_publik_id')->orderBy('urutan');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
