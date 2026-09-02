<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Concerns\MencatatPelaku;
use App\Support\AlurPersetujuan;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'permohonan_informasi';

    /**
     * Jenjang persetujuan tidak boleh hidup lebih lama dari berkasnya.
     *
     * `approval_pengajuan` menunjuk dua tabel sekaligus sehingga tidak bisa
     * dipasangi foreign key, dan lonceng "menunggu persetujuan Anda" menyimpan
     * tautan ke rincian berkas ini. Tanpa pembersihan, permohonan yang dihapus
     * meninggalkan lonceng yang menunjuk halaman yang tidak ada — dan petugas
     * tidak punya cara membuangnya karena berkasnya sendiri sudah hilang dari
     * daftar (langkah 100).
     *
     * Hapus lunak hanya membuang loncengnya: barisnya masih ada dan riwayat
     * jenjangnya masih bernilai sebagai jejak. Hapus permanen membuang
     * keduanya, karena tidak ada lagi berkas yang dirujuk langkahnya.
     */
    protected static function booted(): void
    {
        static::deleted(function (self $permohonan): void {
            if ($permohonan->isForceDeleting()) {
                return;
            }

            AlurPersetujuan::bersihkanLonceng(AlurPersetujuan::JENIS_PERMOHONAN, (int) $permohonan->getKey());
        });

        static::forceDeleted(function (self $permohonan): void {
            AlurPersetujuan::bersihkan(AlurPersetujuan::JENIS_PERMOHONAN, (int) $permohonan->getKey());
        });
    }

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
        'jalur_pelayanan',
        'alasan_penolakan',
        'batas_waktu_tanggapan',
        'batas_waktu_awal',
        'diperpanjang_pada',
        'alasan_perpanjangan',
        'jadwal_layanan',
        'keterangan_petugas',
        'tanggal_tanggapan',
        'ditangani_oleh',
        'tampil_di_register_publik',
    ];

    protected $casts = [
        'tanggal_permohonan' => 'datetime',
        'batas_waktu_tanggapan' => 'datetime',
        'batas_waktu_awal' => 'datetime',
        'diperpanjang_pada' => 'datetime',
        'jadwal_layanan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
        'tampil_di_register_publik' => 'boolean',
    ];

    /**
     * Jalur pelayanan yang dipilih pemohon.
     *
     * Menentukan bentuk tindak lanjut petugas, bukan sekadar label: `online`
     * menuntut dokumen diunggah dan dikirim lewat surel, `langsung` menuntut
     * tanggal dan jam undangan agar pemohon tahu kapan harus datang.
     */
    public const JALUR = [
        'online' => 'Online — dokumen dikirim lewat surel',
        'langsung' => 'Langsung — pemohon hadir di meja layanan',
    ];

    /**
     * Transisi status yang diizinkan. Status akhir (selesai/kedaluwarsa) tidak
     * punya tujuan lanjutan supaya berkas tidak bisa dibuka ulang diam-diam.
     */
    public const TRANSISI = [
        'diajukan' => ['diverifikasi', 'diproses', 'ditolak', 'kedaluwarsa'],
        'diverifikasi' => ['diproses', 'ditolak', 'kedaluwarsa'],
        // Diproses = sudah disetujui PPID Pelaksana, tinggal putusan PPID.
        // Persetujuan PPID menutup perkaranya langsung ke `selesai`; `disetujui`
        // tetap sah sebagai tujuan manual dan sebagai status baris lama.
        'diproses' => ['selesai', 'disetujui', 'ditolak', 'ditolak_sebagian', 'revisi', 'kedaluwarsa'],
        // Dikembalikan ke PPID Pelaksana; dari sini berkasnya diajukan lagi.
        'revisi' => ['diproses', 'ditolak', 'kedaluwarsa'],
        // Kosakata lama, artinya sama dengan `diproses`. Tidak dipasang lagi,
        // tetapi baris yang telanjur tersimpan tetap punya jalan keluar.
        'menunggu_approval' => ['disetujui', 'ditolak', 'ditolak_sebagian', 'revisi', 'diproses'],
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

    /**
     * Arsip putusan sebelum alur persetujuan berjenjang dipakai.
     *
     * Tabelnya tidak ditulis lagi; isinya riwayat yang sudah terjadi dan tetap
     * ditampilkan pada rincian permohonan lama.
     */
    public function approval(): HasMany
    {
        return $this->hasMany(ApprovalPermohonan::class, 'permohonan_id');
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
            ->where('jenis', 'permohonan')
            ->orderBy('id');
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
