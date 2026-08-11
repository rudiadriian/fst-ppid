<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use SoftDeletes;

    protected $table = 'permohonan_informasi';

    /** Status yang berarti permohonan sudah selesai ditangani (boleh disurvei). */
    public const STATUS_TUNTAS = ['selesai', 'ditolak', 'ditolak_sebagian'];

    /** Label status untuk portal pengguna dan halaman publik. */
    public const STATUS_LABEL = [
        'diajukan' => 'Dalam Proses',
        'diverifikasi' => 'Dalam Proses',
        'diproses' => 'Dalam Proses',
        'revisi' => 'Revisi',
        'menunggu_approval' => 'Menunggu Persetujuan',
        'disetujui' => 'Selesai',
        'selesai' => 'Selesai',
        'ditolak' => 'Tolak',
        'ditolak_sebagian' => 'Tolak',
        'kedaluwarsa' => 'Tolak',
    ];

    /** Kelompok status yang dipakai grafik & legend dashboard. */
    public const KELOMPOK = ['Dalam Proses', 'Revisi', 'Menunggu Persetujuan', 'Tolak', 'Selesai'];

    /** Cara memperoleh informasi (Perki 1/2010). */
    public const CARA_MEMPEROLEH = [
        'melihat' => 'Melihat',
        'membaca' => 'Membaca',
        'mencatat' => 'Mencatat',
        'mendengar' => 'Mendengar',
    ];

    protected $fillable = [
        'kode_permohonan',
        'pemohon_id',
        'kategori_id',
        'rincian_informasi',
        'tujuan_penggunaan',
        'cara_memperoleh',
        'format_informasi',
        'cara_pengiriman',
        'status',
        'tanggal_permohonan',
        'batas_waktu_tanggapan',
        'tampil_di_register_publik',
    ];

    protected $casts = [
        'tampil_di_register_publik' => 'boolean',
        'tanggal_permohonan'        => 'datetime',
        'tanggal_tanggapan'         => 'datetime',
        'batas_waktu_tanggapan'     => 'datetime',
    ];

    /**
     * Hanya permohonan yang pemohonnya setuju ditampilkan di Register Permohonan publik.
     * Identitas pemohon tidak pernah ikut diambil di sini.
     */
    public function scopeRegisterPublik($query)
    {
        return $query->where('tampil_di_register_publik', true);
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function survei(): HasOne
    {
        return $this->hasOne(SurveyKepuasan::class, 'permohonan_id');
    }

    public function keberatan(): HasMany
    {
        return $this->hasMany(KeberatanInformasi::class, 'permohonan_id');
    }

    public function logStatus(): HasMany
    {
        return $this->hasMany(PermohonanLogStatus::class, 'permohonan_id');
    }

    /** Permohonan sudah tuntas ditangani, jadi pemohon boleh menilai layanannya. */
    public function bolehDisurvei(): bool
    {
        return in_array($this->status, self::STATUS_TUNTAS, true);
    }

    public function labelStatus(): string
    {
        return __(self::STATUS_LABEL[$this->status] ?? $this->status);
    }

    /**
     * Status mentah yang termasuk satu kelompok label ("Dalam Proses", dst).
     * Dipakai tab filter pada portal pengguna.
     */
    public static function statusKelompok(string $label): array
    {
        return array_keys(array_filter(self::STATUS_LABEL, fn ($nilai) => $nilai === $label));
    }
}
