<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoleModulAkses extends Model
{
    protected $table = 'role_modul_akses';

    public $timestamps = false;

    protected $fillable = [
        'role_id',
        'modul_id',
        'can_view',
        'can_create',
        'can_edit',
        'can_delete',
        'can_approve',
        'can_export',
    ];

    protected $casts = [
        'can_view' => 'boolean',
        'can_create' => 'boolean',
        'can_edit' => 'boolean',
        'can_delete' => 'boolean',
        'can_approve' => 'boolean',
        'can_export' => 'boolean',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function modul(): BelongsTo
    {
        return $this->belongsTo(ModulSistem::class, 'modul_id');
    }
}
