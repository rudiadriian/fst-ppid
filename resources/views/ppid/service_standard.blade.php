@extends('layouts.app')

@section('title', $data['title'] . ' | Standar Pelayanan PPID')

@section('content')

    {{-- HEADER HALAMAN --}}
    <section class="bg-blue-600 py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl font-extrabold text-white">
                Standar Pelayanan
            </h1>
            <p class="mt-3 text-lg text-blue-100">
                {{ $data['title'] }}
            </p>
        </div>
    </section>

    {{-- KONTEN UTAMA --}}
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 bg-white p-8 rounded-xl shadow-lg">

            <h2 class="text-3xl font-bold text-gray-800 mb-6 border-b pb-2">
                {{ $data['title'] }}
            </h2>

            @if ($slug === 'maklumat-pelayanan')
                {{-- Tampilan untuk Maklumat Pelayanan --}}
                <div class="text-center p-8 border-4 border-blue-200 rounded-lg bg-blue-50">
                    <p class="text-xl font-semibold italic text-gray-700 mb-6">
                        "{{ $data['intro'] }}"
                    </p>
                    <ul class="list-none space-y-3 text-gray-700 text-left border-t pt-4">
                        @foreach ($data['content_list'] as $item)
                            <li class="flex items-start">
                                <svg class="w-6 h-6 text-blue-600 mr-2 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                    <p class="mt-8 text-sm italic text-blue-700 font-medium">
                        {{ $data['footer'] }}
                    </p>
                </div>

            @elseif ($slug === 'prosedur-permohonan')
                {{-- Tampilan untuk Prosedur Permohonan --}}
                <p class="text-gray-700 mb-8">{{ $data['intro'] }}</p>

                <ol class="relative border-l border-gray-200 space-y-8">
                    @foreach ($data['steps'] as $index => $step)
                        <li class="ml-6">
                            <span class="absolute flex items-center justify-center w-8 h-8 bg-blue-100 rounded-full -left-4 ring-4 ring-white">
                                <span class="font-bold text-blue-600">{{ $index + 1 }}</span>
                            </span>
                            <h3 class="flex items-center mb-1 text-lg font-semibold text-gray-900">{{ Str::limit($step, 40) }}</h3>
                            <p class="text-base font-normal text-gray-500">{{ $step }}</p>
                        </li>
                    @endforeach
                </ol>

            @elseif ($slug === 'jalur-waktu-layanan')
                {{-- Tampilan untuk Jalur & Waktu Layanan --}}
                <p class="text-gray-700 mb-8">{{ $data['intro'] }}</p>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    {{-- Jalur / Channel --}}
                    <div class="p-6 border rounded-lg shadow-sm bg-blue-50">
                        <h3 class="text-xl font-bold text-blue-800 mb-3 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                            Jalur Pelayanan
                        </h3>
                        <ul class="list-disc list-inside space-y-2 text-gray-700">
                            @foreach ($data['channels'] as $channel)
                                <li>{{ $channel }}</li>
                            @endforeach
                        </ul>
                    </div>

                    {{-- Waktu Layanan --}}
                    <div class="p-6 border rounded-lg shadow-sm bg-gray-100">
                        <h3 class="text-xl font-bold text-gray-800 mb-3 flex items-center">
                            <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Waktu Layanan
                        </h3>
                        <table class="w-full text-gray-700">
                            <tbody>
                                @foreach ($data['hours'] as $schedule)
                                    <tr>
                                        <td class="font-medium pr-4">{{ explode(':', $schedule)[0] }}:</td>
                                        <td>{{ explode(':', $schedule)[1] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <p class="mt-4 text-xs italic text-gray-500">{{ $data['note'] }}</p>
                    </div>
                </div>

            @else
                <p class="text-red-500 text-center">Konten Standar Pelayanan tidak ditemukan.</p>
            @endif
        </div>
    </section>

@endsection
