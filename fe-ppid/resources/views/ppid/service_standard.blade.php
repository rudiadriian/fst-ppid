@extends('layouts.app')

@section('title', $data['title'] . ' | Standar Pelayanan PPID')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Standar Pelayanan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Komitmen pelayanan informasi publik PPID Food Station yang cepat, mudah, dan transparan.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="{{ $slug === 'jalur-waktu-layanan' ? 'max-w-screen-2xl' : 'max-w-6xl' }} mx-auto px-6 lg:px-8">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Detail').' '.__($data['title'])) !!}</h2>

                @if ($slug === 'maklumat-pelayanan')
                    <div class="p-8 rounded-2xl fs-gradient text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <p class="text-xl font-semibold leading-relaxed mb-8 relative z-10">"{{ __($data['intro']) }}"</p>
                        <ul class="space-y-4 border-t border-white/20 pt-6 relative z-10">
                            @foreach ($data['content_list'] as $item)
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-white flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="text-base font-normal text-white/90 leading-relaxed">{{ __($item) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-8 text-sm font-normal text-white/70 border-t border-white/20 pt-4 relative z-10">{{ __($data['footer']) }}</p>
                    </div>

                @elseif (in_array($slug, ['prosedur-permohonan', 'prosedur-keberatan'], true))
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-12">{{ __($data['intro']) }}</p>

                    {{-- Alur ringkas berupa kartu bernomor. Untuk Prosedur
                         Permohonan, blok ini dipindahkan dari beranda. --}}
                    @if (!empty($data['flow']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-x-5 lg:gap-x-6 mb-14">
                            @foreach ($data['flow'] as $i => $langkah)
                                {{-- Wrapper relatif (tanpa overflow) agar konektor tidak terpotong --}}
                                <div class="group relative h-full">
                                    <div class="relative h-full {{ $cardTier($i) }} rounded-3xl p-7 shadow-xl shadow-accent-200/60 dark:shadow-black/30 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 overflow-hidden">
                                        {{-- Nomor watermark --}}
                                        <span class="absolute -top-3 -right-1 text-[6rem] leading-none font-extrabold text-white/25 group-hover:text-white/40 transition-colors select-none">{{ $i + 1 }}</span>

                                        <div class="relative z-10">
                                            <div class="flex items-center gap-3 mb-5">
                                                <div class="w-14 h-14 bg-white text-[#E87317] rounded-full flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $langkah['icon'] }}"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-white uppercase tracking-widest">{{ __('Langkah') }} {{ $i + 1 }}</span>
                                            </div>
                                            <h3 class="text-lg font-bold text-white mb-1.5">{{ __($langkah['title']) }}</h3>
                                            <p class="text-sm text-white/90 leading-relaxed">{{ __($langkah['desc']) }}</p>
                                        </div>
                                    </div>

                                    {{-- Line penghubung + panah antar kartu dalam 1 baris (desktop) --}}
                                    @if (($i + 1) % 3 !== 0 && $i + 1 < count($data['flow']))
                                        <div class="hidden lg:block absolute top-1/2 left-full -translate-y-1/2 w-6 z-20">
                                            <div class="h-[3px] w-full fs-gradient-accent rounded-full opacity-70"></div>
                                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white dark:bg-[#0B2A1D] border border-accent-200 shadow-md flex items-center justify-center text-[#E87317]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Rincian Tahapan'), 1) !!}</h3>
                    @endif

                    <div class="relative pl-6">
                        <div class="absolute top-2 bottom-2 left-6 w-0.5 bg-emerald-100"></div>
                        <div class="space-y-8">
                            @foreach ($data['steps'] as $index => $step)
                                <div class="relative flex items-start gap-5">
                                    <span class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-[#10462F] text-white text-base font-semibold rounded-full relative z-10 -ml-6 ring-4 ring-white">{{ $index + 1 }}</span>
                                    <div class="flex-1 p-6 bg-[#FAF6EC] dark:bg-[#082217] rounded-2xl border border-gray-100 dark:border-white/10">
                                        <p class="text-base font-normal text-gray-700 dark:text-gray-300 leading-relaxed">{{ __($step) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-12 text-center">
                        @if ($slug === 'prosedur-keberatan')
                            <a href="{{ route('ppid.objection') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#10462F] hover:bg-[#0B3524] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                                {{ __('Ajukan Keberatan') }}
                            </a>
                        @else
                            <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#10462F] hover:bg-[#0B3524] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                                {{ __('Mulai Ajukan Permohonan') }}
                            </a>
                        @endif
                    </div>

                @elseif ($slug === 'jalur-waktu-layanan')
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-10">{{ __($data['intro']) }}</p>
                    <div class="space-y-6">
                        {{-- Jalur Pelayanan --}}
                        <div class="p-6 sm:p-8 rounded-2xl bg-[#FAF6EC] dark:bg-[#082217] border border-gray-100 dark:border-white/10">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#10462F] rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                                </span>
                                <h3 class="text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white min-w-0">{{ __('Jalur Pelayanan') }}</h3>
                            </div>
                            @php
                                $channelIcons = [
                                    'Online' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3c2.5 3 2.5 15 0 18-2.5-3-2.5-15 0-18z',
                                    'Langsung' => 'M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.66-.13-1.3-.36-1.86M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.66.13-1.3.36-1.86m0 0A5 5 0 0112 13a5 5 0 014.64 3.14M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                                    'Surat' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z',
                                ];
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                                @foreach ($data['channels'] as $i => $channel)
                                    <div class="h-full flex flex-col rounded-xl {{ $cardTier($i) }} p-5 shadow-lg shadow-accent-200/60 dark:shadow-black/30">
                                        <span class="w-10 h-10 mb-4 bg-white text-[#E87317] rounded-full flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $channelIcons[$channel['label']] ?? $channelIcons['Surat'] }}"></path></svg>
                                        </span>
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="text-[11px] font-bold uppercase tracking-[0.14em] text-white">{{ __($channel['label']) }}</span>
                                            @if ($channel['recommended'])
                                                <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-white text-[#9E470D]">{{ __('Direkomendasikan') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-normal text-white/90 leading-relaxed">{{ __($channel['desc']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Waktu Layanan --}}
                        <div class="p-6 sm:p-8 rounded-2xl bg-[#FAF6EC] dark:bg-[#082217] border border-gray-100 dark:border-white/10">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#10462F] rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <h3 class="text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white min-w-0">{{ __('Waktu Layanan') }}</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($data['hours'] as $schedule)
                                    <div class="rounded-xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 p-5 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#10462F] dark:text-[#3E9C6C]">{{ __($schedule['days']) }}</p>
                                        <p class="mt-2 text-lg sm:text-xl font-bold text-gray-900 dark:text-white tabular-nums break-words">{{ $schedule['time'] }}</p>
                                        <p class="mt-2 flex items-start gap-1.5 text-xs font-normal text-gray-500 dark:text-gray-400">
                                            <svg class="w-4 h-4 mt-px flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v2a2 2 0 01-2 2h-2M5 8h12v7a4 4 0 01-4 4H9a4 4 0 01-4-4V8zM3 21h18"></path></svg>
                                            <span class="tabular-nums">{{ __('Istirahat') }} {{ $schedule['break'] }}</span>
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-5 flex items-start gap-2.5 text-sm font-normal text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-white/10 pt-4 leading-relaxed">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ __($data['note']) }}</span>
                            </p>
                        </div>
                    </div>

                @else
                    <div class="text-center py-12 bg-red-50 border border-red-100 rounded-2xl">
                        <p class="text-red-600 font-semibold text-lg">{{ __('Konten Standar Pelayanan tidak ditemukan.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
