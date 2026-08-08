@extends('layouts.app')

@section('title', 'Register Permohonan Informasi | PPID FSTJ')

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Transparansi Layanan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-3xl mx-auto leading-relaxed">{{ __($data['description']) }}</p>
        </div>
    </section>

    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">

            @include('partials.db_notice')

            {{-- Filter tahun --}}
            @if (!empty($data['years']))
                <form method="GET" action="{{ route('ppid.register') }}" class="mb-6 flex flex-wrap items-center gap-3">
                    <label for="tahun" class="text-sm font-semibold text-gray-600 dark:text-gray-300">{{ __('Tahun') }}</label>
                    <select id="tahun" name="tahun"
                            class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-normal text-gray-700 focus:border-[#10462F] focus:ring-[#10462F] dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-200">
                        <option value="">{{ __('Semua Tahun') }}</option>
                        @foreach ($data['years'] as $year)
                            <option value="{{ $year }}" @selected($data['selected_year'] == $year)>{{ $year }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-xl bg-[#10462F] px-5 py-2.5 text-sm font-semibold text-white transition-colors duration-200 hover:bg-[#0B3524]">{{ __('Tampilkan') }}</button>
                </form>
            @endif

            <div class="overflow-hidden rounded-2xl border border-gray-100 bg-white shadow-sm dark:border-white/10 dark:bg-[#0B2A1D]">
                <div class="overflow-x-auto">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-b border-gray-100 bg-[#FAF6EC] dark:border-white/10 dark:bg-[#082217]">
                                <th scope="col" class="w-12 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('No.') }}</th>
                                <th scope="col" class="w-48 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Nomor Registrasi') }}</th>
                                <th scope="col" class="min-w-[300px] px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Informasi yang Dimohon') }}</th>
                                <th scope="col" class="w-40 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Tanggal Permohonan') }}</th>
                                <th scope="col" class="w-40 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Tanggal Tanggapan') }}</th>
                                <th scope="col" class="w-44 px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">{{ __('Status') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                            @forelse ($data['items'] as $item)
                                <tr class="transition-colors duration-150 hover:bg-[#FAF6EC] dark:hover:bg-white/5">
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-400 dark:text-gray-500">{{ $item['no'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ $item['kode'] }}</td>
                                    <td class="px-6 py-4 text-base font-normal text-gray-700 dark:text-gray-200">{{ $item['rincian'] }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-normal text-gray-600 dark:text-gray-300">{{ $item['tanggal'] ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4 text-sm font-normal text-gray-600 dark:text-gray-300">{{ $item['tanggal_tanggapan'] ?: '-' }}</td>
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold
                                            @if (in_array($item['status'], ['selesai', 'disetujui'])) bg-emerald-50 text-[#10462F]
                                            @elseif (in_array($item['status'], ['ditolak', 'kedaluwarsa'])) bg-red-50 text-red-600
                                            @elseif ($item['status'] === 'ditolak_sebagian') bg-amber-50 text-amber-600
                                            @else bg-blue-50 text-blue-600 @endif">
                                            {{ __($item['status_label']) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-16 text-center">
                                        <svg class="mx-auto mb-3 h-10 w-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                        <p class="text-base font-normal text-gray-500 dark:text-gray-400">{{ __('Belum ada permohonan yang ditampilkan pada register publik.') }}</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Catatan privasi --}}
            <div class="mt-8 flex items-start gap-4 rounded-2xl border border-emerald-100 bg-emerald-50 p-6 sm:p-8 dark:border-emerald-500/20 dark:bg-emerald-500/10">
                <span class="flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-[#10462F] dark:bg-white/10 dark:text-[#3E9C6C]">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </span>
                <div>
                    <p class="mb-1 text-base font-semibold text-gray-900 dark:text-white">{{ __('Perlindungan Data Pemohon') }}</p>
                    <p class="text-base font-normal leading-relaxed text-gray-600 dark:text-gray-300">
                        {{ __('Register ini hanya memuat permohonan yang pemohonnya telah menyetujui publikasi. Identitas pemohon (nama, NIK, alamat, dan kontak) tidak ditampilkan. Untuk memantau permohonan Anda sendiri, gunakan menu') }}
                        <a href="{{ route('ppid.status') }}" class="font-semibold text-[#10462F] hover:text-[#0B3524] dark:text-[#3E9C6C]">{{ __('Cek Status Tiket') }}</a>.
                    </p>
                </div>
            </div>

        </div>
    </section>
@endsection
