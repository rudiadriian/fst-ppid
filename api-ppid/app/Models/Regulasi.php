<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Regulasi extends Model
{
    protected $table = 'regulasi';

    public const UPDATED_AT = null;

    protected $fillable = [
        'kategori',
        'judul',
        'nomor_peraturan',
        'jenis_peraturan',
        'tahun',
        'file_path',
        'tanggal_berlaku',
    ];

    protected $casts = [
        'tahun' => 'integer',
        'tanggal_berlaku' => 'date',
    ];
}
