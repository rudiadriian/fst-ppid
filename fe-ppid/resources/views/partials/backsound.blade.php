{{-- Jingle Food Station — backsound situs publik (langkah 84).

     Tiga hal yang menentukan bentuk berkas ini:

     1. **Peramban memblokir suara yang menyala sendiri.** Chrome, Safari, dan
        Firefox menolak `play()` sebelum pengunjung pernah berinteraksi dengan
        situsnya. Jadi "berbunyi setiap kali halaman dibuka" tidak bisa
        dijanjikan pada kunjungan pertama — yang bisa: dicoba dulu, dan kalau
        ditolak, dijalankan pada sentuhan/klik/tekan tombol pertama.

     2. **Harus bisa dimatikan.** WCAG 2.1 kriteria 1.4.2 mewajibkan suara yang
        berbunyi otomatis lebih dari 3 detik punya cara menghentikannya. Tombol
        di pojok itulah caranya, dan pilihannya diingat di `localStorage`
        sehingga pengunjung yang mematikannya tidak perlu mematikannya lagi di
        setiap halaman.

     3. **Portal pengguna dikecualikan.** Halaman `/akun/*` adalah tempat orang
        mengetik permohonan dan data diri; musik latar di sana mengganggu
        pekerjaan, bukan menyambut tamu.

     Berkasnya MP4 (video + audio AAC). Elemen `<audio>` memainkan trek
     audionya saja; trek videonya ikut terunduh tapi tidak dipakai. Bila
     nantinya ada ffmpeg di mesin build, mengekstraknya ke .m4a akan memangkas
     unduhan dari ~970 KB menjadi puluhan KB. --}}

@php
    // Portal pengguna tidak diberi backsound (lihat alasan 3 di atas).
    $jinglePortal = request()->is('akun', 'akun/*');
@endphp

@unless ($jinglePortal)
    <div x-data="backsoundPpid()" x-init="mulai()" x-cloak
         class="fixed bottom-4 left-4 z-40">

        <audio x-ref="audio" preload="none" src="{{ asset('assets/audio/jingle-food-station.mp4') }}"></audio>

        <button type="button" @click="alih()"
                class="w-11 h-11 rounded-full fs-gradient text-white shadow-lg shadow-black/20 flex items-center justify-center hover:brightness-110 transition-all duration-200"
                :aria-label="menyala ? '{{ __('Matikan musik latar') }}' : '{{ __('Nyalakan musik latar') }}'"
                :title="menyala ? '{{ __('Matikan musik latar') }}' : '{{ __('Nyalakan musik latar') }}'">
            {{-- Ikon speaker menyala --}}
            <svg x-show="menyala" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.536 8.464a5 5 0 010 7.072M17.95 6.05a8 8 0 010 11.9M5 9v6h4l5 4V5L9 9H5z"></path>
            </svg>
            {{-- Ikon speaker mati --}}
            <svg x-show="!menyala" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5 9v6h4l5 4V5L9 9H5zM17 9l4 6m0-6l-4 6"></path>
            </svg>
        </button>
    </div>

    <script>
        function backsoundPpid() {
            const KUNCI = 'ppid.backsound';

            return {
                // Bawaannya menyala; yang pernah mematikannya tetap mati.
                menyala: localStorage.getItem(KUNCI) !== 'mati',

                mulai() {
                    if (!this.menyala) {
                        return;
                    }

                    this.putar();
                },

                putar() {
                    const audio = this.$refs.audio;
                    audio.volume = 0.35;

                    const janji = audio.play();

                    if (!janji) {
                        return;
                    }

                    janji.catch(() => {
                        /*
                         * Ditolak kebijakan autoplay. Dicoba lagi pada
                         * interaksi pertama — sekali saja, lalu penyimaknya
                         * dilepas supaya tidak menyala ulang setiap klik.
                         */
                        const sekaliJalan = () => {
                            if (this.menyala) {
                                audio.play().catch(() => {});
                            }

                            ['pointerdown', 'keydown', 'touchstart'].forEach(
                                (e) => window.removeEventListener(e, sekaliJalan)
                            );
                        };

                        ['pointerdown', 'keydown', 'touchstart'].forEach(
                            (e) => window.addEventListener(e, sekaliJalan, { once: false })
                        );
                    });
                },

                alih() {
                    this.menyala = !this.menyala;
                    localStorage.setItem(KUNCI, this.menyala ? 'nyala' : 'mati');

                    if (this.menyala) {
                        this.putar();
                    } else {
                        this.$refs.audio.pause();
                        this.$refs.audio.currentTime = 0;
                    }
                },
            };
        }
    </script>
@endunless
