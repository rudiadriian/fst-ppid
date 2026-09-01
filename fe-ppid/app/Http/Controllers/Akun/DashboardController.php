<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Beranda Portal Pengguna.
 *
 * Menampilkan ringkasan permohonan & keberatan milik akun, peringatan bila
 * Data Pemohon belum diverifikasi, dan grafik pengajuan per bulan.
 */
class DashboardController extends Controller
{
    /** Tahun berjalan dibandingkan dengan paling banyak tiga tahun sebelumnya. */
    private const TAHUN_PEMBANDING = 3;

    public function __invoke(): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        // `survei` ikut dimuat sekali di sini: ia dipakai menyaring permohonan
        // tuntas yang belum dinilai, dan tanpa eager load penyaringnya menembak
        // satu query per permohonan.
        $permohonan = PermohonanInformasi::with('survei')
            ->where('pemohon_id', $pemohon->id)
            ->get();

        $keberatan = KeberatanInformasi::where('pemohon_id', $pemohon->id)->get();

        $tahun = $this->tahunDibandingkan($permohonan);

        return view('akun.dashboard', [
            'pemohon' => $pemohon,
            'ringkasanPermohonan' => $this->ringkasan($permohonan, PermohonanInformasi::STATUS_LABEL),
            'ringkasanKeberatan' => $this->ringkasan($keberatan, KeberatanInformasi::STATUS_LABEL),
            'totalPermohonan' => $permohonan->count(),
            'totalKeberatan' => $keberatan->count(),
            'grafik' => $this->grafikBulanan($permohonan, $tahun),
            'tahunGrafik' => $tahun,
            'totalPerTahun' => $this->totalPerTahun($permohonan, $tahun),
            'surveiTertunda' => $this->surveiTertunda($permohonan),
        ]);
    }

    /**
     * Permohonan yang sudah tuntas tetapi belum dinilai pemohonnya.
     *
     * Survei kepuasan tidak dikirim lewat surel maupun lonceng: mengejar
     * pemohon dengan pemberitahuan untuk sesuatu yang sifatnya sukarela hanya
     * menambah bising pada kotak masuk yang sudah dipakai memberitahu jalannya
     * permohonan. Yang dilakukan cukup menandainya di beranda portal — tempat
     * yang memang dibuka pemohon saat menengok permohonannya.
     *
     * Yang terbaru di atas: penilaian paling berarti diberikan selagi
     * layanannya masih diingat.
     *
     * @return \Illuminate\Support\Collection<int, PermohonanInformasi>
     */
    private function surveiTertunda($permohonan)
    {
        return $permohonan
            ->filter(fn (PermohonanInformasi $item) => $item->bolehDisurvei() && $item->survei === null)
            ->sortByDesc(fn (PermohonanInformasi $item) => $item->tanggal_tanggapan ?? $item->updated_at ?? $item->created_at)
            ->values();
    }

    /**
     * Jumlah pengajuan per kelompok status Portal: Dalam Proses & Selesai.
     *
     * Memakai `KELOMPOK_PORTAL`, bukan lima kelompok penuh — tahapan internal
     * seperti Revisi dan Menunggu Persetujuan tidak berarti apa-apa bagi
     * pemohon, dan angkanya sudah tercakup di "Dalam Proses". Pengelompokannya
     * sama persis dengan tab pada daftar Permohonan & Keberatan.
     */
    private function ringkasan($baris, array $peta): array
    {
        $hasil = array_fill_keys(PermohonanInformasi::KELOMPOK_PORTAL, 0);

        // Label status rinci → kelompok portal, dihitung sekali di muka.
        $keKelompok = [];

        foreach (PermohonanInformasi::KELOMPOK_PORTAL as $kelompok) {
            foreach (PermohonanInformasi::statusKelompokPortal($kelompok) as $status) {
                $keKelompok[PermohonanInformasi::STATUS_LABEL[$status] ?? $status] = $kelompok;
            }
        }

        foreach ($baris as $item) {
            $label = $peta[$item->status] ?? null;
            $kelompok = $label ? ($keKelompok[$label] ?? null) : null;

            if ($kelompok !== null) {
                $hasil[$kelompok]++;
            }
        }

        return $hasil;
    }

    /**
     * Tahun yang ikut digambar: tahun berjalan + paling banyak tiga tahun
     * sebelumnya, dan hanya tahun yang benar-benar punya pengajuan.
     *
     * Tahun berjalan selalu ikut walau masih kosong — grafik yang hilang
     * seluruh sumbunya lebih membingungkan daripada grafik yang datar.
     */
    private function tahunDibandingkan($permohonan): array
    {
        $sekarang = (int) Carbon::now()->format('Y');
        $terawal = $sekarang - self::TAHUN_PEMBANDING;

        $adaData = $permohonan
            ->map(fn ($item) => optional($item->tanggal_permohonan ?? $item->created_at)->format('Y'))
            ->filter()
            ->map(fn ($tahun) => (int) $tahun)
            ->filter(fn ($tahun) => $tahun >= $terawal && $tahun <= $sekarang)
            ->all();

        $tahun = array_unique(array_merge([$sekarang], $adaData));

        rsort($tahun);

        return array_values($tahun);
    }

    /**
     * Data grafik: 12 bulan (Januari–Desember) × satu seri per tahun.
     *
     * Sumbu bulannya tetap, jadi Januari satu tahun berdiri sejajar dengan
     * Januari tahun lain — itu inti perbandingannya. Digambar dengan HTML/CSS
     * di view, tanpa pustaka luar.
     */
    private function grafikBulanan($permohonan, array $tahun): array
    {
        $kerangka = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $kerangka[$bulan] = [
                'label' => Carbon::create(null, $bulan, 1)->translatedFormat('M'),
                'nilai' => array_fill_keys($tahun, 0),
            ];
        }

        foreach ($permohonan as $item) {
            $tanggal = $item->tanggal_permohonan ?? $item->created_at;

            if (!$tanggal) {
                continue;
            }

            $th = (int) $tanggal->format('Y');
            $bl = (int) $tanggal->format('n');

            if (isset($kerangka[$bl]['nilai'][$th])) {
                $kerangka[$bl]['nilai'][$th]++;
            }
        }

        return array_values($kerangka);
    }

    /** Total setahun penuh per tahun, dipakai legend grafik. */
    private function totalPerTahun($permohonan, array $tahun): array
    {
        $hasil = array_fill_keys($tahun, 0);

        foreach ($permohonan as $item) {
            $tanggal = $item->tanggal_permohonan ?? $item->created_at;
            $th = $tanggal ? (int) $tanggal->format('Y') : null;

            if ($th !== null && isset($hasil[$th])) {
                $hasil[$th]++;
            }
        }

        return $hasil;
    }
}
