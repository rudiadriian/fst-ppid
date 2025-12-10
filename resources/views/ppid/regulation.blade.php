@extends('layouts.app')

@section('title', 'Regulasi | PPID FSTJ')

@section('content')

    {{-- =====================================================================
         1. HEADER HALAMAN (MODERN DENGAN GRADIENT DAN POLA)
         ===================================================================== --}}
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        {{-- Pola Latar Belakang --}}
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                {{ $data['title'] ?? 'Daftar Regulasi' }}
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                {{ $data['description'] ?? 'Peraturan dan ketentuan yang menjadi landasan operasional PPID Food Station.' }}
            </p>
        </div>
    </section>

    {{-- =====================================================================
         2. KONTEN TABEL REGULASI (RESPONSIVE DAN ZEBRA STRIPING)
         ===================================================================== --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
                <div class="overflow-x-auto"> {{-- Membuat tabel bisa discroll horizontal --}}
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-emerald-50 sticky top-0"> {{-- Header sticky untuk scroll horizontal --}}
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-12">
                                    No.
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider min-w-[300px]">
                                    Judul Regulasi
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-40">
                                    Nomor/Tahun
                                </th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-32">
                                    Kategori
                                </th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider w-28">
                                    Aksi
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @foreach ($data['regulations'] as $index => $regulation)
                                <tr class="{{ $index % 2 == 1 ? 'bg-gray-50 hover:bg-gray-100' : 'bg-white hover:bg-gray-100' }} transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                        {{ $regulation['no'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-normal text-sm text-gray-800 font-medium max-w-lg">
                                        {{ $regulation['title'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $regulation['number'] }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        {{-- Badge Kategori --}}
                                        <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full shadow-sm
                                            {{ $regulation['type'] == 'Internal PPID' ? 'bg-[#e0f2f1] text-[#008060]' :
                                            ($regulation['type'] == 'Hukum Negara' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $regulation['type'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                        <a href="{{ $regulation['link'] }}" target="_blank"
                                           class="inline-flex items-center justify-center bg-[#008060] text-white px-4 py-2 rounded-full text-xs font-bold hover:bg-[#00664e] transition duration-300 shadow-md">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                            Unduh
                                        </a>
                                    </td>
                                </tr>
                            @endforeach

                            @if (empty($data['regulations']))
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic bg-white">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        Belum ada daftar regulasi yang tersedia saat ini.
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Catatan Penting (Alert Card) --}}
            <div class="mt-12 p-6 bg-emerald-50 rounded-2xl border-l-4 border-[#008060] shadow-md flex items-start space-x-4">
                <svg class="w-6 h-6 text-[#008060] flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <div>
                    <p class="font-bold text-gray-800 mb-1">Catatan Penting:</p>
                    <p class="text-sm text-gray-700">Dokumen regulasi di halaman ini tersedia untuk diunduh secara langsung (Setiap Saat). Jika Anda membutuhkan dokumen regulasi spesifik lainnya, silakan gunakan menu <a href="{{ route('ppid.request') }}" class="font-semibold text-[#008060] hover:underline">Permohonan Informasi Publik</a>.</p>
                </div>
            </div>

        </div>
    </section>

@endsection
