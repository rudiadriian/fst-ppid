@extends('layouts.portal')

@section('title', __('Profil') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Profil'))

@section('portal')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <form method="POST" action="{{ route('akun.profil.update') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="flex items-center gap-5">
                @if ($pemohon->foto)
                    <img src="{{ route('media.show', ['path' => $pemohon->foto]) }}" alt="{{ __('Avatar') }}"
                         class="w-20 h-20 rounded-full object-cover border border-gray-200 dark:border-white/10">
                @else
                    <span class="w-20 h-20 rounded-full fs-gradient-accent text-white text-2xl font-bold flex items-center justify-center">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($pemohon->nama, 0, 1)) }}
                    </span>
                @endif
                <div class="flex-1 min-w-0">
                    <label for="foto" class="{{ $fsLabel }}">{{ __('Avatar') }}</label>
                    <input id="foto" name="foto" type="file" accept=".jpg,.jpeg,.png" class="{{ $fsInput }}">
                    <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('JPG/PNG, maksimal 2 MB.') }}</p>
                </div>
            </div>

            <div>
                <label for="nama" class="{{ $fsLabel }}">{{ __('Username') }} <span class="text-red-600">*</span></label>
                <input id="nama" name="nama" type="text" value="{{ old('nama', $pemohon->nama) }}" required class="{{ $fsInput }}">
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Nama ini yang tampil pada permohonan Anda.') }}</p>
            </div>

            <div>
                <label for="email" class="{{ $fsLabel }}">{{ __('Email') }}</label>
                <input id="email" type="email" value="{{ $pemohon->email }}" disabled class="{{ $fsInput }} opacity-70 cursor-not-allowed">
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Perubahan email harus lewat petugas PPID.') }}
                    @if ($pemohon->hasVerifiedEmail())
                        <span class="font-semibold text-[#10462F] dark:text-[#3E9C6C]">{{ __('Terverifikasi') }}</span>
                    @endif
                </p>
            </div>

            <div>
                <label for="no_hp" class="{{ $fsLabel }}">{{ __('Nomor Telepon') }} <span class="text-red-600">*</span></label>
                <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $pemohon->no_hp) }}" required class="{{ $fsInput }}">
            </div>

            <button type="submit" class="{{ $fsBtn }}">{{ __('Simpan Perubahan') }}</button>
        </form>
    </div>

@endsection
