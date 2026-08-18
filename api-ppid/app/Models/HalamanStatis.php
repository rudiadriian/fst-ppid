<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HalamanStatis extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'halaman_statis';

    /**
     * `updated_by` tidak lagi fillable: pengisiannya diambil alih trait
     * MencatatPelaku dari pengguna yang login, bukan dari isian klien.
     */
    protected $fillable = [
        'judul',
        'judul_en',
        'slug',
        'konten',
        'konten_en',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Nama lama untuk `pengubah()`; dipakai kolom "Diubah oleh" di panel. */
    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
