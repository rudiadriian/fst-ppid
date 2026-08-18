@extends('layouts.portal')

@section('title', __('Ubah Password') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Ubah Password'))

@section('portal')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-8">
        <form method="POST" action="{{ route('akun.password.change') }}" class="space-y-6 max-w-lg">
            @csrf
            @method('PUT')

            <div>
                <label for="password_lama" class="{{ $fsLabel }}">{{ __('Password Lama') }} <span class="text-red-600">*</span></label>
                <input id="password_lama" name="password_lama" type="password" required autocomplete="current-password" class="{{ $fsInput }}">
            </div>

            <div>
                <label for="password" class="{{ $fsLabel }}">{{ __('Password Baru') }} <span class="text-red-600">*</span></label>
                <input id="password" name="password" type="password" required autocomplete="new-password" class="{{ $fsInput }}">
                <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Minimal 8 karakter, memuat huruf dan angka.') }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="{{ $fsLabel }}">{{ __('Konfirmasi Password Baru') }} <span class="text-red-600">*</span></label>
                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $fsInput }}">
            </div>

            <button type="submit" class="{{ $fsBtn }}">{{ __('Simpan Password') }}</button>
        </form>
    </div>

@endsection
