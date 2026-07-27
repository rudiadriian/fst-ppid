@extends('layouts.app')

@section('title', 'Daftar Informasi Dikecualikan | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Informasi Publik') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __($data['title']) }}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">{{ __($data['description']) }}</p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            @forelse ($data['items'] as $item)
                <article class="mb-6 rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-8 dark:border-white/10 dark:bg-[#121a17]">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-4">
                            <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-50 text-sm font-bold text-[#008060] dark:bg-white/5 dark:text-[#00A66C]">{{ $item['no'] }}</span>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900 dark:text-white sm:text-xl">{{ $item['judul'] }}</h2>
                                @if ($item['ringkasan'])
                                    <p class="mt-2 max-w-3xl text-base font-normal leading-relaxed text-gray-600 dark:text-gray-300">{{ $item['ringkasan'] }}</p>
                                @endif
                            </div>
                        </div>

                        @if ($item['file'])
                            <a href="{{ $item['file'] }}" target="_blank" rel="noopener"
                               class="inline-flex items-center gap-1.5 rounded-lg bg-[#008060] px-4 py-2 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#00664e]">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                {{ __('Surat Penetapan') }}
                            </a>
                        @endif
                    </div>

                    <dl class="mt-6 grid gap-4 border-t border-gray-100 pt-6 dark:border-white/10 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Alasan Pengecualian') }}</dt>
                            <dd class="mt-1 text-base font-normal leading-relaxed text-gray-800 dark:text-gray-200">{{ $item['alasan'] }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Dasar Hukum') }}</dt>
                            <dd class="mt-1 text-base font-normal leading-relaxed text-gray-800 dark:text-gray-200">{{ $item['dasar_hukum'] ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Jangka Waktu') }}</dt>
                            <dd class="mt-1 text-base font-normal leading-relaxed text-gray-800 dark:text-gray-200">{{ $item['jangka_waktu'] ?: '-' }}</dd>
                        </div>
                        <div>
                            <dt class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Tanggal Penetapan') }}</dt>
                            <dd class="mt-1 text-base font-normal leading-relaxed text-gray-800 dark:text-gray-200">{{ $item['tanggal'] ?: '-' }}</dd>
                        </div>
                    </dl>
                </article>
            @empty
                <div class="rounded-2xl border border-gray-100 bg-white px-6 py-16 text-center shadow-sm dark:border-white/10 dark:bg-[#121a17]">
                    <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada daftar informasi dikecualikan yang dipublikasikan.') }}</p>
                </div>
            @endforelse

            {{-- Catatan --}}
            <div class="mt-8 flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-6 sm:p-8 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-[#008060] dark:bg-white/10 dark:text-[#00A66C]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </span>
                <div>
                    <p class="mb-1 text-base font-semibold text-gray-900 dark:text-white">{{ __('Catatan Penting') }}</p>
                    <p class="text-base font-normal leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ __('Pengecualian informasi ditetapkan melalui uji konsekuensi sesuai Pasal 17 UU No. 14 Tahun 2008 dan bersifat sementara sesuai jangka waktu yang ditetapkan. Keberatan atas penetapan ini dapat diajukan melalui menu') }}
                        <a href="{{ route('ppid.objection') }}" class="font-semibold text-[#008060] hover:text-[#00664e] dark:text-[#00A66C]">{{ __('Pengajuan Keberatan') }}</a>.
                    </p>
                </div>
            </div>

        </div>
    </section>
@endsection
