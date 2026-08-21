<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\InformasiPublik;
use App\Models\InformasiPublikFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class InformasiPublikController extends CrudController
{
    protected string $model = InformasiPublik::class;

    protected string $modulSlug = 'informasi-publik';

    protected array $searchable = ['judul', 'ringkasan', 'nomor_klasifikasi'];

    protected array $sortable = ['id', 'judul', 'tanggal_publikasi', 'status', 'views_count', 'created_at'];

    protected array $withList = ['kategori:id,nama,slug'];

    protected array $withDetail = ['kategori:id,nama,slug', 'files', 'publisher:id,name', 'reviewer:id,name'];

    protected array $filterable = [
        'status' => 'exact',
        'kategori_id' => 'exact',
        'tanggal_publikasi' => 'date',
    ];

    protected ?string $slugFrom = 'judul';

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'kategori_id' => [$wajib, Rule::exists('kategori_informasi', 'id')],
            'judul' => [$wajib, 'string', 'max:255'],
            'judul_en' => ['nullable', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string'],
            'ringkasan_en' => ['nullable', 'string'],
            'konten' => ['nullable', 'string'],
            'konten_en' => ['nullable', 'string'],
            // Entri bisa menunjuk ke halaman lain, bukan berkas unggahan.
            'tautan' => ['nullable', 'url', 'max:500'],
            /*
             * Dokumen boleh dilihat siapa saja, tetapi salinannya hanya keluar
             * setelah permohonan pemohon disetujui petugas (langkah 83).
             * Menyalakannya memindahkan berkasnya ke penyimpanan privat —
             * lihat `selaraskanPenyimpanan()`.
             */
            'unduhan_terbatas' => ['sometimes', 'boolean'],
            'nomor_klasifikasi' => ['nullable', 'string', 'max:50'],
            'tanggal_publikasi' => ['nullable', 'date'],
            'status' => ['sometimes', Rule::in(['draft', 'menunggu_review', 'published', 'archived'])],
            // Daftar berkas ikut dikirim bersama form; path berasal dari endpoint upload.
            'files' => ['sometimes', 'array'],
            'files.*.nama_file' => ['nullable', 'string', 'max:255'],
            'files.*.path_file' => ['required', 'string', 'max:500'],
            'files.*.ukuran_file' => ['nullable', 'integer'],
            'files.*.tipe_file' => ['nullable', 'string', 'max:100'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        unset($data['files']); // disimpan terpisah di afterSave

        $userId = Auth::guard('api')->id();
        $statusBaru = $data['status'] ?? $record?->status ?? 'draft';

        if ($statusBaru === 'published') {
            $data['published_by'] = $userId;
            $data['tanggal_publikasi'] = $data['tanggal_publikasi'] ?? $record?->tanggal_publikasi ?? now()->toDateString();
        }

        // Jejak reviewer dicatat saat status berpindah dari antrean review.
        if ($record !== null && $record->status === 'menunggu_review' && $statusBaru !== 'menunggu_review') {
            $data['reviewed_by'] = $userId;
            $data['reviewed_at'] = now();
        }

        return $data;
    }

    protected function afterSave(Model $record, Request $request, string $mode): void
    {
        if (!$request->has('files')) {
            // Penanda unduhan terbatas bisa diubah tanpa menyentuh daftar
            // berkasnya sama sekali, jadi pemindahannya diperiksa lebih dulu.
            $this->selaraskanPenyimpanan($record);

            return;
        }

        $files = $request->input('files', []);
        $idDipakai = [];

        foreach (array_values($files) as $urutan => $file) {
            $baris = InformasiPublikFile::updateOrCreate(
                [
                    'informasi_publik_id' => $record->getKey(),
                    'path_file' => $file['path_file'],
                ],
                [
                    'nama_file' => $file['nama_file'] ?? basename($file['path_file']),
                    'ukuran_file' => $file['ukuran_file'] ?? null,
                    'tipe_file' => $file['tipe_file'] ?? null,
                    'urutan' => $urutan,
                ]
            );

            $idDipakai[] = $baris->id;
        }

        // Berkas yang dilepas dari form ikut dihapus dari tabel lampiran.
        InformasiPublikFile::where('informasi_publik_id', $record->getKey())
            ->whereNotIn('id', $idDipakai ?: [0])
            ->delete();

        $this->selaraskanPenyimpanan($record);
    }

    /**
     * Pindahkan berkas dokumen ke penyimpanan yang sesuai penandanya.
     *
     * Penanda `unduhan_terbatas` tidak cukup ditegakkan di kode: folder
     * `storage/app/public` milik fe-ppid ditautkan ke `public/storage`, jadi
     * apa pun di sana dilayani web server tanpa satu baris PHP pun berjalan.
     * Selama berkasnya di sana, pemeriksaan hak unduh bisa dilewati cukup
     * dengan menyalin alamat berkasnya.
     *
     * Karena itu penandanya memindahkan berkasnya:
     *
     * - dinyalakan  → `media` (publik)          → `dokumen_terbatas` (privat)
     * - dimatikan   → `dokumen_terbatas`        → `media`
     *
     * `path_file` di basis data tidak berubah; yang berubah hanya disk tempat
     * ia tinggal, dan itu selalu bisa disimpulkan dari penandanya. Satu sumber
     * kebenaran, tidak ada kolom kedua yang bisa berbeda isinya.
     *
     * Kegagalan memindahkan tidak boleh menggagalkan penyimpanan datanya —
     * tetapi juga tidak boleh lewat diam-diam, karena akibatnya dokumen yang
     * dikira terbatas masih terbuka. Karena itu dicatat sebagai `warning`.
     */
    private function selaraskanPenyimpanan(Model $record): void
    {
        $publik = Storage::disk('media');
        $privat = Storage::disk('dokumen_terbatas');

        $terbatas = (bool) $record->unduhan_terbatas;
        $dari = $terbatas ? $publik : $privat;
        $ke = $terbatas ? $privat : $publik;

        $berkas = InformasiPublikFile::where('informasi_publik_id', $record->getKey())->get();

        foreach ($berkas as $baris) {
            $path = ltrim(Str::after((string) $baris->path_file, 'storage/'), '/');

            if (blank($path) || !$dari->exists($path) || $ke->exists($path)) {
                continue;
            }

            try {
                $ke->put($path, $dari->get($path));
                $dari->delete($path);
            } catch (\Throwable $e) {
                Log::warning('[PPID] Gagal memindahkan berkas dokumen terbatas: '.$e->getMessage(), [
                    'informasi_publik_id' => $record->getKey(),
                    'path' => $path,
                    'terbatas' => $terbatas,
                ]);
            }
        }
    }
}
