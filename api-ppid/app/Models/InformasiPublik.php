<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiPublik extends Model
{
    use SoftDeletes;

    protected $table = 'informasi_publik';

    /**
     * `search_vector` sengaja tidak fillable: kolom itu diisi trigger PostgreSQL
     * `trg_infopublik_search`, bukan oleh aplikasi.
     */
    protected $fillable = [
        'kategori_id',
        'judul',
        'slug',
        'ringkasan',
        'konten',
        'nomor_klasifikasi',
        'tanggal_publikasi',
        'status',
        'published_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $hidden = ['search_vector'];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'reviewed_at' => 'datetime',
        'views_count' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInformasi::class, 'kategori_id');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(InformasiPublikFile::class, 'informasi_publik_id')->orderBy('urutan');
    }
}
