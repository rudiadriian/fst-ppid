{{-- Tombol aksi satu entri Daftar Informasi Dikecualikan.
     Sejajar dengan `partials/informasi_aksi` pada Daftar Informasi Publik.

     Dua tombol dengan urusan berbeda:
       - "Lihat / Unduh" membuka dialog dua pilihan atas informasinya sendiri —
         membaca lewat tautan yang diisikan petugas, atau mengajukan Permohonan
         Informasi untuk memperoleh salinannya.
       - "Surat Penetapan" adalah berkas keputusan pengecualiannya, dokumen yang
         berbeda dari informasi yang dikecualikan itu; berkasnya terbuka apa
         adanya, dan bila belum diunggah dikatakan apa adanya juga.

     Halaman yang memuatnya wajib ikut memuat `partials.dialog_lihat_unduh`
     satu kali. --}}
<div class="inline-flex flex-wrap items-center justify-center gap-2">
    <button type="button"
            @click="$dispatch('buka-dialog-lihat-unduh', {
                judul: @js($item['judul']),
                tautan: @js($item['tautan']),
                unduh: @js(route('ppid.request'))
            })"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white fs-gradient-accent hover:brightness-110 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        {{ __('Lihat / Unduh') }}
    </button>

    @if (!empty($item['file']))
        <a href="{{ $item['file'] }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#FAF6EC] dark:hover:bg-white/5 transition-colors duration-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            {{ __('Surat Penetapan') }}
        </a>
    @else
        <span class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium rounded-lg border border-dashed border-gray-200 dark:border-white/10 text-gray-500 dark:text-gray-400">
            {{ __('Belum tersedia') }}
        </span>
    @endif
</div>
