<?php

namespace App\View\Composers;

use App\Models\KategoriInformasi;
use App\Models\TautanTerkait;
use App\Support\Cms;
use Illuminate\View\View;

/**
 * Data CMS yang dibutuhkan header dan footer di setiap halaman.
 *
 * Ditaruh di composer, bukan di tiap controller, supaya menu dan blok kontak
 * tetap konsisten walau halaman baru ditambahkan nanti.
 */
class CmsLayoutComposer
{
    public function compose(View $view): void
    {
        $view->with([
            'cmsKategoriInformasi' => $this->kategoriInformasi(),
            'cmsTautan' => $this->tautan(),
            'cmsKontak' => [
                'alamat' => Cms::pengaturan(
                    'kontak.alamat',
                    'Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230'
                ),
                'telepon' => Cms::pengaturan('kontak.telepon', '(021) 4718011 (Ext. PPID)'),
                'email' => Cms::pengaturan('kontak.email', 'ppid@foodstation.co.id'),
            ],
        ]);
    }

    /**
     * Kategori informasi publik untuk menu. Bila tabelnya masih kosong, dipakai
     * tiga klasifikasi wajib menurut UU No. 14 Tahun 2008 sebagai cadangan.
     */
    private function kategoriInformasi(): array
    {
        $baris = Cms::ambil(
            fn () => KategoriInformasi::aktif()->whereNull('parent_id')->get(['nama', 'slug']),
            collect(),
            'menu_kategori_informasi'
        );

        if ($baris->isEmpty()) {
            return [
                ['nama' => 'Informasi Berkala', 'slug' => 'berkala'],
                ['nama' => 'Informasi Serta Merta', 'slug' => 'serta-merta'],
                ['nama' => 'Informasi Setiap Saat', 'slug' => 'setiap-saat'],
            ];
        }

        return $baris->map(fn ($k) => ['nama' => $k->nama, 'slug' => $k->slug])->all();
    }

    private function tautan(): array
    {
        $baris = Cms::ambil(fn () => TautanTerkait::aktif()->get(), collect(), 'tautan_terkait');

        return $baris->map(fn ($t) => [
            'nama' => $t->nama,
            'url' => $t->url,
            'logo' => Cms::url($t->logo),
        ])->all();
    }
}
