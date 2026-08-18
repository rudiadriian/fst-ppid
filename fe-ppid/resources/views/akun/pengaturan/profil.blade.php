@extends('layouts.portal')

@section('title', __('Profil') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Profil'))

@section('portal')

    @php
        /*
         * Seluruh isi halaman ini hanya dibaca — satu-satunya yang boleh diubah
         * pemohon sendiri adalah foto avatar. Data lain ikut dipakai sebagai
         * identitas pada permohonan yang sudah diverifikasi, jadi perubahannya
         * harus lewat petugas PPID (lihat PengaturanController@perbaruiProfil).
         */
        $tab = [
            'akun' => __('Akun'),
            'data-diri' => __('Data Diri'),
            'verifikasi' => __('Verifikasi & Berkas'),
            'aktivitas' => __('Aktivitas'),
        ];

        $tabOff = 'px-4 py-2.5 rounded-xl text-sm font-semibold text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] transition-colors dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
        $tabOn = 'px-4 py-2.5 rounded-xl text-sm font-bold fs-gradient-accent text-white shadow-lg shadow-emerald-900/20';

        $jenisPemohon = \App\Models\Pemohon::JENIS[$pemohon->jenis_pemohon] ?? null;
    @endphp

    {{-- Baris keterangan; dipakai berulang di seluruh tab. --}}
    @php
        $baris = function (string $label, $nilai, ?string $catatan = null) {
            return view('akun.partials.baris-info', [
                'label' => $label,
                'nilai' => $nilai,
                'catatan' => $catatan,
            ]);
        };
    @endphp

    {{-- Foto avatar: satu-satunya formulir di halaman ini. --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data"
              class="flex flex-wrap items-center gap-5">
            @csrf
            @method('PUT')

            @include('akun.partials.avatar', [
                'pemohon' => $pemohon,
                'ukuran' => 'w-20 h-20',
                'teks' => 'text-2xl',
                'cincin' => 'border border-gray-200 dark:border-white/10',
            ])

            <div class="flex-1 min-w-[240px]">
                <label for="foto" class="{{ $fsLabel }}">{{ __('Foto Avatar') }}</label>
                <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png" required class="{{ $fsInput }}">
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('JPG/PNG, maksimal 2 MB. Hanya foto ini yang dapat Anda ubah sendiri.') }}</p>
            </div>

            <button type="submit" class="{{ $fsBtn }}">{{ __('Simpan Foto') }}</button>
        </form>
    </div>

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden"
         x-data="{ aktif: 'akun' }">

        <div class="px-5 pt-5 flex flex-wrap gap-1.5 border-b border-gray-100 dark:border-white/10 pb-4" role="tablist">
            @foreach ($tab as $kunci => $judul)
                <button type="button" role="tab" @click="aktif = '{{ $kunci }}'"
                        :aria-selected="aktif === '{{ $kunci }}' ? 'true' : 'false'"
                        :class="aktif === '{{ $kunci }}' ? '{{ $tabOn }}' : '{{ $tabOff }}'">
                    {{ $judul }}
                </button>
            @endforeach
        </div>

        {{-- AKUN --}}
        <div x-show="aktif === 'akun'" class="p-6 sm:p-8">
            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                {{ $baris(__('Nama Lengkap'), $pemohon->nama) }}
                {{ $baris(__('Email'), $pemohon->email,
                    $pemohon->hasVerifiedEmail() ? __('Email sudah terverifikasi.') : __('Email belum terverifikasi.')) }}
                {{ $baris(__('Nomor Telepon'), $pemohon->no_hp) }}
                {{ $baris(__('Terdaftar Sejak'), optional($pemohon->created_at)->translatedFormat('d F Y')) }}
            </dl>

            <p class="mt-6 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-white/10 pt-4">
                {{ __('Nama, email, dan nomor telepon melekat pada permohonan yang sudah Anda ajukan, jadi hanya petugas PPID yang dapat mengubahnya.') }}
            </p>
        </div>

        {{-- DATA DIRI --}}
        <div x-show="aktif === 'data-diri'" x-cloak class="p-6 sm:p-8">
            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                {{ $baris(__('Jenis Pemohon'), $jenisPemohon ? __($jenisPemohon) : null) }}
                {{ $baris(__('NIK / Nomor KTP'), $pemohon->nik) }}
                {{ $baris(__('Pekerjaan'), $pemohon->pekerjaan) }}
                {{ $baris(__('Nama Lembaga / Organisasi / Kelompok'), $pemohon->nama_lembaga) }}
                {{ $baris(__('Alamat'), $pemohon->alamat) }}
            </dl>

            @unless ($pemohon->dataTerverifikasi())
                <a href="{{ route('akun.data-pemohon') }}" class="mt-6 inline-flex text-sm font-semibold text-[#E87317] hover:underline">
                    {{ __('Lengkapi di Data Pemohon & Berkas') }}
                </a>
            @endunless
        </div>

        {{-- VERIFIKASI & BERKAS --}}
        <div x-show="aktif === 'verifikasi'" x-cloak class="p-6 sm:p-8">
            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                {{ $baris(__('Status Verifikasi'), $pemohon->labelStatusVerifikasi()) }}
                {{ $baris(__('Tanggal Diperiksa'), optional($pemohon->tanggal_verifikasi)->translatedFormat('d F Y')) }}
                {{ $baris(__('Catatan Petugas'), $pemohon->catatan_verifikasi) }}
                @unless ($pemohon->dataTerverifikasi())
                    {{ $baris(__('Sisa Kesempatan Kirim Ulang'), $pemohon->sisaKesempatanVerifikasi().' / '.\App\Models\Pemohon::BATAS_DITOLAK) }}
                @endunless
            </dl>

            <div class="mt-6 border-t border-gray-100 dark:border-white/10 pt-4">
                <p class="text-sm font-semibold text-gray-900 dark:text-white mb-2">{{ __('Berkas KTP') }}</p>
                @if ($pemohon->file_ktp)
                    <a href="{{ route('akun.data-pemohon.ktp') }}" target="_blank" rel="noopener"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-[#E87317] hover:underline">
                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        {{ __('Lihat berkas KTP') }}
                    </a>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Belum ada berkas KTP yang diunggah.') }}</p>
                @endif
            </div>
        </div>

        {{-- AKTIVITAS --}}
        <div x-show="aktif === 'aktivitas'" x-cloak class="p-6 sm:p-8">
            <dl class="divide-y divide-gray-100 dark:divide-white/10">
                {{ $baris(__('Permohonan Informasi Diajukan'), $jumlahPermohonan) }}
                {{ $baris(__('Keberatan Informasi Diajukan'), $jumlahKeberatan) }}
            </dl>

            <a href="{{ route('akun.histori') }}" class="mt-6 inline-flex text-sm font-semibold text-[#E87317] hover:underline">
                {{ __('Lihat Histori Permohonan') }}
            </a>
        </div>
    </div>

@endsection
