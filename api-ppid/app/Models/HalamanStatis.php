<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HalamanStatis extends Model
{
    protected $table = 'halaman_statis';

    public const CREATED_AT = null;

    protected $fillable = [
        'judul',
        'slug',
        'konten',
        'is_active',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
