<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Hitungan permintaan tautan lupa password beserta masa kuncinya.
 *
 * Dipisah dari `PercobaanLoginAdmin` karena yang dibatasi berbeda: bukan
 * penebakan password, melainkan pemakaian jalur kirim email. Seseorang yang
 * benar-benar lupa password tidak boleh ikut terkunci dari mencoba masuk hanya
 * karena meminta tautan beberapa kali, dan sebaliknya.
 */
class PercobaanTautanAdmin extends Model
{
    protected $table = 'percobaan_tautan_admin';

    protected $fillable = [
        'identitas',
        'ip_address',
        'jumlah_minta',
        'tahap_kunci',
        'terkunci_sampai',
        'terakhir_minta_pada',
    ];

    protected $casts = [
        'jumlah_minta' => 'integer',
        'tahap_kunci' => 'integer',
        'terkunci_sampai' => 'datetime',
        'terakhir_minta_pada' => 'datetime',
    ];

    public function sedangTerkunci(): bool
    {
        return $this->terkunci_sampai !== null && $this->terkunci_sampai->isFuture();
    }

    public function sisaKunci(): int
    {
        return $this->sedangTerkunci() ? max(0, (int) now()->diffInSeconds($this->terkunci_sampai, false)) : 0;
    }
}
