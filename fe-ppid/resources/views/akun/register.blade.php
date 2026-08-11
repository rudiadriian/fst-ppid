@extends('layouts.app')

@section('title', 'Daftar Akun Pengunjung | PPID FSTJ')

@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-14 lg:py-16 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-3">{{ __('Akun Pengunjung') }}</p>
            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-tight">{!! $judulDua(__('Daftar Akun Pengunjung'), 2, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-3 text-base text-white/80 max-w-xl mx-auto leading-relaxed">
                {{ __('Satu akun untuk mengajukan permohonan, mengajukan keberatan, melacak status, dan mengisi survei kepuasan.') }}
            </p>
        </div>
    </section>

    <section class="py-14 lg:py-20 bg-[#F3ECDD] dark:bg-[#082217]">
        <div class="max-w-lg mx-auto px-6">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                @include('akun.partials.status')
                @php
    /* Gaya isian formulir akun — disamakan dengan formulir Permohonan/Keberatan. */
    $inputClass = 'mt-1.5 block w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#0B2A1D] focus:border-[#10462F] focus:ring-2 focus:ring-[#10462F]/15 outline-none transition-all text-base';
    $labelClass = 'block text-sm font-medium text-gray-700 dark:text-gray-300';
    $btnClass = 'inline-flex items-center justify-center py-3.5 px-8 fs-gradient-accent text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300';
@endphp

                <form method="POST" action="{{ route('akun.register.store') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="nama" class="{{ $labelClass }}">{{ __('Nama Lengkap') }}</label>
                        <input id="nama" name="nama" type="text" value="{{ old('nama') }}" required autofocus class="{{ $inputClass }}">
                        @error('nama')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="{{ $labelClass }}">{{ __('Email') }}</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="{{ $inputClass }}">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Dipakai untuk masuk dan menerima pemberitahuan status permohonan.') }}</p>
                        @error('email')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="no_hp" class="{{ $labelClass }}">{{ __('Nomor Telepon/HP') }}</label>
                        <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp') }}" class="{{ $inputClass }}">
                        @error('no_hp')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password" class="{{ $labelClass }}">{{ __('Password') }}</label>
                        <input id="password" name="password" type="password" required autocomplete="new-password" class="{{ $inputClass }}">
                        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ __('Minimal 8 karakter, memuat huruf dan angka.') }}</p>
                        @error('password')<p class="mt-1.5 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="{{ $labelClass }}">{{ __('Ulangi Password') }}</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="{{ $inputClass }}">
                    </div>

                    <label class="flex items-start gap-3 text-sm text-gray-600 dark:text-gray-300">
                        <input type="checkbox" name="setuju" value="1" @checked(old('setuju')) required
                               class="mt-0.5 rounded border-gray-300 text-[#10462F] focus:ring-[#10462F]">
                        <span>{{ __('Saya menyatakan data yang saya isi benar dan bersedia data tersebut digunakan untuk pelayanan informasi publik.') }}</span>
                    </label>
                    @error('setuju')<p class="text-sm text-red-600 dark:text-red-400">{{ $message }}</p>@enderror

                    <button type="submit" class="{{ $btnClass }} w-full">{{ __('Daftar') }}</button>
                </form>

                <p class="mt-6 pt-6 border-t border-gray-100 dark:border-white/10 text-sm text-gray-600 dark:text-gray-300">
                    {{ __('Sudah punya akun?') }}
                    <a href="{{ route('akun.login') }}" class="font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Masuk di sini') }}</a>
                </p>
            </div>
        </div>
    </section>

@endsection
