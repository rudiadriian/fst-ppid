@extends('layouts.app')

@section('title', __('Pertanyaan Umum') . ' | ' . __('PPID FSTJ'))
@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Bantuan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__('Pertanyaan Umum'), 1, 'fs-title-accent-soft') !!}</h1>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            @forelse ($grup as $kategori => $daftar)
                <h2 class="mb-4 mt-10 text-xl font-bold text-gray-900 first:mt-0 dark:text-white">{!! $judulDua($kategori) !!}</h2>
                <div class="space-y-3">
                    @foreach ($daftar as $item)
                        <div x-data="{ open: false }" class="overflow-hidden rounded-2xl border border-gray-100 bg-white dark:border-white/10 dark:bg-[#0B2A1D]">
                            <button @click="open = !open" class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left">
                                <span class="text-base font-semibold text-gray-900 dark:text-white">{{ $item['pertanyaan'] }}</span>
                                <span class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg transition-colors duration-300" :class="open ? 'fs-gradient text-white' : 'bg-gray-100 text-gray-400'">
                                    <svg class="h-5 w-5 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </span>
                            </button>
                            <div x-show="open" x-collapse style="display:none">
                                <div class="px-6 pb-5 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                    {{ strip_tags($item['jawaban']) }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @empty
                <p class="rounded-2xl border border-gray-100 bg-white p-8 text-center text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">
                    {{ __('Belum ada pertanyaan umum yang dipublikasikan.') }}
                </p>
            @endforelse
        </div>
    </section>

@endsection
