@extends('layouts.app')

@section('title', __($data['title']) . ' | ' . __('Regulasi PPID FSTJ'))
{{-- Penggambar dokumen memakai pdf.js; hanya dimuat di halaman ini. --}}
@push('scripts')
    @vite('resources/js/sampul-pdf.js')
@endpush

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-14 lg:py-16">
            <a href="{{ route('ppid.regulation') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-white/80 hover:text-white transition mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                {{ __('Kembali ke daftar regulasi') }}
            </a>

            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-white/15 text-white">{{ __($data['type']) }}</span>

                @if (!empty($data['published']))
                    <span class="inline-flex items-center gap-1.5 text-sm font-normal text-white/80">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ \App\Support\Cms::tanggalWaktu($data['published']) }}
                    </span>
                @endif
            </div>

            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-snug max-w-5xl">{{ $data['title'] }}</h1>

            <div class="mt-5 flex flex-wrap items-center gap-2 text-sm text-white/80">
                <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="PT Food Station Tjipinang Jaya (Perseroda)"
                     class="w-7 h-7 rounded-full object-contain bg-white p-0.5">
                {{ __('Diunggah oleh') }}
                <span class="font-semibold text-white">{{ $data['pengunggah'] ?: __('Petugas PPID') }}</span>
            </div>
        </div>
    </section>

    {{-- ISI --}}
    <section class="py-14 lg:py-16 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            @if (!empty($data['ringkasan']))
                <div class="mb-8 bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-6 sm:p-7">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Ringkasan') }}</h2>
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ $data['ringkasan'] }}</p>
                </div>
            @endif

            {{-- Dokumen dibaca di halaman ini, bukan di tab baru. Seluruh halaman
                 PDF digambar berurutan lewat pdf.js. --}}
            <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-4 sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
                    <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Dokumen') }}</h2>

                    @if (!empty($data['jenis']))
                        <span class="text-[11px] font-bold uppercase tracking-[0.14em] text-gray-500 dark:text-gray-400">{{ $data['jenis'] }}</span>
                    @endif
                </div>

                @if (!empty($data['link']) && $data['ext'] === 'pdf')
                    <div class="mx-auto w-full max-w-4xl space-y-4"
                         data-pdf-dokumen="{{ $data['link'] }}"
                         aria-label="{{ __('Isi dokumen') }}: {{ $data['title'] }}">
                        <div data-pdf-dokumen-status class="rounded-xl border border-gray-100 dark:border-white/10 bg-[#FAF6EC] dark:bg-[#082217] py-16 text-center">
                            <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Memuat dokumen…') }}</p>
                        </div>
                    </div>
                @elseif (!empty($data['link']) && in_array($data['ext'], ['jpg', 'jpeg', 'png', 'webp'], true))
                    <img src="{{ $data['link'] }}" alt="{{ $data['title'] }}" class="mx-auto w-full max-w-4xl rounded-xl border border-gray-100 dark:border-white/10">
                @else
                    <div class="rounded-xl border border-gray-100 dark:border-white/10 bg-[#FAF6EC] dark:bg-[#082217] py-16 text-center">
                        <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Berkas dokumen belum tersedia.') }}</p>
                    </div>
                @endif
            </div>

            {{-- POSTINGAN RELEVAN --}}
            @if (!empty($relevan))
                <div class="mt-14">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{!! $judulDua(__('Postingan Relevan'), 1) !!}</h2>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach ($relevan as $lain)
                            <article class="group relative flex flex-col bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                                <div class="p-4 border-b border-gray-100 dark:border-white/10 bg-white dark:bg-[#0B2A1D]">
                                    <div class="relative w-full h-48 overflow-hidden rounded-xl border border-gray-100 dark:border-white/10 bg-white"
                                         @if (!empty($lain['link']) && $lain['ext'] === 'pdf') data-pdf-cover="{{ $lain['link'] }}" @endif
                                         role="img"
                                         aria-label="{{ __('Halaman pertama dokumen') }}: {{ $lain['title'] }}">
                                        @if (!empty($lain['link']) && in_array($lain['ext'], ['jpg', 'jpeg', 'png', 'webp'], true))
                                            <img src="{{ $lain['link'] }}" alt="{{ $lain['title'] }}" loading="lazy"
                                                 class="absolute inset-0 w-full h-full object-cover object-top">
                                        @else
                                            @include('partials.regulasi_sampul_cadangan')
                                        @endif
                                    </div>
                                </div>

                                <div class="flex-1 p-5">
                                    <div class="flex flex-wrap items-center gap-2 mb-2">
                                        <span class="px-2.5 py-0.5 inline-flex text-[11px] font-semibold rounded-full
                                            {{ $lain['type'] === 'Dasar Hukum PPID' ? 'bg-blue-50 text-blue-600' :
                                            ($lain['type'] === 'Pedoman' ? 'bg-amber-50 text-amber-600' : 'bg-emerald-50 text-[#10462F]') }}">
                                            {{ __($lain['type']) }}
                                        </span>

                                        @if (!empty($lain['published']))
                                            <span class="text-xs font-normal text-gray-500 dark:text-gray-400">{{ \App\Support\Cms::tanggal($lain['published']) }}</span>
                                        @endif
                                    </div>

                                    <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug">
                                        <a href="{{ $lain['url'] }}" class="after:absolute after:inset-0 hover:text-[#10462F] dark:hover:text-[#3E9C6C] transition-colors">
                                            {{ \Illuminate\Support\Str::limit($lain['title'], 90) }}
                                        </a>
                                    </h3>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>
    </section>
@endsection
