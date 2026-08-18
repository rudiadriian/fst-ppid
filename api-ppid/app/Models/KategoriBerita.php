<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriBerita extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'kategori_berita';

    protected $fillable = [
        'nama',
        'nama_en',
        'slug',
    ];

    public function berita(): HasMany
    {
        return $this->hasMany(Berita::class, 'kategori_berita_id');
    }
}
