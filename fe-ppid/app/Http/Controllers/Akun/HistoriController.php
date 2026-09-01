<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Histori Permohonan — seluruh pengajuan akun beserta jejak perubahan
 * statusnya, disusun dari yang terbaru.
 */
class HistoriController extends Controller
{
    public function __invoke(Request $request): View
    {
        $pemohon = Auth::guard('pemohon')->user();
        $cari = trim($request->string('cari')->toString());

        $permohonan = PermohonanInformasi::with(['logStatus' => fn ($q) => $q->orderBy('created_at'), 'survei', 'keberatan'])
            ->where('pemohon_id', $pemohon->id)
            ->when($cari !== '', fn ($q) => $q->where('kode_permohonan', 'ilike', '%'.$cari.'%'))
            ->orderByDesc('tanggal_permohonan')
            ->orderByDesc('id')
            ->get();

        $keberatan = KeberatanInformasi::with('permohonan')
            ->where('pemohon_id', $pemohon->id)
            /*
             * Dicocokkan dengan nomor keberatannya sendiri maupun nomor
             * permohonan yang dikeberatankan — pemohon bisa mencari lewat
             * keduanya. Nomor urut barisnya ikut dicocokkan bila yang diketik
             * memang angka, supaya nomor pada tautan notifikasi tetap ketemu.
             */
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('kode_keberatan', 'ilike', '%'.$cari.'%')
                        ->orWhereHas('permohonan', fn ($p) => $p->where('kode_permohonan', 'ilike', '%'.$cari.'%'));

                    if (ctype_digit($cari)) {
                        $sub->orWhere('id', (int) $cari);
                    }
                });
            })
            ->orderByDesc('tanggal_keberatan')
            ->get();

        return view('akun.histori', compact('pemohon', 'permohonan', 'keberatan', 'cari'));
    }
}
