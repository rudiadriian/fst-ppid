<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Berita extends Model
{
    use PunyaVersiInggris;

    use SoftDeletes;

    protected $table = 'berita';

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'views_count'       => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_berita_id');
    }

    /** Hanya berita yang sudah diterbitkan lewat CMS. */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
