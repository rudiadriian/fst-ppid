<?php

namespace App\Http\Controllers\Api\Cms;

use App\Http\Controllers\Api\CrudController;
use App\Models\KeberatanInformasi;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class KeberatanController extends CrudController
{
    protected string $model = KeberatanInformasi::class;

    protected string $modulSlug = 'keberatan';

    protected array $searchable = ['alasan_keberatan', 'tanggapan_atasan_ppid'];

    protected array $sortable = ['id', 'status', 'jenis_keberatan', 'tanggal_keberatan'];

    protected string $defaultSort = '-tanggal_keberatan';

    protected array $withList = ['pemohon:id,nama,email', 'permohonan:id,kode_permohonan', 'petugas:id,name'];

    protected array $withDetail = ['pemohon', 'permohonan', 'petugas:id,name', 'files'];

    protected array $filterable = [
        'status' => 'exact',
        'jenis_keberatan' => 'exact',
        'permohonan_id' => 'exact',
    ];

    protected function rules(string $mode, ?Model $record): array
    {
        $wajib = $mode === 'create' ? 'required' : 'sometimes';

        return [
            'permohonan_id' => [$wajib, Rule::exists('permohonan_informasi', 'id')],
            'pemohon_id' => [$wajib, Rule::exists('pemohon', 'id')],
            'jenis_keberatan' => [$wajib, Rule::in([
                'permohonan_ditolak', 'informasi_tidak_disediakan', 'permintaan_tidak_ditanggapi',
                'informasi_tidak_sesuai', 'biaya_tidak_wajar', 'melebihi_jangka_waktu',
            ])],
            'alasan_keberatan' => [$wajib, 'string'],
            'status' => ['sometimes', Rule::in(['diajukan', 'diproses', 'selesai'])],
            'tanggapan_atasan_ppid' => ['nullable', 'string'],
        ];
    }

    protected function beforeSave(array $data, Request $request, ?Model $record): array
    {
        // Petugas penanggung jawab terisi otomatis begitu keberatan mulai ditangani.
        if (($data['status'] ?? null) !== null && $data['status'] !== 'diajukan') {
            $data['ditangani_oleh'] = $record?->ditangani_oleh ?? Auth::guard('api')->id();
        }

        if (($data['status'] ?? null) === 'selesai') {
            $data['tanggal_tanggapan'] = $record?->tanggal_tanggapan ?? now();
        }

        return $data;
    }
}
