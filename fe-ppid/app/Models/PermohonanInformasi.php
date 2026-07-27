<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PermohonanInformasi extends Model
{
    use SoftDeletes;

    protected $table = 'permohonan_informasi';

    protected $casts = [
        'tampil_di_register_publik' => 'boolean',
        'tanggal_permohonan'        => 'datetime',
        'tanggal_tanggapan'         => 'datetime',
        'batas_waktu_tanggapan'     => 'datetime',
    ];

    /**
     * Hanya permohonan yang pemohonnya setuju ditampilkan di Register Permohonan publik.
     * Identitas pemohon tidak pernah ikut diambil di sini.
     */
    public function scopeRegisterPublik($query)
    {
        return $query->where('tampil_di_register_publik', true);
    }
}
