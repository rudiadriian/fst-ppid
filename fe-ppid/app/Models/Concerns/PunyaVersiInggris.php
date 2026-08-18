<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\App;

/**
 * Pemilih isi berdasarkan bahasa yang sedang dipakai pengunjung.
 *
 * Modul CMS di be-ppid punya pasangan kolom `<kolom>` (Indonesia) dan
 * `<kolom>_en` (Inggris). Kolom Inggrisnya boleh kosong — pada mode EN, isi
 * Indonesia tetap dipakai bila terjemahannya belum diisi petugas, sehingga
 * tidak ada halaman yang jadi kosong hanya karena belum diterjemahkan.
 */
trait PunyaVersiInggris
{
    /** Isi kolom sesuai locale aktif; jatuh ke versi Indonesia bila kosong. */
    public function teks(string $kolom): ?string
    {
        if (App::getLocale() !== 'en') {
            return $this->{$kolom};
        }

        $inggris = $this->{$kolom.'_en'} ?? null;

        return filled($inggris) ? $inggris : $this->{$kolom};
    }
}
