@extends('layouts.app')

@section('title', __('Daftar Informasi Dikecualikan') . ' | ' . __('PPID FSTJ'))
@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
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

            {{-- Penyajiannya mengikuti Daftar Informasi Publik: kartu ringkasan
                 yang sekaligus menyaring, lalu tabel pada layar sedang ke atas
                 dan kartu di ponsel. Daftar ini tidak punya klasifikasi, jadi
                 pengelompokannya memakai ketersediaan surat penetapan. --}}
            <div x-data="{ cari: '', saring: '' }">

                @if (!empty($data['items']))
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-10">
                        <button type="button" @click="saring = ''"
                                class="text-left rounded-2xl border p-5 transition-colors duration-200"
                                :class="saring === '' ? 'border-[#10462F] bg-white dark:bg-[#0B2A1D] dark:border-[#3E9C6C]' : 'border-gray-100 dark:border-white/10 bg-white/70 dark:bg-[#0B2A1D]/70 hover:bg-white dark:hover:bg-[#0B2A1D]'">
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tabular-nums">{{ count($data['items']) }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Semua Entri') }}</p>
                        </button>

                        <button type="button" @click="saring = 'ada'"
                                class="text-left rounded-2xl border p-5 transition-colors duration-200"
                                :class="saring === 'ada' ? 'border-[#10462F] bg-white dark:bg-[#0B2A1D] dark:border-[#3E9C6C]' : 'border-gray-100 dark:border-white/10 bg-white/70 dark:bg-[#0B2A1D]/70 hover:bg-white dark:hover:bg-[#0B2A1D]'">
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tabular-nums">{{ $data['kelompok']['ada'] }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Ada Surat Penetapan') }}</p>
                        </button>

                        <button type="button" @click="saring = 'belum'"
                                class="text-left rounded-2xl border p-5 transition-colors duration-200"
                                :class="saring === 'belum' ? 'border-[#10462F] bg-white dark:bg-[#0B2A1D] dark:border-[#3E9C6C]' : 'border-gray-100 dark:border-white/10 bg-white/70 dark:bg-[#0B2A1D]/70 hover:bg-white dark:hover:bg-[#0B2A1D]'">
                            <p class="text-3xl font-extrabold text-gray-900 dark:text-white tabular-nums">{{ $data['kelompok']['belum'] }}</p>
                            <p class="mt-1 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Belum Ada Surat Penetapan') }}</p>
                        </button>
                    </div>
                @endif

                <div class="flex flex-col md:flex-row justify-between md:items-center mb-6 gap-4">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {!! $judulDua(__('Daftar Informasi Dikecualikan'), 1) !!}
                        <span class="ml-2 align-middle text-sm font-semibold text-gray-500 dark:text-gray-400">{{ count($data['items']) }} {{ __('Entri') }}</span>
                    </h2>

                    <div class="w-full md:w-auto md:min-w-[360px] relative">
                        <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <input x-model="cari" type="search" placeholder="{{ __('Cari informasi dikecualikan...') }}"
                               aria-label="{{ __('Cari informasi dikecualikan') }}"
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
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-56">{{ __('Surat Penetapan') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @foreach ($data['items'] as $item)
                                <tr x-show="(cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($item['judul'])) }}'.includes(cari.toLowerCase()))
                                            && (saring === '' || saring === '{{ $item['file'] ? 'ada' : 'belum' }}')"
                                    class="hover:bg-[#FAF6EC] dark:hover:bg-white/5 transition-colors duration-150">
                                    <td class="px-6 py-4 align-top whitespace-nowrap text-sm font-medium text-gray-400 dark:text-gray-500 tabular-nums">{{ $item['no'] }}</td>
                                    <td class="px-6 py-4 align-top">
                                        <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['judul'] }}</p>
                                        @if (!empty($item['ringkasan']))
                                            <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400 leading-relaxed">{{ $item['ringkasan'] }}</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 align-top text-center">
                                        @include('partials.surat_penetapan_aksi', ['item' => $item])
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @if (empty($data['items']))
                        <div class="px-6 py-16 text-center">
                            <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada daftar informasi dikecualikan yang dipublikasikan.') }}</p>
                        </div>
                    @endif
                </div>

                {{-- Versi ponsel --}}
                <div class="md:hidden space-y-4">
                    @foreach ($data['items'] as $item)
                        <div x-show="(cari === '' || '{{ \Illuminate\Support\Str::lower(addslashes($item['judul'])) }}'.includes(cari.toLowerCase()))
                                     && (saring === '' || saring === '{{ $item['file'] ? 'ada' : 'belum' }}')"
                             class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm p-5">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-accent-50 text-[#9E470D]">
                                    {{ $item['file'] ? __('Ada Surat Penetapan') : __('Belum Ada Surat Penetapan') }}
                                </span>
                                <span class="text-sm font-medium text-gray-400 dark:text-gray-500 tabular-nums">#{{ $item['no'] }}</span>
                            </div>

                            <p class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['judul'] }}</p>

                            @if (!empty($item['ringkasan']))
                                <p class="mt-1 text-sm font-normal text-gray-500 dark:text-gray-400 leading-relaxed">{{ $item['ringkasan'] }}</p>
                            @endif

                            <div class="mt-4">
                                @include('partials.surat_penetapan_aksi', ['item' => $item])
                            </div>
                        </div>
                    @endforeach

                    @if (empty($data['items']))
                        <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 px-6 py-16 text-center">
                            <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada daftar informasi dikecualikan yang dipublikasikan.') }}</p>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </section>
@endsection
