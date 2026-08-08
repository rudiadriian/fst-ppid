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
    public function index(): JsonResponse
    {
        $baris = Notifikasi::where('user_id', Auth::guard('api')->id())
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return response()->json($baris->map(fn (Notifikasi $n) => $this->toFuse($n))->all());
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
