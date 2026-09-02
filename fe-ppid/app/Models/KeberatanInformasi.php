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

    /**
     * Tujuh dasar keberatan menurut Pasal 35 UU No. 14 Tahun 2008.
     *
     * Kuncinya adalah nilai yang diterima CHECK constraint
     * `keberatan_informasi_jenis_keberatan_check`; urutannya mengikuti bunyi
     * pasalnya, bukan abjad, supaya daftar pilihan di formulir terbaca sejajar
     * dengan undang-undangnya. Salinan dari `KeberatanInformasi::JENIS` di
     * api-ppid — keduanya harus berubah bersama.
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

    /** Label status; disamakan dengan penamaan pada Permohonan Informasi. */
    public const STATUS_LABEL = [
        'diajukan' => 'Dalam Proses',
        'diproses' => 'Dalam Proses',
        // Putaran internal PPID ↔ PPID Pelaksana; lihat alasannya pada
        // {@see PermohonanInformasi::STATUS_LABEL}.
        'revisi' => 'Dalam Proses',
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
        'jalur_pelayanan',
        'tanggal_keberatan',
        'batas_waktu_tanggapan',
    ];

    protected $casts = [
        'dikuasakan' => 'boolean',
        'tanggal_keberatan' => 'datetime',
        'tanggal_tanggapan' => 'datetime',
        'batas_waktu_tanggapan' => 'datetime',
        'batas_waktu_sengketa' => 'datetime',
        'jadwal_layanan' => 'datetime',
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

    /**
     * Tonggak alur versi pemohon; sama tiga langkahnya dengan permohonan.
     *
     * Lihat {@see PermohonanInformasi::tahapAlurPortal()} untuk alasannya.
     */
    public function tahapAlurPortal(): string
    {
        if (in_array($this->status, ['selesai', 'ditolak'], true)) {
            return 'selesai';
        }

        return $this->status === 'diajukan' ? 'diajukan' : 'diproses';
    }

    /**
     * Tanggal tiap tonggak.
     *
     * Keberatan tidak punya tabel log status seperti permohonan, jadi tonggak
     * "Diproses" memang tidak bertanggal — lebih baik kosong daripada diisi
     * `updated_at`, yang bergeser maju tiap kali petugas menyentuh barisnya.
     *
     * @return array<string, \Illuminate\Support\Carbon|null>
     */
    public function tanggalAlurPortal(): array
    {
        return [
            'diajukan' => $this->tanggal_keberatan,
            'diproses' => null,
            'selesai' => $this->tanggal_tanggapan,
        ];
    }

    public function labelStatus(): string
    {
        return __(self::STATUS_LABEL[$this->status] ?? $this->status);
    }

    /** Status mentah yang termasuk satu kelompok label; dipakai grafik dashboard. */
    public static function statusKelompok(string $label): array
    {
        return array_keys(array_filter(self::STATUS_LABEL, fn ($nilai) => $nilai === $label));
    }

    /**
     * Status mentah untuk satu tab Portal Pemohon.
     *
     * Pengelompokannya dipinjam dari Permohonan Informasi supaya kedua daftar
     * di portal memakai tab yang sama persis: Semua, Dalam Proses, Selesai.
     */
    public static function statusKelompokPortal(string $label): array
    {
        $rinci = PermohonanInformasi::statusKelompokPortal($label);
        $labelRinci = array_map(
            fn ($status) => PermohonanInformasi::STATUS_LABEL[$status] ?? $status,
            $rinci
        );

        return array_keys(array_filter(
            self::STATUS_LABEL,
            fn ($nilai) => in_array($nilai, $labelRinci, true)
        ));
    }
}
