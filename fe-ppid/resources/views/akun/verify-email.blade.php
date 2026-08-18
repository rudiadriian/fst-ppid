@extends('layouts.app')

@section('title', __('Verifikasi Email') . ' | ' . __('PPID FSTJ'))
@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-14 lg:py-16 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-3">{{ __('Akun Pengunjung') }}</p>
            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-tight">{!! $judulDua(__('Verifikasi Email Anda'), 2, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    <section class="py-14 lg:py-20 bg-[#F3ECDD] dark:bg-[#082217]">
        <div class="max-w-md mx-auto px-6">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                @include('akun.partials.status')

                <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                    {{ __('Kami sudah mengirim tautan verifikasi ke') }}
                    <span class="font-semibold text-gray-900 dark:text-white">{{ $email }}</span>.
                    {{ __('Email wajib diverifikasi sebelum akun bisa dipakai masuk.') }}
                    {{ __('Tautannya berlaku :jam jam; setelah itu mintalah tautan baru di bawah ini.', ['jam' => (int) (config('auth.verification.expire', 1440) / 60)]) }}
                </p>

                @error('email')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror

                <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                    {{ __('Satu tautan dapat dikirim setiap :menit menit. Periksa folder Spam bila belum tampak di kotak masuk.', ['menit' => (int) config('ppid.akun.jeda_kirim_tautan_menit', 30)]) }}
                </p>

                <div class="mt-6 flex flex-wrap items-center gap-3">
                    <form method="POST" action="{{ route('akun.verifikasi.send') }}">
                        @csrf
                        <button type="submit" class="{{ $fsBtn }}">{{ __('Kirim Ulang Tautan') }}</button>
                    </form>

                    <a href="{{ route('akun.login') }}" class="text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">
                        {{ __('Kembali ke halaman masuk') }}
                    </a>
                </div>
            </div>
        </div>
    </section>

@endsection
