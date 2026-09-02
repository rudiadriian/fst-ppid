<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use App\Support\AlurPersetujuan;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class KeberatanInformasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'keberatan_informasi';

    /**
     * Jenjang persetujuan tidak boleh hidup lebih lama dari berkasnya.
     *
     * Alasannya sama persis dengan {@see PermohonanInformasi::booted()}:
     * `approval_pengajuan` tidak bisa dipasangi foreign key, dan lonceng
     * giliran menyimpan tautan ke rincian berkas ini (langkah 100).
     */
    protected static function booted(): void
    {
        static::deleted(function (self $keberatan): void {
            if ($keberatan->isForceDeleting()) {
                return;
            }

            AlurPersetujuan::bersihkanLonceng(AlurPersetujuan::JENIS_KEBERATAN, (int) $keberatan->getKey());
        });

        static::forceDeleted(function (self $keberatan): void {
            AlurPersetujuan::bersihkan(AlurPersetujuan::JENIS_KEBERATAN, (int) $keberatan->getKey());
        });
    }

    /**
     * Tujuh dasar keberatan menurut Pasal 35 UU No. 14 Tahun 2008.
     *
     * Urutannya mengikuti pasalnya, bukan abjad, supaya pilihan di portal
     * pemohon terbaca sejajar dengan bunyi undang-undangnya. Kuncinya adalah
     * nilai yang diterima CHECK constraint
     * `keberatan_informasi_jenis_keberatan_check`.
     */
    public const JENIS = [
        'permohonan_ditolak' => 'Penolakan atas Permintaan Informasi',
        'permintaan_tidak_ditanggapi' => 'Tidak Ditanggapinya Permintaan Informasi',
        'melebihi_jangka_waktu' => 'Penyampaian Informasi Melebihi Waktu yang Diatur',
        'informasi_tidak_sesuai' => 'Permintaan Informasi Tidak Ditanggapi Sebagaimana yang Diminta',
        'permintaan_tidak_dipenuhi' => 'Tidak Dipenuhinya Permintaan Informasi',
        'biaya_tidak_wajar' => 'Pengenaan Biaya yang Tidak Wajar',
        'informasi_tidak_disediakan' => 'Tidak Disediakannya Informasi Berkala',
    ];

    protected $fillable = [
        'permohonan_id',
        'pemohon_id',
        'jenis_keberatan',
        'alasan_keberatan',
        'status',
        'jalur_pelayanan',
        'tanggapan_atasan_ppid',
        'ditangani_oleh',
        'tanggal_tanggapan',
        'batas_waktu_tanggapan',
        'batas_waktu_sengketa',
        'jadwal_layanan',
        'keterangan_petugas',
    ];

    protected $casts = [
        'tanggal_keberatan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
        'batas_waktu_tanggapan' => 'datetime',
        'batas_waktu_sengketa' => 'datetime',
        'jadwal_layanan' => 'datetime',
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
        // Diproses = sudah diteruskan PPID Pelaksana, tinggal putusan PPID.
        'diproses' => ['selesai', 'revisi', 'ditolak'],
        'revisi' => ['diproses', 'ditolak'],
        // Kosakata lama, artinya sama dengan `diproses`; tidak dipasang lagi.
        'menunggu_approval' => ['selesai', 'revisi', 'ditolak', 'diproses'],
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
