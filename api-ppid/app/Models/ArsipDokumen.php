<?php

namespace App\Models;

use App\Models\Concerns\MencatatPelaku;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Satu berkas dalam Arsip Dokumen petugas (langkah 95).
 *
 * Barisnya menunjuk berkas yang sudah ada di disk `media`; melampirkannya ke
 * permohonan berarti menyalin `path_file`-nya, bukan mengunggah ulang.
 *
 * Menghapus baris arsip **tidak** menghapus berkasnya di disk. Berkas yang sama
 * bisa sudah terlampir di permohonan yang sudah dijawab, dan pemohonnya berhak
 * tetap bisa mengunduh apa yang dulu diberikan kepadanya.
 */
class ArsipDokumen extends Model
{
    use MencatatPelaku, SoftDeletes;

    protected $table = 'arsip_dokumen';

    protected $fillable = [
        'nama',
        'keterangan',
        'kategori',
        'path_file',
        'nama_file',
        'ukuran_file',
        'tipe_file',
        'is_active',
    ];

    protected $casts = [
        'ukuran_file' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Catat satu berkas ke arsip bila path-nya belum pernah tercatat.
     *
     * Dipakai jalur lampiran tanggapan: apa pun yang diunggah petugas lewat
     * dialog permohonan ikut masuk arsip, sehingga permohonan berikutnya yang
     * meminta dokumen sama tinggal memilihnya. Berkas yang memang berasal dari
     * arsip tidak menghasilkan baris kedua — `path_file` unik, dan pencarian
     * di sini menghindari galat tabrakan alih-alih mengandalkannya.
     */
    public static function catatSekali(string $pathFile, ?string $namaFile = null): ?self
    {
        $pathFile = trim($pathFile);

        if ($pathFile === '') {
            return null;
        }

        $ada = static::withTrashed()->where('path_file', $pathFile)->first();

        if ($ada !== null) {
            return $ada;
        }

        $nama = $namaFile !== null && trim($namaFile) !== '' ? trim($namaFile) : basename($pathFile);

        // `created_by` diisi sendiri oleh MencatatPelaku dari token petugas.
        return static::create([
            'nama' => str($nama)->limit(255)->toString(),
            'nama_file' => str($nama)->limit(255)->toString(),
            'path_file' => $pathFile,
            'is_active' => true,
        ]);
    }
}
