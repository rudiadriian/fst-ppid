<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModulSistem extends Model
{
    protected $table = 'modul_sistem';

    public $timestamps = false;

    protected $fillable = ['parent_id', 'nama', 'slug', 'icon', 'route', 'urutan', 'is_active'];

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
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }

    public function akses(): HasMany
    {
        return $this->hasMany(RoleModulAkses::class, 'modul_id');
    }
}
