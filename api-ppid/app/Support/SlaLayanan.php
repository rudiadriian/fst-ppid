<?php

namespace App\Support;

use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/**
 * Tenggat layanan informasi publik, di satu tempat.
 *
 * Angka-angkanya berasal dari UU No. 14 Tahun 2008 dan turunannya, bukan dari
 * kebijakan internal, jadi ia ditulis sekali di sini dan dibaca ulang oleh
 * portal pemohon, panel, dashboard, dan surel. Sebelumnya `addWeekdays(10)`
 * berdiri sendiri di controller portal — satu-satunya tempat yang tahu
 * tenggatnya, dan tidak ada yang tahu kapan tenggat itu diperpanjang.
 *
 * Bedakan dua satuan waktu, karena undang-undangnya sendiri membedakannya:
 *
 *   - **hari kerja** — permohonan (10 + 7) dan batas sengketa (14). Akhir pekan
 *     tidak dihitung.
 *   - **hari kalender** — tanggapan keberatan (30). Akhir pekan ikut dihitung.
 *
 * Hari libur nasional belum ikut dikecualikan: daftarnya berubah tiap tahun dan
 * belum ada sumbernya di sistem. Akibatnya tenggat yang dihitung di sini bisa
 * lebih ketat daripada tenggat sebenarnya — arah yang aman, karena membuat
 * petugas ditagih lebih awal, bukan terlambat.
 */
class SlaLayanan
{
    /** Tanggapan permohonan: 10 hari kerja sejak diajukan (Pasal 22 UU KIP). */
    public const PERMOHONAN_HARI_KERJA = 10;

    /** Perpanjangan permohonan: paling lama 7 hari kerja, sekali. */
    public const PERPANJANGAN_HARI_KERJA = 7;

    /** Tanggapan keberatan: 30 hari sejak keberatan diregistrasi. */
    public const KEBERATAN_HARI = 30;

    /** Batas pemohon membawa perkara ke Komisi Informasi: 14 hari kerja. */
    public const SENGKETA_HARI_KERJA = 14;

    /** Ambang "segera jatuh tempo", dalam hari. */
    public const AMBANG_SIAGA_HARI = 3;

    public static function batasPermohonan(?CarbonInterface $mulai = null): Carbon
    {
        return Carbon::instance(($mulai ? Carbon::instance($mulai) : now())->copy())
            ->addWeekdays(self::PERMOHONAN_HARI_KERJA);
    }

    public static function batasKeberatan(?CarbonInterface $mulai = null): Carbon
    {
        return Carbon::instance(($mulai ? Carbon::instance($mulai) : now())->copy())
            ->addDays(self::KEBERATAN_HARI);
    }

    public static function batasSengketa(?CarbonInterface $tanggapan = null): Carbon
    {
        return Carbon::instance(($tanggapan ? Carbon::instance($tanggapan) : now())->copy())
            ->addWeekdays(self::SENGKETA_HARI_KERJA);
    }

    public static function perpanjang(CarbonInterface $batasSekarang): Carbon
    {
        return Carbon::instance($batasSekarang->copy())->addWeekdays(self::PERPANJANGAN_HARI_KERJA);
    }

    /**
     * Keadaan tenggat satu pengajuan, siap ditampilkan.
     *
     * Mengembalikan `null` bila pengajuannya belum punya tenggat — itu keadaan
     * yang sah (pengajuan yang sudah selesai sebelum kolom tenggatnya ada),
     * dan memaksakan angka di situ hanya melahirkan lencana palsu.
     *
     * @return array{keadaan: string, batas: string, sisa_hari: int, terlambat_hari: int, diperpanjang: bool, label: string}|null
     */
    public static function keadaan(Model $pengajuan): ?array
    {
        $batas = $pengajuan->batas_waktu_tanggapan ?? null;

        if (blank($batas)) {
            return null;
        }

        $batas = Carbon::instance($batas);
        $selesai = $pengajuan->tanggal_tanggapan ? Carbon::instance($pengajuan->tanggal_tanggapan) : null;
        $diperpanjang = filled($pengajuan->diperpanjang_pada ?? null);

        // Pengajuan yang sudah ditanggapi dinilai memakai tanggal tanggapannya,
        // bukan tanggal hari ini: keterlambatan yang sudah terjadi tidak boleh
        // ikut tumbuh setiap hari setelah perkaranya ditutup.
        $acuan = $selesai ?? now();
        $selisihHari = $acuan->diffInDays($batas, false);

        $keadaan = match (true) {
            $selesai !== null && $acuan->lte($batas) => 'tepat_waktu',
            $selesai !== null => 'terlambat',
            $acuan->gt($batas) => 'lewat_tenggat',
            $selisihHari <= self::AMBANG_SIAGA_HARI => 'segera',
            default => 'aman',
        };

        return [
            'keadaan' => $keadaan,
            'batas' => $batas->toIso8601String(),
            'sisa_hari' => max(0, (int) $selisihHari),
            'terlambat_hari' => $selisihHari < 0 ? (int) abs($selisihHari) : 0,
            'diperpanjang' => $diperpanjang,
            'label' => match ($keadaan) {
                'tepat_waktu' => 'Ditanggapi tepat waktu',
                'terlambat' => 'Ditanggapi melewati tenggat',
                'lewat_tenggat' => 'Lewat tenggat',
                'segera' => 'Segera jatuh tempo',
                default => 'Dalam tenggat',
            },
        ];
    }

    /**
     * Boleh diperpanjang hanya bila belum pernah, dan perkaranya masih berjalan.
     *
     * Undang-undangnya memberi satu kali perpanjangan; membiarkan tombolnya
     * bisa ditekan dua kali berarti membiarkan tenggat resmi digeser tanpa
     * batas.
     */
    public static function bolehDiperpanjang(PermohonanInformasi $permohonan): bool
    {
        return blank($permohonan->diperpanjang_pada)
            && blank($permohonan->tanggal_tanggapan)
            && !in_array($permohonan->status, ['selesai', 'ditolak', 'kedaluwarsa'], true);
    }

    /** Keberatan tidak mengenal perpanjangan — 30 harinya mutlak. */
    public static function bolehDiperpanjangKeberatan(KeberatanInformasi $keberatan): bool
    {
        return false;
    }
}
