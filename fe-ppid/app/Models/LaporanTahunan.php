<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Sampul Laporan Tahunan perusahaan; dikelola modul Laporan Tahunan di be-ppid.
 *
 * Beranda menampilkannya sebagai galeri sampul. Satu baris = satu tahun buku.
 */
class LaporanTahunan extends Model
{
    use PunyaVersiInggris;

    use SoftDeletes;

    protected $table = 'laporan_tahunan';

    protected $casts = [
        'tahun'  => 'integer',
        'urutan' => 'integer',
    ];

    /**
     * Entri yang tayang di situs publik, tahun terbaru lebih dulu.
     *
     * `urutan` didahulukan supaya petugas bisa memaksa susunan tertentu tanpa
     * mengarang tahun; baris yang tidak diatur (`urutan` 0) tetap tersusun
     * menurun menurut tahun bukunya.
     */
    public function scopeTayang($query)
    {
        return $query->where('status', 'published')
            ->orderBy('urutan')
            ->orderByDesc('tahun');
    }
}
