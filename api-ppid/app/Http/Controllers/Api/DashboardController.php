<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use App\Models\InformasiPublik;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use App\Models\SurveyKepuasan;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Angka ringkasan untuk halaman Dashboard panel admin.
 */
class DashboardController extends Controller
{
    public function ringkasan(): JsonResponse
    {
        $statusPermohonan = PermohonanInformasi::query()
            ->select('status', DB::raw('count(*) as jumlah'))
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $terlambat = PermohonanInformasi::query()
            ->whereNotNull('batas_waktu_tanggapan')
            ->where('batas_waktu_tanggapan', '<', now())
            ->whereNotIn('status', ['selesai', 'ditolak', 'ditolak_sebagian', 'kedaluwarsa'])
            ->count();

        $rataRating = SurveyKepuasan::avg('rating');

        // Tren 6 bulan terakhir untuk grafik batang di dashboard.
        $tren = PermohonanInformasi::query()
            ->where('tanggal_permohonan', '>=', now()->startOfMonth()->subMonths(5))
            ->select(
                DB::raw("to_char(date_trunc('month', tanggal_permohonan), 'YYYY-MM') as bulan"),
                DB::raw('count(*) as jumlah')
            )
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        return response()->json([
            'data' => [
                'permohonan' => [
                    'total' => (int) $statusPermohonan->sum(),
                    'per_status' => $statusPermohonan,
                    'perlu_tindakan' => (int) ($statusPermohonan['diajukan'] ?? 0)
                        + (int) ($statusPermohonan['diverifikasi'] ?? 0)
                        + (int) ($statusPermohonan['diproses'] ?? 0),
                    'menunggu_approval' => (int) ($statusPermohonan['menunggu_approval'] ?? 0),
                    'lewat_batas_waktu' => $terlambat,
                ],
                'keberatan' => [
                    'total' => KeberatanInformasi::count(),
                    'belum_selesai' => KeberatanInformasi::where('status', '!=', 'selesai')->count(),
                ],
                'konten' => [
                    'informasi_publik' => InformasiPublik::count(),
                    'informasi_publik_published' => InformasiPublik::where('status', 'published')->count(),
                    'berita' => Berita::count(),
                    'berita_draft' => Berita::where('status', 'draft')->count(),
                ],
                'kepuasan' => [
                    'jumlah_responden' => SurveyKepuasan::count(),
                    'rata_rating' => $rataRating !== null ? round((float) $rataRating, 2) : null,
                    'persen' => $rataRating !== null ? round(((float) $rataRating / 5) * 100) : null,
                ],
                'tren_permohonan' => $tren,
            ],
        ]);
    }
}
