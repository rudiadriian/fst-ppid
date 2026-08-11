<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\PermohonanInformasi;
use App\Models\SurveyKepuasan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Survei kepuasan layanan informasi publik.
 *
 * Hanya pemilik permohonan yang boleh menilai, hanya untuk permohonan yang
 * sudah tuntas ditangani, dan satu permohonan hanya bisa dinilai sekali.
 * Rata-rata nilainya menjadi angka "Kepuasan" di Laporan Statistik.
 */
class SurveiController extends Controller
{
    public function create(int $permohonan): View|RedirectResponse
    {
        $permohonan = $this->permohonanMilikSendiri($permohonan);

        if ($permohonan->survei) {
            return redirect()->route('akun.dashboard')
                ->with('status', __('Permohonan ini sudah pernah Anda nilai.'));
        }

        return view('akun.survei', compact('permohonan'));
    }

    public function store(Request $request, int $permohonan): RedirectResponse
    {
        $permohonan = $this->permohonanMilikSendiri($permohonan);

        if ($permohonan->survei) {
            return redirect()->route('akun.dashboard')
                ->with('status', __('Permohonan ini sudah pernah Anda nilai.'));
        }

        $data = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'komentar' => ['nullable', 'string', 'max:1000'],
        ]);

        SurveyKepuasan::create([
            'permohonan_id' => $permohonan->id,
            'rating' => $data['rating'],
            'komentar' => $data['komentar'] ?? null,
        ]);

        return redirect()->route('akun.dashboard')
            ->with('status', __('Terima kasih, penilaian Anda sudah kami terima.'));
    }

    /** Permohonan wajib milik akun yang sedang masuk dan sudah tuntas ditangani. */
    private function permohonanMilikSendiri(int $id): PermohonanInformasi
    {
        $pemohon = Auth::guard('pemohon')->user();

        $permohonan = PermohonanInformasi::with('survei')
            ->where('pemohon_id', $pemohon->id)
            ->findOrFail($id);

        abort_unless($permohonan->bolehDisurvei(), 403, __('Permohonan ini belum selesai ditangani.'));

        return $permohonan;
    }
}
