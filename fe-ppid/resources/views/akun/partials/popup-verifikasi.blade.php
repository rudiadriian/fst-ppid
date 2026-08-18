{{--
    Penghalang Verifikasi Data Diri Pemohon.

    Selama data diri belum disetujui petugas, portal pengguna tidak bisa
    dipakai: lapisan ini menutup halaman dan hanya menyediakan satu jalan
    keluar — membuka modul Data Pemohon & Berkas (atau keluar dari akun).

    Halaman Data Pemohon sendiri tidak ikut ditutup; kalau ikut, pemohon tidak
    akan pernah bisa memenuhi syaratnya. Pembatasan yang sebenarnya tetap ada
    di sisi server (lihat PermohonanController dan KeberatanController) —
    lapisan ini urusan tampilan, bukan pengaman.
--}}
@php
    $pemohon = auth('pemohon')->user();

    $halamanDikecualikan = [
        'akun.data-pemohon',
        'akun.data-pemohon.update',
        'akun.data-pemohon.ktp',
        'akun.logout',
    ];

    $blokirSaatMenunggu = (bool) config('ppid.akun.blokir_saat_menunggu', true);

    $perluHalangi = $pemohon
        && !$pemohon->dataTerverifikasi()
        && !in_array(request()->route()?->getName(), $halamanDikecualikan, true)
        && request()->is('akun*')
        && ($blokirSaatMenunggu || !$pemohon->verifikasiMenunggu());

    $sla = (int) config('ppid.akun.sla_verifikasi_hari_kerja', 14);
@endphp

@if ($perluHalangi)
    <div class="fixed inset-0 z-[9998] bg-black/60 backdrop-blur-sm flex items-center justify-center p-4"
         role="alertdialog" aria-modal="true" aria-labelledby="judul-halangi" aria-describedby="isi-halangi">

        <div class="w-full max-w-md bg-white dark:bg-[#0B2A1D] rounded-2xl shadow-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">

            <span class="w-12 h-12 rounded-2xl flex items-center justify-center
                         {{ $pemohon->verifikasiMenunggu() ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
            </span>

            <h2 id="judul-halangi" class="mt-4 text-xl font-bold text-gray-900 dark:text-white">
                {{ __('Verifikasi Data Diri Pemohon') }}
            </h2>

            <div id="isi-halangi" class="mt-2 space-y-3 text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                @if ($pemohon->verifikasiDiblokir())
                    <p>{{ __('Data diri Anda sudah ditolak :batas kali, sehingga pengiriman ulang ditutup. Hubungi petugas PPID untuk melanjutkan.', ['batas' => \App\Models\Pemohon::BATAS_DITOLAK]) }}</p>
                @elseif ($pemohon->verifikasiMenunggu())
                    <p>{{ __('Berkas Anda sudah kami terima dan sedang diperiksa petugas PPID. Layanan portal terbuka kembali setelah data Anda disetujui.') }}</p>
                @elseif ($pemohon->status_verifikasi === 'ditolak')
                    <p>{{ __('Data Anda belum dapat disetujui petugas. Perbaiki isian dan berkas KTP Anda, lalu kirim ulang untuk diperiksa.') }}</p>
                    <p>{{ __('Sisa kesempatan kirim ulang: :sisa dari :batas.', ['sisa' => $pemohon->sisaKesempatanVerifikasi(), 'batas' => \App\Models\Pemohon::BATAS_DITOLAK]) }}</p>
                @else
                    <p>{{ __('Sebelum memakai layanan PPID, identitas Anda harus diverifikasi lebih dulu. Lengkapi Data Pemohon & Berkas — termasuk mengunggah KTP — lalu kirim untuk diperiksa petugas.') }}</p>
                @endif

                @if (filled($pemohon->catatan_verifikasi))
                    <p class="p-3 rounded-xl bg-amber-50 border border-amber-100 text-amber-900 dark:bg-amber-400/10 dark:border-amber-400/20 dark:text-amber-200">
                        <span class="font-semibold block">{{ __('Catatan petugas') }}</span>
                        {{ $pemohon->catatan_verifikasi }}
                    </p>
                @endif

                <p class="p-3 rounded-xl bg-gray-50 dark:bg-white/5 border border-gray-100 dark:border-white/10">
                    {{ __('Pemeriksaan berkas memerlukan waktu paling lama :hari hari kerja sejak berkas lengkap diterima.', ['hari' => $sla]) }}
                </p>

                <p class="font-medium text-gray-800 dark:text-gray-200">
                    {{ __('Status saat ini') }}: {{ $pemohon->labelStatusVerifikasi() }}
                </p>
            </div>

            @unless ($pemohon->verifikasiDiblokir())
                <a href="{{ route('akun.data-pemohon') }}"
                   class="mt-6 w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 fs-gradient-accent text-white text-base font-bold rounded-xl hover:-translate-y-0.5 transition-transform">
                    {{ __('Buka Data Pemohon & Berkas') }}
                </a>
            @endunless

            <form method="POST" action="{{ route('akun.logout') }}" class="mt-3">
                @csrf
                <button type="submit" class="w-full py-2.5 text-sm font-semibold text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition-colors">
                    {{ __('Keluar dari akun') }}
                </button>
            </form>
        </div>
    </div>
@endif
