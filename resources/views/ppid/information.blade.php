@extends('layouts.app')

@section('title', $data['title'] . ' | PPID FSTJ')

@section('content')
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                Daftar Informasi {{ $data['title'] }}
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                {{ $data['description'] }}
            </p>
        </div>
    </section>
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center mb-8 gap-4">
                <h2 class="text-2xl font-bold text-gray-800 hidden md:block">
                    Dokumen & Arsip
                </h2>
                <div class="w-full md:w-auto flex-grow max-w-lg">
                    <input type="text" placeholder="Filter cepat: Cari judul dokumen..." class="w-full p-3 border border-gray-300 rounded-xl focus:ring-[#008060] focus:border-[#008060] shadow-sm">
                </div>
                <button class="text-sm font-semibold text-[#008060] hover:underline whitespace-nowrap">
                    Tampilkan Filter Lanjut
                </button>
            </div>
            <div class="bg-white rounded-2xl shadow-2xl overflow-x-auto border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-800">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider w-16">
                                No.
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider min-w-[300px] lg:min-w-[400px]">
                                Jenis Informasi
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-sm font-bold text-white uppercase tracking-wider w-40">
                                Kategori
                            </th>
                            <th scope="col" class="px-6 py-4 text-center text-sm font-bold text-white uppercase tracking-wider w-40">
                                Aksi
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($data['items'] as $item)
                            <tr class="hover:bg-emerald-50/50 transition duration-150">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $item['no'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-normal text-sm text-gray-700 font-medium">
                                    {{ $item['name'] }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs">
                                    <span class="px-3 py-1 inline-flex leading-5 font-semibold rounded-full bg-emerald-100 text-[#008060]">
                                        {{ $data['title'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <a href="#" class="inline-flex items-center px-4 py-2 border border-[#008060] text-sm font-semibold rounded-full text-[#008060] bg-white hover:bg-[#008060] hover:text-white transition duration-200 shadow-sm">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach

                        @if (empty($data['items']))
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-gray-500 italic bg-gray-50">
                                    <p class="mb-2">
                                        <svg class="w-8 h-8 text-gray-400 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    </p>
                                    Belum ada daftar informasi yang tersedia pada kategori ini.
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-8 p-6 bg-yellow-50 rounded-2xl border-l-4 border-yellow-500 shadow-md text-sm text-gray-700">
                <p class="font-bold text-lg text-yellow-700 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    Informasi Pengecualian
                </p>
                <p>
                    Informasi yang dikecualikan (rahasia perusahaan, pribadi, dll.) tidak dapat diakses langsung di sini.
                    Anda dapat mengajukan permohonan resmi untuk informasi tersebut melalui menu **Permohonan Informasi Publik**.
                </p>
            </div>

        </div>
    </section>

@endsection
