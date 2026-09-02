@extends('layouts.portal')

@section('title', __('Detail Permohonan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Detail Permohonan'))

@section('portal')

    @php
        $baris = [
            __('Nomor Registrasi') => $permohonan->kode_permohonan,
            __('Tanggal Pengajuan') => optional($permohonan->tanggal_permohonan)->translatedFormat('d F Y H:i') ?? '—',
            // Batas Waktu Tanggapan sengaja tidak ditampilkan (langkah 101):
            // itu tenggat kerja petugas, dan di layar pemohon terbaca sebagai
            // janji tanggal yang belum tentu jadi hari jawabannya keluar.
            __('Rincian Informasi') => $permohonan->rincian_informasi,
            __('Tujuan Penggunaan Informasi') => $permohonan->tujuan_penggunaan,
            __('Cara Memperoleh Informasi') => __(\App\Models\PermohonanInformasi::CARA_MEMPEROLEH[$permohonan->cara_memperoleh] ?? '—'),
            __('Salinan Informasi Dibutuhkan') => $permohonan->format_informasi === 'hardcopy' ? __('Salinan Cetak') : __('Salinan Digital'),
            __('Cara Mendapatkan Salinan Informasi') => match ($permohonan->cara_pengiriman) {
                'ambil_langsung' => __('Mengambil Langsung'),
                'email' => __('Salinan Digital (Email)'),
                default => __('Pos'),
            },
            // Tanggal saat statusnya benar-benar berpindah ke Selesai.
            __('Tanggal Tanggapan') => optional($permohonan->tanggalSelesaiPortal())->translatedFormat('d F Y') ?? '—',
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

        {{-- Dokumen jawaban dari petugas.
             Baru tampil setelah permohonannya diserahkan: sebelum itu berkasnya
             masih disiapkan dan belum disetujui PPID. Sebelum langkah 97 berkas
             ini tidak punya tempat sama sekali di portal, padahal loncengnya
             sudah memberitahukannya. --}}
        @if ($permohonan->tanggapanTerbukaUntukPemohon() && $permohonan->tanggapanFiles->isNotEmpty())
            <div class="mt-6">
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-3">{{ __('Berkas Tanggapan') }}</h3>
                <ul class="space-y-2">
                    @foreach ($permohonan->tanggapanFiles as $berkas)
                        <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-100 dark:border-white/10 px-4 py-3">
                            <span class="text-sm text-gray-900 dark:text-white truncate">{{ $berkas->nama_file ?: __('Berkas') }}</span>
                            <a href="{{ route('akun.permohonan.berkas-tanggapan', $berkas->id) }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">
                                {{ __('Unduh') }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

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

    {{-- Alur persetujuan versi pemohon: tiga langkah, selalu tiga.
         Menggantikan Jejak Status yang mencetak tiap perpindahan internal —
         termasuk putaran revisi antara PPID dan PPID Pelaksana, yang bukan
         urusan pemohon dan terbaca sebagai masalah pada berkasnya sendiri
         (langkah 101). --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-5">{!! $judulDua(__('Alur Persetujuan'), 1) !!}</h2>

        @include('akun.partials.alur-persetujuan', [
            'tahap' => $permohonan->tahapAlurPortal(),
            'tanggal' => $permohonan->tanggalAlurPortal(),
        ])
    </div>

@endsection
