<?php

namespace App\Models;

use App\Models\Concerns\TanpaCapUbahSaatDibuat;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use SoftDeletes, TanpaCapUbahSaatDibuat;

    protected $table = 'permohonan_informasi';

    /** Status yang berarti permohonan sudah selesai ditangani (boleh disurvei). */
    public const STATUS_TUNTAS = ['selesai', 'ditolak', 'ditolak_sebagian'];

    /** Label status untuk portal pengguna dan halaman publik. */
    public const STATUS_LABEL = [
        'diajukan' => 'Dalam Proses',
        'diverifikasi' => 'Dalam Proses',
        'diproses' => 'Dalam Proses',
        /*
         * `revisi` sengaja tampil sebagai "Dalam Proses".
         *
         * Statusnya menandai putaran internal antara PPID dan PPID Pelaksana —
         * berkasnya dikembalikan untuk diperbaiki, lalu diajukan lagi, mungkin
         * beberapa kali. Dari sisi pemohon tidak ada yang berubah: permohonannya
         * memang masih diproses. Menampilkannya apa adanya membuat pemohon
         * mengira berkas *miliknya* yang bermasalah, padahal yang diperbaiki
         * pekerjaan petugas. `NotifikasiPortal` di api-ppid diam untuk status
         * ini dengan alasan yang sama.
         */
        'revisi' => 'Dalam Proses',
        'menunggu_approval' => 'Menunggu Persetujuan',
        'disetujui' => 'Selesai',
        'selesai' => 'Selesai',
        'ditolak' => 'Tolak',
        'ditolak_sebagian' => 'Tolak',
        'kedaluwarsa' => 'Tolak',
    ];

    /**
     * Kelompok status yang dipakai grafik & legend dashboard.
     *
     * "Revisi" dilepas: tidak ada lagi status yang memetakan ke sana sejak
     * putaran perbaikan internal ditampilkan sebagai "Dalam Proses", dan
     * legend dengan potongan yang selamanya nol hanya menyisakan pertanyaan.
     */
    public const KELOMPOK = ['Dalam Proses', 'Menunggu Persetujuan', 'Tolak', 'Selesai'];

    /**
     * Tab filter pada daftar Portal Pemohon — sengaja lebih ringkas daripada
     * `KELOMPOK`.
     *
     * Bagi pemohon yang penting cuma "masih berjalan" atau "sudah tuntas";
     * Revisi, Menunggu Persetujuan, dan Tolak adalah tahapan internal yang
     * kalau dijadikan tab justru menyembunyikan barisnya di tempat yang tidak
     * ia duga. Status lengkapnya tetap tampil sebagai label di tiap baris.
     */
    public const KELOMPOK_PORTAL = ['Dalam Proses', 'Selesai'];

    /**
     * Label kelompok besar → label status rinci yang masuk ke dalamnya.
     * Segala yang berakhir (termasuk yang ditolak) dihitung tuntas.
     */
    private const PETA_PORTAL = [
        'Dalam Proses' => ['Dalam Proses', 'Menunggu Persetujuan'],
        'Selesai' => ['Selesai', 'Tolak'],
    ];

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
        // Dokumen Informasi Publik yang diminta, bila permohonannya berangkat
        // dari satu dokumen tertentu (langkah 83). Kosong untuk permohonan
        // biasa, tempat pemohon menuliskan sendiri informasi yang dicarinya.
        'informasi_publik_id',
        'rincian_informasi',
        'tujuan_penggunaan',
        'cara_memperoleh',
        'format_informasi',
        'cara_pengiriman',
        'jalur_pelayanan',
        'status',
        'tanggal_permohonan',
        'batas_waktu_tanggapan',
        'batas_waktu_awal',
        'tampil_di_register_publik',
    ];

    protected $casts = [
        'tampil_di_register_publik' => 'boolean',
        'tanggal_permohonan'        => 'datetime',
        'tanggal_tanggapan'         => 'datetime',
        'batas_waktu_tanggapan'     => 'datetime',
        'batas_waktu_awal'          => 'datetime',
        'diperpanjang_pada'         => 'datetime',
        'jadwal_layanan'            => 'datetime',
    ];

    /**
     * Status yang berarti permohonannya sudah tuntas ditangani.
     *
     * Dipakai portal untuk menentukan permohonan mana yang boleh dikeberatankan
     * (langkah 89). Penolakan dan kedaluwarsa ikut, bukan kelalaian: keduanya
     * justru alasan keberatan yang paling sering — permintaan ditolak, dan
     * permintaan tidak ditanggapi sampai tenggatnya lewat.
     */
    public const STATUS_SELESAI = ['selesai', 'disetujui', 'ditolak', 'ditolak_sebagian', 'kedaluwarsa'];

    /**
     * Permohonan ini punya dasar untuk dikeberatankan?
     *
     * Yang menentukan bukan "sudah selesai", melainkan apakah salah satu dari
     * tujuh dasar Pasal 35 UU KIP bisa berlaku atasnya (langkah 101).
     * Membatasinya pada permohonan yang tuntas justru menutup dua dasar yang
     * paling sering dipakai — **tidak ditanggapinya permintaan informasi** dan
     * **penyampaian informasi melebihi waktu yang diatur** — karena keduanya
     * baru muncul ketika permohonan **belum** ditanggapi sampai tenggatnya
     * lewat, dan berkas semacam itu statusnya masih berjalan.
     *
     * Dua pintu karena itu, bukan satu:
     *
     *  - Penanganannya sudah tuntas — apa pun hasilnya. Ditolak, ditolak
     *    sebagian, selesai, maupun kedaluwarsa sama-sama punya dasar.
     *  - Masih berjalan tetapi tenggat tanggapannya sudah lewat.
     */
    public function layakDikeberatankan(): bool
    {
        if (in_array($this->status, self::STATUS_SELESAI, true)) {
            return true;
        }

        return $this->batas_waktu_tanggapan !== null && $this->batas_waktu_tanggapan->isPast();
    }

    /**
     * Tonggak alur versi pemohon: `diajukan`, `diproses`, atau `selesai`.
     *
     * Tiga langkah, selalu tiga (langkah 101). Seluruh perpindahan internal —
     * diverifikasi, revisi, jenjang persetujuan yang berputar berkali-kali —
     * menyatu di **Diproses**. Yang perlu diketahui pemohon cuma di mana
     * berkasnya berada; menampilkan tiap perpindahan membuat langkahnya
     * bertambah-kurang tiap kali petugas bekerja, dan perbaikan pekerjaan
     * petugas terbaca sebagai masalah pada berkas pemohon sendiri.
     */
    public function tahapAlurPortal(): string
    {
        if (in_array($this->status, self::STATUS_SELESAI, true)) {
            return 'selesai';
        }

        return $this->status === 'diajukan' ? 'diajukan' : 'diproses';
    }

    /**
     * Tanggal tiap tonggak, untuk ditempelkan pada langkahnya.
     *
     * "Diproses" diambil dari perpindahan status pertama yang meninggalkan
     * `diajukan`, bukan dari `updated_at`: kolom itu ikut berubah tiap kali
     * petugas menyentuh barisnya, jadi tanggalnya akan bergeser maju terus
     * selama berkasnya masih ditangani.
     *
     * @return array<string, \Illuminate\Support\Carbon|null>
     */
    public function tanggalAlurPortal(): array
    {
        $mulaiDiproses = $this->logStatus
            ->first(fn ($log) => $log->status_baru !== 'diajukan');

        return [
            'diajukan' => $this->tanggal_permohonan,
            'diproses' => $mulaiDiproses?->created_at,
            'selesai' => $this->tanggalSelesaiPortal(),
        ];
    }

    /**
     * Tanggal permohonannya benar-benar selesai.
     *
     * Diambil dari perpindahan status ke **Selesai**, bukan dari kolom
     * `tanggal_tanggapan` (langkah 101). Kolom itu diisi begitu berkasnya
     * berpindah ke status akhir mana pun dan tidak pernah ditulis ulang, jadi
     * baris yang sempat lewat status akhir lain lebih dulu membawa tanggal yang
     * bukan tanggal selesainya. Kolomnya tetap dipakai sebagai cadangan untuk
     * baris lama yang log statusnya tidak lengkap.
     */
    public function tanggalSelesaiPortal(): ?\Illuminate\Support\Carbon
    {
        $log = $this->logStatus->first(fn ($satu) => $satu->status_baru === 'selesai')
            ?? $this->logStatus->first(fn ($satu) => in_array($satu->status_baru, self::STATUS_SELESAI, true));

        return $log?->created_at ?? $this->tanggal_tanggapan;
    }

    /** Permohonan yang punya dasar keberatan; lihat {@see layakDikeberatankan()}. */
    public function scopeLayakDikeberatankan($query)
    {
        return $query->where(fn ($q) => $q
            ->whereIn('status', self::STATUS_SELESAI)
            ->orWhere(fn ($lewat) => $lewat
                ->whereNotNull('batas_waktu_tanggapan')
                ->where('batas_waktu_tanggapan', '<', now())));
    }

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

    /** Dokumen Informasi Publik yang diminta, bila permohonannya menunjuk satu. */
    public function dokumen(): BelongsTo
    {
        return $this->belongsTo(InformasiPublik::class, 'informasi_publik_id');
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

    /** Dokumen jawaban dari petugas; ditulis panel admin, dibaca portal. */
    public function tanggapanFiles(): HasMany
    {
        return $this->hasMany(PermohonanTanggapanFile::class, 'permohonan_id');
    }

    /**
     * Berkas tanggapan sudah boleh dilihat pemohon.
     *
     * Petugas melampirkan dokumennya jauh sebelum permohonan diputus — saat
     * menyiapkan jawaban, sementara PPID belum menyetujui. Menampilkannya sejak
     * saat itu berarti menyerahkan jawaban yang belum disahkan siapa pun.
     *
     * Daftarnya sama persis dengan yang dipakai api-ppid untuk menentukan kapan
     * pemberitahuan berkas tanggapan dikirim
     * (`PermohonanController::statusTerbukaUntukPemohon()`). Penolakan tidak
     * termasuk: yang disampaikan di sana alasannya, bukan dokumennya.
     */
    public function tanggapanTerbukaUntukPemohon(): bool
    {
        return in_array($this->status, ['disetujui', 'selesai'], true);
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
     * Dipakai grafik dashboard.
     */
    public static function statusKelompok(string $label): array
    {
        return array_keys(array_filter(self::STATUS_LABEL, fn ($nilai) => $nilai === $label));
    }

    /**
     * Status mentah untuk satu tab Portal Pemohon.
     *
     * Diturunkan dari `STATUS_LABEL`, bukan didaftar ulang: status baru yang
     * kelak ditambahkan cukup diberi label, dan tab-nya ikut sendiri.
     */
    public static function statusKelompokPortal(string $label): array
    {
        $rinci = self::PETA_PORTAL[$label] ?? [];

        return array_keys(array_filter(
            self::STATUS_LABEL,
            fn ($nilai) => in_array($nilai, $rinci, true)
        ));
    }
}
