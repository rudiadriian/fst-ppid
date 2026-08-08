<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\InformasiPublik;
use App\Models\InformasiPublikFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'slug' => ['nullable', 'string', 'max:255'],
            'ringkasan' => ['nullable', 'string'],
            'konten' => ['nullable', 'string'],
            // Entri bisa menunjuk ke halaman lain, bukan berkas unggahan.
            'tautan' => ['nullable', 'url', 'max:500'],
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
    }
}
