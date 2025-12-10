@extends('layouts.app')

{{-- Menggunakan judul dinamis dari data --}}
@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')

    {{-- HEADER HALAMAN --}}
    <section class="bg-[#008060] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                {{ $data['title'] }}
            </h1>
            <p class="mt-3 text-lg text-[#e0f2f1]">
                Informasi penting mengenai Pejabat Pengelola Informasi dan Dokumentasi (PPID) Food Station.
            </p>
        </div>
    </section>

    {{-- KONTEN UTAMA --}}
    <section class="py-2 lg:py-2 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-8 rounded-xl shadow-lg">
            <section class="py-2 lg:py-24 bg-gray-50">
                <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-8 rounded-xl shadow-lg">
                    @if ($slug === 'singkat')
                        <h2 class="text-3xl font-extrabold text-[#008060] mb-6">
                            Profil Pejabat Pengelola Informasi dan Dokumentasi
                        </h2>
                        <p class="text-gray-700 leading-relaxed mb-8 border-b pb-6">
                            {{ $data['intro'] }}
                        </p>
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-3 border-l-4 border-yellow-500 pl-3">Fungsi PPID</h3>
                            <ul class="list-disc ml-6 text-gray-700 space-y-2">
                                @foreach ($data['functions'] as $function)
                                    <li>{{ $function }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="mb-8">
                            <h3 class="text-xl font-bold text-gray-800 mb-3 border-l-4 border-red-500 pl-3">Wewenang PPID</h3>
                            <ul class="list-decimal ml-6 text-gray-700 space-y-2">
                                @foreach ($data['authorities'] as $authority)
                                    <li>{{ $authority }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-4 border border-gray-200 rounded-lg bg-gray-50">
                            <h3 class="text-xl font-bold text-gray-800 mb-3">Waktu Layanan Informasi Publik</h3>
                            <ul class="text-gray-700 space-y-1">
                                @foreach ($data['service_hours'] as $schedule)
                                    <li class="font-medium">{{ $schedule }}</li>
                                @endforeach
                            </ul>
                            <p class="text-sm italic text-gray-500 mt-2">
                                Layanan dilaksanakan setiap hari kerja.
                            </p>
                        </div>
                    @elseif ($slug === 'struktur')
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Struktur Organisasi PPID</h2>
                        <p class="text-gray-600 mb-6">{{ $data['content'] }}</p>
                        <div class="bg-gray-200 border border-gray-300 rounded-lg p-4 flex items-center justify-center min-h-[300px]">
                            <p class="text-gray-500 italic">
                                [Placeholder Gambar Struktur Organisasi akan ditempatkan di sini]
                            </p>
                        </div>
                        <div class="mt-6 text-center">
                            <a href="#" class="inline-flex items-center text-[#008060] font-medium hover:text-[#00664e]">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Struktur Organisasi (PDF/JPG)
                            </a>
                        </div>
                    @elseif ($slug === 'visi-misi')
                        <div class="mb-8 border-l-4 border-[#008060] pl-4">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">Visi</h2>
                            <p class="text-gray-700 italic text-lg">
                                "{{ $data['content']['Visi'] }}"
                            </p>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div>
                            <h2 class="text-2xl font-bold text-gray-800 mb-4">Misi</h2>
                            <ul class="space-y-3 list-disc list-inside text-gray-700">
                                @foreach ($data['content']['Misi'] as $misi)
                                    <li class="pl-2">
                                        {{ $misi }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-red-500 text-center">Konten halaman profil tidak dikenali.</p>
                    @endif
                    </div>
            </section>
        </div>
    </section>

@endsection
