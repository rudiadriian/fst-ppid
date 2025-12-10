@extends('layouts.app')

@section('title', $data['title'] . ' | Standar Pelayanan PPID')

@section('content')
    <section class="relative bg-gradient-to-r from-emerald-900 to-gray-900 py-16 lg:py-24 overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center z-10">
            <h1 class="text-4xl sm:text-5xl font-extrabold text-white leading-tight drop-shadow">
                Standar Pelayanan
            </h1>
            <p class="mt-4 text-lg text-emerald-200 max-w-3xl mx-auto">
                {{ $data['title'] }}
            </p>
        </div>
    </section>
    <section class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white p-6 sm:p-8 rounded-3xl shadow-xl border border-gray-100">

                <h2 class="text-3xl font-extrabold text-gray-900 mb-8 border-b pb-3">
                    Detail {{ $data['title'] }}
                </h2>

                @if ($slug === 'maklumat-pelayanan')
                    <div class="p-8 border-4 border-[#008060] rounded-2xl bg-emerald-50 relative overflow-hidden">
                         <svg class="w-16 h-16 text-[#008060] absolute top-2 right-2 opacity-10" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path></svg>

                        <p class="text-xl font-semibold italic text-gray-800 mb-8 relative z-10">
                            "{{ $data['intro'] }}"
                        </p>

                        <ul class="list-none space-y-4 text-gray-700 text-left border-t border-emerald-200 pt-6">
                            @foreach ($data['content_list'] as $item)
                                <li class="flex items-start">
                                    <svg class="w-6 h-6 text-[#008060] mr-3 flex-shrink-0 mt-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="text-base leading-relaxed">{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <p class="mt-8 text-sm italic text-gray-600 border-t border-emerald-200 pt-4">
                            {{ $data['footer'] }}
                        </p>
                    </div>

                @elseif ($slug === 'prosedur-permohonan')
                    <p class="text-gray-700 mb-10 leading-relaxed">{{ $data['intro'] }}</p>
                    <ol class="relative border-l-4 border-emerald-200 space-y-12 pl-4">
                        @foreach ($data['steps'] as $index => $step)
                            <li class="relative">
                                <span class="absolute flex items-center justify-center w-10 h-10 bg-[#008060] rounded-full -left-7 ring-4 ring-white shadow-lg">
                                    <span class="font-bold text-white text-lg">{{ $index + 1 }}</span>
                                </span>
                                <div class="ml-4 p-4 bg-gray-50 rounded-xl shadow-md border-t-2 border-[#008060] hover:shadow-lg transition duration-300">
                                    <h3 class="flex items-center mb-1 text-xl font-bold text-gray-900">
                                        {{ Str::limit($step, 40) }}
                                    </h3>
                                    <p class="text-base font-normal text-gray-700 leading-relaxed">{{ $step }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="mt-12 text-center">
                        <a href="{{ route('ppid.request') }}" class="inline-block bg-[#008060] text-white px-8 py-3 rounded-full text-lg font-bold hover:bg-[#00664e] transition shadow-xl">
                            Mulai Ajukan Permohonan
                        </a>
                    </div>
                @elseif ($slug === 'jalur-waktu-layanan')
                    <p class="text-gray-700 mb-10 leading-relaxed">{{ $data['intro'] }}</p>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <div class="p-8 rounded-2xl shadow-lg border border-emerald-100 bg-emerald-50">
                            <h3 class="text-2xl font-bold text-[#008060] mb-5 flex items-center">
                                <svg class="w-7 h-7 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                                Jalur Pelayanan
                            </h3>
                            <ul class="list-none space-y-3 text-gray-700">
                                @foreach ($data['channels'] as $channel)
                                    <li class="flex items-start">
                                        <svg class="w-5 h-5 mr-3 mt-1 text-[#008060] flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM6 10a1 1 0 000 2h8a1 1 0 100-2H6z"></path></svg>
                                        <p class="text-base">{{ $channel }}</p>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="p-8 rounded-2xl shadow-lg border border-gray-200 bg-gray-100">
                            <h3 class="text-2xl font-bold text-gray-800 mb-5 flex items-center">
                                <svg class="w-7 h-7 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Waktu Layanan
                            </h3>
                            <table class="w-full text-gray-700 text-lg">
                                <tbody>
                                    @foreach ($data['hours'] as $schedule)
                                        <tr>
                                            <td class="font-bold pr-4 py-1">{{ explode(':', $schedule)[0] }}:</td>
                                            <td class="py-1">{{ explode(':', $schedule)[1] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <p class="mt-6 text-sm italic text-gray-600 border-t border-gray-300 pt-3">{{ $data['note'] }}</p>
                        </div>
                    </div>
                @else
                    <div class="text-center py-10 bg-red-50 border border-red-200 rounded-xl">
                        <p class="text-red-600 font-semibold text-lg">Konten Standar Pelayanan tidak ditemukan.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
