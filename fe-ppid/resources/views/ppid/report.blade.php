@extends('layouts.app')

@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Laporan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">{{ __($data['description']) }}</p>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            {{-- Angka ringkas seluruh sistem (pindahan dari Beranda).
                 Hanya diisi controller pada halaman Statistik Informasi Publik. --}}
            @if (!empty($data['stats']))
                <div class="mb-10">
                    <p class="mb-3 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Angka Layanan Saat Ini') }}</p>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                        @foreach ($data['stats'] as $i => $s)
                            <div class="text-center py-8 rounded-3xl {{ $cardTier($i) }} shadow-lg shadow-accent-200/60 dark:shadow-black/30">
                                <p class="text-4xl lg:text-5xl font-extrabold text-white mb-1">{{ $s['value'] }}</p>
                                <p class="text-sm font-medium text-white/90">{{ __($s['label']) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Ringkasan tahun terbaru --}}
            @if ($data['summary'])
                <div class="mb-8">
                    <p class="mb-3 text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Ringkasan Tahun') }} {{ $data['summary']['tahun'] }}</p>
                    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                        @foreach ([
                            ['label' => 'Permohonan Masuk', 'value' => $data['summary']['masuk']],
                            ['label' => 'Dikabulkan', 'value' => $data['summary']['dikabulkan']],
                            ['label' => 'Ditolak', 'value' => $data['summary']['ditolak']],
                            ['label' => 'Keberatan', 'value' => $data['summary']['keberatan']],
                            ['label' => 'Rata-rata Respon (hari)', 'value' => $data['summary']['rata_rata']],
                        ] as $i => $stat)
                            <div class="rounded-2xl {{ $cardTier($i) }} p-5 shadow-lg shadow-accent-200/60 dark:shadow-black/30">
                                <p class="text-3xl font-bold text-white">{{ $stat['value'] }}</p>
                                <p class="mt-1 text-sm font-normal text-white/90">{{ __($stat['label']) }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Tabel laporan per periode --}}
            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-white/10 dark:bg-[#0B2A1D]">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-[#FAF6EC] dark:border-white/10 dark:bg-[#082217]">
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 min-w-[260px]">{{ __('Judul Laporan') }}</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-40">{{ __('Periode') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-24">{{ __('Masuk') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-28">{{ __('Dikabulkan') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-24">{{ __('Ditolak') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-28">{{ __('Keberatan') }}</th>
                                <th scope="col" class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-32">{{ __('Rata-rata Hari') }}</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400 w-28">{{ __('Aksi') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @forelse ($data['reports'] as $report)
                                <tr class="transition-colors duration-150 hover:bg-[#FAF6EC] dark:hover:bg-white/5">
                                    <td class="px-6 py-4 text-base font-medium text-gray-900 dark:text-white">
                                        {{ $report['judul'] }}
                                        @if ($report['ringkasan'])
                                            <span class="mt-1 block text-sm font-normal text-gray-500 dark:text-gray-400">{{ $report['ringkasan'] }}</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-normal text-gray-600 dark:text-gray-300">{{ $report['periode'] }} {{ $report['tahun'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $report['masuk'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-normal text-gray-600 dark:text-gray-300">{{ $report['dikabulkan'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-normal text-gray-600 dark:text-gray-300">{{ $report['ditolak'] + $report['ditolak_sebagian'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-normal text-gray-600 dark:text-gray-300">{{ $report['keberatan'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-right text-sm font-normal text-gray-600 dark:text-gray-300">{{ $report['rata_rata_hari'] ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-center">
                                        @if ($report['file'])
                                            <a href="{{ $report['file'] }}" target="_blank" rel="noopener"
                                               class="inline-flex items-center gap-1.5 rounded-lg bg-[#10462F] px-4 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#0B3524]">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                                {{ __('Unduh') }}
                                            </a>
                                        @else
                                            <span class="text-sm font-normal text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-16 text-center">
                                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-6m4 6V7m4 10v-4m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h10a2 2 0 012 2v14a2 2 0 01-2 2z"/></svg>
                                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada laporan yang dipublikasikan.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="mt-6 text-sm font-normal text-gray-500 dark:text-gray-400">
                {{ __('Angka pada laporan ini merupakan rekapitulasi permohonan informasi publik yang tercatat pada sistem PPID PT Food Station Tjipinang Jaya (Perseroda).') }}
            </p>

        </div>
    </section>
@endsection
