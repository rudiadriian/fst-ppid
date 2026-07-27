@extends('layouts.app')
@section('title', $data['title'] . ' | PPID FSTJ')
@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Profil PPID') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __($data['title']) }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Informasi mengenai Pejabat Pengelola Informasi dan Dokumentasi (PPID) Food Station.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <div class="bg-white dark:bg-[#121a17] p-6 sm:p-10 lg:p-12 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">
                @if ($slug === 'singkat')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ __('Profil Singkat PPID Food Station') }}</h2>

                    <div class="mb-12">
                        <p class="text-base text-gray-700 dark:text-gray-300 leading-relaxed border-l-4 border-[#008060] pl-5 py-4 bg-[#F8FAFC] dark:bg-[#0d1310] rounded-r-xl">
                            {{ __($data['intro']) }}
                        </p>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-12">
                        <div class="p-8 rounded-2xl bg-white dark:bg-[#121a17] border border-gray-100 dark:border-white/10 shadow-sm">
                            <div class="w-14 h-14 mb-5 bg-emerald-50 text-[#008060] rounded-2xl flex items-center justify-center">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.525.321 1.157.498 1.724 1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <h3 class="text-[22px] font-semibold text-gray-900 dark:text-white mb-5">{{ __('Fungsi Utama PPID') }}</h3>
                            <ul class="space-y-3">
                                @foreach ($data['functions'] as $function)
                                    <li class="flex items-start gap-3">
                                        <svg class="w-5 h-5 mt-0.5 text-[#008060] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ __($function) }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-8 rounded-2xl bg-white dark:bg-[#121a17] border border-gray-100 dark:border-white/10 shadow-sm">
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

                    <div class="p-6 sm:p-8 rounded-2xl bg-[#F8FAFC] dark:bg-[#0d1310] border border-gray-100 dark:border-white/10">
                        <h3 class="text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
                            <span class="w-10 h-10 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </span>
                            <span class="min-w-0">{{ __('Waktu Layanan Informasi Publik') }}</span>
                        </h3>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach ($data['service_hours'] as $schedule)
                                <div class="rounded-xl bg-white dark:bg-[#121a17] border border-gray-100 dark:border-white/10 p-5 shadow-sm">
                                    <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#008060] dark:text-[#00A66C]">{{ __($schedule['days']) }}</p>
                                    <p class="mt-2 text-lg sm:text-xl font-bold text-gray-900 dark:text-white tabular-nums break-words">{{ $schedule['time'] }}</p>
                                    <p class="mt-2 flex items-start gap-1.5 text-xs font-normal text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mt-px flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v2a2 2 0 01-2 2h-2M5 8h12v7a4 4 0 01-4 4H9a4 4 0 01-4-4V8zM3 21h18"></path></svg>
                                        <span class="tabular-nums">{{ __('Istirahat') }} {{ $schedule['break'] }}</span>
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <p class="mt-5 flex items-start gap-2.5 text-sm font-normal text-gray-500 dark:text-gray-400 leading-relaxed">
                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>{{ __('Layanan dilaksanakan setiap hari kerja (Senin s.d. Jumat) kecuali hari libur nasional atau cuti bersama.') }}</span>
                        </p>
                    </div>

                @elseif ($slug === 'struktur')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Struktur Organisasi PPID') }}</h2>
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-10">{{ __($data['content']) }}</p>

                    <div class="bg-[#F8FAFC] dark:bg-[#0d1310] border border-gray-100 dark:border-white/10 rounded-2xl p-8 flex flex-col items-center justify-center min-h-[400px]">
                        <p class="text-gray-400 dark:text-gray-500 text-sm text-center mb-8">{{ __('[ Diagram Struktur Organisasi ]') }}</p>
                        <div class="flex flex-col items-center space-y-4">
                            <div class="bg-[#008060] text-white py-2.5 px-7 rounded-xl font-semibold shadow-sm">{{ __('Atasan PPID') }}</div>
                            <span class="w-px h-5 bg-gray-300"></span>
                            <div class="bg-[#00664e] text-white py-2.5 px-7 rounded-xl font-semibold shadow-sm">{{ __('PPID Utama') }}</div>
                            <span class="w-px h-5 bg-gray-300"></span>
                            <div class="flex flex-wrap justify-center gap-4">
                                <div class="bg-white dark:bg-[#121a17] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 py-2 px-5 rounded-xl text-sm font-medium shadow-sm">{{ __('PPID Pembantu 1') }}</div>
                                <div class="bg-white dark:bg-[#121a17] border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-300 py-2 px-5 rounded-xl text-sm font-medium shadow-sm">{{ __('PPID Pembantu 2') }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="#" class="inline-flex items-center gap-2 px-6 py-3.5 bg-[#008060] hover:bg-[#00664e] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            {{ __('Unduh Dokumen Struktur (PDF)') }}
                        </a>
                    </div>

                @elseif ($slug === 'visi-misi')
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-10">{{ __('Visi dan Misi PPID') }}</h2>

                    <div class="mb-10 p-8 lg:p-10 rounded-2xl fs-gradient text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-3 relative z-10">Visi</p>
                        <p class="text-2xl font-semibold leading-relaxed relative z-10">"{{ __($data['content']['Visi']) }}"</p>
                    </div>

                    <div>
                        <h3 class="text-[22px] font-semibold text-gray-900 dark:text-white mb-6">Misi</h3>
                        <ul class="space-y-4">
                            @foreach ($data['content']['Misi'] as $index => $misi)
                                <li class="flex items-start gap-4 bg-[#F8FAFC] dark:bg-[#0d1310] p-5 rounded-2xl border border-gray-100 dark:border-white/10">
                                    <span class="flex-shrink-0 w-9 h-9 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center font-semibold">{{ $index + 1 }}</span>
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
