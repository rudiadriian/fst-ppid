<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeberatanInformasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'keberatan_informasi';

    protected $fillable = [
        'permohonan_id',
        'pemohon_id',
        'jenis_keberatan',
        'alasan_keberatan',
        'status',
        'tanggapan_atasan_ppid',
        'ditangani_oleh',
        'tanggal_tanggapan',
    ];

    protected $casts = [
        'tanggal_keberatan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
    ];

    /**
     * Transisi status yang diizinkan.
     *
     * Disusun sejajar dengan {@see PermohonanInformasi::TRANSISI}: sebelumnya
     * status keberatan bisa dipasang bebas, sehingga berkas yang sudah ditutup
     * masih bisa dibuka ulang tanpa jejak. `selesai` dan `ditolak` karena itu
     * tidak punya tujuan lanjutan.
     *
     * `menunggu_approval` adalah pintu masuk alur persetujuan berjenjang;
     * putusan akhirnya tidak lagi dipasang petugas sendiri.
     */
    public const TRANSISI = [
        'diajukan' => ['diproses', 'ditolak'],
        'diproses' => ['menunggu_approval', 'revisi', 'ditolak'],
        'revisi' => ['diproses', 'ditolak'],
        'menunggu_approval' => ['selesai', 'ditolak', 'diproses'],
        'selesai' => [],
        'ditolak' => [],
    ];

    public function permohonan(): BelongsTo
    {
        return $this->belongsTo(PermohonanInformasi::class, 'permohonan_id');
    }

    public function pemohon(): BelongsTo
    {
        return $this->belongsTo(Pemohon::class, 'pemohon_id');
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ditangani_oleh');
    }

    public function files(): HasMany
    {
        return $this->hasMany(KeberatanFile::class, 'keberatan_id');
    }

    /**
     * Jenjang persetujuan berjalan.
     *
     * `approval_pengajuan` melayani permohonan dan keberatan sekaligus, jadi
     * relasinya wajib menyaring `jenis` — tanpa itu id yang sama pada dua tabel
     * berbeda akan saling meminjam langkah.
     */
    public function approvalLangkah(): HasMany
    {
        return $this->hasMany(ApprovalPengajuan::class, 'pengajuan_id')
            ->where('jenis', 'keberatan')
            ->orderBy('id');
    }
}
