@extends('layouts.app')

@section('title', __('Lupa Password') . ' | ' . __('PPID FSTJ'))
@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-14 lg:py-16 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-3">{{ __('Akun Pengunjung') }}</p>
            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-tight">{!! $judulDua(__('Atur Ulang Password'), 2, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    <section class="py-14 lg:py-20 bg-[#F3ECDD] dark:bg-[#082217]">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                @include('akun.partials.status')
                @php
    /* Gaya isian formulir akun — disamakan dengan formulir Permohonan/Keberatan. */
    $inputClass = 'mt-1.5 block w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#0B2A1D] focus:border-[#10462F] focus:ring-2 focus:ring-[#10462F]/15 outline-none transition-all text-base';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $btnClass = 'inline-flex items-center justify-center py-3.5 px-8 fs-gradient-accent text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300';
@endphp

                <p class="text-sm text-gray-600 dark:text-gray-300 mb-5 leading-relaxed">
                    {{ __('Masukkan email akun Anda. Kami kirimkan tautan untuk membuat password baru.') }}
                </p>

                <form method="POST" action="{{ route('akun.password.email') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="email" class="{{ $labelClass }}">{{ __('Email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="{{ $inputClass }}">
                        @error('email')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    @include('akun.partials.captcha', ['idForm' => 'lupa-password'])

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ __('Satu tautan dapat dikirim setiap :menit menit. Periksa folder Spam bila belum tampak di kotak masuk.', ['menit' => (int) config('ppid.akun.jeda_kirim_tautan_menit', 30)]) }}
                    </p>

                    <button type="submit" class="{{ $btnClass }} w-full">{{ __('Kirim Tautan') }}</button>
                </form>

                <p class="mt-6 pt-6 border-t border-gray-100 dark:border-white/10 text-sm">
                    <a href="{{ route('akun.login') }}" class="font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Kembali ke halaman masuk') }}</a>
                </p>
            </div>
        </div>
    </section>

@endsection
