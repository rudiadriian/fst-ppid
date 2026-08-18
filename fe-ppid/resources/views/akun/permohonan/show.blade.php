@extends('layouts.portal')

@section('title', __('Detail Permohonan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Detail Permohonan'))

@section('portal')

    @php
        $baris = [
            __('Nomor Registrasi') => $permohonan->kode_permohonan,
            __('Tanggal Pengajuan') => optional($permohonan->tanggal_permohonan)->translatedFormat('d F Y H:i') ?? '—',
            __('Batas Waktu Tanggapan') => optional($permohonan->batas_waktu_tanggapan)->translatedFormat('d F Y') ?? '—',
            __('Rincian Informasi') => $permohonan->rincian_informasi,
            __('Tujuan Penggunaan Informasi') => $permohonan->tujuan_penggunaan,
            __('Cara Memperoleh Informasi') => __(\App\Models\PermohonanInformasi::CARA_MEMPEROLEH[$permohonan->cara_memperoleh] ?? '—'),
            __('Salinan Informasi Dibutuhkan') => $permohonan->format_informasi === 'hardcopy' ? __('Salinan Cetak') : __('Salinan Digital'),
            __('Cara Mendapatkan Salinan Informasi') => match ($permohonan->cara_pengiriman) {
                'ambil_langsung' => __('Mengambil Langsung'),
                'email' => __('Salinan Digital (Email)'),
                default => __('Pos'),
            },
            __('Tanggal Tanggapan') => optional($permohonan->tanggal_tanggapan)->translatedFormat('d F Y') ?? '—',
        ];
    @endphp

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Rincian Pengajuan'), 1) !!}</h2>
            <span class="inline-flex px-3 py-1 rounded-full border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-200">
                {{ $permohonan->labelStatus() }}
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

        @if ($permohonan->alasan_penolakan)
            <div class="mt-5 p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700">
                <span class="font-bold">{{ __('Alasan Penolakan') }}:</span> {{ $permohonan->alasan_penolakan }}
            </div>
        @endif

        <div class="mt-7 flex flex-wrap items-center gap-4">
            <a href="{{ route('akun.permohonan.index') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Kembali ke daftar') }}</a>

            @if ($permohonan->bolehDisurvei() && !$permohonan->survei)
                <a href="{{ route('akun.survei.create', $permohonan->id) }}" class="{{ $fsBtn }} py-2.5 px-5 text-sm">{{ __('Isi Survei') }}</a>
            @elseif ($permohonan->survei)
                <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Sudah dinilai') }} — {{ $permohonan->survei->rating }}/5</span>
            @endif

            @if (in_array($permohonan->status, ['ditolak', 'ditolak_sebagian'], true))
                <a href="{{ route('akun.keberatan.create') }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Ajukan Keberatan') }}</a>
            @endif
        </div>
    </div>

    {{-- Jejak status dari petugas --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">{!! $judulDua(__('Jejak Status'), 1) !!}</h2>

        @if ($permohonan->logStatus->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada perubahan status dari petugas.') }}</p>
        @else
            <ol class="relative border-l border-gray-200 dark:border-white/10 ml-2 space-y-5">
                @foreach ($permohonan->logStatus as $log)
                    <li class="ml-5">
                        <span class="absolute -left-1.5 w-3 h-3 rounded-full bg-[#E87317]"></span>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            {{ __(\App\Models\PermohonanInformasi::STATUS_LABEL[$log->status_baru] ?? $log->status_baru) }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ optional($log->created_at)->translatedFormat('d F Y H:i') }}</p>
                        @if ($log->catatan)
                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-300">{{ $log->catatan }}</p>
                        @endif
                    </li>
                @endforeach
            </ol>
        @endif
    </div>

@endsection
