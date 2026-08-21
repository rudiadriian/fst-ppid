<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, MencatatPelaku, Notifiable, SoftDeletes;

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

    /**
     * Email selalu disimpan huruf kecil.
     *
     * Perbandingan `=` di PostgreSQL membedakan huruf besar-kecil, sedangkan
     * seluruh jalur auth membakukan email yang diketik ke huruf kecil sebelum
     * mencarinya. Tanpa pembakuan di sisi tulis, akun yang dibuat sebagai
     * "Budi@Foodstation.co.id" tidak akan pernah ditemukan oleh jalur lupa
     * password — gagal diam-diam, tanpa galat, sampai ada yang membutuhkannya.
     */
    public function setEmailAttribute(?string $nilai): void
    {
        $this->attributes['email'] = $nilai === null ? null : \Illuminate\Support\Str::lower(trim($nilai));
    }

    /**
     * Cari akun tanpa membedakan huruf besar-kecil.
     *
     * Dipakai jalur auth dan lupa password. Baris lama yang terlanjur tersimpan
     * dengan huruf besar tetap ketemu, walau tulisan barunya sudah dibakukan.
     */
    public static function denganEmail(?string $email): ?self
    {
        if (blank($email)) {
            return null;
        }

        return static::query()
            ->whereRaw('lower(email) = ?', [\Illuminate\Support\Str::lower(trim($email))])
            ->first();
    }

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

    /**
     * Kirim tautan atur ulang password lewat surat kita sendiri.
     *
     * Notifikasi bawaan Laravel menyusun tautannya dari `route('password.reset')`
     * — rute halaman web yang tidak ada di API ini. Yang harus dibuka petugas
     * adalah halaman di panel admin (aplikasi terpisah), jadi penyusunan
     * tautannya diambil alih `EmailAkunAdmin`.
     */
    public function sendPasswordResetNotification($token): void
    {
        \App\Support\EmailAkunAdmin::tautanReset($this, $token);
    }
}
