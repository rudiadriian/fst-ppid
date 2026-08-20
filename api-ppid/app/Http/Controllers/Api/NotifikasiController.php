<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Notifikasi milik pengguna yang sedang login.
 *
 * Bentuk response mengikuti kontrak panel notifikasi Fuse supaya komponennya
 * tidak perlu diubah. Setiap query selalu dibatasi ke `user_id` pemilik token —
 * tidak ada jalur untuk membaca notifikasi pengguna lain.
 */
class NotifikasiController extends Controller
{
    /**
     * Daftar lonceng: hanya yang belum dibaca.
     *
     * Notifikasi yang sudah dibuka bukan lagi pemberitahuan — kalau ia tetap
     * tinggal di lonceng, petugas harus mengingat sendiri mana yang sudah
     * ditangani. Riwayatnya tidak hilang: halaman Notifikasi memuatnya lewat
     * `?semua=1`.
     */
    public function index(Request $request): JsonResponse
    {
        $baris = Notifikasi::where('user_id', Auth::guard('api')->id())
            ->when(!$request->boolean('semua'), fn ($q) => $q->where('is_read', false))
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($baris->map(fn (Notifikasi $n) => $this->toFuse($n))->all());
    }

    /**
     * Tandai satu notifikasi sudah dibaca.
     *
     * Penyaring `user_id` bukan sekadar pembatas tampilan: tanpa itu id milik
     * pengguna lain ikut bisa ditandai.
     */
    public function baca(int $id): JsonResponse
    {
        Notifikasi::where('user_id', Auth::guard('api')->id())
            ->whereKey($id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => 'Notifikasi ditandai sudah dibaca']);
    }

    public function bacaSemua(): JsonResponse
    {
        $jumlah = Notifikasi::where('user_id', Auth::guard('api')->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json(['message' => "{$jumlah} notifikasi ditandai sudah dibaca"]);
    }

    public function show(int $id): JsonResponse
    {
        $notifikasi = Notifikasi::where('user_id', Auth::guard('api')->id())->findOrFail($id);

        return response()->json($this->toFuse($notifikasi));
    }

    public function destroy(int $id): JsonResponse
    {
        Notifikasi::where('user_id', Auth::guard('api')->id())->findOrFail($id)->delete();

        return response()->json(['message' => 'Notifikasi dihapus']);
    }

    /**
     * Hapus beberapa sekaligus. Panel Fuse mengirim array id di body DELETE.
     */
    public function destroyMany(Request $request): JsonResponse
    {
        $ids = $request->json()->all();

        if (!is_array($ids)) {
            $ids = $request->input('ids', []);
        }

        $ids = array_filter(array_map('intval', (array) $ids));

        $jumlah = $ids === []
            ? 0
            : Notifikasi::where('user_id', Auth::guard('api')->id())->whereIn('id', $ids)->delete();

        return response()->json(['message' => "{$jumlah} notifikasi dihapus"]);
    }

    private function toFuse(Notifikasi $n): array
    {
        $data = $n->data ?? [];

        return [
            'id' => (string) $n->id,
            'icon' => $data['icon'] ?? 'lucide:bell',
            'title' => $data['title'] ?? ($n->type ?: 'Notifikasi'),
            'description' => $n->message,
            'time' => optional($n->created_at)->toIso8601String(),
            'read' => (bool) $n->is_read,
            'link' => $data['link'] ?? null,
            'useRouter' => (bool) ($data['useRouter'] ?? true),
            'variant' => $data['variant'] ?? 'primary',
        ];
    }
}
