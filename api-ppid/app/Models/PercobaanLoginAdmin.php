<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Hitungan kegagalan masuk panel beserta masa kuncinya.
 *
 * Satu baris = satu kombinasi email + alamat IP. Baris dihapus begitu
 * pemiliknya berhasil masuk, jadi tabel ini hanya berisi percobaan yang masih
 * berjalan.
 */
class PercobaanLoginAdmin extends Model
{
    protected $table = 'percobaan_login_admin';

    protected $fillable = [
        'identitas',
        'ip_address',
        'user_agent',
        'jumlah_gagal',
        'tahap_kunci',
        'terkunci_sampai',
        'terakhir_gagal_pada',
    ];

    protected $casts = [
        'jumlah_gagal' => 'integer',
        'tahap_kunci' => 'integer',
        'terkunci_sampai' => 'datetime',
        'terakhir_gagal_pada' => 'datetime',
    ];

    public function sedangTerkunci(): bool
    {
        return $this->terkunci_sampai !== null && $this->terkunci_sampai->isFuture();
    }

    /** Sisa waktu kunci dalam detik; 0 bila tidak sedang terkunci. */
    public function sisaKunci(): int
    {
        return $this->sedangTerkunci() ? max(0, (int) now()->diffInSeconds($this->terkunci_sampai, false)) : 0;
    }
}
