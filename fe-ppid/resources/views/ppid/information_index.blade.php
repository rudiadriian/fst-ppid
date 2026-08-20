@extends('layouts.app')

@section('title', __($data['title']) . ' | ' . __('PPID FSTJ'))
@section('content')

    {{-- HERO --}}
    {{-- `bg-[#08281B]` tetap dipasang sebagai dasar: bila gambarnya gagal
         dimuat, hero-nya kembali ke hijau gelap, bukan jadi putih. --}}
    <section class="relative bg-[#08281B] overflow-hidden">
        @include('partials.latar_informasi', ['opasitas' => 0.66, 'terang' => 0.68, 'muat' => 'eager'])
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Informasi Publik') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">{{ __($data['description']) }}</p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            {{-- Ringkasan jumlah dokumen per klasifikasi. Tombolnya sekaligus
                 menyaring daftar di bawah tanpa memuat ulang halaman. --}}
            <div x-data="{ cari: '', kategori: '' }">
                @if (!empty($data['kelompok']))
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                        <button type="button" @click="kategori = ''"
                                class="text-left rounded-2xl border p-5 transition-colors duration-200"
                                :class="kategori === '' ? 'border-[#10462F] bg-white dark:bg-[#0B2A1D] dark:border-[#3E9C6C]' : 'border-gray-100 dark:border-white/10 bg-white/70 dark:bg-[#0B2A1D]/70 hover:bg-white dark:hover:bg-[#0B2A1D]'">
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tabular-nums">{{ count($data['items']) }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Semua Klasifikasi') }}</p>
                        </button>

                        @foreach ($data['kelompok'] as $namaKategori => $jumlah)
                            <button type="button" @click="kategori = '{{ addslashes($namaKategori) }}'"
                                    class="text-left rounded-2xl border p-5 transition-colors duration-200"
                                    :class="kategori === '{{ addslashes($namaKategori) }}' ? 'border-[#10462F] bg-white dark:bg-[#0B2A1D] dark:border-[#3E9C6C]' : 'border-gray-100 dark:border-white/10 bg-white/70 dark:bg-[#0B2A1D]/70 hover:bg-white dark:hover:bg-[#0B2A1D]'">
                                <p class="text-3xl font-extrabold text-gray-900 dark:text-white tabular-nums">{{ $jumlah }}</p>
                                <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __($namaKategori) }}</p>
                            </button>
                        @endforeach
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {!! $judulDua(__('Daftar Informasi Publik'), 1) !!}
                        <span class="ml-2 align-middle text-sm font-semibold text-gray-500 dark:text-gray-400">{{ count($data['items']) }} {{ __('Entri') }}</span>
                    </h2>

                    <div class="w-full md:w-auto md:min-w-[360px] relative">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input x-model="cari" type="search" placeholder="{{ __('Cari informasi...') }}"
                               aria-label="{{ __('Cari informasi') }}"
                               class="w-full pl-11 pr-4 py-3 bg-white dark:bg-[#0B2A1D] border border-gray-200 dark:border-white/10 rounded-xl focus:border-[#E87317] focus:ring-2 focus:ring-[#E87317]/20 outline-none transition-all text-base font-normal">
                    </div>
                </div>

                {{-- Tabel dipakai mulai layar sedang; di ponsel isinya jadi kartu
                     supaya tidak perlu digeser ke samping. --}}
                <div class="hidden md:block bg-white dark:bg-[#0B2A1D] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-[#FAF6EC] dark:bg-[#082217] border-b border-gray-100 dark:border-white/10">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-16">{{ __('No.') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">{{ __('Informasi') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-64">{{ __('Klasifikasi') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-44">{{ __('Dokumen') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @foreach ($data['items'] as $item)
                                <tr x-show="(cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($item['name'])) }}'.includes(cari.toLowerCase()))
                                            && (kategori === '' || kategori === '{{ addslashes($item['kategori']) }}')"
                                    class="hover:bg-[#FAF6EC] dark:hover:bg-white/5 transition-colors duration-150">
                                    <td class="px-6 py-4 align-top whitespace-nowrap text-sm font-medium text-gray-400 dark:text-gray-500 tabular-nums">{{ $item['no'] }}</td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</p>
                                        @if (!empty($item['ringkasan']))
                                            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400 leading-relaxed">{{ $item['ringkasan'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top">
                                        @if (!empty($item['kategori_slug']))
                                            <a href="{{ route('ppid.information', $item['kategori_slug']) }}"
                                               class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-50 text-[#9E470D] hover:brightness-95 transition">
                                                {{ __($item['kategori']) }}
                                            </a>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-50 text-[#9E470D]">{{ __($item['kategori']) }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top text-center">
                                        @include('partials.informasi_aksi', ['item' => $item])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (empty($data['items']))
                        <div class="px-6 py-16 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada informasi publik yang diterbitkan.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Versi ponsel --}}
                <div class="md:hidden space-y-4">
                    @foreach ($data['items'] as $item)
                        <div x-show="(cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($item['name'])) }}'.includes(cari.toLowerCase()))
                                     && (kategori === '' || kategori === '{{ addslashes($item['kategori']) }}')"
                             class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-50 text-[#9E470D]">{{ __($item['kategori']) }}</span>
                                <span class="text-sm font-medium text-gray-400 dark:text-gray-500 tabular-nums">#{{ $item['no'] }}</span>
                            </div>

                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['name'] }}</p>

                            @if (!empty($item['ringkasan']))
                                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400 leading-relaxed">{{ $item['ringkasan'] }}</p>
                            @endif

                            <div class="mt-4">
                                @include('partials.informasi_aksi', ['item' => $item])
                            </div>
                        </div>
                    @endforeach

                    @if (empty($data['items']))
                        <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 px-6 py-16 text-center">
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada informasi publik yang diterbitkan.') }}</p>
                        </div>
                    @endif
                </div>

            </div>

        </div>
    </section>

@endsection
