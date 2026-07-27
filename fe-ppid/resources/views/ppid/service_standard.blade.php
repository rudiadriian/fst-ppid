@extends('layouts.app')

@section('title', $data['title'] . ' | Standar Pelayanan PPID')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Standar Pelayanan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __($data['title']) }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Komitmen pelayanan informasi publik PPID Food Station yang cepat, mudah, dan transparan.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="{{ $slug === 'jalur-waktu-layanan' ? 'max-w-6xl' : 'max-w-4xl' }} mx-auto px-6 lg:px-8">
            <div class="bg-white dark:bg-[#121a17] p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{{ __('Detail') }} {{ __($data['title']) }}</h2>

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

                @elseif ($slug === 'prosedur-permohonan')
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-12">{{ __($data['intro']) }}</p>
                    <div class="relative pl-6">
                        <div class="absolute top-2 bottom-2 left-6 w-0.5 bg-emerald-100"></div>
                        <div class="space-y-8">
                            @foreach ($data['steps'] as $index => $step)
                                <div class="relative flex items-start gap-5">
                                    <span class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-[#008060] text-white text-base font-semibold rounded-full relative z-10 -ml-6 ring-4 ring-white">{{ $index + 1 }}</span>
                                    <div class="flex-1 p-6 bg-[#F8FAFC] dark:bg-[#0d1310] rounded-2xl border border-gray-100 dark:border-white/10">
                                        <p class="text-base font-normal text-gray-700 dark:text-gray-300 leading-relaxed">{{ __($step) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-12 text-center">
                        <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#008060] hover:bg-[#00664e] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                            {{ __('Mulai Ajukan Permohonan') }}
                        </a>
                    </div>

                @elseif ($slug === 'jalur-waktu-layanan')
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-10">{{ __($data['intro']) }}</p>
                    <div class="space-y-6">
                        {{-- Jalur Pelayanan --}}
                        <div class="p-6 sm:p-8 rounded-2xl bg-[#F8FAFC] dark:bg-[#0d1310] border border-gray-100 dark:border-white/10">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#008060] rounded-2xl flex items-center justify-center flex-shrink-0">
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
                                @foreach ($data['channels'] as $channel)
                                    <div class="h-full flex flex-col rounded-xl bg-white dark:bg-[#121a17] border border-gray-100 dark:border-white/10 p-5 shadow-sm">
                                        <span class="w-10 h-10 mb-4 bg-emerald-50 text-[#008060] rounded-xl flex items-center justify-center flex-shrink-0 dark:bg-[#00A66C]/15 dark:text-[#00A66C]">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $channelIcons[$channel['label']] ?? $channelIcons['Surat'] }}"></path></svg>
                                        </span>
                                        <div class="flex flex-wrap items-center gap-2 mb-2">
                                            <span class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#008060] dark:text-[#00A66C]">{{ __($channel['label']) }}</span>
                                            @if ($channel['recommended'])
                                                <span class="text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 rounded-full bg-emerald-50 text-[#00664e] dark:bg-[#00A66C]/15 dark:text-[#00A66C]">{{ __('Direkomendasikan') }}</span>
                                            @endif
                                        </div>
                                        <p class="text-sm font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ __($channel['desc']) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Waktu Layanan --}}
                        <div class="p-6 sm:p-8 rounded-2xl bg-[#F8FAFC] dark:bg-[#0d1310] border border-gray-100 dark:border-white/10">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#008060] rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <h3 class="text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white min-w-0">{{ __('Waktu Layanan') }}</h3>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($data['hours'] as $schedule)
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
