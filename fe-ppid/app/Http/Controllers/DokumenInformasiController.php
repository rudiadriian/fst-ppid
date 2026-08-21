<?php

namespace App\Http\Controllers;

use App\Models\InformasiPublik;
use App\Support\AksesDokumen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Penyaji berkas dokumen Informasi Publik (langkah 83).
 *
 * Dua pintu untuk satu berkas yang sama:
 *
 * - `pratinjau` — terbuka untuk siapa saja, dikirim `inline` supaya dibuka di
 *   dalam peramban.
 * - `unduh` — hanya untuk pemohon yang permohonannya atas dokumen itu sudah
 *   disetujui petugas, dikirim sebagai lampiran.
 *
 * Berkasnya sendiri tidak pernah punya URL langsung: yang disimpan di
 * `informasi_publik_files.path_file` berada di dalam disk `public` milik
 * project ini, dan hanya kedua method di bawah yang membacanya. Kalau berkasnya
 * dilayani lewat `asset('storage/…')` seperti dokumen biasa, pemeriksaan hak
 * unduh di sini bisa dilewati hanya dengan menyalin alamatnya.
 *
 * **Batas yang jujur soal "preview only".** Berkas PDF yang sudah sampai di
 * peramban selalu bisa disimpan lewat tombol unduh milik penampil PDF bawaan.
 * Yang benar-benar ditegakkan di sini adalah: tidak ada URL berkas yang bisa
 * disebar, dan salinan resmi hanya keluar lewat pintu unduh yang tercatat.
 * Untuk mencegah penyimpanan sama sekali, dokumennya harus dirender jadi gambar
 * per halaman — itu menuntut ImageMagick/pdftoppm, yang tidak tersedia di
 * lingkungan ini.
 */
class DokumenInformasiController extends Controller
{
    /**
     * Halaman akses dokumen.
     *
     * Bukan penampil berkas: sejak langkah 83 direvisi, "lihat saja" memakai
     * tautan yang memang sudah ada pada dokumennya (mis. halaman Laporan
     * Tahunan di situs perusahaan), bukan berkas yang diunggah ulang. Halaman
     * ini yang menjelaskan apa yang kurang bila unduhannya belum terbuka, dan
     * menyediakan langkah berikutnya.
     */
    public function pratinjau(int $dokumen): View
    {
        $baris = $this->dokumen($dokumen, false);
        $pemohon = Auth::guard('pemohon')->user();

        return view('ppid.document_preview', [
            'dokumen' => $baris,
            'berkas' => $baris->files->first(),
            'akses' => AksesDokumen::keadaanUnduh($baris, $pemohon),
        ]);
    }

    /**
     * Unduh salinan. Dijaga keputusan petugas, bukan sekadar status masuk.
     */
    public function unduh(int $dokumen): StreamedResponse|RedirectResponse
    {
        $baris = $this->dokumen($dokumen);
        $pemohon = Auth::guard('pemohon')->user();

        if (!AksesDokumen::bolehUnduh($baris, $pemohon)) {
            /*
             * Dialihkan ke halaman pratinjaunya, bukan dijawab 403 telanjang.
             * Di sana tombolnya sudah menjelaskan apa yang kurang — belum masuk,
             * belum mengajukan, atau masih menunggu keputusan — dan menyediakan
             * langkah berikutnya. Halaman galat tidak menyediakan apa pun.
             */
            return redirect()
                ->route('ppid.dokumen.pratinjau', $baris->id)
                ->with('status', __('Unduhan dokumen ini terbuka setelah permohonan Anda disetujui petugas.'));
        }

        return $this->kirimBerkas($baris, 'attachment');
    }

    /**
     * Mulai permohonan atas dokumen tertentu.
     *
     * Membawa `informasi_publik_id` ke formulir permohonan sehingga pemohon
     * tidak perlu mengetik ulang judul dokumennya — dan, yang lebih penting,
     * permohonannya benar-benar tertaut ke barisnya sehingga persetujuan
     * petugas bisa dikenali sebagai persetujuan atas dokumen ini.
     */
    public function ajukan(Request $request, int $dokumen): RedirectResponse
    {
        $baris = $this->dokumen($dokumen);

        if (!Auth::guard('pemohon')->check()) {
            return redirect()
                ->route('akun.login')
                ->with('status', __('Masuk dulu untuk mengajukan permohonan unduh dokumen ini.'))
                ->with('tujuan_setelah_masuk', route('ppid.dokumen.ajukan', $baris->id));
        }

        return redirect()->route('akun.permohonan.create', ['dokumen' => $baris->id]);
    }

    /**
     * Dokumen terbit yang tunduk pada aturan unduhan terbatas.
     *
     * @param  bool  $wajibBerkas  true untuk jalur unduh, yang memang tidak
     *                             ada gunanya tanpa berkas; false untuk
     *                             halaman aksesnya, yang tetap berguna sebagai
     *                             penjelas walau berkasnya belum diunggah.
     */
    private function dokumen(int $id, bool $wajibBerkas = true): InformasiPublik
    {
        $baris = InformasiPublik::published()->with('files')->find($id);

        abort_if(!$baris, 404, 'Dokumen tidak ditemukan.');
        abort_if($wajibBerkas && $baris->files->isEmpty(), 404, 'Berkas dokumen belum tersedia.');

        return $baris;
    }

    private function kirimBerkas(InformasiPublik $dokumen, string $disposisi): StreamedResponse
    {
        $berkas = $dokumen->files->first();
        $path = ltrim(Str::after((string) $berkas->path_file, 'storage/'), '/');

        /*
         * Disknya ditentukan penandanya, bukan disimpan di kolom tersendiri.
         * `InformasiPublikController` di api-ppid memindahkan berkasnya setiap
         * kali penanda itu berubah, jadi keduanya selalu sepakat — dan tidak
         * ada kolom kedua yang bisa tertinggal isinya.
         */
        $disk = Storage::disk($dokumen->unduhan_terbatas ? 'dokumen_terbatas' : 'public');

        abort_unless($disk->exists($path), 404, 'Berkas dokumen tidak ditemukan.');

        $ekstensi = pathinfo($path, PATHINFO_EXTENSION);
        $nama = Str::slug($dokumen->judul).($ekstensi ? '.'.$ekstensi : '');

        $headers = [
            'Content-Type' => $berkas->tipe_file ?: ($disk->mimeType($path) ?: 'application/octet-stream'),
            // Berkas yang haknya diperiksa per permintaan tidak boleh mengendap
            // di cache bersama; kalau tidak, proxy bisa menyajikannya kepada
            // orang berikutnya tanpa pemeriksaan apa pun.
            'Cache-Control' => 'private, no-store, max-age=0',
        ];

        return $disposisi === 'inline'
            ? $disk->response($path, $nama, $headers, 'inline')
            : $disk->download($path, $nama, $headers);
    }
}
