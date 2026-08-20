{{-- Lonceng notifikasi Portal Pemohon.

     Isinya umpan balik petugas atas pengajuan dan verifikasi data diri;
     barisnya ditulis api-ppid dan hanya dibaca di sini.

     Daftarnya ditarik lewat `fetch` berkala, bukan disisipkan saat halaman
     dirender: header ini tampil di semua halaman publik yang di-cache
     sebagiannya, dan pemohon kerap membuka portal lalu meninggalkannya terbuka
     sementara petugas memproses pengajuannya. --}}
<div class="relative"
     x-data="{
        buka: false,
        memuat: false,
        belumDibaca: 0,
        daftar: [],
        urlDaftar: '{{ route('akun.notifikasi.daftar') }}',
        urlTandaiSemua: '{{ route('akun.notifikasi.tandai-semua') }}',
        csrf: '{{ csrf_token() }}',

        async muat() {
            if (this.memuat) return;
            this.memuat = true;

            try {
                const res = await fetch(this.urlDaftar, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) return;

                const data = await res.json();
                this.belumDibaca = data.belum_dibaca ?? 0;
                this.daftar = data.daftar ?? [];
            } catch (e) {
                /* Jaringan putus: biarkan angka lama, coba lagi pada tarikan berikutnya. */
            } finally {
                this.memuat = false;
            }
        },

        /* Daftarnya dikosongkan di layar lebih dulu, permintaannya menyusul —
           loncengnya memang hanya memuat yang belum dibaca. */
        async tandaiSemua() {
            this.belumDibaca = 0;
            this.daftar = [];

            try {
                await fetch(this.urlTandaiSemua, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                });
            } catch (e) {}
        },

        /* Barisnya langsung dibuang dari daftar, bukan sekadar diberi tanda:
           yang sudah dibuka bukan pemberitahuan lagi. Riwayatnya tetap ada di
           halaman /akun/notifikasi.

           Penandaan dikirim tanpa ditunggu supaya pindah halamannya tidak
           tertahan jaringan; `keepalive` menjaga permintaannya tetap terkirim
           setelah halaman ditinggalkan. */
        buka_notifikasi(n) {
            this.daftar = this.daftar.filter(item => item.id !== n.id);
            this.belumDibaca = Math.max(0, this.belumDibaca - 1);

            try {
                fetch('{{ url('akun/notifikasi') }}/' + n.id + '/baca', {
                    method: 'POST',
                    keepalive: true,
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': this.csrf },
                });
            } catch (e) {}

            if (n.tautan) window.location = n.tautan;
        },
     }"
     x-init="
        muat();
        setInterval(() => { if (!document.hidden) muat(); }, 60000);
        document.addEventListener('visibilitychange', () => { if (!document.hidden) muat(); });
     "
     @click.outside="buka = false"
     @keydown.escape.window="buka = false">

    <button type="button" @click="buka = !buka; if (buka) muat()"
            aria-haspopup="true" :aria-expanded="buka ? 'true' : 'false'"
            aria-label="{{ __('Notifikasi') }}"
            class="hdr-ctl relative p-2.5 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
        </svg>
        <span x-show="belumDibaca > 0" x-cloak
              class="absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 flex items-center justify-center rounded-full bg-[#E87317] text-white text-[10px] font-bold leading-none ring-2 ring-white dark:ring-[#071A12]"
              x-text="belumDibaca > 9 ? '9+' : belumDibaca"></span>
    </button>

    <div x-show="buka" x-transition x-cloak
         class="absolute z-50 mt-1 w-[22rem] max-w-[calc(100vw-2rem)] rounded-xl shadow-xl bg-white ring-1 ring-black/5 right-0 overflow-hidden origin-top-right dark:bg-[#0A2619] dark:ring-white/10">

        <div class="flex items-center justify-between gap-2 px-4 py-3 border-b border-gray-100 dark:border-white/10">
            <p class="text-sm font-bold text-gray-900 dark:text-white">{{ __('Notifikasi') }}</p>
            <button type="button" @click="tandaiSemua()" x-show="belumDibaca > 0"
                    class="text-xs font-semibold text-[#10462F] hover:underline dark:text-[#3E9C6C]">
                {{ __('Tandai semua dibaca') }}
            </button>
        </div>

        <div class="max-h-[22rem] overflow-y-auto divide-y divide-gray-100 dark:divide-white/10">
            <template x-for="n in daftar" :key="n.id">
                {{-- Seluruh isi daftar pasti belum dibaca, jadi tidak ada lagi
                     dua gaya baris: yang tampil di sini semuanya baru. --}}
                <button type="button" @click="buka_notifikasi(n)"
                        class="w-full text-left px-4 py-3 flex gap-3 bg-emerald-50/40 transition-colors hover:bg-emerald-50/60 dark:bg-white/[0.04] dark:hover:bg-white/5">
                    <span class="mt-1.5 w-2 h-2 rounded-full flex-shrink-0"
                          :class="n.varian === 'warning' ? 'bg-[#E87317]' : 'bg-[#3E9C6C]'"></span>
                    <span class="min-w-0 flex-1">
                        <span class="block text-sm font-bold text-gray-900 dark:text-white" x-text="n.judul"></span>
                        <span class="block text-xs text-gray-600 mt-0.5 dark:text-gray-300" x-text="n.pesan"></span>
                        <span class="block text-[11px] text-gray-400 mt-1 dark:text-gray-500" x-text="n.waktu"></span>
                    </span>
                </button>
            </template>

            <p x-show="daftar.length === 0" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                {{ __('Tidak ada pemberitahuan baru.') }}
            </p>
        </div>

        <a href="{{ route('akun.notifikasi') }}"
           class="block px-4 py-3 text-center text-xs font-semibold text-[#10462F] border-t border-gray-100 hover:bg-emerald-50 transition dark:text-[#3E9C6C] dark:border-white/10 dark:hover:bg-white/5">
            {{ __('Lihat semua notifikasi') }}
        </a>
    </div>
</div>
