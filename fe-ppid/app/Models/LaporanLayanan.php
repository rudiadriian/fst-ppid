<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanLayanan extends Model
{
    protected $table = 'laporan_layanan';

    protected $casts = [
        'tahun'                  => 'integer',
        'jumlah_permohonan_masuk' => 'integer',
        'jumlah_dikabulkan'       => 'integer',
        'jumlah_ditolak'          => 'integer',
        'jumlah_ditolak_sebagian' => 'integer',
        'jumlah_keberatan'        => 'integer',
        'rata_rata_hari_respon'   => 'float',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeTipe($query, string $tipe)
    {
        return $query->where('tipe_laporan', $tipe);
    }
}
