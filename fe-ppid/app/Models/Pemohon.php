<?php

namespace App\Models;

use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Akun pengunjung situs publik (pemohon informasi).
 *
 * Tabel `pemohon` sudah dipakai `be-ppid` sebagai data pemohon; kolom
 * `password` dan `email_verified_at` di tabel itu yang dipakai sebagai akun
 * login publik. Jadi satu orang punya satu baris saja — riwayat permohonan
 * yang dulu diinput petugas tetap menempel setelah orangnya mendaftar.
 *
 * Guard-nya `pemohon` (lihat config/auth.php), terpisah penuh dari guard
 * `web`/tabel `users` yang dipakai panel admin.
 */
class Pemohon extends Authenticatable implements CanResetPasswordContract, MustVerifyEmailContract
{
    use CanResetPassword;
    use MustVerifyEmailTrait;
    use Notifiable;
    use SoftDeletes;

    protected $table = 'pemohon';

    /** Pilihan Jenis Pemohon pada formulir Verifikasi Data Pemohon. */
    public const JENIS = [
        'perorangan' => 'Perorangan',
        'mahasiswa' => 'Mahasiswa',
        'lembaga' => 'Lembaga / Organisasi / Perusahaan',
        'kelompok' => 'Kelompok Orang',
    ];

    protected $fillable = [
        'nik',
        'nama',
        'email',
        'no_hp',
        'alamat',
        'pekerjaan',
        'jenis_pemohon',
        'nama_lembaga',
        'password',
        'email_verified_at',
        'foto',
        'file_ktp',
        'status_verifikasi',
        'tanggal_verifikasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nik',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'password' => 'hashed',
    ];

    /** Tabel `pemohon` tidak punya kolom `remember_token`. */
    public function getRememberToken()
    {
        return null;
    }

    public function setRememberToken($value)
    {
        // Sengaja kosong: fitur "ingat saya" tidak dipakai akun pengunjung.
    }

    public function getRememberTokenName()
    {
        return null;
    }

    /** Nama yang ditampilkan pada sapaan dan notifikasi. */
    public function getNameAttribute(): string
    {
        return (string) $this->nama;
    }

    public function permohonan(): HasMany
    {
        return $this->hasMany(PermohonanInformasi::class, 'pemohon_id');
    }

    public function keberatan(): HasMany
    {
        return $this->hasMany(KeberatanInformasi::class, 'pemohon_id');
    }

    /** Akun yang belum pernah menyetel password berarti belum didaftarkan. */
    public function sudahPunyaAkun(): bool
    {
        return filled($this->password);
    }

    /**
     * Data Pemohon sudah diverifikasi petugas.
     *
     * Syarat untuk boleh mengajukan Permohonan Informasi — identitas pemohon
     * harus jelas sebelum permohonan diproses.
     */
    public function dataTerverifikasi(): bool
    {
        return $this->status_verifikasi === 'terverifikasi';
    }

    /** Berkas verifikasi sudah dikirim, tinggal menunggu pemeriksaan petugas. */
    public function verifikasiMenunggu(): bool
    {
        return $this->status_verifikasi === 'menunggu';
    }

    public function labelStatusVerifikasi(): string
    {
        return match ($this->status_verifikasi) {
            'terverifikasi' => __('Terverifikasi'),
            'menunggu' => __('Menunggu Pemeriksaan'),
            'ditolak' => __('Ditolak'),
            default => __('Belum Diverifikasi'),
        };
    }
}
