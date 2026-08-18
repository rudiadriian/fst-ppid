<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/**
 * Unggah berkas untuk seluruh modul CMS.
 *
 * Pertahanan berlapis:
 * 1. Nama berkas dari klien tidak pernah dipakai sebagai nama di disk —
 *    nama disimpan acak, nama asli hanya jadi metadata.
 * 2. Ekstensi dan mime dicocokkan ke daftar putih per jenis berkas; ekstensi
 *    yang bisa dieksekusi server (php, phtml, htaccess, dsb.) tidak ada di
 *    daftar mana pun.
 * 3. Berkas ditulis ke luar document root (`fe-ppid/storage/app/public`) dan
 *    disajikan lewat route pembaca, bukan dieksekusi web server.
 */
class UploadController extends Controller
{
    /** Folder tujuan yang boleh dipakai; mencegah path traversal lewat input. */
    private const FOLDER = [
        'informasi-publik',
        'informasi-dikecualikan',
        'permohonan',
        'keberatan',
        'laporan',
        'maklumat',
        'berita',
        'galeri',
        'banner',
        'struktur-organisasi',
        'regulasi',
        'tautan',
        'pengaturan',
        'umum',
    ];

    private const EKSTENSI_GAMBAR = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    private const EKSTENSI_DOKUMEN = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'csv', 'txt'];

    private const EKSTENSI_VIDEO = ['mp4', 'webm'];

    /**
     * Berkas yang bisa dibaca langsung di peramban: PDF dan gambar. Dipakai
     * modul yang memang hanya boleh menerima keduanya, mis. Regulasi.
     */
    private const EKSTENSI_PDF_GAMBAR = ['pdf', 'jpg', 'jpeg', 'png', 'webp'];

    /** Batas ukuran per jenis, dalam kilobyte. */
    private const BATAS_KB = [
        'gambar' => 5120,           // 5 MB
        'dokumen' => 20480,         // 20 MB
        'dokumen_gambar' => 20480,  // 20 MB
        'video' => 102400,          // 100 MB
    ];

    public function store(Request $request): JsonResponse
    {
        $meta = $request->validate([
            'folder' => ['required', Rule::in(self::FOLDER)],
            'jenis' => ['required', Rule::in(['gambar', 'dokumen', 'dokumen_gambar', 'video'])],
        ]);

        $jenis = $meta['jenis'];
        $ekstensiSah = match ($jenis) {
            'gambar' => self::EKSTENSI_GAMBAR,
            'dokumen_gambar' => self::EKSTENSI_PDF_GAMBAR,
            'video' => self::EKSTENSI_VIDEO,
            default => self::EKSTENSI_DOKUMEN,
        };

        $request->validate([
            'file' => [
                'required',
                'file',
                'max:'.self::BATAS_KB[$jenis],
                'extensions:'.implode(',', $ekstensiSah),
                $jenis === 'gambar' ? 'image' : 'mimes:'.implode(',', $ekstensiSah),
            ],
        ]);

        /** @var UploadedFile $file */
        $file = $request->file('file');
        $ekstensi = strtolower($file->getClientOriginalExtension());

        // Sabuk pengaman kedua: kalau aturan di atas pernah dilonggarkan tanpa
        // sengaja, pengecekan ini tetap menahan ekstensi di luar daftar.
        if (!in_array($ekstensi, $ekstensiSah, true)) {
            throw ValidationException::withMessages([
                'file' => 'Jenis berkas tidak diizinkan.',
            ]);
        }

        $namaDisk = Str::random(32).'.'.$ekstensi;
        $folderTujuan = 'uploads/'.$meta['folder'].'/'.now()->format('Y/m');

        $path = Storage::disk('media')->putFileAs($folderTujuan, $file, $namaDisk);

        if ($path === false) {
            return response()->json(['error' => 'Berkas gagal disimpan'], 500);
        }

        AuditLogger::record(
            Auth::guard('api')->id(),
            'upload',
            null,
            null,
            null,
            ['path' => $path, 'nama_asli' => $file->getClientOriginalName()]
        );

        return response()->json([
            'data' => [
                // `path` inilah yang disimpan ke kolom path_file/file_path di DB.
                'path' => $path,
                'url' => rtrim(config('filesystems.disks.media.url'), '/').'/'.$path,
                'nama_file' => $file->getClientOriginalName(),
                'ukuran_file' => $file->getSize(),
                'tipe_file' => $file->getClientMimeType(),
            ],
        ], 201);
    }

    /**
     * Hapus berkas yang sudah tidak dipakai. Path divalidasi agar tetap berada
     * di dalam folder uploads (tidak bisa keluar lewat "../").
     */
    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'path' => ['required', 'string', 'max:500'],
        ]);

        $path = ltrim(str_replace('\\', '/', $data['path']), '/');

        if (!str_starts_with($path, 'uploads/') || str_contains($path, '..')) {
            throw ValidationException::withMessages([
                'path' => 'Path berkas tidak valid.',
            ]);
        }

        Storage::disk('media')->delete($path);

        AuditLogger::record(Auth::guard('api')->id(), 'delete_file', null, null, ['path' => $path], null);

        return response()->json(['message' => 'Berkas dihapus']);
    }
}
