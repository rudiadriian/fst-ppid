<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class StrukturOrganisasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'struktur_organisasi';

    protected $fillable = [
        'nama',
        'jabatan',
        'jabatan_en',
        'foto',
        'urutan',
        'deskripsi',
        'deskripsi_en',
        'is_active',
        // Pembentuk bagan pada situs publik.
        'parent_id',
        'tipe_node',
        'poin',
        'poin_en',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
        'parent_id' => 'integer',
    ];

    /** Kotak induk pada bagan struktur organisasi. */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }
}
