@extends('layouts.app')

@section('title', 'Regulasi | PPID FSTJ')

@section('content')

    {{-- HEADER HALAMAN --}}
    <section class="bg-[#008060] py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                {{ $data['title'] }}
            </h1>
            <p class="mt-3 text-lg text-[#e0f2f1]">
                {{ $data['description'] }}
            </p>
        </div>
    </section>

    {{-- KONTEN TABEL REGULASI --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-12">
                                No.
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Judul Regulasi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                Nomor/Tahun
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-32">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-24">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($data['regulations'] as $regulation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $regulation['no'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-800 font-medium">
                                    {{ $regulation['title'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $regulation['number'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                        {{ $regulation['type'] == 'Internal PPID' ? 'bg-[#e0f2f1] text-[#008060]' :
                                        ($regulation['type'] == 'Hukum Negara' ? 'bg-blue-100 text-blue-800' : 'bg-yellow-100 text-yellow-800') }}">
                                        {{ $regulation['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="{{ $regulation['link'] }}" target="_blank" class="text-[#008060] hover:text-[#00664e] font-semibold transition duration-150 flex items-center justify-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @if (empty($data['regulations']))
                            <tr>
                                <td colspan="5" class="px-6 py-10 text-center text-gray-500 italic">
                                    Belum ada daftar regulasi yang tersedia.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Catatan Penting --}}
            <div class="mt-8 p-4 bg-gray-100 rounded-lg border border-gray-200 text-sm text-gray-600">
                <p class="font-semibold mb-1">Penting:</p>
                <p>Dokumen regulasi di halaman ini tersedia untuk diunduh secara langsung. Jika Anda membutuhkan dokumen regulasi spesifik lainnya, silakan gunakan menu Permohonan Informasi Publik.</p>
            </div>

        </div>
    </section>

@endsection
