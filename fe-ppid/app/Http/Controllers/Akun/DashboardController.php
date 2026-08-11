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
    public function __invoke(): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        $permohonan = PermohonanInformasi::where('pemohon_id', $pemohon->id)->get();
        $keberatan = KeberatanInformasi::where('pemohon_id', $pemohon->id)->get();

        return view('akun.dashboard', [
            'pemohon' => $pemohon,
            'ringkasanPermohonan' => $this->ringkasan($permohonan, PermohonanInformasi::STATUS_LABEL),
            'ringkasanKeberatan' => $this->ringkasan($keberatan, KeberatanInformasi::STATUS_LABEL),
            'totalPermohonan' => $permohonan->count(),
            'totalKeberatan' => $keberatan->count(),
            'grafik' => $this->grafikBulanan($permohonan),
            'bulan' => $this->labelBulan(),
        ]);
    }

    /** Hitung jumlah per kelompok status ("Dalam Proses", "Revisi", …). */
    private function ringkasan($baris, array $peta): array
    {
        $hasil = array_fill_keys(PermohonanInformasi::KELOMPOK, 0);

        foreach ($baris as $item) {
            $label = $peta[$item->status] ?? null;

            if ($label && array_key_exists($label, $hasil)) {
                $hasil[$label]++;
            }
        }

        return $hasil;
    }

    /**
     * Data grafik batang bertumpuk: 12 bulan terakhir × 5 kelompok status.
     * Digambar langsung dengan HTML/CSS di view, tanpa pustaka luar.
     */
    private function grafikBulanan($permohonan): array
    {
        $mulai = Carbon::now()->startOfMonth()->subMonths(11);

        $kerangka = [];

        for ($i = 0; $i < 12; $i++) {
            $bulan = $mulai->copy()->addMonths($i);
            $kerangka[$bulan->format('Y-m')] = [
                'label' => $bulan->translatedFormat('M y'),
                'nilai' => array_fill_keys(PermohonanInformasi::KELOMPOK, 0),
                'total' => 0,
            ];
        }

        foreach ($permohonan as $item) {
            $tanggal = $item->tanggal_permohonan ?? $item->created_at;

            if (!$tanggal) {
                continue;
            }

            $kunci = $tanggal->format('Y-m');

            if (!isset($kerangka[$kunci])) {
                continue; // di luar 12 bulan terakhir
            }

            $label = PermohonanInformasi::STATUS_LABEL[$item->status] ?? null;

            if ($label && isset($kerangka[$kunci]['nilai'][$label])) {
                $kerangka[$kunci]['nilai'][$label]++;
                $kerangka[$kunci]['total']++;
            }
        }

        return array_values($kerangka);
    }

    private function labelBulan(): string
    {
        return Carbon::now()->startOfMonth()->subMonths(11)->translatedFormat('F Y')
            .' – '.Carbon::now()->translatedFormat('F Y');
    }
}
