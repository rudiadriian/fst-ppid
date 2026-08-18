<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'permohonan_informasi';

    /**
     * `kode_permohonan` diisi DEFAULT di sisi PostgreSQL, `status` hanya boleh
     * berubah lewat endpoint transisi status supaya log status selalu terisi.
     */
    protected $fillable = [
        'pemohon_id',
        'kategori_id',
        'rincian_informasi',
        'tujuan_penggunaan',
        'format_informasi',
        'cara_pengiriman',
        'alasan_penolakan',
        'batas_waktu_tanggapan',
        'tanggal_tanggapan',
        'ditangani_oleh',
        'tampil_di_register_publik',
    ];

    protected $casts = [
        'tanggal_permohonan' => 'datetime',
        'batas_waktu_tanggapan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
        'tampil_di_register_publik' => 'boolean',
    ];

    /**
     * Transisi status yang diizinkan. Status akhir (selesai/kedaluwarsa) tidak
     * punya tujuan lanjutan supaya berkas tidak bisa dibuka ulang diam-diam.
     */
    public const TRANSISI = [
        'diajukan' => ['diverifikasi', 'ditolak', 'kedaluwarsa'],
        'diverifikasi' => ['diproses', 'ditolak', 'kedaluwarsa'],
        'diproses' => ['menunggu_approval', 'ditolak', 'kedaluwarsa'],
        'menunggu_approval' => ['disetujui', 'ditolak', 'ditolak_sebagian', 'diproses'],
        'disetujui' => ['selesai'],
        'ditolak' => ['selesai'],
        'ditolak_sebagian' => ['selesai'],
        'selesai' => [],
        'kedaluwarsa' => [],
    ];

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(KategoriInformasi::class, 'kategori_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PermohonanFile::class, 'permohonan_id');
    }

    public function tanggapanFiles(): HasMany
    {
        return $this->hasMany(PermohonanTanggapanFile::class, 'permohonan_id');
    }

    public function logStatus(): HasMany
    {
        return $this->hasMany(PermohonanLogStatus::class, 'permohonan_id')->orderByDesc('created_at');
    }

    public function approval(): HasMany
    {
        return $this->hasMany(ApprovalPermohonan::class, 'permohonan_id');
    }

    public function keberatan(): HasMany
    {
        return $this->hasMany(KeberatanInformasi::class, 'permohonan_id');
    }

    public function survey(): HasMany
    {
        return $this->hasMany(SurveyKepuasan::class, 'permohonan_id');
    }
}
