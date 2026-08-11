@extends('layouts.app')

@section('title', ($data['title'] ?? 'Regulasi') . ' | PPID FSTJ')

{{-- Penggambar sampul PDF hanya dimuat di halaman ini, bukan di seluruh situs. --}}
@push('scripts')
    @vite('resources/js/sampul-pdf.js')
@endpush

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Landasan Hukum') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title'] ?? 'REGULASI'), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">
                {{ __($data['description'] ?? 'Peraturan dan ketentuan yang menjadi landasan operasional PPID PT Food Station Tjipinang Jaya (Perseroda).') }}
            </p>
        </div>
    </section>

    {{-- KONTEN — daftar kartu: sampul dokumen di kiri, keterangan di kanan
         (mengikuti contoh tampilan regulasi.png). --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            <div x-data="{ cari: '' }">
                <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {!! $judulDua(__('DAFTAR REGULASI'), 1) !!}
                        <span class="ml-2 align-middle text-sm font-semibold text-gray-500 dark:text-gray-400">{{ count($data['regulations']) }} {{ __('Dokumen') }}</span>
                    </h2>

                    <div class="w-full md:w-auto md:min-w-[360px] relative">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input x-model="cari" type="search" placeholder="{{ __('Cari judul peraturan...') }}"
                               aria-label="{{ __('Cari judul peraturan') }}"
                               class="w-full pl-11 pr-4 py-3 bg-white dark:bg-[#0B2A1D] border border-gray-200 dark:border-white/10 rounded-xl focus:border-[#E87317] focus:ring-2 focus:ring-[#E87317]/20 outline-none transition-all text-base font-normal">
                    </div>
                </div>

                <div class="space-y-5">
                    @foreach ($data['regulations'] as $regulation)
                        <article x-show="cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($regulation['title'])) }}'.includes(cari.toLowerCase())"
                                 class="group relative bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                            <div class="flex flex-col sm:flex-row">

                                {{-- Sampul dokumen: halaman pertama berkas yang
                                     diunggah. PDF digambar ke <canvas> lewat
                                     pdf.js (lihat resources/js/sampul-pdf.js)
                                     supaya hasilnya sama di semua peramban,
                                     gambar ditampilkan apa adanya. Selama
                                     berkas belum tergambar — atau bila gagal —
                                     lambang dokumen yang tampil. --}}
                                <div class="sm:w-56 lg:w-64 flex-shrink-0 bg-white dark:bg-[#0B2A1D] border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-white/10 flex items-center justify-center p-4">
                                    <div class="relative w-full h-56 sm:h-64 overflow-hidden rounded-xl border border-gray-100 dark:border-white/10 bg-white"
                                         @if (!empty($regulation['link']) && $regulation['ext'] === 'pdf') data-pdf-cover="{{ $regulation['link'] }}" @endif
                                         role="img"
                                         aria-label="{{ __('Halaman pertama dokumen') }}: {{ $regulation['title'] }}">
                                        @if (!empty($regulation['link']) && in_array($regulation['ext'], ['jpg', 'jpeg', 'png', 'webp'], true))
                                            <img src="{{ $regulation['link'] }}" alt="{{ $regulation['title'] }}" loading="lazy"
                                                 class="absolute inset-0 w-full h-full object-cover object-top">
                                        @else
                                            @include('partials.regulasi_sampul_cadangan')
                                        @endif
                                    </div>
                                </div>

                                {{-- Keterangan --}}
                                <div class="flex-1 p-6 sm:p-7">
                                    <div class="flex flex-wrap items-center gap-3 mb-3">
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                            {{ $regulation['type'] === 'Dasar Hukum PPID' ? 'bg-blue-50 text-blue-600' :
                                            ($regulation['type'] === 'Pedoman' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-[#10462F]') }}">
                                            {{ __($regulation['type']) }}
                                        </span>

                                        {{-- Tanggal terbit dokumen di situs ini. --}}
                                        @if (!empty($regulation['published']))
                                            <span class="inline-flex items-center gap-1.5 text-sm font-normal text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                {{ \App\Support\Cms::tanggalWaktu($regulation['published']) }}
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white leading-snug mb-3">
                                        @if (!empty($regulation['url']))
                                            {{-- Seluruh kartu bisa diklik: tautan ini dilebarkan
                                                 lewat ::after supaya area kliknya sekartu penuh. --}}
                                            <a href="{{ $regulation['url'] }}"
                                               class="after:absolute after:inset-0 hover:text-[#10462F] dark:hover:text-[#3E9C6C] transition-colors">
                                                {{ $regulation['title'] }}
                                            </a>
                                        @else
                                            {{ $regulation['title'] }}
                                        @endif
                                    </h3>

                                    @if (!empty($regulation['ringkasan']))
                                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-4">
                                            {{ \Illuminate\Support\Str::limit($regulation['ringkasan'], 220) }}
                                        </p>
                                    @endif

                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        {{-- Pengunggah dokumen. --}}
                                        <span class="inline-flex items-center gap-2 text-sm font-normal text-gray-500 dark:text-gray-400">
                                            <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="PT Food Station Tjipinang Jaya (Perseroda)"
                                                 class="w-6 h-6 rounded-full object-contain bg-white ring-1 ring-gray-100 dark:ring-white/10">
                                            {{ __('Diunggah oleh') }}
                                            <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $regulation['pengunggah'] ?: __('Petugas PPID') }}</span>
                                        </span>

                                        @if (!empty($regulation['url']))
                                            <span class="relative z-10 inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 group-hover:bg-[#FAF6EC] dark:group-hover:bg-white/5 transition-colors duration-200">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                {{ __('Lihat') }}
                                            </span>
                                        @elseif (empty($regulation['link']))
                                            <span class="text-sm font-normal text-gray-400 dark:text-gray-500">{{ __('Belum tersedia') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </article>
                    @endforeach

                    @if (empty($data['regulations']))
                        <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 px-6 py-16 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada daftar regulasi yang tersedia saat ini.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
@endsection
