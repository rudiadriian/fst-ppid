<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Histori Permohonan — seluruh pengajuan akun beserta jejak perubahan
 * statusnya, disusun dari yang terbaru.
 */
class HistoriController extends Controller
{
    public function __invoke(): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        $permohonan = PermohonanInformasi::with(['logStatus' => fn ($q) => $q->orderBy('created_at'), 'survei', 'keberatan'])
            ->where('pemohon_id', $pemohon->id)
            ->orderByDesc('tanggal_permohonan')
            ->orderByDesc('id')
            ->get();

        $keberatan = KeberatanInformasi::with('permohonan')
            ->where('pemohon_id', $pemohon->id)
            ->orderByDesc('tanggal_keberatan')
            ->get();

        return view('akun.histori', compact('pemohon', 'permohonan', 'keberatan'));
    }
}
