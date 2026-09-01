<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\ArsipDokumen;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Arsip Dokumen petugas (langkah 95).
 *
 * Tempat berkas yang dipakai berulang kali menjawab permohonan — SK, laporan,
 * daftar informasi publik. Petugas mengunggahnya sekali ke sini, lalu
 * melampirkannya ke permohonan mana pun tanpa unggahan kedua.
 *
 * Hak aksesnya modul sendiri (`arsip-dokumen`) dan bukan menumpang Permohonan:
 * isinya dokumen milik lembaga, bukan berkas satu pengajuan, dan siapa yang
 * boleh menambah atau membuang isi arsip tidak selalu sama dengan siapa yang
 * boleh menangani permohonan.
 *
 * Menghapus baris di sini tidak menghapus berkasnya di disk maupun lampiran
 * yang sudah telanjur diberikan kepada pemohon — lihat {@see ArsipDokumen}.
 */
class ArsipDokumenController extends CrudController
{
    protected string $model = ArsipDokumen::class;

    protected string $modulSlug = 'arsip-dokumen';

    protected array $searchable = ['nama', 'keterangan', 'kategori', 'nama_file'];

    protected array $sortable = ['id', 'nama', 'kategori', 'created_at'];

    protected string $defaultSort = '-id';

    protected array $withList = ['pembuat:id,name'];

    protected array $withDetail = ['pembuat:id,name'];

    protected array $filterable = [
        'kategori' => 'exact',
        'is_active' => 'boolean',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'nama' => [$wajib, 'string', 'max:255'],
            'keterangan' => ['nullable', 'string', 'max:2000'],
            'kategori' => ['nullable', 'string', 'max:100'],
            // Satu berkas fisik hanya boleh punya satu baris arsip; tanpa
            // aturan ini dua baris bisa menunjuk berkas yang sama dan
            // menghapus salah satunya terasa seperti tidak terjadi apa-apa.
            'path_file' => [
                $wajib,
                'string',
                'max:500',
                Rule::unique('arsip_dokumen', 'path_file')->ignore($record?->getKey()),
            ],
            'nama_file' => ['nullable', 'string', 'max:255'],
            'ukuran_file' => ['nullable', 'integer', 'min:0'],
            'tipe_file' => ['nullable', 'string', 'max:150'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
