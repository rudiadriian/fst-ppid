<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu kotak pada Bagan Struktur Organisasi PPID.
 *
 * `parent_id` membentuk pohonnya, `tipe_node` menentukan cara menggambarnya:
 *   utama   — kotak pada alur vertikal, terhubung panah ke induknya;
 *   samping — kotak di samping induk, terhubung garis putus-putus;
 *   grup    — bingkai berjudul yang membungkus anak-anaknya secara berjajar.
 */
class StrukturOrganisasi extends Model
{
    protected $table = 'struktur_organisasi';

    public $timestamps = false;

    protected $fillable = [
        'nama',
        'jabatan',
        'foto',
        'urutan',
        'deskripsi',
        'is_active',
        'parent_id',
        'tipe_node',
        'poin',
    ];

    protected $casts = [
        'urutan'    => 'integer',
        'is_active' => 'boolean',
        'parent_id' => 'integer',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('urutan');
    }

    public function induk(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function anak(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('urutan');
    }

    /** Butir isi kotak (satu per baris pada kolom `poin`). */
    public function daftarPoin(): array
    {
        if (blank($this->poin)) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $this->poin))));
    }

    public function tipe(): string
    {
        return $this->tipe_node ?: 'utama';
    }

    /**
     * Susun daftar datar jadi pohon siap gambar.
     *
     * Anak dipisah dua: `alur` (kotak `utama`/`grup` yang digambar menurun) dan
     * `samping` (kotak bergaris putus di sisi kanan induknya). Kotak yang
     * induknya tidak aktif ikut naik jadi akar, supaya tidak ada yang hilang
     * dari bagan hanya karena satu baris dinonaktifkan.
     *
     * @param  \Illuminate\Support\Collection<int, self>  $baris
     * @return array<int, array{node: self, alur: array, samping: array}>
     */
    public static function pohon($baris): array
    {
        $adaId = $baris->keyBy('id');

        $anak = [];

        foreach ($baris as $item) {
            $indukId = $item->parent_id && $adaId->has($item->parent_id) ? $item->parent_id : 0;
            $anak[$indukId][] = $item;
        }

        $susun = function (int $indukId) use (&$susun, $anak): array {
            $hasil = [];

            foreach ($anak[$indukId] ?? [] as $item) {
                $cabang = $susun($item->id);

                $hasil[] = [
                    'node' => $item,
                    'alur' => array_values(array_filter($cabang, fn ($sub) => $sub['node']->tipe() !== 'samping')),
                    'samping' => array_values(array_filter($cabang, fn ($sub) => $sub['node']->tipe() === 'samping')),
                ];
            }

            return $hasil;
        };

        return $susun(0);
    }
}
