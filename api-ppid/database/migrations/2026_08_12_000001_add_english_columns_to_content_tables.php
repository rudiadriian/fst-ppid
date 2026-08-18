<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kolom versi Inggris untuk isi yang dikelola lewat CMS.
 *
 * Situs publik punya pengalih bahasa ID/EN, tetapi sebelumnya hanya teks
 * bawaan aplikasi yang ikut berubah — judul dokumen, regulasi, FAQ, dan
 * sejenisnya tetap Bahasa Indonesia karena isinya datang dari tabel ini.
 *
 * Semua kolom `*_en` boleh kosong. Situs publik memakai versi Inggris hanya
 * bila kolomnya terisi; kalau kosong, teks Indonesia dipakai apa adanya
 * sehingga tidak ada halaman yang jadi kosong setelah migrasi ini.
 *
 * Kolom `nama` pada `struktur_organisasi` sengaja tidak diterjemahkan — isinya
 * nama orang.
 */
return new class extends Migration
{
    /** @var array<string, array<string, string>> tabel => [kolom_en => kolom asal] */
    private const KOLOM = [
        'kategori_informasi' => [
            'nama_en' => 'nama',
            'deskripsi_en' => 'deskripsi',
        ],
        'informasi_publik' => [
            'judul_en' => 'judul',
            'ringkasan_en' => 'ringkasan',
            'konten_en' => 'konten',
        ],
        'informasi_dikecualikan' => [
            'judul_en' => 'judul',
            'ringkasan_en' => 'ringkasan',
        ],
        'regulasi' => [
            'judul_en' => 'judul',
            'ringkasan_en' => 'ringkasan',
        ],
        'faq' => [
            'pertanyaan_en' => 'pertanyaan',
            'jawaban_en' => 'jawaban',
            'kategori_en' => 'kategori',
        ],
        'struktur_organisasi' => [
            'jabatan_en' => 'jabatan',
            'deskripsi_en' => 'deskripsi',
            'poin_en' => 'poin',
        ],
        'berita' => [
            'judul_en' => 'judul',
            'ringkasan_en' => 'ringkasan',
            'konten_en' => 'konten',
        ],
        'kategori_berita' => [
            'nama_en' => 'nama',
        ],
        'halaman_statis' => [
            'judul_en' => 'judul',
            'konten_en' => 'konten',
        ],
    ];

    /** Kolom yang isinya pendek cukup varchar; sisanya text. */
    private const PENDEK = [
        'kategori_informasi.nama_en' => 150,
        'informasi_publik.judul_en' => 255,
        'informasi_dikecualikan.judul_en' => 255,
        'regulasi.judul_en' => 255,
        'faq.kategori_en' => 100,
        'struktur_organisasi.jabatan_en' => 150,
        'berita.judul_en' => 255,
        'kategori_berita.nama_en' => 150,
        'halaman_statis.judul_en' => 255,
    ];

    public function up(): void
    {
        foreach (self::KOLOM as $tabel => $kolom) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($tabel, $kolom) {
                foreach ($kolom as $baru => $asal) {
                    if (Schema::hasColumn($tabel, $baru)) {
                        continue;
                    }

                    $panjang = self::PENDEK[$tabel.'.'.$baru] ?? null;

                    $kolomBaru = $panjang
                        ? $table->string($baru, $panjang)->nullable()
                        : $table->text($baru)->nullable();

                    // Ditaruh tepat setelah kolom Indonesianya supaya pasangannya
                    // terbaca berdampingan saat melihat struktur tabel.
                    if (Schema::hasColumn($tabel, $asal)) {
                        $kolomBaru->after($asal);
                    }
                }
            });
        }
    }

    public function down(): void
    {
        foreach (self::KOLOM as $tabel => $kolom) {
            if (!Schema::hasTable($tabel)) {
                continue;
            }

            Schema::table($tabel, function (Blueprint $table) use ($tabel, $kolom) {
                $hapus = array_values(array_filter(
                    array_keys($kolom),
                    fn ($baru) => Schema::hasColumn($tabel, $baru)
                ));

                if ($hapus) {
                    $table->dropColumn($hapus);
                }
            });
        }
    }
};
