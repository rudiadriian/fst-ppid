@extends('layouts.portal')

@section('title', __('Data Pemohon & Berkas') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Data Pemohon & Berkas'))

@section('portal')

    @php
        $warnaStatus = match ($pemohon->status_verifikasi) {
            'terverifikasi' => 'bg-emerald-50 border-emerald-100 text-[#10462F]',
            'menunggu' => 'bg-blue-50 border-blue-100 text-blue-800',
            'ditolak' => 'bg-red-50 border-red-100 text-red-700',
            default => 'bg-amber-50 border-amber-100 text-amber-900',
        };

        /*
         * Data yang sudah disetujui petugas dikunci: permohonan yang berjalan
         * memakai identitas itu sebagai dasar verifikasinya. Halaman berubah
         * jadi tampilan baca saja, dan berkas KTP hanya bisa dilihat.
         * Penguncian sebenarnya tetap di server (PengaturanController).
         */
        $terkunci = $pemohon->dataTerverifikasi();
        $kelasBaca = $fsInput.' opacity-70 cursor-not-allowed';
    @endphp

    <div class="p-5 rounded-2xl border {{ $warnaStatus }}">
        <p class="text-sm font-bold">{{ __('Status Verifikasi') }}: {{ $pemohon->labelStatusVerifikasi() }}</p>
        <p class="text-sm mt-1">
            @if ($pemohon->dataTerverifikasi())
                {{ __('Data Anda sudah diverifikasi petugas pada') }}
                {{ optional($pemohon->tanggal_verifikasi)->translatedFormat('d F Y') ?? '—' }}.
                {{ __('Data yang sudah terverifikasi tidak dapat diubah sendiri. Hubungi petugas PPID bila ada yang perlu diperbaiki.') }}
            @elseif ($pemohon->verifikasiMenunggu())
                {{ __('Berkas Anda sedang diperiksa petugas PPID.') }}
                {{ __('Pemeriksaan berkas memerlukan waktu paling lama :hari hari kerja sejak berkas lengkap diterima.', ['hari' => (int) config('ppid.akun.sla_verifikasi_hari_kerja', 14)]) }}
            @elseif ($pemohon->verifikasiDiblokir())
                {{ __('Data diri Anda sudah ditolak :batas kali, sehingga pengiriman ulang ditutup. Hubungi petugas PPID untuk melanjutkan.', ['batas' => \App\Models\Pemohon::BATAS_DITOLAK]) }}
            @elseif ($pemohon->status_verifikasi === 'ditolak')
                {{ __('Perbaiki isian dan berkas KTP Anda, lalu kirim ulang untuk diperiksa.') }}
                {{ __('Sisa kesempatan kirim ulang: :sisa dari :batas.', ['sisa' => $pemohon->sisaKesempatanVerifikasi(), 'batas' => \App\Models\Pemohon::BATAS_DITOLAK]) }}
            @else
                {{ __('Semua isian wajib diisi. Permohonan Informasi baru bisa diajukan setelah data diverifikasi petugas.') }}
            @endif
        </p>

        @if (filled($pemohon->catatan_verifikasi))
            <p class="mt-3 p-3 rounded-xl bg-white/70 dark:bg-white/5 text-sm">
                <span class="font-semibold block">{{ __('Catatan petugas') }}</span>
                {{ $pemohon->catatan_verifikasi }}
            </p>
        @endif
    </div>

    @if ($terkunci)

        {{-- Tampilan baca: isian tidak dirender sebagai input sama sekali,
             supaya tidak ada kesan masih bisa disunting. --}}
        <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
            <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Data Pemohon') }}</h2>

            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                @include('akun.partials.baris-info', ['label' => __('Jenis Pemohon'), 'nilai' => __(\App\Models\Pemohon::JENIS[$pemohon->jenis_pemohon] ?? '—'), 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('Nama Lembaga / Organisasi / Kelompok'), 'nilai' => $pemohon->nama_lembaga, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('Nama Pemohon'), 'nilai' => $pemohon->nama, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('Email'), 'nilai' => $pemohon->email, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('No. Telepon'), 'nilai' => $pemohon->no_hp, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('NIK / Nomor KTP'), 'nilai' => $pemohon->nik, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('Pekerjaan'), 'nilai' => $pemohon->pekerjaan, 'catatan' => null])
                @include('akun.partials.baris-info', ['label' => __('Alamat'), 'nilai' => $pemohon->alamat, 'catatan' => null])
            </dl>

            <div class="mt-6 border-t border-gray-100 dark:border-white/10 pt-4">
                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Berkas KTP') }}</p>
                @if ($pemohon->file_ktp)
                    {{-- Hanya dapat dilihat; unggah ulang ditutup bersama isian lain. --}}
                    <a href="{{ route('akun.data-pemohon.ktp') }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-[#E87317] hover:underline">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        {{ __('Lihat berkas KTP') }}
                    </a>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada berkas KTP yang diunggah.') }}</p>
                @endif
            </div>

            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-white/10 pt-4">
                {{ __('Data Pemohon Anda sudah terverifikasi sehingga tidak dapat diubah sendiri. Hubungi petugas PPID bila ada data yang perlu diperbaiki.') }}
            </p>
        </div>

    @else

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <form method="POST" action="{{ route('akun.data-pemohon.update') }}" enctype="multipart/form-data" class="space-y-6"
              x-data="{ jenis: '{{ old('jenis_pemohon', in_array($pemohon->jenis_pemohon, array_keys(\App\Models\Pemohon::JENIS), true) ? $pemohon->jenis_pemohon : 'perorangan') }}' }">
            @csrf
            @method('PUT')

            <div>
                <label for="jenis_pemohon" class="{{ $fsLabel }}">{{ __('Jenis Pemohon') }} <span class="text-red-600">*</span></label>
                <select id="jenis_pemohon" name="jenis_pemohon" required x-model="jenis" class="{{ $fsInput }}">
                    @foreach (\App\Models\Pemohon::JENIS as $nilai => $label)
                        <option value="{{ $nilai }}">{{ __($label) }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="jenis !== 'perorangan'" x-cloak>
                <label for="nama_lembaga" class="{{ $fsLabel }}">{{ __('Nama Lembaga / Organisasi / Kelompok') }} <span class="text-red-600">*</span></label>
                <input id="nama_lembaga" name="nama_lembaga" type="text" value="{{ old('nama_lembaga', $pemohon->nama_lembaga) }}"
                       :required="jenis !== 'perorangan'" class="{{ $fsInput }}">
            </div>

            {{-- Nama, email, dan telepon mengikuti akun; tidak diketik ulang. --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                <div>
                    <label class="{{ $fsLabel }}">{{ __('Nama Pemohon') }}</label>
                    <input type="text" value="{{ $pemohon->nama }}" disabled class="{{ $fsInput }} opacity-70 cursor-not-allowed">
                </div>
                <div>
                    <label class="{{ $fsLabel }}">{{ __('Email') }}</label>
                    <input type="email" value="{{ $pemohon->email }}" disabled class="{{ $fsInput }} opacity-70 cursor-not-allowed">
                </div>
                <div>
                    <label class="{{ $fsLabel }}">{{ __('No. Telepon') }}</label>
                    <input type="tel" value="{{ $pemohon->no_hp }}" disabled class="{{ $fsInput }} opacity-70 cursor-not-allowed">
                </div>
            </div>
            <p class="-mt-3 text-xs text-gray-500 dark:text-gray-400">
                {{ __('Tiga isian di atas mengikuti akun Anda.') }}
                <a href="{{ route('akun.profil') }}" class="font-semibold text-[#E87317] hover:underline">{{ __('Ubah di Profil') }}</a>.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="nik" class="{{ $fsLabel }}">{{ __('NIK / Nomor KTP') }} <span class="text-red-600">*</span></label>
                    <input id="nik" name="nik" type="text" inputmode="numeric" value="{{ old('nik', $pemohon->nik) }}" required class="{{ $fsInput }}">
                </div>
                <div>
                    <label for="pekerjaan" class="{{ $fsLabel }}">{{ __('Pekerjaan') }} <span class="text-red-600">*</span></label>
                    <input id="pekerjaan" name="pekerjaan" type="text" value="{{ old('pekerjaan', $pemohon->pekerjaan) }}" required class="{{ $fsInput }}">
                </div>
            </div>

            <div>
                <label for="file_ktp" class="{{ $fsLabel }}">
                    {{ __('Upload File KTP Pribadi') }}
                    @unless ($pemohon->file_ktp)<span class="text-red-600">*</span>@endunless
                </label>
                <input id="file_ktp" name="file_ktp" type="file" accept=".pdf,.jpg,.jpeg,.png"
                       @if (!$pemohon->file_ktp) required @endif class="{{ $fsInput }}">
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('PDF/JPG/PNG, maksimal 10 MB.') }}
                    @if ($pemohon->file_ktp)
                        <a href="{{ route('akun.data-pemohon.ktp') }}" target="_blank" rel="noopener" class="font-semibold text-[#E87317] hover:underline">{{ __('Lihat berkas yang tersimpan') }}</a>.
                        {{ __('Kosongkan kalau tidak ingin mengganti.') }}
                    @endif
                </p>
            </div>

            <div>
                <label for="alamat" class="{{ $fsLabel }}">{{ __('Alamat') }} <span class="text-red-600">*</span></label>
                <textarea id="alamat" name="alamat" rows="3" required maxlength="500" class="{{ $fsInput }}">{{ old('alamat', $pemohon->alamat) }}</textarea>
            </div>

            {{-- Tombol dimatikan, bukan disembunyikan, supaya alasannya terbaca.
                 Pembatasan sebenarnya tetap di server (PengaturanController). --}}
            <button type="submit" class="{{ $fsBtn }}" @disabled($pemohon->verifikasiDiblokir())>
                {{ __('Kirim untuk Verifikasi') }}
            </button>

            @if ($pemohon->verifikasiDiblokir())
                <p class="text-sm text-red-600 dark:text-red-400">
                    {{ __('Pengiriman ulang ditutup setelah :batas kali penolakan.', ['batas' => \App\Models\Pemohon::BATAS_DITOLAK]) }}
                </p>
            @endif
        </form>
    </div>

    @endif

@endsection
