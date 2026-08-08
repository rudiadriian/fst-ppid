<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MenuNavigasi extends Model
{
    protected $table = 'menu_navigasi';

    public $timestamps = false;

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
    ];

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')
            ->where('is_active', true)
            ->orderBy('urutan');
    }
}
