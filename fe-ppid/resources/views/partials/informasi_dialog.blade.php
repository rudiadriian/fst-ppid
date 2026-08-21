{{-- Dialog dua pilihan untuk entri Daftar Informasi Publik (langkah 83).

     Satu dialog untuk seluruh tabel, bukan satu per baris: isinya diganti oleh
     event `buka-dialog-dokumen` yang dikirim tombol barisnya. Kalau tiap baris
     membawa dialognya sendiri, halaman berisi 24 dokumen akan mencetak 24
     salinan markup yang sama.

     Dimuat sekali per halaman, berdampingan dengan `partials.informasi_aksi`. --}}
<div x-data="{
        terbuka: false,
        judul: '',
        pratinjau: null,
        unduh: null,
        buka(d) {
            this.judul = d.judul;
            this.pratinjau = d.pratinjau;
            this.unduh = d.unduh;
            this.terbuka = true;
        }
     }"
     @buka-dialog-dokumen.window="buka($event.detail)"
     @keydown.escape.window="terbuka = false"
     x-cloak>

    <div x-show="terbuka" x-transition.opacity
         class="fixed inset-0 z-50 flex items-center justify-center p-4"
         role="dialog" aria-modal="true" :aria-label="judul">

        <div class="absolute inset-0 bg-black/50" @click="terbuka = false"></div>

        <div x-show="terbuka" x-transition
             class="relative w-full max-w-lg rounded-2xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 shadow-2xl p-6 sm:p-7">

            <div class="flex items-start justify-between gap-4 mb-2">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white leading-snug" x-text="judul"></h3>
                <button type="button" @click="terbuka = false"
                        class="flex-shrink-0 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                        aria-label="{{ __('Tutup') }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <p class="text-sm font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-6">
                {{ __('Dokumen ini dapat dibaca langsung tanpa masuk. Untuk memperoleh salinannya, diperlukan permohonan informasi yang disetujui petugas PPID.') }}
            </p>

            <div class="flex flex-col gap-3">
                <template x-if="pratinjau">
                    <a :href="pratinjau" target="_blank" rel="noopener"
                       @click="terbuka = false"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold rounded-xl border border-gray-200 dark:border-white/20 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-white/5 transition-colors duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        {{ __('Di Lihat Saja') }}
                    </a>
                </template>

                {{-- Tautan bacanya belum diisi petugas. --}}
                <template x-if="!pratinjau">
                    <p class="text-center text-sm font-normal text-gray-400 dark:text-gray-500">
                        {{ __('Tautan untuk dibaca belum tersedia.') }}
                    </p>
                </template>

                <template x-if="unduh">
                    <a :href="unduh"
                       class="inline-flex items-center justify-center gap-2 px-5 py-3 text-sm font-bold rounded-xl text-white fs-btn-cta hover:brightness-110 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                        {{ __('Unduh Dokumen') }}
                    </a>
                </template>

                {{-- Berkas salinannya belum diunggah petugas. Dikatakan apa
                     adanya, daripada memasang tombol yang berujung 404. --}}
                <template x-if="!unduh">
                    <p class="text-center text-sm font-normal text-gray-400 dark:text-gray-500">
                        {{ __('Salinan untuk diunduh belum tersedia.') }}
                    </p>
                </template>
            </div>
        </div>
    </div>
</div>
