@extends('layouts.portal')

@section('title', __('Dashboard Pengguna') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Dashboard'))

@section('portal')

    @php
        /* Warna per kelompok status pada kartu ringkasan. */
        $warna = [
            'Dalam Proses' => '#FD8B02',
            'Selesai' => '#10462F',
        ];

        /*
         * Warna seri grafik, satu per tahun, dari yang terbaru. Empat cukup:
         * grafik membandingkan tahun berjalan dengan paling banyak tiga tahun
         * sebelumnya.
         */
        $warnaTahun = ['#10462F', '#FD8B02', '#3E9C6C', '#D9CBAD'];
        $warnaSeri = [];

        foreach ($tahunGrafik as $i => $th) {
            $warnaSeri[$th] = $warnaTahun[$i] ?? '#5B6660';
        }

        // Skala sumbu Y: batang tertinggi di seluruh bulan & tahun.
        $maks = 1;

        foreach ($grafik as $kolom) {
            $maks = max($maks, ...array_values($kolom['nilai']));
        }
    @endphp

    @include('akun.partials.alert-verifikasi')
    @include('akun.partials.alert-survei')

    {{-- Angka utama --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="{{ $cardTier(0) }} rounded-2xl p-6 text-white">
            <p class="text-sm font-semibold text-white/80">{{ __('Total Permohonan Informasi') }}</p>
            <p class="text-4xl font-extrabold mt-2">{{ $totalPermohonan }}</p>
            <a href="{{ route('akun.permohonan.index') }}" class="inline-block mt-4 text-sm font-bold underline">{{ __('Lihat daftar') }}</a>
        </div>
        <div class="{{ $cardTier(1) }} rounded-2xl p-6 text-white">
            <p class="text-sm font-semibold text-white/80">{{ __('Total Permohonan Keberatan') }}</p>
            <p class="text-4xl font-extrabold mt-2">{{ $totalKeberatan }}</p>
            <a href="{{ route('akun.keberatan.index') }}" class="inline-block mt-4 text-sm font-bold underline">{{ __('Lihat daftar') }}</a>
        </div>
    </div>

    {{-- Rincian per status --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach ([
            ['judul' => __('Statistik Permohonan Informasi'), 'data' => $ringkasanPermohonan],
            ['judul' => __('Statistik Permohonan Keberatan'), 'data' => $ringkasanKeberatan],
        ] as $blok)
            <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6">
                <h2 class="text-base font-bold text-gray-900 dark:text-white mb-4">{!! $judulDua($blok['judul'], 1) !!}</h2>
                <dl class="space-y-2.5">
                    @foreach ($blok['data'] as $label => $jumlah)
                        <div class="flex items-center justify-between gap-4">
                            <dt class="flex items-center gap-2.5 text-sm text-gray-600 dark:text-gray-300">
                                <span class="w-2.5 h-2.5 rounded-full" style="background: {{ $warna[$label] }}"></span>
                                {{ __($label) }}
                            </dt>
                            <dd class="text-sm font-bold text-gray-900 dark:text-white">{{ $jumlah }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        @endforeach
    </div>

    {{-- Grafik pengajuan: 12 bulan × satu batang per tahun, tanpa pustaka luar.
         Bulan yang sama berdiri sejajar antar tahun — itu inti perbandingannya. --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6">
        <div class="flex flex-wrap items-baseline justify-between gap-2 mb-1">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Grafik Data Pengajuan'), 1) !!}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">
                {{ __('Perbandingan per bulan, :jumlah tahun terakhir', ['jumlah' => count($tahunGrafik)]) }}
            </p>
        </div>

        <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4 mb-6">
            @foreach ($tahunGrafik as $th)
                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <span class="w-3 h-3 rounded-sm" style="background: {{ $warnaSeri[$th] }}"></span>
                    {{ $th }}
                    <span class="text-gray-400 dark:text-gray-500">({{ $totalPerTahun[$th] ?? 0 }})</span>
                </span>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <div class="flex items-end gap-3 min-w-[720px] h-56 border-b border-gray-100 dark:border-white/10 pb-1">
                @foreach ($grafik as $kolom)
                    <div class="flex-1 flex items-end justify-center gap-1 h-full">
                        @foreach ($tahunGrafik as $th)
                            @php $jumlah = $kolom['nilai'][$th] ?? 0; @endphp
                            {{-- Batang nol tetap dirender setipis garis supaya
                                 urutan tahunnya terbaca sama di tiap bulan. --}}
                            <div class="flex-1 flex flex-col justify-end items-center h-full"
                                 title="{{ $kolom['label'] }} {{ $th }}: {{ $jumlah }}">
                                @if ($jumlah > 0)
                                    <span class="text-[10px] font-bold text-gray-700 dark:text-gray-200">{{ $jumlah }}</span>
                                @endif
                                <div class="w-full rounded-t-sm"
                                     style="height: {{ $jumlah > 0 ? max(2, round($jumlah / $maks * 100)) : 1 }}%; background: {{ $jumlah > 0 ? $warnaSeri[$th] : '#E9DFC9' }}"
                                     aria-label="{{ $kolom['label'] }} {{ $th }}: {{ $jumlah }}"></div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="flex gap-3 min-w-[720px] mt-2">
                @foreach ($grafik as $kolom)
                    <span class="flex-1 text-center text-[11px] text-gray-500 dark:text-gray-400">{{ $kolom['label'] }}</span>
                @endforeach
            </div>
        </div>

        @if (array_sum($totalPerTahun) === 0)
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                {{ __('Belum ada pengajuan pada rentang tahun yang ditampilkan.') }}
            </p>
        @endif
    </div>

@endsection
