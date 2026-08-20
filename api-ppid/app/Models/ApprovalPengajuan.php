<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Satu langkah persetujuan milik satu pengajuan.
 *
 * Barisnya dibuat sekaligus saat pengajuan masuk tahap persetujuan: langkah
 * pertama berstatus `menunggu`, sisanya menyusul setelah langkah sebelumnya
 * disetujui. Membuat semuanya di muka membuat jenjang yang masih akan datang
 * ikut terlihat oleh petugas maupun pemohon, bukan muncul satu per satu.
 *
 * `nama_tahap`, `role_id`, dan `nama_jabatan` adalah salinan definisi tahap
 * pada saat langkah dibuat — riwayat tidak boleh berubah ketika super admin
 * menyusun ulang alurnya.
 */
class ApprovalPengajuan extends Model
{
    protected $table = 'approval_pengajuan';

    public const UPDATED_AT = null;

    /** Langkah yang belum diputus; hanya satu per pengajuan pada satu waktu. */
    public const MENUNGGU = 'menunggu';

    protected $fillable = [
        'jenis',
        'pengajuan_id',
        'alur_id',
        'tahap_id',
        'urutan',
        'nama_tahap',
        'role_id',
        'nama_jabatan',
        'status',
        'catatan',
        'diputus_oleh',
        'tanggal_masuk',
        'batas_waktu',
        'tanggal_putusan',
    ];

    protected $casts = [
        'urutan' => 'integer',
        'tanggal_masuk' => 'datetime',
        'batas_waktu' => 'datetime',
        'tanggal_putusan' => 'datetime',
    ];

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function tahap(): BelongsTo
    {
        return $this->belongsTo(AlurApprovalTahap::class, 'tahap_id');
    }

    public function pemutus(): BelongsTo
    {
        return $this->belongsTo(User::class, 'diputus_oleh');
    }
}
