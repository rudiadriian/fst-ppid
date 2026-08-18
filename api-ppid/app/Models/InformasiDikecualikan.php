<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\SoftDeletes;

class InformasiDikecualikan extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'informasi_dikecualikan';

    protected $fillable = [
        'judul',
        'judul_en',
        'slug',
        'ringkasan',
        'ringkasan_en',
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
