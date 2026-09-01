@extends('layouts.portal')

@section('title', __('Histori Permohonan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Histori Permohonan'))

@section('portal')

    {{-- Pencarian berlaku untuk kedua daftar sekaligus: satu nomor permohonan
         memunculkan permohonannya beserta keberatan yang menunjuk ke sana. --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-5 sm:p-6">
        <form method="GET" class="flex flex-wrap items-center gap-2">
            <label for="cari-histori" class="sr-only">{{ __('Cari nomor pengajuan') }}</label>
            <input id="cari-histori" type="search" name="cari" value="{{ $cari }}"
                   placeholder="{{ __('Cari nomor permohonan atau keberatan…') }}"
                   class="flex-1 min-w-[240px] px-4 py-2.5 bg-gray-50 border border-gray-200 dark:border-white/10 dark:bg-[#082217] rounded-xl text-sm outline-none focus:border-[#10462F] focus:ring-2 focus:ring-[#10462F]/15">

            <button type="submit" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5">
                {{ __('Cari') }}
            </button>

            @if ($cari !== '')
                <a href="{{ route('akun.histori') }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Reset') }}</a>
            @endif
        </form>

        @if ($cari !== '')
            <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Hasil pencarian :kata: :permohonan permohonan, :keberatan keberatan.', [
                    'kata' => $cari,
                    'permohonan' => $permohonan->count(),
                    'keberatan' => $keberatan->count(),
                ]) }}
            </p>
        @endif
    </div>

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-6">{!! $judulDua(__('Riwayat Permohonan Informasi'), 1) !!}</h2>

        @if ($permohonan->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $cari !== '' ? __('Tidak ada permohonan yang cocok dengan pencarian.') : __('Belum ada permohonan.') }}
            </p>
        @else
            <div class="space-y-4">
                @foreach ($permohonan as $item)
                    <details class="group rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden" @if ($loop->first) open @endif>
                        <summary class="cursor-pointer list-none px-5 py-4 bg-[#F3ECDD] dark:bg-[#082217] flex flex-wrap items-center justify-between gap-3">
                            <span>
                                <span class="block text-sm font-bold text-gray-900 dark:text-white">{{ $item->kode_permohonan }}</span>
                                <span class="block text-xs text-gray-500 dark:text-gray-400">
                                    {{ optional($item->tanggal_permohonan)->translatedFormat('d F Y') ?? '—' }} ·
                                    {{ \Illuminate\Support\Str::limit($item->rincian_informasi, 60) }}
                                </span>
                            </span>
                            <span class="inline-flex px-3 py-1 rounded-full border border-gray-200 dark:border-white/10 bg-white dark:bg-[#0B2A1D] text-xs font-bold text-gray-700 dark:text-gray-200">
                                {{ $item->labelStatus() }}
                            </span>
                        </summary>

                        <div class="px-5 py-5 space-y-4">
                            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-sm">
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Tujuan Penggunaan Informasi') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ $item->tujuan_penggunaan ?: '—' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-gray-500 dark:text-gray-400">{{ __('Batas Waktu Tanggapan') }}</dt>
                                    <dd class="text-gray-900 dark:text-white">{{ optional($item->batas_waktu_tanggapan)->translatedFormat('d F Y') ?? '—' }}</dd>
                                </div>
                            </dl>

                            @if ($item->logStatus->isEmpty())
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada perubahan status dari petugas.') }}</p>
                            @else
                                <ol class="relative border-l border-gray-200 dark:border-white/10 ml-2 space-y-4">
                                    @foreach ($item->logStatus as $log)
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

                            <div class="flex flex-wrap items-center gap-4 pt-1">
                                <a href="{{ route('akun.permohonan.show', $item->id) }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Detail') }}</a>
                                @if ($item->keberatan->isNotEmpty())
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Keberatan diajukan') }}: {{ $item->keberatan->count() }}</span>
                                @endif
                                @if ($item->survei)
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ __('Survei') }}: {{ $item->survei->rating }}/5</span>
                                @endif
                            </div>
                        </div>
                    </details>
                @endforeach
            </div>
        @endif
    </div>

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">{!! $judulDua(__('Riwayat Keberatan'), 1) !!}</h2>

        @if ($keberatan->isEmpty())
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $cari !== '' ? __('Tidak ada keberatan yang cocok dengan pencarian.') : __('Belum ada keberatan yang diajukan.') }}
            </p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($keberatan as $item)
                    <li class="py-4 flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $item->kode_keberatan ?? '—' }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ __('atas') }} {{ $item->permohonan->kode_permohonan ?? '—' }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                {{ __(\App\Models\KeberatanInformasi::JENIS[$item->jenis_keberatan] ?? $item->jenis_keberatan) }} ·
                                {{ optional($item->tanggal_keberatan)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        <span class="inline-flex px-3 py-1 rounded-full border border-gray-200 dark:border-white/10 text-xs font-bold text-gray-700 dark:text-gray-200">
                            {{ $item->labelStatus() }}
                        </span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

@endsection
