@extends('layouts.portal')

@section('title', __('Detail Keberatan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Detail Keberatan'))

@section('portal')

    @php
        $baris = [
            __('Nomor Keberatan') => $keberatan->kode_keberatan,
            __('Atas Permohonan') => $keberatan->permohonan->kode_permohonan ?? '—',
            __('Tanggal Pengajuan') => optional($keberatan->tanggal_keberatan)->translatedFormat('d F Y H:i') ?? '—',
            // Batas Waktu Tanggapan tidak ditampilkan; alasannya sama dengan
            // rincian permohonan (langkah 101).
            __('Alasan Keberatan') => __(\App\Models\KeberatanInformasi::JENIS[$keberatan->jenis_keberatan] ?? $keberatan->jenis_keberatan),
            __('Kasus Posisi') => $keberatan->kasus_posisi ?: $keberatan->alasan_keberatan,
            __('Diajukan Melalui Kuasa') => $keberatan->dikuasakan ? __('Ya') : __('Tidak'),
            __('Tanggal Tanggapan') => optional($keberatan->tanggal_tanggapan)->translatedFormat('d F Y') ?? '—',
        ];
    @endphp

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Rincian Keberatan'), 1) !!}</h2>
            <span class="inline-flex px-3 py-1 rounded-full border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-200">
                {{ $keberatan->labelStatus() }}
            </span>
        </div>

        <dl class="divide-y divide-gray-100 dark:divide-white/10">
            @foreach ($baris as $label => $nilai)
                <div class="py-3 grid grid-cols-1 sm:grid-cols-3 gap-1 sm:gap-4">
                    <dt class="text-sm text-gray-500 dark:text-gray-400">{{ $label }}</dt>
                    <dd class="sm:col-span-2 text-sm text-gray-900 dark:text-white whitespace-pre-line">{{ $nilai ?: '—' }}</dd>
                </div>
            @endforeach
        </dl>

        @if ($keberatan->tanggapan_atasan_ppid)
            <div class="mt-6 p-4 rounded-xl bg-[#F3ECDD] dark:bg-[#082217] border border-gray-100 dark:border-white/10">
                <p class="text-sm font-bold text-gray-900 dark:text-white mb-1">{{ __('Tanggapan PPID') }}</p>
                <p class="text-sm text-gray-700 dark:text-gray-200 whitespace-pre-line">{{ $keberatan->tanggapan_atasan_ppid }}</p>
            </div>
        @endif

        @if ($keberatan->berkas->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">{{ __('Lampiran Anda') }}</h3>
                <ul class="space-y-2">
                    @foreach ($keberatan->berkas as $berkas)
                        <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 dark:border-white/10 px-4 py-3">
                            <span class="text-sm text-gray-900 dark:text-white truncate">{{ $berkas->nama_file ?: __('Berkas') }}</span>
                            <a href="{{ route('akun.keberatan.berkas', $berkas->id) }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">
                                {{ __('Unduh') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Hak lanjutan pemohon: sengketa ke Komisi Informasi, 14 hari kerja
             setelah tanggapan. Tanpa disebutkan di sini, tenggatnya hanya ada di
             badan surel dan mudah terlewat. --}}
        @if ($keberatan->batas_waktu_sengketa)
            <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-100 text-sm text-amber-900">
                <span class="font-bold">{{ __('Batas pengajuan sengketa') }}:</span>
                {{ $keberatan->batas_waktu_sengketa->translatedFormat('d F Y') }}.
                {{ __('Bila Anda tidak puas atas tanggapan ini, sengketa informasi publik dapat diajukan ke Komisi Informasi sampai tanggal tersebut.') }}
            </div>
        @endif

        <div class="mt-7 flex flex-wrap items-center gap-4">
            <a href="{{ route('akun.keberatan.index') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Kembali ke daftar') }}</a>

            @if ($keberatan->permohonan)
                <a href="{{ route('akun.permohonan.show', $keberatan->permohonan->id) }}" class="text-sm font-semibold text-[#E87317] hover:underline">
                    {{ __('Lihat permohonan asalnya') }}
                </a>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">{!! $judulDua(__('Alur Persetujuan'), 1) !!}</h2>

        @include('akun.partials.alur-persetujuan', [
            'tahap' => $keberatan->tahapAlurPortal(),
            'tanggal' => $keberatan->tanggalAlurPortal(),
        ])
    </div>

@endsection
