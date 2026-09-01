<?php

namespace App\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

/**
 * Tenggat layanan informasi publik, di satu tempat (langkah 89).
 *
 * Salinan sengaja dari `App\Support\SlaLayanan` di api-ppid. Kedua aplikasi
 * berdiri sendiri — portal pemohon tidak memanggil panel untuk menghitung
 * tenggat — jadi angkanya harus ada di keduanya. Yang tidak boleh terjadi
 * adalah angka itu berserak di dalam controller: sebelum berkas ini ada,
 * `addWeekdays(10)` dan `addDays(30)` masing-masing berdiri sendiri di
 * `PermohonanController` dan `KeberatanController`, dan halaman prosedur
 * publik menyebut angka ketiga yang tidak berhubungan dengan keduanya.
 *
 * Dua satuan waktu, karena undang-undangnya membedakannya:
 *
 *   - **hari kerja** — permohonan (10 + 7) dan batas sengketa (14).
 *   - **hari kalender** — tanggapan keberatan (30).
 *
 * Hari libur nasional belum dikecualikan; akibatnya tenggat di sini bisa lebih
 * ketat daripada tenggat sebenarnya — arah yang aman, karena menagih petugas
 * lebih awal, bukan terlambat.
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
}
