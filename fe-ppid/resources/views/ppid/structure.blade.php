@extends('layouts.app')

@section('title', __('Struktur Organisasi') . ' | ' . __('PPID FSTJ'))
@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Profil') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__('Struktur Organisasi'), 1, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            {{-- Halaman ini hanya menampilkan bagannya; daftar "Susunan Pejabat"
                 dihapus atas permintaan. --}}
            @include('partials.bagan_struktur', ['judulBagan' => __('Bagan Struktur Organisasi PPID Food Station')])

            @if (empty($bagan))
                <p class="rounded-2xl border border-gray-100 bg-white p-8 text-center text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">
                    {{ __('Data struktur organisasi belum tersedia.') }}
                </p>
            @endif
        </div>
    </section>

@endsection
