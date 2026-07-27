@extends('layouts.app')

@section('title', 'Hasil Pencarian | PPID FSTJ')

@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Pencarian') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{{ __('Hasil Pencarian') }}</h1>
            @if ($query !== '')
                <p class="mt-4 text-lg font-normal text-white/80">{{ __('Kata kunci') }}: <span class="font-semibold">{{ $query }}</span></p>
            @endif

            <form action="{{ route('search') }}" method="GET" class="mx-auto mt-8 flex max-w-xl gap-2">
                <label for="query" class="sr-only">{{ __('Kata kunci pencarian') }}</label>
                <input id="query" name="query" type="search" value="{{ $query }}" minlength="3" required
                       placeholder="{{ __('Cari regulasi, laporan, atau informasi...') }}"
                       class="w-full rounded-xl border-0 px-4 py-3 text-base text-gray-800 placeholder-gray-400 focus:ring-2 focus:ring-white">
                <button type="submit" class="rounded-xl bg-white px-6 py-3 text-sm font-semibold text-[#008060] transition-colors hover:bg-emerald-50">{{ __('Cari') }}</button>
            </form>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#F8FAFC] dark:bg-[#0d1310]">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">

            @if ($query !== '' && strlen($query) < 3)
                <p class="text-base font-normal text-gray-600 dark:text-gray-300">{{ __('Masukkan minimal 3 karakter untuk mencari.') }}</p>
            @else
                @forelse ($results as $result)
                    <a href="{{ $result['url'] }}"
                       class="mb-4 flex items-start justify-between gap-4 rounded-2xl border border-gray-100 bg-white p-5 shadow-sm transition hover:border-[#008060] dark:border-white/10 dark:bg-[#121a17]">
                        <span>
                            <span class="block text-base font-semibold text-gray-900 dark:text-white">{{ $result['title'] }}</span>
                            <span class="mt-1 block text-sm font-normal text-gray-500 dark:text-gray-400">{{ __($result['kategori']) }}</span>
                        </span>
                        <svg class="mt-1 h-5 w-5 flex-shrink-0 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"/></svg>
                    </a>
                @empty
                    <div class="rounded-2xl border border-gray-100 bg-white px-6 py-16 text-center shadow-sm dark:border-white/10 dark:bg-[#121a17]">
                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z"/></svg>
                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">
                            {{ $query === '' ? __('Masukkan kata kunci untuk mulai mencari.') : __('Tidak ada hasil yang cocok dengan kata kunci tersebut.') }}
                        </p>
                    </div>
                @endforelse
            @endif

        </div>
    </section>
@endsection
