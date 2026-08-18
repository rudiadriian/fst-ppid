<?php

namespace App\Http\Controllers\Akun;

use App\Http\Controllers\Controller;
use App\Models\KeberatanFile;
use App\Models\KeberatanInformasi;
use App\Models\PermohonanInformasi;
use App\Support\EmailPemohon;
use App\Support\NotifikasiAdmin;
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
            'permohonanSaya' => PermohonanInformasi::where('pemohon_id', $pemohon->id)
                ->orderByDesc('tanggal_permohonan')
                ->get(['id', 'kode_permohonan', 'status', 'rincian_informasi']),
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
                    'tanggal_keberatan' => now(),
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

        // Di luar transaksi: notifikasi ke panel admin tidak boleh menggagalkan
        // keberatan yang sudah tersimpan.
        NotifikasiAdmin::keberatanBaru($keberatan, $permohonan, $pemohon);

        // Tanda terima ke pemohon; relasi permohonan diisi lebih dulu supaya
        // nomor registrasinya bisa dicetak di email tanpa query tambahan.
        $keberatan->setRelation('permohonan', $permohonan);
        EmailPemohon::pengajuanDikirim($keberatan, $pemohon);

        return redirect()->route('akun.keberatan.index')
            ->with('status', __('Keberatan Anda sudah kami terima.'));
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
                    $sub->where('kasus_posisi', 'ilike', '%'.$cari.'%')
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
