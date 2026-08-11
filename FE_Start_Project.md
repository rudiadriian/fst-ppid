Tolong jalankan langkah untuk menyesuaikan/ modifikasi halaman frontend aplikasi ppid ini, jika langkah demi langkah sudah selesai, tolong berikan checklist pada setiap langkahnya, berikut ini langkahnya:
1. [x] tambahkan widget beserta feature untuk penyandang disabelitas mudah mengakses website ini, saya sudah mendaftarkan akun EQUAL WEB, berikut codenya :
    <!-- Accessibility Code for "ppid-fstj.vercel.app" -->
    <script>
    /*
    Want to customize your button? visit our documentation page:
    https://login.equalweb.com/custom-button
    */
    window.interdeal = {
        get sitekey (){ return "210e797aab2a9c0d254a9c1af498d48e"} ,
        get domains(){
            return {
                "js": "https://cdn.equalweb.com/",
                "acc": "https://access.equalweb.com/"
            }
        },
        "Position": "right",
        "Menulang": "ID",
        "draggable": true,
        "btnStyle": {
            "vPosition": [
                "50%",
                "80%"
            ],
            "margin": [
                "0",
                "0"
            ],
            "scale": [
                "0.8",
                "0.5"
            ],
            "color": {
                "main": "#2e850f",
                "second": "#ffffff"
            },
            "icon": {
                "outline": true,
                "outlineColor": "#ffffff",
                "type":  5 ,
                "shape": "circle"
            }
        },
                                "showTooltip": true,
        
    };

    (function(doc, head, body){
        var coreCall             = doc.createElement('script');
        coreCall.src             = interdeal.domains.js + 'core/5.3.1/accessibility.js';
        coreCall.defer           = true;
        coreCall.integrity       = 'sha512-3qLj5jbjMQnXk+FqEdVJjUnjJBGuBTRVOwaiT0ms6mQKQcrz4nulBxl2Hsr0/PpvEqdyJsMsU1NB+Mtfzw8hxA==';
        coreCall.crossOrigin     = 'anonymous';
        coreCall.setAttribute('data-cfasync', true );
        body? body.appendChild(coreCall) : head.appendChild(coreCall);
    })(document, document.head, document.body);
    </script>

2. [x] Tolong buat agar konsep thema warnanya mengikuti desain pada path D:\Project\Ppid\theme-color.jpeg dan D:\Project\Ppid\theme-color1.jpeg

---
## Status Pengerjaan

- **Langkah 1 — BELUM.** Halaman FE (`excluded_information`, `report`, `request_register`) sudah ada, tapi tabel DB-nya belum: tidak ada migration untuk `informasi_dikecualikan`, `laporan_informasi`, kolom consent di `permohonan_informasi`, maupun kolom kategori pembeda Dasar Hukum vs Regulasi di `regulasi`.
- **Langkah 2 — SELESAI.** Widget EqualWeb ada di `fe-ppid/resources/views/partials/accessibility.blade.php`, di-include dari `layouts/app.blade.php`. `Menulang` mengikuti locale aktif; warna tombol disesuaikan tema baru (`#E87317`).
- **Langkah 3 — SELESAI.** Palet warna diambil dari `theme-color.jpeg` + `theme-color1.jpeg`: hijau hutan pekat (struktur) + oranye (aksen/CTA) + krem (latar sesi).
  - `fe-ppid/tailwind.config.js` — token `brand` / `accent` / `cream`; skala `emerald`, `amber`, `green` di-override supaya kelas yang sudah dipakai ikut tema baru.
  - `fe-ppid/resources/css/app.css` — variabel `--color-fs-*` + utility `.fs-btn-cta`.
  - `fe-ppid/resources/views/layouts/app.blade.php` — `.fs-gradient` (hijau), `.fs-gradient-accent` (oranye), `.fs-gradient-text`, body jadi krem.
  - 20 file Blade — hex lama diganti token baru.
  - CTA utama (Permohonan, submit form, cek status) pakai gradient oranye.
  - `npm run build` sukses; 14 rute publik diverifikasi HTTP 200.

3. [x] Slidernya dibuat full seperti gambar pada path D:\Project\Ppid\theme-slider-beranda.jpeg, tidak hanya sebagian seperti sekarang pada halaman Home.
4. [x] bagian ini dihilangkan saja, jadi tampilannya lebih full dan clean seperti gambar acuan.
    <div class="lg:block bg-[#F3ECDD] text-gray-500 text-xs border-b border-gray-100 dark:bg-[#071A12] dark:text-white/50 dark:border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-9">
            <div class="flex items-center gap-5">
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#10462F] dark:text-[#3E9C6C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                    ppid@foodstation.co.id
                </span>
                <span class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#10462F] dark:text-[#3E9C6C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                    (021) 4718011
                </span>
            </div>
            <div class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#10462F] dark:text-[#3E9C6C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Senin–Jumat, 08.00–17.00 WIB
            </div>
        </div>
    </div>

5. [x] pada card Mulai Layanan dari Sini, Cukup : Permohonan Informasi – Pengajuan Keberan - Cek Status Tiket saja, CARD UNDUH DIHAPUS
6. [x] bagian ini dihilangkan/ dihapus saja :
    <section class="bg-white dark:bg-[#071A12]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mb-16 relative z-20">
            
            <div class="bg-[#08281B] rounded-3xl px-8 py-8 lg:px-14 lg:py-10 shadow-2xl shadow-emerald-950/40 flex flex-col items-center justify-center gap-6 overflow-hidden relative">
                <div class="absolute inset-0 fs-dot-pattern opacity-50"></div>
                <div class="relative z-10 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                    <a href="http://localhost:8000/permohonan" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-[#0B3524] text-sm font-bold rounded-xl hover:bg-emerald-50 transition-colors duration-200">Ajukan Permohonan</a>
                    <a href="http://localhost:8000/cek-status" class="inline-flex items-center justify-center px-6 py-3.5 bg-white/10 border border-white/30 text-white text-sm font-bold rounded-xl hover:bg-white/20 transition-colors duration-200">Cek Status Tiket</a>
                </div>
            </div>
        </div>
    </section>

7. [x] tolong ubah background warna pada card-card menjadi warna orange, jika card-cardnya banyak disetiap card dibuat warna orange tetapi dengan komposisi orange yang berbeda. seperti yang ada pada path ini D:\Project\Ppid\theme-color-card.png

    ---

    ## Status Pengerjaan (lanjutan)

    - **Langkah 3 (slider full) — BELUM DIKERJAKAN.** Tidak diminta pada sesi ini.
    - **Langkah 4 — SELESAI.** Top utility bar (email, telepon, jam layanan) dihapus dari `fe-ppid/resources/views/layouts/header.blade.php`. Header sekarang mulai langsung dari logo + navigasi. Alamat/telepon/email tetap tampil di footer dan di section Kontak halaman Home (sumber: `$cmsKontak` / `$contacts` dari CMS).
    - **Langkah 5 — SELESAI.** Judul `Butuh Informasi Publik?` + subjudul `Ajukan permohonan Anda secara resmi, transparan, dan gratis.` dihapus dari CTA strip di `layouts/footer.blade.php`. Strip disesuaikan jadi `justify-center` dengan padding lebih rapat supaya dua tombol (Ajukan Permohonan, Cek Status Tiket) tetap seimbang.
    - **Langkah 6 — SELESAI.** Card `Unduh Dokumen` dihapus dari array `$quickServices` di `ppid/home.blade.php`. Grid `lg:grid-cols-4` → `lg:grid-cols-3`, subjudul `Empat layanan utama…` → `Tiga layanan utama…`. Section `#dokumen` (daftar laporan + form unduh) tetap ada, hanya pintu masuk dari card cepat yang hilang.
    - **Housekeeping.** Kunci terjemahan yang tidak terpakai lagi dihapus dari `fe-ppid/lang/en.json` (`Senin–Jumat…`, `Butuh Informasi Publik?`, `Ajukan permohonan Anda secara resmi…`); kunci `Empat layanan utama…` diganti `Tiga layanan utama…`. JSON tervalidasi.
    - **Verifikasi.** `npm run build` sukses; halaman `/`, `/permohonan`, `/cek-status`, `/berita` HTTP 200; HTML Home dicek: 3 card layanan cepat, teks CTA lama sudah hilang, tidak ada top bar.

    ---

    ## Status Pengerjaan (putaran 3 — penomoran terbaru)

    - **Langkah 3 (slider full) — SELESAI.** Hero di `ppid/home.blade.php` diubah dari layout 2 kolom (teks kiri + kartu slider kecil kanan) jadi slider full-bleed: gambar mengisi seluruh lebar & tinggi hero, teks + CTA menumpuk di atasnya, mengikuti `theme-slider-beranda.jpeg`.
    - Tinggi hero `min-h-[560px]` (mobile) / `min-h-[660px]` (desktop).
    - Dua lapis scrim gelap (kiri→kanan dan atas→bawah) supaya teks tetap terbaca di atas gambar banner apa pun dari CMS.
    - Autoplay 6 detik, jeda saat kursor masuk (`@mouseenter`), tombol prev/next + dots hanya muncul jika slide > 1. Dot aktif berwarna oranye.
    - Slider sekarang tampil juga di mobile (sebelumnya `hidden lg:block`).
    - `HomeController@heroSlides` — fallback diubah dari "1 slide berisi logo" jadi array kosong; logo tidak layak di-stretch jadi background. Kalau CMS belum punya banner, hero jatuh ke gradasi hijau.
    - **Langkah 6 (hapus CTA strip) — SELESAI.** Seluruh `<section>` CTA strip di atas footer dihapus dari `layouts/footer.blade.php`. Padding footer `pt-32` → `pt-16` karena tidak ada lagi kartu yang menggantung (`-mb-16`). Tautan Ajukan Permohonan & Cek Status tetap ada di kolom "Layanan" footer.
    - **Langkah 7 (card oranye) — SELESAI.** Warna disampel langsung dari `theme-color-card.png`: `#FE6B17`, `#FD8B02`, `#FFA849`.
    - Kelas `.fs-card-1/2/3` (+ versi `.dark`) ditambahkan di `layouts/app.blade.php`.
    - Helper `$cardTier($i)` di-share global dari `AppServiceProvider` — memutar tiga nada itu sesuai indeks, jadi kartu berurutan tidak pernah senada.
    - Diterapkan pada: kartu Layanan Cepat, kartu Kategori Informasi Publik, kartu ringkasan statistik, 6 kartu Alur Permohonan, header kartu Laporan (Home); kartu ringkasan di halaman Laporan; kartu Kanal Layanan di Standar Layanan.
    - Ikon dibuat lingkaran putih dengan glyph oranye, teks putih — sama seperti gambar acuan.
    - **Tidak** diterapkan pada kartu berfoto/berthumbnail (Berita, Galeri, Susunan Pejabat), accordion FAQ, dan daftar kontak — latar oranye di sana menutupi gambar dan menurunkan keterbacaan teks panjang.
    - **Catatan kontras (perlu keputusan).** Teks putih di atas ketiga nada oranye acuan menghasilkan rasio 2,9:1 (`#FE6B17`), 2,6:1 (`#FD8B02`), dan 2,0:1 (`#FFA849`) — di bawah ambang WCAG AA 4,5:1 untuk teks normal. Ini bawaan desain acuannya, bukan bug. Kalau ingin lolos audit aksesibilitas (situs ini sudah memasang widget EqualWeb), cukup gelapkan nilai `.fs-card-*` di `layouts/app.blade.php`, misalnya `#C2490A` / `#B05F02` / `#9E6A15` — tampilan tetap oranye bertingkat tapi rasio naik ke ≥4,5:1.
    - **Verifikasi.** `npm run build` sukses. 12 rute publik HTTP 200. HTML dicek: Home 25 kartu ber-tier oranye, Standar Layanan 6, Laporan 11; nol `Undefined variable`/`Whoops`; `.fs-card-1 { background-color: #FE6B17; }` ikut ter-render; kelas `shadow-accent-200/60` & `border-accent-200` ter-compile.
