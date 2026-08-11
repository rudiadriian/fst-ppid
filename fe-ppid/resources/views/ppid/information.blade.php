@extends('layouts.app')

@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Informasi Publik') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__('Daftar Informasi').' '.__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            {{-- =========================================================
                 SUB-KATEGORI — kartu ringkasan + tombol "Selengkapnya".
                 Isinya murni dari modul Kategori Informasi di be-ppid:
                 kategori mana pun yang parent-nya kategori ini akan muncul
                 di sini tanpa perlu mengubah kode.
                 ========================================================= --}}
            @if (!empty($data['subkategori']))
                <div class="mb-12">
                    <div class="mb-8 max-w-2xl">
                        <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#10462F] dark:text-[#3E9C6C]">
                            <span class="w-6 h-px bg-[#10462F]/40 dark:bg-[#3E9C6C]/40"></span>
                            {{ __('Rincian Informasi') }}
                        </span>
                        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5">{!! $judulDua(__('Kelompok Informasi'), 1) !!}</h2>
                        <p class="mt-2.5 text-base font-normal text-gray-500 dark:text-gray-400 leading-relaxed">
                            {{ __('Pilih kelompok informasi di bawah ini untuk melihat daftar dokumen selengkapnya.') }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($data['subkategori'] as $i => $sub)
                            <div class="group h-full flex flex-col {{ $cardTier($i) }} rounded-3xl p-7 shadow-xl shadow-accent-200/60 dark:shadow-black/30 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
                                <div class="flex items-start justify-between gap-3 mb-5">
                                    <span class="w-14 h-14 flex-shrink-0 bg-white text-[#E87317] rounded-full flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"></path></svg>
                                    </span>
                                    <span class="px-3 py-1 rounded-full bg-white/25 text-white text-xs font-bold whitespace-nowrap">
                                        {{ $sub['jumlah'] }} {{ __('Dokumen') }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-bold text-white mb-1.5 leading-snug">{{ $sub['nama'] }}</h3>
                                <p class="text-sm text-white/90 leading-relaxed mb-6">
                                    {{ \Illuminate\Support\Str::limit($sub['deskripsi'] ?: __('Daftar dokumen pada kelompok informasi ini.'), 120) }}
                                </p>

                                <a href="{{ route('ppid.information', $sub['slug']) }}"
                                   class="mt-auto inline-flex items-center justify-center gap-2 w-full px-5 py-3 rounded-xl bg-white text-[#C85C10] text-sm font-bold hover:bg-cream-100 transition-colors duration-200">
                                    {{ __('Selengkapnya') }}
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- =========================================================
                 DAFTAR DOKUMEN — disembunyikan kalau halaman ini murni
                 halaman pengantar (punya sub-kategori, belum punya dokumen
                 sendiri), supaya tidak muncul tabel kosong.
                 ========================================================= --}}
            @if (!empty($data['items']) || empty($data['subkategori']))
                <div x-data="{ cari: '' }">
                    <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
                        {{-- Judul tabel mengikuti klasifikasi kategori (lihat PpidController@showPublicInformation). --}}
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{!! $judulDua(__($data['heading_dokumen']), 2) !!}</h2>
                        <div class="w-full md:w-auto md:min-w-[360px] relative">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            <input x-model="cari" type="search" placeholder="{{ __('Cari judul dokumen...') }}"
                                   aria-label="{{ __('Cari judul dokumen') }}"
                                   class="w-full pl-11 pr-4 py-3 bg-white dark:bg-[#0B2A1D] border border-gray-200 dark:border-white/10 rounded-xl focus:border-[#E87317] focus:ring-2 focus:ring-[#E87317]/20 outline-none transition-all text-base font-normal">
                        </div>
                    </div>

                    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full">
                                <thead>
                                    <tr class="bg-[#FAF6EC] dark:bg-[#082217] border-b border-gray-100 dark:border-white/10">
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">{{ __('No.') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider min-w-[300px] lg:min-w-[400px]">{{ __('Jenis Informasi') }}</th>
                                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">{{ __('Kategori') }}</th>
                                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-40">{{ __('Aksi') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                                    @foreach ($data['items'] as $item)
                                        <tr x-show="cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($item['name'])) }}'.includes(cari.toLowerCase())"
                                            class="hover:bg-[#FAF6EC] dark:hover:bg-white/5 transition-colors duration-150">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400 dark:text-gray-500">{{ $item['no'] }}</td>
                                            <td class="px-6 py-4 whitespace-normal text-base font-medium text-gray-900 dark:text-white">
                                                {{ __($item['name']) }}
                                                @if (!empty($item['ringkasan']))
                                                    <span class="mt-1 block text-sm font-normal text-gray-500 dark:text-gray-400">{{ $item['ringkasan'] }}</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-50 text-[#9E470D]">{{ __($data['title']) }}</span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                                @if (!empty($item['file']))
                                                    {{-- Tautan ke halaman lain memakai label "Selengkapnya";
                                                         berkas unggahan memakai "Lihat". --}}
                                                    <a href="{{ $item['file'] }}" target="_blank" rel="noopener"
                                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white fs-gradient-accent hover:brightness-110 transition-all duration-200">
                                                        @if ($item['jenis'] === 'tautan')
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.5 6H18a2 2 0 012 2v4.5M20 8l-7.5 7.5M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4"></path></svg>
                                                            {{ __('Selengkapnya') }}
                                                        @else
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                            {{ __('Lihat') }}
                                                        @endif
                                                    </a>
                                                @else
                                                    <span class="text-sm font-normal text-gray-400 dark:text-gray-500">{{ __('Belum tersedia') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach

                                    @if (empty($data['items']))
                                        <tr>
                                            <td colspan="4" class="px-6 py-16 text-center">
                                                <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                                <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada daftar informasi yang tersedia pada kategori ini.') }}</p>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </section>

@endsection
