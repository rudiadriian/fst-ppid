@extends('layouts.portal')

@section('title', 'Dashboard Pengguna | PPID FSTJ')
@section('portal-judul', __('Dashboard'))

@section('portal')

    @php
        /* Warna per kelompok status, dipakai kartu ringkasan, legend, dan grafik. */
        $warna = [
            'Dalam Proses' => '#FD8B02',
            'Revisi' => '#FFA849',
            'Menunggu Persetujuan' => '#3E9C6C',
            'Tolak' => '#C2410C',
            'Selesai' => '#10462F',
        ];
        $maks = max(1, collect($grafik)->max('total'));
    @endphp

    @include('akun.partials.alert-verifikasi')

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

    {{-- Grafik pengajuan per bulan (batang bertumpuk, tanpa pustaka luar) --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6">
        <div class="flex flex-wrap items-baseline justify-between gap-2 mb-1">
            <h2 class="text-base font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Grafik Data Pengajuan'), 1) !!}</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $bulan }}</p>
        </div>

        <div class="flex flex-wrap gap-x-5 gap-y-2 mt-4 mb-6">
            @foreach ($warna as $label => $hex)
                <span class="flex items-center gap-2 text-xs font-semibold text-gray-600 dark:text-gray-300">
                    <span class="w-3 h-3 rounded-sm" style="background: {{ $hex }}"></span>{{ __($label) }}
                </span>
            @endforeach
        </div>

        <div class="overflow-x-auto">
            <div class="flex items-end gap-3 min-w-[640px] h-56 border-b border-gray-100 dark:border-white/10 pb-1">
                @foreach ($grafik as $kolom)
                    <div class="flex-1 flex flex-col justify-end items-center gap-1.5 h-full">
                        <span class="text-xs font-bold text-gray-700 dark:text-gray-200">{{ $kolom['total'] ?: '' }}</span>
                        <div class="w-full flex flex-col-reverse rounded-t-md overflow-hidden"
                             style="height: {{ $kolom['total'] ? round($kolom['total'] / $maks * 100) : 0 }}%"
                             title="{{ $kolom['label'] }}: {{ $kolom['total'] }}">
                            @foreach ($kolom['nilai'] as $label => $jumlah)
                                @if ($jumlah > 0)
                                    <div style="height: {{ round($jumlah / max(1, $kolom['total']) * 100) }}%; background: {{ $warna[$label] }}"
                                         aria-label="{{ __($label) }}: {{ $jumlah }}"></div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex gap-3 min-w-[640px] mt-2">
                @foreach ($grafik as $kolom)
                    <span class="flex-1 text-center text-[11px] text-gray-500 dark:text-gray-400">{{ $kolom['label'] }}</span>
                @endforeach
            </div>
        </div>

        @if (collect($grafik)->sum('total') === 0)
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada pengajuan dalam 12 bulan terakhir.') }}</p>
        @endif
    </div>

@endsection
