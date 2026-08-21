<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiPublik extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'informasi_publik';

    /**
     * `search_vector` sengaja tidak fillable: kolom itu diisi trigger PostgreSQL
     * `trg_infopublik_search`, bukan oleh aplikasi.
     */
    protected $fillable = [
        'kategori_id',
        'judul',
        'judul_en',
        'slug',
        'ringkasan',
        'ringkasan_en',
        'konten',
        'konten_en',
        'tautan',
        'unduhan_terbatas',
        'nomor_klasifikasi',
        'tanggal_publikasi',
        'status',
        'published_by',
        'reviewed_by',
        'reviewed_at',
    ];

    protected $hidden = ['search_vector'];

    protected $casts = [
        'unduhan_terbatas' => 'boolean',
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
