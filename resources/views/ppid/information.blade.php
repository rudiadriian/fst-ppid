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
                {{ $data['description'] }}
            </p>
        </div>
    </section>

    {{-- KONTEN TABEL INFORMASI --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="bg-white rounded-xl shadow-lg overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-16">
                                No.
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Jenis Informasi
                            </th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider w-40">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($data['items'] as $item)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item['no'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-700">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $data['title'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    {{-- Tombol Aksi (Simulasi) --}}
                                    <a href="#" class="text-[#008060] hover:text-[#00664e] font-semibold transition duration-150">
                                        Lihat / Unduh
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @if (empty($data['items']))
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic">
                                    Belum ada daftar informasi yang tersedia pada kategori ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Catatan Kaki --}}
            <div class="mt-8 p-4 bg-gray-100 rounded-lg border border-gray-200 text-sm text-gray-600">
                <p class="font-semibold mb-1">Catatan:</p>
                <p>Informasi yang dikecualikan (rahasia perusahaan, pribadi, dll.) tidak dapat diakses langsung. Anda dapat mengajukan permohonan resmi untuk informasi tersebut melalui menu **Permohonan Informasi Publik**.</p>
            </div>

        </div>
    </section>

@endsection
