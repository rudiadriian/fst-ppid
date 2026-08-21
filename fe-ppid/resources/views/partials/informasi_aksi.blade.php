{{-- Tombol aksi satu entri Daftar Informasi Publik.

     Dipakai keempat daftar: indeks seluruh dokumen, Informasi Berkala, Serta
     Merta, dan Setiap Saat. Halaman yang memuatnya wajib ikut memuat
     `partials.informasi_dialog` satu kali — di sanalah dialognya berada.

     Jenisnya ditentukan `PpidController::aksiDokumen()`, bukan di sini, supaya
     tidak ada salinan kedua dari aturan yang sama. --}}
@if ($item['jenis'] === 'dialog')
    <button type="button"
            @click="$dispatch('buka-dialog-dokumen', {
                judul: @js(__($item['name'])),
                pratinjau: @js($item['pratinjau']),
                unduh: @js($item['unduh'])
            })"
            class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg text-white fs-gradient-accent hover:brightness-110 transition-all duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
        {{ __('Lihat') }}
    </button>
@else
    <a href="{{ route('ppid.request') }}"
       class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-semibold rounded-lg border border-gray-200 dark:border-white/10 text-gray-700 dark:text-gray-200 hover:bg-[#FAF6EC] dark:hover:bg-white/5 transition-colors duration-200">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
        {{ __('Mohon Dokumen') }}
    </a>
@endif
