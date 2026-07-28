<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiDikecualikan extends Model
{
    use SoftDeletes;

    protected $table = 'informasi_dikecualikan';

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'alasan_pengecualian',
        'dasar_hukum_pengecualian',
        'jangka_waktu_pengecualian',
        'tanggal_penetapan',
        'pejabat_penetap',
        'file_surat_penetapan',
        'status',
    ];

    protected $casts = [
        'tanggal_penetapan' => 'date',
    ];

    public function pejabat(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pejabat_penetap');
    }
}
