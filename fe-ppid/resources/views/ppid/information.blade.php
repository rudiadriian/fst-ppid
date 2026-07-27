@extends('layouts.app')

@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Informasi Publik') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __('Daftar Informasi') }} {{ __($data['title']) }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">{{ __($data['description']) }}</p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="flex flex-col md:flex-row justify-between md:items-center mb-8 gap-4">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Dokumen & Arsip') }}</h2>
                <div class="w-full md:w-auto md:min-w-[360px] relative">
                    <svg class="w-5 h-5 text-gray-400 dark:text-gray-500 absolute left-4 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" placeholder="{{ __('Cari judul dokumen...') }}" class="w-full pl-11 pr-4 py-3 bg-white dark:bg-[#121a17] border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base font-normal">
                </div>
            </div>

            <div class="bg-white dark:bg-[#121a17] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-[#F8FAFC] dark:bg-[#0d1310] border-b border-gray-100 dark:border-white/10">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-16">{{ __('No.') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider min-w-[300px] lg:min-w-[400px]">{{ __('Jenis Informasi') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-40">{{ __('Kategori') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-40">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($data['items'] as $item)
                                <tr class="hover:bg-[#F8FAFC] dark:bg-[#0d1310] transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400 dark:text-gray-500">{{ $item['no'] }}</td>
                                    <td class="px-6 py-4 whitespace-normal text-base font-medium text-gray-900 dark:text-white">{{ __($item['name']) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-emerald-50 text-[#008060]">{{ __($data['title']) }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="#" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-[#008060] bg-emerald-50 hover:bg-[#008060] hover:text-white transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @if (empty($data['items']))
                                <tr>
                                    <td colspan="4" class="px-6 py-16 text-center">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-base font-normal text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Belum ada daftar informasi yang tersedia pada kategori ini.') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Info Pengecualian --}}
            <div class="mt-8 p-6 sm:p-8 bg-amber-50 rounded-2xl border border-amber-100 flex items-start gap-4">
                <span class="w-10 h-10 flex-shrink-0 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </span>
                <div>
                    <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('Informasi Pengecualian') }}</p>
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('Informasi yang dikecualikan (rahasia perusahaan, pribadi, dll.) tidak dapat diakses langsung di sini. Anda dapat mengajukan permohonan resmi melalui menu') }}
                        <a href="{{ route('ppid.request') }}" class="font-semibold text-[#008060] hover:text-[#00664e]">{{ __('Permohonan Informasi Publik') }}</a>.
                    </p>
                </div>
            </div>

        </div>
    </section>

@endsection
