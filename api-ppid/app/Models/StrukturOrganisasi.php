<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StrukturOrganisasi extends Model
{
    protected $table = 'struktur_organisasi';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jabatan',
        'foto',
        'urutan',
        'deskripsi',
        'is_active',
        // Pembentuk bagan pada situs publik.
        'parent_id',
        'tipe_node',
        'poin',
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
