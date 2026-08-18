<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pemohon extends Model
{
    use MencatatPelaku, SoftDeletes;

    /** Berkas ditolak sebanyak ini berarti pemohon tidak boleh mengirim ulang. */
    public const BATAS_DITOLAK = 3;

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
        'tanggal_verifikasi' => 'datetime',
        'jumlah_ditolak' => 'integer',
    ];

    /**
     * Ditambahkan ke setiap response supaya panel tidak perlu mengulang aturan
     * "sudah tiga kali ditolak" sendiri — satu tempat, satu kebenaran.
     */
    protected $appends = ['verifikasi_diblokir', 'sisa_kesempatan'];

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanInformasi::class, 'pemohon_id');
    }

    public function verifikator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function getVerifikasiDiblokirAttribute(): bool
    {
        return (int) $this->jumlah_ditolak >= self::BATAS_DITOLAK;
    }

    public function getSisaKesempatanAttribute(): int
    {
        return max(0, self::BATAS_DITOLAK - (int) $this->jumlah_ditolak);
    }
}
