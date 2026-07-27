<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'role_id',
        'name',
        'email',
        'password',
        'phone',
        'is_active',
        'photo_url',
        'shortcuts',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'email_verified_at' => 'datetime',
        'shortcuts' => 'array',
        'settings' => 'array',
        'password' => 'hashed',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Matrix hak akses milik role user ini terhadap tiap modul backend.
     */
    public function aksesModul(): HasMany
    {
        return $this->hasMany(RoleModulAkses::class, 'role_id', 'role_id');
    }

    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Klaim tambahan di dalam token. Sengaja minimal (role slug saja) supaya
     * perubahan hak akses selalu dibaca ulang dari DB, bukan dari token lama.
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'role' => $this->role?->slug,
        ];
    }

    /**
     * Bentuk user yang dikonsumsi frontend admin (Fuse React).
     */
    public function toFuseUser(): array
    {
        return [
            'id' => (string) $this->id,
            'role' => $this->role?->slug ? [$this->role->slug] : [],
            'displayName' => $this->name,
            'email' => $this->email,
            'photoURL' => $this->photo_url,
            'shortcuts' => $this->shortcuts ?? [],
            'settings' => $this->settings ?? new \stdClass(),
        ];
    }
}
