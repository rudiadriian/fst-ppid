<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\InformasiPublik;
use App\Models\PermohonanInformasi;
use App\Models\PermohonanTanggapanFile;
use App\Support\Cms;
use App\Support\EmailPemohon;
use App\Support\NotifikasiAdmin;
use App\Support\SlaLayanan;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
use Illuminate\View\View;

/**
 * Modul Permohonan Informasi pada Portal Pengguna.
 *
 * Pengajuan baru hanya boleh dibuat setelah Data Pemohon diverifikasi petugas
 * — identitas pemohon harus jelas sebelum permohonan diproses.
 */
class PermohonanController extends Controller
{
    /** Jumlah baris per halaman yang boleh dipilih pengguna. */
    private const PER_HALAMAN = [10, 25, 50, 100];

    /** Kolom yang boleh dipakai mengurutkan (mencegah SQL injection lewat query string). */
    private const KOLOM_URUT = ['kode_permohonan', 'tanggal_permohonan', 'status'];

    public function index(Request $request): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        return view('akun.permohonan.index', [
            'pemohon' => $pemohon,
            'daftar' => $this->daftar($request, $pemohon->id),
            'cari' => $request->string('cari')->toString(),
            'urut' => $this->kolomUrut($request),
            'arah' => $this->arahUrut($request),
            'per' => $this->perHalaman($request),
            'opsiPer' => self::PER_HALAMAN,
            'status' => $this->kelompokStatus($request),
            'jumlahStatus' => $this->jumlahPerStatus($pemohon->id),
        ]);
    }

    public function create(Request $request): View|RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        if (!$pemohon->dataTerverifikasi()) {
            return redirect()->route('akun.data-pemohon')
                ->with('status', __('Lengkapi dan verifikasi Data Pemohon dulu sebelum mengajukan permohonan.'));
        }

        return view('akun.permohonan.create', [
            'pemohon' => $pemohon,
            'ketentuan' => $this->ketentuan(),
            // Diisi bila pemohon datang dari halaman pratinjau sebuah dokumen
            // berunduhan terbatas (langkah 83).
            'dokumen' => $this->dokumenDiminta($request->integer('dokumen')),
        ]);
    }

    /**
     * Dokumen Informasi Publik yang jadi asal permohonan, bila ada.
     *
     * Hanya dokumen terbit yang berunduhan terbatas yang diterima. Nomor lain
     * diabaikan diam-diam: parameter ini datang dari query string, dan
     * permohonan biasa tidak boleh gagal hanya karena seseorang menyunting
     * alamatnya.
     */
    private function dokumenDiminta(?int $id): ?InformasiPublik
    {
        if (!$id) {
            return null;
        }

        return InformasiPublik::published()
            ->where('unduhan_terbatas', true)
            ->find($id);
    }

    /**
     * Ketentuan layanan yang wajib dibaca sebelum pernyataan dicentang.
     *
     * Bisa diganti dari CMS lewat Pengaturan Situs kunci
     * `layanan.ketentuan_permohonan` (isi HTML). Kalau kosong, dipakai teks
     * bawaan di bawah ini.
     */
    private function ketentuan(): HtmlString
    {
        $dariCms = Cms::pengaturan('layanan.ketentuan_permohonan');

        if (filled($dariCms)) {
            return new HtmlString($dariCms);
        }

        $butir = [
            __('Permohonan informasi publik diajukan berdasarkan UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik dan peraturan turunannya.'),
            __('Pemohon wajib memberikan data diri yang benar. Data identitas yang tidak benar atau tidak dapat diverifikasi menjadi dasar penolakan permohonan.'),
            __('PPID menanggapi permohonan paling lambat 10 (sepuluh) hari kerja sejak permohonan diterima, dan dapat diperpanjang 7 (tujuh) hari kerja disertai alasan tertulis.'),
            __('Informasi yang dikecualikan sebagaimana diatur dalam Pasal 17 UU KIP tidak dapat diberikan.'),
            __('Biaya penggandaan atau pengiriman salinan informasi, bila ada, menjadi tanggungan pemohon sesuai ketentuan yang berlaku.'),
            __('Informasi yang diperoleh tidak boleh digunakan untuk tujuan melawan hukum. Penyalahgunaan informasi menjadi tanggung jawab pemohon.'),
            __('Pokok permohonan (tanpa identitas pemohon) dicatat pada Register Permohonan Informasi Publik yang dapat diakses masyarakat sebagai bentuk akuntabilitas layanan.'),
            __('Data pribadi pemohon hanya digunakan untuk keperluan pelayanan informasi publik dan tidak dipublikasikan.'),
            __('Pemohon dapat mengajukan keberatan apabila permohonan ditolak atau tanggapan dinilai tidak sesuai, paling lambat 30 (tiga puluh) hari kerja sejak tanggapan diterima.'),
        ];

        $html = '<p>'.__('Dengan mengirim permohonan ini, Anda menyatakan telah membaca dan menyetujui ketentuan berikut:').'</p>'
            .'<ol class="list-decimal list-outside pl-5 space-y-2">';

        foreach ($butir as $isi) {
            $html .= '<li>'.e($isi).'</li>';
        }

        return new HtmlString($html.'</ol>');
    }

    public function store(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        if (!$pemohon->dataTerverifikasi()) {
            return redirect()->route('akun.data-pemohon')
                ->with('status', __('Lengkapi dan verifikasi Data Pemohon dulu sebelum mengajukan permohonan.'));
        }

        $data = $request->validate([
            'informasi_publik_id' => ['nullable', 'integer'],
            'rincian_informasi' => ['required', 'string', 'max:2000'],
            'tujuan_penggunaan' => ['required', 'string', 'max:2000'],
            'cara_memperoleh' => ['required', 'in:'.implode(',', array_keys(PermohonanInformasi::CARA_MEMPEROLEH))],
            'format_informasi' => ['required', 'in:hardcopy,softcopy'],
            // Salinan cetak hanya bisa diambil langsung; salinan digital lewat email.
            'cara_pengiriman' => ['required', 'in:ambil_langsung,email'],
            // Pernyataan kebenaran data — wajib, dan hanya bisa dicentang
            // setelah ketentuan layanan dibaca (lihat formulirnya).
            'pernyataan_benar' => ['accepted'],
        ]);

        if ($data['format_informasi'] === 'hardcopy' && $data['cara_pengiriman'] !== 'ambil_langsung') {
            $data['cara_pengiriman'] = 'ambil_langsung';
        }

        if ($data['format_informasi'] === 'softcopy' && $data['cara_pengiriman'] !== 'email') {
            $data['cara_pengiriman'] = 'email';
        }

        /*
         * Nomor dokumennya diperiksa ulang di sini, tidak dipercaya begitu saja
         * dari formulir. Isian tersembunyi bisa diubah siapa pun sebelum
         * dikirim, dan nomor ini yang nanti membuka tombol unduh — menerimanya
         * mentah-mentah sama saja membiarkan orang memilih sendiri dokumen mana
         * yang persetujuannya berlaku baginya.
         */
        $dokumen = $this->dokumenDiminta($data['informasi_publik_id'] ?? null);

        try {
            $permohonan = DB::transaction(fn () => PermohonanInformasi::create([
                'kode_permohonan' => $this->kodeBaru(),
                'pemohon_id' => $pemohon->id,
                'informasi_publik_id' => $dokumen?->id,
                'rincian_informasi' => $data['rincian_informasi'],
                'tujuan_penggunaan' => $data['tujuan_penggunaan'],
                'cara_memperoleh' => $data['cara_memperoleh'],
                'format_informasi' => $data['format_informasi'],
                'cara_pengiriman' => $data['cara_pengiriman'],
                /*
                 * Jalur pelayanan diturunkan dari cara pengiriman, tidak
                 * ditanyakan lagi (langkah 89). Keduanya menjawab pertanyaan
                 * yang sama — dokumennya dikirim atau pemohonnya datang — dan
                 * menaruh dua isian untuk satu jawaban hanya melahirkan
                 * kemungkinan keduanya bertentangan. Petugas tetap bisa
                 * mengubahnya saat menerima permohonan, misalnya ketika
                 * pemohon menelepon minta datang langsung.
                 */
                'jalur_pelayanan' => $data['cara_pengiriman'] === 'ambil_langsung' ? 'langsung' : 'online',
                'status' => 'diajukan',
                'tanggal_permohonan' => now(),
                // UU KIP Pasal 22: tanggapan paling lama 10 hari kerja sejak
                // diterima. Angkanya dibaca dari SlaLayanan, bukan ditulis di
                // sini, supaya panel dan portal tidak pernah menjanjikan dua
                // tenggat berbeda atas berkas yang sama.
                'batas_waktu_tanggapan' => SlaLayanan::batasPermohonan(),
                // Tenggat awal disimpan terpisah supaya perpanjangan 7 hari
                // kerja tidak menghapus jejak janji pertamanya.
                'batas_waktu_awal' => SlaLayanan::batasPermohonan(),
                // Register Permohonan publik memuat pokok permohonan tanpa
                // identitas pemohon; hal ini disebut pada butir ketentuan yang
                // wajib dibaca sebelum pernyataan dicentang.
                'tampil_di_register_publik' => true,
            ]));
        } catch (\Throwable $e) {
            Log::error('[PPID] Gagal menyimpan permohonan portal: '.$e->getMessage());

            return back()->withInput()->with('status', __('Permohonan gagal disimpan. Coba lagi beberapa saat lagi.'));
        }

        // Di luar transaksi: notifikasi ke panel admin tidak boleh menggagalkan
        // permohonan yang sudah tersimpan.
        NotifikasiAdmin::permohonanBaru($permohonan, $pemohon);

        // Tanda terima ke pemohon. Pemberitahuan berikutnya (diterima &
        // selesai) dikirim dari panel admin saat statusnya berpindah.
        EmailPemohon::pengajuanDikirim($permohonan, $pemohon);

        return redirect()->route('akun.permohonan.index')
            ->with('status', __('Permohonan terkirim dengan nomor registrasi :kode.', ['kode' => $permohonan->kode_permohonan]));
    }

    public function show(int $permohonan): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        $baris = PermohonanInformasi::with(['survei', 'keberatan', 'logStatus', 'tanggapanFiles'])
            ->where('pemohon_id', $pemohon->id)
            ->findOrFail($permohonan);

        return view('akun.permohonan.show', ['permohonan' => $baris, 'pemohon' => $pemohon]);
    }

    /**
     * Unduh satu berkas tanggapan milik permohonan sendiri.
     *
     * Dua penjagaan, keduanya perlu: berkasnya harus milik permohonan akun ini,
     * dan permohonannya harus sudah diserahkan. Tanpa penjagaan kedua, dokumen
     * yang masih disiapkan petugas — dan belum disetujui PPID — sudah bisa
     * ditarik pemohon dengan menebak nomor berkasnya.
     */
    public function berkasTanggapan(int $berkas)
    {
        $pemohon = Auth::guard('pemohon')->user();

        $baris = PermohonanTanggapanFile::with('permohonan')->findOrFail($berkas);
        $permohonan = $baris->permohonan;

        abort_unless($permohonan && $permohonan->pemohon_id === $pemohon->id, 403);
        abort_unless($permohonan->tanggapanTerbukaUntukPemohon(), 403, __('Tanggapan permohonan ini belum diserahkan.'));

        $disk = Storage::disk('public');

        abort_unless($disk->exists($baris->path_file), 404);

        return $disk->download($baris->path_file, $baris->nama_file);
    }

    /** Daftar permohonan milik akun: tab status, pencarian, pengurutan, pagination. */
    private function daftar(Request $request, int $pemohonId): LengthAwarePaginator
    {
        $cari = $request->string('cari')->toString();
        $kelompok = $this->kelompokStatus($request);

        return PermohonanInformasi::with('survei')
            ->where('pemohon_id', $pemohonId)
            ->when($kelompok !== '', fn ($q) => $q->whereIn('status', PermohonanInformasi::statusKelompokPortal($kelompok)))
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('kode_permohonan', 'ilike', '%'.$cari.'%')
                        ->orWhere('rincian_informasi', 'ilike', '%'.$cari.'%');
                });
            })
            ->orderBy($this->kolomUrut($request), $this->arahUrut($request))
            ->paginate($this->perHalaman($request))
            ->withQueryString();
    }

    private function kolomUrut(Request $request): string
    {
        $kolom = $request->string('urut')->toString();

        return in_array($kolom, self::KOLOM_URUT, true) ? $kolom : 'tanggal_permohonan';
    }

    /** Tab status aktif; string kosong berarti tab "Semua". */
    private function kelompokStatus(Request $request): string
    {
        $nilai = $request->string('status')->toString();

        return in_array($nilai, PermohonanInformasi::KELOMPOK_PORTAL, true) ? $nilai : '';
    }

    /** Jumlah baris per tab, supaya angkanya tampil di sebelah nama tab. */
    private function jumlahPerStatus(int $pemohonId): array
    {
        $mentah = PermohonanInformasi::where('pemohon_id', $pemohonId)
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $hasil = ['' => (int) $mentah->sum()];

        foreach (PermohonanInformasi::KELOMPOK_PORTAL as $label) {
            $hasil[$label] = collect(PermohonanInformasi::statusKelompokPortal($label))
                ->sum(fn ($status) => (int) ($mentah[$status] ?? 0));
        }

        return $hasil;
    }

    private function arahUrut(Request $request): string
    {
        return $request->string('arah')->toString() === 'asc' ? 'asc' : 'desc';
    }

    private function perHalaman(Request $request): int
    {
        $per = (int) $request->integer('per');

        return in_array($per, self::PER_HALAMAN, true) ? $per : 10;
    }

    /**
     * Nomor registrasi unik: PPID-FSTJ/<tanggal>/<urutan harian>.
     * Baris hari itu dikunci di dalam transaksi supaya dua pengajuan
     * bersamaan tidak mendapat nomor yang sama.
     */
    private function kodeBaru(): string
    {
        $prefix = 'PPID-FSTJ/'.now()->format('Ymd').'/';

        $terakhir = PermohonanInformasi::withTrashed()
            ->where('kode_permohonan', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('kode_permohonan')
            ->value('kode_permohonan');

        $urutan = $terakhir ? ((int) substr($terakhir, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}
