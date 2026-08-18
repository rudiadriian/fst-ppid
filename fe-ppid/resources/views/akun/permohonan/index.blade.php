@extends('layouts.portal')

@section('title', __('Permohonan Informasi') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Permohonan Informasi'))

@section('portal')

    @php
        $bolehAjukan = $pemohon->dataTerverifikasi();

        /* Tautan judul kolom: mempertahankan filter lain, membalik arah urut. */
        $tautanUrut = function (string $kolom) use ($urut, $arah) {
            $arahBaru = ($urut === $kolom && $arah === 'asc') ? 'desc' : 'asc';

            return request()->fullUrlWithQuery(['urut' => $kolom, 'arah' => $arahBaru, 'page' => 1]);
        };

        $warnaStatus = [
            'Selesai' => 'bg-emerald-50 text-[#10462F] border-emerald-100',
            'Tolak' => 'bg-red-50 text-red-700 border-red-100',
            'Revisi' => 'bg-amber-50 text-amber-800 border-amber-100',
            'Menunggu Persetujuan' => 'bg-blue-50 text-blue-700 border-blue-100',
            'Dalam Proses' => 'bg-gray-100 text-gray-700 border-gray-200',
        ];
    @endphp

    @include('akun.partials.alert-verifikasi')

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden">

        @include('akun.partials.tab-status')

        {{-- Alat: cari, jumlah baris, tombol tambah --}}
        <div class="p-5 border-b border-gray-100 dark:border-white/10 flex flex-wrap items-center gap-3 justify-between">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="search" name="cari" value="{{ $cari }}" placeholder="{{ __('Cari nomor atau rincian…') }}"
                       class="px-4 py-2.5 bg-gray-50 border border-gray-200 dark:border-white/10 dark:bg-[#0B2A1D] rounded-xl text-sm outline-none focus:border-[#10462F] focus:ring-2 focus:ring-[#10462F]/15">
                <input type="hidden" name="urut" value="{{ $urut }}">
                <input type="hidden" name="arah" value="{{ $arah }}">

                <select name="per" onchange="this.form.submit()"
                        class="px-3 py-2.5 bg-gray-50 border border-gray-200 dark:border-white/10 dark:bg-[#0B2A1D] rounded-xl text-sm outline-none">
                    @foreach ($opsiPer as $opsi)
                        <option value="{{ $opsi }}" @selected($per === $opsi)>{{ $opsi }} {{ __('baris') }}</option>
                    @endforeach
                </select>

                <button type="submit" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5">
                    {{ __('Cari') }}
                </button>

                @if ($cari !== '')
                    <a href="{{ route('akun.permohonan.index') }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Reset') }}</a>
                @endif
            </form>

            @if ($bolehAjukan)
                <a href="{{ route('akun.permohonan.create') }}" class="{{ $fsBtn }} py-2.5 px-5 text-sm">
                    + {{ __('Tambah Pengajuan') }}
                </a>
            @else
                {{-- Belum terverifikasi: tombol dimatikan, bukan disembunyikan,
                     supaya jelas apa yang harus dilakukan lebih dulu. --}}
                <button type="button" disabled
                        title="{{ __('Verifikasi Data Pemohon dulu untuk bisa mengajukan.') }}"
                        class="{{ $fsBtn }} py-2.5 px-5 text-sm">
                    + {{ __('Tambah Pengajuan') }}
                </button>
            @endif
        </div>

        @if ($daftar->total() === 0)
            <p class="px-6 py-10 text-sm text-center text-gray-500 dark:text-gray-400">
                @if ($cari !== '')
                    {{ __('Tidak ada permohonan yang cocok dengan pencarian.') }}
                @elseif ($status !== '')
                    {{ __('Tidak ada permohonan berstatus') }} “{{ __($status) }}”.
                @else
                    {{ __('Belum ada permohonan.') }}
                @endif
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#F3ECDD] dark:bg-[#082217] text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ $tautanUrut('kode_permohonan') }}" class="inline-flex items-center gap-1 hover:text-[#10462F] dark:hover:text-[#3E9C6C]">
                                    {{ __('Nomor Registrasi') }}
                                    @if ($urut === 'kode_permohonan')<span>{{ $arah === 'asc' ? '↑' : '↓' }}</span>@endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">{{ __('Rincian Informasi') }}</th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ $tautanUrut('tanggal_permohonan') }}" class="inline-flex items-center gap-1 hover:text-[#10462F] dark:hover:text-[#3E9C6C]">
                                    {{ __('Tanggal') }}
                                    @if ($urut === 'tanggal_permohonan')<span>{{ $arah === 'asc' ? '↑' : '↓' }}</span>@endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ $tautanUrut('status') }}" class="inline-flex items-center gap-1 hover:text-[#10462F] dark:hover:text-[#3E9C6C]">
                                    {{ __('Status') }}
                                    @if ($urut === 'status')<span>{{ $arah === 'asc' ? '↑' : '↓' }}</span>@endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold text-right">{{ __('Aksi') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($daftar as $item)
                            @php $label = $item->labelStatus(); @endphp
                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-900 dark:text-white whitespace-nowrap">{{ $item->kode_permohonan }}</td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 max-w-sm">{{ \Illuminate\Support\Str::limit($item->rincian_informasi, 90) }}</td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ optional($item->tanggal_permohonan)->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 rounded-full border text-xs font-semibold {{ $warnaStatus[$label] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">{{ __($label) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <a href="{{ route('akun.permohonan.show', $item->id) }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Detail') }}</a>
                                    @if (!$item->survei && $item->bolehDisurvei())
                                        <a href="{{ route('akun.survei.create', $item->id) }}" class="ml-3 text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">{{ __('Isi Survei') }}</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    {{ __('Menampilkan') }} {{ $daftar->firstItem() }}–{{ $daftar->lastItem() }} {{ __('dari') }} {{ $daftar->total() }} {{ __('data') }}
                </p>
                {{ $daftar->links() }}
            </div>
        @endif
    </div>

@endsection
