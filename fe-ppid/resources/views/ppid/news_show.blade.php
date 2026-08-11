@extends('layouts.app')

@section('title', $berita->judul . ' | PPID FSTJ')

@section('content')

    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-6xl mx-auto px-6 lg:px-8 py-16 lg:py-20">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ $kategori }}</p>
            <h1 class="text-3xl lg:text-4xl font-bold text-white leading-tight">{{ $berita->judul }}</h1>
            <p class="mt-4 text-sm font-normal text-white/70">{{ $tanggal }}</p>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-6xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            <article class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm sm:p-10 dark:border-white/10 dark:bg-[#0B2A1D]">
                @if ($gambar)
                    <img src="{{ $gambar }}" alt="{{ $berita->judul }}" class="mb-8 w-full rounded-xl object-cover">
                @endif

                @if ($berita->ringkasan)
                    <p class="mb-6 text-lg font-normal leading-relaxed text-gray-700 dark:text-gray-200">{{ $berita->ringkasan }}</p>
                @endif

                {{-- Isi berita ditulis lewat editor CMS. Hanya tag aman yang
                     dirender agar konten tidak bisa menyisipkan skrip. --}}
                <div class="fs-rte">
                    {!! strip_tags($berita->konten ?? '', '<p><br><strong><em><u><ul><ol><li><h2><h3><h4><blockquote><a><img><table><thead><tbody><tr><th><td>') !!}
                </div>
            </article>

            @if (count($lainnya) > 0)
                <h2 class="mt-14 mb-6 text-2xl font-bold text-gray-900 dark:text-white">{!! $judulDua(__('Berita Lainnya')) !!}</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                    @foreach ($lainnya as $item)
                        <a href="{{ $item['url'] }}" class="group h-full flex flex-col bg-white dark:bg-[#0B2A1D] rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 hover:shadow-lg transition-all duration-300">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="h-36 w-full object-cover">
                            <div class="p-5">
                                <p class="text-xs font-medium text-gray-400 mb-1 uppercase tracking-wide">{{ $item['date'] }}</p>
                                <h3 class="text-base font-bold text-gray-900 dark:text-white leading-snug group-hover:text-[#10462F] transition-colors">{{ $item['title'] }}</h3>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            <a href="{{ route('ppid.news.index') }}" class="mt-10 inline-flex items-center gap-1.5 text-sm font-semibold text-[#10462F] hover:text-[#0B3524]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path></svg>
                {{ __('Kembali ke daftar berita') }}
            </a>
        </div>
    </section>

@endsection
