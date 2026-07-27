<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function suggestions(Request $request)
    {
        $query = $request->input('query');

        if (strlen($query) < 3) {
            return response()->json([]);
        }

        // Logika Pencarian Sebenarnya:
        // 1. Cari di tabel 'reports' (Laporan Keuangan)
        $reports = \App\Models\Report::where('title', 'like', "%{$query}%")
                                    ->select('title')
                                    ->limit(3)->get()
                                    ->map(fn($item) => ['title' => $item->title, 'url' => route('reports.show', $item)]);

        // 2. Cari di tabel 'regulations' (Regulasi)
        $regulations = \App\Models\Regulation::where('title', 'like', "%{$query}%")
                                            ->select('title')
                                            ->limit(3)->get()
                                            ->map(fn($item) => ['title' => $item->title, 'url' => route('regulations.show', $item)]);

        // Gabungkan hasil dan batasi total suggestion
        $results = $reports->merge($regulations)->take(6);

        return response()->json($results);
    }
}
