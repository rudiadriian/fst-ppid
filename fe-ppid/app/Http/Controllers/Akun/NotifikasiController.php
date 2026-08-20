<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\NotifikasiPemohon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

/**
 * Lonceng notifikasi Portal Pemohon.
 *
 * Isinya umpan balik petugas: perpindahan status permohonan/keberatan beserta
 * catatannya, berkas tanggapan yang dilampirkan, dan hasil Verifikasi Data
 * Diri. Barisnya ditulis api-ppid; di sini hanya dibaca dan ditandai.
 *
 * Loncengnya menarik data lewat `index()` secara berkala, jadi jawabannya
 * dibuat sekecil mungkin — hanya jumlah belum dibaca dan 20 baris terbaru.
 */
class NotifikasiController extends Controller
{
    /**
     * Isi lonceng: hanya yang belum dibaca.
     *
     * Notifikasi yang sudah dibuka bukan lagi pemberitahuan — kalau ia tetap
     * tinggal di daftar, pemohon harus mengingat sendiri mana yang sudah
     * dilihat. Riwayatnya tidak hilang: `/akun/notifikasi` memuat semuanya.
     */
    public function index(): JsonResponse
    {
        $pemohonId = (int) Auth::guard('pemohon')->id();

        $daftar = NotifikasiPemohon::milik($pemohonId)
            ->where('is_read', false)
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->limit(NotifikasiPemohon::BATAS_TAMPIL)
            ->get();

        return response()->json([
            'belum_dibaca' => NotifikasiPemohon::milik($pemohonId)->where('is_read', false)->count(),
            'daftar' => $daftar->map->untukLonceng()->all(),
        ]);
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     *
     * Dipanggil saat barisnya diklik. `where` pemiliknya bukan sekadar
     * penyaring: tanpa itu id milik akun lain ikut bisa ditandai.
     */
    public function baca(int $notifikasi): JsonResponse
    {
        NotifikasiPemohon::milik((int) Auth::guard('pemohon')->id())
            ->whereKey($notifikasi)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    public function bacaSemua(): JsonResponse
    {
        NotifikasiPemohon::milik((int) Auth::guard('pemohon')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['ok' => true]);
    }

    /**
     * Halaman penuh untuk pemohon yang notifikasinya lebih dari yang muat di
     * lonceng — sekaligus jalan keluar bila JavaScript-nya gagal dimuat.
     */
    public function halaman(): \Illuminate\View\View
    {
        $notifikasi = NotifikasiPemohon::milik((int) Auth::guard('pemohon')->id())
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(20);

        return view('akun.notifikasi', compact('notifikasi'));
    }

    /**
     * Buka satu notifikasi dari halaman penuh: ditandai dibaca lalu diantar ke
     * tujuannya. Lewat server, bukan JavaScript, supaya barisnya tetap bisa
     * ditandai pada peramban yang skripnya gagal dimuat.
     */
    public function buka(int $notifikasi): RedirectResponse
    {
        $baris = NotifikasiPemohon::milik((int) Auth::guard('pemohon')->id())
            ->whereKey($notifikasi)
            ->firstOrFail();

        if (!$baris->is_read) {
            $baris->forceFill(['is_read' => true])->save();
        }

        $tujuan = $baris->untukLonceng()['tautan'];

        return redirect($tujuan ?? route('akun.notifikasi'));
    }

    /** Dipakai tombol "Tandai semua dibaca" pada halaman penuh (tanpa JS). */
    public function bacaSemuaHalaman(): RedirectResponse
    {
        NotifikasiPemohon::milik((int) Auth::guard('pemohon')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return redirect()->route('akun.notifikasi')
            ->with('status', __('Semua notifikasi ditandai sudah dibaca.'));
    }
}
