<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Definisi alur persetujuan satu jenis pengajuan.
 *
 * Satu jenis hanya boleh punya satu alur aktif pada satu waktu; itu yang
 * dipakai saat pengajuan baru masuk tahap persetujuan. Alur lama dibiarkan
 * nonaktif, tidak dihapus, supaya langkah yang sudah terlanjur dibuat
 * darinya tetap punya induk.
 */
class AlurApproval extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'alur_approval';

    public const JENIS = ['permohonan', 'keberatan'];

    protected $fillable = [
        'jenis',
        'nama',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Panjang jenjang, supaya daftar modul bisa menampilkannya sebagai kolom
     * tanpa memaksa panel menghitung sendiri dari array relasinya.
     */
    protected $appends = ['tahap_jumlah'];

    public function getTahapJumlahAttribute(): int
    {
        return $this->relationLoaded('tahap')
            ? $this->tahap->count()
            : $this->tahap()->count();
    }

    public function tahap(): HasMany
    {
        return $this->hasMany(AlurApprovalTahap::class, 'alur_id')->orderBy('urutan');
    }

    /** Tahap yang benar-benar dipakai saat alur dijalankan. */
    public function tahapAktif(): HasMany
    {
        return $this->tahap()->where('is_active', true);
    }

    /** Alur yang dipakai untuk jenis ini sekarang. */
    public static function aktifUntuk(string $jenis): ?self
    {
        return static::where('jenis', $jenis)
            ->where('is_active', true)
            ->orderBy('id')
            ->first();
    }
}
