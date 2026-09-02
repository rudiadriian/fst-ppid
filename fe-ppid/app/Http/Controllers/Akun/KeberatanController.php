<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\KeberatanFile;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
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
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Modul Permohonan Keberatan pada Portal Pengguna.
 *
 * Keberatan selalu menunjuk satu permohonan milik akun yang sama, jadi
 * dropdown-nya hanya berisi permohonan sendiri.
 */
class KeberatanController extends Controller
{
    private const PER_HALAMAN = [10, 25, 50, 100];

    private const KOLOM_URUT = ['tanggal_keberatan', 'status'];

    public function index(Request $request): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        return view('akun.keberatan.index', [
            'pemohon' => $pemohon,
            'daftar' => $this->daftar($request, $pemohon->id),
            'cari' => $request->string('cari')->toString(),
            'urut' => $this->kolomUrut($request),
            'arah' => $this->arahUrut($request),
            'per' => $this->perHalaman($request),
            'opsiPer' => self::PER_HALAMAN,
            'punyaPermohonan' => PermohonanInformasi::where('pemohon_id', $pemohon->id)->exists(),
            'status' => $this->kelompokStatus($request),
            'jumlahStatus' => $this->jumlahPerStatus($pemohon->id),
        ]);
    }

    public function create(): View|RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        // Sama seperti Permohonan: identitas harus jelas sebelum berkas
        // keberatan diproses. Pembatasan ditegakkan di server, bukan hanya
        // dengan menyembunyikan tombol.
        if (!$pemohon->dataTerverifikasi()) {
            return redirect()->route('akun.data-pemohon')
                ->with('status', __('Lengkapi dan verifikasi Data Pemohon dulu sebelum mengajukan keberatan.'));
        }

        return view('akun.keberatan.create', [
            'pemohon' => $pemohon,
            /*
             * Permohonan yang punya dasar keberatan menurut Pasal 35 UU KIP —
             * bukan sekadar yang sudah tuntas (langkah 101). Aturannya di
             * {@see PermohonanInformasi::layakDikeberatankan()}.
             */
            'permohonanSaya' => PermohonanInformasi::where('pemohon_id', $pemohon->id)
                ->layakDikeberatankan()
                ->orderByDesc('tanggal_permohonan')
                ->get(['id', 'kode_permohonan', 'status', 'rincian_informasi', 'batas_waktu_tanggapan']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $pemohon = Auth::guard('pemohon')->user();

        if (!$pemohon->dataTerverifikasi()) {
            return redirect()->route('akun.data-pemohon')
                ->with('status', __('Lengkapi dan verifikasi Data Pemohon dulu sebelum mengajukan keberatan.'));
        }

        $data = $request->validate([
            'permohonan_id' => ['required', 'integer'],
            'jenis_keberatan' => ['required', 'in:'.implode(',', array_keys(KeberatanInformasi::JENIS))],
            'kasus_posisi' => ['required', 'string', 'max:2000'],
            'dikuasakan' => ['nullable', 'boolean'],
            // Lampiran dokumen keberatan: PDF/gambar, maksimal 10 MB per berkas.
            'lampiran.*' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $permohonan = PermohonanInformasi::where('pemohon_id', $pemohon->id)->find($data['permohonan_id']);

        if (!$permohonan) {
            return back()->withInput()->withErrors([
                'permohonan_id' => __('Permohonan tidak ditemukan pada akun Anda.'),
            ]);
        }

        // Diperiksa ulang di server, tidak dipercaya dari daftar pilihan:
        // isian tersembunyi bisa diubah sebelum dikirim.
        if (!$permohonan->layakDikeberatankan()) {
            return back()->withInput()->withErrors([
                'permohonan_id' => __('Keberatan hanya dapat diajukan atas permohonan yang sudah ditanggapi, ditolak, atau yang batas waktu tanggapannya sudah lewat.'),
            ]);
        }

        try {
            $keberatan = DB::transaction(function () use ($request, $data, $pemohon, $permohonan) {
                $keberatan = KeberatanInformasi::create([
                    'permohonan_id' => $permohonan->id,
                    'pemohon_id' => $pemohon->id,
                    'jenis_keberatan' => $data['jenis_keberatan'],
                    // Kolom `alasan_keberatan` wajib di DB; isinya kasus posisi
                    // yang ditulis pemohon, jenisnya disimpan terpisah.
                    'alasan_keberatan' => $data['kasus_posisi'],
                    'kasus_posisi' => $data['kasus_posisi'],
                    'dikuasakan' => (bool) ($data['dikuasakan'] ?? false),
                    'status' => 'diajukan',
                    // Jalur pelayanannya mengikuti permohonan yang
                    // dikeberatankan; petugas tetap bisa mengubahnya saat
                    // menerima berkas (langkah 89).
                    'jalur_pelayanan' => $permohonan->jalur_pelayanan ?: 'online',
                    'tanggal_keberatan' => now(),
                    // Tanggapan atas keberatan paling lambat 30 hari sejak
                    // diregistrasi. Hari kalender, bukan hari kerja — itu yang
                    // membedakannya dari tenggat permohonan.
                    'batas_waktu_tanggapan' => SlaLayanan::batasKeberatan(),
                ]);

                foreach ((array) $request->file('lampiran', []) as $berkas) {
                    if (!$berkas) {
                        continue;
                    }

                    $path = $berkas->storeAs(
                        'uploads/keberatan',
                        Str::uuid().'.'.$berkas->getClientOriginalExtension(),
                        'public'
                    );

                    KeberatanFile::create([
                        'keberatan_id' => $keberatan->id,
                        'nama_file' => $berkas->getClientOriginalName(),
                        'path_file' => $path,
                    ]);
                }

                return $keberatan;
            });
        } catch (\Throwable $e) {
            Log::error('[PPID] Gagal menyimpan keberatan portal: '.$e->getMessage());

            return back()->withInput()->with('status', __('Keberatan gagal disimpan. Coba lagi beberapa saat lagi.'));
        }

        // refresh() wajib dan harus lebih dulu: kode_keberatan dilahirkan
        // trigger basis data, jadi baris yang baru saja dibuat belum memuatnya
        // di memori — lonceng panel maupun surel tanda terima sama-sama
        // mencetak nomor itu.
        $keberatan->refresh();

        // Di luar transaksi: notifikasi ke panel admin tidak boleh menggagalkan
        // keberatan yang sudah tersimpan.
        NotifikasiAdmin::keberatanBaru($keberatan, $permohonan, $pemohon);

        // Relasi permohonan diisi supaya nomor induknya bisa dicetak di email
        // tanpa query tambahan.
        $keberatan->setRelation('permohonan', $permohonan);
        EmailPemohon::pengajuanDikirim($keberatan, $pemohon);

        return redirect()->route('akun.keberatan.index')
            ->with('status', __('Keberatan Anda sudah kami terima dengan nomor registrasi :kode.', ['kode' => $keberatan->kode_keberatan]));
    }

    /**
     * Rincian satu keberatan milik akun ini.
     *
     * Sebelum langkah 101 keberatan tidak punya halaman rincian sama sekali:
     * seluruh isinya harus terbaca dari satu baris tabel yang terpotong, dan
     * tanggapan petugas tidak punya tempat untuk ditampilkan.
     */
    public function show(int $keberatan): View
    {
        $pemohon = Auth::guard('pemohon')->user();

        $baris = KeberatanInformasi::with(['permohonan', 'berkas'])
            ->where('pemohon_id', $pemohon->id)
            ->findOrFail($keberatan);

        return view('akun.keberatan.show', ['keberatan' => $baris, 'pemohon' => $pemohon]);
    }

    private function daftar(Request $request, int $pemohonId): LengthAwarePaginator
    {
        $cari = $request->string('cari')->toString();
        $kelompok = $this->kelompokStatus($request);

        return KeberatanInformasi::with(['permohonan', 'berkas'])
            ->where('pemohon_id', $pemohonId)
            ->when($kelompok !== '', fn ($q) => $q->whereIn('status', KeberatanInformasi::statusKelompokPortal($kelompok)))
            ->when($cari !== '', function ($q) use ($cari) {
                $q->where(function ($sub) use ($cari) {
                    $sub->where('kode_keberatan', 'ilike', '%'.$cari.'%')
                        ->orWhere('kasus_posisi', 'ilike', '%'.$cari.'%')
                        ->orWhere('alasan_keberatan', 'ilike', '%'.$cari.'%')
                        ->orWhereHas('permohonan', fn ($p) => $p->where('kode_permohonan', 'ilike', '%'.$cari.'%'));
                });
            })
            ->orderBy($this->kolomUrut($request), $this->arahUrut($request))
            ->paginate($this->perHalaman($request))
            ->withQueryString();
    }

    private function kolomUrut(Request $request): string
    {
        $kolom = $request->string('urut')->toString();

        return in_array($kolom, self::KOLOM_URUT, true) ? $kolom : 'tanggal_keberatan';
    }

    /** Tab status aktif; string kosong berarti tab "Semua". */
    private function kelompokStatus(Request $request): string
    {
        $nilai = $request->string('status')->toString();

        return in_array($nilai, PermohonanInformasi::KELOMPOK_PORTAL, true) ? $nilai : '';
    }

    private function jumlahPerStatus(int $pemohonId): array
    {
        $mentah = KeberatanInformasi::where('pemohon_id', $pemohonId)
            ->selectRaw('status, count(*) as jumlah')
            ->groupBy('status')
            ->pluck('jumlah', 'status');

        $hasil = ['' => (int) $mentah->sum()];

        foreach (PermohonanInformasi::KELOMPOK_PORTAL as $label) {
            $hasil[$label] = collect(KeberatanInformasi::statusKelompokPortal($label))
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

    /** Unduh lampiran keberatan — hanya pemilik keberatannya. */
    public function berkas(int $berkas)
    {
        $pemohon = Auth::guard('pemohon')->user();

        $baris = KeberatanFile::with('keberatan')->findOrFail($berkas);

        abort_unless($baris->keberatan && $baris->keberatan->pemohon_id === $pemohon->id, 403);

        $disk = Storage::disk('public');

        abort_unless($disk->exists($baris->path_file), 404);

        return $disk->download($baris->path_file, $baris->nama_file);
    }
}