8. [x] Disetiap section pada modul coba Judul/ Title label Textnya dibuat konsep berwarna kombinasi seperti konsep gambar D:\Project\Ppid\theme-color.jpeg
9. [x] Modul GALERI dihapus
10. [x] pada Modul Informasi Publik, Sub Modul Informasi Berkala, tolong dibuat dinamis yang nantinya akan dapat disesuaikan pada backend be-ppid. lalu nantinya karena ada tombol Selengkapnya yang berhubungan jika kita lihat pada link ke halaman yang lain, coba anda buatkan konsep seperti tersebut. Saat ini sudah saya coba input lewat backend tetapi tidak muncul di halaman FE modul informasi berkala. coba cek sekiranya seperti ini D:\Project\Ppid\INFORMASI BERKALA.xlsx. lalu Label "Informasi yang wajib disediakan dan diumumkan secara berkala, sekurang-kurangnya setiap 6 (enam) bulan sekali." dihapus; "Beranda / Informasi Publik / Informasi Berkala" bagian ini dihapus juga; "Dokumen & Arsip" label ini di ubah menjadi "Informasi Wajib Disediakan dan Diumumkan Secara Berkala". bagian Informasi Pengecualian Informasi yang dikecualikan (rahasia perusahaan, pribadi, dll.) tidak dapat diakses langsung di sini. Anda dapat mengajukan permohonan resmi melalui menu Permohonan Informasi Publik. " dihapus juga.
11. [x] pada statistik Pemohon, Dokumen, Regulasi, dan Kepuasan yang ada pada modul BERANDA, pindahkan konten ini ke Menu Pelayanan (Label Pelayanan diubah menjadi Layanan) - Laporan Statistik Informasi Publik. 
12. [x] pada Laporan Statistik Informasi Publik ini sudah dinamis mengikuti data real yang ada didatabase ya, coba anda cek kembali dan tolong dikonfigurasi agar integrasi dengan be-ppid.
13. [x] pada bagian footer informasi PERMOHONAN DIPROSES, PENGUNJUNG HARI INI, DAN KEPUASAN PEMOHON tolong dihapus
14. [x] "FE belum punya formulir survei untuk pemohon — nilai kepuasan sementara diinput lewat be-ppid. Mau formulir survei di FE (mis. setelah permohonan selesai), bilang." Iya, tolong dibuatkan tetapi setiap ada formulir harus diarahkan ke halaman login khusus untuk pengunjung (public dibedakan dengan portal untuk admin)
15. [x] Buatkan konsep AUTH sesuai standar untuk pengunjung user (bukan untuk admin) yang mana nantinya setiap USER PENGUNJUNG itu dapat melakukan pengisian FORMULIR harus login.
16. [x] Fitur check laporan juga harus login kalau mau check
17. [x] tolong hapus akses login untuk pegugas /login karena halaman login untuk petugas sudah dibuatkan pada be-ppid
18. [x] tolong infokan ke saya juga untuk username, password dan email pemohon untuk saya mencoba login.
19. [x] Pada setiap Formulir bagian Data Pemohon tidak perlu ditampilkan, dihapus saja karena nantinya otomatis data pemohon akan mengikuti ID penggunanya.
20. [x] Konsep AUTHnya tolong dibuatkan standarisasinya :
    - Email harus terverifikasi dulu, setelah itu baru bisa login, kalau tidak verifikasi, tidak bisa login.
    - Buatkan Halaman Khusus Pengguna dengan Header dan Footer yang tetap sama seperti public, tetapi body konten yang berbeda dari modul-modul lagi ketika pengguna berhasil login dengan menampilkan beberapa modul khusus pengguna terdaftar, adapun modul yang ada pada pengguna :
        - DASHBOARD
            - Munculkan Informasi terkait Statistik Permohonan Informasi dan Keberatan
            - Jika pengguna belum Verifikasi Data Pemohon, munculkan alert
            - Grafik Data Pengajuan, perbulan dengan legend status Permohonan "Dalam Proses, Revisi, Menunggu Persetujuan, Tolak, Selesai"
        - Permohonan Informasi
            - Tombol Tambah Pengajuan (Jika belum Verifikasi Data Pemohon, tombol akan Disable)
            - Harus SUDAH melakukan Verifikasi Data Pemohon, jika belum munculkan alert untuk mengalihkan ke modul Data Pemohon & Berkas
            - Kalau SUDAH, dapat melakukan pengajuan Permohonan Informasi
            - Tampilkan informasi Status "Dalam Proses, Revisi, Menunggu Persetujuan, Tolak, Selesai", Fitur Pencarian, Pagination, Sorting, Show data
            - Adapun Field pada Formulirnya :
                - Rincian Informasi
                - Tujuan Penggunaan Informasi
                - Cara Memperoleh Informasi (drodown)
                    - Melihat
                    - Membaca
                    - Mencatat
                    - Mendengar
                - Salinan Informasi Dibutuhkan
                    - Salinan Cetak
                    - Salinan Digital
                - Cara Mendapatkan Salinan Informasi, Jika Salinan Informasi Dibutuhkan dipilih :
                    - Salinan Cetak, hanya muncul pilihan Mengambil Langsung
                    - Salinan Digital, hanya muncul pilihan Salinan Digital (Email)
        - Permohonan Keberatan
            - Tombol Tambah Pengajuan
            - Tampilkan informasi Status "Dalam Proses, Revisi, Menunggu Persetujuan, Tolak, Selesai", Fitur Pencarian, Pagination, Sorting, Show data
            - Adapun Field pada Formulirnya :
                - Harus sudah melakukan Permohonan Informasi, jika belum tidak ada maka dari itu berikan Dropdown "Pilih Permohonan Informasi", jika ini belum dipilih field-field dibawah disable
                - Alasan Keberatan
                - Kasus Posisi Keberatan
                - Lampiran Dokumen Keberatan
                - Dikuasakan (Check Box = Ya)
        - Histori Permohonan
            - Menampilkan Histori secara Detail Pengajuan yang dilakukan oleh Pengguna
        - Pengaturan (Profile, Data Pemohon & Berkas, dan Ubah Passowrd)
            - Profile (Avatar, Username, Email dan Nomor Telepon)
            - Data Pemohon & Berkas - Formulir Verifikasi Data Pemohon (semua Wajib diisi)
                - Informasi Status Verifikasinya (Sudah verifikasi atau belum)
                - Jenis Pemohon (Perorangan, Mahasiswa, Lembaga/ Organisasi/ Perusahaan, Kelompok Orang)
                - Nama Pemohon (Otomatis terisi karena menyesuaikan registrasi User)
                - NIK/ Nomor Kartu Tanda Penduduk Pribadi (KTP)
                - Upload File KTP Pribadi (Maks 10 Mb)
                - Pekerjaan
                - Email (Otomatis terisi karena menyesuaikan registrasi User)
                - No. Telepon (Otomatis terisi karena menyesuaikan registrasi User)
                - Alamat 
            - Ubah Password (semua Wajib diisi dan sesuai standar)
                - Password Lama
                - Password Baru
                - Konfirmasi Password Baru
        - Logout
            - Jika berhasil Logout, kembali ke Beranda
21. [x] Pada Formulir Permohonan Informasi, ubah label pada checkbox ini "Saya bersedia pokok permohonan ini ditampilkan pada Register Permohonan publik (tanpa identitas saya)." menjadi "Saya menyetujui semua informasi yang saya berikan tentang Data Diri dan Permohonan Informasi ini adalah benar" 
22. [x] Diatas checkboxnya anda berikan EULA atau Ketentuan yang berlaku, dan sebelum pengguna membacanya checkbox tersebut tidak dapat di klik.
23. [x] Pada view Permohonan Informasi dan Permohonan Keberatan tambahkan nav tab status jadi untuk filterisasinya lebih mudah. 
24. [x] Saya ingin membuat BAGAN STRUKTUR ORGANISASI yang Dinamis, dikarenakan anda sudah membuatkan MODUL STRUKTUR ORGANISASI, adapun contoh konsepnya seperti gambar pada path ini D:\Project\Ppid\BAGAN STRUKTUR ORGANISASI PPID.png
25. [x] poin 24 sudah OK, tetapi hasilnya garisnya tidak menyatu dengan line dan entitas, hasilnya seperti digambar D:\Project\Ppid\Screenshot 2026-08-10 165501.png
26. [x] tolong Hapus section Arsip Resmi - Laporan & Dokumen pada modul BERANDA, setelah di hapus. tolong ubah background section Berita & Publikasi menjadi warna putih
27. [x] lebar card warna putih pada setiap MODUL/ SUB MODUL (Kecuali MODUL BERANDA) dibuat lebih lebar lagi ukurannya, karena yang sekarang telalu sempit, contohnya pada struktur organisasi saja ketika web diakses melalui PC/ Monitor tampilannya perlu di scroll supaya terlihat semua.
28. [x] Ketika formulir Permohonan Informasi dan Permohonan Keberatan sudah disubmit berikan notifikasi ke admin (be-ppid).
29. [x] Hapus sub Modul Dasar Hukum pada modul Profil. lalu ganti dengan sub modul Tugas, Fungsi dan Wewenang. tolong buatkan sub modulnya saja dulu dengan konsep dinamis pada be-ppid.
30. [x] Sesuaikan isi konten sub modul-modul pada modul Profil (kecuali STRUKTUR PPID -> GANTI NAMA LABELnya menjadi STRUKTUR ORGANISASI), SESUAIKAN isi kontennya pada path D:\Project\Ppid\Profil VISI MISI FUNGSI.docx
31. [x] Sesuaikan baik di Front End (fe-ppid) maupun di Back End (be-ppid) Modul dan Sub Mobul dengan struktur seperti ini : 
    - Infromasi Publik (Modul existing)
        - Daftar Informasi Publik
        - Daftar Informasi Dikecualikan
        - Informasi Berkala
        - Informasi Serta Merta
        - Informasi Tersedia Setiap Saat
        - Berita (Daftar berita, jika diberanda hanya berita terbaru saja yang muncul)
    - Standar Layanan
        - Maklumat Pelayanan
        - Prosedur Permohonan Informasi Publik (Konten Alur Permohonan Informasi pada beranda dipindahkan ke sub modul ini )
        - Prosedur Permohonan Keberatan Informasi Publik
        - Jalur dan Waktu Layanan
    - Layanan
        - Permohonan Informasi Publik
        - Pengajuan Keberatan Informasi Publik
        - Laporan Statistik Informasi Publik
        - Register Permohonan Informasi
        - Laporan Pelayanan Informasi
    Jika pada struktur modul maupun sub modul sudah sesuai dilewati saja.
32. [x] Hapus Konten Susunan Pejabat PPID pada sub modul Struktur Organisasi. lalu lengkapi setiap label yang bertuliskan Food Station menjadi PT Food Station Tjipinang Jaya (Perseroda)
33. [x] Untuk Modul Regulasi tolong disesuaikan :
    - file-file regulasinya ada di path D:\Project\Ppid\REGULASI contoh tampilannya ada di path D:\Project\Ppid\regulasi.png
    - Label "Regulasi dan Pedoman" diubah menjadi "REGULASI"
    - HAPUS Konten ini "Catatan Penting Dokumen regulasi di halaman ini tersedia untuk diunduh secara langsung (Setiap Saat). Jika membutuhkan dokumen regulasi spesifik lainnya, silakan gunakan menu Permohonan Informasi Publik."
34. [x] pada Back End (be-ppid) Modul Regulasi & Dasar Hukum disesuaikan:
    - diubah label namanya "Regulasi & Dasar Hukum" menjadi "Regulasi"
    - field Nomor dan Tahun di Hapus
    - Hanya bisa upload file PDF/ Gambar
35. sesuaikan Front End (fe-ppid) Modul Regulasi, 
    - [x] pada bagian dibawah ini :
        "sm:w-56 lg:w-64 flex-shrink-0 bg-[#FAF6EC] dark:bg-[#082217] border-b sm:border-b-0 sm:border-r border-gray-100 dark:border-white/10 flex flex-col items-center justify-center gap-3 py-8 px-6 "
        dibuat warna putih card saja, lalu menampilkan halaman pertama pada dokumen yang di upload BUKAN TAHUN DAN JUDUL 
    - [x] Label "DAFTAR PERATURAN" diubah menjadi "DAFTAR REGULASI"
    - [x] Fitur UNDUH dihapus, jadi haru masuk ke halaman DETAIL (setiap data regulasi dapat diKLIK yang nanti menampilkan informasi detail dokumennya, perlakukan juga tombol LIHAT seperti ini) 
    - [x] Halaman Detail itu Menampilkan Dokumennya, tidak pindah tab tapi tetapi disitu di lihatnya. lalu ada Section "Postingan Relevan" dibawahnya agar user dapat melihat REGULASI LAIN tanpa harus kembali ke Halaman REGULASI
    - pada DAFTAR REGULASI informasi yang ditampilkan adalah :
        - [x]  Gambar halaman awal dokumen
        - [x] bagian Internal PPID, disesuaikan dengan Kategori Regulasi
        - [x] bagian Nomor 4 Tahun 2023, disesuaikan dengan Tanggal Waktu Publish
        - [x] Judul Regulasi
        - [x] Ringkasan Regulasi
        - [x] Di upload oleh User Admin Siapa (Ikon Food Station - Diupload oleh - Nama User Admin )
36. 



---


## Status Pengerjaan (putaran 21 — langkah 35 lanjutan)

Butir yang belum tercentang pada langkah 35 dikerjakan di putaran ini: label daftar, halaman detail, dan isi kartu.

### Data baru yang dibutuhkan

Migrasi `api-ppid/database/migrations/2026_08_11_000001_add_ringkasan_and_uploader_to_regulasi_table.php` (**sudah dijalankan**) menambah dua kolom pada `regulasi`:

| Kolom | Isi |
|---|---|
| `ringkasan` | kutipan singkat yang tampil di daftar dan halaman detail |
| `uploaded_by` | petugas pengunggah (FK `users`, `nullOnDelete`) |

`uploaded_by` **diisi server dari token** lewat `beforeSave()` di `RegulasiController`, bukan dari input klien — nama yang tampil di situs publik pasti petugas yang benar-benar menyimpan. Baris lama diisi dengan akun admin yang ada. Modul CMS-nya dapat field **Ringkasan** serta kolom **Diunggah oleh** dan **Tanggal publikasi**.

### Daftar Regulasi (`/regulasi`)

- Judul daftar **"DAFTAR PERATURAN" → "DAFTAR REGULASI"**.
- **Tombol Unduh dihapus.** Seluruh kartu kini dapat diklik menuju halaman detail (tautan judul dilebarkan lewat `after:inset-0`, jadi area kliknya sekartu penuh); tombol **Lihat** ikut menuju halaman detail yang sama, bukan lagi membuka berkas di tab baru.
- Isi kartu sekarang: sampul halaman pertama dokumen, **badge kategori regulasi** (Dasar Hukum PPID / Regulasi / Pedoman — sebelumnya label lama "Internal PPID"), **tanggal dan jam publikasi** (menggantikan nomor peraturan), judul, **ringkasan**, dan baris **"Diunggah oleh <nama petugas>"** berikon logo perusahaan.
- Urutan daftar mengikuti tanggal publikasi terbaru.

