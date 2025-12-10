@extends('layouts.app')

{{-- Menggunakan judul dinamis dari data --}}
@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')

    {{-- =====================================================================
         1. HEADER HALAMAN (MODERN DENGAN GRADIENT DAN POLA)
         ===================================================================== --}}
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        {{-- Pola Latar Belakang (Sama dengan section gelap di Home) --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                {{ $data['title'] }}
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                Informasi penting mengenai Pejabat Pengelola Informasi dan Dokumentasi (PPID) Food Station.
            </p>
        </div>
    </section>

    {{-- =====================================================================
         2. KONTEN UTAMA
         ===================================================================== --}}
    <section class="py-12 lg:py-20 bg-gray-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-10 lg:p-16 rounded-3xl shadow-xl border border-gray-100">

                @if ($slug === 'singkat')
                    {{-- Konten untuk Profil Singkat --}}

                    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-4">
                        Profil Singkat PPID Food Station
                    </h2>

                    {{-- 2.1. INTRODUKSI --}}
                    <div class="mb-12">
                        <p class="text-lg text-gray-700 leading-relaxed italic border-l-4 border-[#008060] pl-4 py-1 bg-emerald-50/50 rounded-lg p-4">
                            {{ $data['intro'] }}
                        </p>
                    </div>

                    {{-- 2.2. FUNGSI & WEWENANG (Menggunakan Grid Card) --}}
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-12">

                        {{-- Kartu Fungsi --}}
                        <div class="p-6 rounded-2xl bg-emerald-50 border border-emerald-100 shadow-md">
                            <h3 class="text-2xl font-bold text-[#008060] mb-5 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.525.321 1.157.498 1.724 1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                Fungsi Utama PPID
                            </h3>
                            <ul class="list-none text-gray-700 space-y-3">
                                @foreach ($data['functions'] as $function)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 mt-1 mr-3 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                        <p class="text-base">{{ $function }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>

                        {{-- Kartu Wewenang --}}
                        <div class="p-6 rounded-2xl bg-red-50 border border-red-100 shadow-md">
                            <h3 class="text-2xl font-bold text-red-700 mb-5 flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Wewenang PPID
                            </h3>
                            <ul class="list-none text-gray-700 space-y-3">
                                @foreach ($data['authorities'] as $authority)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 mt-1 mr-3 text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zm-5.414 7.586l1.414 1.414a1 1 0 010 1.414l-2 2a1 1 0 01-1.414 0l-2-2a1 1 0 010-1.414l1.414-1.414a1 1 0 011.414 0l.5.5.5-.5z"></path></svg>
                                        <p class="text-base">{{ $authority }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    {{-- 2.3. WAKTU LAYANAN --}}
                    <div class="p-6 border border-gray-200 rounded-2xl bg-gray-50 text-center lg:text-left">
                        <h3 class="text-2xl font-bold text-gray-800 mb-4 flex items-center justify-center lg:justify-start">
                             <svg class="w-7 h-7 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Waktu Layanan Informasi Publik
                        </h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:gap-12 text-gray-700">
                             <div>
                                @foreach ($data['service_hours'] as $schedule)
                                    <p class="font-semibold text-lg">{{ $schedule }}</p>
                                @endforeach
                             </div>
                             <div class="text-sm italic text-gray-500 mt-4 md:mt-0 pt-4 md:pt-0 border-t md:border-t-0 md:border-l md:pl-4 border-gray-200">
                                <p>Layanan dilaksanakan setiap hari kerja (Senin s.d. Jumat) kecuali hari libur nasional atau cuti bersama.</p>
                             </div>
                        </div>
                    </div>

                @elseif ($slug === 'struktur')
                    {{-- Konten untuk Struktur Organisasi --}}

                    <h2 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-4">
                        Struktur Organisasi PPID
                    </h2>
                    <p class="text-lg text-gray-700 leading-relaxed mb-10">
                        {{ $data['content'] }}
                    </p>

                    {{-- Visualisasi Struktur --}}
                    <div class="bg-gray-100 border-2 border-dashed border-gray-300 rounded-2xl p-6 flex flex-col items-center justify-center min-h-[400px] shadow-inner relative">
                        {{-- Aksen Dekorasi --}}
                        <div class="absolute top-4 left-4 w-6 h-6 bg-[#008060] rounded-full opacity-60 blur-sm"></div>
                        <div class="absolute bottom-4 right-4 w-8 h-8 bg-yellow-500 rounded-full opacity-60 blur-sm"></div>

                        <p class="text-gray-500 italic text-center mb-6">
                            [Placeholder Gambar/Diagram Struktur Organisasi akan ditempatkan di sini]
                        </p>

                        {{-- Simulasi Diagram Node --}}
                        <div class="flex flex-col items-center space-y-4">
                            <div class="bg-[#008060] text-white py-2 px-6 rounded-full font-bold shadow-md">Atasan PPID</div>
                            <svg height="20" width="20" class="text-gray-400"><line x1="10" y1="0" x2="10" y2="20" stroke="currentColor" stroke-width="2"/></svg>
                            <div class="bg-blue-600 text-white py-2 px-6 rounded-full font-bold shadow-md">PPID Utama</div>
                            <svg height="20" width="20" class="text-gray-400"><line x1="10" y1="0" x2="10" y2="20" stroke="currentColor" stroke-width="2"/></svg>
                            <div class="flex space-x-6">
                                <div class="bg-gray-700 text-white py-2 px-4 rounded-lg text-sm">PPID Pembantu 1</div>
                                <div class="bg-gray-700 text-white py-2 px-4 rounded-lg text-sm">PPID Pembantu 2</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 text-center">
                        <a href="#" class="inline-flex items-center text-[#008060] font-bold hover:text-[#00664e] bg-emerald-50 px-6 py-3 rounded-xl transition duration-300 shadow-md">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh Dokumen Struktur Organisasi (PDF)
                        </a>
                    </div>

                @elseif ($slug === 'visi-misi')
                    {{-- Konten untuk Visi & Misi --}}

                    <h2 class="text-3xl font-extrabold text-gray-900 mb-12 border-b pb-4">
                        Visi dan Misi PPID
                    </h2>

                    {{-- Kartu Visi --}}
                    <div class="mb-12 p-8 rounded-3xl bg-emerald-50 border border-emerald-200 shadow-lg relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-[#008060]/10 rounded-full -mr-10 -mt-10"></div>
                        <h3 class="text-2xl font-bold text-[#008060] mb-4 flex items-center relative z-10">
                            <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M12 15a4 4 0 100-8 4 4 0 000 8z"></path></svg>
                            Visi
                        </h3>
                        <p class="text-xl text-gray-800 italic leading-relaxed relative z-10">
                            "{{ $data['content']['Visi'] }}"
                        </p>
                    </div>

                    {{-- List Misi --}}
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
                            <svg class="w-6 h-6 mr-3 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                            Misi
                        </h3>
                        <ul class="space-y-4 text-gray-700">
                            @foreach ($data['content']['Misi'] as $index => $misi)
                                <li class="flex items-start bg-gray-50 p-4 rounded-xl border-l-4 border-blue-500 shadow-sm hover:shadow-md transition">
                                    <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 font-extrabold mr-4 mt-0.5">
                                        {{ $index + 1 }}
                                    </div>
                                    <p class="text-base leading-relaxed">
                                        {{ $misi }}
                                    </p>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                @else
                    {{-- Konten Default (Error) --}}
                    <div class="text-center py-10 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-red-600 font-semibold text-lg">Konten halaman profil tidak dikenali atau belum tersedia.</p>
                        <p class="text-red-500 text-sm mt-2">Mohon periksa slug yang digunakan.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

    {{-- Section Kontak Singkat (Opsional, jika ingin menambahkan CTA) --}}
    <section class="bg-gray-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h3 class="text-2xl font-bold text-white mb-4">Butuh Informasi Lebih Lanjut?</h3>
            <a href="{{ route('ppid.request') }}" class="inline-block bg-[#008060] text-white px-8 py-3 rounded-full text-lg font-bold hover:bg-[#00664e] transition shadow-lg">
                Ajukan Permohonan Sekarang
            </a>
        </div>
    </section>

@endsection
