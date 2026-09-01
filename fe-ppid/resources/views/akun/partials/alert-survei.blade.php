{{-- Ajakan mengisi Survei Kepuasan untuk permohonan yang sudah tuntas.
     Hanya tampil di beranda portal: survei bersifat sukarela, jadi ia diletakkan
     di tempat yang memang dibuka pemohon, bukan dikejar lewat surel atau lonceng.

     Butuh $surveiTertunda — koleksi PermohonanInformasi tuntas yang belum dinilai. --}}
@if ($surveiTertunda->isNotEmpty())
    @php
        /* Tiga teratas saja; sisanya dirujuk lewat tautan ke daftar permohonan.
           Kartu yang memanjang mengikuti jumlah permohonan akan mendorong isi
           beranda yang lain sampai ke bawah layar. */
        $ditampilkan = $surveiTertunda->take(3);
        $sisa = $surveiTertunda->count() - $ditampilkan->count();
    @endphp

    <div class="p-5 rounded-2xl border bg-[#F3ECDD] border-[#E2D6BC] dark:bg-[#082217] dark:border-white/10">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div class="flex items-start gap-3">
                <span class="w-9 h-9 flex-shrink-0 rounded-xl flex items-center justify-center bg-[#E87317]/15 text-[#B4560F] dark:text-[#F0A860]">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.783-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/></svg>
                </span>
                <div>
                    <p class="text-sm font-bold text-gray-900 dark:text-white">
                        {{ trans_choice('{1}Satu permohonan Anda menunggu penilaian|[2,*]:jumlah permohonan Anda menunggu penilaian', $surveiTertunda->count(), ['jumlah' => $surveiTertunda->count()]) }}
                    </p>
                    <p class="text-sm mt-1 text-gray-700 dark:text-gray-300">
                        {{-- "Tuntas", bukan "selesai": permohonan yang ditolak pun
                             sudah selesai ditangani dan tetap boleh dinilai —
                             mutu layanannya justru penting diketahui di situ. --}}
                        {{ __('Permohonannya sudah tuntas ditangani. Penilaian Anda dipakai untuk memperbaiki mutu layanan informasi publik, dan mengisinya hanya butuh sebentar.') }}
                    </p>
                </div>
            </div>
        </div>

        <ul class="mt-4 space-y-2">
            @foreach ($ditampilkan as $item)
                <li class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 px-4 py-3">
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 dark:text-white">{{ $item->kode_permohonan }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ \Illuminate\Support\Str::limit($item->rincian_informasi, 90) }}</p>
                    </div>
                    <a href="{{ route('akun.survei.create', $item->id) }}" class="inline-flex items-center gap-1.5 px-4 py-2 fs-gradient-accent text-white text-sm font-bold rounded-xl hover:-translate-y-0.5 transition-transform">
                        {{ __('Isi Survei') }}
                    </a>
                </li>
            @endforeach
        </ul>

        @if ($sisa > 0)
            <a href="{{ route('akun.permohonan.index') }}" class="inline-block mt-3 text-sm font-semibold text-[#10462F] dark:text-[#3E9C6C] hover:underline">
                {{ __(':jumlah permohonan lain juga menunggu penilaian', ['jumlah' => $sisa]) }}
            </a>
        @endif
    </div>
@endif
