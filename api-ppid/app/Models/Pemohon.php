<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemohon extends Model
{
    use SoftDeletes;

    protected $table = 'pemohon';

    protected $fillable = [
        'nik',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'pekerjaan',
        'jenis_pemohon',
        'nama_lembaga',
    ];

    /**
     * NIK dan password tidak pernah ikut response API: NIK data pribadi,
     * password hash tidak ada urusannya dengan panel admin.
     */
    protected $hidden = [
        'password',
        'nik',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanInformasi::class, 'pemohon_id');
    }
}