### Halaman detail (`/regulasi/{id}`)

- Rute baru `ppid.regulation.show`; id di luar data menghasilkan 404.
- Dokumen **dibaca di halaman itu juga** — seluruh halaman PDF digambar berurutan ke `<canvas>` oleh `resources/js/sampul-pdf.js` (fungsi `gambarDokumen`), tidak membuka tab baru dan tidak memaksa unduh. Berkas gambar ditampilkan sebagai `<img>`.
- Bagian atas memuat kategori, tanggal + jam publikasi, judul, dan pengunggah; di bawahnya ringkasan, lalu dokumennya.
- Section **"Postingan Relevan"** berisi 6 regulasi lain — yang sekategori didahulukan, sisanya yang terbaru — lengkap dengan sampul halaman pertamanya, sehingga pengunjung bisa langsung pindah dokumen tanpa kembali ke daftar.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` di be-ppid tanpa error; `npm run build` sukses.
- `/regulasi` 200 dengan 9 kartu, **nol tombol "Unduh"**, 9 baris "Diunggah oleh", tautan detail per kartu (`/regulasi/17`, `/regulasi/15`, …).
- `/regulasi/17` 200: tangkapan layar memperlihatkan dokumen Perda Nomor 4 Tahun 2023 tergambar utuh di halaman (bukan tautan unduh), ringkasan tampil, dan 6 kartu "Postingan Relevan" ikut menggambar sampulnya.
- `/regulasi/99999` 404; beranda tetap 200. `lang/en.json`: +12 kunci (total 619).

**Catatan** — menjalankan ulang seeder regulasi mengembalikan kategori kedelapan dokumen ke `dasar_hukum_ppid`, sehingga badge-nya kini "Dasar Hukum PPID". Kalau Anda memang ingin ketiganya berkategori "Regulasi", ubah dari panel be-ppid; seeder hanya perlu dijalankan sekali dan tidak akan dijalankan lagi kecuali diminta.

---

## Status Pengerjaan (putaran 20 — langkah 34 & 35)

### Langkah 34 — modul Regulasi di be-ppid

- Label modul **"Regulasi & Dasar Hukum" → "Regulasi"** (`be-ppid/src/app/(control-panel)/ppid/lib/resources.ts`).
- **Field Nomor dan Tahun dihapus** dari formulir maupun kolom tabelnya. Kolomnya sendiri tetap ada di database supaya data lama tidak hilang; tabel CMS sekarang menampilkan Judul, Kategori, Jenis, dan Berkas. Karena kolom Tahun tidak ada lagi, urutan bawaannya pindah ke **judul** — ikut diubah di `api-ppid/app/Http/Controllers/Api/Cms/RegulasiController.php` supaya sisi server dan panel sepakat.
- **Unggahan dibatasi PDF dan gambar.** Ditambahkan jenis berkas baru `dokumen_gambar` di `api-ppid/app/Http/Controllers/Api/UploadController.php` (ekstensi `pdf, jpg, jpeg, png, webp`, batas 20 MB, divalidasi lewat `extensions:` + `mimes:` dan dicek ulang setelahnya). Sisi panel: `UploadField` memakai `accept=".pdf,image/jpeg,image/png,image/webp"`, dan tipe `jenis` di `types.ts` serta `ppidApi.upload()` diperluas. Field berkasnya diberi keterangan bahwa halaman pertamanya dipakai sebagai sampul di situs publik.

Penolakan berkas di luar daftar terjadi di server, bukan hanya di dialog pilih berkas — jadi ekstensi lain tetap ditolak walau atribut `accept` di-bypass.

### Langkah 35 — sampul kartu Regulasi di fe-ppid

Blok sampul (yang kelasnya Anda tunjuk) sekarang **berlatar putih** (`bg-white dark:bg-[#0B2A1D]`) dan isinya **halaman pertama dokumen**, bukan lagi tahun dan jenis peraturan.

**Cara menggambarnya** — `resources/js/sampul-pdf.js` (entri Vite terpisah, hanya dimuat di halaman Regulasi lewat `@push('scripts')` + `@stack('scripts')` yang baru ditambahkan di layout). Halaman pertama PDF digambar ke `<canvas>` memakai **pdf.js** (`pdfjs-dist`, dependensi baru). Berkas gambar (JPG/PNG/WEBP) langsung ditampilkan sebagai `<img>`.

Catatan teknis yang perlu diingat kalau nanti disentuh lagi:

- Awalnya PDF disematkan lewat `<object>`, tapi hasilnya bergantung pembaca PDF bawaan peramban — sebagian perangkat menampilkan kotak kosong. pdf.js dipilih supaya hasilnya seragam.
- Worker pdf.js **dijalankan di thread utama** (`globalThis.pdfjsWorker` + `workerSrc` kosong). Versi worker terpisah (`?worker` maupun `workerSrc`) membuat `getDocument().promise` menggantung tanpa galat — berkasnya terunduh penuh, tapi jawaban worker tidak pernah datang. Menggambar satu halaman pertama cukup ringan untuk thread utama.
- Berkas baru diunduh saat kartunya mendekati layar (cek `getBoundingClientRect()` pada muat, gulir, dan ubah ukuran), dan digambar satu per satu — halaman berisi banyak dokumen tidak menarik semua PDF sekaligus.
- Selama belum tergambar, atau bila berkas rusak/bukan PDF, yang tampil `partials/regulasi_sampul_cadangan.blade.php` (lambang dokumen + "Pratinjau tidak tersedia").

### Verifikasi

- `php -l` bersih di berkas PHP yang disentuh; `npx tsc --noEmit` di be-ppid tanpa error; `npm run build` di fe-ppid sukses (bundel `sampul-pdf` ±479 kB, hanya dimuat di halaman Regulasi).
- Halaman `/regulasi` diperiksa lewat peramban headless: **8 sampul PDF benar-benar tergambar** (halaman pertama, terbaca), satu baris tanpa berkas memakai sampul cadangan. Sempat gagal diam-diam sampai penyebabnya ketemu (worker menggantung) — bukti render sekarang berupa elemen `<canvas>` yang muncul di DOM.
- Beranda, `/informasi`, `/berita`, dan `/standar-layanan/prosedur-permohonan` tetap 200.
- `lang/en.json`: +2 kunci (total 607).

**Catatan** — kategori kedelapan dokumen di database saat ini terbaca `regulasi` (bukan `dasar_hukum_ppid` seperti saat diseed), sehingga badge-nya tampil "Internal PPID". Sepertinya sudah Anda ubah lewat panel; saya biarkan apa adanya. Bilang saja kalau mau dikembalikan ke Dasar Hukum PPID.

---

## Status Pengerjaan (putaran 19 — langkah 33)

### Langkah 33 — Modul Regulasi

**Delapan berkas dari folder REGULASI sudah masuk sistem.** PDF-nya disalin ke disk `media` (`fe-ppid/storage/app/public/uploads/regulasi/`) dengan nama rapi, lalu ditautkan lewat `api-ppid/database/seeders/RegulasiDasarHukumSeeder.php` (**sudah dijalankan**):

| Nomor | Jenis | Berkas |
|---|---|---|
| Nomor 14 Tahun 2008 | Undang-Undang | `uu-14-2008.pdf` |
| Nomor 25 Tahun 2009 | Undang-Undang | `uu-25-2009.pdf` |
| Nomor 1 Tahun 2021 | Peraturan Komisi Informasi | `perki-1-2021.pdf` |
| Nomor 1 Tahun 2003 | Peraturan Komisi Informasi | `perki-1-2003.pdf` |
| Nomor 175 Tahun 2016 | Peraturan Gubernur | `pergub-175-2016.pdf` |
| Nomor 61 Tahun 2010 | Peraturan Pemerintah | `pp-61-2010.pdf` |
| Nomor 54 Tahun 2017 | Peraturan Pemerintah | `pp-54-2017.pdf` |
| Nomor 4 Tahun 2023 | Peraturan Daerah | `perda-4-2023.pdf` |

Judul dan kategorinya (`dasar_hukum_ppid`) mengikuti `REGULASI/PPID_Dasar Hukum.docx`. Seeder-nya idempoten: dicocokkan lewat `file_path`, dan **baris data contoh lama yang membahas peraturan yang sama tetapi belum punya berkas dipakai ulang, bukan digandakan** — itulah kenapa UU 14/2008, PP 61/2010, dan Perki 1/2021 tidak muncul dobel. Seluruh isinya tetap bisa disunting dari be-ppid → Regulasi.

**Tampilan diganti jadi daftar kartu** (`resources/views/ppid/regulation.blade.php`), mengikuti contoh `regulasi.png`: sampul dokumen di kiri (lambang berkas + tahun + jenis peraturan), lalu badge kategori, nomor peraturan, judul, dan tombol **Unduh** + **Lihat**. Ada pencarian judul di sisi kanan judul daftar. Tabel lima kolom yang lama dilepas.

**Label** "Regulasi dan Pedoman" → **"REGULASI"** (judul halaman + `<title>`), dan tautan footer "Regulasi & Pedoman" ikut disederhanakan jadi "Regulasi".

**Blok "Catatan Penting"** beserta tautan ke Permohonan Informasi Publik **dihapus** dari halaman.

Perubahan kecil yang menyertai: `mapRegulasi()` kini mengirim `year` dan `jenis`; berkas yang kosong tidak lagi memberi tombol unduh menuju `#` melainkan keterangan "Belum tersedia".

**Verifikasi** — `php -l` bersih; `/regulasi` HTTP 200 dan memuat kedelapan tautan PDF; berkas `storage/uploads/regulasi/uu-14-2008.pdf` dilayani `200 application/pdf` 1.040.400 byte; tangkapan layar 1700px diperiksa (kartu, badge, tombol, judul "REGULASI", tanpa blok Catatan Penting); beranda dan pencarian tetap 200. `lang/en.json`: 11 kunci baru (total 605), JSON tervalidasi.

**Catatan** — satu baris data contoh lama, "Peraturan Direksi tentang Pedoman Keterbukaan Informasi Publik" (No. 12 Tahun 2023, kategori Regulasi), tidak punya berkas sehingga tampil dengan keterangan "Belum tersedia". Sengaja tidak saya hapus karena itu peraturan internal yang mungkin memang mau Anda unggah; bilang saja kalau mau dibuang.

---

## Status Pengerjaan (putaran 18 — langkah 29 & 30)

### Langkah 29 — Dasar Hukum diganti Tugas, Fungsi dan Wewenang

**Yang dihapus** — rute `/profile/dasar-hukum` (`ppid.legal_basis`), method `PpidController@showLegalBasisPage`, serta tautannya di menu Profil (desktop + ponsel) dan footer.

**Dokumennya tidak ikut hilang** — dua baris regulasi berkategori `dasar_hukum_ppid` sebelumnya hanya tayang di halaman itu. Halaman **Regulasi** sekarang memuat ketiga kategori (`regulasi`, `pedoman`, `dasar_hukum_ppid`), dan hasil pencarian untuk dasar hukum diarahkan ke sana. Tanpa ini, dua dokumen tersebut jadi tidak bisa dibuka publik.

**Sub modul barunya dinamis dari be-ppid** — `api-ppid/database/seeders/HalamanProfilSeeder.php` (**sudah dijalankan**) membuat satu baris di modul **Halaman Statis** dengan slug `tugas-fungsi-wewenang`, judul "Tugas, Fungsi dan Wewenang", isinya sudah terisi dari dokumen acuan. Operator tinggal membuka be-ppid → Halaman Statis → sunting isinya; situs publik langsung ikut berubah tanpa deploy. Seeder-nya idempoten dan **tidak menimpa** isi yang sudah disunting operator — baris yang sudah ada dilewati.

Rute `/profile/{slug}` yang lama sudah mendukung pola ini, jadi halaman baru tidak butuh rute sendiri. Bila baris CMS-nya dinonaktifkan atau database sedang bermasalah, halaman jatuh ke tata letak bawaan (kartu Fungsi + kartu Wewenang) yang ditulis di controller — halaman tidak pernah kosong.

**Perbaikan yang menyusul** — kelas `prose` yang dipakai untuk menampilkan isi CMS tidak menghasilkan gaya apa pun karena plugin `@tailwindcss/typography` memang tidak terpasang di proyek ini; judul dan daftar tampil rata tanpa penanda. Ditambahkan kelas `.fs-rte` di `resources/css/app.css` (heading, daftar bernomor/berbutir, tautan, kutipan, gambar, tabel, lengkap dengan mode gelap) dan dipakai di halaman Profil serta detail Berita.

### Langkah 30 — isi sub modul Profil mengikuti dokumen resmi

Sumber: `Profil VISI MISI FUNGSI.docx`.

| Sub modul | Perubahan |
|---|---|
| Profil Singkat | Judul isi jadi "Tentang PPID PT Food Station Tjipinang Jaya (Perseroda)", teksnya dipecah dua paragraf sesuai dokumen. Blok **Fungsi & Wewenang** dipindah ke sub modul baru. Blok **Waktu Layanan** tetap di sini (tidak dibahas dokumen, jadi tidak dibuang) |
| Struktur PPID | **Label diganti jadi "Struktur Organisasi"** di menu Profil, footer, judul halaman `/profile/struktur`, dan halaman `/struktur-ppid`. Isinya tidak diubah — dokumen tidak memuat bagian ini |
| Visi & Misi | Visi diganti dengan rumusan resmi ("Terwujudnya pelayanan informasi publik yang transparan…"); Misi jadi **4 butir** sesuai dokumen (sebelumnya 3 butir lama) |
| Tugas, Fungsi dan Wewenang | Baru. 1 butir Fungsi + 7 butir Wewenang, persis dokumen |

`lang/en.json`: 7 kunci mati dihapus, 11 kunci baru ditambahkan (total 557), JSON tervalidasi dan diurut.

### Verifikasi

- `php -l` bersih pada seluruh berkas PHP yang disentuh; `npm run build` sukses.
- Halaman dicek: `/profile/singkat`, `/profile/struktur`, `/profile/visi-misi`, `/profile/tugas-fungsi-wewenang`, `/struktur-ppid`, `/regulasi`, `/search?q=undang` semuanya **200**; `/profile/dasar-hukum` sekarang **404** sebagaimana mestinya.
- Isi terverifikasi di HTML hasil render: halaman baru menampilkan isi dari CMS (bukan cadangan controller), visi baru muncul di `/profile/visi-misi`, dan "Undang-Undang Nomor 14" kini tampil di `/regulasi`.
- Tangkapan layar ketiga halaman Profil diperiksa: judul, daftar berbutir, dan penomoran misi tampil benar setelah `.fs-rte` dipasang.
- Terjemahan diuji dengan mengganti bahasa ke EN: "Organizational Structure", "Duties, Functions and Authority", dan visi versi Inggris tampil; bahasa dikembalikan ke ID.

---

## Status Pengerjaan (putaran 17 — langkah 27)

### Langkah 27 — card modul dilebarkan

**Keputusan Anda**: lebar baru **1536px** (`max-w-screen-2xl`), dipakai halaman modul/sub-modul **plus** navbar dan footer; section beranda sengaja dibiarkan 1280px.

**Pemetaan lebar** — hanya pembungkus halaman (baris yang memang `mx-auto px-…`) yang diubah, sehingga lebar baca paragraf hero dan kartu formulir tidak ikut melar:

| Sebelum | Sesudah | Contoh halaman |
|---|---|---|
| `max-w-7xl` (1280) | `max-w-screen-2xl` (1536) | struktur, informasi publik, regulasi, laporan, berita, register permohonan, header, footer, layout portal akun |
| `max-w-6xl` (1152) | `max-w-screen-2xl` | standar layanan "Jalur & Waktu Layanan" |
| `max-w-5xl` (1024) | `max-w-7xl` (1280) | profil |
| `max-w-4xl` (896) | `max-w-6xl` (1152) | FAQ, detail berita, hasil pencarian, standar layanan lain |
| `max-w-3xl` (768) | `max-w-5xl` (1024) | cek status |

**Tidak diubah**: `resources/views/ppid/home.blade.php` (sesuai "kecuali MODUL BERANDA") dan kartu formulir sempit `max-w-md`/`max-w-lg` di halaman masuk, daftar, lupa/reset password, verifikasi email — kotak login selebar layar justru menyulitkan.

**Efek langsung** — halaman Struktur PPID di monitor 1920px sekarang memuat seluruh bagan tanpa digulir ke samping (keluhan pada butir ini), dan tabel Regulasi/Laporan punya ruang kolom lebih lega.

**Verifikasi** — `npm run build` sukses (`max-w-screen-2xl` masuk CSS hasil build). 12 halaman publik dicek: beranda, struktur, profil, informasi dikecualikan, regulasi, berita, FAQ, register permohonan, pencarian, masuk, daftar semua 200; `/permohonan` dan `/keberatan` 302 (memang wajib masuk). Tangkapan layar 1920px halaman Struktur dan Regulasi diperiksa: isi card sejajar dengan navbar dan footer, bagan utuh tanpa terpotong.

**Catatan** — karena beranda tetap 1280px sementara navbar sudah 1536px, isi beranda sekarang lebih menjorok ±128px dibanding logo di navbar. Itu konsekuensi pilihan "beranda tetap"; bilang saja kalau mau beranda ikut disamakan, satu kali ubah saja.

---

## Status Pengerjaan (putaran 16 — langkah 26 & 28)

### Langkah 28 — notifikasi ke admin saat formulir dikirim

Catatan penomoran: saat perintah dijalankan, butir ini masih bernomor 26. Nomor 26 kemudian Anda pakai untuk tugas beranda, jadi butir notifikasi dipindahkan ke nomor 28 — isinya tidak diubah.

**Jalur data** — situs publik dan panel admin memakai database yang sama (`ppiddb`), dan lonceng notifikasi be-ppid membaca tabel `notifikasi` lewat `GET /v1/notifikasi` di api-ppid. Jadi fe-ppid cukup menulis baris ke tabel itu; tidak ada endpoint baru, tidak ada antrean, tidak ada kunci API antar aplikasi.

**Yang ditambahkan di fe-ppid**

| Berkas | Isi |
|---|---|
| `app/Models/Notifikasi.php` | Model tabel `notifikasi` (tanpa `updated_at`, `data` di-cast array) |
| `app/Support/NotifikasiAdmin.php` | `permohonanBaru()` dan `keberatanBaru()` — menentukan penerima lalu menulis notifikasinya |

`PermohonanController@store` dan `KeberatanController@store` memanggilnya **setelah** transaksi commit, jadi notifikasi yang gagal ditulis tidak pernah membatalkan permohonan atau keberatan yang sudah tersimpan; galatnya hanya masuk log. Di `KeberatanController`, closure transaksi sekarang mengembalikan objek keberatannya supaya id-nya bisa ikut dibawa ke notifikasi.

**Penerimanya bukan semua pengguna** — hanya akun aktif (`is_active`, belum dihapus) yang rolenya punya hak `view` pada modul terkait: modul `permohonan` untuk permohonan informasi, modul `keberatan` untuk keberatan. Admin yang tidak menangani layanan tidak ikut kebanjiran, dan penambahan role baru otomatis terbawa tanpa mengubah kode.

**Isi notifikasinya** mengikuti kontrak panel Fuse: judul ("Permohonan informasi baru" / "Keberatan informasi baru"), pesan berisi kode permohonan + nama pemohon, ikon (`lucide:inbox` / `lucide:file-warning`), warna (`primary` / `warning`), dan tautan ke `/ppid/permohonan` atau `/ppid/keberatan`. Id permohonan/keberatan ikut disimpan di kolom `data` untuk keperluan nanti.

**Satu perubahan di be-ppid** — `useGetAllNotifications` diberi `refetchInterval: 60_000` (tidak berjalan saat tab tidak aktif). Permohonan masuk dari situs publik, bukan dari panel, jadi tanpa ini lonceng baru berubah setelah admin memuat ulang halaman.

**Verifikasi** — `php -l` bersih di empat berkas fe-ppid yang disentuh; `npx tsc --noEmit` di be-ppid tanpa error. Alur diuji sungguhan lewat controller-nya: kirim permohonan → 1 baris notifikasi untuk admin (`Permohonan PPID-FSTJ/… dari Pemohon Contoh menunggu ditangani.`); kirim keberatan → 1 baris `keberatan_baru`. Payload `GET /v1/notifikasi` untuk akun admin dicek dan sudah berbentuk Fuse (`{id, icon, title, description, time, read, link, useRouter, variant}`). Penjagaan "data pemohon belum diverifikasi" tetap jalan (pengiriman ditolak, tidak ada notifikasi). **Semua data uji sudah dihapus**: 1 permohonan uji, 1 keberatan uji, dan seluruh baris notifikasi; `status_verifikasi` pemohon demo dikembalikan ke `belum`.

### Langkah 26 — hapus section Arsip Resmi, latar Berita jadi putih

- Markup section **Arsip Resmi — Laporan & Dokumen** sudah tidak ada di `resources/views/ppid/home.blade.php` (terhapus di working tree Anda). Sisanya yang ikut mati saya bereskan: `HomeController` tidak lagi menyiapkan `arsipSlides` dan `reports`, tiga method privat (`arsipSlides()`, `laporanTerbaru()`, `ukuranBerkas()`) dan dua import (`Galeri`, `LaporanLayanan`) dihapus, komentar daftar variabel beranda ikut disesuaikan.
- Section **Berita & Publikasi** (`#berita`) latarnya berubah dari krem `bg-[#F3ECDD]` jadi `bg-white` (mode gelap `dark:bg-[#0B2A1D]`, sama dengan section putih lain di beranda).
- **Belum saya sentuh, perlu keputusan Anda**: modal unduh laporan di `home.blade.php` (baris ±374–509) beserta rute `report.download` — pemicunya dulu tombol di section Arsip Resmi, jadi sekarang tidak ada yang membukanya. Bilang saja kalau mau ikut dihapus; halaman `/laporan/{slug}` punya tautan unduh sendiri dan tidak memakai modal ini.
- **Verifikasi** — `php -l` bersih; beranda HTTP 200 dan tidak lagi memuat teks "Arsip Resmi"; HTML section berita keluar sebagai `class="py-16 lg:py-24 bg-white dark:bg-[#0B2A1D]"`; tangkapan layar beranda penuh dicek, urutannya Alur Permohonan → Berita & Publikasi → Bantuan/Kontak tanpa celah kosong bekas section.

---

## Status Pengerjaan (putaran 15 — langkah 25)

### Langkah 25 — garis bagan menyatu dengan kotak

Semua perbaikan ada di `fe-ppid/resources/views/partials/bagan_node.blade.php`.

**Penyebab** — kotak samping (Tim Pertimbangan) ikut dalam alur baris yang sama dengan kotak induk. Akibatnya: (a) baris berisi dua kotak lalu dipusatkan, sehingga kotak induk bergeser ke kiri dan tidak lagi segaris dengan panah serta kotak di bawahnya; (b) tinggi baris mengikuti kotak samping yang lebih tinggi, sehingga batang turun mulai jauh di bawah sisi kotak induk. Ditambah `mt-2` pada baris anak dan `<svg>` berperilaku inline (ada sisa ruang baseline di bawahnya), ujung panah pun tidak menyentuh kotak/bingkai berikutnya.

**Perbaikan**

- Kotak samping keluar dari alur: dibungkus `absolute top-0 left-full` di dalam pembungkus `relative` selebar kotaknya sendiri. Kolom utama kembali lurus dari atas ke bawah, tinggi baris tidak terkerek, dan lebar bagan tidak bertambah — kotak samping menumpang lebar bingkai grup, persis seperti gambar acuan.
- Garis putus-putus ke kotak samping dipasang di ketinggian kepala kotak (`mt-5`), menempel di sisi kanan kotak induk dan sisi kiri kotak samping.
- Batang turun langsung menempel di sisi bawah kotak (tanpa `mt-`/`gap`), dan `<svg>` panah diberi `block` supaya ujungnya benar-benar menyentuh kotak atau bingkai di bawahnya.
- Percabangan lebih dari satu anak sekarang punya garis penghubung sungguhan: batang turun → garis mendatar → satu batang + panah untuk tiap anak. Garis mendatar berhenti di titik tengah anak terkiri dan terkanan (`left-1/2 right-0` / `left-0 right-1/2`), tidak menjulur keluar. Sebelumnya hanya satu panah di tengah sementara anak-anaknya melayang tanpa garis.
- `flex-wrap` pada baris anak dan isi grup dilepas; di dalam wadah `min-w-max` pembungkusan tidak pernah terjadi, tapi bisa memutus garis kalau isian bagan bertambah.

**Verifikasi** — `npm run build` sukses (kelas baru `left-full`, `right-1/2`, `-translate-x-1/2` ada di CSS hasil build). `/struktur-ppid` dan `/profile/struktur` HTTP 200. Tangkapan layar peramban headless (1500px dan 420px) dicek: kotak `Atasan PPID`, `PPID`, dan bingkai `PPID Pelaksana` satu sumbu; kedua panah menyentuh kotak/bingkai; garis putus-putus menyatu di kedua ujung; seluruh bagan muat tanpa terpotong di layar lebar dan tetap digulir di dalam wadahnya di layar ponsel.

---

## Status Pengerjaan (putaran 14 — langkah 23 & 24)

### Langkah 23 — nav tab status pada Permohonan Informasi & Keberatan

- Partial baru `resources/views/akun/partials/tab-status.blade.php` — dipakai kedua modul, jadi satu tempat kalau labelnya berubah. Tab: **Semua**, **Dalam Proses**, **Revisi**, **Menunggu Persetujuan**, **Tolak**, **Selesai**, masing-masing dengan angka jumlah barisnya.
- Filter jalan di server, bukan menyembunyikan baris di browser: `?status=<label>` diterjemahkan jadi daftar status mentah lewat `PermohonanInformasi::statusKelompok()` / `KeberatanInformasi::statusKelompok()`, lalu `whereIn`. Nilai `status` di luar daftar diabaikan (jatuh ke tab Semua), jadi query string ngawur tidak bisa menembus filter.
- Tab, pencarian, pengurutan, dan jumlah baris per halaman saling menjaga: pindah tab tetap membawa `cari`/`urut`/`per` (`fullUrlWithQuery`), dan form pencarian menyimpan tab aktif lewat input tersembunyi. Pindah tab selalu balik ke halaman 1.
- Pesan kosong ikut menyesuaikan: "Tidak ada permohonan berstatus *Revisi*" berbeda dari "Belum ada permohonan".
- Jumlah per tab dihitung satu query `GROUP BY status` per modul, bukan satu query per tab.

### Langkah 24 — Bagan Struktur Organisasi dinamis

**Skema** — `api-ppid/database/migrations/2026_08_10_000003_add_bagan_to_struktur_organisasi_table.php` (**sudah dijalankan**) menambah tiga kolom pada `struktur_organisasi`:

| Kolom | Isi |
|---|---|
| `parent_id` | kotak induk (FK ke tabel yang sama, `nullOnDelete`); kosong = kotak paling atas |
| `tipe_node` | `utama` (kotak pada alur, panah ke induk), `samping` (di sisi induk, garis putus-putus), `grup` (bingkai berjudul yang membungkus anak-anaknya berjajar) |
| `poin` | daftar butir isi kotak, satu butir per baris (mis. isi Tim Pertimbangan PPID) |

**Penggambaran** — `StrukturOrganisasi::pohon()` menyusun daftar datar jadi pohon, memisah anak `alur` dan `samping`. Kotak yang induknya dinonaktifkan otomatis naik jadi akar supaya tidak hilang dari bagan. View-nya dua partial: `partials/bagan_struktur.blade.php` (bingkai + judul + area gulir) dan `partials/bagan_node.blade.php` (satu cabang, memanggil dirinya sendiri untuk anak-anaknya). Tampilannya mengikuti gambar acuan: kepala hijau berisi jabatan, badan abu berisi nama atau daftar butir, panah turun antar tingkat, garis putus-putus ke kotak samping, bingkai membulat untuk grup. Bagan digulir di dalam wadahnya sendiri (`overflow-x-auto`), halaman tidak ikut bergeser di ponsel.

**Dipasang di dua halaman**: `/struktur-ppid` (di atas daftar Susunan Pejabat) dan `/profile/struktur` — di halaman profil, diagram lama yang isinya kotak contoh dipaku (`PPID Utama`, `PPID Pembantu 1/2`, tombol unduh PDF yang menuju `#`) **dihapus** dan diganti bagan dari CMS.

**Bisa dikelola dari be-ppid** — modul Struktur Organisasi dapat tiga field baru: **Kotak induk** (relasi ke dirinya sendiri, label memakai jabatan), **Tipe kotak pada bagan** (pilihan utama/samping/grup, masing-masing dengan keterangan), dan **Butir isi kotak** (satu butir per baris). Tabelnya menampilkan kolom Induk dan Tipe kotak. Sisi API: `parent_id`, `tipe_node`, `poin` masuk `$fillable` + aturan validasi, relasi `parent()`/`children()` ditambahkan, dan `$withList = ['parent']` supaya kolom Induk terisi.

**Data contoh** — `api-ppid/database/seeders/BaganStrukturPpidSeeder.php` (idempoten, dicocokkan lewat `jabatan`) mengisi 7 kotak persis seperti gambar acuan. **Sudah dijalankan**; tabelnya tadinya kosong sehingga bagan tidak akan tampak apa-apa.

```
php artisan db:seed --class=BaganStrukturPpidSeeder
```

### Verifikasi

- Migrasi jalan; `php -l` bersih di fe-ppid & api-ppid; `npx tsc --noEmit` di be-ppid tanpa error; `npm run build` sukses.
- `/struktur-ppid` dan `/profile/struktur` HTTP 200. HTML memuat `Atasan PPID`, `Tim Pertimbangan PPID`, `PPID Pelaksana`, butir `Risk Management`, dan garis putus-putus (`border-dashed`); nol `Undefined variable`/`Whoops`.
- Tab status diuji dengan 3 baris contoh (`diajukan`, `selesai`, `revisi`): tab Semua 3 baris, Dalam Proses 1, Selesai 1, Revisi 1, Tolak 0. `?status=NgawurXYZ` tetap 200 dan jatuh ke tab Semua. Kombinasi `?status=…&cari=…` 200.
- 5 rute publik + 3 halaman portal 200, nol galat.
- `lang/en.json`: +7 kunci, 5 kunci mati dihapus (total 553), JSON tervalidasi.
- Tiga baris `UJI-TAB/*` yang dipakai menguji tab **sudah dihapus**.

### Catatan

- Tersisa satu permohonan atas nama akun demo (`PPID-FSTJ/20260810/0001`, rincian `a`, status `diajukan`) yang bukan buatan pengujian saya — sepertinya hasil Anda mencoba formulir lewat peramban. Sengaja **tidak** saya hapus; bilang saja kalau mau dibersihkan.
- Langkah 25 (notifikasi ke admin be-ppid saat formulir disubmit) belum dikerjakan — belum diminta pada sesi ini.

---

## Status Pengerjaan (putaran 13 — langkah 21 & 22)

### Langkah 21 — label checkbox diganti jadi pernyataan kebenaran data

- `resources/views/akun/permohonan/create.blade.php` — checkbox `tampil_di_register_publik` diganti `pernyataan_benar` dengan label **"Saya menyetujui semua informasi yang saya berikan tentang Data Diri dan Permohonan Informasi ini adalah benar"**.
- `PermohonanController@store` — aturan validasi `'pernyataan_benar' => ['accepted']`. Wajib, dijaga di server: permohonan tanpa pernyataan ditolak walau kotaknya dipaksa lewat request langsung.
- **Keputusan yang perlu Anda tahu.** Checkbox lama adalah izin menampilkan pokok permohonan di **Register Permohonan Informasi Publik** — bukan pernyataan kebenaran. Kalau checkbox itu hilang tanpa pengganti, tidak akan ada lagi permohonan baru yang masuk register publik (padahal register itu kewajiban Perki). Jadi: klausanya dipindah ke butir ketentuan yang wajib dibaca ("Pokok permohonan tanpa identitas pemohon dicatat pada Register Permohonan Informasi Publik…"), dan `tampil_di_register_publik` kini selalu `true`. Kalau Anda lebih suka register publik dimatikan untuk pengajuan dari portal, ubah satu baris di `PermohonanController@store` jadi `false` — bilang saja.

### Langkah 22 — EULA/ketentuan wajib dibaca sebelum checkbox aktif

- Panel **Ketentuan Layanan Permohonan Informasi Publik** ditaruh tepat di atas checkbox: kotak bergulir setinggi `max-h-56` dengan penanda status di kanan atas (`Gulir sampai akhir untuk membaca` → `Sudah dibaca`).
- Checkbox `:disabled="!sudahBaca"` — benar-benar tidak bisa diklik sebelum ketentuan dibaca, labelnya ikut redup.
- Tiga cara "dianggap sudah membaca", supaya tidak ada yang terkunci:
  1. menggulir kotak sampai ± akhir (`scrollTop + clientHeight >= scrollHeight - 8`);
  2. menekan tautan **"Saya sudah membaca ketentuan di atas"** — jalur untuk pengguna keyboard dan pembaca layar;
  3. otomatis, bila teks ketentuan lebih pendek dari kotaknya sehingga tidak ada yang perlu digulir (`x-init`).
  Kotaknya juga `tabindex="0"` supaya bisa digulir dengan keyboard.
- **Isi ketentuan** ada di `PermohonanController@ketentuan()`: 9 butir (dasar hukum UU 14/2008, kewajiban data benar, 10 hari kerja + perpanjangan 7 hari, informasi dikecualikan Pasal 17, biaya penggandaan, larangan penyalahgunaan, pencatatan di register publik, perlindungan data pribadi, hak keberatan 30 hari kerja). Bisa diganti tanpa menyentuh kode lewat Pengaturan Situs kunci **`layanan.ketentuan_permohonan`** (isi HTML); kalau kosong, teks bawaan yang dipakai.

### Verifikasi

- `php -l` bersih; `npm run build` sukses.
- `/akun/permohonan/baru` HTTP 200; HTML memuat label baru, judul panel ketentuan, `name="pernyataan_benar"`, atribut `:disabled="!sudahBaca"`; nol sisa `tampil_di_register_publik` di formulir; nol `Undefined variable`/`Whoops`.
- Kirim permohonan **tanpa** `pernyataan_benar` → ditolak, jumlah baris `permohonan_informasi` tidak bertambah. Dengan pernyataan → tersimpan, `tampil_di_register_publik=ya`, dan barisnya muncul di `/register-permohonan` (HTTP 200).
- `lang/en.json` +14 kunci, 1 kunci mati dihapus (total 551), JSON tervalidasi.
- Data uji dihapus lagi: `permohonan_informasi` kembali 3 baris.

---

## Status Pengerjaan (putaran 12 — langkah 19 & 20: Portal Pengguna)

### Perubahan skema (api-ppid)

`api-ppid/database/migrations/2026_08_10_000002_add_portal_pemohon_columns.php` — **sudah dijalankan**.

- `pemohon`: `foto`, `file_ktp`, `status_verifikasi` (`belum` / `menunggu` / `terverifikasi` / `ditolak`, bawaan `belum`), `tanggal_verifikasi`.
- `permohonan_informasi`: `cara_memperoleh` (`melihat` / `membaca` / `mencatat` / `mendengar`).
- `keberatan_informasi`: `kasus_posisi`, `dikuasakan`.
- CHECK constraint diperluas: jenis pemohon bertambah `perorangan`, `mahasiswa`, `lembaga` (nilai lama tetap diterima); status permohonan **dan** keberatan bertambah `revisi` (+ keberatan bertambah `menunggu_approval`, `ditolak`) supaya lima label status yang diminta bisa dipakai penuh.

### Langkah 19 — blok Data Pemohon dihapus dari formulir

Formulir pengajuan pindah seluruhnya ke Portal Pengguna dan **tidak lagi menanyakan identitas**: nama, NIK, alamat, telepon, dan email diambil dari akun yang sedang masuk.

- `resources/views/ppid/request_form.blade.php` dan `objection_form.blade.php` **dihapus**; `/permohonan` → `/akun/permohonan/baru` dan `/keberatan` → `/akun/keberatan/baru` (redirect bernama, jadi menu/bookmark lama tetap hidup).
- Method `showRequestForm`, `submitRequest`, `showObjectionForm`, `submitObjection`, dan helper `kodePermohonanBaru` dihapus dari `PpidController` — logikanya pindah ke controller portal.
- Formulir unduh laporan di Beranda sudah memakai data akun sejak putaran 11; tidak ada lagi formulir publik yang meminta identitas.

### Langkah 20 — standarisasi AUTH + Portal Pengguna

**1. Email wajib terverifikasi sebelum bisa masuk.**

- `config/ppid.php` → `wajib_verifikasi_email` bawaannya sekarang **true** (`PPID_WAJIB_VERIFIKASI_EMAIL`).
- Pendaftaran tidak lagi langsung masuk: akun dibuat, tautan verifikasi dikirim, pengguna diarahkan ke `/akun/verifikasi`.
- Login dengan email belum terverifikasi: sesi yang terlanjur dibuat langsung dibatalkan (`logout` + `session()->invalidate()`), lalu diarahkan ke halaman verifikasi yang bisa mengirim ulang tautan. Halaman itu kini bisa dibuka tanpa sesi login (emailnya dititipkan di session), dan tautan verifikasinya boleh dibuka di peramban mana pun — kuncinya tanda tangan URL + hash email, berlaku 60 menit.
- `.env` fe-ppid disetel `MAIL_MAILER=log` untuk lokal (mailpit tidak jalan): tautan verifikasi & reset password ditulis ke `storage/logs/laravel.log`. Ganti ke `smtp` setelah SMTP produksi siap.

**2. Halaman khusus pengguna.** `layouts/portal.blade.php` — header, footer, tema, dan widget aksesibilitas sama persis dengan halaman publik (`@extends('layouts.app')`); yang berbeda hanya isi body: menu modul di kiri, konten modul di kanan, plus kartu sapaan berisi avatar/nama/email.

| Modul | Route | Isi |
|---|---|---|
| Dashboard | `/akun` | Statistik permohonan & keberatan per status, alert bila Data Pemohon belum diverifikasi, grafik batang bertumpuk 12 bulan dengan legend 5 status |
| Permohonan Informasi | `/akun/permohonan` | Daftar + pencarian, sorting kolom, pagination, pilihan 10/25/50/100 baris; tombol Tambah Pengajuan; detail + jejak status |
| Permohonan Keberatan | `/akun/keberatan` | Daftar (fitur sama) + form dengan dropdown permohonan, lampiran, dikuasakan |
| Histori Permohonan | `/akun/histori` | Seluruh pengajuan beserta jejak perubahan statusnya |
| Pengaturan | `/akun/pengaturan/{profil,data-pemohon,password}` | Profil (avatar, username, email, telepon), Verifikasi Data Pemohon & Berkas, Ubah Password |
| Logout | `POST /akun/keluar` | Kembali ke Beranda |

**3. Aturan yang dijaga server (bukan sekadar disembunyikan di tampilan).**

- Tombol **Tambah Pengajuan** dimatikan bila Data Pemohon belum terverifikasi; membuka `/akun/permohonan/baru` langsung pun dialihkan ke Data Pemohon. `POST` juga menolak.
- Formulir permohonan: Salinan Cetak hanya memunculkan **Mengambil Langsung**, Salinan Digital hanya **Salinan Digital (Email)** — dan server memaksakan pasangan itu, bukan hanya Alpine.
- Formulir keberatan: seluruh field terkunci (`<fieldset :disabled>`) sampai permohonan dipilih; server memastikan permohonannya milik akun yang sama.
- Lampiran keberatan & berkas KTP hanya bisa diunduh pemiliknya (403 untuk akun lain). Unggahan dibatasi PDF/JPG/PNG maks 10 MB (avatar JPG/PNG maks 2 MB) dan disimpan dengan nama acak UUID di `storage/app/public/uploads/{ktp,keberatan,avatar}`.
- Data Pemohon yang diubah otomatis kembali berstatus `menunggu` — perubahan identitas harus diperiksa ulang petugas.
- Pencarian/urutan/pagination memakai daftar kolom yang diizinkan, tidak menerima nama kolom bebas dari query string.

**4. Label status** disatukan jadi lima kelompok yang diminta: `Dalam Proses`, `Revisi`, `Menunggu Persetujuan`, `Tolak`, `Selesai` (peta di `PermohonanInformasi::STATUS_LABEL` dan `KeberatanInformasi::STATUS_LABEL`). Grafik dashboard digambar dengan HTML/CSS biasa — tidak ada pustaka chart dari luar.

### Verifikasi

- Migrasi jalan; `php -l` bersih; `npm run build` sukses; `route:list --path=akun` 31 route.
- Uji ujung-ke-ujung (server lokal port 8125) dengan akun demo: kirim Data Pemohon + berkas KTP → status `menunggu`, `/akun/permohonan/baru` masih dialihkan → petugas set `terverifikasi` → form terbuka → permohonan tersimpan (`cara_memperoleh=membaca`, `format=softcopy`) → keberatan tersimpan dengan `dikuasakan=ya` + 1 lampiran → unduh lampiran 200 `application/pdf`, unduh KTP 200 → daftar dengan `?cari=&per=25&urut=status&arah=asc` 200 → dashboard menampilkan grafik + legend.
- Uji verifikasi email: daftar akun baru → dialihkan ke `/akun/verifikasi`, `/akun` masih tertutup; login sebelum verifikasi → dialihkan ke `/akun/verifikasi`; buka tautan dari `laravel.log` → berhasil; login sesudahnya → masuk ke `/akun`.
- Uji isolasi antar-akun: lampiran keberatan milik akun lain **403**, detail permohonan milik akun lain **404**.
- 8 rute publik + `/permohonan` & `/keberatan` (redirect ke portal) diperiksa; nol `Undefined variable`/`Whoops` di seluruh halaman portal.
- `lang/en.json` bertambah ~120 kunci (total 537), 5 kunci mati dihapus, JSON tervalidasi.
- **Data uji dibersihkan**: permohonan & keberatan uji beserta lampirannya dihapus, akun uji dihapus. Sisa: `permohonan_informasi` 3, `keberatan_informasi` 0, `survey_kepuasan` 0, `pemohon` 2.

### Catatan

- Akun demo `pemohon.demo@foodstation.co.id` / `Pemohon@2026` sengaja **dibiarkan berstatus terverifikasi** (dengan berkas KTP contoh) supaya bisa langsung mencoba pengajuan tanpa menunggu petugas.
- Persetujuan/penolakan Verifikasi Data Pemohon dilakukan petugas dari be-ppid dengan mengubah kolom `status_verifikasi` pada modul Pemohon. Kolomnya belum ditambahkan ke form modul itu — bilang saja kalau mau saya pasang.
- Status `revisi` sudah diterima database dan ditampilkan portal, tapi belum ada pilihannya di form status modul Permohonan/Keberatan pada be-ppid.

---

## Status Pengerjaan (putaran 11 — langkah 16, 17 & 18)

### Langkah 16 — cek laporan wajib login

Dua fitur yang cocok dengan "check laporan" dikunci login, keduanya memang formulir:

- **Cek Status Tiket** (`/cek-status`, `POST /check-status`) masuk grup `auth.pemohon` + `verified.pemohon`. Pengunjung yang belum masuk dialihkan ke `/akun/masuk`.
  - Hasil pencarian sekarang **dibatasi pada permohonan milik akun yang masuk**. Nomor registrasi milik orang lain dijawab `TIDAK DITEMUKAN`, jadi status permohonan orang tidak bisa diintip dengan menebak nomor.
  - Halamannya ternyata masih **palsu**: `statusMap` di Alpine berisi tiga nomor contoh yang di-hardcode dan tidak pernah memanggil server. Sekarang benar-benar `fetch` ke `/check-status`, memakai label status dari DB (`DIAJUKAN`, `DALAM PENELITIAN`, `DIPROSES`, `MENUNGGU PERSETUJUAN`, `DITERIMA`, `DITOLAK`, `DITOLAK SEBAGIAN`, `SELESAI`, `KEDALUWARSA`). Pewarnaan kartu status mengikuti tiga kelompok (baik / buruk / berjalan), dan tombol "Ajukan Keberatan" muncul untuk `DITOLAK` maupun `DITOLAK SEBAGIAN`.
- **Formulir Unduh Dokumen** (modal di Beranda, `POST /download-report`) ikut wajib masuk. Modal ini juga sebelumnya palsu (`setTimeout` 1 detik lalu "berhasil"), dan controller-nya mengirim tautan ke domain karangan `https://link-download-laporan-fstj.com/...`. Sekarang:
  - Kalau belum masuk, modal tidak menampilkan formulir — hanya ajakan Masuk/Daftar.
  - Kalau sudah masuk, nama & email diambil dari akun (tidak bisa diketik ulang), yang dikirim ke server hanya `laporan_id`. Server yang menentukan berkasnya, bukan judul dari browser.
  - Tautan yang dikirim ke email adalah **URL bertanda tangan** (`URL::temporarySignedRoute`) berlaku 72 jam menuju route baru `GET /unduh-laporan/{laporan}` (middleware `signed`). Route itu menyajikan berkas asli dari disk `public`, atau mengalihkan kalau CMS menyimpan URL penuh. Tanda tangan yang diubah sedikit pun ditolak 403.
  - `HomeController@laporanTerbaru` menambah `id` pada tiap kartu laporan supaya modal bisa mengirim `laporan_id`.
- **Halaman Laporan tetap publik.** `/laporan/statistik-informasi`, `/laporan/pelayanan-informasi`, dan tombol Unduh langsung di tabel halaman Laporan tidak dikunci — isinya informasi publik yang wajib diumumkan; yang dikunci adalah formulirnya.

### Langkah 17 — login petugas dihapus dari situs publik

Seluruh permukaan autentikasi Breeze di `fe-ppid` dihapus, bukan sekadar disembunyikan. `/login`, `/register`, `/dashboard`, `/profile` sekarang **404**.

Yang dihapus:
- `routes/auth.php` (beserta `require`-nya di `web.php`), route `/dashboard`.
- `app/Http/Controllers/Auth/` (7 controller), `app/Http/Requests/Auth/LoginRequest.php`, `app/Http/Controllers/ProfileController.php`, `app/Http/Requests/ProfileUpdateRequest.php`, `app/Models/User.php`.
- `resources/views/auth/`, `resources/views/profile/`, `resources/views/dashboard.blade.php`, `resources/views/layouts/guest.blade.php`, `resources/views/components/` (komponen Breeze — hanya dipakai view yang ikut dihapus; komponen email `x-mail::*` bukan bagian ini).
- Middleware `Authenticate` & `RedirectIfAuthenticated` beserta alias `auth`, `guest`, `auth.basic`, `auth.session`, `can`, `password.confirm`, `precognitive`, `verified` di `Kernel.php`.

Yang disesuaikan:
- `config/auth.php` — guard `web`, provider `users`, dan broker `users` dihapus; guard bawaan sekarang `pemohon`. Tidak ada lagi jalan masuk ke tabel `users` dari situs publik.
- `RouteServiceProvider::HOME` `/dashboard` → `/`.
- `AppServiceProvider` — closure URL notifikasi disederhanakan jadi khusus `Pemohon` (sebelumnya punya cabang ke route Breeze yang kini tidak ada).
- Tabel `users` **tidak disentuh** — itu milik `api-ppid`/`be-ppid` dan tetap dipakai panel admin.

### Langkah 18 — akun pemohon untuk uji coba

Seeder baru `api-ppid/database/seeders/PemohonDemoSeeder.php` (idempoten, dicocokkan lewat email) — **sudah dijalankan**:

```
php artisan db:seed --class=PemohonDemoSeeder
```

| | |
|---|---|
| Alamat login | `http://localhost:8000/akun/masuk` |
| Username | tidak ada username terpisah — **login memakai email** |
| Email | `pemohon.demo@foodstation.co.id` |
| Password | `Pemohon@2026` |

Emailnya sudah ditandai terverifikasi, jadi tetap bisa dipakai walau `PPID_WAJIB_VERIFIKASI_EMAIL` nanti dinyalakan. Ganti passwordnya lewat **Akun Saya → Data Diri → Ubah Password** kalau situs sudah dipakai sungguhan; ini akun contoh, jangan dibawa ke produksi.

Untuk mencoba **survei kepuasan**: masuk dengan akun di atas → Ajukan Permohonan → catat nomor registrasinya → ubah status permohonan itu jadi **Selesai** lewat modul Permohonan di be-ppid → tombol **Isi Survei** muncul di Akun Saya.

### Verifikasi

- `php -l` bersih; `npm run build` sukses; `php artisan route:list` — nol route `login`/`register`/`dashboard`/`profile edit`.
- `/login`, `/register`, `/dashboard` → **404**. `/permohonan`, `/keberatan`, `/cek-status`, `/akun` tanpa login → dialihkan ke `/akun/masuk`.
- Login akun demo berhasil → `/cek-status` 200 → kirim permohonan → cek status nomor sendiri mengembalikan `DIAJUKAN`; cek status nomor milik pemohon lain (`PPID-2026-000002`) mengembalikan `TIDAK DITEMUKAN`.
- `POST /download-report` tanpa login → 401 + `login_url`; sudah login tapi laporan tanpa berkas → 404 dengan pesan jelas. Uji berkas nyata: laporan diberi berkas sementara → tautan bertanda tangan mengunduh PDF (HTTP 200, `application/pdf`); tanda tangan diubah → 403; tanpa tanda tangan → 403.
- 16 rute publik HTTP 200, nol `Undefined variable`/`Whoops`.
- `lang/en.json` — 9 kunci baru, 4 kunci mati dihapus (total 424), JSON tervalidasi.
- **Data uji dibersihkan**: berkas PDF sementara dihapus, `file_laporan` laporan 1 dikembalikan kosong, permohonan uji dihapus. Sisa DB: `permohonan_informasi` 3, `survey_kepuasan` 0, `pemohon` 2 (1 lama + 1 akun demo yang memang diminta).

### Catatan

- Tautan unduh dikirim lewat email, jadi fiturnya baru terasa setelah `MAIL_*` benar. Selama `MAIL_HOST=mailpit` belum jalan, permintaan unduh menjawab 500 dengan pesan "Tautan gagal dikirim" dan kegagalannya tercatat di log.
- Tiga baris `laporan_layanan` yang terbit sekarang belum punya `file_laporan`, jadi kartu unduh di Beranda akan menjawab "Berkas laporan tidak ditemukan" sampai berkasnya diunggah lewat be-ppid.
- Tabel `permintaan_unduhan` ada di DB tapi belum dipakai — kolomnya mengarah ke `informasi_publik_files`, bukan `laporan_layanan`. Kalau riwayat permintaan unduh perlu dicatat, bilang saja.


---

## Status Pengerjaan (putaran 10 — langkah 14 & 15: akun pengunjung + formulir survei)

### Konsep AUTH pengunjung (langkah 15)

Akun pengunjung **terpisah penuh** dari akun petugas. Tidak ada satu pun kredensial yang dipakai bersama, jadi akun publik tidak pernah bisa membuka panel admin (`/dashboard` fe-ppid maupun be-ppid).

| | Pengunjung | Petugas/Admin |
|---|---|---|
| Guard | `pemohon` | `web` |
| Tabel akun | `pemohon` | `users` |
| Token reset | `password_reset_tokens_pemohon` | `password_reset_tokens` |
| Halaman | `/akun/masuk`, `/akun/daftar`, … | `/login`, `/register` (Breeze) |

- `fe-ppid/config/auth.php` — guard `pemohon`, provider `pemohon`, broker password `pemohon`.
- `api-ppid/database/migrations/2026_08_10_000001_create_password_reset_tokens_pemohon_table.php` — tabel token reset pengunjung; kolom `password` & `email_verified_at` pada `pemohon` dicek (sudah ada di skema). **Sudah dijalankan** (`php artisan migrate` di api-ppid).
- `fe-ppid/app/Models/Pemohon.php` — model akun, `implements MustVerifyEmail, CanResetPassword`, password auto-hash, relasi `permohonan()` & `keberatan()`.
- Middleware baru + alias di `app/Http/Kernel.php`: `auth.pemohon`, `guest.pemohon`, `verified.pemohon`.
- Controller di `app/Http/Controllers/Akun/`: `SessionController`, `RegisterController`, `PasswordResetController`, `EmailVerificationController`, `AkunController`, `SurveiController`. Route di `routes/akun.php`.
- **Pengaman yang dipasang:** CSRF (grup `web`), session regenerate saat masuk & invalidate saat keluar, batas percobaan login 5×/menit per email+IP (`LoginPemohonRequest`) plus throttle per route, aturan password `Password::defaults()->min(8)->letters()->numbers()`, tautan verifikasi bertanda tangan & kedaluwarsa 60 menit, jawaban "lupa password" selalu sama supaya email terdaftar tidak bisa ditebak, email tidak bisa diganti sendiri dari halaman Data Diri.
- **Klaim baris lama.** Kalau email yang didaftarkan sudah ada di tabel `pemohon` (dulu diinput petugas) dan belum punya password, baris itu dipakai ulang — riwayat permohonan lamanya tetap menempel. Kalau sudah punya password, pendaftaran ditolak dan diarahkan ke Masuk/Lupa Password. Status verifikasi dikosongkan saat klaim.
- **Verifikasi email.** Alurnya lengkap (kirim, kirim ulang, halaman notice, tautan bertanda tangan), tapi **penegakannya mati secara bawaan**: `config/ppid.php` → `PPID_WAJIB_VERIFIKASI_EMAIL=false`. Alasannya `MAIL_HOST=mailpit` belum jalan di lingkungan ini; kalau dipaksa wajib, tidak ada pengunjung yang bisa lanjut. Setelah SMTP produksi siap, cukup set `PPID_WAJIB_VERIFIKASI_EMAIL=true` — middleware `verified.pemohon` sudah terpasang di formulir dan survei. Selama mati, pengunjung yang belum verifikasi tetap melihat spanduk peringatan di Akun Saya.
- Halaman baru (`resources/views/akun/`): `login`, `register`, `forgot-password`, `reset-password`, `verify-email`, `dashboard` (Akun Saya), `profil` (Data Diri + ubah password), `survei`. Semua memakai `layouts.app` — tema, header/footer, dan widget aksesibilitas sama seperti halaman publik lain.
- Header dapat menu akun: tombol **Masuk** kalau belum login; avatar + dropdown (Akun Saya / Data Diri / Keluar) kalau sudah, versi desktop dan mobile.

### Formulir survei kepuasan (langkah 14)

- `/akun/survei/{permohonan}` — wajib login (`auth.pemohon`). Rating 1–5 (skala berlabel Sangat Tidak Puas … Sangat Puas, tetap radio asli supaya bisa dipakai pembaca layar) + komentar opsional.
- Tiga syarat yang dijaga server: permohonan harus milik akun yang sedang masuk (kalau bukan → 404), statusnya harus tuntas (`selesai`, `ditolak`, `ditolak_sebagian`; kalau belum → 403), dan satu permohonan hanya bisa dinilai sekali (kirim ulang dialihkan, tidak menambah baris).
- Pintu masuknya di **Akun Saya**: tiap baris riwayat permohonan menampilkan tombol **Isi Survei**, atau "Sudah dinilai — n/5", atau "Menunggu permohonan selesai".
- Nilai masuk ke tabel `survey_kepuasan` — sumber yang sama dengan kartu **Kepuasan** di `/laporan/statistik-informasi` dan modul Survei Kepuasan di be-ppid. Tidak ada tabel baru.

### Formulir lain ikut dikunci login — dan sekarang benar-benar tersimpan

Permintaan langkah 14 "setiap ada formulir harus diarahkan ke halaman login" berarti `/permohonan` dan `/keberatan` ikut di belakang `auth.pemohon`. Saat dikerjakan ketahuan **kedua formulir itu sebelumnya palsu**: Alpine hanya menunggu 1,5 detik lalu memunculkan nomor registrasi acak (`Math.random`), tidak ada satu baris pun masuk database. Tanpa permohonan nyata, survei tidak punya objek untuk dinilai, jadi keduanya disambungkan ke DB:

- **Permohonan** → `permohonan_informasi`. Nomor registrasi `PPID-FSTJ/<Ymd>/<urut 4 digit>` diambil dari nomor terbesar hari itu di dalam transaksi ber-`lockForUpdate`, jadi dua pengiriman bersamaan tidak dapat nomor kembar. Status awal `diajukan`, batas tanggapan +10 hari kerja (UU KIP). Identitas terisi otomatis dari akun dan data terbarunya ikut disimpan balik ke `pemohon`. Ada kotak persetujuan tampil di Register Permohonan publik (`tampil_di_register_publik`).
- **Keberatan** → `keberatan_informasi`. Nomor registrasi tidak lagi diketik bebas: pilihannya hanya permohonan milik akun sendiri. Nilai "alasan keberatan" disesuaikan dengan CHECK constraint DB (`permohonan_ditolak`, `informasi_tidak_disediakan`, …) — pilihan lama (`ditolak`, `tidak_disediakan`, …) akan ditolak database. Blok "Data Pemohon" tidak diketik ulang, diambil dari akun.
- **Cek Status Tiket** tetap terbuka untuk umum (bukan formulir isian), tapi datanya jadi nyata: dicari berdasarkan nomor registrasi di `permohonan_informasi`. Sebelumnya statusnya ditebak dari 4 digit terakhir nomor. Identitas pemohon tidak pernah ikut dikembalikan.
- Keberatan yang masuk bisa ditindaklanjuti petugas lewat modul **Keberatan** yang sudah ada di be-ppid.

### Verifikasi

- `php -l` bersih untuk seluruh berkas PHP baru/berubah; `npm run build` sukses; `php artisan route:list --path=akun` menampilkan 18 route.
- Uji ujung-ke-ujung dengan server lokal (`php artisan serve --port=8123`): daftar akun uji → `/permohonan` 200 → kirim permohonan → dapat `PPID-FSTJ/20260810/0001` di DB → kirim keberatan → cek status mengembalikan `DIAJUKAN` → survei ditolak 403 selagi status `diajukan` → status diubah `selesai` → survei terkirim (rating 5, tersimpan 1 baris) → kirim ulang tidak menambah baris → kartu Kepuasan di `/laporan/statistik-informasi` berubah jadi 100%.
- Uji pemisahan akses: `/permohonan`, `/keberatan`, `/akun` tanpa login → dialihkan ke `/akun/masuk`; survei atas permohonan milik akun lain → 404; keberatan atas nomor registrasi milik akun lain → 422; sesi pengunjung membuka `/dashboard` admin → dialihkan ke `/login` petugas. Login dengan password salah → kembali dengan galat; benar → masuk ke Akun Saya.
- Uji SMTP mati: `POST /akun/lupa-password` tidak error 500, tetap kembali dengan pesan netral (kegagalan kirim dicatat di log).
- **Data uji sudah dihapus lagi**: `survey_kepuasan` 0 baris, `permohonan_informasi` 3 baris, `pemohon` 1 baris, `keberatan_informasi` 0 baris — sama seperti sebelum pengujian.
- 14 rute publik dicek HTTP 200, nol `Undefined variable`/`Whoops`.
- `lang/en.json` bertambah 105 kunci terjemahan (total 419), JSON tervalidasi.

### Catatan untuk produksi

1. Setel `MAIL_*` yang benar, lalu nyalakan `PPID_WAJIB_VERIFIKASI_EMAIL=true` di `.env` fe-ppid.
2. Tabel `pemohon` belum punya UNIQUE pada `email`. Pendaftaran sudah mengecek duplikat di aplikasi, tapi kalau petugas menginput email yang sama dua kali lewat be-ppid, baris ganda bisa muncul. Kalau mau dikunci di level DB, perlu migrasi tambahan — bilang saja.
3. Unggahan lampiran pada formulir permohonan/keberatan belum ada (tabel `permohonan_files` & `keberatan_files` sudah tersedia). Belum diminta.

---

## Status Pengerjaan (putaran 9 — langkah 12 & 13)

### Langkah 12 — Laporan Statistik Informasi Publik: cek data & integrasi be-ppid

**Hasil pemeriksaan (sebelum diubah).** Halaman `/laporan/statistik-informasi` memang sudah memakai data nyata, tidak ada angka yang di-hardcode:

- `fe-ppid` dan `api-ppid` memakai database yang sama (`pgsql`, `ppiddb`, host `127.0.0.1:5432`), jadi apa pun yang disimpan lewat be-ppid langsung terbaca situs publik tanpa sinkronisasi tambahan.
- Tabel laporan diambil dari `laporan_layanan` (`status = published`, `tipe_laporan = statistik_informasi`), dikelola modul **Laporan Layanan** di be-ppid. Saat dicek berisi 3 baris nyata (2 statistik, 1 pelayanan).
- Kartu "Ringkasan Tahun" dihitung dari baris tahun terbaru, bukan angka tetap.
- Empat kartu "Angka Layanan Saat Ini" dihitung langsung: `permohonan_informasi` (Pemohon), `informasi_publik` terbit (Dokumen), `regulasi` (Regulasi), rata-rata `survey_kepuasan.rating` (Kepuasan).

**Dua celah yang ditemukan dan ditutup:**

1. **Angka rekap laporan harus diketik manual.** API sudah punya `GET /api/v1/laporan-layanan/rekap?tahun=…` (menghitung permohonan masuk/dikabulkan/ditolak/keberatan/rata-rata hari dari data permohonan asli), tapi belum ada yang memanggilnya dari be-ppid. Sekarang formulir Laporan Layanan punya tombol **Hitung otomatis**.
   - `be-ppid/.../ppid/lib/types.ts` — opsi baru `aksiIsiOtomatis` pada `ResourceConfig` (label, endpoint, param yang dikirim, field yang diisi, teks bantuan). Generik, bukan khusus satu modul.
   - `be-ppid/.../ppid/api/ppidApi.ts` — helper `ambil(path, params)` untuk endpoint non-CRUD ber-GET.
   - `be-ppid/.../ppid/components/ResourceFormDialog.tsx` — tombol muncul otomatis kalau modul punya `aksiIsiOtomatis`; menolak jalan kalau Tahun belum diisi; hasilnya mengisi enam field angka dan tetap bisa disunting sebelum disimpan.
   - Field Status diberi keterangan: situs publik hanya menampilkan laporan berstatus **Terbit**.
2. **Angka "Kepuasan" tidak punya sumber yang bisa dikelola.** Tabel `survey_kepuasan` ada, tapi tidak ada modul CMS maupun endpoint-nya, jadi kartu itu selamanya `—`. Sekarang ada modul **Survei Kepuasan**:
   - `api-ppid/app/Http/Controllers/Api/Cms/SurveyKepuasanController.php` + route `CrudRoute::register('survey-kepuasan', …, 'permohonan')`. Hak aksesnya menumpang modul `permohonan` (pola yang sama dipakai modul `pemohon`), jadi **tidak perlu** baris baru di `modul_sistem` maupun seeding hak akses ulang.
   - `be-ppid/.../ppid/lib/resources.ts` — modul `survey-kepuasan`: kolom Permohonan (kode), Rating, Komentar, Tanggal; form rating 1–5 + komentar + tautan ke permohonan (opsional).
   - `be-ppid/.../ppid/lib/navigation.ts` — masuk grup **Layanan Informasi**.
- **Verifikasi.** `php -l` bersih; `php artisan route:list` menampilkan 7 route `survey-kepuasan`. `npx tsc --noEmit` di be-ppid tanpa error; `eslint` file yang disentuh 0 error (sisa peringatan `prettier` milik baris lama). Uji ujung-ke-ujung: satu baris survei rating 4 disisipkan sementara → kartu Kepuasan berubah `—` → `80%`; baris uji **sudah dihapus lagi** (`survey_kepuasan` kembali 0 baris). Tabel laporan menampilkan 2 baris statistik dari DB.
- **Catatan.** Situs publik belum punya formulir survei untuk pemohon; pengisian nilai kepuasan untuk sementara lewat modul Survei Kepuasan di be-ppid. Kalau mau ada formulir survei di FE (mis. setelah permohonan selesai), bilang saja.

### Langkah 13 — hapus blok angka di footer

- `fe-ppid/resources/views/layouts/footer.blade.php` — grid tiga angka **Permohonan Diproses**, **Pengunjung Hari Ini**, dan **Kepuasan Pemohon** dihapus seluruhnya. Dua yang pertama memakai `Math.random()` dari Alpine (angka palsu yang berubah tiap 2 detik), yang ketiga `98.5%` di-hardcode. Pemisah `border-b` dan `pt-14` ikut disesuaikan supaya footer tidak menyisakan garis dan jarak menggantung.
- `lang/en.json` — tiga kunci terjemahan yang jadi mati dihapus. JSON tervalidasi.
- **Verifikasi.** `npm run build` sukses; 13 rute publik HTTP 200; HTML Beranda: nol sisa teks ketiga label dan nol `Math.random`, nol `Undefined variable`/`Whoops`.

---

## Status Pengerjaan (putaran 8 — langkah 11: statistik pindah ke Laporan Statistik)

- **Statistik dihapus dari Beranda.** Grid empat angka (Pemohon, Dokumen, Regulasi, Kepuasan) di atas section Alur Permohonan dihapus dari `resources/views/ppid/home.blade.php`. Method `HomeController@statistik` dan kunci `stats` dihapus; import `InformasiPublik`, `PermohonanInformasi`, `Regulasi`, dan facade `DB` ikut dibuang karena hanya method itu yang memakainya.
- **Statistik pindah ke Laporan Statistik Informasi Publik** (`/laporan/statistik-informasi`). Logikanya dipindah ke `PpidController@statistikRingkas()` (private), memakai pembungkus `fromDatabase()` milik controller itu supaya halaman tetap terbuka saat DB bermasalah (angka jadi `—`). Label `Permohonan` diganti `Pemohon` sesuai penamaan pada langkah 11.
- Controller mengisi `data['stats']` **hanya** untuk slug `statistik-informasi`; `/laporan/pelayanan-informasi` tidak menampilkannya.
- `resources/views/ppid/report.blade.php` — blok kartu ditaruh di atas "Ringkasan Tahun", judul kecil `Angka Layanan Saat Ini`, tetap memakai tiga nada oranye `$cardTier` dan gaya kartu yang sama seperti di Beranda dulu.
- **Label menu diubah.** `Pelayanan` → `Layanan` pada dropdown navigasi desktop; versi mobile yang tadinya `Standar Pelayanan` disamakan jadi `Layanan`. Judul kolom footer (`Standar Pelayanan`) dan eyebrow halaman Standar Layanan sengaja tidak diubah — itu nama halaman, bukan label menu.
- **Housekeeping.** `lang/en.json`: kunci `Pelayanan` (sudah tidak dipakai) dihapus; ditambahkan `Pemohon` dan `Angka Layanan Saat Ini`. Kunci `Layanan` sudah ada. JSON tervalidasi.
- **Verifikasi.** `php -l` dua controller bersih, `npm run build` sukses, `view:clear` dijalankan. `/laporan/statistik-informasi` HTTP 200 — empat kartu tampil (Pemohon 3, Dokumen 10, Regulasi 4, Kepuasan `—` karena `survey_kepuasan` masih kosong). `/laporan/pelayanan-informasi` 200 tanpa blok statistik. Beranda 200, nol kartu statistik tersisa, nol `Undefined variable`/`Whoops`; label menu `Layanan` ter-render di desktop dan mobile.
- **Catatan.** Di footer (`layouts/footer.blade.php`) masih ada blok angka terpisah: `Pengunjung Hari Ini` (angka acak dari Alpine, bukan data nyata) dan `Kepuasan Pemohon 98.5%` yang di-hardcode. Bukan bagian statistik yang diminta pindah, jadi dibiarkan — bilang saja kalau mau dihapus atau disambungkan ke data asli.

---

## Status Pengerjaan (putaran 7 — penyelesaian langkah 10: pembersihan tampilan)

Empat permintaan teks pada langkah 10 yang belum dikerjakan di putaran 5 & 6 sekarang selesai. Semuanya di `fe-ppid`.

- **Label deskripsi kategori dihapus.** Paragraf di bawah judul hero (`Informasi yang wajib diumumkan secara rutin dan teratur, sekurang-kurangnya setiap 6 (enam) bulan sekali.`) dihapus dari `resources/views/ppid/information.blade.php`. Kunci `description` ikut dihapus dari `PpidController@showPublicInformation` karena tidak dipakai lagi.
- **Breadcrumb dihapus.** Seluruh blok `<nav>` "Beranda / Informasi Publik / Informasi Berkala" dihapus. Karena breadcrumb-lah satu-satunya pemakai kategori induk, query `$induk` dan kunci `data['induk']` ikut dihapus dari controller — satu query DB lebih sedikit per request.
- **Judul tabel diganti.** `Dokumen & Arsip` → `Informasi Wajib Disediakan dan Diumumkan Secara Berkala`. Judulnya dibuat mengikuti kategori, bukan dipaku satu teks, supaya halaman lain tidak salah label:
  - `berkala` → `Informasi Wajib Disediakan dan Diumumkan Secara Berkala`
  - `serta-merta` → `Informasi Wajib Diumumkan Secara Serta Merta`
  - `setiap-saat` → `Informasi Wajib Tersedia Setiap Saat`
  - kategori lain (termasuk sub-kategori dari CMS) → memakai namanya sendiri.
  Peta ini ada di `$bawaan` pada controller, dikirim ke view lewat `data['heading_dokumen']`.
- **Kotak "Informasi Pengecualian" dihapus.** Blok peringatan kuning beserta tautan ke Permohonan Informasi Publik dihapus dari bawah tabel.
- **Housekeeping.** `lang/en.json`: kunci `Dokumen & Arsip`, `Informasi Pengecualian`, dan kalimat pengecualian dihapus; tiga judul tabel baru ditambahkan terjemahannya. JSON tervalidasi.
- **Verifikasi.** `php -l` controller bersih, `npm run build` sukses, `view:clear` dijalankan. `/informasi/berkala` HTTP 200 — 10 baris dengan 10 tombol "Selengkapnya", judul baru ter-render, nol sisa teks breadcrumb / pengecualian / `Dokumen & Arsip` / "sekurang-kurangnya", nol `Undefined variable`/`Whoops`. 15 rute publik lain (`/`, `/informasi/serta-merta`, `/informasi/setiap-saat`, `/informasi/dikecualikan`, `/permohonan`, `/keberatan`, `/cek-status`, `/berita`, `/faq`, `/regulasi`, `/struktur-ppid`, `/register-permohonan`, `/profile/visi-misi`, `/profile/dasar-hukum`, `/laporan/statistik-informasi`, `/standar-layanan/maklumat-pelayanan`) tetap 200.

---

## Status Pengerjaan (putaran 6 — perbaikan langkah 10)

**Kenapa data yang diinput lewat backend tidak muncul di FE — dua sebab, keduanya sudah ditangani.**

1. **Status masih `draft`.** Situs publik hanya menampilkan entri Informasi Publik berstatus `published`. Entri yang tersimpan (`Profil dan Sejarah Perusahaan`, id 2) berstatus `draft`, jadi tidak ikut terambil. Ini perilaku yang benar, bukan bug — form di be-ppid memang memakai `defaultValue: draft`. Sekarang field Status diberi keterangan: *"Situs publik hanya menampilkan entri berstatus Terbit."*
2. **Belum ada kolom untuk tautan.** Berdasarkan `INFORMASI BERKALA.xlsx`, tiap baris menunjuk ke halaman lain (kolom **Link**, mis. `https://foodstation.id/sejarah-perusahaan/`), bukan berkas unggahan. Tabel `informasi_publik` belum punya kolom itu, sehingga tidak ada cara menyimpan tautannya.

**Yang dikerjakan:**

- `api-ppid/database/migrations/2026_08_07_000001_add_tautan_to_informasi_publik_table.php` — kolom `tautan` (varchar 500, nullable) pada `informasi_publik`. Sudah dijalankan (`php artisan migrate`).
- `api-ppid/app/Models/InformasiPublik.php` — `tautan` masuk `$fillable`.
- `api-ppid/.../Cms/InformasiPublikController.php` — validasi `tautan` => `nullable|url|max:500`.
- `be-ppid/.../ppid/lib/resources.ts` — field **Tautan halaman** pada form Informasi Publik, plus keterangan pada field Status dan Lampiran supaya jelas kapan pakai tautan dan kapan pakai berkas.
- `fe-ppid/.../PpidController.php` — tombol aksi memakai berkas bila ada; kalau tidak ada, memakai `tautan`. Jenisnya ikut dikirim ke view.
- `fe-ppid/.../ppid/information.blade.php` — tombol aksi jadi tiga keadaan: **Selengkapnya** (oranye, ikon tautan keluar) untuk entri bertautan, **Lihat** untuk entri berkas, dan teks *Belum tersedia* bila keduanya kosong.
- `api-ppid/database/seeders/InformasiBerkalaSeeder.php` — 10 entri dari `INFORMASI BERKALA.xlsx` (judul + link), status `published`, kategori `berkala`. Idempoten (dicocokkan lewat `slug`), jadi aman dijalankan ulang:

  ```
  php artisan db:seed --class=InformasiBerkalaSeeder
  ```

  Seeder ini **sudah dijalankan**. Entri `Profil dan Sejarah Perusahaan` yang tadinya `draft` ikut diperbarui jadi `published` + diisi tautannya.

- **Catatan urutan.** Tabel diurutkan `tanggal_publikasi` menurun lalu judul menaik, jadi nomor urutnya tidak persis sama dengan nomor di Excel. Tidak ada kolom urutan pada `informasi_publik`. Kalau urutan Excel harus dipertahankan, perlu tambahan kolom `urutan` — bilang saja.
- **Verifikasi.** `/informasi/berkala` HTTP 200 menampilkan 10 baris dengan 10 tombol "Selengkapnya" yang menunjuk ke 6 URL foodstation.id sesuai Excel; nol `Undefined variable`/`Whoops`. 12 rute publik lain tetap 200.

---

## Status Pengerjaan (putaran 5)

- **Langkah 10 (Informasi Berkala bertingkat) — SELESAI.**
  - **Catatan soal acuan:** halaman `ppid.jiep.co.id` itu SPA Nuxt — HTML-nya kosong, isinya baru dimuat JavaScript dari API internal mereka, jadi strukturnya tidak bisa dibaca langsung. Konsep di bawah dibangun dari deskripsi permintaan (konten dinamis dari CMS + tombol "Selengkapnya" yang menautkan ke halaman lain) memakai design system situs ini.
  - **Konsepnya:** kategori informasi jadi **pohon**. Halaman kategori (`/informasi/{slug}`) kini menampilkan kartu untuk setiap sub-kategorinya, masing-masing dengan nama, ringkasan deskripsi, jumlah dokumen terbit, dan tombol **Selengkapnya** yang menuju halaman sub-kategori itu. Kedalamannya bebas — sub-kategori boleh punya sub-kategori lagi.
  - `PpidController@showPublicInformation` — mengambil `children` kategori aktif (urut `urutan`, lalu `nama`) beserta `withCount` dokumen berstatus `published`, plus kategori induk untuk breadcrumb.
  - `resources/views/ppid/information.blade.php` — ditulis ulang: breadcrumb (Beranda / Informasi Publik / Induk / Kategori), grid kartu sub-kategori dengan tiga nada oranye bergantian (`$cardTier`), lalu tabel dokumen.
  - Tabel dokumen **disembunyikan** kalau halaman itu murni pengantar (punya sub-kategori tapi belum punya dokumen sendiri) — supaya tidak muncul tabel kosong. Kalau kategori punya keduanya, kartu dan tabel tampil berdampingan.
  - Kotak pencarian dokumen yang sebelumnya hanya hiasan sekarang berfungsi (filter baris via Alpine `x-model`).
  - Kolom ringkasan dokumen ikut ditampilkan di bawah judul kalau diisi di CMS.
  - `partials.db_notice` ditambahkan supaya halaman ini ikut memberi tahu saat backend tidak terjangkau.
  - **Tanpa perubahan backend.** Tabel `kategori_informasi` sudah punya `parent_id`, dan modul Kategori Informasi di be-ppid sudah punya field "Kategori induk". Yang ditambahkan hanya teks bantuan di `be-ppid/src/app/(control-panel)/ppid/lib/resources.ts` supaya admin paham efek `parent_id` dan `deskripsi` terhadap tampilan situs.
  - **Cara memakainya di be-ppid:** buat Kategori Informasi baru → isi *Kategori induk* = "Informasi Berkala" → isi *Deskripsi* (jadi ringkasan di kartu) → simpan. Lalu di modul Informasi Publik, arahkan dokumen ke kategori anak tersebut. Halaman `/informasi/berkala` otomatis menampilkan kartunya.
  - **Belum ada datanya.** DB saat ini baru berisi 3 kategori induk (`berkala`, `serta-merta`, `setiap-saat`) tanpa anak, jadi `/informasi/berkala` masih menampilkan tabel dokumen seperti sebelumnya. Kartu "Selengkapnya" baru muncul setelah sub-kategori dibuat di CMS. Tidak ada data contoh yang ditulis ke DB — bilang saja kalau mau di-seed.
- **Verifikasi.** `npm run build` sukses. 12 rute publik HTTP 200, nol `Undefined variable`/`Whoops`. View diuji render dengan 3 sub-kategori contoh: 3 kartu ber-tier oranye muncul, tombol "Selengkapnya" ada, tabel dokumen otomatis tersembunyi. Dengan data DB asli (tanpa anak): breadcrumb tampil, tabel dokumen tampil, filter pencarian aktif.

---

## Status Pengerjaan (putaran 4)

- **Langkah 8 (judul dua warna) — SELESAI.** Konsep dari `theme-color.jpeg`: satu judul dipecah dua warna — sebagian kata warna dasar, sisanya oranye.
  - Helper `$judulDua($teks, $kataAksen = 1, $kelas = 'fs-title-accent')` di-share global dari `AppServiceProvider`. Memecah judul per kata, memberi warna aksen pada N kata terakhir, dan selalu menyisakan minimal satu kata pada warna dasar. Judul satu kata jadi aksen seluruhnya. Output di-escape lalu dibungkus `HtmlString`.
  - Dua kelas warna di `layouts/app.blade.php`: `.fs-title-accent` (`#E87317`, untuk judul di atas latar terang; mode gelap otomatis jadi `#F5A94C`) dan `.fs-title-accent-soft` (`#F5A94C`, untuk judul di atas latar hijau/hero).
  - Diterapkan pada 35 judul: H1 hero di 14 halaman + H2 section di Home (7), Profil (4), Standar Layanan, Informasi, Berita Lainnya, FAQ (judul kategori), Cek Status, serta judul blok formulir Permohonan (3) dan Keberatan (2).
  - Hero Home dibuat manual karena mengandung `<br>`: baris 1 putih, baris 2 (`Informasi Publik`) oranye.
  - Judul ber-`&` (`Berita & Publikasi`, `Laporan & Dokumen`, `Dokumen & Arsip`) diberi `kataAksen = 2` supaya `&` tidak menggantung sendirian di sisi warna dasar.
  - Tidak diterapkan pada judul kartu/isi (judul berita, nama pejabat, judul item) — itu data, bukan label section.
- **Langkah 9 (hapus modul Galeri) — SELESAI.**
  - `routes/web.php` — route `ppid.gallery` dihapus.
  - `KontenController` — method `galeri()` dan import `App\Models\Galeri` dihapus; docblock kelas disesuaikan.
  - `resources/views/ppid/gallery.blade.php` dihapus.
  - Tautan menu Galeri dihapus dari `layouts/header.blade.php` (desktop + mobile) dan `layouts/footer.blade.php`.
  - Tidak ada kunci terjemahan khusus Galeri di `lang/en.json`, jadi tidak ada yang perlu dibersihkan.
  - **Yang sengaja dibiarkan:** model `App\Models\Galeri` dan tabel `galeri` masih dipakai `HomeController@arsipSlides` — slider gambar di section "Laporan & Dokumen" Home mengambil 3 foto galeri terbaru. Kalau modul Galeri mau hilang total (termasuk slider ini dan modul CMS-nya di be-ppid), bilang saja.
- **Verifikasi.** `npm run build` sukses. `/galeri` sekarang 404; 14 rute publik lain HTTP 200. HTML dicek: nol `Undefined variable`/`Whoops`, nol tautan "Galeri", judul dua warna ter-render benar (`Berita <span class="fs-title-accent">&amp; Publikasi</span>`, `Informasi <span class="fs-title-accent-soft">Publik</span>`, dst).
