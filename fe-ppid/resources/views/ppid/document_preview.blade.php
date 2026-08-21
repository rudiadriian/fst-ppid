@extends('layouts.app')

@section('title', $dokumen->teks('judul') . ' | ' . __('Informasi Publik PPID FSTJ'))

@section('content')

    @php
        $keadaan = $akses['keadaan'];
        $permohonan = $akses['permohonan'];
        $adaBerkas = $berkas !== null;
    @endphp

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-14 lg:py-16">
            <a href="{{ route('ppid.information.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white transition mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                {{ __('Kembali ke Daftar Informasi Publik') }}
            </a>

            <div class="flex flex-wrap items-center gap-3 mb-4">
                @if ($dokumen->kategori)
                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-white/15 text-white">{{ $dokumen->kategori->teks('nama') }}</span>
                @endif

                @if ($dokumen->unduhan_terbatas)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs font-semibold rounded-full bg-white/15 text-white">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        {{ __('Unduhan terbatas') }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-snug max-w-5xl">{{ $dokumen->teks('judul') }}</h1>

            @if ($dokumen->teks('ringkasan'))
                <p class="mt-4 max-w-4xl text-base font-normal text-white/80 leading-relaxed">{{ $dokumen->teks('ringkasan') }}</p>
            @endif
        </div>
    </section>

    {{-- ISI --}}
    <section class="py-14 lg:py-16 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8 space-y-8">

            @if (session('status'))
                <div class="rounded-2xl border border-amber-200 bg-amber-50 dark:border-amber-500/30 dark:bg-amber-500/10 p-5">
                    <p class="text-base font-medium text-amber-900 dark:text-amber-200">{{ session('status') }}</p>
                </div>
            @endif

            {{-- DI LIHAT SAJA — memakai tautan yang memang sudah ada pada
                 dokumennya, bukan berkas yang diunggah ulang. Terbuka untuk
                 siapa saja, tanpa masuk. --}}
            @if ($dokumen->tautan)
                <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-7">
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Baca dokumennya') }}</h2>
                            <p class="mt-1 max-w-2xl text-sm font-normal text-gray-600 dark:text-gray-300">
                                {{ __('Isi dokumen dapat dibaca langsung tanpa masuk, di halaman resminya.') }}
                            </p>
                        </div>

                        <a href="{{ $dokumen->tautan }}" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl border border-gray-200 dark:border-white/20 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H18a2 2 0 012 2v4.5M20 8l-7.5 7.5M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"></path></svg>
                            {{ __('Di Lihat Saja') }}
                        </a>
                    </div>
                </div>
            @endif

            {{-- UNDUH — keadaannya ditentukan App\Support\AksesDokumen. --}}
            <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-7">
                @if (!$adaBerkas)
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Salinan untuk diunduh belum tersedia') }}</h2>
                    <p class="mt-1 max-w-2xl text-sm font-normal text-gray-600 dark:text-gray-300">
                        {{ __('Petugas PPID belum mengunggah berkas salinan dokumen ini. Isinya tetap dapat dibaca lewat tautan di atas.') }}
                    </p>

                @elseif ($keadaan === 'bebas' || $keadaan === 'terbuka')
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Salinan dokumen tersedia') }}</h2>
                            <p class="mt-1 text-sm font-normal text-gray-600 dark:text-gray-300">
                                @if ($keadaan === 'terbuka' && $permohonan)
                                    {{ __('Dibuka berdasarkan permohonan nomor :kode yang telah disetujui petugas.', ['kode' => $permohonan->kode_permohonan]) }}
                                @else
                                    {{ __('Dokumen ini dapat diunduh tanpa permohonan.') }}
                                @endif
                            </p>
                        </div>

                        <a href="{{ route('ppid.dokumen.unduh', $dokumen->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl text-white fs-btn-cta hover:brightness-110 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            {{ __('Unduh Dokumen') }}
                        </a>
                    </div>

                @elseif ($keadaan === 'masuk')
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Ingin mengunduh salinannya?') }}</h2>
                            <p class="mt-1 max-w-2xl text-sm font-normal text-gray-600 dark:text-gray-300">
                                {{ __('Untuk memperoleh salinan dokumen, masuk ke Portal Pemohon lalu ajukan permohonan informasi — salinan diberikan setelah permohonan Anda disetujui petugas.') }}
                            </p>
                        </div>

                        <a href="{{ route('ppid.dokumen.ajukan', $dokumen->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl text-white fs-btn-cta hover:brightness-110 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h5a3 3 0 013 3v1"></path></svg>
                            {{ __('Masuk & Ajukan Permohonan') }}
                        </a>
                    </div>

                @elseif ($keadaan === 'ajukan')
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Ajukan permohonan untuk mengunduh') }}</h2>
                            <p class="mt-1 max-w-2xl text-sm font-normal text-gray-600 dark:text-gray-300">
                                {{ __('Salinan dokumen ini diberikan setelah permohonan Anda disetujui petugas PPID. Judul dokumen akan terisi otomatis pada formulir permohonan.') }}
                            </p>
                        </div>

                        <a href="{{ route('ppid.dokumen.ajukan', $dokumen->id) }}"
                           class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl text-white fs-btn-cta hover:brightness-110 transition-all duration-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            {{ __('Ajukan Permohonan') }}
                        </a>
                    </div>

                @else
                    {{-- menunggu --}}
                    <div class="flex flex-wrap items-center justify-between gap-4">
                        <div>
                            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Permohonan Anda sedang diproses') }}</h2>
                            <p class="mt-1 max-w-2xl text-sm font-normal text-gray-600 dark:text-gray-300">
                                @if ($permohonan)
                                    {{ __('Permohonan nomor :kode atas dokumen ini masih menunggu keputusan petugas. Tombol unduh terbuka begitu permohonan disetujui.', ['kode' => $permohonan->kode_permohonan]) }}
                                @else
                                    {{ __('Permohonan atas dokumen ini masih menunggu keputusan petugas.') }}
                                @endif
                            </p>
                        </div>

                        @if ($permohonan)
                            <a href="{{ route('akun.permohonan.show', $permohonan->id) }}"
                               class="inline-flex items-center gap-2 px-5 py-3 text-sm font-bold rounded-xl border border-gray-200 dark:border-white/20 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200">
                                {{ __('Lihat Status Permohonan') }}
                            </a>
                        @endif
                    </div>
                @endif
            </div>

        </div>
    </section>

@endsection
