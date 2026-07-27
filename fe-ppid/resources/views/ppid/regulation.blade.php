@extends('layouts.app')

@section('title', 'Regulasi | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Landasan Hukum') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __($data['title'] ?? 'Daftar Regulasi') }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __($data['description'] ?? 'Peraturan dan ketentuan yang menjadi landasan operasional PPID Food Station.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            <div class="bg-white dark:bg-[#121a17] rounded-2xl shadow-sm border border-gray-100 dark:border-white/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="bg-[#F8FAFC] dark:bg-[#0d1310] border-b border-gray-100 dark:border-white/10">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-12">{{ __('No.') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider min-w-[300px]">{{ __('Judul Regulasi') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-40">{{ __('Nomor/Tahun') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-32">{{ __('Kategori') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 dark:text-gray-500 uppercase tracking-wider w-28">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($data['regulations'] as $index => $regulation)
                                <tr class="hover:bg-[#F8FAFC] dark:bg-[#0d1310] transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-400 dark:text-gray-500">{{ $regulation['no'] }}</td>
                                    <td class="px-6 py-4 whitespace-normal text-base font-medium text-gray-900 dark:text-white max-w-lg">{{ __($regulation['title']) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-normal text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ $regulation['number'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full
                                            {{ $regulation['type'] == 'Internal PPID' ? 'bg-emerald-50 text-[#008060]' :
                                            ($regulation['type'] == 'Hukum Negara' ? 'bg-blue-50 text-blue-600' : 'bg-amber-50 text-amber-600') }}">
                                            {{ __($regulation['type']) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ $regulation['link'] }}" target="_blank"
                                           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white bg-[#008060] hover:bg-[#00664e] transition-colors duration-200">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @if (empty($data['regulations']))
                                <tr>
                                    <td colspan="5" class="px-6 py-16 text-center">
                                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-base font-normal text-gray-500 dark:text-gray-400 dark:text-gray-500">{{ __('Belum ada daftar regulasi yang tersedia saat ini.') }}</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Catatan --}}
            <div class="mt-8 p-6 sm:p-8 bg-emerald-50 rounded-2xl border border-emerald-100 flex items-start gap-4">
                <span class="w-10 h-10 flex-shrink-0 bg-emerald-100 text-[#008060] rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </span>
                <div>
                    <p class="text-base font-semibold text-gray-900 dark:text-white mb-1">{{ __('Catatan Penting') }}</p>
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">
                        {{ __('Dokumen regulasi di halaman ini tersedia untuk diunduh secara langsung (Setiap Saat). Jika membutuhkan dokumen regulasi spesifik lainnya, silakan gunakan menu') }}
                        <a href="{{ route('ppid.request') }}" class="font-semibold text-[#008060] hover:text-[#00664e]">{{ __('Permohonan Informasi Publik') }}</a>.
                    </p>
                </div>
            </div>

        </div>
    </section>
@endsection
