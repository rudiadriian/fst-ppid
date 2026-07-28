<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Berita extends Model
{
    use SoftDeletes;

    protected $table = 'berita';

    protected $fillable = [
        'kategori_berita_id',
        'judul',
        'slug',
        'thumbnail',
        'ringkasan',
        'konten',
        'tanggal_publikasi',
        'status',
        'penulis',
    ];

    protected $casts = [
        'tanggal_publikasi' => 'date',
        'views_count' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriBerita::class, 'kategori_berita_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'penulis');
    }
}
