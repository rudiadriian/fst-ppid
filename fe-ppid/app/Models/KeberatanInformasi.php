<?php

namespace App\Models;

use App\Models\Concerns\TanpaCapUbahSaatDibuat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Keberatan atas layanan informasi publik.
 *
 * Diisi pengunjung lewat Portal Pengguna (wajib login) dan ditindaklanjuti
 * petugas lewat be-ppid.
 */
class KeberatanInformasi extends Model
{
    use SoftDeletes, TanpaCapUbahSaatDibuat;

    protected $table = 'keberatan_informasi';

    /** Nilai yang diterima CHECK constraint `keberatan_informasi_jenis_keberatan_check`. */
    public const JENIS = [
        'permohonan_ditolak' => 'Permohonan Informasi Ditolak',
        'informasi_tidak_disediakan' => 'Informasi Tidak Disediakan',
        'permintaan_tidak_ditanggapi' => 'Permintaan Tidak Ditanggapi',
        'informasi_tidak_sesuai' => 'Informasi yang Diberikan Tidak Sesuai',
        'biaya_tidak_wajar' => 'Pengenaan Biaya yang Tidak Wajar',
        'melebihi_jangka_waktu' => 'Permintaan Melebihi Jangka Waktu Tanggapan',
    ];

    /** Label status; disamakan dengan penamaan pada Permohonan Informasi. */
    public const STATUS_LABEL = [
        'diajukan' => 'Dalam Proses',
        'diproses' => 'Dalam Proses',
        'revisi' => 'Revisi',
        'menunggu_approval' => 'Menunggu Persetujuan',
        'ditolak' => 'Tolak',
        'selesai' => 'Selesai',
    ];

    protected $fillable = [
        'permohonan_id',
        'pemohon_id',
        'jenis_keberatan',
        'alasan_keberatan',
        'kasus_posisi',
        'dikuasakan',
        'status',
        'tanggal_keberatan',
    ];

    protected $casts = [
        'dikuasakan' => 'boolean',
        'tanggal_keberatan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function berkas(): HasMany
    {
        return $this->hasMany(KeberatanFile::class, 'keberatan_id');
    }

    public function labelStatus(): string
    {
        return __(self::STATUS_LABEL[$this->status] ?? $this->status);
    }

    /** Status mentah yang termasuk satu kelompok label; dipakai tab filter. */
    public static function statusKelompok(string $label): array
    {
        return array_keys(array_filter(self::STATUS_LABEL, fn ($nilai) => $nilai === $label));
    }
}
