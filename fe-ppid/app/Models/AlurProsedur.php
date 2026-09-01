<?php

namespace App\Models;

use App\Models\Concerns\PunyaVersiInggris;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Gambar alur pada halaman Standar Layanan.
 *
 * Isinya infografis resmi yang diunggah petugas lewat modul Alur Prosedur di
 * be-ppid; halaman publik menayangkan gambarnya utuh, bukan mengetik ulang
 * isinya di template.
 */
class AlurProsedur extends Model
{
    use PunyaVersiInggris, SoftDeletes;

    protected $table = 'alur_prosedur';

    protected $casts = [
        'urutan' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Gambar yang tayang untuk satu halaman Standar Layanan, sudah berurutan.
     *
     * Baris tanpa gambar ikut disaring di sini: kartunya tidak punya isi apa
     * pun untuk ditampilkan, dan membiarkannya lolos hanya menghasilkan kotak
     * kosong bernomor di tengah alur.
     */
    public function scopeTayang($query, string $halaman)
    {
        return $query->where('halaman', $halaman)
            ->where('is_active', true)
            ->whereNotNull('gambar')
            ->where('gambar', '!=', '')
            ->orderBy('urutan')
            ->orderBy('id');
    }
}
