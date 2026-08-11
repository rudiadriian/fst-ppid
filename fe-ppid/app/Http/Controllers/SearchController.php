<?php

namespace App\Http\Controllers;

use App\Models\InformasiDikecualikan;
use App\Models\LaporanLayanan;
use App\Models\Regulasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SearchController extends Controller
{
    /** Halaman hasil pencarian. */
    public function index(Request $request)
    {
        $query   = trim((string) $request->input('query'));
        $results = strlen($query) >= 3 ? $this->cari($query, 30) : [];

        return view('ppid.search_results', [
            'query'   => $query,
            'results' => $results,
        ]);
    }

    /** Suggestion untuk kotak pencarian (JSON). */
    public function suggestions(Request $request)
    {
        $query = trim((string) $request->input('query'));

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        return response()->json($this->cari($query, 6));
    }

    /**
     * Cari di regulasi, laporan layanan, dan informasi dikecualikan.
     * Kegagalan DB tidak boleh menjatuhkan halaman — kembalikan hasil kosong.
     */
    private function cari(string $query, int $limit): array
    {
        $like = '%' . $query . '%';

        try {
            $regulasi = Regulasi::where('judul', 'ilike', $like)
                ->orderByDesc('tahun')
                ->limit($limit)
                ->get()
                ->map(fn ($row) => [
                    'title'    => $row->judul,
                    'kategori' => $row->kategori === 'dasar_hukum_ppid' ? 'Dasar Hukum' : 'Regulasi',
                    // Dasar hukum kini ikut tayang di halaman Regulasi, jadi
                    // seluruh hasil regulasi menuju satu halaman yang sama.
                    'url'      => route('ppid.regulation'),
                ]);

            $laporan = LaporanLayanan::published()
                ->where('judul', 'ilike', $like)
                ->orderByDesc('tahun')
                ->limit($limit)
                ->get()
                ->map(fn ($row) => [
                    'title'    => $row->judul,
                    'kategori' => 'Laporan',
                    'url'      => route('ppid.report', $row->tipe_laporan === 'statistik_informasi'
                        ? 'statistik-informasi'
                        : 'pelayanan-informasi'),
                ]);

            $dikecualikan = InformasiDikecualikan::published()
                ->where('judul', 'ilike', $like)
                ->limit($limit)
                ->get()
                ->map(fn ($row) => [
                    'title'    => $row->judul,
                    'kategori' => 'Informasi Dikecualikan',
                    'url'      => route('ppid.excluded'),
                ]);

            return $regulasi->merge($laporan)->merge($dikecualikan)->take($limit)->values()->all();
        } catch (\Throwable $e) {
            Log::warning('[PPID] Pencarian gagal: ' . $e->getMessage());

            return [];
        }
    }
}
