<?php

namespace App\Models;

use App\Models\Concerns\TanpaCapUbahSaatDibuat;
use App\Notifications\ResetPasswordPemohon;
use App\Notifications\VerifikasiEmailPemohon;
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
    use TanpaCapUbahSaatDibuat;

    protected $table = 'pemohon';

    /**
     * Batas penolakan berkas verifikasi.
     *
     * Setelah ditolak sebanyak ini, pemohon tidak boleh mengirim ulang dan
     * harus menghubungi petugas PPID secara langsung.
     */
    public const BATAS_DITOLAK = 3;

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
        'jumlah_ditolak',
        'catatan_verifikasi',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'nik',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_verifikasi' => 'datetime',
        'jumlah_ditolak' => 'integer',
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

    /**
     * Email pemohon memakai kop PPID Food Station, bukan templat markdown
     * bawaan Laravel — lihat App\Notifications.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify((new VerifikasiEmailPemohon())->locale(config('ppid.bahasa_email')));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify((new ResetPasswordPemohon($token))->locale(config('ppid.bahasa_email')));
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

    /**
     * Berkasnya sudah ditolak sampai batas — tidak boleh kirim ulang lagi.
     *
     * Sengaja diturunkan dari hitungan penolakan, bukan dari status baru:
     * dengan begitu `status_verifikasi` tetap memakai empat nilai yang sudah
     * dikenal seluruh sistem, dan data lama tidak perlu diisi ulang.
     */
    public function verifikasiDiblokir(): bool
    {
        return (int) $this->jumlah_ditolak >= self::BATAS_DITOLAK;
    }

    /** Berapa kali lagi berkasnya boleh dikirim ulang. */
    public function sisaKesempatanVerifikasi(): int
    {
        return max(0, self::BATAS_DITOLAK - (int) $this->jumlah_ditolak);
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
