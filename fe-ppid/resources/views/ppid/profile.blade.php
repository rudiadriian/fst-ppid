@extends('layouts.app')
@section('title', __($data['title']) . ' | ' . __('PPID FSTJ'))
@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Profil PPID') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Informasi mengenai Pejabat Pengelola Informasi dan Dokumentasi (PPID) Food Station.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-10 lg:p-12 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">
                @include('partials.db_notice')

                {{-- Bila halaman ini sudah dibuat di modul Halaman Statis (CMS),
                     isinya yang dipakai. Kalau belum, tata letak bawaan di bawah
                     tetap tampil supaya halaman tidak pernah kosong. --}}
                @if (!empty($data['html']))
                    <div class="fs-rte">
                        {!! strip_tags($data['html'], '<p><br><strong><em><u><ul><ol><li><h2><h3><h4><blockquote><a><img><table><thead><tbody><tr><th><td>') !!}
                    </div>
                @elseif ($slug === 'singkat')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Tentang PPID PT Food Station Tjipinang Jaya (Perseroda)'), 2) !!}</h2>

                    {{-- Tanpa jarak bawah: blok ini sekarang isi terakhir halaman
                         Profil Singkat, jadi jarak tambahan hanya menyisakan
                         ruang kosong di dalam kartu. --}}
                    <div class="space-y-4 border-l-4 border-[#10462F] pl-5 py-4 bg-[#FAF6EC] dark:bg-[#082217] rounded-r-xl">
                        @foreach ((array) $data['intro'] as $paragraf)
                            <p class="text-base text-gray-700 dark:text-gray-300 leading-relaxed">{{ __($paragraf) }}</p>
                        @endforeach
                    </div>

                    {{-- Blok "Waktu Layanan Informasi Publik" dilepas dari sini:
                         jam dan jalur layanan sudah disajikan lengkap di halaman
                         Standar Layanan → Jalur dan Waktu Layanan, jadi di profil
                         isinya hanya kembar. --}}

                @elseif ($slug === 'tugas-fungsi-wewenang')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Tugas, Fungsi dan Wewenang PPID'), 1) !!}</h2>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
                        <div class="p-8 rounded-2xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 mb-5 bg-emerald-50 text-[#10462F] rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.525.321 1.157.498 1.724 1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h3 class="text-[22px] font-semibold text-gray-900 dark:text-white mb-5">{{ __('Fungsi Utama PPID') }}</h3>
                            <ul class="space-y-3">
                                @foreach ($data['functions'] as $function)
                                    <li class="flex items-start gap-3">
                                        <svg class="w-5 h-5 mt-0.5 text-[#10462F] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ __($function) }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-8 rounded-2xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 mb-5 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                            <h3 class="text-[22px] font-semibold text-gray-900 dark:text-white mb-5">{{ __('Wewenang PPID') }}</h3>
                            <ul class="space-y-3">
                                @foreach ($data['authorities'] as $authority)
                                    <li class="flex items-start gap-3">
                                        <svg class="w-5 h-5 mt-0.5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ __($authority) }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                @elseif ($slug === 'struktur')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{!! $judulDua(__('Struktur Organisasi PPID')) !!}</h2>
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-10">{{ __($data['content']) }}</p>

                    {{-- Bagan digambar dari modul Struktur Organisasi (CMS),
                         bukan kotak contoh yang dipaku seperti sebelumnya. --}}
                    @if (!empty($data['bagan']))
                        @include('partials.bagan_struktur', [
                            'bagan' => $data['bagan'],
                            'judulBagan' => __('Bagan Struktur Organisasi PPID Food Station'),
                        ])
                    @else
                        <div class="bg-[#FAF6EC] dark:bg-[#082217] border border-gray-100 dark:border-white/10 rounded-2xl p-8 text-center">
                            <p class="text-gray-500 dark:text-gray-400 text-sm">{{ __('Bagan struktur belum diisi pada modul Struktur Organisasi.') }}</p>
                        </div>
                    @endif

                @elseif ($slug === 'visi-misi')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10">{!! $judulDua(__('Visi dan Misi PPID'), 2) !!}</h2>

                    <div class="mb-10 p-8 lg:p-10 rounded-2xl fs-gradient text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-3 relative z-10">Visi</p>
                        <p class="text-2xl font-semibold leading-relaxed relative z-10">"{{ __($data['content']['Visi']) }}"</p>
                    </div>

                    <div>
                        <h3 class="text-[22px] font-semibold text-gray-900 dark:text-white mb-6">Misi</h3>
                        <ul class="space-y-4">
                            @foreach ($data['content']['Misi'] as $index => $misi)
                                <li class="flex items-start gap-4 bg-[#FAF6EC] dark:bg-[#082217] p-5 rounded-2xl border border-gray-100 dark:border-white/10">
                                    <span class="flex-shrink-0 w-9 h-9 bg-emerald-50 text-[#10462F] rounded-xl flex items-center justify-center font-semibold">{{ $index + 1 }}</span>
                                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed pt-1">{{ __($misi) }}</p>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @else
                    <div class="text-center py-12 bg-red-50 border border-red-100 rounded-2xl">
                        <p class="text-red-600 font-semibold text-lg">{{ __('Konten halaman profil tidak dikenali atau belum tersedia.') }}</p>
                        <p class="text-red-500 text-sm mt-2">{{ __('Mohon periksa slug yang digunakan.') }}</p>
                    </div>
                @endif

            </div>
        </div>
    </section>

@endsection
