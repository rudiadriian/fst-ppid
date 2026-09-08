<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Sampul Laporan Tahunan perusahaan yang tampil sebagai galeri di beranda.
 *
 * Satu baris = satu tahun buku. `tautan` adalah halaman tempat laporannya
 * dibaca; salinannya tidak ditaruh di sini karena pelepasan salinan tetap
 * melewati Permohonan Informasi.
 */
class LaporanTahunan extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'laporan_tahunan';

    protected $fillable = [
        'tahun',
        'judul',
        'judul_en',
        'sampul',
        'tautan',
        'urutan',
        'status',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'urutan' => 'integer',
    ];
}
