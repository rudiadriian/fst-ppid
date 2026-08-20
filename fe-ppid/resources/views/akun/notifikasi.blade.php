@extends('layouts.portal')

@section('title', __('Notifikasi') . ' | ' . __('PPID FSTJ'))
@section('portal-judul', __('Notifikasi'))

@section('portal')

    {{-- Halaman penuh untuk riwayat yang tidak muat di lonceng header.
         Barisnya ditandai sudah dibaca lewat tautannya sendiri, jadi halaman
         ini tetap berguna walau JavaScript-nya gagal dimuat. --}}
    <div class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden">

        <div class="px-6 py-4 border-b border-gray-100 dark:border-white/10 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Pemberitahuan dari Petugas') }}</h2>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    {{ __('Perubahan status pengajuan, berkas tanggapan, dan hasil verifikasi data diri.') }}
                </p>
            </div>

            @if ($notifikasi->where('is_read', false)->isNotEmpty())
                <form method="POST" action="{{ route('akun.notifikasi.baca-semua') }}">
                    @csrf
                    <button type="submit" class="px-4 py-2.5 rounded-xl border border-gray-200 dark:border-white/10 text-sm font-semibold text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5">
                        {{ __('Tandai semua dibaca') }}
                    </button>
                </form>
            @endif
        </div>

        @if ($notifikasi->isEmpty())
            <p class="px-6 py-12 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('Belum ada pemberitahuan dari petugas.') }}
            </p>
        @else
            <ul class="divide-y divide-gray-100 dark:divide-white/10">
                @foreach ($notifikasi as $item)
                    @php $isi = $item->untukLonceng(); @endphp
                    <li class="{{ $isi['dibaca'] ? '' : 'bg-emerald-50/40 dark:bg-white/[0.04]' }}">
                        <a href="{{ route('akun.notifikasi.buka', $item->id) }}"
                           class="flex gap-3 px-6 py-4 transition-colors hover:bg-emerald-50/60 dark:hover:bg-white/5">
                            <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0 {{ $isi['dibaca'] ? 'bg-transparent' : ($isi['varian'] === 'warning' ? 'bg-[#E87317]' : 'bg-[#3E9C6C]') }}"></span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm text-gray-900 dark:text-white {{ $isi['dibaca'] ? 'font-semibold' : 'font-bold' }}">{{ $isi['judul'] }}</span>
                                <span class="block text-xs text-gray-600 dark:text-gray-300 mt-0.5">{{ $isi['pesan'] }}</span>
                                <span class="block text-[11px] text-gray-400 dark:text-gray-500 mt-1">{{ $isi['waktu'] }}</span>
                            </span>
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="px-6 py-4 border-t border-gray-100 dark:border-white/10">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    {{ __('Menampilkan') }} {{ $notifikasi->firstItem() }}–{{ $notifikasi->lastItem() }} {{ __('dari') }} {{ $notifikasi->total() }} {{ __('data') }}
                </p>
                {{ $notifikasi->links() }}
            </div>
        @endif
    </div>

@endsection
