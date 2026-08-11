@extends('layouts.app')

@section('title', 'Berita & Publikasi | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Kabar Terbaru') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__('Berita & Publikasi'), 2, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">
                {{ __('Kabar kegiatan, capaian, dan publikasi resmi PT Food Station Tjipinang Jaya (Perseroda).') }}
            </p>
        </div>
    </section>

    {{-- DAFTAR --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            @if (count($items) === 0)
                <p class="rounded-2xl border border-gray-100 bg-white p-8 text-center text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">
                    {{ __('Belum ada berita yang diterbitkan.') }}
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    @foreach ($items as $item)
                        <a href="{{ $item['url'] }}" class="group h-full flex flex-col bg-white dark:bg-[#0B2A1D] rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-1 transition-all duration-300">
                            <div class="relative h-48 flex-shrink-0 overflow-hidden">
                                <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                <span class="absolute top-4 left-4 fs-gradient text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">{{ $item['category'] }}</span>
                            </div>
                            <div class="p-6 flex flex-col flex-1">
                                <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">{{ $item['date'] }}</p>
                                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#10462F] transition-colors">{{ $item['title'] }}</h2>
                                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed line-clamp-3 mb-4">{{ $item['excerpt'] }}</p>
                                <span class="mt-auto inline-flex items-center gap-1.5 text-[#10462F] font-semibold text-sm">
                                    {{ __('Baca Selengkapnya') }}
                                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </span>
                            </div>
                        </a>
                    @endforeach
                </div>

                @if ($paginator)
                    <div class="mt-10">{{ $paginator->links() }}</div>
                @endif
            @endif
        </div>
    </section>

@endsection
