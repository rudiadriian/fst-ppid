@extends('layouts.portal')

@section('title', __('Permohonan Keberatan') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Permohonan Keberatan'))

@section('portal')

    @php
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

    @unless ($punyaPermohonan)
        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-100 text-sm text-amber-900 flex flex-wrap items-center justify-between gap-4">
            <p>{{ __('Keberatan hanya bisa diajukan atas permohonan yang pernah Anda kirim. Ajukan permohonan informasi lebih dulu.') }}</p>
            <a href="{{ route('akun.permohonan.index') }}" class="font-bold underline">{{ __('Buka Permohonan Informasi') }}</a>
        </div>
    @endunless

    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden">

        @include('akun.partials.tab-status')

        <div class="p-5 border-b border-gray-100 dark:border-white/10 flex flex-wrap items-center gap-3 justify-between">
            <form method="GET" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="status" value="{{ $status }}">
                <input type="search" name="cari" value="{{ $cari }}" placeholder="{{ __('Cari nomor permohonan atau isi keberatan…') }}"
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
                    <a href="{{ route('akun.keberatan.index') }}" class="text-sm font-semibold text-[#E87317] hover:underline">{{ __('Reset') }}</a>
                @endif
            </form>

            <a href="{{ route('akun.keberatan.create') }}" class="{{ $fsBtn }} py-2.5 px-5 text-sm">+ {{ __('Tambah Pengajuan') }}</a>
        </div>

        @if ($daftar->total() === 0)
            <p class="px-6 py-10 text-sm text-center text-gray-500 dark:text-gray-400">
                @if ($cari !== '')
                    {{ __('Tidak ada keberatan yang cocok dengan pencarian.') }}
                @elseif ($status !== '')
                    {{ __('Tidak ada keberatan berstatus') }} “{{ __($status) }}”.
                @else
                    {{ __('Belum ada keberatan yang diajukan.') }}
                @endif
            </p>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[#F3ECDD] dark:bg-[#082217] text-left text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">
                        <tr>
                            <th class="px-6 py-3 font-semibold">{{ __('Nomor Keberatan') }}</th>
                            <th class="px-6 py-3 font-semibold">{{ __('Alasan Keberatan') }}</th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ $tautanUrut('tanggal_keberatan') }}" class="inline-flex items-center gap-1 hover:text-[#10462F] dark:hover:text-[#3E9C6C]">
                                    {{ __('Tanggal') }}
                                    @if ($urut === 'tanggal_keberatan')<span>{{ $arah === 'asc' ? '↑' : '↓' }}</span>@endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">
                                <a href="{{ $tautanUrut('status') }}" class="inline-flex items-center gap-1 hover:text-[#10462F] dark:hover:text-[#3E9C6C]">
                                    {{ __('Status') }}
                                    @if ($urut === 'status')<span>{{ $arah === 'asc' ? '↑' : '↓' }}</span>@endif
                                </a>
                            </th>
                            <th class="px-6 py-3 font-semibold">{{ __('Lampiran') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-white/10">
                        @foreach ($daftar as $item)
                            @php $label = $item->labelStatus(); @endphp
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="block font-semibold text-gray-900 dark:text-white">{{ $item->kode_keberatan ?? '—' }}</span>
                                    {{-- Nomor permohonan yang dikeberatankan: berkasnya terpisah, tetapi
                                         pemohon mengenali perkaranya lewat nomor permohonan asalnya. --}}
                                    <span class="block text-xs text-gray-500 dark:text-gray-400">{{ __('atas') }} {{ $item->permohonan->kode_permohonan ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600 dark:text-gray-300 max-w-sm">
                                    <span class="block font-semibold text-gray-800 dark:text-gray-100">{{ __(\App\Models\KeberatanInformasi::JENIS[$item->jenis_keberatan] ?? $item->jenis_keberatan) }}</span>
                                    {{ \Illuminate\Support\Str::limit($item->kasus_posisi ?: $item->alasan_keberatan, 80) }}
                                    @if ($item->dikuasakan)
                                        <span class="mt-1 inline-flex px-2 py-0.5 rounded-full bg-emerald-50 text-[#10462F] text-[11px] font-bold">{{ __('Dikuasakan') }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ optional($item->tanggal_keberatan)->translatedFormat('d M Y') ?? '—' }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex px-3 py-1 rounded-full border text-xs font-semibold {{ $warnaStatus[$label] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">{{ __($label) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @forelse ($item->berkas as $berkas)
                                        <a href="{{ route('akun.keberatan.berkas', $berkas->id) }}" class="block text-sm text-[#E87317] hover:underline">{{ \Illuminate\Support\Str::limit($berkas->nama_file, 24) }}</a>
                                    @empty
                                        <span class="text-sm text-gray-400">—</span>
                                    @endforelse
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
