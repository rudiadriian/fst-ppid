@extends('layouts.app')

@section('title', 'Struktur PPID | PPID FSTJ')

@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Profil') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__('Struktur PPID'), 1, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            @include('partials.bagan_struktur', ['judulBagan' => __('Bagan Struktur Organisasi PPID Food Station')])

            @if (count($anggota) > 0 && !empty($bagan))
                <h2 class="mt-12 mb-6 text-xl font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Susunan Pejabat PPID'), 1) !!}</h2>
            @endif

            @if (count($anggota) === 0)
                <p class="rounded-2xl border border-gray-100 bg-white p-8 text-center text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">
                    {{ __('Data struktur organisasi belum tersedia.') }}
                </p>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach ($anggota as $orang)
                        <div class="rounded-2xl border border-gray-100 bg-white p-6 text-center dark:border-white/10 dark:bg-[#0B2A1D]">
                            @if ($orang['foto'])
                                <img src="{{ $orang['foto'] }}" alt="{{ $orang['nama'] }}" class="mx-auto mb-4 h-28 w-28 rounded-full object-cover">
                            @else
                                <div class="mx-auto mb-4 flex h-28 w-28 items-center justify-center rounded-full bg-emerald-50 text-2xl font-bold text-[#10462F] dark:bg-white/5">
                                    {{ mb_substr($orang['nama'], 0, 1) }}
                                </div>
                            @endif
                            <h2 class="text-base font-bold text-gray-900 dark:text-white">{{ $orang['nama'] }}</h2>
                            <p class="mt-1 text-sm font-medium text-[#10462F]">{{ $orang['jabatan'] }}</p>
                            @if ($orang['deskripsi'])
                                <p class="mt-3 text-sm leading-relaxed text-gray-500 dark:text-gray-400">{{ $orang['deskripsi'] }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

@endsection
