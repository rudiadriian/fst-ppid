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
36. [x] Pada modul Informasi Publik, tolong anda sesuaikan:
    - Isi konten pada sub Modul Daftar Informasi Publik berbentuk Table atau mungkin anda ada ide lain untuk menampilkannya agar lebih rapi, modern, responsive dan informatif yang isinya pada path D:\Project\Ppid\DAFTAR INFORMASI PUBLIK PT FOOD STATION TJIPINANG JAYA.docx.pdf buatkan konsep di back-end (be-ppid)nya juga
    - Isi konten pada sub Modul Daftar Informasi Dikecualikan berbentuk Table atau mungkin anda ada ide lain untuk menampilkannya agar lebih rapi, modern, responsive dan informatif yang isinya pada path D:\Project\Ppid\DAFTAR INFORMASI DIKECUALIKAN PT FOOD STATION TJIPINANG JAYA.docx.pdf
37. [x] sub Modul Informasi Dikecualikan tolong hapus informasi Alasan Pengecualian, Dasar Hukum, Jangka Waktu, dan Tanggal Penetapan. jadikan opsional pada backend (be-ppid) pada field-field ini, jadi tidak perlu di isi
38. [x] Perbaiki widget perubahaan Bahasa Indonesia - Inggris masih belum sempurna dan Ikon Bendera Bahasa Indonesia dan Inggris juga belum tampil
39. [x] Tampilan Daftar Informasi Dikecualikan samakan konsep penyajian informasinya seperti Daftar Informasi Publik, lalu hapus label ini "Entri yang dokumennya belum tersedia untuk diunduh tetap dapat dimohonkan melalui menu Permohonan Informasi Publik." dan label ini "Catatan Penting Pengecualian informasi ditetapkan melalui uji konsekuensi sesuai Pasal 17 UU No. 14 Tahun 2008 dan bersifat sementara sesuai jangka waktu yang ditetapkan. Keberatan atas penetapan ini dapat diajukan melalui menu Pengajuan Keberatan." 
40. [x] Modul Layanan dan sub Modul Register Permohonan Informasi bagian ini adalah halaman untuk Registrasi Akun untuk bisa melakukan permohonan informasi
41. [x] Modul Layanan dan sub Modul Laporan Pelayanan Informasi bagian ini kontennya adalah berupa Laporan Pelayanan Informasi per tahun dalam bentuk PDF yang di upload oleh Admin, Konsepnya Ui/Ux sama seperti MODUL REGULASI, buatkan konsep Backend (be-ppid)nya juga untuk upload file Laporan Pelayanan Informasi
42. [x] pada backend tolong sesuaikan penamaan modul (jangan terlalu panjang).
43. [x] pada backend fitur translate Bahasa Indonesia - Inggris (dan sebaliknya) masih belum bisa.
44. [x] Ikon bendera pda fitur translate, ukurannya tidak ideal terlalu besar.
45. [x] Buatkan konsep summary, analisa, dan SLA yang sekiranya dapat menjadi referensi tindakan, laporan serta penilaian KPI
46. [x] Sesuaikan Modul-modul yang memang berfungsi untuk pengelolaan informasi, management (user, modul, roles, settings, audit log dan jika ada yang perlu ditambahkan silahkan) dan disesuaikan pada front end (fe-ppid)
47. [x] Modul Analitik & SLA dipindahkan saja kontennya ke Modul DASHBOARD jadi yang diakses secara garis besar hanya modul dashboard saja tapi informasi yang didapat sudah ada analisis juga.
48. [x] pada Modul Dasboard susunan informasinya seperti ini :
    - CARD (Total permohonan, menunggu persetujuan, lewat batas waktu, keberatan belum selesai)
    - CARD (Informasi Publik, Berita, Kepuasan Pemohon)
    - CARD Perlu tindakan segera (bagian ini perlu di highlight)
    - CARD Kepatuhan SLA
    - CARD Capaian KPI
    - CARD Permohonan masuk vs ditanggapi (dibuat summary perbulan saja, lalu nanti ada comparasi berbading dengan maksimal 3 tahun kebelakang), disebelahnya CARD Sebaran status dan Kategori paling diminta
    - CARD Informasi Publik, Berita, dan Kepuasan Pemohon (DI HAPUS)
49. [x] pada Modul MAKLUMAT bagian ini adalah tampilan pdf/ gambar yang terview langsung isinya di frontend, di backend konsepnya adalah upload dokumen. filenya ada di path D:\Project\Ppid\MAKLUMAT PPID.png
50. [x] pada banner di modul BERANDA tolong dibuat full (tinggi bannernya kurang maksimal) agar tampilannya lebih clean, karena saat ini seperti di file ini D:\Project\Ppid\Screenshot 2026-08-13 105621.png, adapun contoh tampilannya seperti ini D:\Project\Ppid\Screenshot 2026-08-13 105813.png. infokan juga gambar ukuran resolusi gambar yang ideal jika nantinya gambar di upload dari backend
51. [x] pada HEADER saya ingin headernya transparan ketika belum discroll kebawah, ketika discroll kebawah headernya menjadi putih.
52. [x] saya ingin banner ini bisa multi gambar, caption dan ringkasan pada gambar dibannernya. lalu, ada transisi animasi ketika slider berjalan. tolong hapus angka 01/02 dipojok kanan bawah bener dan tidak perlu input Judul bahasa inggris karena nanti akan otomatis translate ya?
53. [x] pda modul Profil Singkat, tolong hapus bagian Waktu Layanan Informasi Publik karena ini sudah difasilitasi informasinya di modul lain.
54. [x] Hapus modul Tautan dan Modul Sistem
55. [x] Hapus Fitur CRUD pada modul Pemohon
56. [x] Anda belum membuat Konsep Management User (khusus untuk admin) yang dinamis, jika User dibuat lalu role juga dibuat harusnya role tersebut dapat mengikat hak akses terhadap menu-menu, fitur CRUD pada setiap modul di backend (be-ppid)
57. [x] ikon modul FAQ di be-ppid (backend) tidak ada, tolong berikan icon pada modulnya. 
58. [x] Hapus modul Laporan Statistik di be-ppid (backend)
59. [x] pada semua modul yang ada di be-ppid (backend) pastikan ada field/ kolom yang menginformasikan 
    - Dibuat, Diubah, Dihapus oleh user siapa
    - pada hari dan waktu kapan
    tujuannya untuk traceability dokumen
60. [x] semua modul yang ada di be-ppid (backend) data Diubah dikosongkan saja, karena belum ada aktifitas Ubah data kan? ketika buat data baru pun harusnya tanggal ubah belum terisi. lalu,
61. [x] semua modul yang ada di be-ppid (backend) Kolom "Diubah Oleh" dan "Diubah pada" dibuat menjadi default hide saja tetapi kalau mau ditampilkan bisa menggunakan widget Show/Hide kolom.
62. [x] ubah label kolom Diubah pada menjadi Diubah dan Dibuat pada menjadi Dibuat
63. [x] tolong jalankan langkah registrasi akun pengguna/ pengunjung website ppdi alur prosesnya :
    1. isi formulir registrasi akun:
        - Nama lengkap
        - Email
        - No Telpon/ Hp (Whatsapp)
        - Password
        - Konfirmasi Password
        - konfirmasi Captcha
    2. setelah disubmit, redirect ke halaman login (munculkan alert bahwa sebelum login harus verifikasi email)
    3. lalu, klik link verifikasi yang dikirim (jika berhasil kembali ke halaman login) berikan waktu expired yang ideal
    4. login dengan nomor telepon atau email dan password, isi captcha
    5. jika sukses, pengguna harus Verifikasi Data Diri Pemohon, jika belum notifikasi ini selalu muncul dan tidak bisa melakukan apapun (pop up, klik tombol mengarahkan ke modul Data Pemohon dan Berkas)    
    6. proses Verifikasi Data Diri Pemohon paling minimal 14 hari kerja   
    # konfigurasi email sebagai berikut :
    - email : noreply-ppid@foodstation.co.id 
    - password : Ppid@123
    - server mail : srv179.niagahoster.com
    # dikarenakan kami ada batas limit email agar tidak terkena spam, buatkan regulasi yang ketat agar tidak ada percobaan untuk spam, hacker, brute force, sql injection ataupun hal lainnya di Metode AUTH (baik fitur AUTH Pengguna dan AUTH Admin backend)
64. [x] Sekarang buat fitur keamanan fitur AUTH akun pengunjung, seperti :
    - Kirim tautan saat REGISTRASI dan LUPA PASSWORD (tambahkan captcha) bisa dilakukan ketika 30 menit sekali dengan alamat ip dan mac address yang tercatat
    - Fitur login user ketika gagal 3 kali, buat menunggu dulu: 
        - percobaan 3 kali pertama, 1 jam
        - percobaan 3 kali kedua, 24 jam
        - percobaan 3 kali ketiga, 72 jam
65. [x] melanjuti, langkah ke 64. proses pendaftaran ini akan masuk ke notifikasi be-ppid (backend admin) dan masuk ke modul Layanan - Pemohon. pada modul ini pastikan ada fitur untuk memverifikasi data Pemohon apakah sesuai atau tidak, jika sesuai (statusnya berhasil terverifikasi, jika tidak status verifikasi ditolak tetapi pengguna masih bisa mengajukan kembali sampai 3 kali ditolak, proses registrasi diblockir). 
66. [x] melanjuti, langkah ke 65. 
    - buatkan fitur detail be-ppid (backend admin) pada modul Layanan - Pemohon, gunanya agar memudahkan dalam melakukan verifikasi data pemohon. 
    - notifikasi sudah berhasil, tapi tidak bisa diklik yang langsung mengarah ke data sesuai notifikasinya.
67. [x] tolong buatkan format email untuk konfirmasi akun baik untuk registrasi akun pemohon baru, reset password atau hal lainnya. contohnya: 
    - untuk verifikasi akun pemohon yang baru mendaftar ada dipath ini D:\Project\Ppid\Konfirmasi Email.docx
    - untuk reset password, tolong buatkan dengan konsep yang sesuai standar
    - untuk notifikasi email permohonan informasi atau keberatan informasi juga dibuatkan sesuai standar (dengan catatan pemohon hanya mendapatkan email tersebut jika pengajuan permohonan informasi atau keberatan informasi terlah berhasil Dikirim dan Diterima, dan Selesai  oleh Admin PPID PT Food Station Tjipinang Jaya (Perseroda) )
68. [x] modul Laporan Infromasi Statistik Publik di fe-ppid (dihapus) berlakukan juga jika ada di api-ppid dan be-ppid 
69. [x] pada fe-ppid tolong ubah penamaan pada modul "Prosedur Permohonan Informasi Publik" menjadi "Prosedur Permohonan Informasi" dan "Prosedur Permohonan Keberatan Informasi Publik" menjadi "Prosedur Permohonan Keberatan"
70. [x] pada fe-ppid tolong sesuaikan pada modul Standar Layanan, sub modul Jalu dan Waktu Layanan :
    - hapus bagian Jalur Pelayanan CARD SURAT - Mengirimkan surat permohonan ke alamat kantor PPID.
    - ketika diklik CARD ONLINE, mengarah ke halaman Login Pemohon
    - ketika bagian Waktu Pelayanan dibuat minimize, jadi ketika bagian Jalur Pelayanan CARD LANGSUNG diklik maximize Waktu Pelayanan (tambahkan peta lokasi google.com/maps/search/pt+food+station+tjipinang+jaya/@-6.213053,106.881272,17z?entry=s&sa=X&ved=1t%3A199789) berlakukan juga Peta lokasi pada modul Beranda dibagian Hubungi Kami Kontak PPID Food Station
    - diubah dan disesuaikan seperti ini saja Waktu Pelayanan :
        - Senin - Jum'at
        - 08:00 - 17:00
71. [x] pada bagian fe-ppid tolong disesuaikan :
    -[x]  dibagian /akun/permohonan status permohonannya tampilkan Semua, Dalam Proses, dan Selesai
    -[x]  dibagian /akun/keberatan status permohonannya tampilkan Semua, Dalam Proses, dan Selesai
    -[x]  dibagian /akun/histori tambahkan fitur pencarian id Permohonan Informasi maupun Keberatan
    -[x] dibagian profil tambahkan semua infromasi pemohon, secara detail dan dipisahkan dengan nav tabs sesuai dengan informasinya dan tidak bisa diubah kecuali foto Avatar profile saja.
    -[x] dibagian Data Pemohon & Berkas, jika sudah terverifikasi semua informasi tidak dapat diubah-ubah dan foto ktp bisa di view saja
    -[x] dibagian Header ada icon foto avatar profile user, saat ini bagian tersebut tidak update menyesuaikan dengan avatar dihalaman profile
    -[x] dibagian Dashboard (/akun) statistiknya hanya diberikan informasi status permohonan Dalam Proses dan Selesai saja dan pada Grafik Data 
    -[x]Pengajuan dibuat perbulan tapi dibuat perbandingan pertahun jadi 1 grafik bisa menampilkan 12 bulan dengan maksimal perbandingan 3 tahun sebelumnya
72. [x] pada fe-ppid pada portal pemohon atau http://localhost:8000/akun tolong buatkan lonceng notifikasi jika ada upadate dari feedback yang diberikan oleh admin
73. [x] pada be-ppid di modul PEMOHON, PERMOHONAN, dan KEBERATAN dihapus fitur ubah dan delete karena data ini adalah data yang diinput oleh pemohon jadi tidak boleh ada manipulasi aktivitas ataupun datanya.
74. [x] pada fe-ppid, ketika pemohon sudah login tolong sembunyikan bagian tombol Permohonan karena tombol ini sudah dijadikan modul pada portal pemohon.
75. [x] pada be-ppid tolong disesuaikan informasi pada modul Dashboard :
    - Informasi total data Pemohon
    - Total data pemohon klasifikasi menajdi per Jenis Pemohon
    - Total data pemohon yang mendaftar :
        - yang belum diverifikasi
        - yang sudah diverifikasi
        - yang belum melakukan verifikasi data pemohon
    - Total Data berdasarkan Permohonan, baik Permohonan Infomasi dan Keberatan Informasi
    - Total Data berdasarkan Status Permohonan, baik Permohonan Infomasi dan Keberatan Informasi
    - Total Data berdasarkan per Jenis Pemohon, baik Permohonan Infomasi dan Keberatan Informasi
    - Hapus bagian Kepuasan pemohon, Sebaran status dan Kategori paling diminta
    - Revisi grafik informasi Permohonan masuk vs ditanggapi, dibuat diagram bar saja menjadi 1 grafik bisa menampilkan 12 bulan dengan maksimal perbandingan 3 tahun sebelumnya
    - revisi label "Pribadi (data lama)" menjadi "Pribadi" saja 
    dari poin di atas sesuaikan card Total permohonan, Menunggu persetujuan, Lewat batas waktu, dan Keberatan belum selesai 
76. [x] notifikasi pada ikon bell/lonceng di portal be-ppid ataupun fe-ppid ketika di klik/ diakses datanya, harusnya tidak muncul lagi pada daftar notifikasi di loncengnya (karena sudah dibaca/ read).
77. [x] sesuaikan tanggal dan waktu pada sistem menjadi waktu Jakarta/ Indonesia 
78. [x] pada be-ppid di modul PERMOHONAN, dan KEBERATAN :
    - [x] Hapus fitur Tambah Pengajuan karena data ini adalah data yang diinput oleh pemohon jadi tidak boleh ada manipulasi aktivitas ataupun datanya.
    - [x] Tambahkan Fitur Detail, agar memudahkan saat melihat pengajuan dari pemohon.
79. [x] buatkan alur proses dari awal-akhir sesuai dengan struktur organisasi (roles hak akses user disesuaikan dengan struktur organisasi) baik permohonan informasi ataupun keberatan informasi dengan konsep approval yang dinamis dan berjenjang (jika sewaktu berubah, super admin bisa merubahnya melalui be-ppid)
80. [x] pada fe-ppid dibagian Kategori Informasi Informasi Publik, saya ingin backgroundnya pakai gambar di path ini D:\Project\Ppid\fe-ppid\ppid_foody_dimana_saja.png sehingga tidak polos warna hijau saja. tapi saat ini ukuran sectionnya kurang tinggi menyebabkan gambarnya tidak maksimal dan section itu ga 1 layar tampil full, hasilnya bisa dilihat disini D:\Project\Ppid\fe-ppid\3.png (konsepnya sesuaikan sama banner ukuran tinggi dan lebarnya)
81. [x] masih ada masalah dan kekurangan project ini seperti :
    - [x] Lambat ketika login (fitur auth), mengakses modul, perpindahan ke modul lain, CRUD data, serta mengakses semua fitur yang ada didalam modul terasa lambat sekali, tolong diperbaiki prosesnya agar cepat dan ringan.
    - [x] Tidak adanya alert error, misalnya ketika fitur auth berjalan (seperti ketika api tidak berjalan atau server bermasalah atau ada proses yang salah)
    - [x] Belum ada fitur lupa password untuk be-ppid, buatkan konsepnya yang perlu verifikasi ke email
    - [x] Buat sistem keamanan pada fitur auth untuk menghindari spam ataupun hal lainnya seperti contoh :
        - [x] Captcha di login, verifikasi lupa password, dan di konfirmasi pembuatan password baru
        - [x] Maxmimal salah password saat login, 3 kali = 1 jam baru bisa lagi, 3 kali ke 2 = 1 hari, 3 kali ke 3 = 14 hari, 3 kali ke 4 = suspend akun, berikan informasi untuk menghubungi administrator
        - [x] Pastikan yang bisa menjalankan fitur auth hanya email yang terdaftar
        - [x] Maxmimal untuk lupa password verifikasi ke email 3 kali = 1 jam baru bisa lagi, 3 kali ke 2 = 1 hari, 3 kali ke 3 = 14 hari, 3 kali ke 4 = suspend akun, berikan informasi untuk menghubungi administrator
    - [x] email (rudiadriian@gmail.com) yang bukan sebagai user admin, masih bisa direspon saat lupa password :
        Permintaan diterima
        Jika email tersebut terdaftar sebagai akun panel, tautan atur ulang password sudah kami kirim. Periksa juga folder Spam.
82. [x] Ubah Informasi Kontak PPID Food Station, Jam Layanan agar menjadi lebih rapi tampilannya:
    - Senin - Jumat 
    - Pukul 08:00 - 15:00 WIB
    - Istirahat Pukul 12:00 - 13:00 WIB
83. [x] Untuk Daftar Informasi Publik, Informasi Berkala, Infromasi Serta Merta, dan Infromasi Setiap Saat, tolong dibuatkan konsep tombolnya memiliki fitur 2 pilihan. Jadi, ketika di klik muncul POP UP modal dialog dengan 2 tombol. Di lihat Saja (Preview only) diinput tautan dan  jika ingin di Download harus login dan mengajukan Permohonan Informasi.
84. [x] Saya ini ketika publik mengakses fe-ppid ada backsound lagu, setiap kali akses atau refresh halaman D:\Project\Ppid\Jingle_Food_Station_Vocal.mp4
85. [x] Yang berhubungan dengan Logo perusahaan pada header dan footer di fe-ppid dan be-ppid diganti menggunakan file ini D:\Project\Ppid\Logo_fstj.png
86. [x] pada fe-ppid tolong buatkan konsep pada modul Standar layanan - Prosedur Permohonan Informasi karena konsepnya saya ingin menampilkan gambar alur prosesnya sesuai dengan gambar-gambar di folder ini D:\Project\Ppid\standar layanan beserta urutannya. buatkan juga modul untuk CRUD di be-ppid agar konsepnya dinamis. dan Hapus bagian Ringkasan Tahapan dan Rincian Tahapan, dan bagian ini Detail Prosedur Permohonan Informasi
Alur lengkap dari membuat akun sampai permohonan Anda diproses.
87. [x] pada fe-ppid tolong buatkan konsep pada modul Standar layanan - Prosedur Permohonan Keberatan (seperti Prosedur Permohonan Informasi ) karena konsepnya saya ingin menampilkan gambar alur prosesnya sesuai dengan gambar-gambar di folder ini D:\Project\Ppid\standar layanan beserta urutannya. buatkan juga modul untuk CRUD di be-ppid agar konsepnya dinamis. dan Hapus bagian Ringkasan Tahapan dan Rincian Tahapan, dan bagian ini Detail Prosedur Permohonan Informasi Alur lengkap dari membuat akun sampai permohonan Anda diproses.
88. [x] pada fe-ppid tolong buatkan konsep pada modul Standar layanan - Maklumat Pelayanan (seperti Prosedur Permohonan Informasi ) karena konsepnya saya ingin menampilkan gambar alur prosesnya sesuai dengan gambar-gambar dipath ini D:\Project\Ppid\MAKLUMAT PPID.png. buatkan juga modul untuk CRUD di be-ppid agar konsepnya dinamis. saat ini gambarnya terlalu besar.
89. [x] sekarang buatkan alur proses Permohonan Informasi dan Permohonan Keberatan Informasi dari Pemohon kepada Internal Food Station dengan formasi user sesuai struktur organisasi di path D:\Project\Ppid\struktur organisasi.jpeg. adapun alur persiapannya seperti ini :
    1. [x] Buatkan user-user untuk Role dengan Kategori :
        - PPID Pelaksana, terdiri dari :
            - Pengelola Dokumentasi Informasi (jabatan : Kepala Seksi Humas)
            - Pengumuman Informasi (jabatan :  Staf Sekretaris Perusahaan & Kepatuhan)
            - Penyediaan Informasi (jabatan : Staf Seksi Humas)
        - PPID, (jabatan : Sekretaris Perusahaan & Kepatuhan)
    2. [x] Sesuaikan Modul Permohonan pada be-ppid :
        1. [x] Kategori dipisahkan berdasarkan 2 saja yaitu Permohonan Informasi dan Permohonan Keberatan Informasi
        2. [x] Status tolong disesuaikan pada langkah ke 3 dibawah ini
        3. [x] Buat kategori Jalur Pelayanan, yaitu Online (dokumen dikirim via email) atau Langsung (kirim email untuk tanggal dan waktu undangan ke pemohon)
    3. [x] Alur proses Permohonan Informasinya seperti ini :
        - Pemohon mengajukan form permohonan informasi pada fe-ppid (saat ini sudah), status = Diajukan
        - PPID Pelaksana, menerima Permohonan (tujuannya untuk didistribusikan ke hierarki yang lebih tinggi yaitu PPID)
            - Proses penerimaannya, dengan cara klik tombol detail untuk proses verifikasi pada be-ppid (modul Permohonan), terdapat informasi
              detail beserta kolom menyetujui (tidak bisa menolak).
            - Jika permintaan secara Online tampilkan modal dialog, field keterangan dan upload dokumen yang diminta.
            - Jika permintaan secara Langsung tampilkan modal dialog, field keterangan, tanggal dan waktu undangan untuk pemohon bisa hadir sesuai
              layanan
            - Jika telah klik tombol Menyetujui, proses selanjutnya ke PPID untuk approval terakhir, Status berubah menjadi = Sedang Diproses
        - PPID dapat menyetuji dan menolak
            - Jika PPID Setuju, status berubah menjadi Selesai (Closed). Dokumen langsung didistribusikan (kirim notifikasi), jika Online kirim email, tetapi jika langsung berikan undangan ke email Pemohon dengan menyertakan informasi Keterangan, Tanggal dan Waktu, dan hadir sesuai
              layanan:
                Alamat : Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230
                Email & Telepon : ppid@foodstation.co.id · (021) 4718011 (Ext. PPID)
                Jam Layanan : Senin–Jumat Pukul 08.00–15.00 WIB dan Istirahat Pukul 12.00–13.00 WIB
            - Jika PPID Menolak, Status berubah menjadi Ditolak (Rejected), langsung kirim notifikasi ke portal Pemohon (fe-ppid)
    4. [x] Alur proses Permohonan Keberatan Informasi seperti ini :
        - Pemohon mengajukan form Permohonan Keberatan Informasi pada fe-ppid atas dasar permohonan yang sudah diajukan dengan status progress sudah Selesai atau Ditolak oleh PPID. Status => Diajukan
            Adapun alasan pengajuan keberatan yang nanti di isi oleh pemohon sebagai berikut :
            - Penolakan atas permintaan informasi
            - Tidak ditanggapinya permintaan informasi
            - Penyampaian informasi yang melebihi waktu yang diatur
            - Permintaan informasi tidak ditanggapi sebagaimana yang diminta
            - Tidak dipenuhinya permintaan informasi
            - Pengenaan biaya yang tidak wajar
            - Tidak disediakannya Informasi berkala 
            Dan dari alasan ini bisa dijadikan Data analisa di Dashboard
        - PPID Pelaksana, menerima Permohonan Keberatan (tujuannya untuk didistribusikan ke hierarki yang lebih tinggi yaitu PPID)
            - Proses penerimaannya, dengan cara klik tombol detail untuk proses verifikasi pada be-ppid (modul Permohonan), terdapat informasi
              detail beserta kolom menyetujui (tidak bisa menolak).
            - Jika permintaan secara Online tampilkan modal dialog, field keterangan dan upload dokumen yang diminta.
            - Jika permintaan secara Langsung tampilkan modal dialog, field keterangan, tanggal dan waktu undangan untuk pemohon bisa hadir sesuai
              layanan
            - Jika telah klik tombol Menyetujui, proses selanjutnya ke PPID untuk approval terakhir, Status berubah menjadi = Sedang Diproses
        - PPID dapat menyetuji dan menolak
            - Jika PPID Setuju, status berubah menjadi Selesai (Closed). Dokumen langsung didistribusikan (kirim notifikasi), jika Online kirim email, tetapi jika langsung berikan undangan ke email Pemohon dengan menyertakan informasi Keterangan, Tanggal dan Waktu, dan hadir sesuai
              layanan:
                Alamat : Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230
                Email & Telepon : ppid@foodstation.co.id · (021) 4718011 (Ext. PPID)
                Jam Layanan : Senin–Jumat Pukul 08.00–15.00 WIB dan Istirahat Pukul 12.00–13.00 WIB
            - Jika PPID Menolak, status berubah menjadi Ditolak (Rejected), langsung kirim notifikasi ke portal Pemohon (fe-ppid)
    5. [x] Buatkan konsep SLA untuk proses permohonan pada sistem untuk di informasikan se-detail2nya di  be-ppid dan alert di modul dashboard ataupun modul permohonan seperti :
        - Permohonan Informasi PPID memberikan atau mengirimkan informasi kepada pemohon paling lambat 10 hari kerja dan dapat memperpanjang paling lambat 7 hari kerja
        - Permohonan Keberatan Informasi PPID menyampaikan tanggapan keberatan kepada PPID untuk diteruskan kepada pemohon informasi yang mengajukan keberatan paling lambat 30 hari sejak diregistrasinya pengajuan keberatan, Jika pemohon informasi tidak puas terhadap tanggapan keberatan, maka daam waktu 14 hari kerja setelah tanggapan, dapat mengajukan permohonan sengketa informasi publik ke komisi informasi
    6. [x] Terapkan SLA ini pada alur proses Permohonan Informasi dan Permohonan Keberatan Informasi
    7. [x] Sertakan notifikasi lonceng pada be-ppid ketika yang terecord, lalu berikan informasi untuk memberikan informasi traceability, dan untuk penomoran dokumen Permohonan Informasi dan Permohonan Keberatan Informasi dibedakan agar lebih terarsip dengan benar.
90. [x] Banner pada modul beranda di fe-ppid tolong disesuaikan dengan menggunakan path D:\Project\Ppid\HOME 1920 x 1080.png, Judul : Selamat Datang di Portal Resmi PPID Food Station. , dan  Ringkasan : Dikelola oleh Pejabat Pengelola Informasi dan Dokumentasi (PPID) PT Food Station Tjipinang Jaya (Perseroda). Informasi yang disediakan diharapkan dapat digunakan secara bijak dan dimanfaatkan untuk kepentingan masyarakat sesuai dengan ketentuan peraturan perundang-undangan yang berlaku.
91. [x] Notifikasi verifikasi data pemohon terkirim double, coba anda cek semua fitur notifikasi dan pastikan notifikasi ini sudah menyesuaikan dengan menu akses yang sesuai dengan otorisasi hak akses menu berdasarkan role user.
92. [x] saya sudah testing dari pemohon yang sudah terdaftar melakukan verifikasi data diri "Data Pemohon terkirim dan menunggu pemeriksaan petugas PPID. Pemeriksaan memerlukan waktu paling lama 14 hari kerja." sudah berhasil. tapi di web admin notifikasinya tidak ada (saya sedang login dengan role PPID Pelaksana)
93. [x] dari sisi pemohon (fe-ppid) notifikasi verifikasi data diri sudah terkirim jika telah diverifikasi oleh ppid pelaksana di admin ppid, tapi notifikasi tersebut ketika diklik harusnya mengarah ke halaman /akun/pengaturan/data-pemohon di (fe-ppid)
94. [x] pada modul ppid/permohonan (be-ppid) mekanisme untuk approvalnya sangat ribet, lu buat juga fitur ketika ppid pelaksan cek detail permohonan yang masuk untuk bisa approval/ ubah statusnya. Lalu, tomobl Ubah Status di datatablenya lu hapus aja, mekanisme approval tetap di modal dialog Detail dan Verifikasi, lu buatin juga supaya rownya ketika di klik langsung buka modal dialog Detail dan Verifikasi (ini juga berlaku di modul lainnya di be-ppid).
95. [x] be-ppid di Modal Dialog Detail Permohonan Informasi, saat gua upload berkas tanggapan (pakai role user ppid pelaksana) tidak bisa terupload, tolong perbaiki dan buatkan semacam penampung dokumen atau semacam arsip, jika dokumen diupload akan diletakan disitu dan sewaktu admin ingin melampirkan dokumen dipermohonan, tanpa harus upload, tinggal buka arsip dan pilih filenya.
96. [x] tambahkan fitur Survey hasil kepuasan pemohon, setelah permohonan dari pemohon selesai dilakukan, jika tidak dijalankan, berikan highlight saja di dashboard pemohon (fe-ppid)
97. [x] Ada kesalahan alur proses saat permohonan pemohon ingin ditanggapi oleh user admin (be-ppid) yaitu Ketika modul permohonan di be-ppid diakses melalui tombol Detail & Verifikasi, lalu user upload tanggapi dokumen, belum dilakukan simpan, proses tersebut mengirimkan Notifikasi "Berkas Tanggapan ........... " ke portal pemohon (fe-ppid). coba anda cek kembali semua format yang ada pada alur proses permohonan
98. [x] logo pada be-ppid dengan label "PPID Admin" di halaman login, maupun sidebar/header/footer dan atau dimanapun jika ada, tolong dihapus. cukup pakai logo yang sudah ada saja.
99. [x] ubah favicon atau icon taskbar halaman di be-ppid agar sama dengan fe-ppid, dan tolong ubah loader pada be-ppid menggunakan file ini D:\Project\Ppid\loader-fs.gif
100. [x] Alur proses permohonannya masih error, ketika pemohon telah mengajukan Permohonan Informasi/ Keberatan Informasi :
    - Saat ini, saya login sebagai role PPID (Sekretaris Perusahaan) di Detail Permohonan Informasi tidak ada aksi apapun, pastikan alur prosesnya sudah betul ya mulai dari Pemohon -> PPID Pelaksana -> PPID. Harusnya role ini bisa Setuju, Tolak dan Revisi.
        - Jika PPID Setuju, status berubah menjadi Selesai (Closed). Dokumen langsung didistribusikan (kirim notifikasi), jika Online kirim email, tetapi jika langsung berikan undangan ke email Pemohon dengan menyertakan informasi Keterangan, Tanggal dan Waktu, dan hadir sesuai layanan:
            Alamat : Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230
            Email & Telepon : ppid@foodstation.co.id · (021) 4718011 (Ext. PPID)
            Jam Layanan : Senin–Jumat Pukul 08.00–15.00 WIB dan Istirahat Pukul 12.00–13.00 WIB
        - Jika PPID Menolak, status berubah menjadi Ditolak (Rejected), langsung kirim notifikasi ke portal Pemohon (fe-ppid)
        - Jika PPID Revisi, 
            - PPID Pelaksana akan menyesuiakan hasil revisi arahan PPID, jika sudah selesai Revisi, klik tombol Setuju lagi, untuk kembali ke PPID (begitu seterusnya)
            - status akan berubah menjadi Revisi tapi status ini tidak terlihat di sisi Pemohon (fe-ppid) dari sisi pemohon statusnya tetap Diproses
    - Cek kembali, karena ada bug kode PPID-FSTJ/20260831/0001 tolong dicek. Saat ini, saya login sebagai role PPID Pelaksana tetapi tidak dapat memperoses permohonan informasi.
    - Tampilan UI/UX modal dialog Detail Permohonan Informasi bisakah dibuat lebih baik lagi? dan tidak menumpuk2, misalhnya dibagi menjadi 2 col, disebelah kiri form yang harus di isi, sebelah kanan form detail infomasi permohonan.
    - Bagian Ubah Status "Berkas ini sudah siap diputuskan. Lanjutkan dari panel Persetujuan Berjenjang di atas — Setujui untuk meneruskannya, atau Kembalikan untuk diperbaiki." pada modal dialog Detail Permohonan Informasi apakah diperlukan? jika tidak dihilangkan saja agar tampilan lebih simple tapi tetap informatif.
    - Bagian Persetujuan Berjenjang pada modal dialog Detail Permohonan Informasi, bisakah dibuat tampilannya desainnya dibuat lebih minimalis tapi dengan informasi yang sama? 
101. [x] pada fe-ppid tolong disesuaikan, disempurnakan lagi ;
    1. di modul akun/keberatan/baru form keberatan dapat diajukan dengan syarat permohonan informasi yang pernah diajukan memiliki alasan sebagai berikut :
        - Penolakan atas permintaan informasi
        - Tidak ditanggapinya permintaan informasi
        - Penyampaian informasi yang melebihi waktu yang diatur
        - Permintaan informasi tidak ditanggapi sebagaimana yang diminta
        - Tidak dipenuhinya permintaan informasi
        - Pengenaan biaya yang tidak wajar
        - Tidak disediakannya Informasi berkala 
    2. table data di akun/permohonan dan akun/keberatan rownya dapat di klik untuk melihat detail dari permohonan dan field Search nomor atau rincian permohonannya dibuat lebih lebar lagi karena kalau yang diketik panjang, tulisannya kepotong
    3. pada tampilan detail permohonan informasi maupun keberatan ubah label Jejak Status menjadi Alur Persetujuan dengan menampilkan HANYA status Diajukan (ketika pemohon mengajukan), Diproses (permohonan sedang diproses internal, meskipun ada revisi ataupun status lainnya di be-ppid pada fe-ppid tetap menampilkan sedang diproses, lalu stepnya tidak berubah hanya 1 saja), dan Selesai (permohonan selesai) 
    4. tampilan riwayat permohonan informasi pada modul Histori permohonan di akun/histori, ini juga informasi statusnya dibuat seperti poin nomor 3 (baik dibagian Permohonan Informasi ataupun Keberatan Informasi)
    5. Batas Waktu Tanggapan tidak perlu ditampilkan informasinya, dan Tanggal Tanggapan diisi tanggal perubahan status menjadi Selesai
102. 1. [x] Tampilan UI/UX be-ppid di bagian modal dialog Detail Permohonan Informasi modul Permohonan terlalu ribet. coba buat responsive, menarik, informatif dan lebih rapi secara penyampaian/ penempatan informasi maupun field form yang bisa di isi.
    2. [x] sekarang sudah bagus tetapi letak bagian Berkas Tanggapan Petugas tolong diletakan diatas Putusan Anda
    3. [x] Label-label di /ppid/permohonan disesuaikan, Putaran diubah menjadi Alur Persetujuan, Putusan Anda diubah menjadi Verifikasi Permohonan, Berkas Tanggapan Petugas dibuah menjadi Lampiran dan Keterangan, Pilih berkas tanggapan diubah menjadi Pilih dari Perangkat, Belum ada berkas tanggapan diubah menjadi Dokumen belum dilampirkan, Kirim putusan diubah menjadi Setuju, Persetujuan Berjenjang diubah menjadi Verifikasi Permohonan
    4. [x] Lampiran dan Keterangan ini adanya di dalam Verifikasi Permohonan, nah HARUSNYA Verifikasi Permohonan yang disebelah kiri ini ada section bagiannya :
        1. Alur Persetujuan Permohonan
        2. Verifikasi Petugas PPID
        3. Lampiran dan Keterangan 
        4. Tombol Konfirmasi (sebelumnya Setuju). Tombol Setuju, Tolak dan Revisi (khusus untuk role user PPID)
            Konfirmasi = meminta konfirmasi dari Role user PPID Pelaksana ke PPID
            Setuju = Permohonan disetujui oleh Role PPID untuk diinformasikan ke Pemohon
            Tolak = Permohonan ditolak oleh Role PPID untuk diinformasikan ke Pemohon
            Revisi = Konfirmasi dari Role user PPID Pelaksana ke PPID untuk meminta direvisi (perbaikan) atas permohonan yang diajukan dan telah dikonfirmasi   
        Untuk bagian sebelah kanan : Isi Permohonan, Pemohon, Pelayanan & Tanggapan, Riwayat Status (bagian ini sudah sesuai.)


---


## Status Pengerjaan (putaran 89 — langkah 102.4)

### Verifikasi Permohonan jadi satu kartu berbagian

Empat bagian, dalam urutan kerjanya:

1. **Alur Persetujuan Permohonan** — di mana berkasnya sekarang, beserta putaran sebelumnya yang dilipat.
2. **Verifikasi Petugas PPID** — jalur pelayanan, jadwal, keterangan untuk pemohon, dan catatan.
3. **Lampiran dan Keterangan** — dokumen yang akan diserahkan.
4. **Putusan** — tombolnya.

Lampiran tidak lagi berdiri sebagai kartu tersendiri di atasnya; ia bagian ketiga di dalam kartu ini. Bagian 1 dan 3 selalu tampil — keduanya bacaan, dan yang belum kebagian giliran pun perlu melihatnya. Bagian 2 dan 4 hanya untuk pemegang giliran.

Panelnya tidak membangun lampirannya sendiri: `BerkasTanggapanPanel` diserahkan dari dialog lewat prop `lampiran`. Berkas tanggapan punya aturan unggah, arsip, dan penguncian sendiri yang tidak ada hubungannya dengan jenjang persetujuan; yang ditentukan panel ini cuma tempatnya.

### Tombol per putusan, bukan dropdown

Dropdown **Keputusan** dilepas. Ia menyembunyikan dua dari tiga pilihan di balik satu klik tambahan, dan memaksa tombol kirimnya berbunyi netral — petugas menekan "Kirim putusan" tanpa satu kata pun yang menyebut bahwa perkara pemohon akan ditutup.

Sekarang tombolnya menyebut sendiri apa yang akan terjadi, dan yang tampil mengikuti hak tahapnya:

| Tahap | Tombol | Artinya |
| --- | --- | --- |
| Penerimaan PPID Pelaksana (`boleh_tolak: false`) | **Konfirmasi** | Meminta konfirmasi PPID — meneruskan berkasnya, bukan menyetujui permohonannya |
| Persetujuan PPID (`boleh_tolak: true`) | **Setuju** | Menutup permohonan dan memberitahukan hasilnya ke pemohon |
| | **Revisi** | Mengembalikan berkas ke PPID Pelaksana untuk diperbaiki |
| | **Tolak** | Menolak permohonan dan memberitahukan alasannya ke pemohon |

Tiap kelompok tombol diberi satu baris keterangan di bawahnya, karena "Konfirmasi" dan "Setuju" sama-sama mengirim `disetujui` ke server tetapi berbeda akibatnya bagi pemohon — yang satu belum mengubah apa pun baginya, yang lain menutup perkaranya.

Catatan wajib untuk Tolak dan Revisi dijaga dua kali: tombolnya mati selama catatan kosong, dan `kirim()` menolaknya sekali lagi sebelum permintaan berangkat. Alasan itu dibaca orang lain — pemohon pada penolakan, petugas pada revisi — jadi tidak boleh lolos hanya karena tombolnya sempat hidup.

### Kamus Inggris

Sebelas padanan baru — nama tiap bagian, ketiga tombol, dan keterangan di bawahnya. Tiga kunci yang mati bersama dropdown-nya (dua helper text "Wajib diisi…" dan satu petunjuk unggah yang menyebut panel yang sudah tidak ada) dibuang.

### Berkas yang disentuh

| Berkas | Isi |
| --- | --- |
| `components/PersetujuanBerjenjang.tsx` | `Bagian`, prop `lampiran`, empat bagian, tombol per putusan |
| `components/PermohonanDetailDialog.tsx` | Lampiran masuk ke dalam kartu Verifikasi Permohonan |
| `@i18n/kamusPpid.ts` | Sebelas padanan baru, tiga kunci mati dibuang |

### Verifikasi

- be-ppid `tsc --noEmit` bersih; `eslint` atas seluruh berkas yang disunting tanpa galat dan tanpa peringatan.
- api-ppid **76 lulus** (396 asersi), fe-ppid **96 lulus** (361 asersi).
- Dijalankan atas `PPID-FSTJ/20260831/0001` yang sungguhan (transaksi, lalu dibatalkan): tahap Pelaksana `boleh_tolak: false` → satu tombol Konfirmasi → status `diproses`; tahap PPID `boleh_tolak: true` → tiga tombol → Revisi menurunkannya ke `revisi`, Konfirmasi ulang mengembalikannya ke `diproses`, Setuju menutupnya ke `selesai`.

---

## Status Pengerjaan (putaran 88 — langkah 102.2 & 102.3)

### Lampiran mendahului putusan

Kartu **Lampiran dan Keterangan** naik ke atas kartu putusan. Urutan kerjanya memang begitu: petugas melampirkan dokumen dan menulis keterangannya, baru memutus. Formulir putusan yang berdiri lebih dulu menawarkan langkah terakhir sebagai langkah pertama.

### Label disamakan dengan kosakata petugas

| Sebelum | Sekarang |
| --- | --- |
| Persetujuan Berjenjang | **Verifikasi Permohonan** |
| Putusan Anda | *(dilepas — lihat catatan)* |
| Putaran | **Alur Persetujuan** |
| Berkas Tanggapan Petugas | **Lampiran dan Keterangan** |
| Pilih berkas tanggapan | **Pilih dari Perangkat** |
| Belum ada berkas tanggapan | **Dokumen belum dilampirkan** |
| Kirim putusan | **Setuju** *(mengikuti putusan yang dipilih)* |

Dua keputusan yang menyimpang sedikit dari daftarnya, keduanya karena mengikuti daftar apa adanya justru merugikan:

**"Putusan Anda" tidak diganti melainkan dilepas.** Permintaannya mengubah *dua* label menjadi "Verifikasi Permohonan" — nama kartunya dan judul kotak putusan di dalamnya. Keduanya bertumpuk, dan dua judul yang sama beruntun hanya menambah baris tanpa menambah keterangan. Nama itu dipakai kartunya; judul di dalam kotak dilepas.

**"Kirim putusan" jadi "Setuju" — tetapi hanya saat putusannya memang Setujui.** Satu tombol yang selalu berbunyi "Setuju" untuk tiga putusan yang berbeda tidak menyebutkan apa yang akan terjadi, dan yang paling merugikan justru penolakan: petugas yang memilih Tolak akan menekan tombol bertuliskan "Setuju" untuk menutup perkara pemohon. Bunyinya karena itu mengikuti pilihan di atasnya — **Setuju**, **Tolak**, atau **Kembalikan untuk diperbaiki** — dan pada keadaan bawaan tetap berbunyi "Setuju" persis seperti yang diminta.

### Kamus Inggris ikut disamakan

Kunci lama diganti dan padanannya diperbarui: *Request Verification*, *Attachments and Notes*, *Choose from device*, *No documents attached yet*, *Approval Flow*, *Approve*.

Sekalian ditutup lubang yang tertinggal dari putaran 100 dan 102: sembilan kalimat baru — pengumuman giliran, keterangan lampiran terkunci, label strip ringkasan — belum pernah punya padanan dan tampil sebagai bahasa Indonesia saat panel dipakai dalam bahasa Inggris. Satu kunci yang sudah tidak dipakai kode mana pun (`Permohonan ini sedang berada di jenjang persetujuan…`, milik panel status yang kini diam) ikut dibuang.

### Berkas yang disentuh

| Berkas | Isi |
| --- | --- |
| `components/PermohonanDetailDialog.tsx`, `components/KeberatanDetailDialog.tsx` | Urutan kartu; judul kartu |
| `components/PersetujuanBerjenjang.tsx` | `LABEL_TOMBOL`; label putaran; judul kotak putusan dilepas |
| `components/BerkasTanggapanPanel.tsx` | Dua label tombol dan keadaan kosong |
| `components/KeberatanTanggapanPanel.tsx` | Rujukan nama panel |
| `@i18n/kamusPpid.ts` | Kunci diganti, sembilan padanan baru, satu kunci mati dibuang |

### Verifikasi

- be-ppid `tsc --noEmit` bersih; `eslint` atas seluruh berkas yang disunting tanpa galat dan tanpa peringatan.
- api-ppid **76 lulus** (396 asersi), fe-ppid **96 lulus** (361 asersi) — keduanya tidak tersentuh putaran ini dan dijalankan sebagai pemeriksaan mundur.

---

## Status Pengerjaan (putaran 87 — langkah 101 & 102)

### Langkah 101 — dua butir tambahan

**101.4 — Histori memakai alur yang sama.** Modul Histori masih mencetak jejak status apa adanya: tiap perpindahan internal jadi satu titik di linimasa, termasuk putaran revisi antara PPID dan PPID Pelaksana. Keduanya kini memakai partial alur tiga langkah yang sama dengan rincian.

Daftar keberatan di Histori sekaligus dinaikkan bentuknya. Dulu satu baris datar tanpa alur sama sekali — pemohon tidak punya cara tahu keberatannya sudah sampai mana. Sekarang bentuknya sama dengan permohonan: kepala berkas yang bisa dilipat, alasan keberatan, tanggal tanggapan, alur, dan tautan ke rinciannya.

**101.5 — Batas Waktu Tanggapan dilepas, Tanggal Tanggapan dibetulkan.** Batas waktu adalah tenggat kerja petugas; di layar pemohon ia terbaca sebagai janji tanggal yang belum tentu jadi hari jawabannya keluar. Dilepas dari rincian permohonan, rincian keberatan, dan Histori.

Tanggal Tanggapan tidak lagi dibaca dari kolom `tanggal_tanggapan`. Kolom itu diisi begitu berkasnya berpindah ke status akhir **mana pun** dan tidak pernah ditulis ulang, jadi baris yang sempat lewat status akhir lain lebih dulu membawa tanggal yang bukan tanggal selesainya. `tanggalSelesaiPortal()` mengambilnya dari perpindahan status ke **Selesai**, dengan kolomnya sebagai cadangan untuk baris lama yang log statusnya tidak lengkap.

---

### Langkah 102 — rincian permohonan di panel dirapikan

Putaran 100 sudah memecahnya jadi dua kolom, tetapi isinya masih satu aliran `judul-kecil → isi → garis pemisah` yang panjang. Semua bagian tampil setara, tidak ada yang menonjol, dan petugas membaca dari atas tiap kali hanya untuk menemukan satu isian.

**Nomor berkas naik ke judul dialog.** Judul yang cuma berbunyi "Detail Permohonan Informasi" tidak memberi tahu apa pun tentang berkas yang sedang dibuka, sementara nomornya — satu-satunya penanda yang dipakai petugas saat bicara dengan pemohon — tenggelam sebagai baris pertama isi. Sekarang nomor, status, dan penanda keberatan terkait berdiri di judul.

**Empat angka pokok jadi strip ringkasan.** Kapan masuk, kapan tenggatnya, lewat jalur apa, siapa petugasnya — dulu tersebar di dalam blok "Penanganan" di tengah halaman, bercampur isian yang jarang dibaca. Kini berdiri di kepala dan terbaca sebelum petugas menggulung apa pun.

**Tiap bagian jadi kartu berbingkai** dengan ikon yang bisa dikenali sekilas dan tempat tetap untuk aksinya. Isinya disusun ulang menurut apa yang dicari bersamaan, bukan menurut urutan kolom tabelnya:

- **Isi Permohonan** — rincian, tujuan, kategori, format, cara pengiriman, dan lampiran pemohon. Lampiran menempel di sini karena ia bagian dari permintaan itu, bukan kartu tersendiri.
- **Pemohon** — identitas; jenis pemohon naik jadi chip di kepala kartu.
- **Pelayanan & Tanggapan** — jalur, jadwal, tanggal tanggapan, register publik, keterangan untuk pemohon, dan alasan penolakan bila ada. Lima isian yang dulu terserak di dua blok.
- **Riwayat Status** — tetap terakhir.

**Kolom kerja menempel** (`lg:sticky`) saat kolom kanan digulung, jadi tombol putusan tidak hilang dari layar saat petugas membaca berkasnya. Di bawah `md` dialognya jadi layar penuh: dialog `lg` di layar ponsel menyisakan bingkai tipis di tiap sisi dan memotong isinya dua kali.

Rincian keberatan disusun sama persis — dua modul yang isinya sejenis tidak boleh menuntut dua kebiasaan membaca.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| fe-ppid | `Models/PermohonanInformasi.php` | `tanggalSelesaiPortal()` |
| fe-ppid | `views/akun/histori.blade.php` | Alur tiga langkah di kedua daftar; keberatan dinaikkan bentuknya |
| fe-ppid | `views/akun/permohonan/show.blade.php`, `views/akun/keberatan/show.blade.php` | Batas waktu dilepas; tanggal tanggapan dari perpindahan ke Selesai |
| fe-ppid | `tests/Feature/PortalAlurDanKeberatanTest.php` | Dua tes baru |
| be-ppid | `components/RincianPengajuan.tsx` | `Kartu`, `Ringkasan`; `Judul` yang jadi kode mati dibuang |
| be-ppid | `components/PermohonanDetailDialog.tsx`, `components/KeberatanDetailDialog.tsx` | Judul, ringkasan, kartu, kolom menempel, layar penuh di layar sempit |

### Verifikasi

- api-ppid **76 lulus** (396 asersi), fe-ppid **96 lulus** (361 asersi). be-ppid `tsc --noEmit` bersih; `eslint` atas seluruh berkas yang disunting tanpa galat **dan** tanpa peringatan.
- Dua tes baru: Histori memasang alur tiga langkah di kedua daftar tanpa membocorkan `revisi` maupun tenggat petugas; Tanggal Tanggapan mengikuti perpindahan ke Selesai meski `tanggal_tanggapan` memuat tanggal lain.

---

## Status Pengerjaan (putaran 86 — langkah 100 & 101)

Dua langkah sekaligus: tampilan rincian di panel admin, dan penyempurnaan Portal Pemohon.

---

### Langkah 100 — rincian permohonan tidak lagi menumpuk

**Dua kolom.** Rincian dan formulir kerjanya sebelumnya bercampur dalam satu kolom panjang: petugas menggulung melewati enam blok bacaan sebelum sampai ke tempat ia harus bertindak, lalu menggulung balik untuk memastikan apa yang tadi ia baca. Kiri sekarang berisi yang **dikerjakan** — putusan berjenjang, berkas tanggapan, ubah status — dan kanan yang **dibaca**: pemohon, isi permohonan, penanganan, lampiran, riwayat. Dialognya melebar ke `lg`; di layar sempit keduanya kembali menumpuk dengan kolom kerja tetap lebih dulu. Rincian keberatan disusun sama supaya keduanya tidak menuntut dua kebiasaan berbeda.

**Bagian Ubah Status yang kosong dilepas.** Keterangan "Berkas ini sudah siap diputuskan, lanjutkan dari panel Persetujuan Berjenjang" adalah satu blok penuh yang tidak menambah satu pun tindakan — dan isinya sudah disebut di kepala rincian, tepat di tempat petugas membacanya. `adaPilihanStatus()` memutuskan apakah bagian itu perlu dipasang sama sekali; kalau tidak ada perpindahan yang bisa dipilih, bagiannya tidak ada.

**Jenjang persetujuan jadi garis waktu, bukan tumpukan kartu.** Tiap tahap dulu satu kartu berbingkai dengan ikon berwarna, dua chip, dan tiga baris keterangan — empat tahap berarti empat kotak yang menuntut perhatian sama besar dengan berkas dan formulir putusannya. Sekarang satu baris bertitik penanda: nama tahap, pemegangnya, dan status pada satu baris; waktunya di baris kedua. Isinya sama persis. Yang dilepas cuma bingkai, ikon, dan chip "Giliran sekarang" — giliran sudah ditandai titik beraksen, tebalnya baris, dan pengumuman di kepala rincian. Kotak "bukan giliran Anda" di dasar panel ikut dilepas karena mengulang pengumuman yang sama.

---

### Langkah 101 — Portal Pemohon

**1. Dasar keberatan mengikuti Pasal 35, bukan "sudah selesai".** Aturan lama hanya mengizinkan keberatan atas permohonan yang penanganannya tuntas. Justru itu menutup dua dasar yang paling sering dipakai — **tidak ditanggapinya permintaan informasi** dan **penyampaian informasi melebihi waktu yang diatur** — karena keduanya baru muncul ketika permohonan **belum** ditanggapi sampai tenggatnya lewat, dan berkas semacam itu statusnya masih berjalan.

`layakDikeberatankan()` karena itu punya dua pintu: penanganannya sudah tuntas (apa pun hasilnya), **atau** masih berjalan tetapi tenggat tanggapannya sudah lewat. Ketujuh dasarnya juga ditulis di muka formulir, bukan ditemukan satu per satu di dalam dropdown setelah pemohon terlanjur menyusun kasus posisinya.

**2. Baris tabel bisa diklik, kolom cari dilebarkan.** Daftar permohonan dan keberatan sama-sama membuka rinciannya dari mana pun di barisnya; tautan Detail tetap ada untuk papan ketik, pembaca layar, dan buka-di-tab-baru. Tautan di dalam baris — Isi Survei, unduh lampiran — menghentikan klik barisnya sendiri. Kotak carinya dipatok `sm:w-80 lg:w-96`; sebelumnya selebar bawaan peramban, dan pencarian yang panjang terpotong.

Keberatan sebelumnya **tidak punya halaman rincian sama sekali**: seluruh isinya harus terbaca dari satu baris tabel yang terpotong, dan tanggapan petugas tidak punya tempat untuk ditampilkan. Halamannya dibuat, lengkap dengan tanggapan PPID, lampiran, dan batas pengajuan sengketa ke Komisi Informasi — tenggat yang sebelumnya hanya ada di badan surel dan mudah terlewat.

**3. Jejak Status jadi Alur Persetujuan: tiga langkah, selalu tiga.** Panel admin punya jenjang berlapis dengan putaran revisi yang bisa berulang; semuanya urusan internal. Yang perlu diketahui pemohon cuma di mana berkasnya berada — **Diajukan**, **Diproses**, **Selesai**. Langkahnya tidak bertambah-kurang tiap kali petugas bekerja, dan perbaikan pekerjaan petugas tidak lagi terbaca sebagai masalah pada berkas pemohon sendiri. Satu partial dipakai rincian permohonan maupun keberatan.

---

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| be-ppid | `components/PermohonanDetailDialog.tsx`, `components/KeberatanDetailDialog.tsx` | Dua kolom; Ubah Status dipasang bersyarat |
| be-ppid | `components/PermohonanStatusPanel.tsx` | Diam saat bukan gilirannya; helper pindah ke `lib` |
| be-ppid | `components/PersetujuanBerjenjang.tsx` | Garis waktu menggantikan tumpukan kartu |
| be-ppid | `lib/statusPengajuan.ts` | `tujuanStatus()`, `adaPilihanStatus()`, `LANJUT_PEMEGANG` |
| fe-ppid | `Models/PermohonanInformasi.php` | `layakDikeberatankan()`, `tahapAlurPortal()`, `tanggalAlurPortal()` |
| fe-ppid | `Models/KeberatanInformasi.php` | Tonggak alur versi pemohon |
| fe-ppid | `Akun/KeberatanController.php` | `show()`; kelayakan mengikuti Pasal 35 |
| fe-ppid | `routes/akun.php` | Rute `akun.keberatan.show` |
| fe-ppid | `views/akun/partials/alur-persetujuan.blade.php` | Baru — tiga langkah, dipakai dua rincian |
| fe-ppid | `views/akun/keberatan/show.blade.php` | Baru — rincian keberatan |
| fe-ppid | `views/akun/permohonan/{index,show}.blade.php`, `views/akun/keberatan/{index,create}.blade.php` | Baris diklik, kolom cari, alur, tujuh dasar |
| fe-ppid | `tests/Feature/PortalAlurDanKeberatanTest.php` | Baru — enam tes |

### Verifikasi

- api-ppid **76 lulus** (396 asersi), fe-ppid **94 lulus** (353 asersi). be-ppid `tsc --noEmit` bersih, `eslint` atas seluruh modul `ppid` tanpa galat.
- Enam tes baru di fe-ppid: kelayakan keberatan atas berkas lewat tenggat dan atas berkas ditolak, penolakan server atas berkas yang belum layak, rincian keberatan beserta penjagaan kepemilikannya, tiga langkah alur atas sepuluh status berbeda, dan rincian permohonan yang tidak lagi membocorkan `revisi` maupun `diverifikasi`.

### Catatan

`npx eslint --fix` sempat dijalankan atas seluruh direktori `ppid/` dan ikut merapikan delapan berkas yang tidak ada hubungannya dengan langkah ini. Semuanya dikembalikan; yang tersisa di diff hanya berkas yang memang disunting.

---

## Status Pengerjaan (putaran 85 — langkah 100, `PPID-FSTJ/20260831/0001`)

"Login sebagai PPID Pelaksana tetapi tidak dapat memproses permohonan informasi."

### Bukan berkasnya — kuncinya kelewat rapat

Berkas `PPID-FSTJ/20260831/0001` sehat. Jenjangnya ada, tahap 1 **Penerimaan PPID Pelaksana** menunggu, dan putusan Setujui atasnya dijawab HTTP 200. Yang tidak bisa dilakukan petugas adalah **memprosesnya** — menandai berkas baru itu **Diverifikasi** sebelum ia menyiapkan tanggapan.

Putaran 80 mengunci seluruh perpindahan status selama ada tahap yang menunggu. Yang benar untuk berkas yang sudah diteruskan; salah untuk berkas yang justru masih di meja orang yang membukanya. Jenjang lahir sejak berkasnya pertama dibaca, jadi kuncinya terpasang sejak detik pertama: satu-satunya tombol yang tersisa bagi PPID Pelaksana adalah **Setujui** — yang justru melempar berkas ke PPID sebelum ia sempat mengerjakannya.

Riwayat status membenarkannya. `PPID-FSTJ/20260831/0002` sempat melewati `diajukan → diverifikasi → diproses` pada 31 Agustus, sebelum kunci itu ada. Berkas `0001` mencoba jalan yang sama dan ditolak.

### Kunci sekarang punya tiga jawaban, bukan dua

| Keadaan | Dropdown status |
| --- | --- |
| Tidak ada tahap berjalan | Sepenuhnya milik petugas |
| Tahap berjalan milik orang lain | Tertutup penuh |
| **Tahap berjalan milik Anda** | **Hanya perpindahan yang membiarkan berkasnya di meja Anda** |

Untuk permohonan, isi baris ketiga adalah `diverifikasi` (`STATUS_LANJUT_PEMEGANG`). Yang tetap tertutup — bagi siapa pun, termasuk pemegang gilirannya — adalah perpindahan yang memindahkan berkas ke meja lain atau menutup perkaranya: `diproses`, `revisi`, dan seluruh status akhir. Ketiganya hasil putusan di panel Persetujuan Berjenjang, bukan pilihan dropdown. Super admin tetap dikecualikan, dengan alasan yang sama seperti pada `bolehMemutus()`.

Panelnya mengikuti aturan yang sama dan menyebutkan tahap yang sedang memegang berkasnya, bukan sekadar "sedang di jenjang persetujuan". Saat pilihan pemegang giliran memang habis — berkasnya sudah Diverifikasi dan tinggal diputuskan — keterangannya menunjuk ke panel putusan, bukan berbunyi "status ini sudah final" yang justru keliru.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Cms/PermohonanController.php` | `STATUS_LANJUT_PEMEGANG`, `pastikanBolehGeserStatus()` |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php` | Satu tes baru |
| be-ppid | `components/PermohonanStatusPanel.tsx` | Tiga wajah kunci; tujuan disaring; keterangan menyebut tahapnya |
| be-ppid | `components/PermohonanDetailDialog.tsx` | Meneruskan `giliranSaya` dan nama tahap berjalan |

### Verifikasi

- api-ppid **76 lulus** (396 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.
- Tes baru: pemegang giliran boleh menandai Diverifikasi; `diproses`, `ditolak`, dan `kedaluwarsa` tetap ditolak baginya; PPID yang belum kebagian giliran ditolak bahkan untuk Diverifikasi; alurnya tetap berjalan sesudahnya lewat putusan.
- Dijalankan atas `PPID-FSTJ/20260831/0001` yang sungguhan (transaksi, lalu dibatalkan): `diajukan` → Diverifikasi berhasil → loncatan ke `diproses` ditolak → Setujui meneruskannya ke `diproses`.

---

## Status Pengerjaan (putaran 84 — langkah 100, putusan PPID menutup perkara)

Langkah 100 ditulis ulang menjadi tiga akibat putusan PPID. Yang sudah berjalan sejak putaran sebelumnya — PPID kebagian giliran dengan Setujui / Tolak / Kembalikan untuk diperbaiki, dan putaran revisi berulang sampai selesai — tidak diubah. Yang berubah tiga hal.

### 1. Setuju menutup perkaranya, bukan menaikkannya satu tingkat lagi

Putusan PPID dulu memasang **Disetujui**, dan `selesai` menunggu ada yang menggesernya lagi lewat dropdown — langkah yang tidak dimiliki siapa pun dalam alur ini, sehingga berkas yang sebetulnya tuntas menumpuk di status antara. Sekarang putusan Setuju memasang **Selesai (Closed)** langsung.

`disetujui` tidak dihapus: masih status yang sah, masih dipegang baris lama, dan dari sana `selesai` masih bisa dituju. Yang berubah hanya apa yang **dihasilkan** alurnya.

### 2. Dokumen didistribusikan saat putusan akhir, bukan saat berkasnya diteruskan

Undangan jalur Langsung dulu berangkat begitu PPID Pelaksana meneruskan berkasnya — menjanjikan pertemuan pada tanggal tertentu atas permohonan yang belum diputus, dan yang bisa saja ditolak PPID beberapa hari kemudian. Undangan itu kini berangkat bersama putusan yang menutup perkaranya, lengkap dengan Keterangan petugas, Tanggal & Waktu, alamat, kontak, dan jam layanan.

Jalur Online tidak dikirimi surel kedua: perpindahan ke `selesai` sudah memicu surel "selesai ditangani" beserta tautan portalnya. Satu peristiwa, satu surat — yang khas jalur Langsung memang tidak ada di surel itu, jadi undangannya berdiri sendiri.

Penolakan tidak berubah: status **Ditolak**, alasannya ikut ke lonceng portal pemohon.

### 3. Revisi tidak pernah terlihat pemohon

Putaran perbaikan antara PPID dan PPID Pelaksana adalah urusan internal. Dari sisi pemohon tidak ada yang berubah — permohonannya memang masih diproses — dan mengabarkan tiap putaran hanya membuat pemohon mengira berkas *miliknya* yang bermasalah, padahal yang diperbaiki pekerjaan petugas.

Disembunyikan di dua lapis yang harus sepakat, karena lonceng yang menyebut satu status sementara daftarnya menyebut yang lain lebih membingungkan daripada tidak berkabar sama sekali:

- **api-ppid** — `NotifikasiPortal::STATUS_INTERNAL` menahan loncengnya; tidak ada notifikasi yang berangkat.
- **fe-ppid** — `revisi` dilabeli **Dalam Proses** / **Diproses** di seluruh peta status portal dan halaman cek status publik.

"Revisi" ikut dilepas dari `KELOMPOK` grafik dashboard pemohon: tidak ada lagi status yang memetakan ke sana, dan legend dengan potongan yang selamanya nol hanya menyisakan pertanyaan.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Cms/PermohonanController.php` | Setuju → `selesai` |
| api-ppid | `Models/PermohonanInformasi.php` | `diproses` boleh menuju `selesai` |
| api-ppid | `Concerns/MenanganiPersetujuan.php` | Undangan jalur Langsung menyusul putusan akhir |
| api-ppid | `Support/NotifikasiPortal.php` | `STATUS_INTERNAL` — `revisi` tidak dikabarkan |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php`, `BerkasTanggapanTest.php` | Dua tes baru; satu tes disusun ulang |
| be-ppid | `lib/statusPengajuan.ts` | Transisi disamakan |
| fe-ppid | `Models/PermohonanInformasi.php`, `Models/KeberatanInformasi.php` | `revisi` tampil sebagai Dalam Proses; `KELOMPOK` dirapikan |
| fe-ppid | `PpidController.php` | Cek status publik & label status ikut menyamarkan `revisi` |

### Verifikasi

- api-ppid **75 lulus** (384 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.
- Dua tes baru: putusan PPID menutup berkas ke `selesai` **dan** mengirim undangan jalur Langsung — sekaligus menegaskan tidak ada surat yang berangkat saat PPID Pelaksana baru meneruskan; putusan Revisi memindahkan status di panel tanpa menambah satu pun lonceng di sisi pemohon.
- Rantai penuh dijalankan atas `PPID-FSTJ/20260831/0002` yang sungguhan (transaksi, lalu dibatalkan): PPID kebagian giliran → Revisi menurunkannya ke `revisi` tanpa lonceng pemohon → Pelaksana menyetujui lagi → `diproses` → PPID menyetujui → **`selesai`**.

---

## Status Pengerjaan (putaran 83 — langkah 100, keluhan ketiga)

> "Kalau sudah Konfirmasi tidak bisa Pilih Berkas Tanggapan dan Pilih dari Arsip … padahal sudah setuju dan saya sudah upload berkas tanggapan, tapi kenapa tidak ada di tampilan Detail Permohonan Informasi."

Dua cacat, tidak berkaitan satu sama lain.

### 1. Berkas tanggapan tidak pernah tampil — kunci JSON-nya salah

Eloquent menulis nama relasi dalam bentuk snake_case saat menyusun jawabannya (`$snakeAttributes`), jadi rincian permohonan mengirim `tanggapan_files` dan `log_status`. Dialognya membaca `data.tanggapanFiles` dan `data.logStatus` — keduanya `undefined`, tanpa galat apa pun. Panel Berkas Tanggapan karena itu selalu berbunyi "Belum ada berkas tanggapan", dan Riwayat Status selalu kosong, berapa pun isinya.

Terbukti di basis data: permohonan `PPID-FSTJ/20260831/0002` menyimpan `KEBERATAN.png` sebagai berkas tanggapan dan dua baris `permohonan_log_status`. Keduanya ada sejak awal; yang tidak ada adalah kunci yang dibaca panelnya.

Kedua pembacaan disamakan dengan kunci yang sungguh dikirim, dan satu tes menjaga keduanya sekaligus — termasuk menegaskan bahwa kunci camelCase memang **tidak pernah** ada, supaya kekeliruan yang sama tidak kembali dalam bentuk `?? data.tanggapanFiles`.

### 2. Lampiran masih bisa diubah setelah berkasnya diteruskan

Dropdown status sudah terkunci sejak putaran 80, tetapi lampirannya tidak. PPID Pelaksana yang sudah menekan Setujui tetap bisa Pilih Berkas Tanggapan, Pilih dari Arsip, dan membuang lampiran yang sudah ada — sehingga berkas yang sedang dipertimbangkan PPID bisa berubah isi di tengah pertimbangannya, dan yang akhirnya sampai ke pemohon bukan yang disetujui.

Aturannya sekarang satu kalimat: **lampiran hanya boleh disentuh pemegang giliran.** Dijaga server di `tambahTanggapanFile()` dan `hapusTanggapanFile()` lewat `bolehMemutus()` — aturan yang sama persis dengan yang menentukan siapa boleh memutus, jadi tidak ada dua definisi yang bisa berbeda — dan panel mengunci dirinya dengan jawaban yang sama.

Dua hal sengaja tetap terbuka:

- **Berkas yang jenjangnya sudah tuntas.** Itu jalur melampirkan dokumen susulan setelah permohonannya diserahkan, dan pemohon memang diberi tahu saat itu juga.
- **Berkas yang dikembalikan untuk diperbaiki.** Gilirannya balik ke PPID Pelaksana, jadi lampirannya terbuka lagi — memang itu yang diminta putusan Revisi.

Panelnya tidak sekadar kehilangan tombol: ada keterangan kenapa terkunci dan apa yang membukanya kembali. Tombol yang hilang tanpa sebab terbaca sebagai kerusakan.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Cms/PermohonanController.php` | `pastikanBolehUbahBerkas()` pada kedua endpoint lampiran |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php` | Dua tes baru |
| be-ppid | `components/PermohonanDetailDialog.tsx` | Kunci `tanggapan_files` / `log_status`; lampiran ikut terkunci |
| be-ppid | `components/BerkasTanggapanPanel.tsx` | `alasanTerkunci` — keterangan saat lampiran dikunci |

### Verifikasi

- api-ppid **72 lulus** (362 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.
- Dua tes baru: PPID Pelaksana ditolak menambah maupun menghapus lampiran setelah meneruskan, lalu boleh lagi setelah berkasnya dikembalikan untuk diperbaiki; rincian membawa `tanggapan_files` dan `log_status` dan tidak pernah membawa kunci camelCase.
- Dijalankan atas baris sungguhan (transaksi, lalu dibatalkan): rincian `PPID-FSTJ/20260831/0002` mengirim satu berkas tanggapan (`KEBERATAN.png`) dan dua baris riwayat; PPID Pelaksana yang mencoba melampirkan berkas baru ditolak dengan *"Berkas ini sedang menunggu putusan tahap 'Persetujuan PPID', jadi lampirannya tidak bisa diubah."*

---

## Status Pengerjaan (putaran 82 — langkah 100, arti status Diproses)

> "Kalau statusnya Diproses berarti permohonan sudah disetujui oleh PPID Pelaksana, tinggal PPID yang menentukan Setuju, Tolak, dan atau Revisi."

Aturan itu menyelesaikan cacatnya di akarnya, bukan di tampilannya. Sebelum ini sistem punya **dua** nama untuk satu keadaan — `diproses` dan `menunggu_approval` — dan jenjang persetujuannya membaca yang kedua. Berkas yang petugas tandai **Diproses** karena itu tetap dianggap belum diteruskan, dan PPID menunggu giliran yang tidak pernah datang.

### Kosakata status sesudah putaran ini

| Status | Artinya | Ada di meja |
| --- | --- | --- |
| `diajukan` / `diverifikasi` | Baru masuk, belum ditangani | PPID Pelaksana |
| **`diproses`** | **Sudah disetujui PPID Pelaksana; tinggal putusan PPID** | **PPID** |
| `revisi` | Dikembalikan PPID untuk diperbaiki | PPID Pelaksana |
| `disetujui` / `ditolak` / `ditolak_sebagian` / `selesai` / `kedaluwarsa` | Akhir | — |

`menunggu_approval` tidak dipasang lagi. Nilainya tetap dikenali di seluruh jalur — CHECK constraint, peta transisi, penghitung dashboard — supaya baris yang telanjur tersimpan tetap terbaca benar, tetapi tidak pernah lagi ditulis. Dua nama untuk satu keadaan memaksa petugas menghafal mana yang sedang berlaku, dan justru itu yang membuat cacat ini lolos.

Konsekuensinya: **revisi tidak boleh lagi mengembalikan berkas ke `diproses`.** Sebelumnya begitu, dan kini artinya berlawanan — berkas yang baru dikembalikan ke petugas akan tampil sama dengan berkas yang sudah menunggu putusan PPID. Statusnya menjadi `revisi`, nilai yang sudah lama diterima CHECK constraint `permohonan_informasi` tetapi belum pernah dipakai, jadi tidak ada migrasi.

### Jenjang dibuka di tahap yang sesuai statusnya

`AlurPersetujuan::mulai()` tidak lagi selalu membuka tahap pertama. Berkas berstatus Diproses lahir dengan tahap penerima **sudah disetujui** — berketerangan dari mana kesimpulannya diambil, tanpa nama pemutus yang dikarang — dan gilirannya langsung jatuh ke PPID beserta loncengnya.

Berkas yang jenjangnya telanjur dibuat dengan aturan lama ikut dibetulkan saat dibaca (`selaraskanDenganStatus()`): tahap penerima yang masih menahan berkas berstatus Diproses ditutup, tahap PPID dibuka, lonceng lama yang tidak lagi punya giliran dibuang. Satu arah saja dan hanya dari tahap pertama — jenjang tidak pernah dimundurkan, dan putaran yang memang baru dibuka setelah revisi (statusnya `revisi`, bukan `diproses`) tidak tersentuh. Tidak ada baris yang perlu disunting tangan.

### Dashboard ikut berpindah meja

`perlu_tindakan` selama ini menghitung `diproses` sebagai beban PPID Pelaksana. Di bawah aturan baru berkas itu justru sudah lepas dari mejanya, jadi angkanya menghitung `diajukan` + `diverifikasi` + `revisi`, sementara `menunggu_approval` — kartu "Menunggu persetujuan" — menghitung `diproses` (plus kosakata lamanya). Tanpa ini kedua kartu sama-sama salah: yang satu menghitung pekerjaan yang sudah selesai, yang lain menampilkan nol selamanya.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Support/AlurPersetujuan.php` | `STATUS_SUDAH_DITERUSKAN`, `mulai()` membuka tahap sesuai status, `selaraskanDenganStatus()`, `hapusLonceng()` |
| api-ppid | `Cms/PermohonanController.php`, `Cms/KeberatanController.php` | Meneruskan → `diproses`; revisi → `revisi`; `STATUS_DI_MEJA_PPID` |
| api-ppid | `Models/PermohonanInformasi.php`, `Models/KeberatanInformasi.php` | Peta transisi mengikuti arti baru |
| api-ppid | `AnalitikController.php` | Dua beban kerja dipisah menurut mejanya |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php`, `BerkasTanggapanTest.php` | Dua tes baru; kosakata disamakan |
| be-ppid | `lib/statusPengajuan.ts`, `lib/resources.ts` | Transisi & label disamakan; `revisi` punya label |
| be-ppid | `PpidDashboard.tsx` | Keterangan kartu mengikuti arti baru |
| fe-ppid | `PpidController.php`, `Support/AksesDokumen.php` | Label `revisi`; keterangan status diperbarui |

### Verifikasi

- api-ppid **70 lulus** (343 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.
- Dua tes baru: berkas berstatus Diproses langsung jadi giliran PPID dengan ketiga pilihan terbuka; jenjang lama yang tertahan di tahap penerima ikut dimajukan saat dibaca, tanpa berlipat.
- Dijalankan atas `PPID-FSTJ/20260831/0002` yang sungguhan (transaksi, lalu dibatalkan): dibuka PPID → tahap 1 tertutup, tahap 2 berjalan, `boleh_memutus` benar → Revisi menurunkannya ke `revisi` dan mengembalikannya ke Pelaksana → Pelaksana setuju → kembali `diproses`.

### Catatan

Permohonan yang tempo hari tertahan tidak lagi butuh langkah manual: begitu PPID membuka rinciannya, jenjangnya menyusul sendiri dan ketiga tombol — Setujui, Tolak, Kembalikan untuk diperbaiki — langsung tersedia.

---

## Status Pengerjaan (putaran 81 — langkah 100, keluhan kedua)

"Login sebagai PPID, di Detail Permohonan tidak ada aksi apapun."

### Alurnya sendiri sudah benar

Ditelusuri di basis data yang sesungguhnya, bukan di tes: permohonan `PPID-FSTJ/20260831/0002` punya jenjang lengkap, tahap 1 **Penerimaan PPID Pelaksana** masih `menunggu`, tahap 2 **Persetujuan PPID** belum kebagian giliran. PPID memang belum boleh berbuat apa-apa — berkasnya belum sampai kepadanya.

Dijalankan sampai habis atas baris yang sama (di dalam transaksi yang lalu dibatalkan), rantainya utuh: PPID Pelaksana menyetujui → status berpindah ke **Menunggu Persetujuan** dan lonceng sampai ke akun PPID → PPID membuka rincian dan `boleh_memutus` menjadi `true` → putusan **Kembalikan untuk diperbaiki** melempar berkasnya balik ke Pelaksana beserta loncengnya. Pemohon → PPID Pelaksana → PPID berjalan sebagaimana mestinya.

Hak aksesnya juga bukan penyebabnya: role `ppid-utama` memegang `can_view` + `can_approve` atas modul Permohonan maupun Keberatan, dan akun `ppid@foodstation.co.id` memang ber-role itu.

### Yang salah: halamannya tidak mengatakan apa-apa

Berkas tertahan di tahap 1, dan yang PPID lihat hanyalah rincian tanpa satu pun tombol. Panel status terkunci — memang seharusnya — dan panel jenjang menjawab **"Tahap yang berjalan bukan giliran role Anda"**, kalimat yang tidak menyebut tahap mana, siapa yang memegangnya, sejak kapan, atau apa yang ditunggu. Dari kursi PPID itu tidak terbaca sebagai antrean; itu terbaca sebagai halaman rusak.

Sisi lainnya sama diamnya. PPID Pelaksana yang membuka berkas untuk membacanya tidak menggulung sampai dasar dialog, jadi tidak pernah melihat bahwa gilirannyalah yang sedang ditunggu — dan berkas diam di tahap 1 tanpa ada yang merasa ditunggu. Itulah yang membuat berkas ini tidak pernah sampai ke PPID.

**Giliran karena itu diumumkan di kepala rincian**, di kedua dialog. Yang sedang kebagian membaca "Giliran Anda memutus berkas ini pada tahap …"; yang belum membaca "Menunggu putusan tahap …". Alert di panel jenjang ikut menyebut tahap, jabatan pemegangnya, jam masuk, dan batas waktunya.

### Dua cacat yang ikut ditemukan

**1. Putaran bertumpuk jadi satu daftar rata.** Tiap revisi membuat satu set langkah baru di atas yang lama, dan panel menerima seluruhnya sekaligus — berkas yang sekali dikembalikan tampil sebagai "1. Penerimaan, 2. Persetujuan, 1. Penerimaan, 2. Persetujuan". Urutan yang mundur di tengah daftar terbaca sebagai data rusak, bukan sebagai berkas yang berputar dua kali.

`AlurPersetujuan::putaran()` memecahnya di batas `urutan` yang kembali ke 1 — penanda yang sudah tersimpan tersirat, karena langkah selalu dibuat satu set penuh dari urutan 1, jadi tidak perlu kolom baru dan tidak perlu migrasi. Endpoint mengirim putaran berjalan sebagai `langkah`, sisanya sebagai `riwayat_putaran`, dan panel melipat yang lama ke dalam accordion.

**2. Lonceng giliran hidup lebih lama dari berkasnya.** Tiga pemberitahuan "menunggu persetujuan Anda" di basis data menunjuk permohonan 974 dan langkah 17 — keduanya sudah tidak ada. Diklik, hasilnya rincian yang tidak bisa dibuka; dan tidak ada cara membuangnya karena berkasnya sendiri sudah lenyap dari daftar.

`AlurPersetujuan::bersihkan()` sudah ada sejak lama tetapi **tidak pernah dipanggil dari mana pun**. Sekarang dipasang sebagai peristiwa model di kedua pengajuan: hapus lunak membuang loncengnya saja (`bersihkanLonceng()`) karena langkahnya masih bernilai sebagai jejak; hapus permanen membuang keduanya, karena `approval_pengajuan` menunjuk dua tabel sekaligus dan tidak bisa dipasangi foreign key.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Support/AlurPersetujuan.php` | `putaran()`, `bersihkanLonceng()`, `bersihkan()` ikut membuang lonceng |
| api-ppid | `Models/PermohonanInformasi.php`, `Models/KeberatanInformasi.php` | `booted()` — jenjang tidak hidup lebih lama dari berkasnya |
| api-ppid | `Concerns/MenanganiPersetujuan.php` | Balasan memisahkan putaran berjalan dari riwayatnya |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php` | Dua tes baru |
| be-ppid | `api/usePersetujuan.ts` | `riwayat_putaran`, `putaran`, `langkahBerjalan()`, `pemegangGiliran()` |
| be-ppid | `components/PersetujuanBerjenjang.tsx` | Putaran lama dilipat; alert menyebut pemegang, jam masuk, batas waktu |
| be-ppid | `components/PermohonanDetailDialog.tsx`, `components/KeberatanDetailDialog.tsx` | Giliran diumumkan di kepala rincian |

### Verifikasi

- api-ppid **68 lulus** (319 asersi), termasuk dua tes baru: putaran lama terpisah dari yang berjalan setelah satu revisi, dan lonceng ikut hilang saat berkasnya dihapus (lunak maupun permanen).
- fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` atas empat berkas yang diubah bersih.
- Rantai Pemohon → PPID Pelaksana → PPID → revisi → PPID Pelaksana dijalankan atas baris sungguhan di `ppiddb`, di dalam transaksi yang dibatalkan sesudahnya.

### Catatan

Tiga lonceng yatim yang sudah telanjur ada (`notifikasi` id 378–380) tidak ikut terbawa perbaikan ini — kodenya mencegah yang baru, bukan membersihkan yang lama. Buang sendiri kalau mengganggu:

```sql
DELETE FROM notifikasi
WHERE type = 'approval_menunggu'
  AND (data->>'approval_id')::int NOT IN (SELECT id FROM approval_pengajuan);
```

Permohonan `PPID-FSTJ/20260831/0002` masih menunggu PPID Pelaksana menekan **Setujui** pada panel Persetujuan Berjenjang; berkas itu ditangani sebelum jenjangnya ada, jadi tahap 1-nya belum pernah diputus. Setelah itu ia sampai ke PPID sendiri.

---

## Status Pengerjaan (putaran 80 — langkah 100)

Empat cacat yang dilaporkan berasal dari satu sebab: jenjang persetujuan tidak pernah dimulai.

### Sebabnya

Jenjang baru lahir ketika petugas memindahkan status ke **Menunggu Persetujuan** sendiri, lewat dropdown. Tidak ada satu pun yang melakukannya — dan memang tidak masuk akal untuk dituntut: PPID Pelaksana mengira pekerjaannya selesai setelah mengunggah berkas dan menulis keterangan, dan status berhenti di **Diproses**.

Basis datanya membenarkan laporan itu: `approval_pengajuan` **kosong sama sekali**, sementara ada permohonan berstatus `diproses` dan `diajukan`. Tidak ada jenjang berarti tidak ada giliran, dan tidak ada giliran berarti tidak ada satu pun lonceng yang dikirim ke PPID.

Dari situ ketiga keluhan lainnya mengikuti sendiri: dropdown status masih menawarkan seluruh perpindahan kepada petugas yang sudah selesai bagiannya; PPID tidak menerima pemberitahuan apa pun; dan pilihan yang tampil di formulir memang **Menunggu diproses / Ditolak / Kedaluwarsa** — daftar transisi status, bukan Setujui / Tolak / Revisi milik panel persetujuan yang tidak pernah aktif.

### Empat perbaikan

**1. Berkas yang masuk selalu punya jenjang.** `AlurPersetujuan::pastikanBerjalan()` membuat jenjangnya saat rincian persetujuan pertama kali dibaca — idempoten, dan melewati berkas yang statusnya sudah akhir supaya perkara yang ditutup sebelum alur berjenjang dipakai tidak terbuka lagi hanya karena dibaca orang.

Endpoint baca yang menulis memang tidak lazim, dan itu disengaja: pengajuan lahir di portal pemohon — aplikasi terpisah yang tidak memuat mesin persetujuan ini — jadi jenjangnya tidak bisa dibuat di tempat berkasnya dibuat. Membuatnya saat dibaca berarti tidak ada satu pun langkah manual yang bisa terlewat.

**2. Meneruskan berkas memindahkan statusnya.** Sebelumnya, selama masih ada jenjang di atasnya, putusan tidak mengubah status pengajuan sama sekali. Sekarang `terapkanLanjutPersetujuan()` memasangnya ke **Menunggu Persetujuan** lewat jalur yang sama dengan perpindahan status lain — jadi `permohonan_log_status` tetap terisi dan riwayatnya tidak berlubang.

**3. Dropdown status terkunci selama jenjang berjalan.** Penjagaan lama hanya menutup putusan akhir dari `menunggu_approval`; PPID Pelaksana yang sudah meneruskan berkasnya masih bisa menariknya kembali, menolaknya sendiri, atau menyatakannya kedaluwarsa — tiga hal yang seluruhnya melangkahi PPID. Sekarang **seluruh** perpindahan ditolak selama ada tahap yang menunggu. Super admin dikecualikan dengan alasan yang sama seperti pada `bolehMemutus()`: berkas yang macet karena rolenya kosong harus tetap bisa dibebaskan tanpa menyunting basis data.

Panel di be-ppid mengunci dirinya sendiri dengan aturan yang sama, dan membaca keadaan jenjang dari query yang **sama persis** dengan panel putusannya (`api/usePersetujuan.ts`). Ini bukan kerapian belaka: jenjang dibuat server saat dibaca, jadi `approvalLangkah` pada rincian bisa masih kosong ketika dialognya baru terbuka — dan panel yang menyimpulkan "belum ada jenjang" dari data basi itu akan membuka kunci yang justru seharusnya terpasang.

**4. Revisi mengembalikan berkas, bukan menjatuhkannya.** Dikembalikan untuk diperbaiki kini membuka putaran baru saat itu juga, sehingga PPID Pelaksana langsung menerima lonceng giliran dan siklusnya bisa berulang sebanyak yang diperlukan. Sebelumnya berkasnya berhenti di **Diproses** menunggu seseorang ingat mengajukannya lagi — persis keadaan yang membuat cacat ini tidak terlihat sejak awal.

### Yang PPID lihat sekarang

Rincian permohonan dan keberatan menampilkan **jalur pelayanan**, **jadwal layanan**, dan **keterangan petugas untuk pemohon** — tiga isian yang ditetapkan jenjang penerima. Berkas lampiran pemohon dan berkas tanggapan petugas memang sudah tampil sejak sebelumnya, tetapi tanpa ketiga isian itu penyetuju memutus tanpa tahu jalur mana yang dijanjikan ke pemohon, kapan ia diundang, dan keterangan apa yang sudah dikirimkan.

Formulir putusannya sendiri sudah benar sejak langkah 89 dan tidak diubah: **Setujui**, **Kembalikan untuk diperbaiki**, dan — hanya pada jenjang yang diberi hak menolak — **Tolak**. Jenjang penerima memang tidak diberi hak menolak; penolakan menurut UU KIP harus datang dari pejabat yang berwenang, disertai alasan tertulis.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Support/AlurPersetujuan.php` | `pastikanBerjalan()`, daftar status akhir, nomor keberatan |
| api-ppid | `Concerns/MenanganiPersetujuan.php` | Jenjang dibuat saat dibaca; hasil `lanjut` memindahkan status |
| api-ppid | `Cms/PermohonanController.php` | Kunci dropdown, `terapkanLanjutPersetujuan()`, revisi membuka putaran baru |
| api-ppid | `Cms/KeberatanController.php` | Sama, untuk keberatan |
| api-ppid | `Models/PermohonanInformasi.php`, `Models/KeberatanInformasi.php` | `menunggu_approval` jadi tujuan sah dari status awal |
| api-ppid | `tests/Feature/AlurPersetujuanPermohonanTest.php` | Baru |
| be-ppid | `api/usePersetujuan.ts` | Baru — satu sumber keadaan jenjang |
| be-ppid | `components/PersetujuanBerjenjang.tsx` | Memakai hook itu; teks keadaan kosong diperbaiki |
| be-ppid | `components/PermohonanStatusPanel.tsx`, `components/KeberatanTanggapanPanel.tsx` | Terkunci selama jenjang berjalan |
| be-ppid | `components/PermohonanDetailDialog.tsx`, `components/KeberatanDetailDialog.tsx` | Jalur, jadwal, keterangan petugas |
| be-ppid | `lib/statusPengajuan.ts`, `@i18n/kamusPpid.ts` | Transisi disamakan; istilah baru versi Inggris |

### Verifikasi

- **7 tes** `AlurPersetujuanPermohonanTest`: jenjang lahir tanpa langkah manual dan tidak berlipat bila dibaca dua kali; berkas yang sudah tuntas tidak dibuatkan jenjang baru; penerimaan PPID Pelaksana memindahkan status **dan** memberi tahu PPID; Pelaksana tidak bisa menolak, menarik kembali, maupun mengedaluwarsakan berkas yang sudah diteruskan; revisi mengembalikan berkas ke jenjang pertama beserta loncengnya lalu alurnya berjalan sampai putusan akhir; jenjang penerima tidak diberi hak menolak; PPID membaca jalur, jadwal, keterangan, dan kedua wadah berkas.
- Suite penuh: api-ppid **66 lulus** (298 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.

### Catatan

Dua permohonan yang sudah ada di basis data (`diajukan` dan `diproses`) belum punya jenjang. Keduanya akan mendapatkannya sendiri begitu rinciannya dibuka di panel — tidak ada migrasi data yang perlu dijalankan.

---

## Status Pengerjaan (putaran 79 — langkah 99)

Ikon panel disamakan dengan portal pemohon, dan tiga titik pantul bawaan template diganti maskot Food Station.

### Ikon tab dan taskbar

`be-ppid/public/favicon.ico` masih ikon bawaan Fuse (15 KB, tidak pernah diganti sejak template dipasang), sementara portal memakai `fe-ppid/public/assets/images/logo/favicon.ico` (93 KB). Di bilah tab dan taskbar keduanya karena itu tampak sebagai dua situs yang tidak berhubungan.

Berkas portal disalin apa adanya — bukan dibuat ulang — supaya keduanya benar-benar identik, dan tautannya disusun sama persis dengan yang ada di `fe-ppid/resources/views/layouts/app.blade.php`: `icon`, `shortcut icon`, dan `apple-touch-icon`. Yang terakhir menunjuk `logo_fs.png`, juga salinan dari portal.

Nama PWA di `manifest.json` sudah "PPID Food Station" sejak langkah 98 dan ikonnya menunjuk `/favicon.ico` yang kini sudah tergantikan, jadi tidak ada yang perlu diubah di sana.

### Pemuat

`loader-fs.gif` (272×250) dipasang di `public/assets/images/logo/` dan menggantikan tiga titik pantul di **dua** tempat yang harus seragam:

- **`index.html`** — splash yang menutupi jeda sebelum bundel React berjalan.
- **`FuseSplashScreen.tsx`** — splash yang menutupi jeda sesudahnya.

Keduanya kini memakai markup dan nama kelas yang sama; bentuk yang berbeda akan terlihat sebagai kedipan saat satu berganti ke yang lain. **`FuseLoading.tsx`** — pemuat di dalam halaman, dipakai 68 berkas — ikut memakai gambar yang sama dengan ukuran lebih kecil (112 px) dan tanpa latar sendiri, karena ia muncul di dalam panel yang sudah punya warnanya.

Latar splash diubah dari `#121212` bawaan template menjadi putih, dengan ragam gelap `#082217` mengikuti `prefers-color-scheme`. Maskotnya bergaris putih dan tenggelam di atas latar gelap bawaan itu.

Karena GIF-nya beranimasi sendiri, seluruh keyframes `fuse-bouncedelay` dan aturan `#spinner` dibuang. Ikut dibuang: penimpaan warna `& #spinner > div` di `FuseTheme.tsx`, yang sejak titik ini mewarnai markup yang sudah tidak ada.

`src/styles/splash-screen.css` **tidak diimpor dari mana pun** — `src/styles/index.css` tidak pernah memuatnya, dan memang tidak boleh: splash harus tergaya sebelum lembar gaya React sempat dimuat, jadi gayanya tinggal di `<style>` pada `index.html`. Isinya tetap disamakan supaya siapa pun yang menemukan berkas itu lebih dulu tidak menyalin gaya spinner yang markup-nya sudah dihapus.

### Berkas yang disentuh

| Berkas | Isi |
| --- | --- |
| `public/favicon.ico`, `public/assets/images/logo/favicon.ico` | Salinan ikon portal |
| `public/assets/images/logo/logo_fs.png` | Salinan, untuk `apple-touch-icon` |
| `public/assets/images/logo/loader-fs.gif` | Baru — maskot pemuat |
| `index.html` | Tautan ikon, markup & gaya splash |
| `@fuse/core/FuseSplashScreen/FuseSplashScreen.tsx` | Maskot, seragam dengan `index.html` |
| `@fuse/core/FuseLoading/FuseLoading.tsx` | Maskot ukuran kecil, tanpa latar |
| `@fuse/core/FuseTheme/FuseTheme.tsx` | Penimpaan warna spinner dibuang |
| `src/styles/splash-screen.css` | Disamakan (berkas ini tidak diimpor) |

### Verifikasi

- `npx tsc --noEmit` — bersih.
- `npx eslint` atas tiga berkas TSX yang diubah — bersih.
- Tidak ada lagi rujukan `#spinner` maupun `bounce1` di luar folder `documentation`.

### Catatan

Halaman contoh bawaan template di `(control-panel)/pages/authentication/*`, `apps/e-commerce`, dan `coming-soon` masih memakai `logo.svg` bawaan Fuse. Semuanya di luar rute yang dipakai — halaman masuk yang sebenarnya ada di `(public)/(auth)` — jadi dibiarkan. Bilang saja kalau mau ikut dibersihkan atau rutenya dicabut.

---

## Status Pengerjaan (putaran 78 — langkah 89, penyempurnaan)

Langkah 89 sudah berjalan sejak putaran-putaran sebelumnya; putaran ini menutup lima celah yang tersisa dan menandai tiap butirnya.

### Yang sudah benar sebelum putaran ini

- **89.1** — empat akun jabatan (`PenggunaPpidSeeder`), tiga anggota PPID Pelaksana berbagi satu role dan dibedakan `struktur_id`.
- **89.2** — modul Permohonan memakai endpoint gabungan `pengajuan` dengan dua kategori saja; `jalur_pelayanan` (Online/Langsung) tercatat sebagai kolomnya sendiri, bukan disimpulkan dari `cara_pengiriman`.
- **89.3** — alur permohonan lengkap sampai surel undangan jalur Langsung beserta alamat, kontak, dan jam layanan.
- **89.6** — tenggat tersimpan sebagai kolom (`batas_waktu_tanggapan`, `batas_waktu_awal`, `diperpanjang_pada`) dan dinilai `SlaLayanan::keadaan()`.

### Lima celah yang ditutup

**1. Keberatan tidak punya nomor (89.7).** Permohonan lahir dengan `kode_permohonan`; keberatan hanya dikenali lewat id tabelnya, dan di seluruh sistem dirujuk memakai nomor permohonan yang justru sedang dipersoalkan. Dua keberatan atas permohonan yang sama karena itu tampak sebagai baris kembar — di daftar panel, di lonceng, dan di surel.

Kolom `kode_keberatan` ditambahkan beserta trigger `fn_keberatan_kode`, berpola `KBT-FSTJ/<tanggal>/<urutan>`. Awalannya sengaja berbeda dari `PPID-FSTJ/`: keduanya berkas dengan tenggat berbeda (10 hari kerja vs 30 hari kalender), dan satu deret bersama akan menyamarkannya. Baris lama ikut dinomori memakai tanggal pengajuannya sendiri, bukan tanggal migrasi dijalankan. Nomornya kini tampil di daftar panel, rincian keberatan, daftar dan histori portal, lonceng panel, dan surel — serta bisa dicari di ketiganya.

**2. Satu alasan keberatan hilang, enam sisanya salah bunyi (89.4).** Pasal 35 UU KIP menyebut tujuh dasar; tabelnya baru menerima enam. Yang tidak ada — "tidak dipenuhinya permintaan informasi" — justru dasar yang dipakai ketika informasi diberikan sebagian, sehingga keberatan semacam itu terpaksa dititipkan ke alasan lain. Label keenam yang ada pun ringkasan bebas ("Informasi Tidak Disediakan"), bukan bunyi pasalnya ("Tidak Disediakannya Informasi Berkala").

CHECK constraint diperlebar ke tujuh nilai dan daftar labelnya disatukan: `KeberatanInformasi::JENIS` di api-ppid menjadi sumbernya, disalin ke fe-ppid dan be-ppid, dan `EmailPemohon` tidak lagi menyimpan salinan pribadinya.

**3. Alasan keberatan belum jadi data analisa (89.4).** Permintaannya sudah ditulis sejak awal — "dari alasan ini bisa dijadikan Data analisa di Dashboard" — tetapi `AnalitikController` hanya menghitung status dan jenis pemohon. Sebaran `alasan_keberatan` ditambahkan, mengembalikan ketujuh dasar termasuk yang bernilai nol supaya yang kosong terbaca sebagai nol, bukan sebagai belum diukur. Dashboard menampilkannya, dan modul Keberatan mendapat saringan dengan daftar yang sama — sebaran tidak berguna kalau barisnya sendiri tidak bisa disaring.

**4. Batas sengketa tidak pernah diisi (89.5, 89.6).** Kolom `batas_waktu_sengketa` ada sejak putaran sebelumnya tetapi tidak ada satu pun kode yang menulisinya; angka 14 hari kerja hanya disebut di badan surel. Sekarang diisi begitu keberatan ditanggapi (`selesai` atau `ditolak`), dihitung dari tanggal tanggapannya — bukan dari hari ini, supaya perpindahan status susulan tidak memperpanjang hak pemohon diam-diam. Rincian keberatan di panel menampilkannya bersama batas tanggapan.

**5. Satuan waktu keberatan salah di tiga tempat (89.5).** `SlaLayanan` menghitung 30 hari **kalender**, tetapi Dashboard, surel tanda terima, dan halaman Prosedur Keberatan sama-sama menulis "30 hari kerja" — sekitar dua minggu lebih longgar daripada yang sebenarnya berlaku. Ketiganya diperbaiki, dan halaman prosedur publik kini menyebut batas sengketa 14 hari kerja yang sebelumnya tidak ada.

Sekalian: fe-ppid tidak punya `SlaLayanan` sendiri — `addWeekdays(10)` dan `addDays(30)` berdiri sendiri di dua controller, sementara halaman prosedurnya menyebut angka ketiga. Salinan `App\Support\SlaLayanan` dibuat di fe-ppid dan dipakai keduanya.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `migrations/2026_08_31_000002_penomoran_keberatan_dan_alasan.php` | Baru — `kode_keberatan` + trigger, CHECK tujuh alasan |
| api-ppid | `Models/KeberatanInformasi.php` | `JENIS` (tujuh dasar Pasal 35) |
| api-ppid | `Cms/KeberatanController.php` | `batas_waktu_sengketa`, nomor ikut dicari & diurutkan |
| api-ppid | `Cms/PengajuanLayananController.php` | Baris membawa `kode` masing-masing, bukan selalu nomor permohonan |
| api-ppid | `AnalitikController.php` | Sebaran `alasan_keberatan` |
| api-ppid | `Support/EmailPemohon.php` | Nomor keberatan, satuan tenggat, label alasan dari satu sumber |
| api-ppid | `tests/Feature/PenomoranKeberatanTest.php` | Baru |
| fe-ppid | `Support/SlaLayanan.php` | Baru — tenggat di satu tempat |
| fe-ppid | `Models/KeberatanInformasi.php` | `JENIS` disamakan |
| fe-ppid | `Akun/KeberatanController.php` | `refresh()` sebelum lonceng & surel, nomor pada tanda terima, pencarian |
| fe-ppid | `Akun/PermohonanController.php`, `Akun/HistoriController.php` | SlaLayanan, pencarian nomor keberatan |
| fe-ppid | `PpidController.php` | Satuan waktu prosedur keberatan + batas sengketa |
| fe-ppid | `Notifications/StatusLayanan.php`, `Support/NotifikasiAdmin.php` | Nomor keberatan pada pemberitahuan |
| fe-ppid | `views/akun/keberatan/index.blade.php`, `views/akun/histori.blade.php` | Nomor keberatan + nomor induknya |
| fe-ppid | `lang/en.json` | Label alasan baru, kalimat prosedur, nomor keberatan |
| fe-ppid | `tests/Feature/PenomoranKeberatanPortalTest.php` | Baru |
| be-ppid | `lib/statusPengajuan.ts` | `JENIS_KEBERATAN` tujuh dasar |
| be-ppid | `lib/resources.ts`, `lib/types.ts`, `components/ResourceListPage.tsx` | Kolom `kode`, kolom & saringan alasan, tipe kolom `map` |
| be-ppid | `components/KeberatanDetailDialog.tsx` | Nomor keberatan, dua tenggat |
| be-ppid | `PpidDashboard.tsx` | Sebaran alasan, satuan tenggat keberatan |
| be-ppid | `@i18n/kamusPpid.ts` | Istilah baru versi Inggris |

### Verifikasi

- **7 tes** `PenomoranKeberatanTest` (api-ppid): nomor lahir berawalan `KBT-FSTJ/` dan berbeda dari nomor permohonan, berderet unik, tampil dan bisa dicari di daftar gabungan, alasan ketujuh diterima, batas sengketa terisi 14 hari kerja sejak tanggapan, analitik memuat ketujuh dasar.
- **4 tes** `PenomoranKeberatanPortalTest` (fe-ppid): keberatan portal lahir bernomor dan nomornya ikut ke tanda terima, alasan ketujuh diterima sementara alasan di luar daftar ditolak, tenggat dihitung hari kalender, nomor tampil di daftar.
- `NotifikasiAdminTest` diperluas: lonceng panel memuat kedua nomor.
- Suite penuh: api-ppid **59 lulus** (245 asersi), fe-ppid **88 lulus** (316 asersi). be-ppid `tsc --noEmit` bersih, `eslint` tanpa galat.

### Catatan

Angka 30 hari untuk tanggapan keberatan diperlakukan sebagai **hari kalender**, mengikuti bunyi permintaan pada butir 89.5 ("paling lambat 30 hari sejak diregistrasinya"). Pasal 36 UU KIP menyebut "30 hari kerja". Perhitungan kalender lebih ketat — petugas ditagih lebih awal, bukan terlambat — jadi dibiarkan demikian; bilang saja kalau mau diubah ke hari kerja, satu tetapan di `SlaLayanan::KEBERATAN_HARI` beserta satuannya yang perlu diganti.

---

## Status Pengerjaan (putaran 77 — langkah 98)

Label teks "PPID Admin" di samping logo dibuang di seluruh be-ppid. Yang tersisa hanya lambang perusahaan yang sudah dipasang pada langkah 85.

### Di mana saja label itu muncul

Tujuh tempat, dan tidak semuanya di sebelah logo:

- **Sidebar/header** — `components/theme-layouts/components/Logo.tsx`. Blok `logo-text` berisi dua baris bertumpuk, "PPID" besar dan "Admin" kecil di bawahnya. Dihapus seluruhnya beserta impor `Typography` yang jadi tak terpakai. Kelas `logo-text` tidak dirujuk CSS mana pun di luar berkas ini (satu-satunya pemakai lain, `DocumentationSidebarHeader.tsx`, punya markup sendiri), jadi tidak ada gaya yang menggantung.
- **Kop halaman auth** — `JudulAuth.tsx`, `SignInPageTitle.tsx`, `SignOutPageTitle.tsx`, `SignUpPageTitle.tsx`. Keempatnya memuat baris `<Typography>` yang sama persis di samping `<img>` logo. Baris itu dibuang; pembungkus `flex items-center gap-3` dibiarkan supaya penempatan logonya tidak bergeser.
- **Footer panel** — `AppFooterContent.tsx`. Sisi kiri hanya berisi "PPID Admin". Setelah dibuang, `justify-between` diganti `justify-end` supaya baris hak cipta tidak melompat ke kiri.
- **Panel sambutan halaman masuk** — `AuthPagesMessageSection.tsx`. Judul besarnya berbunyi "Selamat datang di / PPID Admin". Baris keduanya dibuang dan baris pertama dipendekkan jadi "Selamat datang"; paragraf keterangan di bawahnya tetap.

### Judul tab dan nama PWA

`index.html` dan `public/manifest.json` juga memakai "PPID Admin" — sebagai judul tab, deskripsi meta, dan nama aplikasi bila panelnya dipasang sebagai PWA. Ketiganya tidak boleh kosong, jadi diganti **"PPID Food Station"**, mengikuti penamaan yang sudah dipakai portal pemohon ("Portal Resmi PPID Food Station", langkah 90). Bilang saja kalau mau nama lain.

Sekalian: splash screen di `index.html` masih menunjuk `assets/images/logo/logo.svg`, tanda bawaan template yang sudah diganti di tempat lain pada langkah 85. Diarahkan ke `logo-fstj.png` supaya layar pertama yang dilihat orang tidak berbeda dari logo di dalam panel.

### Berkas yang disentuh

| Berkas | Isi |
| --- | --- |
| `components/theme-layouts/components/Logo.tsx` | Blok `logo-text` + impor `Typography` dihapus |
| `components/theme-layouts/components/AppFooterContent.tsx` | Label kiri dihapus, `justify-end` |
| `(auth)/components/ui/JudulAuth.tsx` | Baris label dihapus |
| `(auth)/components/ui/SignInPageTitle.tsx` | Baris label dihapus |
| `(auth)/components/ui/SignOutPageTitle.tsx` | Baris label dihapus |
| `(auth)/components/ui/SignUpPageTitle.tsx` | Baris label dihapus |
| `(auth)/components/ui/AuthPagesMessageSection.tsx` | Judul jadi "Selamat datang" |
| `index.html` | Judul, deskripsi, komentar, logo splash |
| `public/manifest.json` | `name` dan `short_name` |

### Verifikasi

- `npx tsc --noEmit` — bersih.
- `npx eslint` atas tujuh berkas TSX yang diubah — bersih.

---

## Status Pengerjaan (putaran 76 — langkah 97)

Memilih berkas tidak lagi berarti mengirimkannya. Ada langkah Simpan, dan pemohon baru diberi tahu ketika permohonannya benar-benar diserahkan.

### Yang dilaporkan, dan apa yang sebenarnya terjadi

Benar: satu klik unggah sudah mengirim notifikasi. Panel memanggil `tanggapan-files` seketika berkasnya terunggah, dan API menulis notifikasi portal saat baris lampirannya tersimpan. Tidak ada tahap Simpan di antara keduanya — pilih berkas, pemohon diberi tahu.

Menelusuri alurnya memunculkan cacat yang lebih dalam, dan keduanya berasal dari anggapan yang sama:

**Waktunya salah.** Berkas tanggapan dilampirkan PPID Pelaksana pada tahap penerimaan — saat menyiapkan jawaban, sementara PPID belum memutus apa pun. Memberitahukannya pada saat itu berarti menjanjikan dokumen yang belum tentu jadi diberikan; berkasnya bahkan masih bisa dicabut atau permohonannya ditolak setelah itu.

**Tujuannya tidak ada.** Notifikasinya berbunyi "Petugas melampirkan N berkas tanggapan…" dan menaut ke rincian permohonan di portal — padahal halaman itu **tidak pernah menampilkan berkas tanggapan sama sekali**. Tidak ada daftarnya, tidak ada jalur unduhnya. Pemohon diberi tahu tentang dokumen yang tidak bisa ia buka di mana pun, sejak fitur ini ada.

### Tiga perbaikan yang saling mengunci

**1. Ada langkah Simpan (be-ppid).** Berkas yang dipilih naik ke penyimpanan supaya punya alamat, lalu menunggu di daftar "Belum disimpan" — bisa dilepas satu-satu — sampai petugas menekan **Simpan berkas tanggapan**. Sebelum itu tidak ada yang tercatat pada permohonan. Berkas dari Arsip Dokumen masuk ke antrean yang sama, jadi keduanya disimpan sekali jalan.

**2. Pemberitahuannya menunggu penyerahan (api-ppid).** Melampirkan berkas hanya memberi tahu pemohon bila permohonannya memang sudah berstatus **Disetujui** atau **Selesai**. Untuk berkas yang dilampirkan lebih awal, pemberitahuannya menyusul saat status berpindah ke sana — sekali saja, karena Disetujui → Selesai tidak mengulanginya. Penolakan tidak ikut: yang disampaikan di sana alasannya, dan itu sudah dibawa notifikasi status.

**3. Berkasnya kini punya tempat (fe-ppid).** Rincian permohonan di portal mendapat bagian **Berkas Tanggapan** beserta tautan unduhnya, dengan dua penjagaan di server: berkasnya harus milik permohonan akun itu, dan permohonannya harus sudah diserahkan. Tanpa penjagaan kedua, dokumen yang masih disiapkan bisa ditarik dengan menebak nomor berkasnya.

Daftar status "sudah diserahkan" ditulis di dua sisi (`PermohonanController::statusTerbukaUntukPemohon()` dan `PermohonanInformasi::tanggapanTerbukaUntukPemohon()`) dan sengaja sama persis: berkas yang terlihat tanpa pemberitahuan sama membingungkannya dengan pemberitahuan atas berkas yang tak bisa dibuka.

### Sisa alurnya, hasil pemeriksaan

- **Perpindahan status** — surel dan lonceng sudah dijaga tidak berangkat bila statusnya tidak benar-benar berubah, dan keduanya menunggu commit sehingga transaksi yang batal tidak menyisakan pemberitahuan.
- **Surel jalur pelayanan** pada tahap penerimaan tetap berangkat saat itu juga, dan memang seharusnya: isinya undangan kehadiran (jalur Langsung) atau pemberitahuan cara pelayanan (jalur Online) — bukan penyerahan dokumen. Kalimatnya pun sudah benar: dokumen "dapat diunduh melalui Portal Pemohon setelah tersedia", dan sejak putaran ini kalimat itu tidak lagi menunjuk halaman kosong.
- **Notifikasi status `selesai`** berbunyi "Tanggapan dapat dilihat di portal" — baru sekarang benar-benar bisa.
- **Catatan internal** petugas tetap tidak pernah ikut ke pemohon; yang dioper hanya alasan penolakan dan tanggapan atasan.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Cms/PermohonanController.php` | Pemberitahuan berkas menunggu status terbuka; `statusTerbukaUntukPemohon()` |
| api-ppid | `tests/Feature/BerkasTanggapanTest.php` | Baru |
| api-ppid | `tests/Feature/AlurLayananPpidTest.php` | Menyesuaikan aturan baru |
| be-ppid | `components/BerkasTanggapanPanel.tsx` | Antrean "Belum disimpan" + tombol Simpan |
| be-ppid | `@i18n/kamusPpid.ts` | Istilah baru versi Inggris |
| fe-ppid | `Models/PermohonanTanggapanFile.php` | Baru |
| fe-ppid | `Models/PermohonanInformasi.php` | Relasi berkas + `tanggapanTerbukaUntukPemohon()` |
| fe-ppid | `Akun/PermohonanController.php` | Unduhan berkas tanggapan milik sendiri |
| fe-ppid | `routes/akun.php` | Route unduhan |
| fe-ppid | `views/akun/permohonan/show.blade.php` | Bagian Berkas Tanggapan |
| fe-ppid | `lang/en.json` | Teks baru versi Inggris |
| fe-ppid | `tests/Feature/BerkasTanggapanPortalTest.php` | Baru |

### Verifikasi

- **4 tes** `BerkasTanggapanTest` (api-ppid): melampirkan saat masih `diproses` tidak memberi tahu siapa pun; pemberitahuannya menyusul tepat pada putusan akhir dan tidak terulang saat Disetujui → Selesai; berkas yang dilampirkan setelah diserahkan tetap diberitahukan saat itu juga; penolakan tidak memberitahukan berkas.
- **6 tes** `BerkasTanggapanPortalTest` (fe-ppid): berkas tampil hanya setelah diserahkan, unduhan ditolak selama belum diserahkan, berkas akun lain ditolak, pemiliknya bisa mengunduh, dan berkas yang hilang di disk dijawab 404.
- Suite penuh: api-ppid **52 lulus** (221 asersi), fe-ppid **84 lulus** (296 asersi).
- `tsc --noEmit` be-ppid bersih; ESLint 0 error pada berkas yang disentuh.

### Perlu diketahui

Berkas yang sudah terunggah tetapi tidak jadi disimpan tetap tertinggal di penyimpanan — tidak tercatat pada permohonan mana pun dan tidak masuk Arsip Dokumen (pencatatan arsip terjadi saat disimpan). Membersihkannya perlu perintah tersendiri; belum dibuat.

### Yang belum dikerjakan

Tidak berubah: dua baris notifikasi ganda lama di lonceng panel, dua notifikasi verifikasi lama yang tautannya masih ke `/akun`, tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja. `npm run build` be-ppid masih tersandung `@tiptap/pm`.
---


## Status Pengerjaan (putaran 75 — langkah 95 & 96)

Arsip Dokumen lahir sebagai modul sendiri: berkas diunggah sekali, lalu dilampirkan ke permohonan mana pun tanpa unggahan kedua. Sorotan survei kepuasan masuk ke beranda Portal Pemohon.

### Langkah 95 — kenapa unggahannya ditolak

Endpoint unggahnya sendiri tidak bermasalah: diuji dengan token akun **PPID Pelaksana**, `POST /v1/uploads` untuk berkas PDF menjawab **201**. Hak aksesnya juga bukan penyebabnya — jalur unggah hanya menuntut token, dan jalur lampirannya (`permohonan/{id}/tanggapan-files`) menuntut hak `Ubah` yang memang sudah dipegang PPID Pelaksana sejak langkah 91.

Yang menolak adalah **daftar jenis berkasnya**. Panel yang dibuat pada langkah 94 mengirim semua unggahan sebagai jenis `dokumen_gambar`, dan jenis itu di API hanya menerima PDF, JPG, PNG, dan WEBP. Tanggapan petugas justru kerap berupa dokumen Office — begitu berkas `.docx` atau `.xlsx` dipilih, server menjawab 422 "Jenis berkas tidak diizinkan" dan yang terlihat hanyalah unggahan yang tidak jadi.

Sekarang jenisnya ditentukan per berkas: PDF dan gambar tetap lewat `dokumen_gambar`, sisanya lewat `dokumen` yang memang menerima doc/docx/xls/xlsx/ppt/pptx/csv/txt. Daftar `accept` pada pemilih berkasnya ikut diperlebar supaya berkas yang sah tidak lagi tersaring sebelum sempat dikirim.

Satu penyebab kedua ikut ditutup: berkas di atas 20 MB ditolak web server sebelum sampai ke Laravel, dan jawabannya bukan JSON — pesannya jadi "gagal" tanpa alasan. Ukurannya kini diperiksa lebih dulu di panel, dengan pesan yang menyebut nama berkas dan batasnya.

Yang tidak bisa dipastikan: peramban Anda tidak ikut diperiksa, jadi bila kegagalannya ternyata bukan salah satu dari dua hal di atas, pesan galat yang sekarang tampil akan menyebutkan alasannya dengan jelas — itu yang sebelumnya tidak ada.

### Langkah 95 — Arsip Dokumen

Modul baru `Arsip Dokumen` (menu **Layanan**), dengan tabelnya sendiri:

- **Semua yang dilampirkan ikut tercatat.** Setiap berkas yang masuk lewat `tanggapan-files` dicatat ke arsip oleh API — bukan oleh panel — sehingga jalur mana pun ikut terkena. Pencatatannya dikunci pada `path_file` yang unik, jadi berkas yang memang berasal dari arsip tidak menghasilkan baris kedua, dan melampirkan berkas yang sama ke sepuluh permohonan tetap menyisakan satu baris arsip.
- **Melampirkan tanpa mengunggah.** Dialog rincian permohonan mendapat tombol "Pilih dari Arsip": daftar arsip aktif, bisa dicari, pilih beberapa sekaligus, lampirkan. Yang dikirim hanya `path_file`-nya — tidak ada berkas yang berpindah, dan satu berkas fisik dipakai bersama banyak permohonan.
- **Gagal mencatat arsip tidak membatalkan lampirannya.** Yang pokok adalah berkasnya sampai ke pemohon; kegagalan pencatatan hanya dicatat di log.

Hak aksesnya modul sendiri, bukan menumpang Permohonan: isinya dokumen milik lembaga, dan siapa yang boleh membuang isi arsip tidak sama dengan siapa yang menangani permohonan. PPID Pelaksana boleh melihat, menambah, dan menyunting; menghapus hanya PPID — baris yang hilang membuat dokumennya tidak lagi bisa dipilih petugas lain. Atasan PPID tidak diberi akses.

Menghapus baris arsip **tidak** menghapus berkasnya di disk dan tidak mencabut lampiran yang sudah telanjur diberikan: pemohon yang sudah menerima dokumen berhak tetap bisa mengunduh apa yang dulu diberikan kepadanya.

### Langkah 96 — sorotan survei di beranda pemohon

Fitur surveinya sudah ada sejak lama: formulir 1–5 beserta saran, satu permohonan sekali nilai, dan tombol "Isi Survei" pada daftar serta rincian permohonan. Yang belum ada adalah **sorotannya di beranda** — kalau pemohon tidak kebetulan membuka daftar permohonannya, ajakan menilai itu tidak pernah terlihat.

Beranda portal kini menghitung permohonan yang sudah tuntas tetapi belum dinilai, lalu menampilkannya sebagai satu kartu berisi tiga teratas beserta tombolnya; sisanya dirujuk ke daftar permohonan supaya kartunya tidak memanjang mendorong isi beranda yang lain.

Tidak ada surel maupun lonceng untuk ini, sesuai yang diminta ("highlight saja"): mengejar pemohon dengan pemberitahuan untuk sesuatu yang sukarela hanya menambah bising pada kotak masuk yang sudah dipakai memberitahu jalannya permohonan.

Kata "tuntas" dipakai, bukan "selesai": permohonan yang ditolak pun sudah selesai ditangani dan tetap boleh dinilai — mutu layanan pada kasus itu justru penting diketahui.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `migrations/2026_08_31_000001_create_arsip_dokumen_table.php` | Baru; `path_file` unik |
| api-ppid | `Models/ArsipDokumen.php` | Baru; `catatSekali()` idempoten |
| api-ppid | `Cms/ArsipDokumenController.php` | Baru; CRUD modul `arsip-dokumen` |
| api-ppid | `Cms/PermohonanController.php` | Lampiran tanggapan ikut tercatat ke arsip |
| api-ppid | `routes/api.php` | Route `arsip-dokumen` |
| api-ppid | `seeders/ModulSistemSeeder.php` | Modul + hak akses `arsip-dokumen` |
| api-ppid | `tests/Feature/ArsipDokumenTest.php` | Baru |
| be-ppid | `components/BerkasTanggapanPanel.tsx` | Jenis unggahan per berkas, batas ukuran, tombol Pilih dari Arsip |
| be-ppid | `components/ArsipDokumenPicker.tsx` | Baru; pemilih berkas arsip |
| be-ppid | `ppid/lib/resources.ts` | Modul Arsip Dokumen |
| be-ppid | `ppid/lib/navigation.ts` | Entri menu pada grup Layanan |
| be-ppid | `@i18n/kamusPpid.ts` | Istilah baru versi Inggris |
| fe-ppid | `Akun/DashboardController.php` | Menghitung permohonan tuntas yang belum dinilai |
| fe-ppid | `views/akun/partials/alert-survei.blade.php` | Baru; kartu sorotan |
| fe-ppid | `views/akun/dashboard.blade.php` | Memuat kartu sorotan |
| fe-ppid | `lang/en.json` | Teks sorotan versi Inggris, termasuk bentuk jamaknya |
| fe-ppid | `tests/Feature/PortalDashboardTest.php` | Empat tes sorotan survei |

### Verifikasi

- **6 tes** `ArsipDokumenTest`: PDF dan `.docx` sama-sama terunggah oleh PPID Pelaksana sedangkan `.docx` lewat jenis `dokumen_gambar` memang ditolak 422 (sebab kegagalan yang dilaporkan), lampiran tanggapan tercatat di arsip, berkas sama tidak menggandakan barisnya, PPID Pelaksana bisa membaca dan menambah arsip, `path_file` kembar ditolak, dan Atasan PPID dijawab 403.
- **4 tes** sorotan survei di `PortalDashboardTest`: permohonan tuntas yang belum dinilai muncul (yang masih berjalan tidak), permohonan yang sudah dinilai hilang dari sorotan, tanpa permohonan tuntas tidak ada sorotan, dan bentuk jamak versi Inggrisnya benar.
- Suite penuh: api-ppid **48 lulus** (198 asersi), fe-ppid **78 lulus** (286 asersi).
- `tsc --noEmit` be-ppid bersih; ESLint 0 error pada berkas yang disentuh.
- Migrasi dan `ModulSistemSeeder` sudah dijalankan: tabel `arsip_dokumen` ada, dan hak aksesnya tercatat untuk super-admin, PPID Pelaksana, serta PPID.

### Yang belum dikerjakan

Tidak berubah: dua baris notifikasi ganda lama di lonceng panel, dua notifikasi verifikasi lama yang tautannya masih ke `/akun`, tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja. `npm run build` be-ppid masih tersandung `@tiptap/pm` seperti dicatat pada putaran 72; pemeriksaan tampilan lewat `npm run dev`.
---


## Status Pengerjaan (putaran 74 — langkah 94)

Menangani satu pengajuan kini terjadi di satu tempat: rinciannya. Menu "Ubah status" di tabel dilepas, dan mengklik barisnya langsung membuka dialog Detail & Verifikasi — berlaku untuk seluruh modul panel.

### Ribetnya datang dari keputusan yang terpisah dari berkasnya

Sebelum ini satu pengajuan punya tiga pintu yang berbeda, semuanya lewat menu tiga titik: "Detail & verifikasi" untuk membaca, "Ubah status" untuk memindahkan status, dan "Tanggapan & status" untuk keberatan. Putusan persetujuan sendiri ada di dalam Detail, sementara perpindahan status ada di dialog lain yang tidak menampilkan apa pun tentang berkasnya — petugas memilih status dari baris tabel, tanpa melihat isi permohonan yang sedang ia pindahkan.

Sekarang tinggal satu pintu. Dialog rincian memuat, berurutan: identitas pemohon, isi permohonan, penanganan, berkas lampiran, **Berkas Tanggapan**, **Persetujuan Berjenjang**, **Ubah Status**, lalu riwayat status. Dialog status dan dialog tanggapan yang berdiri sendiri dihapus — isinya pindah utuh menjadi panel di dalam rincian, jadi tidak ada dua salinan aturan yang harus dijaga tetap sama.

Hak aksesnya ikut pindah, tidak melonggar: panel status dan tanggapan menuntut hak `Ubah`, putusan persetujuan menuntut hak `Setujui`, dan membaca rinciannya tetap cukup dengan hak `Lihat`. Role yang tidak berhak melihat panelnya sebagai keterangan, bukan formulir yang menolak saat disimpan.

### Panel yang selama ini disebut tapi tidak ada

Jenjang penerima yang menyetujui permohonan jalur **Online** selalu diberi tahu: "Unggah dokumen yang diminta lewat panel Berkas Tanggapan pada detail pengajuan ini." Panel itu tidak pernah ada. Dialog rinciannya hanya menampilkan daftar berkas tanggapan, tanpa satu pun jalan mengunggah — endpoint `permohonan/{id}/tanggapan-files` hanya bisa dipanggil langsung ke API.

Panelnya sekarang ada: unggah beberapa berkas sekaligus (PDF/gambar), lihat, dan hapus. Unggahannya dikirim langsung, bukan ditahan sampai tombol simpan, karena API memang menulis notifikasi ke lonceng pemohon begitu barisnya tersimpan — tidak ada keadaan setengah jadi yang perlu dijaga. Berkasnya dikirim satu per satu ke `uploads`, bukan serentak: unggahan besar yang ditembakkan bersamaan lebih sering kena batas ukuran dan waktu di server.

### Baris keberatan di modul Permohonan tidak pernah bisa dibuka

Ketahuan saat merapikan penyusunan dialognya. Sejak langkah 89 daftar Permohonan memuat dua kategori, dan memilih "Detail" pada baris keberatan memang menyetel `detailKeberatan` — tetapi `KeberatanDetailDialog` hanya dirender di cabang `modulKeberatan`. Di modul Permohonan komponennya tidak ada di layar sama sekali, jadi yang terjadi: menu tertutup, tidak ada yang terbuka, tanpa pesan galat.

Ini juga yang membuat tautan notifikasi keberatan dari langkah 92 (`/ppid/permohonan?detail=…&jenis=keberatan`) berujung diam. Dialognya kini ikut dirender di modul Permohonan.

### Klik baris membuka rinciannya

`ResourceListPage` menerima `onRowClick`. Modul yang rinciannya berupa dialog — Permohonan, Keberatan, Pemohon — mengisinya dengan dialog masing-masing; modul lain memakai perilaku bawaannya, yaitu membuka formulir **Ubah** selama rolenya memang boleh menyunting. Modul baca-saja tidak ikut berubah: barisnya tidak bisa diklik dan kursornya tetap biasa.

Klik pada isi yang punya maksud sendiri tidak ikut membuka baris — kotak centang pemilihan, tombol menu aksi, dan tautan berkas disaring lewat `closest()`. Tanpa itu, mencentang satu baris untuk dihapus malah membuka dialognya.

Menu tiga titik tetap ada. Ia menampung tindakan lain (Hapus, Atur hak akses, Atur tahap) dan tetap jadi jalan bagi papan ketik; yang berubah hanya jalan tercepatnya.

### Satu cacat lama yang ikut ditutup

Tabel modul Permohonan dilayani endpoint gabungan `pengajuan` (langkah 89), sedangkan penyimpanan status, putusan persetujuan, dan berkas tanggapan hanya membatalkan cache `permohonan`/`keberatan`. Akibatnya tabel di belakang dialog masih menunjukkan status lama sampai halamannya dimuat ulang. Ketiganya kini ikut membatalkan `pengajuan`.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| be-ppid | `components/PermohonanStatusPanel.tsx` | Baru; isi dialog status lama, jadi panel di dalam rincian |
| be-ppid | `components/KeberatanTanggapanPanel.tsx` | Baru; isi dialog tanggapan lama, jadi panel di dalam rincian |
| be-ppid | `components/BerkasTanggapanPanel.tsx` | Baru; unggah, buka, dan hapus berkas tanggapan |
| be-ppid | `components/PermohonanStatusDialog.tsx` | Dihapus |
| be-ppid | `components/KeberatanTanggapanDialog.tsx` | Dihapus |
| be-ppid | `components/PermohonanDetailDialog.tsx` | Memuat panel status dan panel berkas tanggapan |
| be-ppid | `components/KeberatanDetailDialog.tsx` | Memuat panel tanggapan & status |
| be-ppid | `components/PersetujuanBerjenjang.tsx` | Ikut menyegarkan daftar gabungan |
| be-ppid | `components/ResourceListPage.tsx` | `onRowClick` + klik baris membuka rinciannya |
| be-ppid | `PpidResourcePage.tsx` | Menu baris tinggal "Detail & verifikasi"; dialog keberatan ikut dirender di modul Permohonan |
| be-ppid | `@i18n/kamusPpid.ts` | Istilah panel baru versi Inggris |
| api-ppid | `tests/Feature/AlurLayananPpidTest.php` | PPID Pelaksana melampirkan berkas tanggapan |

### Verifikasi

- **1 tes baru** di api-ppid: PPID Pelaksana melampirkan berkas tanggapan lewat `permohonan/{id}/tanggapan-files` — **201**, dan notifikasi `permohonan_tanggapan_file` untuk pemohonnya tertulis satu. Ini menjaga hak `Ubah` yang dipakai panel barunya.
- Suite penuh: api-ppid **42 lulus** (177 asersi), fe-ppid **74 lulus** (272 asersi).
- `tsc --noEmit` be-ppid bersih; ESLint pada berkas yang disentuh **0 error** (sisa peringatan format adalah bawaan berkas lama yang tidak ikut diubah).

### Perlu diketahui

Perubahan putaran ini seluruhnya di panel; tidak ada endpoint API yang berubah. Pemeriksaan tampilannya lewat `npm run dev` — `npm run build` masih tersandung `@tiptap/pm` seperti dicatat pada putaran 72.

### Yang belum dikerjakan

Tidak berubah: dua baris notifikasi ganda lama di lonceng panel, dua notifikasi verifikasi lama yang tautannya masih ke `/akun`, tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja.
---


## Status Pengerjaan (putaran 73 — langkah 93)

Notifikasi hasil Verifikasi Data Diri kini selalu mengantar ke `/akun/pengaturan/data-pemohon`, apa pun keputusannya.

### Tautannya bercabang tiga, dan dua di antaranya salah tujuan

`NotifikasiPortal::hasilVerifikasiData()` memilih tautan berdasarkan keadaan:

```php
'link' => $bolehKirimUlang ? '/akun/pengaturan/data-pemohon' : '/akun',
```

Hanya penolakan yang masih punya sisa kesempatan yang dibawa ke halaman Data Pemohon. Yang **disetujui** dan yang **sudah kehabisan kesempatan** dilempar ke `/akun`. Alasan aslinya masuk akal untuk keadaan kedua — formulir yang tidak lagi menerima kiriman memang tidak perlu dibuka — tetapi akibatnya sama untuk keduanya: pemohon mengklik pemberitahuan lalu mendarat di dasbor yang tidak menyebut hasil pemeriksaannya sama sekali. Isi notifikasi yang baru saja ia buka justru hilang.

Sekarang ketiga keadaan menuju satu halaman yang sama. Halaman itu memang sudah menangani ketiganya sejak awal: lencana status, catatan petugas, sisa kesempatan, formulir perbaikan bila masih boleh dikirim ulang, isian terkunci setelah terverifikasi, dan tombol kirim tertutup setelah kesempatannya habis. Jadi tidak ada cabang yang berujung buntu — yang ada justru satu tempat yang menjawab "lalu bagaimana keadaan saya sekarang".

### Yang tidak ikut diubah

Tombol pada **surel** hasil verifikasi dibiarkan seperti sebelumnya: yang disetujui diberi tombol "Buka Portal Pemohon" ke `/akun`, yang masih boleh memperbaiki diberi "Perbaiki Data Pemohon" ke halaman Data Pemohon. Yang diminta pada langkah ini notifikasi loncengnya, dan label tombol surel itu memang menjanjikan tujuan yang berbeda.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `Support/NotifikasiPortal.php` | Tautan hasil verifikasi selalu ke halaman Data Pemohon |
| api-ppid | `tests/Feature/NotifikasiPanelTest.php` | Tautan diuji untuk keputusan disetujui dan ditolak |
| fe-ppid | `tests/Feature/PortalNotifikasiTest.php` | Membuka notifikasinya mengalihkan ke halaman itu, dan halamannya tetap terbuka setelah terverifikasi |

### Verifikasi

- **2 asersi baru** di api-ppid: putusan `terverifikasi` maupun `ditolak` sama-sama menulis `link` `/akun/pengaturan/data-pemohon`.
- **1 tes baru** di fe-ppid: `akun.notifikasi.buka` mengalihkan ke halaman itu, dan halamannya dijawab **200** oleh pemohon yang datanya sudah terverifikasi — tautan yang benar tidak berguna kalau tujuannya menolak keadaan yang paling sering mengkliknya.
- Suite penuh: fe-ppid **74 lulus** (272 asersi), api-ppid **41 lulus** (174 asersi).

### Perlu diketahui

Dua baris notifikasi verifikasi yang sudah terlanjur tercatat masih menyimpan tautan lama ke `/akun` (satu di antaranya belum dibaca). Data lama tidak diubah tanpa diminta; pemberitahuan berikutnya sudah memakai tautan yang benar. Bila ingin baris lamanya ikut diperbaiki, katakan saja.

### Yang belum dikerjakan

Tidak berubah dari putaran sebelumnya: dua baris notifikasi ganda lama di lonceng panel, tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja.
---


## Status Pengerjaan (putaran 72 — langkah 92)

Notifikasinya sebenarnya sudah tertulis dan sudah bisa dibaca API. Yang tidak ada adalah **halamannya** — dan lonceng yang menampilkannya terlambat menyegarkan diri.

### Yang diperiksa lebih dulu: apakah barisnya ada

Sebelum mengubah apa pun, keadaan basis data dan API dibaca apa adanya:

- Tabel `notifikasi` memuat lima baris `verifikasi_pemohon` dari pengujian tersebut — satu untuk super admin, satu untuk PPID, dan satu untuk masing-masing dari tiga akun PPID Pelaksana. Isinya bertanda `"modul": "permohonan"` dan menaut ke `/ppid/pemohon?detail=…`.
- `GET /v1/notifikasi` dipanggil langsung dengan token akun PPID Pelaksana: **200**, berisi baris itu.

Jadi sisi penulisan (langkah 91) dan sisi pembacaan sudah benar. Persoalannya di panel.

### Halaman Notifikasi tidak pernah punya route

Komponennya lengkap sejak lama — `NotificationsAppView`, `NotificationsAppHeader`, kartu, hook, semuanya sudah berbahasa Indonesia — tetapi tidak ada satu pun berkas `*Route.tsx` di dalam `apps/notifications`. Route panel dikumpulkan lewat `import.meta.glob('/src/app/**/*Route.tsx')`, jadi tanpa berkas itu halamannya tidak pernah terdaftar. Alamat apa pun ke sana jatuh ke `/404`, dan tidak ada entri menunya.

Akibatnya satu-satunya jalan melihat notifikasi adalah lonceng — padahal lonceng **hanya memuat yang belum dibaca**. Notifikasi yang terlanjur dibuka hilang tanpa tempat untuk melihatnya kembali, persis seperti yang dilaporkan.

Sekarang halamannya terdaftar di `/ppid/notifikasi`, didaftarkan sebelum pola `:resourceSlug` supaya tidak tertangkap sebagai nama modul dan dijawab "Modul tidak dikenal". Entri menunya tidak digantung pada hak modul mana pun: isi notifikasi sudah disaring per pengguna dan per modul di API, jadi menggantungkannya pada satu modul justru menyembunyikan halaman dari role yang notifikasinya ada.

### Lonceng bisa menampilkan keadaan satu menit yang lalu

`QueryClient` panel menyetel `staleTime` lima menit dan mematikan penyegaran saat jendela mendapat fokus. Setelan itu benar untuk daftar modul — berpindah tab bukan tanda datanya berubah — tetapi salah untuk lonceng, karena kejadian yang diberitahukan justru datang dari situs publik saat petugas sedang mengerjakan hal lain.

Ditambah `refetchIntervalInBackground: false`, urutannya jadi begini: petugas membuka portal di tab lain, pengambilan berkala lonceng berhenti, pemohon mengirim berkas, petugas kembali ke panel — tidak ada penyegaran karena fokus, dan detak berikutnya baru satu menit kemudian. Yang terlihat: lonceng kosong padahal barisnya sudah ada.

Query lonceng kini menyimpang dari setelan bawaan itu secara sengaja (`staleTime: 0`, segarkan saat dipasang, saat fokus, dan saat tersambung lagi), dan membuka loncengnya selalu menarik ulang daftarnya. Interval 60 detiknya tetap.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| be-ppid | `ppid/PpidRoute.tsx` | Route `/ppid/notifikasi`, didaftarkan sebelum pola modul |
| be-ppid | `ppid/lib/navigation.ts` | Entri menu Notifikasi, tanpa gantungan hak modul |
| be-ppid | `apps/notifications/api/hooks/useGetAllNotifications.ts` | Segarkan saat dipasang/fokus/tersambung lagi |
| be-ppid | `apps/notifications/components/views/NotificationPanel.tsx` | Tarik ulang saat lonceng dibuka; tautan ke halaman arsip pada kedua keadaan |
| be-ppid | `apps/notifications/components/views/NotificationsAppView.tsx` | Query gagal tidak menjatuhkan halaman |
| be-ppid | `@i18n/kamusPpid.ts` | Label menu versi Inggris |

### Verifikasi

- Basis data: lima baris `verifikasi_pemohon` dari pengujian Anda, termasuk untuk ketiga akun PPID Pelaksana.
- `GET /v1/notifikasi` dengan token PPID Pelaksana: **200**, memuat baris tersebut.
- `tsc --noEmit` be-ppid bersih. Suite PHP tidak tersentuh putaran ini — tidak ada perubahan di api-ppid maupun fe-ppid.

### Perlu diketahui

`npm run build` be-ppid gagal sebelum sampai ke kode ini: `Failed to resolve entry for package "@tiptap/pm"`. Kerusakan paket pihak ketiga yang sudah ada sebelum putaran ini dan tidak bersinggungan dengan berkas yang disentuh — mode pengembangan (`npm run dev`) tetap jalan. Dicatat supaya tidak tertukar dengan akibat perubahan ini.

### Yang belum dikerjakan

Tidak berubah dari putaran sebelumnya: dua baris notifikasi ganda lama yang sengaja dibiarkan, tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja.
---


## Status Pengerjaan (putaran 71 — langkah 91)

Notifikasi verifikasi data pemohon tidak lagi berganda, dan seluruh jalur notifikasi kini mengikuti hak akses modul — termasuk satu hak yang selama ini kurang dan membuat tahap pertama alur persetujuan tidak bisa diputus.

### Gandanya bukan satu kiriman yang dicatat dua kali

Baris di basis data memperlihatkan dua notifikasi `verifikasi_pemohon` untuk pemohon yang sama, berjarak tiga menit. Jadi bukan satu penyimpanan yang menulis dua baris, melainkan pemohon yang memperbaiki lalu mengirim ulang berkasnya — dan tiap kiriman menambah baris baru sementara yang lama masih menggantung belum dibaca.

Bagi petugas keduanya tampil sebagai dua pekerjaan, padahal berkas yang menunggu diperiksa hanya satu. Yang lama malah menyesatkan: teksnya masih menyebut "pengiriman ke-1" saat yang menunggu sudah kiriman berikutnya.

Karena itu `NotifikasiAdmin::kirim()` sekarang mencari notifikasi **belum dibaca** untuk pokok yang sama (`pemohon_id`, `permohonan_id`, `keberatan_id`), lalu menimpa isinya dan menaikkan `created_at` supaya kembali ke atas lonceng. Yang **sudah dibaca** sengaja dibiarkan utuh: petugas menandainya karena sudah menanganinya, jadi kiriman sesudahnya memang pemberitahuan baru.

### Sisi pemohon: putusan yang sama tidak dikirim dua kali

Dari arah sebaliknya, `POST /pemohon/{id}/verifikasi` tidak punya penyaring apa pun terhadap putusan yang tidak berubah. Klik ganda pada tombolnya membuat pemohon menerima dua surel dan dua baris lonceng untuk satu pemeriksaan — dan pada penolakan, klik keduanya ikut menaikkan `jumlah_ditolak`, memakan jatah kirim ulang untuk berkas yang bahkan belum sempat ia perbaiki.

Sekarang putusan yang identik (status dan catatan sama persis) dijawab tanpa menyimpan apa pun. Perubahan yang benar-benar berbeda tetap tercatat dan tetap diberitahukan.

### Hak akses: satu yang kurang, satu yang salah sasaran

Menelusuri seluruh jalur notifikasi memunculkan dua ketimpangan.

**PPID Pelaksana tidak bisa memutus tahapnya sendiri.** Tahap pertama alur persetujuan — penerimaan permohonan dan keberatan — dipegang role `ppid-pelaksana`, tetapi matrix hak aksesnya memberi role itu `can_approve = false` pada modul Permohonan dan Keberatan. Ketiga jalur yang ia butuhkan (`permohonan/{id}/approval`, `keberatan/{id}/approval`, `pemohon/{id}/verifikasi`) dijaga `akses:{modul},approve`, jadi notifikasinya sampai tetapi tombol putusannya dijawab 403 — berkas berhenti di tahap pertama tanpa penjelasan di mana pun. `ModulSistemSeeder` kini memberi hak `approve` untuk kedua modul layanan itu. Hak menolak tidak ikut terbuka: itu ditentukan `boleh_tolak` pada tahap alurnya dan ditegakkan terpisah di `MenanganiPersetujuan`.

**Notifikasi keberatan menaut ke menu yang sudah dilepas.** Sejak langkah 89 modul Keberatan tidak lagi punya entri menu — isinya tampil sebagai kategori di modul Permohonan. Notifikasinya masih menaut ke `/ppid/keberatan` dan penerimanya masih diambil dari hak lihat modul `keberatan`, dua hal yang sudah tidak sejalan dengan menu yang dilihat petugas. Sekarang keduanya mengikuti modul Permohonan, dengan `jenis=keberatan` pada tautannya supaya halaman tahu rincian mana yang dibuka — id 3 bisa berarti permohonan 3 maupun keberatan 3, dan tanpa penanda itu tautannya berpeluang membuka berkas orang lain.

### Penyaring kedua saat notifikasi dibaca

Hak akses bisa berubah setelah notifikasinya ditulis. Karena itu tiap notifikasi kini membawa slug modulnya, dan `GET /v1/notifikasi` menyaring baris yang modulnya tidak lagi boleh dilihat role pembacanya — seperti middleware `akses:`, super admin lolos lebih dulu. Baris lama yang belum punya penanda modul dibiarkan lewat: menyembunyikan riwayat yang sah hanya karena bentuk datanya lebih tua bukan perbaikan.

Penerima di sisi penulisan juga disamakan dengan perilaku panel: super admin ikut menerima tanpa bergantung pada matrix, karena satu modul baru yang belum dicentang akan membuatnya diam-diam berhenti diberi tahu.

### Pemberitahuan giliran persetujuan

`AlurPersetujuan::beriTahuPenyetuju()` memilih penerima hanya dari `role_id` tahapnya. Role yang dipasang di alur tetapi hak modulnya belum dibuka jadi menerima tautan yang berujung "Akses ditolak", jadi hak `view` + `approve` ikut disyaratkan — sama dengan yang dijaga route-nya. Ditambah penyaring agar satu tahap tidak diberitahukan dua kali kalau alurnya dihitung ulang.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| fe-ppid | `Support/NotifikasiAdmin.php` | Timpa notifikasi belum dibaca untuk pokok sama; keberatan ikut modul Permohonan; slug modul dicatat; super admin ikut penerima |
| fe-ppid | `tests/Feature/NotifikasiAdminTest.php` | Baru |
| api-ppid | `Cms/PemohonController.php` | Putusan verifikasi yang tidak berubah tidak disimpan dan tidak diberitahukan |
| api-ppid | `NotifikasiController.php` | Lonceng menyaring modul yang tidak boleh dilihat |
| api-ppid | `Support/AlurPersetujuan.php` | Penerima disaring hak modul; tautan ikut modul Permohonan; tanpa pemberitahuan ganda per tahap |
| api-ppid | `seeders/ModulSistemSeeder.php` | PPID Pelaksana diberi `approve` pada modul Permohonan dan Keberatan |
| api-ppid | `tests/Feature/NotifikasiPanelTest.php` | Baru |
| be-ppid | `ppid/PpidResourcePage.tsx` | `?jenis=keberatan` membuka rincian keberatan dari modul Permohonan |

### Verifikasi

- **5 tes** `NotifikasiAdminTest`: kiriman ulang data pemohon tidak menggandakan baris, yang sudah dibaca tidak ditimpa, penerima terbatas pada role yang boleh melihat modulnya, slug modul dan tautan pemohon benar, dan notifikasi keberatan menaut ke modul Permohonan.
- **5 tes** `NotifikasiPanelTest`: putusan verifikasi yang diulang hanya sekali diberitahukan dan tidak memakan jatah penolakan, putusan yang berubah tetap diberitahukan, lonceng menyembunyikan modul tanpa hak lihat, super admin melihat semuanya, dan PPID Pelaksana bisa memutus tahap pertamanya sampai giliran berpindah ke PPID.
- Suite penuh: fe-ppid **73 lulus** (269 asersi), api-ppid **40 lulus** (169 asersi). `tsc --noEmit` be-ppid bersih.
- `ModulSistemSeeder` dijalankan ulang; `ppid-pelaksana` kini `approve = true` pada modul Permohonan dan Keberatan.

### Yang belum dikerjakan

Dua baris notifikasi ganda yang terlanjur tercatat sebelum perbaikan ini masih ada di lonceng dan sengaja tidak dihapus — data lama tidak diubah tanpa diminta. Sisanya tidak berubah dari putaran sebelumnya: tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja.
---


## Status Pengerjaan (putaran 70 — langkah 90)

Hero beranda memakai gambar resmi `HOME 1920 x 1080.png` beserta judul dan ringkasan barunya.

### Tidak ada kode tampilan yang perlu ditambah

Slider hero sudah membaca gambar, judul, dan ringkasan per slide dari modul Banner sejak lama; yang belum ada hanyalah **isinya** — tabel `banner_slider` kosong, jadi beranda jatuh ke gradasi hijau polos dengan teks bawaan template ("Portal Keterbukaan Informasi Publik").

Jadi yang dikerjakan bukan menulis teks baru ke dalam template, melainkan mengisi datanya lewat `BannerBerandaSeeder`: berkasnya disalin ke disk `media` seperti hasil unggahan biasa, lalu dicatat sebagai satu banner aktif berisi judul dan ringkasan yang diminta. Humas bisa menggantinya lewat modul Banner tanpa deploy — hal yang tidak berlaku kalau kalimatnya ditulis di Blade.

Seedernya berhenti begitu ada banner apa pun. Banner yang sudah disusun petugas tidak boleh tergeser oleh seeder yang kebetulan dijalankan ulang.

### Satu perubahan tampilan: judulnya ikut dua warna

Judul dari CMS sebelumnya tampil satu warna, sedangkan setiap judul lain di situs memakai konsep judul dua warna. Sekarang judul slide dilewatkan `$judulDua(…, 2, 'fs-title-accent-soft')`, jadi dua kata terakhirnya beraksen:

```html
Selamat Datang di Portal Resmi PPID <span class="fs-title-accent-soft">Food Station.</span>
```

Helpernya meng-escape isinya, jadi judul dari CMS tetap tidak bisa menyuntikkan markup.

Komposisi gambarnya kebetulan cocok dengan tata letak hero: maskot berada di sepertiga kanan, sisi kirinya bidang hijau kosong — persis tempat judul, ringkasan, dan tombol hero berdiri.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `seeders/BannerBerandaSeeder.php` | Baru; menyalin gambar + judul + ringkasan, idempoten |
| fe-ppid | `views/ppid/home.blade.php` | Judul slide memakai judul dua warna |
| fe-ppid | `lang/en.json` | Judul dan ringkasan versi Inggris |
| fe-ppid | `tests/Feature/BannerBerandaTest.php` | Baru |

### Verifikasi

- **2 tes** `BannerBerandaTest`: hero memakai gambar, judul, dan ringkasan milik banner — bukan teks bawaan — dan banner nonaktif tidak tayang.
- Beranda diminta langsung dalam dua bahasa: **200**, satu gambar banner terpasang, penanda aksen judul ada pada keduanya. Teks bawaan lama ("Portal Keterbukaan") sudah tidak muncul.
- Suite penuh: fe-ppid **68 lulus** (257 asersi), api-ppid **35 lulus**.

### Yang belum dikerjakan

Tidak berubah dari putaran sebelumnya: tombol buka suspend akun panel, kolom dokumen yang diminta pada modul Permohonan, dan hari libur nasional yang belum dikecualikan dari hitungan hari kerja.
---


## Status Pengerjaan (putaran 69 — langkah 89)

Alur layanan PPID dari pemohon sampai putusan PPID, keenam sub-bagiannya selesai: akun per jabatan, dua kategori dalam satu daftar, jalur pelayanan, jenjang persetujuan, aturan SLA, dan penerapannya.

**Akun untuk tiap jabatan (sub-1).** Tabel `users` mendapat `struktur_id` yang menunjuk kotak pada bagan struktur organisasi. Role menentukan *boleh apa*, struktur menentukan *siapa dalam bagan* — pembedaan yang baru diperlukan sekarang, karena ketiga anggota PPID Pelaksana berbagi satu role tetapi menempati tiga jabatan berbeda. Memecahnya jadi tiga role hanya menyalin matrix hak akses yang sama tiga kali.

`PenggunaPpidSeeder` membuat empat akun, idempoten dan tidak menimpa kata sandi akun yang sudah dipakai:

| Surel | Jabatan pada bagan | Role |
| --- | --- | --- |
| `ppid@foodstation.co.id` | PPID (Sekretaris Perusahaan & Kepatuhan) | `ppid-utama` |
| `dokumentasi.ppid@foodstation.co.id` | Pengelolaan & Dokumentasi Informasi | `ppid-pelaksana` |
| `pengumuman.ppid@foodstation.co.id` | Pengumuman Informasi | `ppid-pelaksana` |
| `penyediaan.ppid@foodstation.co.id` | Penyediaan Informasi | `ppid-pelaksana` |

Kata sandi awal `PpidFood#2026` — akun uji alur, ganti sebelum melayani permohonan nyata.

**Jenjang persetujuannya diperpendek (sub-3, sub-4).** Alur lama berjenjang tiga (PPID → Atasan PPID). Alur yang diminta berhenti di PPID, jadi tahap Atasan PPID dilepas dari kedua alur; rolenya tetap ada dan susunannya masih bisa dikembalikan super admin lewat modul Alur Persetujuan tanpa menyentuh kode.

| Alur | Tahap 1 | Tahap 2 |
| --- | --- | --- |
| Permohonan | Penerimaan PPID Pelaksana — SLA 3 hari, **tanpa hak menolak** | Persetujuan PPID — SLA 7 hari, boleh menolak |
| Keberatan | Penerimaan PPID Pelaksana — SLA 10 hari, **tanpa hak menolak** | Putusan PPID — SLA 20 hari, boleh menolak |

Jenjang pertama sengaja tanpa hak menolak, persis seperti diminta: tugasnya meneruskan, dan menolak permohonan menurut UU KIP harus disertai alasan tertulis dari pejabat berwenang. Pembagian SLA-nya menjumlah tepat ke tenggat undang-undang: 3 + 7 = 10 hari kerja, 10 + 20 = 30 hari.

**Jalur pelayanan (sub-2).** Kolom `jalur_pelayanan` (`online` / `langsung`) ditambahkan ke permohonan **dan** keberatan, beserta `jadwal_layanan` dan `keterangan_petugas`. Endpoint putusan persetujuan sudah memakainya: pada jenjang penerima, jalur wajib dipilih, dan jalur `langsung` wajib disertai tanggal dan jam undangan — undangan tanpa waktu bukan undangan. Dokumen jalur `online` tidak diwajibkan di titik yang sama karena berkasnya punya endpoint unggah tersendiri dan boleh menyusul, sedangkan waktu kunjungan harus sudah pasti saat pemohon diberi tahu.

**Aturan SLA (sub-5).** `App\Support\SlaLayanan` menampung seluruh angkanya di satu tempat: 10 hari kerja tanggapan, 7 hari kerja perpanjangan, 30 hari tanggapan keberatan, 14 hari kerja batas sengketa. Sebelumnya `addWeekdays(10)` berdiri sendiri di controller portal — satu-satunya tempat yang tahu tenggatnya. Kelasnya juga menghitung keadaan tenggat satu pengajuan (aman / segera / lewat tenggat / tepat waktu / terlambat).

Hari libur nasional belum dikecualikan: daftarnya berubah tiap tahun dan belum ada sumbernya di sistem. Tenggat yang dihitung karena itu bisa lebih ketat dari yang sebenarnya — arah yang aman, karena menagih petugas lebih awal, bukan terlambat.

**Perpanjangan tenggat.** Endpoint `POST permohonan/{id}/perpanjang` menggeser tenggat 7 hari kerja, sekali saja, wajib beralasan, mencatat log status, mengirim surel dan lonceng portal. Tenggat awalnya disimpan di kolom terpisah supaya penilaian ketepatan waktu tetap punya pembanding asli — tanpa itu, setiap keterlambatan bisa dihapus dengan menggeser tenggatnya. Hak yang dijaga `approve`, bukan `edit`: perpanjangan menggeser janji resmi kepada pemohon.

### Dua kategori dalam satu daftar (sub-2)

Sub-4 menyebut keberatan pun ditangani "pada be-ppid (modul Permohonan)", jadi keduanya dibaca dari satu daftar dan dibedakan kolom **Kategori**. Petugas menangani keduanya dengan gerak yang sama — buka detail, periksa, teruskan — sedangkan dua menu terpisah memaksanya memeriksa dua tempat untuk menjawab satu pertanyaan: apa yang menunggu saya hari ini.

Endpoint barunya `GET pengajuan` (`PengajuanLayananController`), **hanya baca**. Perubahan tetap lewat endpoint modul masing-masing, karena aturan status, jenjang, dan pencatatan keduanya memang berbeda — menyeragamkannya di sana hanya akan menyembunyikan perbedaan yang penting.

Tiga keputusan kecil yang menentukan bentuknya:

- **Digabung di PHP, bukan `UNION` di basis data.** Kedua tabel punya kolom yang jauh berbeda, dan `UNION` menuntut daftar kolom yang dipaksa sama panjang — bentuk yang mudah salah begitu satu tabel berubah.
- **Id barisnya dibuat unik lintas tabel** (`permohonan-3`, `keberatan-3`) karena daftarnya memakai id sebagai kunci baris; id asli tetap dibawa sebagai `ref_id` dan itulah yang membuka detail ke endpoint yang benar.
- **Modul Keberatan dilepas dari menu**, bukan dihapus. Isinya sudah tampil sebagai salah satu kategori; modul, API, dan hak aksesnya tetap ada dan bisa dikembalikan ke menu dengan menambahkan satu slug di `navigation.ts`.

Daftarnya membawa kolom Kategori, Kode, Pemohon, Pokok pengajuan, Status, Jalur, **Tenggat**, Diajukan, dan Batas waktu, dengan penyaring untuk empat yang pertama.

### Verifikasi di panel (sub-3, sub-4)

Formulir putusan pada panel jenjang persetujuan berubah isi mengikuti jenjangnya. **Jenjang penerima dikenali dari haknya, bukan namanya**: tahap yang tidak diberi hak menolak adalah tahap yang tugasnya meneruskan, dan dialah yang berhubungan dengan pemohon. Nama tahap bisa diubah super admin kapan saja; haknya menentukan perannya.

Pada jenjang itu, pilihan "Tolak" tidak ditawarkan sama sekali — menawarkan sesuatu yang pasti ditolak server hanya membuang waktu petugas — dan muncul isian jalur pelayanan:

| Jalur | Yang diminta panel |
| --- | --- |
| Online | Petunjuk mengunggah dokumen lewat panel Berkas Tanggapan; berkasnya boleh menyusul |
| Langsung | Tanggal dan jam undangan (wajib), ditambah alamat, kontak, dan jam layanan untuk disalin ke undangan |

Keterangan untuk pemohon bisa diisi pada kedua jalur. Tombol kirim tetap mati sampai isiannya lengkap, dan server memeriksa hal yang sama lagi — panel yang menuntut isian tidak menggantikan aturan, ia hanya mencegah petugas mengetuk tombol yang akan ditolak.

Alamat meja layanan ditulis di komponennya, bukan diambil dari Pengaturan Situs: petugas membacanya untuk **disalin ke undangan**, dan alamat yang salah pada undangan jauh lebih merugikan daripada alamat yang tertinggal satu versi di halaman kontak.

### Portal pemohon (sub-2, sub-4)

**Jalur pelayanan diturunkan, tidak ditanyakan.** Formulir permohonan sudah menanyakan cara pengiriman, dan cara pengiriman menjawab pertanyaan yang sama: dokumennya dikirim atau pemohonnya datang. Dua isian untuk satu jawaban hanya melahirkan kemungkinan keduanya bertentangan. `ambil_langsung` → Langsung, `email` → Online; petugas tetap bisa mengubahnya saat menerima berkas.

**Keberatan hanya atas permohonan yang sudah tuntas.** Daftar pilihannya disaring, dan aturannya diperiksa ulang di server — isian tersembunyi bisa diubah sebelum dikirim. Penolakan dan kedaluwarsa ikut dianggap tuntas, bukan kelalaian: keduanya justru alasan keberatan yang paling sering, sebagaimana tertulis pada infografis Alasan Pengajuan Keberatan di langkah 87.

**Tenggat keberatan diisi saat pengajuan**: 30 hari kalender sejak diregistrasi — hari kalender, bukan hari kerja, dan itulah yang membedakannya dari tenggat permohonan.

### Surel per jalur (sub-6)

Begitu jenjang penerima meneruskan berkas, pemohon menerima surel yang isinya berbeda per jalur, karena yang perlu ia lakukan memang berbeda: jalur Online cukup ditunggu, jalur Langsung menuntutnya datang pada waktu dan tempat tertentu. Surel jalur Langsung karena itu memuat waktu kunjungan, alamat, kontak, dan jam layanan.

Dikirim di luar transaksi: surel yang gagal tidak boleh membatalkan putusan yang sudah sah.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `migrations/2026_08_27_000002_alur_layanan_ppid` | `users.struktur_id`; `jalur_pelayanan`, `jadwal_layanan`, `keterangan_petugas` pada permohonan & keberatan; kolom perpanjangan dan tenggat keberatan |
| api-ppid | `Support/SlaLayanan.php` | Seluruh angka tenggat + hitungan keadaannya |
| api-ppid | `Cms/PengajuanLayananController.php` | Daftar gabungan dua kategori |
| api-ppid | `Cms/PermohonanController@perpanjangTenggat` | Perpanjangan 7 hari kerja, sekali |
| api-ppid | `Concerns/MenanganiPersetujuan.php` | Isian jalur pada jenjang penerima + surelnya |
| api-ppid | `Support/EmailPemohon.php`, `NotifikasiPortal.php` | Surel jalur pelayanan & perpanjangan, lonceng portal |
| api-ppid | `Api/AnalitikController.php` | Angka SLA dashboard dibaca dari `SlaLayanan` |
| api-ppid | `seeders/PenggunaPpidSeeder.php`, `AlurApprovalSeeder.php` | Empat akun; jenjang dua tahap |
| be-ppid | `ppid/lib/resources.ts`, `navigation.ts` | Modul Permohonan jadi daftar gabungan; Keberatan lepas dari menu |
| be-ppid | `PpidResourcePage.tsx` | Aksi baris memilih dialog menurut kategori |
| be-ppid | `components/PersetujuanBerjenjang.tsx` | Formulir putusan per jenjang dan per jalur |
| fe-ppid | `Akun/PermohonanController.php`, `Akun/KeberatanController.php` | Jalur diturunkan; keberatan disaring; tenggat 30 hari |
| fe-ppid | `Models/PermohonanInformasi.php`, `KeberatanInformasi.php` | Kolom baru + `STATUS_SELESAI` |

### Verifikasi

- **6 tes** `AlurLayananPpidTest` (api-ppid): daftar gabungan memuat dua kategori dengan id yang tidak bertabrakan, penyaring kategori memisahkannya, perpanjangan hanya sekali dan wajib beralasan, tenggat awal tersimpan, angka SLA sesuai undang-undang, dan tenggat yang lewat ditandai.
- **2 tes** `AlurLayananPortalTest` (fe-ppid): daftar status tuntas memuat penolakan dan kedaluwarsa tetapi bukan yang masih berjalan, dan pemetaan jalur ↔ cara pengiriman tidak terbalik.
- Suite penuh: api-ppid **35 lulus** (147 asersi), fe-ppid **66 lulus** (250 asersi).
- `tsc --noEmit` bersih; `eslint` pada keempat berkas be-ppid yang disunting tidak menyisakan peringatan baru (sisanya prettier bawaan `resources.ts`).
- Migrasi dan ketiga seeder dijalankan; cache route dan config dibangun ulang.

### Yang belum dikerjakan

Tidak berubah: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid. Satu catatan baru: **hari libur nasional belum dikecualikan** dari hitungan hari kerja — perlu sumber daftarnya lebih dulu.
---


## Status Pengerjaan (putaran 68 — langkah 88)

**Maklumat Pelayanan** kini tayang seperti kedua halaman prosedur: dokumennya mengisi halaman, judul "Detail Maklumat Pelayanan Informasi Publik" dihapus.

### Modul CRUD-nya sudah ada sejak awal — yang rusak isinya

Permintaannya menyebut "buatkan juga modul untuk CRUD di be-ppid agar konsepnya dinamis". Modul itu sudah ada dan sudah dipakai: **Maklumat**, di grup Standar Layanan, dengan unggahan berkas, tanggal terbit, dan status Draft/Terbit/Arsip. Membuat modul kedua untuk halaman yang sama hanya melahirkan dua sumber yang bisa berbeda isinya, jadi tidak dibuat.

Yang membuat halamannya terlihat "belum dinamis" adalah cacat data, bukan cacat modul: baris maklumat terbit ada di basis data, tetapi `file_dokumen`-nya **kosong**. Halamannya diam-diam memakai teks cadangan yang ditulis di template, dan tidak ada satu pun yang gagal untuk mengabarkannya.

Penyebabnya `MaklumatAwalSeeder`. Syarat berhentinya dulu berbunyi "kalau sudah ada maklumat berstatus published, berhenti" — tanpa memeriksa apakah barisnya punya berkas. Baris yang terlanjur dibuat pada saat berkas acuannya belum tersedia karena itu tidak pernah bisa dilengkapi: setiap kali seeder dijalankan lagi, baris itulah yang membuatnya berhenti.

Sekarang syaratnya memeriksa isinya, dan barisnya **dilengkapi**, bukan digandakan:

```
Dokumen maklumat terbit dilengkapi: uploads/maklumat/2026/08/x1pufFqUqHCUbTnH22hBuwBQ9Gx4Ud2q.png
```

Menambah baris terbit kedua akan membuat situs memilih salah satunya berdasarkan tanggal dan meninggalkan dua arsip untuk satu dokumen yang sama.

### Judulnya hilang berdasarkan isi, bukan nama halaman

Syarat penghilangan judul "Detail …" tidak ditulis sebagai daftar nama halaman, melainkan satu pertanyaan: apakah halaman ini punya gambar untuk ditayangkan?

```blade
$tayangBergambar = !empty($data['gambar_alur']) || !empty($data['dokumen']);
```

Halaman yang gambarnya belum diunggah tetap memakai judul itu sebagai pembuka isinya — termasuk Maklumat sendiri, kalau berkasnya suatu saat dikosongkan lagi. Daftar nama halaman akan menuntut suntingan template setiap kali ada halaman baru; pertanyaan ini tidak.

### Halamannya tinggal dokumennya

Tiga hal dilepas dari sekeliling gambar: kalimat pengantar "Pernyataan komitmen PPID …", tombol "Buka di tab baru", dan tombol "Unduh Maklumat".

Ketiganya menerangkan atau menyediakan jalan menuju satu benda yang sudah ada di layar dan sudah lengkap isinya. Pengantarnya meringkas dokumen yang bisa dibaca utuh dua sentimeter di bawahnya. Tombol "Buka di tab baru" melakukan persis yang dilakukan mengklik gambarnya. Yang tersisa hanya tombol unduh, dan satu tombol berdiri sendiri di atas dokumen lebih mirip kotak menganggur daripada ajakan — sedangkan menyimpan gambar sudah punya jalannya sendiri di setiap peramban.

Sebagai gantinya **gambarnya sendiri yang jadi tautannya**: satu sasaran besar, sama mudahnya disentuh di ponsel maupun diklik di layar lebar, dan zoom-nya memakai peramban alih-alih overlay yang justru mengecilkan gambar lagi.

Susunannya sekarang satu `<figure>`: dokumen di dalam kartu putih bersudut membulat dengan bayangan tipis yang menegas saat disorot, lalu satu baris keterangan penerbitan di bawahnya — kecil, di tengah, dan turun sendiri jadi dua baris di layar sempit.

### Lebar lembarnya dicari lewat dua kutub yang ditolak

Ukurannya tidak ketemu sekali jadi, dan kedua percobaan yang gagal justru yang menentukan angka akhirnya.

| Percobaan | Lebar lembar | Ditolak karena |
| --- | --- | --- |
| Pertama | `max-w-3xl` — 768 px | Terlalu kecil: maklumat ini **lembar A4 hasil pindai**, tulisannya ikut mengecil bersama lebarnya sampai harus diklik dulu sekadar untuk dibaca |
| Kedua | `w-full` di dalam `max-w-screen-xl` — ±1.180 px | Terlalu besar: pada lebar itu lembar A4 menjulang ±1.670 px, lebih tinggi dari layar mana pun, dan tanda tangannya baru terlihat setelah gulir panjang |
| **Sekarang** | **`max-w-4xl` — 896 px** | Di tengah keduanya |

Yang membuat kutub kedua meleset adalah sifat dokumennya: A4 potret berbanding 1 : 1,414, jadi setiap penambahan lebar dibayar tinggi satu setengah kali lipat. Melebarkan lembar tegak bukan membuatnya lebih terbaca, melainkan lebih panjang.

Pelebaran kontainer halaman yang sempat dipasang bersama percobaan kedua ikut dibatalkan: `max-w-screen-xl` kembali ke `max-w-6xl` dan padding kartu kembali `p-6 sm:p-10`, sama dengan halaman Standar Layanan lainnya. Aturan lebar khusus per halaman tidak lagi ada — satu pengecualian yang tersisa hanyalah Jalur & Waktu Layanan seperti sebelum langkah ini.

Sisa keterbacaan diserahkan ke klik-untuk-ukuran-penuh, yang memang sudah menempel pada gambarnya. Efek hover "membesar sedikit" tetap dilepas: pada lembar sebesar ini gerakannya terasa gemetar, bukan hidup.

Kolom `ringkasan` ikut dibersihkan sampai ke pangkalnya. Controller berhenti membawanya ke view, dan isian **Pengantar** dilepas dari formulir modul Maklumat di be-ppid: isian yang diminta kepada operator tetapi tidak pernah muncul di mana pun adalah jebakan, bukan fitur. Kolomnya tetap ada di basis data — isian lama tersimpan dan masih terjangkau kotak pencarian modul — hanya formulirnya yang tidak lagi memintanya.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `seeders/MaklumatAwalSeeder.php` | Berhenti hanya bila maklumat terbit sudah punya berkas; baris kosong dilengkapi |
| fe-ppid | `views/ppid/service_standard.blade.php` | `$tayangBergambar`; blok maklumat jadi satu `<figure>` `max-w-4xl` tanpa pengantar dan tanpa kedua tombol |
| fe-ppid | `PpidController@lengkapiMaklumat` | `ringkasan` tidak lagi diteruskan ke view |
| fe-ppid | `lang/en.json` | Satu kunci baru, dua kunci mati dihapus |
| fe-ppid | `tests/Feature/MaklumatHalamanTest.php` | Baru |
| be-ppid | `ppid/lib/resources.ts` | Isian Pengantar dilepas dari formulir Maklumat |

Modul CRUD-nya sendiri tidak ditambah — Maklumat sudah menyediakannya.

### Verifikasi

- **2 tes** `MaklumatHalamanTest` baru: dokumen gambar tayang tanpa judul "Detail …", tanpa pengantar, dan tanpa kedua tombol, tetapi tetap bisa dibuka ukuran penuh lewat gambarnya dan tetap menampilkan keterangan penerbitan; halaman tanpa dokumen kembali ke teks bawaan beserta judulnya. Yang kedua penting justru karena keadaan itulah yang tadinya menutupi bug-nya.
- Halaman diminta langsung: `maklumat-pelayanan` **200**, 1 berkas, pengantar/tombol tab/tombol unduh/judul "Detail …" keempatnya **tidak ada**. `prosedur-permohonan` 5 gambar, `prosedur-keberatan` 3, `jalur-waktu-layanan` **200** dengan judulnya utuh.
- Lebar dibaca dari markup yang benar-benar terkirim: `maklumat-pelayanan` → isi `max-w-6xl`, lembar `max-w-4xl`; `prosedur-permohonan` → isi `max-w-6xl`; `jalur-waktu-layanan` → isi `max-w-screen-2xl`.
- Suite penuh: fe-ppid **64 lulus** (241 asersi), api-ppid **29 lulus**. `tsc --noEmit` bersih; peringatan `eslint` pada `resources.ts` turun dari 35 ke 33 dan seluruhnya prettier bawaan berkas itu, 0 error.

### Yang belum dikerjakan

Tidak berubah: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid.
---


## Status Pengerjaan (putaran 67 — langkah 87)

**Prosedur Permohonan Keberatan** kini tayang sebagai alur bergambar juga, dengan tiga infografis. Tidak ada modul baru, tidak ada tabel baru, tidak ada satu baris pun di Blade yang ditambah untuk halaman ini: modul Alur Prosedur dari putaran 66 memang dibuat menerima dua halaman, dan langkah 87 hanya membuktikannya — mengunggah gambarnya sudah cukup.

Aturan yang sudah berlaku ikut berjalan sendiri di halaman ini: begitu gambarnya ada, kartu tahapan, Rincian Tahapan, judul "Detail …", dan intronya berhenti tayang; tombol "Ajukan Keberatan" tetap.

| Urutan | Judul | Isinya |
| --- | --- | --- |
| 1 | Tata Cara Permohonan Keberatan | Masuk dengan akun terverifikasi, buka Permohonan Keberatan, tambah pengajuan, isi, ajukan |
| 2 | Mekanisme Permohonan Keberatan | Enam tahap penanganan: pencatatan berkas → tanggapan Atasan PPID paling lambat 30 hari → sengketa ke Komisi Informasi dalam 14 hari kerja |
| 3 | Alasan Pengajuan Keberatan | Tujuh alasan sah: penolakan, permintaan tak ditanggapi, tanggapan lewat batas waktu, biaya tidak wajar, dan seterusnya |

Alasan ditaruh paling akhir walau ia yang paling "awal" secara logika. Pemohon yang membuka halaman ini umumnya sudah tahu dirinya keberatan; yang ia cari adalah caranya, bukan izin untuk merasa keberatan. Daftar alasan berguna sebagai rujukan setelah tahu prosedurnya.

### Satu bug diam yang ditutup di sini

Folder `standar layanan/` datang lagi berisi `1.png`, `2.png`, `3.png` — nama yang sama persis dengan berkas Prosedur Permohonan putaran lalu, tetapi isinya Keberatan. Berkas lamanya tertimpa.

Baris di basis data tidak terpengaruh: seeder menyalin gambarnya ke disk `media` saat dijalankan, jadi kelima gambar Permohonan tetap tayang. Yang rusak adalah **seedernya**. `AlurProsedurAwalSeeder` menunjuk `1.png`–`5.png` dengan judul-judul Permohonan; dijalankan di basis data kosong hari ini, ia akan memasang gambar Keberatan dengan judul Permohonan dan tidak mengeluh sedikit pun. Kegagalan yang hanya terlihat oleh orang yang membuka halamannya.

Perbaikannya memisahkan sumbernya per halaman:

```
standar layanan/
  prosedur-permohonan/  1.png … 5.png
  prosedur-keberatan/   1.png … 3.png
```

Kelima gambar Permohonan dipulihkan dari disk `media` ke folder barunya, jadi seeder tetap bisa membangun ulang keadaan penuh dari nol. Seedernya sendiri sekarang berjalan per halaman dan **idempoten per halaman** — halaman yang barisnya sudah ada dilewati, halaman yang belum tetap diisi. Itulah yang membuat perintah yang sama bisa dijalankan lagi tanpa menggandakan Permohonan:

```
Alur bergambar prosedur-permohonan sudah ada — dilewati.
Alur bergambar prosedur-keberatan dibuat: 3 gambar.
```

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `seeders/AlurProsedurAwalSeeder.php` | Dua halaman, sumber per subfolder, idempoten per halaman |
| — | `standar layanan/` | Dipecah jadi `prosedur-permohonan/` dan `prosedur-keberatan/` |
| fe-ppid | `tests/Feature/AlurProsedurTest.php` | Satu tes baru; tes "tanpa gambar" kini menonaktifkan gambarnya lebih dulu |

Tidak ada perubahan di be-ppid dan tidak ada perubahan di Blade — keduanya sudah menangani `prosedur-keberatan` sejak putaran 66.

### Verifikasi

- **6 tes** `AlurProsedurTest` lulus (23 asersi). Yang baru: Prosedur Keberatan tayang bergambar sekaligus kehilangan kartu tahapan, rincian, dan intronya, tetapi tetap punya tombol "Ajukan Keberatan". Tes "halaman tanpa gambar" tidak lagi bergantung pada Keberatan yang kebetulan kosong — ia menonaktifkan gambarnya lebih dulu, jadi tetap berarti setelah halaman itu terisi.
- Render langsung: `prosedur-permohonan` **200** dengan 5 gambar, `prosedur-keberatan` **200** dengan 3 gambar, keduanya tanpa judul "Detail …".
- Suite penuh: fe-ppid **62 lulus** (229 asersi), api-ppid **29 lulus**.

### Yang belum dikerjakan

Tinggal dua, keduanya dari daftar lama: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid. Catatan putaran 66 soal Keberatan belum punya infografis sudah gugur — gambarnya sekarang ada.
---


## Status Pengerjaan (putaran 66 — langkah 86)

Halaman **Standar Layanan → Prosedur Permohonan Informasi** sekarang membuka dengan lima infografis resmi yang tayang berurutan, dan gambar-gambar itu dikelola dari be-ppid lewat modul baru **Alur Prosedur**.

### Gambarnya jadi data, bukan berkas di dalam template

Menyalin kelima PNG ke `public/assets` lalu menyebutnya satu per satu di Blade akan bekerja hari ini dan menjadi beban besok: setiap kali humas memperbaiki satu panel — mengganti tangkapan layar yang tampilannya sudah berubah, menambah tahap baru — perubahan itu berhenti di antrean deploy. Padahal isinya persis jenis konten yang selama ini sudah dikelola sendiri oleh petugas: Maklumat, Banner, Regulasi.

Jadi gambarnya masuk sebagai data, dengan pola yang sama seperti Maklumat — tabel sendiri, unggahan ke disk `media`, dan situs publik membaca berkasnya utuh tanpa mengetik ulang isinya di CMS.

### Urutannya mengikuti perjalanan pemohon, bukan nomor berkasnya

Nama berkas di folder `standar layanan/` kebetulan sudah 1–5, tetapi urutan itu ditegakkan lewat kolom `urutan`, bukan diwarisi dari nama berkas — begitu petugas menyisipkan gambar keenam, nama berkas tidak lagi punya arti apa pun.

| Urutan | Judul | Isinya |
| --- | --- | --- |
| 1 | Tata Cara Pembuatan Akun Permohonan Informasi | Langkah 1–4: kunjungi situs, buka menu Permohonan Informasi, daftar, isi data pendaftaran |
| 2 | Proses Verifikasi & Lengkapi Data | Langkah 5–13: konfirmasi email, masuk, lengkapi identitas, unggah dokumen, verifikasi Tim PPID |
| 3 | Tata Cara Permohonan Informasi Publik | Langkah 1–5 setelah akun terverifikasi: masuk, tambah pengajuan, isi rincian, ajukan |
| 4 | Mekanisme Permohonan Informasi Publik | Lima tahap penanganan di sisi Badan Publik, termasuk batas 10 hari kerja + perpanjangan 7 hari |
| 5 | Syarat Pengajuan Permohonan Informasi Publik | Dokumen identitas untuk perorangan, kelompok, mahasiswa, dan badan hukum |

Dua gambar pertama adalah cara membuat akun, baru yang ketiga cara mengajukan. Itulah urutan yang dialami pemohon baru, dan itu berbeda dari urutan judulnya — "Tata Cara Permohonan" terdengar seperti awal, padahal ia langkah setelah akunnya terverifikasi.

### Satu tabel untuk dua halaman

Kolom `halaman` menentukan halaman Standar Layanan mana yang menayangkan satu baris; nilainya dibatasi ke slug yang memang ada (`prosedur-permohonan`, `prosedur-keberatan`). Prosedur Keberatan belum punya infografisnya, tetapi begitu gambarnya tersedia ia cukup diunggah dengan pilihan halaman yang berbeda — tanpa tabel kedua, controller kedua, atau menu kedua. Halaman yang belum punya gambar tampil persis seperti sebelumnya.

### Hak aksesnya menumpang Halaman Statis

Alasannya sama dengan Maklumat: alur bergambar adalah **isi salah satu halaman** Standar Layanan, bukan modul layanan tersendiri. Menjadikannya modul baru di matrix role berarti setiap role harus diberi hak lagi satu per satu untuk mengurus hal yang secara wewenang tidak berbeda dari mengurus halamannya. Matrix role tidak bertambah barisnya.

### Gambar menggantikan seluruh isi teks halamannya

Kartu enam tahapan dan daftar Rincian Tahapan **tidak lagi tayang** di halaman Prosedur Permohonan Informasi. Keduanya menceritakan prosedur yang sama dengan gambar di atasnya, dan halamannya jadi menyuruh pemohon membaca satu prosedur tiga kali sebelum sampai ke tombolnya.

Teksnya tidak dihapus dari kode, hanya berhenti tayang selama gambarnya ada — dan itu bukan setengah-setengah, melainkan yang menjaga Prosedur Keberatan tetap punya isi. Halaman itu memakai blok Blade yang sama tetapi belum punya infografis; menghapus teksnya di kode akan mengosongkan halaman yang tidak diminta diubah. Aturannya satu kalimat: **ada gambar → gambar saja; belum ada gambar → teks tahapannya.** Begitu infografis Keberatan diunggah, halamannya berpindah sendiri tanpa menyentuh kode.

Judul "Detail Prosedur Permohonan Informasi" dan kalimat pengantarnya ikut dilewati. Hero di atas halaman sudah memasang nama halamannya sebagai judul besar; mengulanginya di baris pertama isi tidak menambah keterangan apa pun, hanya mendorong gambar pertama turun hampir satu layar. Intronya sendiri sempat diganti lebih dulu dari "Enam tahapan sederhana…" — yang menjanjikan enam kartu yang sudah tidak ada — menjadi "Alur lengkap dari membuat akun sampai permohonan Anda diproses."; kalimat itu tetap tersimpan dan dipakai halaman yang belum punya gambar.

Yang tersisa di halaman: judul "Panduan Bergambar", satu kalimat cara memakainya, kelima gambar, dan tombol "Mulai Ajukan Permohonan". Tombolnya tetap di tempatnya karena ia ajakan, bukan bagian tahapan.

Ketiga halaman Standar Layanan lain — Maklumat, Prosedur Keberatan, Jalur & Waktu Layanan — masih memakai judul "Detail …" seperti biasa; penghilangannya terikat pada ada-tidaknya gambar alur, bukan pada nama halaman.

Klik gambar membukanya di tab baru, bukan lightbox. Tulisan di dalam infografis ini kecil, dan tab baru memberi pemohon zoom bawaan peramban — termasuk di ponsel, tempat overlay justru mengecilkan gambarnya lagi ke dalam kotak yang lebih sempit dari layarnya.

### Berkas yang disentuh

| Aplikasi | Berkas | Isi |
| --- | --- | --- |
| api-ppid | `migrations/2026_08_27_000001_create_alur_prosedur_table.php` | Tabel `alur_prosedur` + jejak dokumen (migrasi traceability sudah lewat, jadi kolomnya ditulis di sini) |
| api-ppid | `app/Models/AlurProsedur.php`, `Api/Cms/AlurProsedurController.php` | CRUD, hak akses menumpang `halaman-statis` |
| api-ppid | `routes/api.php` | `CrudRoute::register('alur-prosedur', …)` |
| api-ppid | `seeders/AlurProsedurAwalSeeder.php` | Menyalin kelima gambar ke disk `media`; idempoten |
| be-ppid | `ppid/lib/resources.ts`, `navigation.ts` | Modul Alur Prosedur, masuk grup Standar Layanan |
| be-ppid | `@i18n/kamusPpid.ts` | Label dan bantuan versi Inggris |
| fe-ppid | `app/Models/AlurProsedur.php` | Scope `tayang()`: aktif, punya berkas, terurut |
| fe-ppid | `PpidController@lengkapiAlurGambar` | Mengisi `gambar_alur`; DB mati → halaman tetap terbuka tanpa gambar |
| fe-ppid | `PpidController` (`$standardData`) | Intro Prosedur Permohonan diganti, tidak lagi menjanjikan enam kartu |
| fe-ppid | `views/ppid/service_standard.blade.php` | Blok Panduan Bergambar; kartu tahapan + Rincian Tahapan pindah ke cabang `@else`; judul "Detail …" dan intro dilewati saat ada gambar |
| fe-ppid | `lang/en.json` | Lima kalimat baru; dua kunci mati dihapus |

### Verifikasi

- **5 tes** `AlurProsedurTest` baru: urutan tayang mengikuti kolom `urutan` (barisnya sengaja dibuat terbalik, jadi tes gagal kalau urutannya diabaikan), baris nonaktif dan baris tanpa berkas tidak tayang, gambar menggantikan Ringkasan Tahapan, Rincian Tahapan, judul "Detail …", dan intronya sementara tombol ajakannya tetap ada, halaman yang belum punya gambar tetap memakai teks tahapannya, dan gambar milik satu halaman tidak bocor ke halaman satunya.
- Migrasi dijalankan dan seeder mengisi **5 baris**; `/standar-layanan/prosedur-permohonan` menjawab **200** dengan kelima gambar terpasang dan tanpa kartu tahapan, `/standar-layanan/prosedur-keberatan` menjawab **200** dengan teks tahapannya utuh.
- Keempat halaman Standar Layanan diminta langsung: `prosedur-permohonan` **200** tanpa judul "Detail …", tiga lainnya **200** dengan judulnya utuh.
- Suite penuh: fe-ppid **61 lulus** (224 asersi), api-ppid **29 lulus**.
- `tsc --noEmit` bersih; `eslint` tidak menambah peringatan baru pada berkas be-ppid yang disunting.
- **Cache route api-ppid ternyata basi** — `route:list` masih memakai daftar lama sehingga endpoint baru seolah tidak ada. Cache dibersihkan lalu dibangun ulang. Catatan ini berlaku umum: setiap penambahan route di api-ppid perlu `php artisan route:clear && php artisan route:cache` di lingkungan yang route-nya di-cache.

### Yang belum dikerjakan

Tidak berubah: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid. Ditambah satu yang menunggu bahan, bukan menunggu kode: **Prosedur Permohonan Keberatan belum punya infografisnya** — modulnya sudah siap menerima.
---


## Status Pengerjaan (putaran 65 — langkah 85)

Logo perusahaan pada header dan footer fe-ppid dan be-ppid diganti `Logo_fstj.png` (707×243, 26,5 KB).

### Dua cara berbeda, karena keadaannya berbeda

**fe-ppid — berkasnya ditimpa, kodenya tidak disentuh.** Situs publik sudah merujuk `assets/images/logo/logo_fs.png` di **sebelas** tempat: header, footer, kop email, apple-touch-icon, gambar cadangan Open Graph, dan lima kartu "diunggah oleh" pada halaman Regulasi dan Laporan. Semuanya menunjuk satu hal yang sama — logo perusahaan. Menimpa berkasnya membuat kesebelasnya berganti sekaligus; menambah berkas baru lalu menyunting sebelas rujukan hanya menciptakan sebelas kesempatan untuk terlewat satu.

**be-ppid — berkasnya baru, rujukannya disunting.** Panel admin masih memakai `logo.svg` bawaan template Fuse: tanda biru persegi 32×32, sama sekali bukan logo perusahaan. Menimpanya akan menyesatkan siapa pun yang membuka berkas bernama `logo.svg` dan menemukan PNG di dalamnya. Jadi logonya masuk sebagai `logo-fstj.png`, dan yang menunjuk logo perusahaan dialihkan ke sana:

- `components/Logo.tsx` — logo header navbar (layout1 + navbar style-1, yang aktif)
- keempat kop halaman auth: `JudulAuth`, `SignInPageTitle`, `SignOutPageTitle`, `SignUpPageTitle`

Berkas milik layout 2 dan 3 sengaja dibiarkan: keduanya tidak dipakai, dan mengubah kode template yang mati hanya menambah selisih terhadap hulunya tanpa ada yang melihat hasilnya.

### Perbandingannya berubah, dan itu menentukan ukurannya

| | Lebar × tinggi | Perbandingan |
| --- | --- | --- |
| `logo_fs.png` lama | 1382 × 629 | 2,20 : 1 |
| `Logo_fstj.png` baru | 707 × 243 | **2,91 : 1** |
| `logo.svg` be-ppid lama | 32 × 32 | 1,00 : 1 |

Selisih itu tidak berbahaya selama tingginya yang dikunci dan lebarnya mengikuti. Header fe-ppid (`h-9 sm:h-10 w-auto`) dan footer (`h-10 w-auto`) memang sudah begitu — keduanya tidak perlu disentuh.

Yang perlu disentuh justru be-ppid, karena slot lamanya **persegi**:

- `Logo.tsx`: `h-6 w-6` → `h-8 w-auto`
- keempat kop auth: `w-12` → `h-10 w-auto`

Dibiarkan seperti semula, logo 2,91 : 1 akan dipipihkan ke dalam kotak 1 : 1 — dan pemipihan logo resmi perusahaan bukan cacat kosmetik kecil.

Ada satu hal yang sempat dikhawatirkan lalu ternyata tidak berlaku: navbar Fuse biasanya punya keadaan "terlipat" berupa rel sempit yang hanya memuat ikon persegi, dan logo selebar ini akan meluber di sana. `NavbarStyle1` yang dipakai panel ini lebarnya tetap 280 px dan tidak punya keadaan itu — ia menggeser diri keluar layar (`marginLeft: -280px`), bukan menyempit. Jadi tidak ada kompromi ukuran yang perlu diambil.

### Lima kartu kecil yang dibiarkan apa adanya

Halaman Regulasi, Laporan Pelayanan, dan Standar Layanan memakai logo yang sama sebagai avatar "diunggah oleh" berukuran 24×24 dan 28×28 piksel. Logo selebar ini di dalam kotak sekecil itu tinggal setinggi ~8 piksel.

Tidak diubah, dua alasan: kelasnya memakai `object-contain`, jadi yang terjadi hanya pengecilan, bukan pemipihan; dan logo lama pun sudah dikotakkan begitu — 2,20 : 1 ke dalam 1 : 1. Ini pola lama yang memang layak dirapikan suatu saat, tetapi bukan bagian dari yang diminta, dan mengubahnya sekarang berarti menyentuh lima berkas di luar permintaan.

### Verifikasi

- **3 tes** `LogoPerusahaanTest` baru. Salah satunya mengikat ukuran berkasnya pada 707×243: penggantian yang tidak disengaja — termasuk kembali ke logo lama — gagal di tes, bukan baru ketahuan di layar orang. Satu lagi membaca markup header dan footer dan menuntut `w-auto` tetap ada, karena begitu salah satunya dikunci lebarnya logonya memipih.
- Berkasnya dilayani kedua aplikasi: fe-ppid `/assets/images/logo/logo_fs.png` **200**, be-ppid `/assets/images/logo/logo-fstj.png` **200**, keduanya 27.156 byte.
- Beranda memuat rujukan logonya **3 kali** (apple-touch-icon, header, footer).
- `tsc --noEmit` bersih, `eslint` bersih pada kelima berkas be-ppid yang disunting.
- Suite penuh: fe-ppid **56 lulus** (206 asersi), api-ppid **29 lulus**.

### Yang belum dikerjakan

Tidak berubah: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid.
---


## Status Pengerjaan (putaran 64 — langkah 83 diperluas & langkah 84)

### Langkah 83 — dari satu dokumen jadi empat daftar

Putaran 63 memasang dialog dua pilihan hanya untuk dokumen yang penandanya menyala — praktisnya cuma Annual Report. Permintaan sekarang memberlakukannya pada **Daftar Informasi Publik, Informasi Berkala, Serta Merta, dan Setiap Saat**, dengan pratinjau memakai tautan yang diisikan petugas.

#### Yang menentukan keputusan tanpa perlu bertanya

Sebelum mengubah apa pun, isi daftarnya dihitung:

| | Jumlah |
| --- | --- |
| Dokumen terbit | 24 |
| Punya tautan baca | 10 |
| **Punya berkas terunggah** | **0** |
| Tidak punya keduanya | 14 |

Tidak satu pun dokumen punya berkas. Artinya tidak ada satu pun unduhan yang tersedia hari ini, dan menjadikan seluruh unduhan bergerbang **tidak menutup apa pun yang sekarang terbuka** — yang selama ini bisa dibuka publik adalah tautan bacanya, dan itu tetap terbuka. Kekhawatiran "24 dokumen mendadak terkunci" tidak berlaku, jadi tidak ada yang perlu dikonfirmasi.

#### Dua salinan aturan disatukan

Ketahuan saat mengerjakan: keputusan tombol ada **dua kali**. `partials/informasi_aksi.blade.php` dipakai halaman indeks, sedangkan `information.blade.php` menyimpan salinan inline-nya sendiri — yang putaran 63 saya sunting. Dua salinan aturan yang sama pada satu daftar akan berpisah cepat atau lambat; putaran 63 sudah membuktikannya, karena dialognya hanya masuk ke salah satu.

Sekarang satu jalur:

- `partials/informasi_aksi.blade.php` — tombol satu baris. Dipakai keempat daftar.
- `partials/informasi_dialog.blade.php` — dialognya, dimuat sekali per halaman.

`information.blade.php` menyusut dari 220-an baris jadi 139.

#### Penandanya jadi bawaan, bukan pengecualian

`unduhan_terbatas` sekarang bernilai `true` secara bawaan, dan 24 baris yang ada ikut dinyalakan. Membiarkan yang lama `false` berarti dokumen lama diam-diam berperilaku lain dari dokumen yang dibuat besok — dua aturan pada satu daftar, tanpa apa pun di layar yang menjelaskan bedanya.

Penandanya **tidak dihapus**. Ia tetap jadi jalan keluar: dokumen yang memang boleh diunduh siapa saja cukup dimatikan penandanya, tanpa perubahan program.

#### Perilaku akhirnya

Satu pintu untuk semua entri di keempat daftar. Tombol **Lihat** membuka dialog:

| Isi dialog | Kapan |
| --- | --- |
| **Di Lihat Saja** → tautan dokumen, tab baru, tanpa masuk | Bila tautannya diisi |
| "Tautan untuk dibaca belum tersedia" | Bila belum diisi |
| **Unduh Dokumen** → rute bergerbang | Bila berkas salinannya diunggah |
| "Salinan untuk diunduh belum tersedia" | Bila belum (keadaan seluruh 24 dokumen saat ini) |

Entri yang tidak punya tautan maupun berkas tidak membuka dialog sama sekali — tombolnya tetap **Mohon Dokumen** seperti sebelumnya.

Bantuan isian di be-ppid ikut diperbarui, karena artinya berubah: **Tautan halaman** sekarang alamat tempat dokumen **dibaca** (terbuka untuk umum), **Lampiran dokumen** untuk salinan yang **diunduh** (menuntut permohonan disetujui).

---

### Langkah 84 — backsound jingle

Dipasang, dengan tiga hal yang perlu Anda ketahui sebelum menilai hasilnya.

**Peramban memblokir suara yang menyala sendiri.** Chrome, Safari, dan Firefox menolak `play()` sebelum pengunjung pernah menyentuh halamannya. Jadi "berbunyi setiap kali akses atau refresh" tidak bisa dijanjikan pada kunjungan pertama — bukan karena tidak dikerjakan, melainkan karena kebijakan peramban sejak 2018 dan tidak ada cara sah melewatinya. Yang dipasang: dicoba dulu; kalau ditolak, dijalankan pada sentuhan, klik, atau tekan tombol pertama. Sesudah itu setiap muat halaman berbunyi seperti yang diminta.

**Harus ada cara mematikannya.** WCAG 2.1 kriteria 1.4.2 mewajibkan suara yang berbunyi otomatis lebih dari tiga detik punya mekanisme penghenti — dan untuk situs badan publik ini bukan saran. Tombol bulat di pojok kiri bawah itulah mekanismenya. Pilihannya disimpan di `localStorage`, jadi pengunjung yang mematikannya tidak perlu mematikannya lagi di setiap halaman. Volumenya 0,35, bukan penuh.

**Portal pengguna dikecualikan.** Halaman `/akun/*` adalah tempat orang mengetik permohonan, data diri, dan password. Musik latar di sana mengganggu pekerjaan, bukan menyambut tamu.

**Soal berkasnya.** `Jingle_Food_Station_Vocal.mp4` adalah video H.264 dengan audio AAC, 972 KB. Elemen `<audio>` memainkan trek audionya dan mengabaikan videonya — tetapi videonya tetap ikut terunduh. Setiap pengunjung membayar ~970 KB untuk suara yang mungkin puluhan KB saja. Tidak ada ffmpeg di mesin ini untuk mengekstraknya; bila nanti ada, mengubahnya ke `.m4a` memangkas beban itu tanpa mengubah satu baris kode pun — cukup ganti berkasnya dan nama di `partials/backsound.blade.php`.

### Verifikasi

- **14 tes** `DokumenTerbatasTest` (dua baru: dialog tampil di indeks **dan** halaman kategori; entri tanpa isi tetap menawarkan Mohon Dokumen). Tes pertama itu yang menahan kedua daftar agar tidak berpisah lagi seperti di putaran 63.
- **4 tes** `BacksoundTest`: terpasang di halaman publik, tidak ada di portal pengguna, tombol penghenti beserta penyimpan pilihannya ada, berkasnya ada.
- Berkas jingle dilayani HTTP **200**, 995.539 byte.
- Migrasi backfill: 24 dari 24 dokumen terbit kini `unduhan_terbatas = true`.
- Suite penuh: fe-ppid **53 lulus** (195 asersi), api-ppid **29 lulus**. `npm run build` sukses.

### Yang perlu Anda lakukan

**Unggah berkas salinan** pada dokumen yang memang ingin bisa diunduh — lewat be-ppid → Informasi Publik → pilih dokumen → Lampiran dokumen. Selama belum ada, dialognya tetap berfungsi tetapi hanya menawarkan Di Lihat Saja.

**Isi Tautan halaman** pada 14 dokumen yang belum punya keduanya, bila memang ada halaman tempat isinya bisa dibaca. Sekarang tombolnya masih Mohon Dokumen.

### Yang belum dikerjakan

Tidak berubah: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid.

---


## Status Pengerjaan (putaran 63 — langkah 82 & 83 direvisi)

Keduanya ditulis ulang setelah putaran 62. Yang berubah bukan tambahan, melainkan arah.

### Langkah 82 — jamnya sudah benar, tampilannya yang belum

Angka jamnya diperbaiki putaran 62. Permintaan sekarang soal **tampilan** di bagian Kontak beranda, dan alasannya terlihat begitu dibaca ulang:

> Senin–Jumat, 08.00–15.00 WIB (istirahat 12.00–13.00 WIB)

Satu kalimat panjang berkurung. Orang yang cuma ingin tahu "tutup jam berapa" harus membaca seluruhnya. Sekarang tiga baris:

- Senin–Jumat
- Pukul 08.00–15.00 WIB
- Istirahat Pukul 12.00–13.00 WIB

Baris pertama ditebalkan sedikit sebagai kepala, dua sisanya `tabular-nums` supaya angkanya sejajar.

**Sumbernya tetap satu.** Nilai `kontak.jam_layanan` di CMS tidak dipecah jadi tiga kolom — pemecahannya dilakukan saat menampilkan, dari nilai yang sama. Bila petugas menggantinya dengan kalimat lain yang tidak dikenali polanya, hasil pemecahannya `null` dan tampilannya kembali memakai kalimat apa adanya. Menebak-nebak struktur dari teks bebas hanya akan memotongnya di tempat yang salah.

### Langkah 83 — arahnya dibalik

Putaran 62 dibangun dari dua jawaban Anda: unduh terbuka setelah permohonan **disetujui**, dan pratinjau membaca **berkas terunggah**. Yang kedua sekarang dibatalkan:

> Di lihat Saja (Preview only) **tautan tetap yang sekarang**

Jadi "lihat" memakai tautan `foodstation.id/laporan-tahunan-fstj/` yang memang sudah ada pada barisnya — bukan PDF yang harus diunggah ulang. Dan pilihannya muncul lewat **dialog**, bukan halaman.

Yang dibongkar dari putaran 62:

- Penampil pdf.js di halaman dokumen — dilepas. Pratinjaunya bukan lagi berkas kita.
- Rute `ppid.dokumen.berkas` (penyaji berkas `inline`) — dihapus. Tidak ada lagi yang memakainya, dan pintu yang tidak dipakai tetap pintu.
- Tes `test_isi_berkas_pratinjau_terbuka_untuk_tamu` — ikut dilepas bersama rutenya.

Yang **tetap dipakai** dari putaran 62, karena bagian itu tidak berubah: kolom `unduhan_terbatas` dan `informasi_publik_id`, pemindahan berkas ke disk privat, seluruh aturan `AksesDokumen`, dan gerbang unduhnya.

#### Dialognya satu untuk seluruh tabel

Tombol tiap baris tidak membawa markup dialognya sendiri — ia mengirim event `buka-dialog-dokumen` berisi judul, tautan pratinjau, dan alamat rute unduh. Satu dialog di bawah tabel yang menangkapnya. Kalau tiap baris punya dialognya sendiri, tabel berisi 24 dokumen akan mencetak 24 salinan markup yang sama.

Isi dialognya:

| Tombol | Perilaku |
| --- | --- |
| **Di Lihat Saja** | Membuka tautan dokumen di tab baru. Tanpa masuk, tanpa permohonan |
| **Unduh Dokumen** | Menuju rute unduh, yang memeriksa apakah permohonan orangnya sudah disetujui |

Bila berkas salinannya belum diunggah petugas, tombol Unduh **tidak dipasang** — diganti keterangan "Salinan untuk diunduh belum tersedia". Tombol yang berujung 404 lebih buruk daripada tidak ada tombol.

#### Halaman dokumen berubah peran

`/informasi/dokumen/{id}` bukan lagi penampil berkas. Ia sekarang halaman yang menjelaskan **apa yang kurang** ketika unduhannya belum terbuka, dan menyediakan langkah berikutnya — ke situlah rute unduh mengalihkan orang yang belum berhak. Isinya: tautan baca di atas, panel keadaan unduh di bawah.

Karena tidak lagi menyajikan berkas, halaman itu juga tidak lagi menuntut berkas: dokumen yang baru punya tautan tetap membukanya dengan keterangan yang jujur, bukan 404.

### Verifikasi

- **12 tes** `DokumenTerbatasTest`, semuanya lulus. Dua baru: dialog memuat tautan baca **dan** alamat rute unduh; halaman akses tetap terbuka tanpa berkas.
- Keempat status yang belum diputus, permohonan ditolak, pemohon lain, dan persetujuan atas dokumen lain — semuanya tetap tertutup, sama seperti putaran 62.
- Diuji pada baris Annual Report sungguhan (terbatas, punya tautan, belum punya berkas): tombol dialog ada, tautan `foodstation.id` tertanam, `unduh: null` — tombol unduhnya memang tidak dipasang.
- Satu jebakan tes yang perlu dicatat: alamat ditanam lewat `@js()`, jadi Blade mencetaknya sebagai literal JavaScript dan garis miringnya lolos menjadi `\/`. Membandingkannya sebagai URL mentah selalu gagal walau halamannya benar. Pembandingnya dibungkus `sepertiDiJs()`.
- Beranda: ketiga baris jam layanan tampil, kalimat berkurung lama tidak ada lagi.
- Suite penuh: fe-ppid **47 lulus**, api-ppid **29 lulus**. `npm run build` sukses.

### Yang perlu Anda lakukan

**Unggah PDF Annual Report** lewat be-ppid → Informasi Publik → Annual Report → Lampiran dokumen, bila salinan unduhannya memang ingin disediakan. Tanpa itu dialognya tetap berfungsi — tombol Di Lihat Saja bekerja, dan tombol Unduh diganti keterangan bahwa salinannya belum ada.

### Yang belum dikerjakan

Tidak berubah dari putaran 62: tombol buka suspend akun panel, dan kolom dokumen yang diminta pada modul Permohonan di be-ppid.
---


## Status Pengerjaan (putaran 62 — langkah 82 & 83)

### Basis data sempat terhapus di tengah putaran ini

Perlu dibaca lebih dulu, karena mengubah isi basis data.

Saat menjalankan `php artisan test` di fe-ppid untuk memeriksa pekerjaan langkah 83, seluruh tabel `ppiddb` hilang. Tinggal 5 tabel bawaan Laravel, persis seperti kejadian **18 Agustus 2026** yang tercatat di bawah — dan sekarang penyebabnya diketahui.

Tujuh berkas tes peninggalan Breeze (`tests/Feature/Auth/*` dan `ProfileTest.php`) memakai `RefreshDatabase`. Sifat trait itu menjalankan `migrate:fresh` sebelum tesnya jalan. Yang membuatnya berbahaya di proyek ini: `phpunit.xml` **tidak** menunjuk basis data terpisah — baris `DB_CONNECTION=sqlite` dan `DB_DATABASE=:memory:` di sana dikomentari — jadi yang dibangun ulang adalah `ppiddb` yang sedang dipakai.

Tes-tes itu sendiri sudah tidak berguna sejak lama: route, controller, dan view Breeze (`/login`, `/register`, `/dashboard`, `/profile`) sudah dihapus dari situs publik, dan komentarnya masih ada di `routes/web.php`. Tesnya ditinggalkan. Jadi tujuh berkas itu tidak pernah bisa lulus — satu-satunya yang mereka lakukan adalah menghapus basis data setiap kali seseorang menjalankan seluruh suite.

**Pemulihan** mengikuti prosedur 18 Agustus: 20 migrasi, `db:seed`, sepuluh seeder konten, dan akun admin dibuat ulang. Hasilnya 49 tabel; 24 informasi publik, 22 informasi dikecualikan, 8 regulasi, 7 simpul struktur, 5 FAQ, 1 maklumat, 1 halaman profil, 19 modul, 4 role, 76 baris hak akses. Perubahan data langkah 82 dan 83 dipasang ulang di atasnya.

**Yang tidak kembali:** akun pemohon, permohonan, keberatan, dan berita yang pernah diinput sejak pemulihan 18 Agustus. Tidak ada backup di mesin ini. Akun admin dibuat ulang dengan kata sandi sementara — **segera ganti** lewat `php artisan ppid:set-password admin@foodstation.co.id`.

**Lubangnya ditutup, bukan sekadar dibersihkan:**

1. Ketujuh berkas tes Breeze dihapus. Yang diujinya sudah tidak ada.
2. `tests/TestCase.php` di **kedua** aplikasi kini menggagalkan tes mana pun yang memakai `RefreshDatabase` sementara basis datanya bukan basis data uji. Penjagaannya di kelas induk yang dilewati setiap tes, bukan diserahkan pada ingatan orang yang menulis tes berikutnya. Pesannya menyebutkan apa yang akan hilang dan apa gantinya (`DatabaseTransactions`).

Setelah itu `php artisan test` di fe-ppid: **46 lulus**, dan basis datanya utuh — 49 tabel, 24 informasi publik.

---

### Langkah 82 — jam layanan

Tiga tempat menyimpan jam layanan, dan ketiganya harus sepakat:

| Tempat | Peran |
| --- | --- |
| `pengaturan_situs` → `kontak.jam_layanan` | Nilai hidup, disunting petugas lewat CMS. Tampil di bagian Kontak beranda |
| `HomeController` | Nilai cadangan bila baris CMS-nya belum ada |
| `PpidController::showServiceStandardPage()` | Tabel Waktu Layanan di halaman Standar Layanan |

Ketiganya diubah, plus `KontenAwalSeeder` supaya pemasangan baru ikut benar sejak awal:

- **Senin–Jumat, 08:00–15:00 WIB, istirahat 12:00–13:00 WIB.**

Kunci `break` pada tabel Waktu Layanan dihidupkan kembali — langkah 70 sempat melepasnya karena istirahat tidak lagi diumumkan. Sekarang justru penting disebut: jendelanya pendek, dan tanpa keterangan itu pemohon yang datang pukul 12:30 mengira layanan masih buka sampai sore.

Terjemahan Inggrisnya ditambahkan ke `lang/en.json`. Tidak ada lagi `17:00` maupun `17.00` yang tersisa di `app/`, `resources/`, atau seeder.

---

### Langkah 83 — Annual Report: lihat bebas, unduh setelah disetujui

Dua keputusan diambil lebih dulu karena keduanya mengubah bentuk pekerjaannya, dan keduanya bukan keputusan teknis:

- **Yang membuka tombol Unduh: permohonan yang sudah disetujui petugas** (status `selesai`), bukan sekadar permohonan terkirim. Keputusan pelepasan salinan tetap di tangan PPID sesuai alur UU KIP; portal hanya menjalankan keputusan yang sudah dibuat.
- **Pratinjau membaca berkas terunggah**, bukan menyematkan tautan luar. Situs luar tidak bisa dipaksa "preview only", dan banyak menolak ditampilkan dalam bingkai.

#### Dibangun sebagai penanda, bukan daftar nama

`informasi_publik.unduhan_terbatas` menandai dokumen mana yang tunduk pada aturan ini. Annual Report yang diminta, tetapi petugas bisa menerapkannya pada dokumen lain kapan saja tanpa menunggu perubahan program.

`permohonan_informasi.informasi_publik_id` mencatat permohonan ini meminta dokumen yang mana. Tanpa kolom itu tidak ada cara memeriksa "sudah pernah disetujui untuk dokumen ini" — `rincian_informasi` hanya teks bebas, dan mencocokkan judul di dalamnya adalah tebakan, bukan pemeriksaan. Relasinya `nullOnDelete`: menghapus dokumen dari daftar tidak boleh menghapus riwayat bahwa seseorang pernah memintanya dan petugas pernah memutuskannya.

#### Penandanya memindahkan berkasnya

Ini bagian yang hampir salah. Rencana awalnya menaruh pemeriksaan hak di controller dan selesai — dan itu tidak akan menegakkan apa pun.

`public/storage` pada fe-ppid adalah **symlink** ke `storage/app/public`. Apa pun di sana dilayani web server secara langsung, tanpa satu baris PHP pun berjalan. Selama berkasnya di folder itu, seluruh pemeriksaan hak bisa dilewati cukup dengan menyalin alamat berkasnya.

Karena itu penandanya bekerja secara fisik. Disk baru `dokumen_terbatas` (`storage/app/dokumen-terbatas`, di luar symlink) ditambahkan di kedua aplikasi, dan `InformasiPublikController` memindahkan berkasnya setiap kali penandanya berubah:

- dinyalakan → `media` (publik) → `dokumen_terbatas` (privat)
- dimatikan → `dokumen_terbatas` → `media`

`path_file` tidak berubah; disk tempatnya tinggal selalu disimpulkan dari penandanya. Satu sumber kebenaran, tidak ada kolom kedua yang bisa berbeda isinya.

Terbukti saat penandanya dinyalakan lewat API sungguhan: berkasnya hilang dari `storage/app/public/…` dan muncul di `storage/app/dokumen-terbatas/…`, dan alamat publik lamanya menjawab **404**.

#### Pratinjau memakai pdf.js, bukan bingkai PDF

Halaman `/informasi/dokumen/{id}` menggambar dokumennya ke canvas lewat `sampul-pdf.js` — mesin yang sama yang sudah dipakai halaman Regulasi. Bedanya dengan `<iframe>`: tidak ada penampil PDF bawaan peramban, jadi tidak ada tombol unduh bawaannya.

**Batasnya dikatakan apa adanya**, tidak dijanjikan lebih: berkas yang sudah sampai di peramban selalu bisa disimpan oleh orang yang cukup gigih. Yang benar-benar ditegakkan adalah tidak ada URL berkas yang bisa disebar, dan salinan resmi hanya keluar lewat pintu unduh yang memeriksa keputusan petugas. Mencegah penyimpanan sama sekali menuntut render per halaman jadi gambar — perlu ImageMagick/pdftoppm, yang tidak ada di mesin ini.

#### Empat keadaan tombol

`App\Support\AksesDokumen` memutuskan, dan dipakai bersama oleh daftar, halaman pratinjau, dan penyaji berkasnya — supaya tombol tidak pernah mengatakan satu hal sementara penyajinya melakukan hal lain:

| Keadaan | Tampilan |
| --- | --- |
| `masuk` | Belum masuk → "Masuk & Ajukan Permohonan" |
| `ajukan` | Sudah masuk, belum pernah minta → "Ajukan Permohonan" |
| `menunggu` | Permohonannya masih diproses → nomor permohonan + tautan ke statusnya |
| `terbuka` | Sudah disetujui → tombol Unduh, menyebut nomor permohonan yang jadi dasarnya |

Permohonan yang **ditolak** kembali ke `ajukan`, bukan bertahan di `menunggu`: keputusannya sudah ada, dan pemohon boleh mencoba lagi.

Formulir permohonan yang dibuka dari halaman ini membawa nomor dokumennya di isian tersembunyi dan mengisi Rincian Informasi otomatis. Nomor itu **diperiksa ulang di server** — isian tersembunyi bisa disunting siapa pun sebelum dikirim, dan nomor itulah yang nanti membuka tombol unduh.

### Verifikasi

- **11 tes baru** untuk langkah 83 (`DokumenTerbatasTest`), semuanya lulus. Yang diuji termasuk dua hal yang paling mudah salah: persetujuan **tidak** berlaku untuk pemohon lain, dan persetujuan atas dokumen lain **tidak** membuka dokumen ini. Bila salah satu keliru, satu persetujuan akan membuka dokumen itu untuk semua pemegang akun dan keputusan petugas kehilangan artinya.
- Keempat status yang belum diputus (`diajukan`, `diverifikasi`, `diproses`, `menunggu_approval`) diuji satu per satu — tidak satu pun membuka unduhan.
- Alur lengkapnya juga dijalankan lewat HTTP sungguhan sebelum dijadikan tes: tamu **200** pratinjau / **302** unduh; belum mengajukan **302**; menunggu **302**; ditolak **302**; disetujui **200**; pemohon lain **302**.
- Daftar Informasi Publik terbukti tidak memuat alamat berkasnya, dan memuat tautan pratinjaunya.
- Halaman `/`, `/informasi`, `/informasi/berkala`, `/regulasi`, `/faq`, dan `/standar-layanan/jalur-waktu-layanan`: **200**. Panel be-ppid dan 5 endpoint api-ppid: **200**.
- Suite penuh: api-ppid **29 lulus**, fe-ppid **46 lulus**.

### Yang perlu Anda lakukan

1. **Ganti kata sandi admin** yang dibuat ulang saat pemulihan.
2. **Unggah PDF Annual Report** lewat be-ppid → Informasi Publik → Annual Report → Lampiran dokumen. Penanda **Unduhan terbatas** sudah menyala. Sampai berkasnya diunggah, barisnya tetap menampilkan tautan lama ke `foodstation.id` seperti sebelumnya — fiturnya baru terlihat setelah ada berkas yang bisa dipratinjau.
3. **Pertimbangkan backup berkala `ppiddb`.** Dua kali kehilangan penuh dalam tiga hari, keduanya tanpa backup. Penjagaan yang dipasang hari ini menutup penyebab yang sudah diketahui; ia tidak menolong bila penyebab berikutnya berbeda.

### Yang belum dikerjakan

- **Membuka suspend akun panel** masih belum ada tombolnya (dibawa dari putaran 60 dan 61).
- **Modul Permohonan di be-ppid belum menampilkan dokumen yang diminta.** Kolomnya sudah terisi dan sudah dipakai memutuskan hak unduh, tetapi petugas yang meninjau permohonan belum melihat judul dokumennya di panel — ia baru terbaca di `rincian_informasi` yang terisi otomatis. Menampilkannya sebagai kolom tersendiri akan membuat keputusan petugas lebih jelas kaitannya.

---


## Status Pengerjaan (putaran 61 — langkah 81, email asing di Lupa password)

### Yang dilaporkan bukan kerusakan, melainkan keputusan yang salah

`rudiadriian@gmail.com` bukan akun panel, tetapi formulir Lupa password tetap menjawab "Permintaan diterima — jika email tersebut terdaftar, tautan sudah kami kirim."

Itu perilaku yang sengaja dipasang di putaran 60: jawaban diseragamkan supaya endpoint ini tidak bisa dipakai memastikan alamat mana yang punya akun panel. Kalau jawabannya dibedakan, siapa pun bisa memasukkan alamat satu per satu dan mendaftar seluruh petugas yang ada di sistem.

Yang tidak diperhitungkan waktu itu: alamat yang dilaporkan di atas adalah **salah ketik** dari alamat sungguhan (`rudiadrian16@gmail.com`). Orang yang salah mengetik alamatnya sendiri menerima "periksa juga folder Spam", lalu menunggu email yang tidak akan pernah datang — dan tidak ada apa pun di layar yang memberitahunya bahwa ia sedang menunggu sia-sia. Itu bukan kasus jarang; itu kasus yang paling sering.

Untuk layanan terbuka, jawaban seragam tetap pilihan yang benar. Untuk panel ini, tidak:

- origin-nya sudah dibatasi `ADMIN_ORIGINS`;
- akunnya segelintir dan alamatnya institusional (`@foodstation.co.id`), jadi bisa ditebak tanpa bantuan endpoint mana pun;
- sejak putaran 60, permintaan untuk email asing **ikut dihitung** tangga bertingkat.

Poin terakhir yang membuat pertukarannya masuk akal. Penyisiran alamat berhenti sendiri pada percobaan ketiga — satu jam, lalu satu hari, lalu 14 hari. Yang hilang kecil; yang didapat besar.

### Yang berubah

Email yang tidak berhak menerima tautan sekarang ditolak **422** dengan alasannya, dibedakan menjadi tiga karena tindak lanjutnya berbeda:

| Keadaan | Jawaban |
| --- | --- |
| Tidak terdaftar (atau sudah dihapus) | "Email ini tidak terdaftar sebagai akun panel. Periksa kembali ejaannya…" |
| Terdaftar tapi nonaktif | "Akun ini nonaktif… Hubungi administrator." |
| Terdaftar tapi disuspend | "Akun ini disuspend… Hubungi administrator untuk membukanya kembali." |

Yang berhak tetap menerima **200**, dengan kalimat yang sekarang boleh tegas: "Tautan atur ulang password sudah dikirim ke email tersebut" — bukan lagi "jika email tersebut terdaftar".

Perilaku lamanya tidak dibuang, hanya dipindah ke belakang sakelar `PPID_BERITAHU_EMAIL_ASING`. Diisi `false`, seluruh jawabannya seragam kembali seperti putaran 60. Itu yang harus dipakai bila panel ini suatu saat dapat dijangkau dari internet terbuka, dan catatannya ditulis lengkap di `config/ppid.php` supaya keputusannya bisa ditimbang ulang oleh orang yang tidak ikut percakapan ini.

Halaman panelnya tidak perlu diubah sama sekali: penolakan datang dalam bentuk `[{type: 'email', message}]` yang sama dengan galat lain, jadi spanduk `Alert` dan pesan di bawah kotak email dari putaran 60 langsung menampilkannya.

### Sisi masuk tidak ikut diubah

`sign-in` tetap menjawab "Email atau kata sandi salah" tanpa membedakan email asing dari password salah. Bukan karena itu masih menyembunyikan sesuatu — sekali Lupa password berterus terang, daftar akun bisa disusun dari sana — melainkan karena tidak ada yang didapat dengan mengubahnya. Orang yang mencoba masuk sudah tahu alamat emailnya sendiri; yang tidak tahu adalah alat penyisir password, dan alat itu tidak perlu dibantu membedakan "akun ini ada, lanjutkan menebak" dari "berhenti di sini".

### Verifikasi

- **29 tes hijau** (124 asersi), naik dari 25. Empat tes baru: penolakan email asing, penolakan akun nonaktif dan disuspend, email terdaftar tetap diterima, dan jawaban kembali seragam saat sakelarnya dimatikan.
- Satu tes khusus memastikan remnya tidak ikut lepas: penolakan email asing **tetap** menaikkan hitungan — dua kali **422**, yang ketiga **429** dengan kunci satu jam terpasang. Tanpa tes ini, hilangnya jawaban seragam akan berarti penyisiran alamat tanpa biaya.
- Diuji dengan alamat yang dilaporkan, lewat HTTP sungguhan dan captcha yang benar:
  - `rudiadrian16@gmail.com` → **422** "Email ini tidak terdaftar sebagai akun panel…"
  - `rudiadriian@gmail.com` → **429**, karena alamat itu sudah dipakai dua kali sebelumnya sehingga percobaan ini yang ketiga. Tangganya bekerja persis seperti seharusnya.
- Baris hitungan yang tertinggal dari pengujian dihapus; `percobaan_tautan_admin` dan `percobaan_login_admin` kembali kosong.

### Yang belum dikerjakan

Tetap sama seperti putaran 60: **membuka suspend belum ada tombolnya di panel**. Kolom `disuspend_pada` dan `alasan_suspend` terisi dan terbaca, tetapi administrator masih harus mengosongkannya lewat basis data. Sekarang kebutuhannya bertambah nyata — pesan penolakan di atas menyuruh orang "hubungi administrator", dan administratornya belum punya tempat untuk menindaklanjuti.

---


## Status Pengerjaan (putaran 60 — langkah 81, poin 2–4)

Poin pertama (lambat) sudah dikerjakan putaran 59. Yang baru disebut di sana adalah **login**, dan itu diperiksa ulang di bawah. Tiga poin sisanya — alert galat, lupa password, dan pengaman auth — dikerjakan di putaran ini.

### Login: sisa waktunya memang bcrypt

Setelah perbaikan putaran 59, satu percobaan masuk yang gagal memakan **360–415 ms**, turun dari ~1.500 ms. Sisanya diukur terpisah dengan memisahkan email terdaftar dan tidak:

| | Waktu |
| --- | --- |
| Email tidak terdaftar (tidak sampai ke bcrypt) | 75–95 ms |
| Email terdaftar (password diperiksa) | 360–415 ms |
| `password_verify` sendirian, cost 12 | 209 ms |

Selisihnya bcrypt, dan itu **tidak diturunkan**. `BCRYPT_ROUNDS=12` adalah nilai yang disengaja: biaya inilah yang membuat penebakan password mahal bagi penyerang. Menurunkannya ke 10 memang memangkas 150 ms sekali per sesi masuk, dengan imbalan setiap tebakan menjadi empat kali lebih murah untuk siapa pun yang suatu saat memegang salinan tabel `users`. Pertukaran yang salah arah.

Satu hal yang justru dipercepat: akun yang sudah disuspend sekarang ditolak **sebelum** passwordnya diperiksa. Selain menghemat 200 ms yang percuma, pemiliknya jadi mendapat pesan yang benar — "akun disuspend, hubungi administrator" — bukan "kata sandi salah" yang menyesatkan.

### Poin 2 — halaman auth yang diam saat API mati

Formulir masuk hanya membaca `error.data`, yakni galat validasi per isian. Semua kegagalan lain tidak memunculkan apa pun: API belum dinyalakan, proxy menjawab 502, koneksi putus — tombolnya berhenti berputar dan halaman diam saja.

Itu keadaan terburuk yang mungkin untuk halaman masuk. Orang yang tidak melihat pesan apa-apa akan mengira salah ketik, lalu mencoba lagi dengan password berbeda — dan sejak putaran ini, setiap percobaan menaikkan hitungan kunci bertingkat. Bisu ditambah kunci bertingkat sama dengan mengunci orang dari akunnya sendiri karena servernya mati.

`src/@auth/services/jwt/utils/pesanGalat.ts` menerjemahkan galat apa pun menjadi kalimat. Tiap cabangnya wajib menghasilkan kalimat, tidak boleh string kosong:

- `TypeError` dari `fetch` (API mati sama sekali) → "Tidak dapat menghubungi server API. Pastikan layanan api-ppid sedang berjalan."
- `TimeoutError` → server tidak menjawab dalam waktu wajar.
- 401/403/404/429/500/502/503/504 masing-masing punya kalimatnya sendiri, dengan angka statusnya ikut dicetak supaya bisa dilaporkan tanpa membuka alat pengembang.
- Badan JSON `[{type, message}]` dari api-ppid tetap dipakai apa adanya dan ditempelkan ke isiannya.

Hasilnya ditampilkan sebagai spanduk `Alert` di atas formulir — bukan hanya sebagai teks kecil di bawah kotak password. Pesan seperti "coba lagi setelah 1 jam" terlalu penting untuk disembunyikan di sana. Warnanya dibedakan: kuning bila permintaannya tidak pernah sampai ke server (periksa servernya), merah bila server menjawab dan menolak (periksa ketikan).

### Poin 3 — lupa password

Sebelumnya tautan "Forgot password?" mengarah ke `/#`. Sekarang ada dua halaman:

- `/lupa-password` — masukkan email, terima tautan.
- `/reset-password?token=…&email=…` — dibuka dari email, pasang password baru.

Di sisi API, `PasswordResetController` memakai broker `users` bawaan Laravel dengan tabel `password_reset_tokens`, terpisah dari token akun pengunjung sehingga token satu jenis akun tidak pernah bisa dipakai mengambil alih akun jenis lain.

Notifikasi bawaan Laravel tidak dipakai: ia menyusun tautannya dari `route('password.reset')`, rute halaman web yang tidak ada di API ini. Yang harus dibuka petugas adalah halaman di panel — aplikasi terpisah di port lain — jadi `User::sendPasswordResetNotification()` diambil alih `EmailAkunAdmin`, yang menyusun tautannya dari `PPID_PANEL_URL`.

Empat keputusan yang perlu diketahui:

**Jawabannya selalu sama.** Terdaftar atau tidak, endpoint-nya menjawab kalimat yang sama persis. Kalau dibedakan, tombol "Lupa password" berubah menjadi alat mendaftar email petugas mana saja yang ada di sistem.

**Hitungan permintaan dinaikkan sebelum tahu emailnya terdaftar.** Kalau hanya yang terdaftar yang dihitung, selisih perilakunya sendiri sudah cukup untuk membedakan — pesannya seragam tapi waktunya tidak.

**Umur tautannya satu sumber.** `config/auth.php` sebelumnya memaku `expire => 60`, sementara badan emailnya mencetak angka dari tempat lain. Dua angka yang berdiri sendiri untuk hal yang sama akan berpisah suatu saat, dan yang terjadi adalah surat yang menjanjikan "berlaku 60 menit" untuk tautan yang sudah mati sejak menit ke-15. Keduanya sekarang membaca `PPID_UMUR_TAUTAN_MENIT`.

**Password baru membuka kunci yang sedang berlaku.** Orang yang baru saja membuktikan dirinya pemilik email itu harus bisa langsung masuk. Membiarkan kuncinya berarti fitur ini tidak menyelesaikan apa pun.

Setiap penggantian password juga mengirim pemberitahuan ke pemiliknya. Itu bukan basa-basi: kalau bukan dia yang mengganti, email itulah satu-satunya tanda bahwa akunnya sudah berpindah tangan.

### Poin 4 — pengaman fitur auth

**Captcha.** Gambar GD buatan sendiri, tanpa layanan pihak ketiga — panel tidak boleh bergantung pada jaringan luar saat orang mencoba masuk, dan tidak ada kunci layanan captcha yang tersedia. Konsepnya mengikuti yang sudah ada di portal pemohon, dengan satu perbedaan yang memaksa: **panel adalah SPA tanpa sesi**, jadi jawabannya tidak bisa dititipkan di session seperti di fe-ppid. Sebagai gantinya tiap kode punya `id` sendiri, dan yang disimpan server hanyalah hash-nya di cache dengan masa hidup 5 menit. Sekali diperiksa langsung dibuang — benar atau salah — sehingga satu gambar hanya berlaku untuk satu kali kirim.

Konsekuensinya di sisi panel: setiap kiriman yang gagal harus memuat gambar baru. Tanpa itu, percobaan berikutnya pasti ditolak dengan alasan captcha, dan orangnya akan mengira passwordnya yang salah. Ketiga formulir melakukannya otomatis.

Dipasang di tiga titik sesuai permintaan: masuk, minta tautan lupa password, dan pasang password baru.

**Kunci bertingkat.** Tiap 3 percobaan menaikkan satu tahap:

| Percobaan | Akibat |
| --- | --- |
| 3 | Tunggu 1 jam |
| 6 | Tunggu 1 hari |
| 9 | Tunggu 14 hari |
| 12 | **Akun disuspend** — hanya administrator yang membukanya |

Berlaku sama untuk percobaan masuk dan permintaan tautan lupa password, tetapi **hitungannya terpisah**. Menggabungkannya berarti orang yang benar-benar lupa password ikut terkunci dari mencoba masuk hanya karena meminta tautan beberapa kali — dua penyalahgunaan yang berbeda tidak boleh berbagi satu penghitung.

Tangganya sendiri ditaruh di satu kelas (`KunciBertingkat`) justru supaya bisa diuji tanpa basis data, dan supaya tidak ada versi kedua yang tertinggal kalau angkanya digeser suatu saat.

Hitungannya disimpan di basis data, bukan cache: masa kuncinya bisa berminggu-minggu, sedangkan cache berkas bisa terhapus tanpa sengaja dan kuncinya ikut hilang bersamanya.

Kuncinya dipasang per **email + IP**, bukan per email saja. Kalau per email, siapa pun yang tahu alamat seorang petugas bisa menutup akunnya selama 14 hari hanya dengan sengaja salah password sembilan kali — pemblokiran layanan yang lebih merugikan daripada serangan yang hendak dicegah. Suspend pada tahap keempat adalah pengecualiannya, dan memang harus: kalau satu IP sudah gagal 12 kali pada satu akun, itu bukan lagi salah ketik.

Suspend memakai kolom baru `users.disuspend_pada`, sengaja dibedakan dari `users.is_active`. Yang satu hasil pengamanan otomatis, yang satu keputusan administratif — keduanya menutup akses, tetapi hanya yang pertama yang perlu dijelaskan ke pemiliknya sebagai pemblokiran keamanan.

**Hanya email terdaftar.** Dipenuhi dengan menolaknya, bukan dengan mengatakannya. Email asing mendapat jawaban yang sama persis dengan password salah — status maupun bentuk pesannya — dan ikut menaikkan hitungan kunci. Jawaban yang membedakan keduanya akan berubah menjadi alat untuk mendaftar email petugas mana saja yang ada di sistem ini.

### Satu penghitung dibuang, bukan ditambah

`AuthController` sudah punya penghitung kegagalan sendiri sebelum putaran ini: 10 kegagalan per 15 menit lewat cache. Penghitung itu **dihapus**, bukan dibiarkan berdampingan dengan yang baru.

Alasannya ketahuan dari tes yang gagal: dua penghitung untuk pekerjaan yang sama bukan pertahanan berlapis, melainkan saling menutupi. Yang lebih longgar berbunyi lebih dulu — pada kegagalan ke-10 — sehingga tangga yang seharusnya berakhir di suspend pada kegagalan ke-12 tidak akan pernah sampai ke sana. Fitur yang diminta akan terpasang rapi di kode dan tidak pernah berjalan.

Yang bertahan adalah yang lebih kuat: hitungannya di basis data, masa tunggunya naik sampai 14 hari, dan ujungnya menutup akun. `throttle:login` di berkas rute tetap ada dan tidak tumpang-tindih — jendelanya satu menit, tugasnya menahan banjir, bukan menghitung kegagalan sepanjang hari.

### Dua cacat lain yang ikut ketahuan

**Email huruf besar membuat lupa password gagal diam-diam.** PostgreSQL membedakan huruf besar-kecil pada `=`, sedangkan seluruh jalur auth membakukan email yang diketik ke huruf kecil sebelum mencarinya. Akun yang dibuat sebagai `Budi@Foodstation.co.id` tidak akan pernah ditemukan — tanpa galat, tanpa tanda, sampai ada petugas yang benar-benar membutuhkannya. Saat ini belum ada baris seperti itu di `ppiddb`, tetapi tidak ada yang mencegahnya muncul besok lewat modul Pengguna. Ditutup dari dua sisi: mutator di `User` membakukan email pada setiap penulisan, dan `User::denganEmail()` mencari tanpa membedakan huruf besar-kecil untuk baris lama.

**Tautan "Sign up" di halaman masuk mengantar ke formulir yang pasti gagal.** Pendaftaran mandiri sudah ditutup — `authSignUp` menolak setiap panggilan — tetapi tautannya masih ada. Diganti keterangan tentang dari mana akun panel sebenarnya berasal.

### Verifikasi

- **25 tes hijau** (110 asersi), naik dari 2. Delapan menguji tangganya tanpa basis data (`KunciBertingkatTest`), lima belas menguji endpoint-nya sungguhan lewat HTTP (`AuthKeamananTest`, dengan `DatabaseTransactions` sehingga tidak ada yang tertinggal di `ppiddb`).
- Tangga naiknya diuji berurutan sampai suspend: kegagalan ke-3 → tahap 1 (60 menit), ke-6 → tahap 2 (1.440 menit), ke-9 → tahap 3 (20.160 menit), ke-12 → `disuspend_pada` terisi. Kuncinya dilewati dengan memundurkan waktu berakhirnya, bukan dengan menghapus barisnya, supaya hitungannya tetap berjalan.
- Percobaan yang ditolak saat sedang terkunci terbukti **tidak** menambah hitungan — ditolak di gerbang tidak boleh mempercepat perjalanan menuju suspend.
- Jalur captcha diuji lewat HTTP sungguhan, di luar PHPUnit (tempat captcha dimatikan): kode benar diterima, kode yang sama dipakai ulang ditolak **422**, dan `lupa-password` dengan captcha benar menjawab **200** dengan pesan seragam.
- Login tanpa captcha ditolak **422** di ketiga endpoint.
- `npx tsc --noEmit` bersih; `eslint` bersih setelah `--fix` merapikan dua baris. Kelima modul baru terbukti dilayani Vite (`200`), dan proxy `:3000 → :8001` untuk `auth/captcha` juga **200**.
- Tautan reset yang dihasilkan diperiksa isinya: `http://localhost:3000/reset-password?token=…&email=…` — mengarah ke rute panel yang baru didaftarkan, bukan ke API.
- Baris uji yang sempat tertulis saat pengujian lewat HTTP (`percobaan_login_admin`, `percobaan_tautan_admin`, `pengiriman_tautan_admin`, `password_reset_tokens`, dan 22 baris `audit_log`) dihapus setelahnya.

### Perlu diketahui

- **Satu email sungguhan terkirim** ke `admin@foodstation.co.id` saat pengujian jalur lupa password lewat HTTP, karena `MAIL_MAILER=smtp` di `.env`. Isinya tautan atur ulang password yang sah; tokennya sudah dihapus dari basis data, jadi tautan itu kini tidak berlaku.
- **`composer test` sekarang membuang cache config lebih dulu.** Selama cache config ada, `<env>` di `phpunit.xml` tidak terbaca sama sekali — `CACHE_DRIVER=array` dan `BCRYPT_ROUNDS=4` diabaikan, sehingga tes saling mewarisi keadaan pembatas laju dan berjalan dengan bcrypt penuh. Ini persis jebakan yang disebut putaran 59; sekarang tertutup di skripnya.
- **`dev/serve.php` diperbaiki.** Versi putaran 59 memasang OPcache lewat `PHP_INI_SCAN_DIR`, dan itu tidak bekerja saat dijalankan lewat `composer serve`: `ServeCommand` menyaring environment yang diteruskan ke `php -S` memakai daftar-putih `$passthroughVariables`, dan `PHP_INI_SCAN_DIR` tidak ada di daftar itu — nilainya justru dihapus dari proses anak. OPcache mati persis di proses yang melayani permintaan, tanpa tanda apa pun; yang terlihat cuma server yang lambat lagi. Sekarang `php -S` dipanggil langsung dengan opsi `-d`, tanpa perantara. Terverifikasi lewat `opcache_get_status()` dari dalam proses servernya: `enabled=true`, dan `/api/v1/health` kembali ke **22–29 ms**.

### Yang belum dikerjakan

- **Membuka suspend belum ada tombolnya di panel.** Kolom `disuspend_pada` dan `alasan_suspend` sudah terisi dan terbaca, tetapi administrator masih harus mengosongkannya lewat basis data. Modul Pengguna perlu satu aksi "Buka suspend" — pekerjaan sisi panel yang belum diminta di langkah 81.
- **Captcha gambar GD lebih lemah daripada reCAPTCHA/Turnstile.** Ia menahan skrip sederhana, bukan pemecah captcha sungguhan. Yang menahan serangan serius di sini tetap kunci bertingkat dan pembatas laju; captcha-nya lapis tambahan, bukan lapis utama.

---


## Status Pengerjaan (putaran 59 — langkah 81)

### Yang lambat ternyata bukan kode panelnya

Keluhannya ada di be-ppid, tapi penyebabnya tidak satu pun ada di be-ppid. Diukur dulu sebelum diubah: setiap endpoint api-ppid dipanggil lewat HTTP, lalu permintaan yang sama dijalankan ulang di dalam satu proses PHP yang sudah panas.

| Endpoint | Lewat HTTP | Di dalam proses | Query |
| --- | --- | --- | --- |
| `me/navigation` | 1.104 ms | 44 ms | 3 |
| `permohonan` (15 baris) | 1.101 ms | 47 ms | 3 |
| `berita` (15 baris) | 928 ms | 21 ms | 1 |
| `dashboard/analitik` | 1.186 ms | 46 ms | 31 |

Selisihnya — sekitar satu detik pada setiap permintaan — tidak dihabiskan oleh kode aplikasi maupun basis data. Bahkan `GET /api/v1/health`, rute yang isinya cuma mengembalikan `{"status":"ok"}` dan tidak menyentuh basis data sama sekali, memakan **340 ms**. Itu harga yang dibayar sebelum baris pertama kode PPID dijalankan.

Ada tiga sumbernya, semuanya di lingkungan, bukan di aplikasi.

### 1. OPcache mati — PHP mengompilasi ulang Laravel tiap permintaan

`php -m` tidak memuat Zend OPcache sama sekali. Tanpa itu PHP membaca, mem-parsing, dan mengompilasi ulang lebih dari 1.500 berkas Laravel dan vendor pada **setiap** permintaan HTTP, lalu membuang hasilnya begitu permintaan selesai. `php_opcache.dll` sudah ada di XAMPP, hanya tidak pernah dinyalakan.

Menyalakannya lewat `C:\xampp\php\php.ini` akan mengubah perilaku semua proyek PHP di mesin ini, jadi bukan itu yang dipakai. `dev/php.ini.d/00-opcache.ini` dimuat lewat variabel lingkungan `PHP_INI_SCAN_DIR`, yang diwarisi proses anak — `artisan serve` menjalankan `php -S` sebagai anak, jadi server dev-nya ikut menyala dengan OPcache tanpa php.ini XAMPP disentuh.

`opcache.validate_timestamps` sengaja dibiarkan hidup (dicek 2 detik sekali) supaya hasil edit kode tetap langsung terlihat; yang dihindari cuma kompilasi ulangnya, bukan pemeriksaan perubahannya.

### 2. PostgreSQL melahirkan satu proses per sambungan

Setelah OPcache menyala, `health` turun ke 40 ms tetapi endpoint yang butuh login masih 280 ms. Selisih 240 ms itu muncul hanya pada permintaan yang menyentuh basis data — dan terukur langsung:

```
pg_connect  →  ~195 ms
```

PostgreSQL menangani tiap sambungan dengan satu proses backend terpisah. Di Linux itu `fork()` yang murah; di Windows itu `CreateProcess` penuh. PHP menutup sambungannya di akhir tiap permintaan, jadi ongkos ~120–200 ms itu dibayar ulang terus-menerus.

`DB_PERSISTENT=true` menahan sambungannya tetap terbuka di dalam proses PHP, sehingga permintaan berikutnya memakai yang sudah ada:

| | Buka sambungan + `select 1` |
| --- | --- |
| Sambungan baru tiap kali | 120,0 ms |
| Sambungan dipakai ulang | 24,1 ms |

Bawaannya tetap **mati** di `config/database.php`. Sambungan yang dipakai ulang ikut membawa sisa keadaan sesi kalau sebuah permintaan mati di tengah transaksi, jadi ini keputusan per lingkungan — dinyalakan di `.env` mesin ini, didokumentasikan mati di `.env.example`.

Sekalian: `sslmode` yang tadinya dipaku `prefer` sekarang bisa diatur lewat `DB_SSLMODE`. `prefer` membuat libpq mencoba jalur TLS dulu dan mundur ke sambungan biasa — dua kali jabat tangan untuk sambungan yang tidak pernah meninggalkan mesin ini.

### 3. Konfigurasi dan rute dibaca ulang dari nol

30-an berkas `config/*.php` di-parsing dan seluruh berkas rute didaftarkan ulang tiap permintaan. `config:cache` + `route:cache` menggabungkan masing-masing jadi satu berkas; hasilnya ~25–40 ms lagi.

Cache rute yang tertinggal itu jebakan: rute yang ditambahkan setelahnya diam-diam tidak terdaftar. Karena itu `dev/serve.php` yang mengurusnya — dibangun ulang setiap kali server dinyalakan, dan dibuang lagi lewat `register_shutdown_function` saat server berhenti, sehingga keadaan diam proyek ini tetap "tanpa cache". Jalur yang dipakai jadi `composer serve`, bukan `php artisan serve`.

### Hasilnya

Rata-rata tiga panggilan panas, setelah tiga panggilan pemanasan:

| Endpoint | Sebelum | Sesudah | |
| --- | --- | --- | --- |
| `health` | 340 ms | **37 ms** | 9× |
| `me/navigation` | 1.104 ms | **31 ms** | 36× |
| `permohonan` (15 baris) | 1.101 ms | **35 ms** | 31× |
| `pemohon` (15 baris) | 1.076 ms | **33 ms** | 33× |
| `dashboard/ringkasan` | 1.250 ms | **34 ms** | 37× |
| `dashboard/analitik` | 1.186 ms | **52 ms** | 23× |
| `notifikasi` | 1.464 ms | **28 ms** | 52× |
| 5 permintaan bersamaan | 4.203 ms | **563 ms** | 7× |

Angka HTTP-nya sekarang sama dengan angka di dalam proses. Artinya ongkos di luar aplikasi sudah habis — yang tersisa memang kerja aplikasinya sendiri.

Baris "5 permintaan bersamaan" perlu dibaca terpisah. `artisan serve` melayani satu permintaan pada satu waktu, jadi permintaan yang ditembakkan berbarengan oleh panel mengantre. Dulu antreannya 5 × ~840 ms; sekarang 5 × ~110 ms. Sifat mengantrenya belum hilang — hanya sudah tidak terasa. Kalau suatu saat perlu benar-benar paralel, api-ppid harus dilayani Apache/nginx, bukan server bawaan PHP.

### Email tidak lagi ditunggu petugas

Terpisah dari soal lingkungan, ada satu tempat aplikasi ini memang menahan tanggapan: `App\Support\EmailPemohon` menjalankan percakapan SMTP ke `srv179.niagahoster.com` di tengah permintaan. Sambung + jabat tangan TLS-nya saja sudah 120 ms sebelum AUTH, DATA, dan QUIT.

Akibatnya, saat petugas menekan Simpan pada verifikasi data pemohon atau perubahan status pengajuan, dialognya baru tertutup setelah email selesai terkirim — padahal datanya sudah tersimpan sejak tadi.

Kedua pemanggilan `Mail::to()->send()` sekarang lewat `EmailPemohon::antre()`, yang membungkusnya dengan `dispatch(...)->afterResponse()`. SMTP baru mulai bekerja setelah tanggapan lengkap sampai di peramban. `QUEUE_CONNECTION` tidak diubah dan tidak ada worker yang harus dijalankan.

### Sisi be-ppid

Tiga perubahan, semuanya kecil karena kode panelnya sendiri memang tidak bermasalah:

- **`optimizeDeps.include` dilengkapi.** Pustaka yang cuma dipakai halaman yang dimuat malas — `material-react-table`, `apexcharts`, `notistack`, `react-hook-form`, dan sembilan lainnya — baru ditemukan Vite di tengah sesi, dan penemuan itu memicu optimasi ulang plus **muat ulang halaman penuh** tepat saat operator membuka modulnya. Sekarang semuanya di-prebundle saat dev server menyala.
- **`refetchOnWindowFocus` dimatikan, `gcTime` 30 menit.** Berpindah tab bukan tanda datanya berubah; tanpa ini setiap panel mendapat fokus semua query basi ditembak serentak ke server yang melayani satu permintaan pada satu waktu. `gcTime` yang panjang membuat kembali ke modul yang tadi dibuka menampilkan tabel dari cache, bukan menunggu API lagi.
- **`manualChunks` untuk build produksi.** React, MUI, tabel, grafik, dan editor dipisah dari kode panel supaya menambah satu modul CMS tidak membatalkan cache peramban untuk semuanya sekaligus.

### Verifikasi

- Siklus CRUD penuh lewat HTTP pada modul FAQ: create **201**, update **200** (39 ms), show **200**, delete **200** (44 ms). Transaksi dan audit log tetap benar dengan sambungan persisten; baris ujinya dihapus permanen berikut tiga jejak auditnya.
- Stempel waktu tidak bergeser: baris uji tercatat `2026-08-21T02:31:41Z` untuk 09:31 WIB. `SET TimeZone` tetap dijalankan ulang pada tiap sambungan yang dipakai ulang.
- `auth/sign-in-with-token` **200** (45 ms). Login dengan kata sandi salah tetap **401** dalam 400 ms — itu bcrypt, dan memang tidak boleh dipercepat.
- Jalur penuh lewat proxy Vite (`:3000` → `:8001`) diukur terpisah: `permohonan` **29 ms**, `me/navigation` **25 ms**.
- `npx tsc --noEmit` bersih. Vite menyala dalam 2,1 detik; keempat belas pustaka yang baru ditambahkan terbukti ada di `_metadata.json`, tanpa galat resolusi.
- Tes api-ppid tetap hijau: **2 lulus**.
- `php -l` bersih pada `EmailPemohon.php`, `config/database.php`, dan `dev/serve.php`.

### Yang tidak dikerjakan

- **fe-ppid** (situs publik, `:8000`) berjalan di bawah `artisan serve` tanpa OPcache dan menanggung ongkos ~340 ms yang persis sama. Langkah 81 menyebut be-ppid, jadi tidak disentuh — tetapi `dev/serve.php` bisa disalin ke sana apa adanya.
- **Middleware `CheckModulAkses`** menjalankan tiga query per permintaan. Sempat dipertimbangkan untuk di-cache, lalu tidak: totalnya 6 ms dari permintaan 30-an ms, sementara cache hak akses menuntut invalidasi yang benar setiap kali hak role berubah. Risikonya lebih besar daripada hasilnya.
- **Indeks basis data** tidak ditambah. Waktu query terukur 1,6–18 ms untuk seluruh endpoint, termasuk `dashboard/analitik` yang menjalankan 31 query dalam 8 ms. Belum ada yang perlu diindeks.

---


## Status Pengerjaan (putaran 58 — langkah 80)

### Kenapa terlihat hijau polos

Bagian **Kategori Informasi → Informasi Publik** di beranda sebenarnya sudah punya latar gambar — foto stok Unsplash — tetapi ditutup gradasi hijau beropasitas **0,96**. Praktis tidak ada gambar yang tembus, jadi yang terlihat memang blok hijau. Sekalian ketahuan cacat lain: situs publik menarik gambar itu dari CDN pihak ketiga tiap kali halaman dibuka.

Keduanya diganti sekali jalan: gambar maskot PPID milik sendiri, dengan gradasi yang benar-benar tembus pandang. Tidak ada lagi rujukan `unsplash` di seluruh `resources/views` dan `app/`.

### Gambarnya dikecilkan dulu, bukan dipasang apa adanya

`ppid_foody_dimana_saja.png` aslinya **2,2 MB** (1536 × 1024). Latar halaman tidak boleh seberat itu — beban yang dibayar tiap pengunjung, termasuk yang membuka dari ponsel dengan kuota. Dikonversi ke WebP mutu 82 pada lebar aslinya (tanpa perbesaran): **204 KB**, turun ~91%.

Konversinya memakai GD bawaan PHP (`imagewebp`), karena tidak ada ImageMagick di mesin ini. Hasilnya di `public/assets/images/ppid/ppid-di-mana-saja.webp`. Berkas PNG aslinya dibiarkan di tempatnya sebagai master.

WebP saja, tanpa `<picture>` dan berkas cadangan: seluruh peramban arus utama sudah mendukungnya sejak 2020 (Safari 14 ke atas), dan menggandakan berkas latar hanya untuk peramban yang sudah tidak dipakai justru menambah berat yang tadi dikurangi.

### Satu partial, tiga tempat

`resources/views/partials/latar_informasi.blade.php` — tiga lapis, urutannya menentukan keterbacaan:

1. gambar, diredupkan lewat `filter: brightness()`;
2. gradasi hijau `fs-gradient` dengan opasitas yang diatur pemanggil;
3. pola titik tipis, sama seperti hero lain.

Dipakai di tiga tempat, karena ketiganya bagian dari area Informasi Publik yang sama-sama hijau polos:

| Berkas | Bagian | Opasitas hijau | Kecerahan gambar |
| --- | --- | --- | --- |
| `ppid/home.blade.php` | section Kategori Informasi | 0,55 | 0,78 |
| `ppid/information_index.blade.php` | hero Informasi Publik | 0,66 | 0,68 |
| `ppid/information.blade.php` | hero per kategori | 0,66 | 0,68 |

Section beranda boleh lebih tembus karena isinya kartu berlatar sendiri; hero halaman teksnya duduk langsung di atas latar. Beranda juga memakai `scrim`: gelap tambahan di sisi atas, tepat tempat judul section berada. Hero memakai `loading="eager"` (di atas lipatan), section beranda `lazy`.

Ketiga section tetap diberi `bg-[#08281B]` sebagai dasar — bila gambarnya gagal dimuat, latarnya kembali hijau gelap, bukan jadi putih dengan teks putih di atasnya. Gambarnya `aria-hidden` dan `alt=""`: ini hiasan, pembaca layar tidak punya urusan dengannya.

### Angkanya diukur, bukan dikira-kira

Ilustrasinya punya bidang putih terang (jendela, langit, lingkaran mockup di tengah). Menaruh teks putih di atasnya tanpa diredupkan membuatnya tidak terbaca — dan itu tidak kelihatan dari kode.

Lapisannya karena itu disusun ulang di luar peramban dengan GD, lalu kontras teks putih diukur pada pita tempat judul benar-benar duduk:

| Bagian | Terburuk | Rerata |
| --- | --- | --- |
| Beranda — pita judul | **5,49:1** | 9,98:1 |
| Hero — pita judul | **5,66:1** | 9,47:1 |

Keduanya di atas ambang WCAG AA (4,5:1). Kombinasi pertama yang dicoba (0,68/0,80) sebenarnya lolos jauh — 7,0:1 dan 7,5:1 — tetapi gambarnya nyaris tidak terlihat, yang persis keluhan yang sedang diperbaiki. Angkanya dilonggarkan sampai gambarnya terbaca jelas dan kontrasnya masih aman, lalu dicatat di komentar partial-nya supaya penyunting berikutnya tahu batas itu ada dan dari mana asalnya.

### Tinggi section disamakan dengan hero banner

Putaran pertama masih menyisakan keluhan yang terlihat di `3.png`: gambarnya tampil, tetapi sectionnya hanya setinggi isinya (`py-16 lg:py-24`), sehingga `object-cover` memotong ilustrasinya jadi pita tipis — badge "DI RUMAH"/"DI KANTOR" terpotong separuh dan bagian tengahnya nyaris tak terbaca.

Tingginya kini menyalin hero banner beranda persis:

```
min-h-[560px] lg:min-h-screen lg:max-h-[1100px]
```

Isinya dibungkus `flex items-center` supaya terpusat vertikal di ruang yang baru. `pt-[104px]` milik hero **tidak** ikut disalin: jarak itu ada untuk menghindari header melayang, dan header hanya menimpa bagian paling atas beranda, bukan section ini.

Efek sampingnya pada scrim: karena isinya sekarang terpusat, judulnya tidak lagi menempel di atas — gelap tambahannya dipindah dari sisi atas ke pita tengah (`from-transparent via-black/45 to-transparent`). Diukur ulang pada geometri barunya (wadah 1920 × 1080, `object-cover` gambar 1536 × 1024, pita judul 22–42% tinggi):

| Scrim | Terburuk | Rerata |
| --- | --- | --- |
| di atas (lama) | 5,09:1 | 9,89:1 |
| di tengah (baru) | **5,40:1** | 10,36:1 |

Keduanya lolos AA; yang di tengah lebih baik justru karena itulah tempat teksnya sekarang berada.

### Verifikasi

- Ketiga halaman dirender lewat HTTP kernel: `/` **200**, `/informasi` **200**, `/informasi/setiap-saat` **200**; masing-masing memuat `ppid-di-mana-saja.webp`, tidak satu pun memuat `unsplash`.
- Beranda memuat `min-h-[560px] lg:min-h-screen lg:max-h-[1100px]` **dua kali** — hero banner dan section Kategori Informasi, persis seperti yang diminta.
- Kelas Tailwind baru (`bg-gradient-to-b`, `from-transparent`, `via-black/45`, `bg-[#08281B]`, `min-h-[560px]`, `max-h-[1100px]`) sempat **tidak ada** di CSS terbangun — Tailwind memangkas kelas yang belum dipakai, jadi tanpa `vite build` ulang scrim dan tinggi barunya tidak akan berlaku sama sekali. Sudah dibangun ulang dan semuanya terbukti ada.
- Seluruh tes fe-ppid tetap hijau: **33 lulus** (121 asersi).

---


## Status Pengerjaan (putaran 57 — langkah 78 & 79)

### Langkah 78 — Permohonan dan Keberatan berhenti bisa ditulis, mulai bisa dibaca

Tombol **Tambah Pengajuan** dilepas dari kedua modul, dan seperti pada langkah 73 penghapusannya dikerjakan **di dua lapis**: `CrudRoute::register(..., ['store', 'update', 'destroy'])` membuat endpoint `POST /v1/permohonan` dan `POST /v1/keberatan` benar-benar tidak ada, bukan sekadar tombolnya disembunyikan. `route:list` sekarang hanya menyisakan `GET` untuk keduanya, ditambah endpoint khusus (status, tanggapan, persetujuan, berkas tanggapan).

Di panel, `ResourceConfig` mendapat `tanpaTambah` yang berdiri sendiri dari `tanpaUbah`/`tanpaHapus`. Sebelumnya keduanya menyandera satu bendera `bolehTulis`, dan komentarnya masih menyebut alasan lama — "modul kiriman pemohon tetap boleh ditambah petugas untuk pencatatan permohonan luring". Alasan itu kini gugur, jadi ikut dibetulkan.

Karena tidak ada lagi jalur tambah maupun ubah, `fields` kedua modul dikosongkan dan `ResourceFormDialog`-nya tidak lagi dipasang. Sepuluh field permohonan dan empat field keberatan yang tinggal di registry hanya akan menyesatkan pembaca berikutnya: formulir yang tidak punya route.

**Yang menggantikannya: rincian pengajuan.** Tanpa formulir, isian yang ditulis pemohon — tujuan penggunaan, cara pengiriman, kasus posisi, berkas lampiran — kehilangan tempat untuk dibaca selain kolom tabel yang terpotong. `PermohonanDetailDialog` dan `KeberatanDetailDialog` mengisi lubang itu: identitas pemohon, isi pengajuan, penanganan, berkas pemohon dan berkas tanggapan, jenjang persetujuan, dan riwayat status dalam satu layar. Potongan yang sama dipakai keduanya (`RincianPengajuan.tsx`) supaya satu perbaikan tampilan tidak hanya sampai ke separuh panel.

Aksi **Lihat detail** sengaja tidak dijaga hak `edit`: membaca rincian pengajuan adalah bagian dari melihat modulnya. Yang menuntut hak tulis tetap dialog status dan dialog tanggapan.

Ikut dibetulkan: label status keberatan di panel hanya memuat tiga nilai (`diajukan`, `diproses`, `selesai`) padahal CHECK constraint tabelnya sudah menerima `revisi`, `menunggu_approval`, dan `ditolak` sejak langkah 65 — barisnya tampil sebagai nilai mentah. Sekarang keenamnya punya label.

### Langkah 79 — persetujuan berjenjang yang datanya, bukan kodenya

Yang ada sebelumnya bukan alur: `approval_permohonan` cuma satu baris bebas — siapa pun yang punya hak `approve` bisa memutus, tidak ada urutan, dan keberatan tidak punya jalur persetujuan sama sekali.

**Tiga tabel memisahkan definisi dari jalannya.**

| Tabel | Isi |
| --- | --- |
| `alur_approval` | satu definisi alur per jenis pengajuan |
| `alur_approval_tahap` | jenjangnya: urutan, `role_id` penyetuju, `struktur_id` kotak bagan yang diwakilinya, SLA, boleh-tolak |
| `approval_pengajuan` | langkah nyata milik satu pengajuan |

Tabel ketiga **menyalin** nama tahap, role, dan nama jabatannya saat langkah dibuat. Tanpa salinan itu, super admin yang menyusun ulang alur ikut menulis ulang riwayat yang sudah terjadi — persetujuan tahun lalu akan terbaca seolah diputus jabatan yang baru dibuat kemarin.

`approval_permohonan` yang lama tidak dihapus dan tidak ditulis lagi; ia tinggal sebagai arsip putusan sebelum alur berjenjang dipakai, dan tetap ikut pada rincian permohonan lama.

**Pengikat ke struktur organisasi.** Tiap tahap menunjuk satu kotak `struktur_organisasi`, jadi yang tampil pada jenjang adalah nama jabatan yang sama dengan yang dilihat publik pada bagan — bukan slug role teknis. Role `atasan-ppid` ditambahkan ke `ModulSistemSeeder` karena jenjang teratas bagan sebelumnya tidak punya pemegang; tanpa itu tahap "Atasan PPID" akan macet begitu berkas sampai di sana. Hak aksesnya sempit dengan sengaja: hanya Dashboard, Permohonan, Keberatan, dan Laporan — ia mengesahkan layanan, bukan menyunting konten situs.

Susunan awal (seluruhnya bisa diubah dari panel, `AlurApprovalSeeder`):

| Jenis | Tahap 1 | Tahap 2 |
| --- | --- | --- |
| Permohonan | Persetujuan PPID — `ppid-utama`, 3 hari | Pengesahan Atasan PPID — `atasan-ppid`, 3 hari |
| Keberatan | Telaah PPID — `ppid-utama`, 5 hari, **tanpa hak tolak** | Putusan Atasan PPID — `atasan-ppid`, 5 hari |

Keberatan berhenti di Atasan PPID sesuai UU No. 14 Tahun 2008, yang menempatkan putusan keberatan di tangannya.

**Aturan yang dijaga mesinnya** (`App\Support\AlurPersetujuan`):

- Seluruh jenjang dibuat sekaligus saat pengajuan masuk `menunggu_approval`, tetapi hanya langkah pertama yang berstatus `menunggu`. Membuat semuanya di muka membuat sisa perjalanan ikut terlihat, bukan muncul satu per satu; menjalankan satu per satu menjaga urutannya tetap berarti.
- **Satu langkah `menunggu` per pengajuan.** Dua langkah terbuka sekaligus membuat "berjenjang" kehilangan artinya.
- Satu penolakan menutup seluruh sisa jenjang — langkah di atasnya ditandai `dilewati`, bukan dibiarkan menggantung.
- Yang boleh memutus hanya pemegang role tahapnya. Super admin selalu boleh: ia yang menyusun alurnya, dan berkas yang macet karena rolenya kosong atau pemegangnya nonaktif harus bisa dibebaskan tanpa menyunting basis data.
- SLA tahap baru mulai berjalan saat **gilirannya tiba**, bukan saat berkas masuk antrean.

**Penutup celahnya.** Selama masih ada tahap yang menunggu, dialog status petugas menolak memasang `disetujui`/`ditolak` — putusan akhir milik penyetuju. Yang tersisa dari dialog itu adalah menarik berkas kembali ke `diproses`. Tanpa penjagaan ini seluruh jenjangnya bisa dilangkahi lewat satu dropdown.

**Keberatan akhirnya punya aturan transisi.** Statusnya dulu bisa dipasang bebas, sehingga berkas yang sudah ditutup masih bisa dibuka ulang tanpa jejak. `KeberatanInformasi::TRANSISI` menyusunnya sejajar dengan permohonan: `diajukan → diproses → menunggu_approval → selesai`, dengan `revisi` sebagai jalan pulang dan `selesai`/`ditolak` sebagai status akhir tanpa tujuan lanjutan.

**Penerjemah hasil ke status ada di controller, bukan di mesinnya**, karena kedua modul memakai kosakata berbeda: permohonan berakhir `disetujui`, keberatan langsung `selesai` — keberatan memang tidak mengenal status "disetujui", yang disetujui adalah tanggapan atasan atasnya.

Penyetuju tahap berikutnya mendapat notifikasi panel begitu gilirannya tiba (`approval_menunggu`), menaut ke `/ppid/{modul}?detail={id}` yang kini membuka rincian pengajuannya — tempat jenjangnya berada. Tanpa itu berkas menunggu tanpa ada yang tahu.

**Modul barunya**: `alur-approval` ("Alur Persetujuan") di grup Manajemen Sistem. Hak tulisnya hanya untuk super admin; PPID Pelaksana dan PPID Utama sebatas melihat — role yang berada **di dalam** alur tidak boleh bisa menyusun ulang jenjangnya sendiri. Jenjangnya diatur lewat dialog "Atur tahap": urutan ditentukan posisi baris (tombol naik/turun), bukan angka yang diketik, supaya susunan yang terlihat dan urutan yang dijalankan server tidak bisa berbeda. Seluruh jenjang disimpan dalam satu permintaan — alur yang tersimpan setengah jadi adalah alur yang macet.

### Verifikasi

Basis data produksi tidak punya basis data uji terpisah, jadi seluruh pemeriksaan API dijalankan **di dalam transaksi yang di-`rollBack`**, bukan lewat `RefreshDatabase`.

- Alur permohonan penuh: `diajukan → diverifikasi → diproses → menunggu_approval` membuat 2 langkah; tahap 1 disetujui → `lanjut` dan tahap 2 mendapat jam masuk + batas waktunya sendiri; tahap 2 disetujui → status permohonan `disetujui`.
- Revisi di tahap 1 → status kembali `diproses`, tahap 2 tercatat `dilewati`.
- Penolakan di tahap 1 → status `ditolak` dan catatannya masuk sebagai `alasan_penolakan` yang dibaca pemohon; `permohonan_log_status` bertambah setiap kali.
- Dialog status ditolak saat mencoba melompati persetujuan: *"Permohonan ini sedang menunggu putusan tahap persetujuan…"*.
- Menolak tanpa catatan ditolak; menolak pada tahap yang `boleh_tolak = false` ditolak (*"Tahap 'Telaah PPID' tidak diberi hak menolak…"*).
- Alur keberatan penuh: `diajukan → selesai` ditolak sebagai transisi terlarang; `diajukan → diproses → menunggu_approval`, dua tahap disetujui → status `selesai` dengan `tanggal_tanggapan` terisi.
- `simpanTahap` menyusun ulang jenjang (menambah tahap baru, menghapus yang hilang dari kiriman, membalik urutan) dan menolak tahap aktif tanpa role.
- `npx tsc --noEmit` bersih; `eslint` pada modul PPID **0 error**; `vite build` sukses.
- Seluruh tes portal fe-ppid tetap hijau: **33 lulus** (121 asersi) — 30 `Portal*` + 3 `HeaderPermohonan`.
- `php -l` bersih pada seluruh berkas PHP yang disentuh; migration dan kedua seeder berjalan.

---


## Status Pengerjaan (putaran 56 — langkah 76 & 77)

### Langkah 76 — lonceng hanya memuat yang belum dibaca

Kedua lonceng dulu memuat **semua** notifikasi. Yang sudah dibuka tetap tinggal di daftar, hanya berbeda tebal hurufnya — jadi pembacanya harus mengingat sendiri mana yang sudah ditangani, dan lencananya tidak pernah benar-benar kosong.

Penyaringannya dikerjakan **di server**, bukan disembunyikan di klien: daftar yang dikirim memang hanya berisi yang belum dibaca. Kalau penyaringannya di klien, muatannya tetap membesar tanpa batas dan penandaan yang gagal terkirim tetap terlihat berhasil sampai halaman dimuat ulang.

**api-ppid** — `GET /v1/notifikasi` kini hanya mengembalikan `is_read = false`. Riwayat lengkapnya diminta dengan `?semua=1`. Dua endpoint baru:

| Endpoint | Guna |
| --- | --- |
| `POST /v1/notifikasi/{id}/baca` | tandai satu |
| `POST /v1/notifikasi/baca-semua` | tandai semua milik pengguna itu |

Keduanya menyaring `user_id` pemilik token — bukan sekadar pembatas tampilan; tanpa itu id milik pengguna lain ikut bisa ditandai. Didaftarkan **sebelum** pola `/{id}` supaya `baca-semua` tidak tertangkap sebagai id.

**be-ppid** — `useGetAllNotifications(semua = false)`; kunci cache-nya dibedakan (`belum-dibaca` / `semua`) supaya lonceng dan halaman arsip tidak saling menimpa. `NotificationCard` mendapat prop `onOpen`, dipanggil saat kartunya diklik termasuk kartu tanpa tautan — yang dibuka berarti sudah dibaca. Lonceng memakai daftar belum-dibaca (jadi lencananya langsung benar tanpa disaring ulang), halaman Notifikasi memakai `semua = true` dan meredupkan yang sudah dibaca.

Tombol **dismiss all** di lonceng diganti **tandai semua dibaca**: menghapus notifikasi yang belum sempat dibaca adalah kehilangan, bukan kerapian. Menghapus tetap ada lewat tombol silang tiap kartu, dan **Hapus semua** di halaman arsip kini memakai konfirmasi karena riwayatnya tidak bisa dikembalikan.

Ikut dibersihkan di halaman arsip: tombol **Example notification** peninggalan templat Fuse dilepas — ia memanggil `create`, yang di aplikasi ini memang selalu ditolak (`Notifikasi tidak dapat dibuat dari panel admin.`), jadi satu-satunya hasilnya galat. Teks Inggris bawaannya sekalian diterjemahkan.

**fe-ppid** — `GET /akun/notifikasi/daftar` ikut menyaring `is_read = false`. Di lonceng, baris yang diklik **dibuang dari daftar**, bukan diberi tanda; `/akun/notifikasi` tetap memuat semuanya. Karena isi lonceng dijamin belum dibaca, dua gaya baris (dibaca/belum) dilepas — semuanya kini bergaya "baru".

### Langkah 77 — waktu sistem jadi WIB

Ada cacat yang tidak kelihatan sampai jamnya dibaca orang: **`app.timezone` masih `UTC` sementara TimeZone sesi PostgreSQL `Asia/Bangkok` (+07).** Laravel mengirim tanggal sebagai jam dinding tanpa offset (`Y-m-d H:i:s`), dan PostgreSQL-lah yang memberi offset memakai TimeZone sesi. Akibatnya setiap penulisan ke kolom `timestamptz` meleset tujuh jam — tanpa galat.

Terbukti lewat uji pulang-pergi sebelum perbaikan:

```
Laravel now()   = 2026-08-19T09:42:01+00:00   (= 16:42 WIB)
tersimpan di PG = 2026-08-19 09:42:02+07      (= 02:42 UTC)
dibaca Laravel  = 2026-08-19T09:42:02+07:00
```

Jam yang dimaksud 16:42 WIB, yang tersimpan 09:42 WIB. Persis keluhannya.

Perbaikannya di setelan, bukan di tiap pencetak tanggal:

| Berkas | Isi |
| --- | --- |
| `config/app.php` (kedua aplikasi) | `'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta')` |
| `config/database.php` (kedua aplikasi) | `connections.pgsql.timezone => env('DB_TIMEZONE', 'Asia/Jakarta')` |

Keduanya **harus sama**; itu inti masalahnya, jadi ditulis sebagai komentar di kedua berkas supaya tidak dipisah lagi kelak. Sesudahnya jam pulang-perginya utuh: `16:51:35 WIB` masuk, `16:51:35+07` tersimpan, `16:51:35+07` terbaca.

Efek sampingnya menguntungkan: `whereYear`/`whereDate` kini dihitung dalam WIB. Sebelumnya permohonan yang masuk 1 Januari pukul 06.00 WIB tercatat 31 Desember di UTC, dan penyaring tahun pada dashboard salah menghitungnya.

Pergeseran `->timezone(config('ppid.zona_waktu'))` di kelas email dan model jadi tidak mengubah apa pun, tetapi **tetap dipasang**: label "WIB" yang dicetak di sebelahnya baru dijamin benar kalau zonanya disebut, bukan diwarisi setelan yang bisa berubah. Komentar di kedua `config/ppid.php` yang masih menyebut "waktu tersimpan dalam UTC" dibetulkan.

**Panel be-ppid** mencetak tanggal dengan `toLocaleString('id-ID', …)` tanpa `timeZone`, jadi hasilnya mengikuti setelan mesin petugas. Tiga salinan pencetak yang hampir sama itu disatukan ke `ppid/lib/waktu.ts` dengan zona **dipatok** `Asia/Jakarta` — petugas yang membuka panel dari mesin berzona lain tidak boleh melihat jam berbeda dari rekannya di kantor.

**Koreksi data lama.** Seluruh baris yang ditulis sebelum perbaikan meleset tujuh jam ke arah yang sama, baik pada kolom `timestamptz` (PostgreSQL menstempel jam UTC sebagai +07) maupun `timestamp` polos (berisi jam UTC, kini dibaca sebagai jam Jakarta). Karena arah dan besarnya sama, koreksinya satu: migration `2026_08_19_000002_geser_waktu_lama_ke_jakarta` menambahkan tujuh jam.

Yang menjaganya tetap aman:

- **Batas waktu.** Hanya nilai yang lebih tua dari `2026-08-19 16:45:00` yang digeser; baris yang ditulis setelah setelan dibetulkan sudah benar dan menggesernya justru merusak. Pembandingnya dipasang pada kolomnya sendiri, bukan pada `created_at` tabelnya — baris lama bisa punya kolom yang baru diisi hari ini.
- **Daftar kolom dibaca dari katalog**, bukan ditulis tangan: kolom waktu tersebar di 103 kolom pada puluhan tabel, dan satu yang terlewat berarti satu tempat yang jamnya tetap meleset. Tabel bawaan kerangka kerja (`migrations`, `failed_jobs`, tabel token) dikecualikan.
- **`down()` menggeser balik**, jadi keputusannya bisa dibatalkan.

Dijalankan setelah dikonfirmasi, dan diuji dulu di dalam transaksi yang di-`rollBack`. Hasilnya: login terakhir petugas yang tersimpan `09:24+07` jadi `16:24+07` — cocok dengan jam nyata saat login itu terjadi.

### Verifikasi

- `PortalNotifikasiTest` jadi **12 tes** (29 asersi), semuanya lulus. Tiga di antaranya baru: lonceng hanya memuat yang belum dibaca (3 baris, 1 sudah dibaca → daftar berisi 2); baris yang ditandai dibaca hilang dari lonceng; halaman penuh tetap memuat yang sudah dibaca.
- Endpoint panel diuji langsung ke basis data: 3 belum dibaca → tandai satu → lonceng 2, arsip tetap 3 → tandai semua → lonceng 0, arsip tetap 3.
- `time` pada response notifikasi kini membawa offset `+07:00`.
- Seluruh tes portal fe-ppid tetap hijau: **33 lulus** (121 asersi).
- `php -l` bersih; `npx tsc --noEmit` bersih; `eslint` pada modul PPID dan modul notifikasi **0 error**; `vite build` dan `npm run build` sukses.

---


## Status Pengerjaan (putaran 55 — langkah 75)

Dashboard panel disusun ulang: dari halaman yang hampir seluruhnya bicara permohonan, jadi halaman yang bicara **pemohon dulu, baru pengajuannya** — dan setiap angka pengajuan selalu berpasangan Permohonan Informasi + Keberatan Informasi.

### Angka baru di `v1/dashboard/analitik`

Semua tambahan dihitung di `AnalitikController`, satu endpoint yang sama, supaya tidak ada dua bagian halaman yang menampilkan hitungan berbeda:

| Kunci baru | Isi |
| --- | --- |
| `pemohon.total` | jumlah pemohon terdaftar |
| `pemohon.per_jenis` | sebaran menurut `jenis_pemohon` |
| `pemohon.verifikasi` | `menunggu`, `terverifikasi`, `belum`, `ditolak` |
| `analisa.per_status` | jadi objek berisi `permohonan` **dan** `keberatan` |
| `analisa.per_jenis_pemohon` | `permohonan` dan `keberatan`, di-join ke tabel pemohon |

Penyaring tahun ikut berlaku untuk pemohon, lewat `created_at` — itu yang berarti "mendaftar pada tahun ini".

Tiga keadaan verifikasi yang diminta dipetakan ke nilai `status_verifikasi` yang sudah ada: **belum diverifikasi** = `menunggu` (berkas sudah dikirim, menunggu diperiksa), **sudah diverifikasi** = `terverifikasi`, **belum melakukan verifikasi** = `belum` (belum mengirim berkas sama sekali). Keadaan keempat, `ditolak`, ikut dikembalikan meski tidak diminta: tanpa itu jumlah keempat angka tidak akan sama dengan total pemohon, dan angka yang tidak menutup adalah angka yang tidak bisa dipercaya. Di panel ia tampil sebagai keterangan kecil pada kartu "Menunggu diverifikasi".

Sebaran per jenis pemohon di-`join`, bukan lewat relasi Eloquent: yang dibutuhkan cuma satu kolom pengelompokan, menarik seluruh baris pemohon untuk itu pemborosan. Jenis yang kosong dikelompokkan sebagai `tidak_diisi` supaya barisnya tidak lenyap diam-diam dari total.

### Susunan halaman

1. **Pemohon** — Total pemohon · Sudah diverifikasi · Menunggu diverifikasi · Belum verifikasi data.
2. **Pengajuan** — Permohonan Informasi · Keberatan Informasi · Menunggu persetujuan · Lewat batas waktu.
3. **Sebaran** — Pemohon per jenis · Status Permohonan Informasi · Status Keberatan Informasi.
4. **Per jenis pemohon** — Permohonan Informasi dan Keberatan Informasi berdampingan.
5. Konten · 6. Perlu tindakan · 7. SLA · 8. KPI · 9. Tren.

Kartu lama disesuaikan seperti diminta: "Total permohonan" jadi **Permohonan Informasi**, dan "Keberatan belum selesai" naik jadi kartu **Keberatan Informasi** yang menampilkan totalnya, dengan sisa yang belum selesai sebagai keterangan — dua angka dalam satu kartu, bukan satu angka tanpa pembandingnya. "Menunggu persetujuan" dan "Lewat batas waktu" tetap.

Sebaran ditampilkan sebagai daftar berbatang (label, batang, angka), bukan deretan chip: chip menyembunyikan besaran, dan yang ingin dilihat di sini justru perbandingan antarbaris. Batangnya diskala terhadap nilai terbesar dalam daftar itu sendiri.

### Yang dihapus

- Kartu **Kepuasan pemohon**.
- Panel **Sebaran status** (deretan chip) — digantikan dua daftar status yang terpisah permohonan/keberatan.
- Panel **Kategori paling diminta**, beserta method `kategoriTeratas()` di API yang jadi tidak punya pembaca.

Yang **tidak** dihapus: indikator "Kepuasan pemohon" di bagian **Capaian KPI**. Bagian itu tidak disebut pada langkah ini, dan isinya beda peran — capaian terhadap target, bukan angka sesaat. Bila memang ingin kepuasan hilang sepenuhnya dari dashboard, indikator itu tinggal dilepas dari `TARGET_KPI` dan daftar `kpi()`.

### Grafik masuk vs ditanggapi jadi diagram batang

Bentuk lamanya deretan `LinearProgress` mendatar — satu baris per tahun per bulan, 12 × 4 baris. Sekarang **satu diagram batang** (ApexCharts, pustaka yang memang sudah dipakai template panel): sumbu X tetap **Januari–Desember**, satu seri per tahun, batang tahun berdampingan di tiap bulan. Sumbu bulan yang tetap itu yang membuat grafiknya bisa dipakai membandingkan tahun; pada bentuk "12 bulan terakhir", Maret satu tahun tidak pernah berdiri sejajar dengan Maret tahun lain.

`TAHUN_PEMBANDING` dinaikkan dari 3 ke **4** — tahun terpilih + paling banyak **tiga tahun sebelumnya**, sama persis dengan grafik Portal Pemohon (langkah 71) supaya satu peristiwa tidak terbaca beda rentang di dua tempat. Hanya tahun yang benar-benar punya data yang ikut.

Kedua besaran tetap ada, tetapi **bergantian** lewat pilihan **Data: Masuk / Ditanggapi** di kanan judul. Menggambar keduanya sekaligus untuk empat tahun berarti delapan batang per bulan, dan tidak ada yang bisa dibaca dari sana. Total setahun per tahun tetap tampil sebagai chip di atas grafik dalam bentuk `masuk / ditanggapi`, jadi perbandingan keduanya tidak hilang.

Warna seri pertama selalu jatuh ke tahun terpilih, sumbu Y dipaksa bilangan bulat (jumlah permohonan tidak mengenal 0,5), dan `noData` diisi supaya grafik kosong tetap menjelaskan dirinya.

### Label jenis pemohon

"Pribadi (data lama)" jadi **"Pribadi"**, dan imbuhan yang sama dilepas dari "Instansi" — dua nilai itu berdampingan di daftar yang sama, dan menandai salah satunya saja sebagai data lama justru membingungkan. Keduanya tetap dipetakan (bukan dibiarkan tampil sebagai kode mentah) karena barisnya masih ada di basis data.

### Verifikasi

- Payload `analitik` diuji langsung: dengan satu permohonan + satu keberatan contoh (dibuat di dalam transaksi lalu di-`rollBack`), `per_status` dan `per_jenis_pemohon` mengembalikan `{"permohonan":{"diajukan":1},"keberatan":{"diajukan":1}}` dan `{"permohonan":{"pribadi":1},"keberatan":{"pribadi":1}}`.
- `pemohon.verifikasi` menutup ke total pemohon.
- Rentang tren diuji dengan satu permohonan di tiap tahun 2023–2026 (juga di dalam transaksi lalu di-`rollBack`): `tahun_dibanding` mengembalikan keempat tahunnya dan baris Maret terisi 1/1 untuk masing-masing — sumbu bulannya benar-benar sejajar antar tahun.
- `php -l` bersih; `npx tsc --noEmit` bersih; `eslint` pada `PpidDashboard.tsx` **0 error**; `vite build` sukses.

---


## Status Pengerjaan (putaran 54 — langkah 74)

Tombol CTA **Permohonan** di header hilang begitu pemohon masuk. Tautannya (`/permohonan`) hanya `Route::redirect` ke `/akun/permohonan/baru`, dan halaman itu sudah jadi modul tersendiri di Portal Pemohon — bagi yang sudah masuk tombolnya cuma pintu kedua ke tempat yang sama.

Disembunyikan di **dua** tempat, bukan satu: CTA hijau di header desktop dan tombol "Permohonan Informasi" di menu mobile. Keduanya tombol yang sama dengan pembungkus berbeda; menyembunyikan salah satunya saja membuat tombolnya muncul kembali begitu layar mengecil.

Yang **tidak** ikut hilang:

- Tautan **Permohonan Informasi Publik** di menu Layanan. Itu navigasi antarhalaman, bukan tombol ajakan — sama seperti Pengajuan Keberatan dan Laporan Pelayanan yang berdampingan dengannya.
- Tautan permohonan di badan halaman (Beranda, Standar Layanan, halaman informasi). Konteksnya berbeda: di sana tautannya bagian dari alur baca, bukan tombol tetap yang mengikuti di setiap halaman.

Pengunjung yang belum masuk tetap melihat tombolnya seperti semula — bagi mereka tombol itu justru pintu masuknya, dan pengalihannya berhenti di halaman login.

### Verifikasi

Ditambahkan `tests/Feature/HeaderPermohonanTest.php` — 3 tes, semuanya lulus (8 asersi). Pemeriksaannya memakai kelas khas tombol CTA (`hidden sm:inline-flex … fs-gradient-accent` dan `block w-full text-center py-3 fs-gradient-accent`), bukan URL-nya: URL yang sama juga dipakai menu Layanan dan isi Beranda, jadi `assertDontSee` atas URL akan lulus/gagal karena alasan yang salah.

- Tamu tetap melihat kedua tombol.
- Pemohon yang sudah masuk tidak melihat keduanya.
- Menu Layanan-nya tetap memuat tautan permohonan meski sudah masuk.

Seluruh tes portal tetap hijau: 31 lulus (115 asersi) untuk `--filter="Portal|HeaderPermohonan"`. `npm run build` sukses.

---


## Status Pengerjaan (putaran 53 — langkah 73)

Modul **Pemohon**, **Permohonan**, dan **Keberatan** tidak lagi punya jalur ubah maupun hapus. Ketiganya berisi data yang ditulis pemohon sendiri lewat portal; petugas menanggapinya, bukan menyuntingnya.

### Dihapus di dua lapis, bukan hanya tombolnya

Menyembunyikan menu di panel saja tidak cukup — endpoint-nya tetap hidup dan masih bisa dipanggil langsung dengan token petugas. Karena itu jalurnya dilepas di api-ppid lebih dulu:

- `CrudRoute::register()` mendapat parameter `$kecuali`. `permohonan` dan `keberatan` didaftarkan dengan `['update', 'destroy']`, sehingga `PUT`, `PATCH`, `DELETE /{id}`, dan `POST /hapus-massal` **tidak ada** di daftar route. `php artisan route:list` untuk keduanya kini hanya menyisakan `index`, `show`, `store`, dan endpoint alur kerja.
- Modul **Pemohon** sudah sejak awal begitu: hanya `index`, `show`, `berkas-ktp`, dan `verifikasi`. Yang berubah di sini sisi panelnya (lihat bawah).

Tambah (`store`) sengaja **tetap ada**: permohonan yang masuk lewat meja layanan tetap perlu dicatat petugas, dan pencatatan itu sudah menuliskan barisnya sendiri di `permohonan_log_status`.

### Keberatan mendapat endpoint alur kerja sendiri

Keberatan tidak punya endpoint status seperti permohonan — status dan tanggapan atasan selama ini diisi lewat `update` CRUD biasa. Melepas `update` begitu saja akan membuat keberatan tidak bisa diproses sama sekali.

Penggantinya `POST /v1/keberatan/{id}/tanggapan` (`akses:keberatan,edit`), yang **hanya** memvalidasi `status` dan `tanggapan_atasan_ppid`. Jenis keberatan, alasan, kasus posisi, dan penguasaan tidak pernah tersentuh meski ikut dikirim. Endpoint ini juga mengerjakan yang dulu tersebar di `beforeSave`/`afterSave`: mengisi `ditangani_oleh` begitu keberatan mulai ditangani, menstempel `tanggal_tanggapan` saat selesai, mencatat `audit_log`, lalu mengirim email dan notifikasi lonceng setelah commit.

### Panel: `tanpaUbah` / `tanpaHapus`

`ResourceConfig` mendapat dua flag baru. Keduanya **hanya bisa mempersempit** — hak role tetap diperiksa lebih dulu, jadi flag ini tidak pernah memberi hak baru:

```
const bolehUbah = bolehTulis && akses.edit && !config.tanpaUbah;
const bolehHapus = bolehTulis && akses.delete && !config.tanpaHapus;
```

Keduanya dipakai di **empat** tempat yang sebelumnya masing-masing memeriksa `akses.edit`/`akses.delete` sendiri: menu Ubah, menu Hapus, kotak centang pilih-baris, dan tombol hapus massal di toolbar.

Ini sekaligus menutup celah lama: `renderRowActionMenuItems` dulu hanya melihat `akses.edit`/`akses.delete` tanpa `bolehTulis`, sehingga **modul `readOnly` pun tetap memunculkan menu Ubah dan Hapus** — termasuk Pemohon, dan termasuk saat daftar sedang menampilkan data terhapus. Menunya ada, endpoint-nya tidak; yang didapat petugas cuma pesan galat.

Aksi baris yang tersisa jadi satu-satunya jalur tulis:

| Modul | Aksi baris | Hak |
| --- | --- | --- |
| Pemohon | Detail & verifikasi | `view` untuk melihat, `approve` untuk memutuskan |
| Permohonan | Ubah status | `edit` |
| Keberatan | Tanggapan & status | `edit` |

Rantai ternary bertingkat di `PpidResourcePage` yang merakit aksi baris diganti fungsi dengan `return` awal per modul — dengan bertambahnya modul keempat, bentuk lamanya sudah tidak terbaca.

Formulir keberatan ikut dirapikan: `status` dan `tanggapan_atasan_ppid` dilepas dari `fields` karena formulirnya kini hanya dipakai saat menambah, dan kedua kolom itu sudah punya rumahnya sendiri di dialog tanggapan.

### Perbaikan bawaan: catatan internal sempat bocor ke pemohon

Notifikasi lonceng dari langkah 72 mengirimkan `catatan` pada perpindahan status permohonan ke pemohon — padahal kolom itu dilabeli "Catatan internal" di panel, dengan keterangan "tidak ditampilkan ke pemohon". Yang dikirim sekarang `alasan_penolakan`, yang memang ditujukan ke pemohon, dan parameternya diganti nama jadi `$keterangan` supaya tidak tertukar lagi. Teks notifikasinya ikut berubah dari "Catatan petugas:" menjadi "Keterangan petugas:".

### Verifikasi

- `php artisan route:list`: `permohonan` 7 route, `keberatan` 4 route — tanpa `PUT`/`PATCH`/`DELETE`/`hapus-massal` pada keduanya.
- Endpoint tanggapan diuji langsung ke basis data: keberatan berstatus `diajukan` → `selesai` menghasilkan `tanggapan_atasan_ppid` tersimpan, `ditangani_oleh` terisi, `tanggal_tanggapan` terstempel, dan notifikasi portal "Keberatan atas permohonan … telah selesai ditangani. Tanggapan dapat dilihat di portal. Keterangan petugas: …". Baris ujinya dihapus lagi setelahnya.
- `npx tsc --noEmit` bersih; `eslint` pada modul PPID **0 error**; `vite build` sukses; `php -l` bersih.

---


## Status Pengerjaan (putaran 52 — langkah 72)

Portal Pemohon mendapat **lonceng notifikasi** berisi umpan balik petugas. Sebelumnya satu-satunya pemberitahuan ke pemohon adalah email, dan email hanya dikirim pada dua tahap besar (pengajuan *diterima* dan *selesai*) karena kuota SMTP terbatas — perpindahan status lain, catatan petugas, berkas tanggapan, dan hasil verifikasi data diri hanya terlihat kalau pemohon kebetulan membuka halamannya.

### Tabel `notifikasi_pemohon`

Loncengnya **tidak** dibuat dengan menurunkan peristiwa dari data yang sudah ada (`permohonan_log_status`, `tanggal_tanggapan`, `tanggal_verifikasi`). Cara itu gugur di keberatan: tabelnya tidak punya `updated_at` maupun tabel log, jadi perpindahan ke `diproses` tidak punya cap waktu sama sekali dan tidak bisa diurutkan bersama peristiwa lain.

Yang dipakai kembaran tabel lonceng panel admin — migration `2026_08_19_000001_create_notifikasi_pemohon_table` di **api-ppid** (pemilik skema):

| Kolom | Keterangan |
| --- | --- |
| `pemohon_id` | FK ke `pemohon`, cascade on delete |
| `type` | `permohonan_status`, `keberatan_status`, `permohonan_tanggapan_file`, `verifikasi_pemohon` |
| `message` | kalimat yang dibaca pemohon, termasuk catatan petugas |
| `is_read` | penanda sudah dibaca |
| `data` | `title`, `icon`, `link`, `variant`, id pengajuan — bentuknya sama dengan `notifikasi.data` |

Dipisah dari tabel `notifikasi` milik panel, bukan ditumpuk dengan kolom "jenis penerima", karena kunci asingnya berbeda: satu ke `users`, satu ke `pemohon`. Menyatukannya berarti melepas kedua constraint itu.

### Penulisnya: api-ppid, di tiga titik yang sudah ada

`App\Support\NotifikasiPortal` adalah cerminan `NotifikasiAdmin` di fe-ppid, arah sebaliknya. Pemanggilannya diletakkan persis di sebelah pengiriman email yang sudah ada supaya tidak ada jalur perubahan status baru yang perlu diingat:

- `PermohonanController::ubahStatus()` — di dalam `DB::afterCommit` yang sama dengan `EmailPemohon`, jadi transaksi yang batal tidak menyisakan notifikasi atas status yang tidak jadi. `catatan` petugas ikut dibawa.
- `PermohonanController::tambahTanggapanFile()` — notifikasi tersendiri; berkasnya kerap menyusul beberapa saat setelah status berpindah, dan tanpa ini pemohon tidak punya penanda kapan dokumennya bisa diunduh.
- `KeberatanController::afterSave()` — `tanggapan_atasan_ppid` ikut dibawa; keberatan tidak punya tabel log status seperti permohonan.
- `PemohonController::verifikasi()` — hasil Verifikasi Data Diri beserta catatan dan sisa kesempatan kirim ulang. Yang ditolak diantar ke halaman perbaikannya, kecuali kalau kesempatannya sudah habis — tautan ke formulir yang tidak lagi menerima kiriman cuma bikin pemohon berputar.

Cakupannya sengaja lebih luas daripada email: setiap perpindahan status diberitahukan, bukan hanya dua tahap. Status pembuka (`diajukan`) tidak masuk — itu pengajuan pemohon sendiri, bukan umpan balik. Status yang tidak berubah juga tidak menghasilkan baris; petugas kerap menyimpan ulang satu baris hanya untuk membetulkan kolom lain. Gagal menulis notifikasi dicatat di log lalu diabaikan — tidak boleh membatalkan keputusan yang sudah tersimpan.

### Loncengnya di fe-ppid

`akun/partials/lonceng.blade.php` disisipkan di header, **bukan hanya di Portal** — pemohon jadi tahu pengajuannya sudah ditanggapi tanpa harus membuka `/akun` dulu. Isinya ditarik `fetch` ke `GET /akun/notifikasi/daftar` setiap 60 detik dan setiap tab kembali aktif, bukan disisipkan saat render: portal kerap ditinggalkan terbuka sementara petugas memproses pengajuannya.

- Lencana jingga di atas ikon memuat jumlah belum dibaca (`9+` bila lebih).
- Mengklik satu baris menandainya dibaca lalu membuka tautannya. Penandaannya dikirim dengan `keepalive` dan tidak ditunggu, jadi perpindahan halamannya tidak tertahan jaringan.
- "Tandai semua dibaca" mengosongkan lencana di layar lebih dulu, permintaannya menyusul.
- Daftar di lonceng dibatasi 20 baris terbaru; selebihnya di `/akun/notifikasi`.

Halaman penuh `/akun/notifikasi` bukan sekadar arsip: barisnya dibuka lewat `GET /akun/notifikasi/{id}/buka` yang menandai dibaca **di server** lalu mengalihkan ke tujuannya, jadi loncengnya tetap berfungsi pada peramban yang skripnya gagal dimuat. Menu portal ikut memuat entri Notifikasi beserta lencana jumlah belum dibaca.

**Tautan notifikasi disaring**: hanya path internal yang diawali satu garis miring yang dipakai (`untukLonceng()`); selain itu tautannya dibuang. Tanpa itu satu baris `data.link` cukup untuk melempar pemohon ke domain lain. Setiap endpoint juga menyaring `pemohon_id` pemilik — bukan sekadar penyaring tampilan, tanpa itu id milik akun lain ikut bisa ditandai.

### Verifikasi

Ditambahkan `tests/Feature/PortalNotifikasiTest.php` — 10 tes, semuanya lulus (23 asersi), memakai `DatabaseTransactions`:

- Jumlah belum dibaca dan isi daftarnya benar; tautannya dirender sebagai URL penuh.
- Notifikasi akun lain tidak ikut terbaca (`belum_dibaca` 0, daftar kosong).
- Menandai satu baris dan menandai semua bekerja; id milik akun lain **tidak** ikut tertandai.
- Membuka dari halaman penuh menandai dibaca lalu mengalihkan ke tujuannya.
- `link` berisi URL domain lain diabaikan — pengalihannya kembali ke halaman notifikasi.
- Lonceng benar-benar terpasang di header saat pemohon masuk; tamu mendapat 401.

Sisi penulisnya diuji langsung ke basis data: perpindahan `diproses` → `ditolak` menghasilkan pesan bernomor registrasi lengkap beserta `link`, `variant`, dan `permohonan_id` yang benar; `php -l` bersih untuk seluruh berkas baru maupun yang disunting; `lang/en.json` bertambah 8 kunci dan tetap sah; `npm run build` sukses.

**Catatan basis data:** menjalankan `php artisan test` tanpa filter di fe-ppid **mengosongkan `ppiddb`**. Tes bawaan Breeze yang masih tertinggal (`tests/Feature/Auth/*`, `ProfileTest`) memakai `RefreshDatabase`, sementara migration fe-ppid hanya memuat tabel bawaan Laravel — seluruh tabel PPID ikut terhapus dan tidak dibuat ulang. Basis data pengembangan sudah dipulihkan lewat `php artisan migrate` + seluruh seeder di api-ppid, dan akun `admin@foodstation.co.id` (super-admin) dibuat ulang tanpa kata sandi yang bisa dipakai — setel dulu dengan `php artisan ppid:set-password admin@foodstation.co.id`. Data yang tidak berasal dari seeder tidak dapat dipulihkan. Jalankan tes portal dengan `--filter` sampai tes bawaan itu dilepas.

---


## Status Pengerjaan (putaran 51 — langkah 71)

### Tab status Portal Pemohon tinggal tiga

`/akun/permohonan` dan `/akun/keberatan` sebelumnya punya enam tab (Semua, Dalam Proses, Revisi, Menunggu Persetujuan, Tolak, Selesai). Sekarang tinggal **Semua, Dalam Proses, Selesai**.

Pemetaannya **tidak** dibuat dengan membuang tab begitu saja — kalau tab "Tolak" hilang tanpa penggantinya, permohonan berstatus `ditolak` tidak akan muncul di tab mana pun kecuali "Semua", dan pemohon kehilangan barisnya di tempat yang tidak ia duga. Karena itu ditambahkan `KELOMPOK_PORTAL` beserta `statusKelompokPortal()`:

| Tab | Status permohonan | Status keberatan |
| --- | --- | --- |
| Dalam Proses | `diajukan`, `diverifikasi`, `diproses`, `revisi`, `menunggu_approval` | `diajukan`, `diproses`, `revisi`, `menunggu_approval` |
| Selesai | `disetujui`, `selesai`, `ditolak`, `ditolak_sebagian`, `kedaluwarsa` | `ditolak`, `selesai` |

Segala yang berakhir dihitung tuntas, termasuk yang ditolak. Daftarnya diturunkan dari `STATUS_LABEL`, bukan ditulis ulang, sehingga status baru cukup diberi label dan tab-nya ikut menyesuaikan sendiri. `KeberatanInformasi` meminjam pengelompokan dari `PermohonanInformasi` supaya kedua daftar memakai tab yang persis sama.

**`KELOMPOK` yang lama tidak diubah** — grafik dan legend di Dashboard portal masih memerlukan lima kelompok itu. Label status rinci juga tetap tampil di tiap baris; yang hilang hanya tab-nya.

### Pencarian di Histori

`/akun/histori` mendapat satu kotak pencarian yang menyaring **kedua** daftar sekaligus:

- **Permohonan** dicari lewat `kode_permohonan`.
- **Keberatan** dicari lewat nomor permohonan induknya — keberatan tidak punya nomor registrasi sendiri, di seluruh portal ia memang dirujuk begitu. Nomor urut barisnya ikut dicocokkan bila yang diketik berupa angka, supaya nomor pada tautan notifikasi tetap ketemu.

Satu nomor permohonan karena itu memunculkan permohonannya beserta keberatan yang menunjuk ke sana. Di bawah kotaknya ada ringkasan jumlah hasil, dan pesan kosong tiap daftar berubah dari "Belum ada …" menjadi "Tidak ada … yang cocok dengan pencarian" saat kata kuncinya terisi — dua keadaan yang berbeda dan tidak boleh terbaca sama.

### Profil jadi halaman baca

`/akun/pengaturan/profil` dulu berupa formulir yang bisa mengubah nama dan nomor telepon. Sekarang seluruh data pemohon ditampilkan lengkap, dipisah **empat tab**, dan **satu-satunya yang bisa diubah sendiri adalah foto avatar**:

| Tab | Isi |
| --- | --- |
| Akun | nama, email (+ keterangan terverifikasi), nomor telepon, terdaftar sejak |
| Data Diri | jenis pemohon, NIK, pekerjaan, nama lembaga, alamat |
| Verifikasi & Berkas | status, tanggal diperiksa, catatan petugas, sisa kesempatan kirim ulang, tautan lihat berkas KTP |
| Aktivitas | jumlah permohonan & keberatan yang diajukan, tautan ke Histori |

Alasan penguncian ditulis di halamannya: nama, email, dan nomor telepon melekat pada permohonan yang sudah diverifikasi petugas — mengubahnya diam-diam berarti mengubah identitas pada berkas yang sudah terlanjur diproses.

Penguncian **bukan sekadar menghapus input**: `perbaruiProfil()` kini hanya memvalidasi dan menyimpan `foto`, dengan `forceFill` pada satu kolom itu saja. Mengirim `nama` atau `no_hp` lewat request buatan tidak berpengaruh — ada tesnya.

Baris keterangannya dipisah ke `akun/partials/baris-info.blade.php` karena dipakai berulang di empat tab dan di halaman Data Pemohon. Nilai yang kosong tetap dicetak sebagai "—", bukan barisnya dihilangkan: pemohon perlu tahu isian itu memang belum diisi, bukan hilang dari halaman.

### Data Pemohon terkunci setelah terverifikasi

Bila `status_verifikasi` sudah `terverifikasi`, `/akun/pengaturan/data-pemohon` **tidak lagi merender formulir sama sekali** — diganti tampilan baca berisi seluruh isian, dan berkas KTP hanya bisa dibuka lewat tautan "Lihat berkas KTP". Tidak ada input, tidak ada tombol kirim, jadi tidak ada kesan masih bisa disunting.

Sisi server ikut dijaga: `simpanDataPemohon()` menolak dengan galat validasi begitu datanya sudah terverifikasi, sebelum apa pun disimpan. Ini melengkapi penjagaan yang sudah ada untuk pemohon yang ditolak tiga kali.

Pemohon yang **belum** terverifikasi — termasuk yang berkasnya ditolak dan masih punya kesempatan — tetap melihat formulir seperti sebelumnya.

Teks statusnya ikut diperbaiki: kalimat "Mengubah data di bawah akan membuat status kembali menunggu pemeriksaan" sudah tidak benar lagi, diganti arahan menghubungi petugas PPID.

### Avatar akhirnya seragam di semua tempat

Foto yang diunggah di Profil tidak pernah muncul di header situs: blok akun pada `layouts/header.blade.php` **selalu** menggambar inisial nama, tidak pernah menyentuh kolom `foto`. Akibatnya foto baru seolah gagal tersimpan padahal sudah masuk.

Penggambaran avatar dipindah ke `akun/partials/avatar.blade.php` dan dipakai empat tempat: header desktop, menu mobile (yang sebelumnya bahkan tidak punya avatar sama sekali), sapaan Portal Pemohon, dan halaman Profil. Ukuran, cincin, dan warna latar inisial diatur lewat variabel, jadi tampilannya tetap berbeda-beda sesuai tempatnya sementara sumber datanya satu. Bila `foto` kosong, tampilannya jatuh ke inisial nama seperti semula.

### Dashboard: dua angka status, grafik jadi perbandingan tahun

Kartu **Statistik Permohonan Informasi** dan **Statistik Permohonan Keberatan** dulu memecah angkanya jadi lima kelompok. Sekarang memakai `KELOMPOK_PORTAL` yang sama dengan tab daftar — **Dalam Proses** dan **Selesai** saja. Tidak ada angka yang hilang: Revisi dan Menunggu Persetujuan melebur ke Dalam Proses, Tolak melebur ke Selesai, jadi jumlah keduanya tetap sama dengan total.

**Grafik Data Pengajuan** dulu memuat 12 bulan terakhir sebagai batang bertumpuk per status — bentuk itu tidak bisa dipakai membandingkan tahun karena sumbunya bergerak mengikuti bulan berjalan. Sekarang sumbu X-nya tetap **Januari–Desember**, dan tiap tahun jadi satu batang berdampingan di tiap bulan, sehingga Maret satu tahun berdiri sejajar dengan Maret tahun lain.

- Tahun yang digambar: tahun berjalan + **paling banyak tiga tahun sebelumnya** (4 seri), dan hanya tahun yang benar-benar punya pengajuan. Data lebih tua dari itu tidak ikut.
- **Tahun berjalan selalu ikut walau masih kosong** — grafik yang kehilangan seluruh sumbunya lebih membingungkan daripada grafik yang datar.
- Legend memuat tahun beserta total setahun penuhnya; batang bernilai nol tetap digambar setipis garis supaya urutan tahun terbaca sama di setiap bulan.
- Masih HTML/CSS murni, tanpa pustaka grafik.

### Verifikasi

Ditambahkan `tests/Feature/PortalDashboardTest.php` — 4 tes, semuanya lulus:

- Blok ringkasan hanya memuat "Dalam Proses" dan "Selesai"; "Revisi", "Menunggu Persetujuan", dan "Tolak" tidak ada di sana meski datanya berstatus `menunggu_approval` dan `ditolak`.
- Tiga pengajuan di tiga tahun berbeda → legend memuat ketiga tahunnya, label bulan tetap 12, dan batang Maret tahun ini maupun tahun lalu masing-masing bernilai 1.
- Pengajuan berumur lima tahun tidak ikut digambar; batas tiga tahun ke belakang ikut.
- Akun tanpa pengajuan tetap memunculkan tahun berjalan beserta pesan kosongnya.

Ditambahkan `tests/Feature/PortalPengaturanTest.php` — 8 tes, semuanya lulus:

- Avatar: pemohon berfoto → URL fotonya muncul di header Beranda **dan** di Portal; pemohon tanpa foto → tidak ada `uploads/avatar/` di halaman, inisialnya yang tampil.

- Profil memuat keempat tab beserta data dari semua tab (NIK, pekerjaan, alamat, telepon) dalam satu render.
- Profil hanya punya `name="foto"`; `name="nama"` dan `name="no_hp"` tidak ada.
- `PUT` profil yang menyertakan `nama` & `no_hp` tetap tidak mengubah keduanya, sementara fotonya tersimpan ke `uploads/avatar/`.
- Data Pemohon terverifikasi: tanpa `name="nik"`, tanpa `name="file_ktp"`, tanpa tombol "Kirim untuk Verifikasi", ada tautan "Lihat berkas KTP".
- Data Pemohon berstatus `ditolak` tetap memunculkan formulirnya.
- `PUT` data pemohon pada akun terverifikasi ditolak (`assertSessionHasErrors`) dan NIK di basis data tidak berubah.

Ditambahkan `tests/Feature/PortalDaftarTest.php` — 6 tes, **semuanya lulus** (22 asersi). Memakai `DatabaseTransactions`, jadi seluruh baris ujinya di-rollback; basis data setelahnya tetap berisi data nyata saja (1 pemohon, 1 permohonan milik akun sungguhan, nol sisa `@example.test`).

- Blok `role="tablist"` hanya memuat Semua / Dalam Proses / Selesai; "Revisi", "Menunggu Persetujuan", dan "Tolak" tidak ada di sana — tetapi tetap tampil sebagai label status baris, yang juga diperiksa.
- Penyaringan tab benar: 4 permohonan (`diajukan`, `menunggu_approval`, `selesai`, `ditolak`) → Semua 4, Dalam Proses 2, Selesai 2. Keberatan: 3 baris → Semua 3, Dalam Proses 1, Selesai 2.
- Histori: mencari satu nomor memunculkan permohonan itu dan menyembunyikan yang lain; nomor asing memunculkan kedua pesan "tidak cocok"; tanpa kata kunci semuanya tampil.
- Total **18 tes portal lulus** (84 asersi). Keduanya memakai `DatabaseTransactions`, jadi seluruh baris ujinya di-rollback — basis data setelahnya nol sisa `@example.test`.
- `lang/en.json`: 23 kunci baru; 7 kunci yang kehilangan pemakainya setelah Profil jadi hanya-baca (`Username`, `Simpan Perubahan`, `Profil berhasil diperbarui.`, dan seterusnya) dilepas. JSON tetap sah.
- `php -l` bersih; `npm run build` sukses.


## Status Pengerjaan (putaran 50 — langkah 70)

Halaman **Standar Layanan → Jalur & Waktu Layanan** (`/standar-layanan/jalur-waktu-layanan`) dirombak jadi dua kartu yang bisa ditindaklanjuti, bukan tiga kartu keterangan.

### Jalur Pelayanan

- **Kartu Surat dihapus** dari `PpidController` — permohonan lewat surat tidak lagi dilayani. Gridnya menyesuaikan dari 3 kolom jadi 2.
- **Kartu Online kini tautan** ke `route('akun.login')`, jadi pengunjung yang memilih jalur daring langsung mendarat di halaman masuk Portal Pemohon.
- **Kartu Langsung kini tombol** yang membuka panel Waktu Layanan, lalu menggulirkan halaman ke panel itu.

Kedua kartu memakai penanda baru `aksi` (`masuk` / `waktu`) di data channel, bukan dicocokkan lewat label — label bisa berubah kata, penanda tidak. Isi kartunya dipindah ke `partials/jalur_layanan_isi.blade.php` karena pembungkusnya kini berbeda (`<a>` vs `<button>`) sedangkan isinya harus tetap sama persis. Keduanya juga mendapat baris petunjuk di kaki kartu ("Masuk ke Portal Pemohon" / "Lihat waktu layanan & lokasi") supaya jelas kartunya bisa diklik.

### Jam layanan disederhanakan

Dua baris jadwal (Senin–Kamis `08.00 – 15.00` istirahat `12.00 – 13.00`, dan Jum'at `08.00 – 15.00` istirahat `11.30 – 13.30`) diganti satu baris: **Senin - Jum'at, 08:00 - 17:00 WIB**. Jam istirahat tidak lagi diumumkan, jadi kunci `break` dilepas dari datanya dan blok istirahat di view dibuat kondisional (`@if (!empty($schedule['break']))`) — bukan dihapus, supaya jadwal yang memang punya istirahat masih bisa ditulis kembali tanpa menyentuh view. Gridnya juga menyesuaikan: dua kolom hanya bila barisnya lebih dari satu.

Angka ini sudah sejalan dengan kartu Kontak di Beranda yang memang memakai `Senin–Jumat, 08.00–17.00 WIB`.

### Waktu Layanan jadi panel lipat

Judulnya berubah jadi tombol ber-`aria-expanded`/`aria-controls`, panelnya `x-collapse`, dan **tertutup saat halaman dibuka** — jam operasional hanya berarti bagi yang memang berencana datang. Saat tertutup, subjudul "Jam operasional & lokasi kantor PPID" menerangkan isinya; panah ikut berputar mengikuti keadaan.

Ditambahkan juga `[x-cloak] { display: none !important; }` di `resources/css/app.css`. Aturan itu belum pernah ada padahal `x-cloak` sudah dipakai beberapa layar akun; tanpa itu panel yang seharusnya tertutup sempat berkedip tampil sebelum Alpine selesai memuat.

### Peta lokasi

Peta dipisah ke `partials/peta_lokasi.blade.php` dan dipakai dua tempat: di dalam panel Waktu Layanan (tinggi 320px) dan di kartu Kontak Beranda (260px).

Beranda **sudah** punya iframe peta sebelumnya, tetapi titiknya berbeda dari yang diminta dan URL-nya berupa token `maps/embed?pb=…` hasil salinan peramban — panjang, tidak terbaca, dan tidak bisa disesuaikan kalau titiknya bergeser. Sekarang keduanya memakai koordinat yang sama (`-6.213053, 106.881272`, zoom 17) lewat bentuk `?q=<lat>,<lng>&output=embed` yang bisa dibaca dan disunting. Di bawah peta ada tautan **Buka di Google Maps** ke alamat yang diberikan, karena peta tersemat tidak bisa dipakai menyusun rute.

### Verifikasi

- `/standar-layanan/jalur-waktu-layanan` **200**: dua kartu (`Online`, `Langsung`), nol kemunculan `Surat`; kartu Online menaut ke `/akun/masuk`; penanda `waktuTerbuka` terpasang; peta memakai koordinat baru.
- Tiga halaman Standar Layanan lain (`maklumat-pelayanan`, `prosedur-permohonan`, `prosedur-keberatan`) tetap **200**, nol `Undefined variable`/`Whoops`.
- Beranda memuat peta dari partial yang sama beserta tautan Google Maps-nya.
- Jam layanan tampil satu kartu: `Senin - Jum'at` / `08:00 - 17:00 WIB`, tanpa baris istirahat; versi Inggrisnya `Monday - Friday`.
- `lang/en.json`: tujuh kunci baru ditambahkan; tiga kunci sisa jalur Surat dan sembilan kunci sisa jadwal lama (Senin s.d. Kamis, Jum'at, seluruh varian `08.00 – 15.00`) dilepas — semuanya sudah nol pemakai. JSON tetap sah. Versi Inggris halaman itu menampilkan `Sign in to the Applicant Portal`, `View service hours & location`, `PPID Office Location`, `Open in Google Maps` — nol teks Indonesia yang tertinggal.
- `npm run build` sukses.


## Status Pengerjaan (putaran 49 — langkah 69)

Dua label di menu Layanan dipendekkan: **Prosedur Permohonan Informasi Publik → Prosedur Permohonan Informasi**, dan **Prosedur Permohonan Keberatan Informasi Publik → Prosedur Permohonan Keberatan**.

Yang diubah hanya teks yang dibaca pengunjung, di empat berkas fe-ppid: judul halaman pada `PpidController@showServiceStandardPage` (2 baris), tautan menu di `layouts/header.blade.php` (desktop + mobile) dan `layouts/footer.blade.php`, serta kunci terjemahannya di `lang/en.json` — kuncinya ikut diganti karena `__()` memakai teks Indonesia sebagai kunci, dan terjemahannya dipendekkan sejalan: `Information Request Procedure` dan `Objection Request Procedure`.

**Slug rutenya sengaja tidak disentuh.** `/standar-layanan/prosedur-permohonan` dan `/standar-layanan/prosedur-keberatan` tetap seperti semula supaya tautan yang sudah tersebar tidak mati; yang berubah cuma label.

Dicek juga bahwa label ini tidak tersimpan di basis data: `halaman_statis.judul`, `pengaturan_situs.value`, dan `menu_navigasi.label` nol baris yang memuat frasa itu, jadi tidak ada isian CMS yang perlu ikut disunting.

### Verifikasi

- Nol sisa frasa lama di `app`, `resources`, `routes`, dan `lang`.
- Kedua halaman **200**; `<title>`-nya kini `Prosedur Permohonan Informasi | Standar Pelayanan PPID` dan `Prosedur Permohonan Keberatan | Standar Pelayanan PPID`.
- Menu di Beranda menampilkan kedua label baru; versi Inggris (`?lang=en`) menampilkan `Information Request Procedure` dan `Objection Request Procedure`, tanpa sisa "Public …".


## Status Pengerjaan (putaran 48 — langkah 68)

Langkah 58 melepas modul Laporan Statistik dari panel, tetapi sengaja menyisakan halaman publiknya, endpoint rekap, dan tipe `statistik_informasi` — waktu itu situs masih memakainya. Putaran ini menuntaskannya di ketiga aplikasi.

### fe-ppid — halaman publiknya hilang

- `resources/views/ppid/report.blade.php` dihapus.
- Rute `/laporan/{slug}` diganti `/laporan/pelayanan-informasi`. Nama rutenya tetap `ppid.report` supaya tautan yang sudah ada tidak perlu diubah namanya, tetapi kini **tanpa parameter** — alamat lama `/laporan/statistik-informasi` menghasilkan 404, begitu pula slug karangan lain.
- `PpidController@showReportPage` tinggal meneruskan ke Laporan Pelayanan; percabangan slug dan seluruh perakitan angka rekap (`masuk`, `dikabulkan`, `ditolak`, `keberatan`, `rata_rata_hari`) dilepas.
- `statistikRingkas()` ikut dihapus. Empat angka Pemohon/Dokumen/Regulasi/Kepuasan dulu tampil di Beranda, lalu dipindah ke halaman Laporan Statistik pada langkah 11; sekarang tidak punya tempat tampil lagi, jadi query-nya tidak disisakan menganggur.
- Tautan di menu Layanan (header desktop, header mobile, footer) dihapus; tiga tautan Laporan Pelayanan yang tersisa memakai `route('ppid.report')` tanpa argumen.
- **Pencarian situs** dulu menaut hasil laporan ke `/laporan/statistik-informasi` bila tipenya statistik. Sekarang `SearchController` hanya mencari `tipe('pelayanan_informasi')` dan menaut ke halaman detail laporannya — kalau tidak, hasil pencarian bisa menuju halaman yang sudah tidak ada.
- `lang/en.json`: kunci `Laporan Statistik Informasi Publik`, `Dikabulkan`, dan `Rata-rata Hari` dilepas karena tidak dipakai berkas mana pun lagi. `Ditolak Sebagian` dan `Kepuasan` **tetap** — keduanya masih dipakai layar lain.

### api-ppid — endpoint rekap dan tipenya dilepas

- Route `GET laporan-layanan/rekap` beserta `LaporanLayananController@rekap` dihapus. Satu-satunya pemakainya adalah tombol "Hitung otomatis" di form Laporan Statistik.
- `tipe_laporan` sekarang hanya menerima `pelayanan_informasi`; mengirim `statistik_informasi` ditolak 422.
- Aturan validasi enam kolom angka rekap dilepas, dan kolomnya dikeluarkan dari `$fillable`/`$casts` model `LaporanLayanan`.
- **Kolomnya sendiri tidak di-drop.** `jumlah_permohonan_masuk`, `jumlah_dikabulkan`, `jumlah_ditolak`, `jumlah_ditolak_sebagian`, `jumlah_keberatan`, dan `rata_rata_hari_respon` masih ada di tabel `laporan_layanan` dalam keadaan tidak terpakai. Menghapus kolom itu perubahan skema yang tidak bisa dibatalkan tanpa data, jadi dibiarkan sebagai migrasi tersendiri bila memang mau dibereskan.

### be-ppid — kode mati ikut dibuang

Modulnya sudah tidak ada sejak langkah 58, tapi mesinnya masih tertinggal:

- `aksiIsiOtomatis` di `lib/types.ts` beserta implementasinya di `ResourceFormDialog.tsx` (fungsi `isiOtomatis`, state `menghitung`, tombolnya, dan panel bantuannya) dihapus — tidak ada satu pun resource yang memakainya setelah Laporan Statistik lepas. Impor `useState` dan `ppidApi` yang menganggur ikut dibersihkan.
- Tujuh kunci `@i18n/kamusPpid.ts` yang hanya melayani tombol itu dilepas; deskripsi modul Survei yang menyebut halaman Laporan Statistik ditulis ulang.

### Verifikasi

- `/laporan/pelayanan-informasi` **200**; `/laporan/statistik-informasi` dan `/laporan/apa-saja` **404**. Beranda, Informasi, Regulasi, Berita, FAQ, dan `/akun/masuk` tetap 200.
- `/search?q=…` dan `/search-suggest?q=…` **200**, dan HTML hasilnya nol tautan `statistik-informasi`.
- `laporan-layanan/rekap` **404**; CRUD `laporan-layanan` tetap **200**. Simpan `tipe_laporan: statistik_informasi` → **422** (`"Pilihan Tipe laporan tidak sah."`), `pelayanan_informasi` → **201**. Baris ujinya dihapus, `laporan_layanan` kembali 0 baris.
- Tujuh endpoint panel lain (navigasi, survei, permohonan, keberatan, pemohon, dashboard) tetap 200.
- `npx tsc --noEmit` bersih; `npx vite build` sukses; `php -l` bersih di seluruh berkas yang disentuh.


## Pemulihan Basis Data (18 Agustus 2026)

Panel be-ppid tidak bisa masuk sama sekali. `POST /api/v1/auth/sign-in` menjawab 500 dengan `column users.deleted_at does not exist`. Penyebabnya bukan kode: basis data `ppiddb` tinggal 5 tabel bawaan Laravel, `users` **0 baris**, dan tabel `pemohon`, `berita`, `roles`, dan seterusnya sudah tidak ada. Tabel `migrations` hanya menyimpan 4 baris batch 1. Tidak ada backup di mesin ini, dan hanya ada satu instance PostgreSQL (PG18, port 5432) — jadi bukan salah alamat sambungan.

### Lubang yang membuat ini bisa terjadi

Seluruh tabel inti PPID dulu dibuat lewat DDL manual di luar Git. Migrasi yang ada di repo semuanya hanya menambal (`add_*`, `make_*`, `clear_*`) — tidak satu pun membuat tabel intinya, dan riwayat Git juga tidak pernah memuatnya. Artinya proyek ini **tidak pernah bisa dipasang ulang dari nol**, dan sekali basis datanya hilang, tidak ada jalan kembali.

Karena itu ditambahkan `2026_07_20_000000_create_skema_dasar_ppid.php`: baseline berisi 33 tabel, direkonstruksi dari model Eloquent, aturan validasi controller, seeder, dan pemakaian kolom di kedua aplikasi. Tanggalnya sengaja mendahului seluruh migrasi tambalan, dan isinya **keadaan sebelum ditambal** — kolom seperti `pemohon.status_verifikasi`, `regulasi.ringkasan`, atau seluruh kolom `*_en` tidak ada di sana, karena migrasi tambalannya yang memasang, persis seperti urutan aslinya. Setiap blok dijaga `hasTable`/`hasColumn` supaya aman dijalankan di basis data yang isinya sudah sebagian.

Dua hal yang semula hidup sebagai DDL manual ikut dipindahkan ke migrasi:

- **`trg_infopublik_search`** — mengisi `informasi_publik.search_vector`, bobot A untuk judul, B ringkasan, C konten, plus indeks GIN.
- **`trg_permohonan_kode`** — mengisi `kode_permohonan` berformat `PPID-FSTJ/<tanggal>/<urutan harian>` bila pemanggilnya tidak menyertakan nomor. Panel admin memang menyimpan permohonan tanpa nomor, sedangkan Portal Pengguna menghitung nomornya sendiri; trigger ini menghormati nomor yang sudah diisi dan hanya mengisi yang kosong, sehingga keduanya berbagi satu deret nomor per hari. `pg_advisory_xact_lock` menahan dua permohonan bersamaan agar tidak memperebutkan urutan yang sama.

### Yang dijalankan

1. `php artisan migrate` — 15 migrasi, semua DONE. Basis data kini 42 tabel.
2. `php artisan db:seed` (`ModulSistemSeeder`) — 18 modul, 3 role, 54 baris hak akses.
3. Akun admin `admin@foodstation.co.id` (role `super-admin`) dibuat ulang.
4. Seeder konten resmi dijalankan: `KontenAwal`, `PenamaanModul`, `HalamanProfil`, `RegulasiDasarHukum`, `DaftarInformasiPublik`, `DaftarInformasiDikecualikan`, `InformasiBerkala`, `MaklumatAwal`, `BaganStrukturPpid`, `TerjemahanInggris`. Hasilnya 24 informasi publik, 22 informasi dikecualikan, 8 regulasi, 7 simpul struktur, 5 FAQ, 1 maklumat, 1 halaman profil. **`PemohonDemoSeeder` sengaja tidak dijalankan** — isinya akun contoh, bukan konten resmi.

### Verifikasi

- Login panel lewat jalur asli (proxy Vite `/api/v1` → `127.0.0.1:8001`): **200**, JWT terbit, role `super-admin`.
- 28 endpoint modul panel diuji dengan token: **semuanya 200**, termasuk `me/navigation` dan kedua endpoint dashboard.
- 8 halaman situs publik: **semuanya 200**; `/informasi` merender 36 tautan dokumen dan `/regulasi` 8 berkas PDF, tanpa `Undefined variable` maupun `SQLSTATE`.
- Uji tulis (baris ujinya dihapus permanen setelahnya): nomor registrasi terbit urut `…/0001`, `…/0002`; nomor yang dipasok aplikasi (`…/7777`) tidak ditimpa; `search_vector` terisi dan `to_tsquery('simple','realisasi')` menemukan barisnya.

**Data lama tidak dapat dikembalikan** — yang pulih adalah struktur dan konten dasar yang memang ada seedernya. Permohonan, keberatan, akun pemohon, dan berita yang pernah diinput sudah hilang bersama basis data lamanya.

**Catatan:** `2014_10_12_000000_create_users_table` tercatat sudah jalan di tabel `migrations`, tetapi **berkasnya tidak ada di repo**. Di mesin baru, `users` akan dibuat oleh baseline ini. Kolom khas panel (`role_id`, `phone`, `is_active`, `last_login_at`) ditambahkan lewat blok ber-`hasColumn`, jadi basis data lama maupun baru berakhir sama.


## Status Pengerjaan (putaran 47 — langkah 67)

### Satu kop untuk semua email

Sebelumnya tidak ada satu pun templat email pemohon: verifikasi pendaftaran dan reset password memakai notifikasi bawaan Laravel — markdown polos, sebagian berbahasa Inggris, berkop nama aplikasi. Sekarang semuanya lewat komponen yang sama di `resources/views/components/email/`:

- `layout` — kop logo Food Station, kartu putih di atas latar krem, kaki berisi nama instansi, alamat kontak layanan, dan peringatan "jangan balas". Tata letaknya `<table>` dengan gaya inline, bukan flex/grid: Gmail dan Outlook membuang `<style>` di `<head>`.
- `tombol`, `tautan-cadangan`, `rincian` — tombol aksi, URL mentah untuk klien email yang memblokir tombol, dan tabel ringkasan pengajuan.

Isi suratnya:

| Berkas | Peristiwa |
| --- | --- |
| `emails/akun/verifikasi.blade.php` | Pendaftar baru — naskahnya mengikuti `Konfirmasi Email.docx` |
| `emails/akun/reset-password.blade.php` | Permintaan lupa password |
| `emails/layanan/status.blade.php` | Status pengajuan & hasil verifikasi data pemohon |

`App\Notifications\VerifikasiEmailPemohon` dan `ResetPasswordPemohon` menggantikan notifikasi bawaan lewat override `sendEmailVerificationNotification()` / `sendPasswordResetNotification()` di model `Pemohon`. Tautan verifikasinya tetap URL bertanda tangan dengan hash email seperti bawaan Laravel, jadi pembatasan 30 menit sekali dan masa berlaku 24 jam dari putaran sebelumnya tidak berubah.

### Email layanan hanya di tiga titik

Sesuai permintaan, pemohon hanya diberi email saat pengajuan **Dikirim**, **Diterima**, dan **Selesai** — pergeseran status internal (diproses, menunggu approval) tidak mengirim apa pun. Kuota kirim SMTP terbatas, dan semua status tetap terlihat di portal.

- **Dikirim** — dari situs, begitu formulir Permohonan/Keberatan tersimpan (`App\Support\EmailPemohon`).
- **Diterima** — dari panel: permohonan saat berpindah ke `diverifikasi`, keberatan saat berpindah ke `diproses`. Keberatan tidak punya status `diverifikasi`, jadi `diproses` yang dipakai sebagai penanda "sudah diperiksa petugas".
- **Selesai** — dari panel, saat status menjadi `selesai`.

Pengirimannya menunggu `DB::afterCommit`: transaksi yang batal tidak boleh menyisakan pemberitahuan atas status yang tidak jadi tersimpan. Perbandingan dengan status lama menjaga email tidak terkirim dua kali kalau petugas menyimpan ulang baris yang statusnya tidak berubah.

**Tambahan di luar tiga peristiwa itu:** hasil pemeriksaan Verifikasi Data Diri Pemohon (terverifikasi / ditolak) kini juga dikirim lewat email — sebelumnya pemohon baru tahu kalau kebetulan membuka portal, padahal selama belum terverifikasi ia tidak bisa mengajukan apa pun dan kesempatan kirim ulangnya terbatas. Suratnya memuat catatan petugas dan sisa kesempatan; kalau kesempatannya sudah habis, tombol "Perbaiki Data Pemohon" ikut ditiadakan supaya tidak bertabrakan dengan isinya.

### api-ppid ikut mengirim email

Status berpindah di panel admin, jadi api-ppid yang harus mengirim dua email terakhir. Karena itu:

- `MAIL_*` di `api-ppid/.env` diarahkan ke SMTP yang sama dengan situs (`srv179.niagahoster.com`, pengirim `noreply-ppid@foodstation.co.id`). Sebelumnya masih `mailpit`/`hello@example.com` bawaan Laravel.
- Berkas templat emailnya **disalin apa adanya** ke `api-ppid/resources/views/`, dan `config/ppid.php` dibuat di kedua aplikasi dengan kunci yang sama (`kontak`, `situs_url`, `bahasa_email`, `zona_waktu`). Konsekuensinya: **kalau salah satu templat diubah, salinannya harus ikut diubah.** Alternatifnya adalah tabel antrean email di `ppiddb` yang dikirim satu pekerja terjadwal — lebih rapi, tapi menambah komponen yang harus selalu hidup, jadi tidak diambil sekarang.

### Dua keputusan yang perlu diketahui

- **Bahasa email dikunci Bahasa Indonesia** (`config('ppid.bahasa_email')`), tidak mengikuti pemilih bahasa situs. Panel admin tidak tahu bahasa yang dipilih pengunjung, jadi tanpa kunci ini satu pengajuan bisa menghasilkan rangkaian email yang berpindah-pindah bahasa.
- **Jam pada email digeser ke `Asia/Jakarta`** sebelum diberi label "WIB". Aplikasi menyimpan waktu dalam UTC (`config('app.timezone')`). Catatan terpisah: `App\Support\Cms::tanggalWaktu()` yang dipakai halaman portal **belum** melakukan penggeseran ini, jadi jam di layar masih meleset tujuh jam dari jam di email. Perbaikannya menyentuh tampilan tanggal di seluruh situs, jadi dicatat di sini sebagai pekerjaan tersendiri.

### Verifikasi

Semua email dirender lewat transport `array` (tidak ada yang benar-benar terkirim, tidak ada data uji yang tertinggal di basis data — semua modelnya dibuat di memori):

- fe-ppid, dengan `App::setLocale('en')` untuk meniru pengunjung versi Inggris: 4 email keluar, semuanya tetap Bahasa Indonesia, pengirim `"PPID Food Station" <noreply-ppid@foodstation.co.id>`, tautan mengarah ke `http://localhost:8000/akun/...` dan logonya ke `/assets/images/logo/logo_fs.png`.
- api-ppid, 5 perpindahan status diuji → **3 email**; `diverifikasi → diproses` dan penyimpanan ulang `selesai → selesai` benar-benar tidak mengirim apa pun.
- Hasil verifikasi data pemohon: 3 keadaan (disetujui, ditolak dengan sisa 2 kesempatan, ditolak dengan kesempatan habis) — yang terakhir terbukti tidak lagi memuat tombol perbaikan.
- `php -l` bersih di seluruh berkas yang disentuh.
- `php artisan test` di fe-ppid tetap merah seperti sebelum putaran ini: 23 kegagalannya semua milik sisa scaffolding Breeze (`App\Models\User` yang memang tidak ada, rute `/login` & `/register` yang sudah pindah ke `/akun/...`). Tidak ada satu pun yang menyentuh berkas putaran ini.


## Status Pengerjaan (putaran 46 — langkah 66)

### Detail Pemohon

Dialog **Detail & Verifikasi Pemohon** menggantikan dialog verifikasi cepat dari putaran 45. Digabung dalam satu layar dengan sengaja: memutuskan tanpa melihat KTP dan isian identitasnya berdampingan berarti menebak.

Isinya: identitas lengkap (email, no. HP, **NIK**, jenis pemohon, pekerjaan, lembaga, alamat), status email, jumlah permohonan, **pratinjau berkas KTP** langsung di dialog, jejak verifikasi (diperiksa oleh, tanggal, terdaftar sejak), catatan penolakan sebelumnya, lalu formulir keputusannya.

- **NIK kini ditampilkan — tetapi hanya di endpoint detail.** Sebelumnya NIK disembunyikan dari seluruh panel; tanpa NIK petugas tidak bisa mencocokkan isian dengan KTP yang diunggah, padahal itu inti pemeriksaannya. Pembukaannya sempit dan disengaja: hanya lewat `GET pemohon/{id}`, hanya untuk yang berhak melihat modul, dan tetap tidak muncul di daftar maupun ekspor.
- **Membuka detail cukup hak `Lihat`;** formulir keputusannya yang menuntut hak `Setujui`. Menu barisnya menyesuaikan: "Detail & verifikasi" atau "Lihat detail".

### Berkas KTP tidak lagi lewat URL publik

Berkas yang diunggah pemohon tersimpan di `storage/app/public/uploads/ktp/…` dan disajikan situs lewat route `/storage/{path}` yang **tidak menuntut siapa pun masuk**. Untuk gambar berita hal itu wajar; untuk **dokumen identitas** terlalu longgar — nama berkasnya memang UUID sehingga sulit ditebak, tetapi sekali URL-nya bocor, siapa pun bisa mengunduhnya tanpa akun.

Karena itu panel tidak memakai URL publik tersebut. Ditambahkan `GET pemohon/{id}/berkas-ktp` di api-ppid yang membaca berkas dari disk `media` dan menyajikannya di belakang token panel serta hak akses modul, dengan `Cache-Control: private, no-store`. Panel mengambilnya sebagai blob (peramban tidak mengirim header Authorization untuk `<img src>`/`<iframe src>`), lalu memakainya lewat object URL yang dilepas kembali saat dialog ditutup.

**Catatan:** route publik `/storage/{path}` di situs sendiri belum diubah — masih melayani `uploads/ktp/…` bagi siapa pun yang tahu URL-nya. Mempersempitnya menyentuh penyajian media di luar cakupan langkah ini, jadi dicatat di sini sebagai hal yang sebaiknya dibereskan tersendiri.

### Notifikasi yang membuka datanya

Kartu notifikasi di panel sebenarnya sudah bisa diklik — `NotificationCard` merender `NavLinkAdapter` bila `link` terisi, dan API memang mengirimkannya. Yang menjadi keluhan adalah tujuannya: tautannya hanya `/ppid/pemohon`, jadi berhenti di daftar modul dan petugas masih harus mencari barisnya sendiri.

Sekarang semua notifikasi menaut ke barisnya: `/ppid/pemohon?detail={id}`, `/ppid/permohonan?detail={id}`, `/ppid/keberatan?detail={id}`. `PpidResourcePage` membaca parameter `detail`, membuka dialog detail modul yang bersangkutan, lalu **membersihkan parameternya dari URL** — kalau dibiarkan, menutup dialog dan memuat ulang halaman akan membukanya lagi.

Untuk saat ini yang punya dialog detail baru modul **Pemohon**; notifikasi Permohonan dan Keberatan tetap membawa `?detail=` tetapi mendarat di daftarnya, menunggu modul itu punya layar detail sendiri.

### Verifikasi

Diuji lewat HTTP sungguhan; token admin dibuat dari akun `Administrator` yang sudah ada — **tidak ada akun uji baru di panel**. Data uji sudah dihapus.

- `GET pemohon/12` mengembalikan `nik`, `jumlah_permohonan`, `punya_berkas_ktp: true`, dan relasi `verifikator`.
- `GET pemohon/12/berkas-ktp` dengan token → **200**, `Content-Type: image/png`. **Tanpa token → 401.**
- `GET notifikasi` mengembalikan `"link":"/ppid/pemohon?detail=12"`.
- `npx tsc --noEmit` + `npx vite build` (be) bersih; `php -l` bersih.

### Rapi-rapi

Ditemukan satu berkas KTP yatim di `storage/app/public/uploads/ktp` — sisa pengujian putaran 45 yang gagal terhapus karena dihapus dari disk `local`, padahal berkasnya ada di disk `public`. Sudah dibersihkan; pemeriksaan ulang menunjukkan 0 berkas yatim.

### Yang belum dikerjakan

- Captcha gambar masih belum dapat diakses pembaca layar (tercatat sejak putaran 43).
- Lonceng notifikasi **di portal pemohon** (kalimat tanpa nomor di bawah blok langkah 66) belum dikerjakan.
- Halaman detail untuk modul Permohonan dan Keberatan.
- Route media publik `/storage/{path}` masih melayani berkas KTP tanpa autentikasi.

---


## Status Pengerjaan (putaran 45 — langkah 65)

### Notifikasi ke be-ppid

Lonceng notifikasi panel sudah ada (membaca `GET /v1/notifikasi`), yang belum ada adalah pemicunya dari sisi pemohon. Sekarang ada dua, dan sengaja dipisah karena bobotnya berbeda:

| Kejadian | Tipe | Isi |
|---|---|---|
| Akun pengunjung baru mendaftar | `pemohon_baru` | "Akun pemohon baru: :nama (:email)." |
| Berkas data diri dikirim / dikirim ulang | `verifikasi_pemohon` | "Data diri :nama menunggu verifikasi (pengiriman ke-:ke dari :batas)." |

Yang menuntut tindakan adalah yang kedua — pendaftaran saja belum ada yang bisa diperiksa, karena berkas identitasnya baru dikirim pada langkah berikutnya. Teks notifikasi kedua menyebut pengiriman ke berapa, supaya petugas tahu bobot keputusannya (menolak untuk ketiga kalinya menutup pintu bagi pemohon) sebelum membuka berkasnya. Keduanya menaut ke `/ppid/pemohon`, dan penerimanya hanya akun aktif yang rolenya punya hak lihat modul tersebut.

### Fitur verifikasi di modul Layanan → Pemohon

Modul Pemohon tetap **baca-saja** — datanya milik pengunjung dan disunting dari akunnya sendiri. Yang ditambahkan hanya satu tindakan: **Verifikasi data** pada menu aksi baris.

- Dialog barunya (`PemohonVerifikasiDialog.tsx`) menampilkan status sekarang, catatan penolakan sebelumnya, dan **sisa kesempatan kirim ulang**, lalu meminta keputusan **Terverifikasi** atau **Ditolak**.
- **Alasan wajib diisi saat menolak.** Kesempatan pemohon terbatas; menolak tanpa memberi tahu apa yang salah membuat kesempatan itu terbuang percuma. Alasannya tampil di portal pemohon sebagai "Catatan petugas".
- Aksi ini memakai hak **`Setujui`**, bukan `Ubah` — yang dilakukan petugas memang menyetujui/menolak berkas, bukan menyunting data. Konsekuensinya modul Pemohon perlu hak `Setujui` di matrix hak akses role.
- `ResourceListPage` disesuaikan: kolom aksi kini juga muncul pada modul baca-saja yang punya aksi khusus. Sebelumnya kolom itu hanya muncul bila modulnya bisa ditulis, sehingga menu aksi pada Pemohon tidak akan pernah tampil.
- Daftar Pemohon mendapat kolom **Verifikasi** (badge), **Jumlah Ditolak**, **Diperiksa oleh**, plus filter status verifikasi. Urutan bawaan diubah ke terbaru dulu.
- Perbaikan sekalian: filter **Jenis** masih memakai pilihan lama (`pribadi` / `instansi` / `kelompok`) yang tidak ada di formulir situs publik, jadi memfilter apa pun selalu menghasilkan daftar kosong. Sekarang mengikuti `App\Models\Pemohon::JENIS`: perorangan, mahasiswa, lembaga, kelompok.

### Batas tiga kali penolakan

- Kolom baru pada `pemohon`: `jumlah_ditolak`, `catatan_verifikasi`, `diverifikasi_oleh`.
- Keadaan "diblokir" **diturunkan dari `jumlah_ditolak >= 3`**, bukan dari status baru. Dengan begitu `status_verifikasi` tetap memakai empat nilai yang sudah dikenal seluruh sistem, CHECK constraint yang ada tidak perlu diubah, dan data lama tetap sah.
- Setelah tiga penolakan:
  - tombol **Kirim untuk Verifikasi** di portal dimatikan **dan** pengirimannya ditolak di server (`PengaturanController`), bukan sekadar disembunyikan;
  - **pendaftaran ulang dengan email yang sama ditolak** (`RegisterController`) — kalau tidak, blokirnya bisa dilewati hanya dengan mendaftar lagi;
  - API menolak penolakan keempat;
  - lapisan penghalang di portal berubah pesannya dan tidak lagi menawarkan tombol Data Pemohon, karena tidak ada lagi yang bisa dikirim.
- Setiap pengiriman ulang **menghapus catatan penolakan lama**, supaya pemohon tidak membaca alasan yang sudah tidak berlaku untuk berkas barunya.
- Berkas yang sudah **terverifikasi tidak bisa ditolak** lewat endpoint ini: membalik keputusan berarti mencabut layanan yang mungkin sudah berjalan, jadi itu harus disengaja, bukan salah klik.
- Keputusan verifikasi dicatat ke `audit_log` sebagai `verifikasi_pemohon` lengkap dengan nilai sebelum dan sesudah.

### Berkas yang ditambahkan / disentuh

- `api-ppid`: migrasi `2026_08_15_000002_add_verifikasi_pemohon_columns.php`, `PemohonController::verifikasi()`, rute `POST pemohon/{id}/verifikasi`, model `Pemohon` (relasi `verifikator`, atribut `verifikasi_diblokir` & `sisa_kesempatan`).
- `fe-ppid`: `NotifikasiAdmin::pendaftaranBaru()` & `verifikasiPemohonMenunggu()`, penjagaan di `RegisterController` dan `PengaturanController`, model `Pemohon` (`verifikasiDiblokir()`, `sisaKesempatanVerifikasi()`), tampilan Data Pemohon dan lapisan penghalang.
- `be-ppid`: `PemohonVerifikasiDialog.tsx`, penyesuaian `PpidResourcePage.tsx`, `ResourceListPage.tsx`, `lib/resources.ts`, kamus Inggris.

### Verifikasi

Diuji lewat HTTP sungguhan pada dua server (`api` 8811, `fe` 8813); token admin dibuat dari akun `Administrator` yang sudah ada — **tidak ada akun uji baru di panel**. Seluruh data uji sudah dihapus.

- **Daftar akun** → tercatat 2 baris notifikasi `pemohon_baru` (dua admin berhak), pesan: "Akun pemohon baru: Uji Verifikasi 65 (uji65@…)."
- **Kirim berkas data diri** (dengan unggahan KTP) → status `menunggu`, berkas tersimpan, 2 baris notifikasi `verifikasi_pemohon`: "…menunggu verifikasi (pengiriman ke-1 dari 3)."
- **API `GET pemohon`** mengembalikan `status_verifikasi`, `jumlah_ditolak`, `verifikasi_diblokir`, `sisa_kesempatan`, dan relasi `verifikator`.
- **Tolak 3 kali** → 200 dengan sisa kesempatan **2 → 1 → 0**. **Tolak keempat** → **422** "Pemohon ini sudah ditolak 3 kali dan tidak dapat mengirim berkas lagi." **Tolak tanpa alasan** → **422** "Alasan penolakan wajib diisi…".
- **Portal saat diblokir**: halaman Data Pemohon menampilkan alasan petugas, "Pengiriman ulang ditutup setelah 3 kali penolakan", tombol kirim `disabled`; pengiriman paksa lewat POST **ditolak server** (isian `alamat` tidak berubah).
- **Daftar ulang dengan email terblokir** → ditolak: "Pendaftaran dengan email ini diblokir karena data diri sudah ditolak 3 kali…".
- **Setujui** → 200 "Data pemohon dinyatakan terverifikasi.", `tanggal_verifikasi` dan `verifikator` terisi (`Administrator`). Mencoba **menolak setelah terverifikasi** → **422**.
- **Setelah disetujui**, lapisan penghalang di portal hilang (0 kemunculan) dan `/akun/permohonan/baru` terbuka **200**.
- `php -l` bersih; `npx tsc --noEmit` + `npx vite build` (be) bersih.

### Catatan untuk operator

Modul Pemohon sekarang membutuhkan hak **Setujui** pada modul `permohonan` di matrix hak akses role. Role yang hanya punya `Lihat` akan melihat daftarnya tetapi tanpa menu **Verifikasi data**.

### Yang belum dikerjakan

- Captcha gambar masih belum dapat diakses pembaca layar (tercatat sejak putaran 43).
- Lonceng notifikasi **di portal pemohon** (kalimat tanpa nomor di bawah blok langkah 65) belum dikerjakan. Yang selesai pada putaran ini adalah arah sebaliknya: pemohon → notifikasi admin.

---


## Status Pengerjaan (putaran 44 — langkah 64)

### Soal MAC address — tidak bisa dipenuhi apa adanya

Permintaannya menyebut pencatatan **MAC address**. Alamat itu **tidak pernah sampai ke server**: paket yang tiba sudah melewati banyak perangkat jaringan, dan MAC hanya terlihat oleh perangkat yang berada di segmen jaringan yang sama dengan pengunjung. Tidak ada header HTTP, API JavaScript, maupun konfigurasi server yang bisa mengambilnya dari peramban — ini batas protokol, bukan soal pustaka yang belum dipasang.

Gantinya dicatat tiga hal, dan **ketiganya dihitung terpisah**:

| Yang dicatat | Sifatnya |
|---|---|
| `ip_address` | asal permintaan; bisa berubah bila ganti jaringan |
| `penanda_perangkat` | nilai acak 48 karakter dalam cookie berumur 2 tahun; menandai **peramban**, hilang bila cookie dibersihkan atau memakai mode penyamaran |
| `user_agent` | sidik peramban, untuk catatan bila cookie dihapus |

Karena hitungannya terpisah, menghapus cookie **tidak** mengosongkan hitungan email dan IP — jadi pembatasannya tidak bergantung pada penanda perangkat sendirian.

### Kirim tautan: satu kali per 30 menit

Berlaku untuk **tautan verifikasi pendaftaran** dan **tautan lupa password**, masing-masing dengan jatah sendiri (`jenis` berbeda di tabel).

- Tabel baru **`pengiriman_tautan_akun`** mencatat tiap pengiriman: jenis, email tujuan, IP, penanda perangkat, user agent, waktu. Selain menjadi dasar pembatasan, isinya jejak bila perlu menelusuri siapa yang menghabiskan kuota email.
- Pemeriksaannya membandingkan **email ATAU IP ATAU penanda perangkat** dalam 30 menit terakhir. Tanpa hitungan per-IP, satu orang bisa menguras kuota hanya dengan mengetik alamat yang berbeda-beda tiap kali.
- **Tombol "Kirim Ulang Tautan"** pada halaman verifikasi ikut memakai jatah yang sama. Kalau tidak, tombol itu jadi celah yang membuat pembatasan di halaman daftar tidak ada gunanya.
- Pemeriksaan dijalankan **sebelum** baris pemohon dibuat, sehingga percobaan yang ditolak tidak meninggalkan akun setengah jadi.
- Pesannya menyebut sisa menitnya dan mengingatkan memeriksa folder Spam.
- **Captcha ditambahkan pada formulir Lupa Password** (pada Registrasi sudah ada sejak putaran 43), berikut honeypot dan jeda pengisian minimum.

Angkanya di `config('ppid.akun.jeda_kirim_tautan_menit')` (`PPID_JEDA_KIRIM_TAUTAN_MENIT`, bawaan 30).

### Kunci masuk bertingkat

Tabel baru **`percobaan_login_pemohon`** menyimpan hitungan kegagalan dan sampai kapan dikunci. Disimpan di basis data, bukan cache: kuncinya bisa berlaku sampai 72 jam, sedang cache berkas bisa terhapus tanpa sengaja dan kuncinya ikut hilang.

| Kegagalan ke- | Masa tunggu |
|---|---|
| 3 | 1 jam |
| 6 | 24 jam |
| 9 | 72 jam |
| 12, 15, … | tetap 72 jam |

- Selama terkunci, **password yang benar pun ditolak** — kalau tidak, kuncinya tidak menahan apa pun.
- Berhasil masuk **menghapus** barisnya, jadi hitungan mulai dari nol lagi.
- Hitungan disetel ulang bila 72 jam berlalu tanpa kegagalan baru (`PPID_RESET_HITUNGAN_GAGAL_JAM`). Tanpa ini, orang yang lupa password tiga kali beberapa bulan lalu akan langsung mendarat di tahap berikutnya.
- Kegagalan captcha **tidak** dihitung sebagai kegagalan masuk: captcha diperiksa pada tahap validasi, sebelum password diadu.

**Kunci dipasang per kombinasi identitas + alamat IP, bukan per identitas saja.** Ini pilihan sadar dan perlu diketahui: mengunci per identitas berarti siapa pun yang tahu email seorang pemohon bisa mengunci akun itu selama 72 jam hanya dengan sengaja salah password sembilan kali — pemblokiran layanan yang lebih merugikan daripada serangan yang hendak dicegah. Penyerang sungguhan tetap tertahan karena kegagalannya menumpuk pada IP-nya sendiri, dan penyebaran lewat banyak IP masih dibatasi rem per-IP per menit yang dipasang pada putaran 43. Bila tetap ingin per identitas, yang perlu diubah hanya kunci pencarian di `App\Support\KunciLoginPemohon`.

Selain itu, syarat password pada **Reset Password** dinaikkan agar sama dengan formulir pendaftaran (10 karakter, huruf besar-kecil, angka) — sebelumnya masih 8 karakter, sehingga reset password menjadi jalan memutar untuk memasang password lemah.

### Berkas yang ditambahkan

- `api-ppid/database/migrations/2026_08_15_000001_create_keamanan_akun_tables.php`
- `fe-ppid/app/Support/PenandaPerangkat.php`, `PembatasTautan.php`, `KunciLoginPemohon.php`
- `fe-ppid/app/Models/PengirimanTautanAkun.php`, `PercobaanLoginPemohon.php`

### Verifikasi

Diuji lewat HTTP sungguhan pada `php artisan serve` (mailer dialihkan ke `log` selama pengujian lalu dikembalikan ke `smtp`; seluruh data uji sudah dihapus):

- **Daftar pertama** → **302 ke `/akun/masuk`**, dan tercatat satu baris `pengiriman_tautan_akun`: `registrasi | uji64a@… | ip=127.0.0.1 | perangkat=Qnkgzvah9lE9… | ua=curl/8.8.0`.
- **Daftar kedua, email berbeda, IP/perangkat sama** → ditolak: "Tautan sudah pernah dikirim. Silakan coba lagi dalam 26 menit…", **tanpa** akun baru dan **tanpa** catatan kirim tambahan.
- **Lupa password pertama** → terkirim (jatahnya sendiri, tidak terpengaruh jatah pendaftaran). **Kedua, email berbeda** → ditolak "…coba lagi dalam 30 menit", catatan tetap 1 baris.
- **Kunci bertingkat**: gagal ke-1 dan ke-2 dijawab "Email/nomor telepon atau password tidak cocok"; gagal ke-3 → **"Sudah 3 kali gagal masuk. Demi keamanan akun, coba lagi setelah 1 jam."** Tahap berikutnya diuji langsung: ke-6 → **24 jam**, ke-9 → **72 jam**, ke-12 → **tetap 72 jam** (`tahap_kunci` 2/3/4).
- **Password benar saat terkunci** → tetap ditolak: "Akun ini sementara dikunci karena terlalu banyak percobaan masuk yang gagal…".
- **Login berhasil** setelah kunci dilepas → baris `percobaan_login_pemohon` **terhapus** (0 baris tersisa).
- 6 rute akun diperiksa **200** (`/akun/verifikasi` 302 karena tidak ada akun yang menunggu); `php -l` bersih.

### Yang belum dikerjakan

- Captcha gambar masih **belum dapat diakses pembaca layar** (dicatat sejak putaran 43).
- Lonceng notifikasi portal pemohon di bawah blok langkah 64 belum dikerjakan — tidak bernomor dan tidak termasuk permintaan putaran ini.

---


## Status Pengerjaan (putaran 43 — langkah 63)

Alur akun pengunjung sudah ada sebagian sejak awal (pendaftaran, verifikasi email, guard `pemohon`, kolom `status_verifikasi`). Putaran ini melengkapi yang belum ada dan meluruskan alurnya sesuai enam langkah yang diminta.

### 1. Formulir pendaftaran

- **Nomor Telepon/HP (WhatsApp) kini wajib** — sebelumnya boleh kosong. Formatnya dibatasi angka dan tanda `+ ( ) -`, dan keterangannya menyebut nomor itu juga dipakai untuk masuk.
- **Captcha gambar** ditambahkan (lihat bagian keamanan). Nama lengkap, email, password, dan konfirmasi password sudah ada sebelumnya.
- Syarat password dinaikkan dari 8 karakter menjadi **10 karakter dengan huruf besar, huruf kecil, dan angka**.
- Email divalidasi `rfc,dns` — domain yang tidak bisa menerima surat ditolak sejak awal, sehingga tidak ada kuota kirim yang terbuang ke alamat mati.
- Perbaikan sekalian: pendaftar baru sempat disimpan dengan `jenis_pemohon = 'pribadi'`, nilai yang tidak ada di daftar pilihan modul Pemohon. Sekarang `'perorangan'`.

### 2. Setelah submit → halaman masuk

Sebelumnya pendaftar dilempar ke halaman "cek email Anda". Sekarang ia mendarat di **halaman masuk** dengan kotak peringatan kuning: akun sudah dibuat, tetapi tautan verifikasi harus dibuka dulu, lengkap dengan alamat tujuannya dan masa berlaku tautan. Di dalamnya ada tautan "Belum menerima emailnya?" menuju halaman kirim ulang.

Alasannya alurnya jadi satu arah: halaman masuk adalah tempat yang sama yang akan ia datangi lagi setelah membuka tautan di emailnya.

### 3. Tautan verifikasi

- Masa berlaku dipindah ke `config('auth.verification.expire')` dan disetel **24 jam** (`PPID_VERIFIKASI_EMAIL_MENIT`), naik dari bawaan Laravel 60 menit. 60 menit terlalu pendek untuk layanan publik: orang membuka emailnya beberapa jam kemudian, lalu meminta tautan baru — dan tiap permintaan ulang memakan kuota kirim yang terbatas. Angka yang sama dipakai saat membuat tautan, di badan email, dan di teks halaman, jadi tidak ada dua angka yang berbeda.
- Tautannya tetap bertanda tangan dan terikat pada id + email pemiliknya.
- Membuka tautan tanpa sesi login → kembali ke **halaman masuk** dengan pesan "Email Anda sudah terverifikasi. Silakan masuk." (perilaku ini sudah benar sebelumnya, hanya diuji ulang).

### 4. Masuk dengan email **atau** nomor telepon

- Satu isian `identitas` menerima keduanya; bentuknya ditebak dari ada tidaknya `@`.
- Nomor telepon **tidak** diserahkan mentah ke guard sebagai `['no_hp' => …]`. Kolom itu tidak dijamin unik — baris lama bisa diinput petugas dengan nomor sama — jadi nomornya diterjemahkan dulu menjadi email pemiliknya, hanya di antara baris yang benar-benar punya akun. Penulisan `0812…`, `+62 812…`, dengan spasi atau tanda hubung sama-sama dikenali karena kedua sisi diringkas ke angka saja.
- Nomor yang tidak ditemukan tetap diteruskan sebagai email kosong supaya gagalnya lewat jalur yang sama — halaman ini tidak bisa dipakai menebak nomor mana yang terdaftar.
- Captcha ditambahkan di sini juga.

### 5. Penghalang Verifikasi Data Diri Pemohon

Lapisan penuh layar (`akun/partials/popup-verifikasi.blade.php`) menutup portal pengguna selama data diri belum disetujui petugas. Isinya menyesuaikan status (belum diisi / sedang diperiksa / ditolak), menyebut status terkini, dan hanya menyediakan dua jalan: **Buka Data Pemohon & Berkas** atau keluar dari akun.

- Halaman Data Pemohon sendiri, penyimpanannya, unduhan KTP, dan tombol keluar **tidak** ikut ditutup — kalau ikut, syaratnya tidak akan pernah bisa dipenuhi.
- Lapisan ini urusan tampilan. Pembatasan yang sebenarnya ada di server: **Permohonan** sudah dijaga sejak sebelumnya, dan pada putaran ini **Keberatan** (`create` + `store`) ikut dijaga — sebelumnya bisa diakses langsung lewat URL.
- Selagi berkas masih diperiksa, portal **tetap** terkunci sesuai permintaan. Karena pada tahap itu pemohon tidak punya lagi yang bisa dikerjakan, perilakunya bisa dilonggarkan lewat `PPID_BLOKIR_SAAT_MENUNGGU=false` tanpa mengubah kode.

### 6. Janji layanan 14 hari kerja

`config('ppid.akun.sla_verifikasi_hari_kerja')` (bawaan 14) ditampilkan di lapisan penghalang dan di banner status pada Dashboard/Permohonan.

### Konfigurasi email

`.env` fe-ppid disetel ke SMTP Niagahoster: `srv179.niagahoster.com`, port **465** (SSL), pengguna dan pengirim **`noreply-ppid@foodstation.co.id`**.

Percobaan pertama memakai `reply-ppid@…` (tanpa `no`) dan ditolak `535 Incorrect authentication data`; setelah alamatnya dibetulkan, autentikasi dijawab `235 Authentication succeeded` dan satu email verifikasi sungguhan berhasil terkirim.

Server mengumumkan batas kirimnya sendiri: `MAILMAX=1000`, `RCPTMAX=50` — itulah kuota yang dijaga oleh masa berlaku tautan 24 jam dan penolakan domain mati pada validasi `rfc,dns`.

### Pengetatan AUTH (pengguna & admin)

Bukan satu rem, melainkan berlapis — captcha saja mudah dilewati skrip yang mengirim langsung ke endpoint.

**Portal pengunjung (fe-ppid)**

- **Captcha gambar buatan sendiri** (`App\Support\Captcha`, GD): tanpa layanan pihak ketiga, jadi tidak ada ketergantungan jaringan luar saat orang mendaftar. Kodenya **tidak pernah disimpan apa adanya** — hanya HMAC-nya yang dititipkan di session; berlaku 5 menit; **sekali diperiksa langsung dibuang**, sehingga satu gambar tidak bisa dipakai menebak berkali-kali. Huruf besar/kecil tidak dibedakan.
- **Honeypot** `alamat_surat`: isian yang disembunyikan dari mata, pembaca layar, dan urutan tab. Terisi = pasti bukan manusia.
- **Jeda pengisian minimum**: waktu formulir dibuka dititipkan **terenkripsi** sehingga tidak bisa dipalsukan klien; kiriman yang datang di bawah 3 detik ditolak, begitu pula formulir yang sudah dibuka lebih dari 2 jam (agar penanda waktunya tidak bisa dipakai berulang).
- **Batas percobaan masuk berlapis**: per kombinasi identitas + IP (5/menit, sudah ada) **ditambah** per IP saja (20/menit). Yang kedua menahan penyerang yang mencoba banyak akun sekaligus dari satu tempat — pola itu lolos dari batas per-identitas karena tiap percobaan memakai identitas berbeda.
- Pesan gagal tidak membedakan "akun tidak ada" dan "password salah".

**Panel admin (api-ppid)**

- Ditambah **kunci akun sementara**: 10 kegagalan dalam jendela 15 menit → percobaan berikutnya dijawab **429** selama sisa waktu itu. Melengkapi `throttle:login` yang jendelanya hanya satu menit dan karenanya masih mengizinkan penebakan pelan-pelan sepanjang hari. Kejadiannya dicatat ke `audit_log` sebagai `login_locked`.
- Syarat kata sandi akun petugas sudah kuat sejak sebelumnya (minimal 12 karakter, huruf besar-kecil, angka, simbol) — tidak diubah.

**Injeksi SQL.** Tidak ada satu pun query yang menyambung isian pengguna ke SQL: seluruh modul lewat Eloquent, kolom `sort`/`filter` di panel dicocokkan ke daftar putih, dan satu-satunya ekspresi mentah yang baru (normalisasi nomor telepon saat masuk) memakai parameter terikat.

### Dua bug yang ditemukan saat pengujian dan ikut dibetulkan

1. **Isian captcha tetap wajib walau captcha dimatikan** lewat konfigurasi — formulirnya jadi mustahil dikirim karena isiannya memang tidak dirender. Sekarang wajibnya ikut sakelar yang sama.
2. **Permintaan gambar captcha menimpa "halaman sebelumnya".** `StartSession` mencatat setiap GET biasa, dan `<img>` termasuk GET biasa — akibatnya `back()` setelah validasi gagal melempar pengguna ke berkas gambar, bukan kembali ke formulirnya. Diperbaiki dengan middleware `BukanHalamanSebelumnya` pada rute captcha.

Ditemukan juga bahwa pesan penolakan honeypot/jeda sebelumnya menumpang pada isian captcha, sehingga hilang tanpa jejak saat captcha dimatikan. Sekarang pesannya punya kunci sendiri (`perisai`) dan tempat tayang sendiri.

### Verifikasi

Diuji lewat HTTP sungguhan pada `php artisan serve` (akun uji `uji.alur63@foodstation.co.id`, **sudah dihapus permanen** beserta baris audit uji):

- **Daftar** → **302 ke `/akun/masuk`**, halaman masuk menampilkan "Verifikasi email dulu sebelum masuk" beserta alamat tujuan dan "Tautannya berlaku 24 jam."
- **Tautan verifikasi** dari log: `expires` = besok pukul 07.41 terhadap waktu uji 07.42 hari ini → **tepat 24 jam**. Dibuka → **302 ke `/akun/masuk`** dengan "Email Anda sudah terverifikasi. Silakan masuk."
- **Masuk memakai `+62 812 3456 7890`** (didaftarkan sebagai `0812-3456-7890`) → **302 ke `/akun`**, sesi terbentuk.
- **Lapisan penghalang**: muncul di `/akun` dan `/akun/histori`, **tidak** muncul di `/akun/pengaturan/data-pemohon`; teksnya memuat "paling lama 14 hari kerja" dan status terkini.
- **Gerbang server**: `/akun/permohonan/baru` dan `/akun/keberatan/baru` sama-sama **302 ke Data Pemohon**.
- **Honeypot terisi** → ditolak; **kiriman di bawah 3 detik** → ditolak; keduanya menampilkan pesannya dan **0 baris tersimpan** di basis data.
- **Captcha**: endpoint mengembalikan **PNG sah** (`89 50 4E 47`, ~5,6 KB) dengan `Content-Type: image/png`; jawaban salah ditolak dan pengguna tetap belum masuk; setelah perbaikan, `back()` mendarat di `/akun/masuk`. Uji satuan: jawaban benar (huruf kecil) diterima, salah ditolak, **kode yang sama tidak bisa dipakai dua kali**, kode kedaluwarsa ditolak, isian kosong ditolak.
- **Kunci akun admin**: percobaan ke-1..10 dijawab **401**, percobaan ke-11 dijawab **429** "Terlalu banyak percobaan masuk. Coba lagi dalam 896 detik."
- **SMTP**: `AUTH LOGIN` ke `srv179.niagahoster.com:465` dijawab **`235 Authentication succeeded`**. Satu email verifikasi sungguhan dikirim lewat jalur aplikasi ke kotak surat itu sendiri (`noreply-ppid@foodstation.co.id`) — tanpa exception dan tanpa peringatan di log. Selama pengujian alur lainnya mailer dialihkan ke `log` supaya tidak ada email yang terbuang, lalu dikembalikan ke `smtp`.
- 7 rute publik dan halaman akun diperiksa **200**; `php -l` bersih.

### Yang belum dikerjakan

- Captcha gambar ini **tidak dapat diakses pembaca layar**. Situs sudah memakai widget aksesibilitas EqualWeb, jadi ini penurunan yang nyata bagi pengguna tunanetra. Alternatif yang setara aman dan tetap terbaca: pertanyaan hitung sederhana dalam bentuk teks. Belum dikerjakan karena permintaannya menyebut "konfirmasi Captcha".
- Permintaan **lonceng notifikasi di portal pemohon** yang tertulis di bawah blok langkah 63 belum dikerjakan — nomornya tidak termasuk yang diminta pada putaran ini.

---


## Status Pengerjaan (putaran 42 — langkah 61, 62)

### Langkah 61 — "Diubah oleh" dan "Diubah" tersembunyi dulu

Kolomnya tetap ada di setiap modul, hanya tidak ditampilkan sejak awal. Operator memunculkannya lewat tombol **Show/Hide columns** di toolbar tabel (ikon `lucide:columns-3-cog`), dan pilihannya bertahan selama halaman itu dibuka.

- `lib/jejak.ts` menambah `visibilitasAwalJejak(config)` yang menghasilkan `{ pengubah: false, updated_at: false }`. Dipakai `ResourceListPage` sebagai `initialState.columnVisibility`, dihitung sekali lewat `useState(() => …)` karena MRT hanya membaca `initialState` pada render pertama — setelah itu visibilitas kolom milik operator dan tidak boleh ditimpa ulang.
- Kolom bernama sama yang **ditulis sendiri** oleh sebuah modul tidak ikut disembunyikan: modul itu memang sengaja memilihnya sebagai kolom utama.
- `DataTable` sebelumnya memakai `_.defaults` yang hanya mengisi kunci kosong, jadi `initialState` dari pemanggil akan **menghapus seluruh bawaan** (kerapatan, pin kolom, ukuran halaman). Sekarang `initialState` dikeluarkan lebih dulu lalu digabung per kunci, sehingga bawaan tetap hidup dan pemanggil cukup mengirim yang ingin diubah. Tabel lain tidak terpengaruh karena tak satu pun mengirim `initialState`.
- Pasangan **"Dihapus oleh"/"Dihapus"** tetap tampil apa adanya — kolomnya memang hanya muncul saat filter **Status data** sedang membuka arsip penghapusan, jadi menyembunyikannya di sana justru melawan alasan operator membukanya.

### Langkah 62 — label kolom dipendekkan

`Dibuat pada` → **`Dibuat`**, `Diubah pada` → **`Diubah`**. Kata "pada" tidak menambah arti; kolom sebelahnya sudah bernama "Dibuat oleh"/"Diubah oleh" sehingga bedanya tetap terbaca.

`Dihapus pada` ikut jadi **`Dihapus`** supaya ketiganya seragam — ini di luar yang diminta, dan cuma label, jadi mudah dikembalikan bila memang ingin tetap "Dihapus pada".

### Rapi-rapi yang menyertai

- Modul **Halaman Statis** tidak lagi menulis kolom `editor` ("Diubah oleh") dan `updated_at` ("Diperbarui") sendiri — keduanya sudah datang dari jejak dokumen, termasuk aturan sembunyikan-dulu-nya. Tanpa ini modul tersebut jadi satu-satunya yang menampilkan "Diubah" secara bawaan.
- Muatan relasi `editor:id,name` di `HalamanStatisController` dilepas: `CrudController` sudah memuat `pembuat`/`pengubah`/`penghapus` untuk semua modul. Relasi `editor()` di model tetap ada sebagai nama lama.
- Kunci terjemahan yang tidak terpakai lagi dihapus dari `@i18n/kamusPpid.ts` (`Dibuat pada`, `Diubah pada`, `Dihapus pada`, `Diperbarui`); `Diubah` ditambahkan; `Diubah oleh` diselaraskan dari `Edited by` menjadi `Updated by`.

### Verifikasi

- Tombol **Show/Hide columns** dipastikan memang dirender: `DataTableTopToolbar` memasang `MRT_ToolbarInternalButtons`, dan MRT menampilkan `MRT_ShowHideColumnsButton` bila `enableHiding || enableColumnOrdering || enableColumnPinning` — dua yang terakhir memang `true` di `DataTable`, dan `enableHiding` tidak pernah dimatikan.
- `php -l` bersih; `npx tsc --noEmit` + `npx vite build` (be) bersih.

**Belum diverifikasi lewat browser.** Perubahan putaran ini seluruhnya tampilan, jadi pemeriksaan visual sebaiknya dilakukan sebelum dianggap tuntas.

---


## Status Pengerjaan (putaran 41 — langkah 60)

Kolom **Diubah** sekarang hanya terisi oleh perubahan isi yang sungguhan. Sebelumnya Laravel menyamakan `updated_at` dengan `created_at` saat baris dibuat, jadi "Diubah pada" selalu terisi walau belum ada satu pun aktivitas Ubah — angka yang tidak berarti apa-apa.

### Yang diperbaiki di sisi tulis

- **Buat data baru → "Diubah" kosong.** Trait `MencatatPelaku` (api-ppid) meng-null-kan `updated_at` + `updated_by` pada event `creating`, sebelum Laravel sempat menstempelnya. Pasangannya di fe-ppid adalah trait baru `TanpaCapUbahSaatDibuat`, dipasang pada model yang barisnya dibuat pengunjung: `PermohonanInformasi`, `KeberatanInformasi`, `Pemohon`, `SurveyKepuasan`.
- **Hapus data tidak lagi mengisi "Diubah".** Menghapus bukan mengubah isi. `deleting` mematikan `timestamps` supaya `runSoftDelete()` tidak menstempel `updated_at`, lalu `deleted` mengisi `deleted_by` lewat query builder mentah (`Eloquent\Builder::update()` akan menambahkan `updated_at` sendiri — persis yang dihindari).
- **Login tidak lagi mengisi "Diubah" pada modul Pengguna.** `AuthController@signIn` menyimpan `last_login_at`; penyimpanannya kini dibungkus `timestamps = false`. Cap waktu login bukan penyuntingan data pengguna.
- Penghitung tampilan berita di fe-ppid (`KontenController@beritaShow`) memang sudah memakai `timestamps = false` sejak awal — tidak perlu diubah.

### Data lama dikosongkan

Migrasi `2026_08_14_000002_clear_updated_trail_never_edited` mengosongkan `updated_at` pada **22 tabel** modul dengan syarat `updated_by IS NULL`.

Syarat itu penting: sejak jejak dokumen aktif, setiap perubahan isi yang sungguhan selalu meninggalkan pelaku di `updated_by`. Baris tanpa pelaku berarti tidak pernah disunting lewat panel, jadi aman dikosongkan — dan migrasinya tetap aman bila dijalankan lagi di lingkungan yang datanya sudah hidup, karena baris yang benar-benar pernah diubah tidak tersentuh.

Pemeriksaan sebelum dijalankan membenarkan dugaan pada langkah ini: **tidak ada satu pun baris dengan `updated_by` terisi**. Nilai `updated_at` yang ada semuanya cap waktu semu — sama persis dengan waktu pembuatan, atau seragam `2026-08-12 03:30:50` yang berasal dari `TerjemahanInggrisSeeder`. Migrasinya **tidak bisa dibalik**: nilai lama itu tidak disimpan di mana pun, tetapi memang bukan riwayat penyuntingan yang hilang.

### Tampilan

Blok **Jejak dokumen** membedakan dua keadaan yang sebelumnya sama-sama tertulis "Tidak tercatat":
- **"Belum pernah diubah"** — baris memang belum pernah disunting;
- **"Tidak tercatat"** — ada cap waktunya tetapi pelakunya tidak terekam (mis. data lama, atau perubahan dari situs publik oleh pemohon sendiri yang bukan pengguna panel).

Kolom "Diubah oleh"/"Diubah pada" di daftar modul menampilkan `—` bila kosong.

### Verifikasi

- **Buat data** (`POST banner-slider`, `POST faq`, `Pemohon::create` di fe-ppid) → `updated_at` dan `updated_by` **null**, `created_by` terisi.
- **Ubah data** (`PUT banner-slider`) → `updated_at` terisi waktu baru + `updated_by` terisi.
- **Hapus setelah diubah** → `deleted_at`/`deleted_by` terisi, `updated_at` **tetap pada waktu perubahan tadi** (tidak ikut maju).
- **Buat lalu hapus tanpa pernah diubah** → `deleted_at`/`deleted_by` terisi, `updated_at`/`updated_by` **tetap null**.
- **Hapus massal** (`hapus-massal` kategori berita, 2 baris) → hasil sama.
- **Login ulang** → `last_login_at` terisi, `updated_at` tetap **null**.
- **23 endpoint modul disapu**: semuanya **200**, dan **tidak ada** `updated_at` terisi di seluruh modul setelah migrasi.
- **17 rute publik fe-ppid** semuanya **200**.
- `php -l` bersih; `npx tsc --noEmit` + `npx vite build` (be) bersih.
- Data uji dan admin sementara sudah dihapus permanen setelah pengujian.

**Belum diverifikasi lewat browser** (pengujian Playwright dihentikan atas permintaan). Perubahan tampilan pada putaran ini hanya label di blok Jejak dokumen; jalur datanya sudah diuji lewat HTTP.

---


## Status Pengerjaan (putaran 40 — langkah 58, 59)

### Langkah 58 — modul Laporan Statistik dilepas dari panel

Modulnya sudah tidak ada di registry (`lib/resources.ts`) maupun menu (`lib/navigation.ts`); grup **Layanan** kini berisi Permohonan, Keberatan, Laporan Pelayanan, Pemohon, dan Survei. Sisa kunci terjemahannya (`Laporan Statistik` dan dua teks bantuan khusus laporan statistik) dibersihkan dari `@i18n/kamusPpid.ts` pada putaran ini.

Yang **tidak** dihapus: tabel `laporan_layanan` beserta baris bertipe `statistik_informasi` dan endpoint `laporan-layanan/rekap` di API. Halaman **Laporan Statistik Informasi Publik** di situs publik masih memakai data itu, jadi menghapusnya akan mematikan halaman publik — yang diminta hanya modulnya di panel.

### Langkah 59 — jejak dokumen (traceability) di semua modul

Setiap tabel modul kini punya enam kolom seragam: `created_at`/`created_by`, `updated_at`/`updated_by`, `deleted_at`/`deleted_by`.

- **Migrasi `2026_08_14_000001_add_traceability_columns`** menambahkannya ke **22 tabel** modul (kategori informasi, informasi publik, informasi dikecualikan, pemohon, permohonan, keberatan, laporan layanan, survei, kategori berita, berita, galeri, FAQ, banner, struktur, halaman statis, maklumat, regulasi, menu navigasi, tautan terkait, pengaturan situs, roles, users). Kolom pelaku memakai `nullOnDelete` supaya pengguna yang dihapus tidak menyeret dokumennya ikut hilang.
- **Penghapusan jadi soft delete.** Tanpa itu `deleted_by` tidak ada gunanya — barisnya sudah lenyap sebelum sempat dicatat. Model di api-ppid **dan** fe-ppid sama-sama memakai `SoftDeletes`, jadi baris terhapus tetap tidak tampil di situs publik. Satu query mentah yang tidak lewat model (`DB::table('survey_kepuasan')->avg('rating')` di `PpidController@statistikRingkas`) diberi `whereNull('deleted_at')` sendiri.
- **Pengisiannya otomatis** lewat trait `App\Models\Concerns\MencatatPelaku`: `creating` mengisi `created_by` + `updated_by`, `updating` mengisi `updated_by`, `deleting` mengisi `deleted_by`, `restoring` mengosongkannya lagi. Ketiganya **tidak fillable** — pelaku diambil dari token yang login, bukan dari isian klien, jadi tidak bisa dipalsukan lewat body request. Aksi dari luar panel (situs publik, seeder, artisan) meninggalkan pelaku `NULL`, bukan tertulis atas nama orang lain.
- **`CrudController`** memuat relasi `pembuat`, `pengubah`, `penghapus` (`id,name` saja) di semua daftar dan detail, mengizinkan pengurutan kolom jejak tanpa perlu didaftarkan ulang tiap modul, dan menerima `?terhapus=hanya|semua` untuk membuka arsip penghapusan.
- **Panel** menempelkan kolom **Dibuat oleh / Dibuat pada / Diubah oleh / Diubah pada** ke setiap daftar modul secara otomatis (`lib/jejak.ts`) — modul baru ikut dapat tanpa mengubah `resources.ts`. Filter **Status data** (Aktif / Terhapus / Aktif + terhapus) membuka data terhapus; saat itu aktif, dua kolom **Dihapus oleh / Dihapus pada** ikut muncul dan tombol Tambah/Ubah/Hapus dimatikan karena barisnya tinggal arsip. Formulir Ubah menampilkan blok **Jejak dokumen** berisi ketiganya.
- **Audit Log dikecualikan** (`tanpaJejak: true`): tabel itu memang catatan perubahan, barisnya tidak pernah diubah atau dihapus.
- **Data lama tidak diisi mundur.** Baris yang dibuat sebelum migrasi bernilai `NULL` dan tampil sebagai "Tidak tercatat" — menebak pelaku/waktu untuk data lama justru merusak nilai jejaknya. Blok Jejak dokumen tetap muncul supaya jelas bedanya "belum pernah dicatat" dan "modul ini memang tanpa jejak".

Hubungannya dengan `audit_log` yang sudah ada: log berisi **riwayat lengkap** tiap aksi (termasuk nilai lama/baru dan IP), kolom-kolom baru ini adalah **keadaan terakhir** yang bisa ditampilkan dan diurutkan langsung di daftar modul.

### Verifikasi

Diuji lewat HTTP sungguhan dengan admin sementara (`uji.jejak@local.test`, **sudah dihapus permanen setelah pengujian** beserta data ujinya):

- Siklus penuh pada modul FAQ: `POST` → `created_by=6, updated_by=6`; `PUT` → `updated_by=6` + `updated_at` baru; `DELETE` → `deleted_by=6`, `deleted_at` terisi, baris **hilang dari daftar aktif**; `GET ?terhapus=hanya` → baris itu muncul lengkap dengan `pembuat`, `pengubah`, `penghapus`. `restore()` mengosongkan `deleted_by` kembali.
- **23 endpoint modul disapu**: semuanya **200**, dan setiap modul yang ada isinya mengembalikan relasi `pembuat`. Audit Log benar tidak mengembalikannya.
- **18 rute publik fe-ppid** semuanya **200** (`/cek-status` 302 ke login seperti sebelumnya); halaman FAQ, banner beranda, regulasi, dan berita tetap menampilkan isinya — soft delete tidak menyembunyikan data aktif.
- Panel dibuka dengan Playwright: daftar FAQ dan Banner menampilkan kolom "Dibuat oleh / Dibuat pada / Diubah oleh / Diubah pada"; filter **Status data** ada di keduanya dan **tidak ada** di Audit Log; memilih **Terhapus** memunculkan kolom "Dihapus oleh / Dihapus pada" beserta barisnya; formulir Ubah menampilkan blok **Jejak dokumen**. Tidak ada error di console.
- `php -l` bersih untuk seluruh berkas yang disentuh; `npx tsc --noEmit` dan `npx vite build` (be) bersih.

---


## Status Pengerjaan (putaran 39 — langkah 57)

Ikon menu diambil dari sprite `be-ppid/public/assets/icons/lucide.svg`, dan nama ikon harus persis sama dengan id `<symbol>` di dalamnya. Modul FAQ memakai `lucide:circle-question-mark` — id itu tidak ada di sprite, jadi `<use>`-nya tidak menemukan apa pun dan menunya tampil tanpa ikon (bukan ikon gagal muat, melainkan nama yang salah).

- Ikon FAQ diganti ke **`lucide:circle-help`** (id yang memang ada di sprite).
- Seluruh nama ikon lain di panel ikut diperiksa terhadap sprite: **36 ikon dipakai, hanya FAQ yang salah nama**; sisanya cocok.
- Dua ikon pada `ModulSistemSeeder` juga tidak ada di sprite heroicons: `heroicons-outline:photograph` → `heroicons-outline:photo` (Galeri) dan `heroicons-outline:exclamation` → `heroicons-outline:exclamation-triangle` (Keberatan). Ikon di tabel `modul_sistem` belum dipakai menu panel (menu memakai ikon dari registry), tetapi dibetulkan sekalian supaya tidak jadi jebakan bila kolomnya dipakai nanti.

### Verifikasi

- Panel dibuka dengan Playwright memakai admin sementara (**sudah dihapus setelah pengujian**): menu FAQ kini bergambar ikon tanda tanya dalam lingkaran.
- Pemeriksaan nama ikon terhadap sprite: 0 nama yang tidak ditemukan setelah perbaikan.
- `php -l` bersih, seeder dijalankan ulang; `npx tsc --noEmit` + `npx vite build` (be) bersih.

---


## Status Pengerjaan (putaran 38 — langkah 54, 55, 56)

### Langkah 54 — modul Tautan dan Modul Sistem dilepas

- Keduanya dihapus dari registry modul panel (`lib/resources.ts`) dan dari menu (`lib/navigation.ts`).
- Baris `tautan-terkait` di tabel `modul_sistem` **dinonaktifkan** (bukan dihapus) lewat `ModulSistemSeeder`. Efeknya berantai: hilang dari menu, hilang dari matrix hak akses, dan endpoint-nya ditolak middleware `akses:` untuk role non-super-admin. Data `tautan_terkait` beserta hak akses lamanya tetap tersimpan.
- "Modul Sistem" tidak perlu halaman sendiri: daftar modul kini muncul sebagai baris-baris matrix pada dialog **Atur hak akses** di modul Role, diambil langsung dari tabel `modul_sistem`.
- Situs publik tidak terpengaruh — tabel `tautan_terkait` memang masih kosong, jadi blok tautan di footer sudah tidak tampil sejak awal.

### Langkah 55 — modul Pemohon jadi baca saja

- Konfigurasi modul diberi `readOnly: true` dan daftar field dikosongkan: tombol Tambah, aksi Ubah/Hapus, dan formulirnya hilang.
- Rute API-nya ikut dipersempit: hanya `GET pemohon` dan `GET pemohon/{id}`. Tidak ada lagi POST/PUT/DELETE, jadi pembatasannya bukan sekadar menyembunyikan tombol.
- Alasannya ditulis di deskripsi modul: akun pemohon dibuat dan disunting sendiri oleh pengunjung lewat Registrasi Akun di situs publik.

### Langkah 56 — matrix hak akses role yang dinamis

Fondasinya sudah ada (tabel `role_modul_akses`, middleware `akses:{slug},{aksi}`, endpoint `GET/PUT role/{id}/akses`, penyaringan menu di panel), yang belum ada adalah **layar untuk mengaturnya**. Sekarang ditambahkan:

- **Dialog "Atur hak akses"** pada modul Role (`components/RoleAksesDialog.tsx`), dibuka dari menu aksi tiap baris role.
- Daftar modulnya **diambil dari API**, bukan ditulis di panel — modul baru yang ditambahkan ke `modul_sistem` otomatis muncul sebagai baris baru tanpa mengubah kode.
- Enam kolom hak per modul: **Lihat, Tambah, Ubah, Hapus, Setujui, Ekspor**. Ada centang induk per kolom (semua modul sekaligus) dan per baris (semua hak satu modul).
- Aturan bawaan yang dijaga di layar: mencentang hak tulis ikut menyalakan **Lihat**, dan mematikan **Lihat** ikut mematikan semua hak tulis — hak tulis tanpa hak lihat tidak ada gunanya karena modulnya tidak muncul di menu.
- Role `super-admin` ditampilkan terkunci (API juga menolaknya) supaya tidak ada keadaan "semua orang terkunci di luar".
- Setelah disimpan, cache menu (`me/navigation`) di-invalidasi sehingga menu dan tombol langsung menyesuaikan bila yang diubah adalah role milik pengguna yang sedang login.

**Alur lengkapnya sekarang:** buat Role → atur matriknya → buat Pengguna dan pilih rolenya. Menu samping hanya berisi modul dengan hak **Lihat**, tombol Tambah/Ubah/Hapus mengikuti hak masing-masing, dan API menolak permintaan di luar hak itu — jadi pembatasannya tidak bisa diakali dari peramban.

### Verifikasi

Diuji lewat HTTP sungguhan dengan pengguna admin sementara (`uji.akses@local.test`, **sudah dihapus setelah pengujian**):

- `GET role/2/akses` → **18 modul** (hanya yang aktif), `tautan-terkait` **tidak ikut**.
- `PUT role/2/akses` mematikan Tambah+Ubah pada modul FAQ → **200**, dibaca ulang: `create=false edit=false`. Nilainya **dikembalikan seperti semula** setelah uji (`create=true edit=true`).
- `PUT role/{super-admin}/akses` → **422** "Hak akses super-admin bersifat tetap dan tidak bisa dibatasi."
- `GET pemohon` → **200**; `POST pemohon` → **405** (rute tulisnya memang sudah tidak ada).
- Panel dibuka dengan Playwright: menu **Konten Situs** tinggal Banner, Struktur, Regulasi, FAQ (tanpa Tautan) dan **Manajemen Sistem** tinggal Pengguna, Role, Pengaturan, Audit (tanpa Modul Sistem); halaman Pemohon tanpa tombol Tambah dan tanpa kolom aksi; dialog matrix tampil berisi 18 modul dengan ringkasan "15 dari 18 modul dapat dilihat role ini."
- `php -l` bersih; `npx tsc --noEmit` + `npx vite build` (be) bersih.

---


## Status Pengerjaan (putaran 37 — langkah 52)

### Banner multi gambar + teks + animasi

- Tabel `banner_slider` ditambah `judul_en`, `ringkasan`, `ringkasan_en` (migrasi `2026_08_13_000002`); model, validasi, dan pencarian `BannerSliderController` menyesuaikan.
- Modul Banner di panel mendapat field **Judul** dan **Ringkasan**; boleh lebih dari satu banner dan tampil bergantian.
- Slider beranda menampilkan judul + ringkasan milik slide yang sedang tayang; slide tanpa judul memakai teks bawaan beranda. Blok teks tiap slide ditumpuk pada satu sel `grid` supaya tombol di bawahnya tidak melompat saat slide berganti. Slide yang punya tautan mendapat tombol teks "Selengkapnya".
- **Animasi:** silang-pudar gambar (masuk 1 detik, keluar 0,7 detik), teks menyusul naik-memudar (jeda 150 ms), dan gambar aktif merayap membesar (Ken Burns, `scale(1) → scale(1.08)` selama 8 detik; mati bila pengunjung memilih `prefers-reduced-motion`). Autoplay 7 detik, berhenti saat kursor di atas banner.

### Penyesuaian yang diminta pada putaran ini

- **Penanda nomor slide (`01 / 04`) di pojok kanan bawah dihapus.** Posisi slide sudah terbaca dari titik-titik di bawah banner.
- **Isian Judul/Ringkasan versi Inggris dilepas dari modul Banner.** Sebagai gantinya, teks banner dicocokkan ke kamus situs (`fe-ppid/lang/en.json`) lewat `__()` saat pengunjung memilih bahasa Inggris.

**Penting — tidak ada penerjemah otomatis.** Situs ini tidak memanggil layanan terjemahan mana pun. Yang berjalan adalah pencocokan ke kamus: kalimat yang sudah ada di `lang/en.json` tampil dalam Bahasa Inggris, kalimat yang belum ada **tetap tampil Bahasa Indonesia**. Jadi setiap judul/ringkasan banner baru perlu ditambahkan ke kamus itu (satu baris `"teks Indonesia": "English text"`) supaya ikut berganti bahasa. Kolom `judul_en`/`ringkasan_en` sengaja dibiarkan ada di basis data bila suatu saat isian manualnya ingin dikembalikan.

### Verifikasi

- Diuji dengan dua banner (satu banner uji tambahan sudah dihapus; tersisa dua banner milik CMS): tombol berikutnya memindahkan gambar, judul, ringkasan, dan titik aktif; penanda nomor sudah tidak ada.
- Kolom `judul_en`/`ringkasan_en` dikosongkan lebih dulu, lalu `?lang=en` tetap menampilkan "Public Information Disclosure Portal" — jalur kamus terbukti bekerja; `?lang=id` menampilkan judul Indonesianya.
- `php -l` bersih, `lang/en.json` sah; `npm run build` (fe) sukses; `npx tsc --noEmit` + `npx vite build` (be) bersih.

---


## Status Pengerjaan (putaran 36 — langkah 53)

Blok **Waktu Layanan Informasi Publik** dilepas dari halaman Profil Singkat (`resources/views/ppid/profile.blade.php`). Jam layanan sekarang hanya punya satu tempat tayang: **Standar Layanan → Jalur dan Waktu Layanan**, sehingga tidak ada dua daftar jam yang bisa berbeda isinya.

- Data `service_hours` untuk slug `singkat` ikut dihapus dari `PpidController::showProfilePage()` — tanpa itu datanya jadi data mati yang tidak pernah dipakai.
- Jarak bawah blok pengantar dilepas (`mb-12` → tanpa margin) karena sekarang blok itu isi terakhir kartu; kalau dibiarkan, kartunya menyisakan ruang kosong.
- Tautan "Jalur dan Waktu Layanan" di header dan footer tidak disentuh — itu jalan menuju halaman jam layanan yang tersisa.

### Verifikasi

- `php -l` bersih. `/profile/singkat`, `/profile/struktur`, `/profile/visi-misi`, `/profile/tugas-fungsi-wewenang`, dan `/standar-layanan/jalur-waktu-layanan` semuanya **200**.
- Halaman Profil Singkat tidak lagi memuat teks "Waktu Layanan Informasi Publik" (0 kemunculan); yang tersisa hanya tautan menu "Jalur dan Waktu Layanan" di header/footer.
- Tangkapan layar Playwright: kartu profil rapat, tanpa ruang kosong di bawah pengantar.

---


## Status Pengerjaan (putaran 34 — langkah 50)

Banner beranda sebelumnya selalu terpotong: tinggi hero dipatok `min-h-[660px]` sementara lebarnya mengikuti layar, jadi `object-cover` harus memperbesar gambar sampai memenuhi lebar dan sisa atas-bawahnya dibuang. Di layar 1920 px, banner 1440 × 550 dipotong ±90 px — persis yang terlihat pada tangkapan layar acuan (tulisan "PANGAN BERKUALITAS" terpangkas).

### Yang diubah

- **Hero mengisi satu layar penuh**, mengikuti contoh PPID JIEP: `lg:min-h-[calc(100vh-72px)]` — tinggi viewport dikurangi header sticky (72 px), dibatasi `lg:max-h-[1100px]` supaya di layar jangkung banner tidak jadi terlalu panjang. Di bawah `lg` tetap `min-h-[560px]`.
- Percobaan pertama memakai tinggi mengikuti rasio banner (`lg:aspect-[8/3]`, ±714 px di layar 1920). Gambarnya memang tampil utuh, tetapi tinggi banner terasa kurang dan masih menyisakan ruang sebelum section berikutnya — **diganti** dengan satu layar penuh.
- **Scrim diringankan**: gelap hanya di sisi kiri (zona teks) dan memudar jadi bening di kanan, plus bayangan tipis di dasar. Sebelumnya seluruh banner tertutup dua lapis gelap sehingga gambarnya kusam.
- Teks hero kembali mengalir di dalam hero (bukan `absolute`), rata tengah secara vertikal.

### Ukuran gambar banner yang dianjurkan

Sudah ditulis sebagai keterangan field **Gambar banner** di modul Banner (be-ppid), jadi petugas membacanya saat mengunggah:

- **Ideal 1920 × 1080 px (rasio 16:9)**, minimal 1600 × 900 px.
- Format **JPG/WEBP**, usahakan **di bawah 500 KB** (batas sistem 5 MB untuk jenis gambar).
- Banner mengisi satu layar penuh, jadi **tepi gambar ikut terpotong** mengikuti bentuk layar pengunjung — objek penting ditaruh di tengah, hindari teks pada 15% tepi kiri/kanan.
- **Sisi kiri juga tertutup judul dan tombol hero.**

**Perlu diganti.** Banner yang terpasang sekarang 1440 × 550 (rasio 2,62) — jauh lebih lebar dari layar penuh, jadi kiri-kanannya terpotong ±27% (tulisan "FOOD STATION" dan "BERKUALITAS" terpangkas). Ganti dengan gambar 16:9 supaya hasilnya bersih.

### Verifikasi

- Tangkapan layar headless 1920 × 1080 dan 1366 × 768: hero mengisi layar penuh sampai batas header, tanpa ruang tersisa sebelum section berikutnya.
- 768 px dan 430 px: susunan tetap benar, teks dan tombol terbaca.
- `npm run build` (fe) sukses; `npx tsc --noEmit` + `npx vite build` (be) bersih.

**Catatan — temuan di luar langkah ini.** Pada lebar ±430 px halaman menggulir mendatar sedikit (tombol hero dan kartu di `/regulasi` terpotong di tepi kanan). Diuji juga pada versi sebelum perubahan ini dan hasilnya sama, jadi ini bukan akibat langkah 50 melainkan luapan mendatar yang sudah ada di tata letak. Belum diperbaiki — perlu langkah tersendiri.

---


## Status Pengerjaan (putaran 33 — langkah 49)

Maklumat berhenti jadi teks yang ditulis di template. Sekarang isinya **satu berkas yang diunggah petugas** dan dibaca utuh di situs publik — sama seperti maklumat cetak yang ditandatangani, bukan butir-butir yang diketik ulang di CMS.

### Backend (api-ppid) — modul unggah dokumen

- Tabel baru `maklumat`: `judul`/`judul_en`, `ringkasan`/`ringkasan_en` (pengantar, opsional), `file_dokumen`, `tanggal_terbit`, `status`, `published_by`. Migrasi `2026_08_13_000001_create_maklumat_table.php`, batasan status disamakan dengan tabel konten lain (`draft`/`published`/`archived`).
- `MaklumatController` (turunan `CrudController`) + rute CRUD `api/v1/maklumat`. **Berkas wajib diisi saat membuat** — tanpa berkas modul ini kehilangan artinya. Saat status jadi Terbit, `published_by` dan `tanggal_terbit` diisi otomatis bila kosong.
- Hak aksesnya **menumpang modul Halaman Statis** (`akses:halaman-statis,…`), mengikuti pola `pemohon`/`survey-kepuasan`. Tidak ada modul baru di matrix role, jadi tidak perlu menyeed ulang hak akses.
- Folder unggahan `maklumat` ditambahkan ke daftar putih `UploadController`; jenisnya `dokumen_gambar` (PDF/JPG/PNG/WEBP, maks 20 MB).
- Barisnya boleh lebih dari satu supaya maklumat lama tetap tersimpan sebagai arsip; situs memakai satu baris `published` dengan tanggal terbit terbaru.

### Panel (be-ppid)

Modul **Maklumat** muncul di grup *Standar Layanan*, di atas Halaman. Formulirnya: judul (+English), dokumen (wajib), tanggal terbit, status, pengantar (+English). Kolom daftar memperlihatkan tanggal terbit, status, pengunggah, dan tautan berkas.

### Situs publik (fe-ppid)

Halaman `/standar-layanan/maklumat-pelayanan` menarik maklumat terbit dari database, lalu:

- **PDF digambar halaman demi halaman lewat pdf.js** (`data-pdf-dokumen`), persis mekanisme halaman detail Regulasi — bukan `<iframe>`, karena pembaca PDF bawaan tidak ada di semua ponsel.
- **Gambar (PNG/JPG/WEBP) ditampilkan langsung** sebagai `<img>`.
- Di atas dokumen ada tanggal terbit, nama pengunggah, tombol **Buka di tab baru** dan **Unduh Maklumat**.
- Bila belum ada maklumat terbit atau database tidak terjangkau, halaman **kembali memakai teks bawaan** yang lama dan menampilkan pemberitahuan `db_notice` — halaman tidak pernah kosong.

Judul dan pengantar mengikuti pengalih bahasa lewat trait `PunyaVersiInggris` (kolom `*_en`; kosong = teks Indonesia dipakai).

### Data awal

`MaklumatAwalSeeder` menyalin `MAKLUMAT PPID.png` ke disk `media` seperti hasil unggahan biasa lalu menerbitkannya, jadi halaman langsung menayangkan dokumen aslinya. Idempoten — berhenti bila sudah ada maklumat terbit.

```
php artisan migrate
php artisan db:seed --class=MaklumatAwalSeeder
```

### Verifikasi

- `php -l` bersih untuk seluruh berkas PHP baru/berubah; `npx tsc --noEmit` bersih di be-ppid.
- Migrasi dan seeder dijalankan: baris terbit `uploads/maklumat/2026/08/….png`.
- `/standar-layanan/maklumat-pelayanan` **200**, URL dokumen muncul di halaman; `?lang=en` **200** dengan judul dan tombol versi Inggris.
- `route:list` menampilkan tujuh rute `api/v1/maklumat`.

**Catatan.** `@php(...)` bentuk satu baris tidak dikompilasi utuh oleh Blade di versi ini (tag `<?php` dibuka tanpa penutup, sisa berkas berhenti dikompilasi). Dipakai bentuk blok `@php … @endphp`.

**Catatan.** Verifikasi panel masih statis (typecheck); modul Maklumat belum dibuka lewat browser.

---


## Status Pengerjaan (putaran 32 — langkah 48)

Susunan Dashboard diurutkan ulang persis seperti yang diminta:

1. **Kartu beban kerja** — Total permohonan, Menunggu persetujuan, Lewat batas waktu, Keberatan belum selesai
2. **Kartu konten** — Informasi Publik, Berita, Kepuasan Pemohon *(naik ke atas; baris kembarnya di bagian bawah halaman dihapus)*
3. **Perlu tindakan segera** — **disorot**
4. **Kepatuhan SLA**
5. **Capaian KPI**
6. **Permohonan masuk vs ditanggapi** (2 kolom) berdampingan dengan **Sebaran status** dan **Kategori paling diminta** (1 kolom)

### Sorotan pada "Perlu tindakan segera"

Kartunya diberi bingkai tebal dan latar tipis yang **warnanya mengikuti keadaan**: merah bila ada permohonan mendesak (plus ikon sirene dan jumlahnya sebagai chip), hijau bila bersih. Warnanya diambil dari palet tema, jadi ikut menyesuaikan mode terang/gelap.

### Tren bulanan + pembanding tahun

Bentuknya berubah dari "12 bulan terakhir" jadi **ringkasan Januari–Desember**, supaya bulan yang sama antar tahun berdiri sejajar dan bisa dibandingkan.

- Tahun utama mengikuti penyaring tahun; tanpa penyaring dipakai tahun berjalan.
- Ditambah **maksimal 2 tahun sebelumnya** sebagai pembanding (total 3 tahun) — hanya tahun yang benar-benar punya data yang ditarik.
- Tiap bulan menampilkan satu batang per tahun; tahun utama digambar tebal berwarna, tahun pembanding lebih tipis dan diredupkan. Di kanannya angka `masuk/ditanggapi`.
- **Skala batang disamakan lintas tahun** — kalau tiap tahun dinormalkan ke lebarnya sendiri, tahun sepi akan terlihat sama ramainya dengan tahun ramai.
- Di atas grafik ada chip total setahun per tahun pembanding.

Sisi API: `analisa.tren` berubah dari daftar datar jadi objek `{ tahun_utama, tahun_dibanding, bulanan, total }`.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` bersih; `npx vite build` sukses; `/ppid/dashboard` 200 di dev server panel.
- **Perbandingan tahun diuji dengan tiga baris permohonan sementara** (dua di 2025, satu di 2024): `tahun_dibanding` terbaca `[2026, 2025, 2024]`, total per tahun `2026: 5/2, 2025: 2/2, 2024: 1/1`, dan baris Maret benar menempatkan angkanya pada kolom 2025. Saat penyaring diarahkan ke 2025, pembandingnya menyusut jadi `[2025, 2024]` — tahun setelahnya memang tidak ikut dibandingkan. **Ketiga baris ujinya sudah dihapus**; jumlah permohonan kembali 5.
- Kamus i18n +6 kunci (355) — cakupan 354 string UI, sisa 3 (`FAQ`, `ID`, `IP`) memang sama dalam Bahasa Inggris.

**Catatan.** `tanggal_permohonan` bukan kolom `fillable` pada `PermohonanInformasi` — nilainya diisi database. Ini ketahuan saat menyiapkan baris uji (tanggal yang saya kirim diabaikan, barisnya jatuh ke tahun berjalan). Bukan bug: memang seharusnya server yang menentukan kapan permohonan masuk. Dicatat di sini supaya tidak salah paham kalau nanti perlu membuat data uji lagi.

**Catatan.** Verifikasi panel masih statis (typecheck, build, uji controller, rute); belum dibuka lewat browser.

---


## Status Pengerjaan (putaran 31 — langkah 47)

Halaman **Analitik & SLA** yang dibuat pada putaran 30 dilebur ke **Dashboard**. Sekarang hanya ada satu halaman gambaran umum di panel, tetapi isinya sudah lengkap dengan analisa.

### Susunan Dashboard sekarang

Dari "apa keadaannya" sampai "apa yang harus dikerjakan":

1. **Ringkasan** — total permohonan (+ berapa menunggu tindakan), menunggu persetujuan, lewat batas waktu, keberatan belum selesai.
2. **Kepatuhan SLA** — persentase kepatuhan, tepat waktu, telat dijawab, lewat batas belum dijawab, mendekati batas, plus kepatuhan tanggapan keberatan.
3. **Capaian KPI** — 5 indikator beserta target, realisasi, dasar perhitungan, dan tanda Tercapai / Belum tercapai.
4. **Analisa** — tren 12 bulan (masuk vs ditanggapi), sebaran status, kategori paling diminta.
5. **Perlu tindakan segera** — 10 permohonan paling mendesak lengkap dengan hitungan telat/sisa hari.
6. **Kondisi konten** — informasi publik, berita, kepuasan pemohon.

Penyaring tahun ikut pindah, jadi seluruh halaman bisa dilihat per tahun.

### Yang berubah di kode

- **Satu endpoint untuk satu halaman.** `GET /v1/dashboard/analitik` diperluas: `ringkasan` menambah `perlu_tindakan`, `menunggu_approval`, dan `keberatan_belum_selesai`; ditambah blok `konten` (informasi publik & berita). Dashboard kini memanggil endpoint ini saja — sebelumnya dua halaman memanggil dua endpoint dan berisiko menampilkan angka yang berbeda untuk hal yang sama.
- Jumlah konten sengaja **tidak** ikut disaring tahun: yang relevan adalah keadaan pustaka informasi sekarang, bukan per periode.
- `PpidAnalitik.tsx` dihapus; isinya masuk `PpidDashboard.tsx`.
- **Alamat lama `/ppid/analitik` tetap hidup** sebagai pengalihan ke `/ppid/dashboard`, supaya tautan atau bookmark yang sudah tersebar tidak mati.
- Entri menu "Analitik & SLA" dilepas — menu panel kembali hanya punya satu item gambaran umum: Dashboard.
- Endpoint lama `GET /v1/dashboard/ringkasan` **dibiarkan ada** (tidak lagi dipakai panel) supaya integrasi lain yang mungkin memakainya tidak ikut putus. Bilang saja kalau mau dihapus.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` bersih; `npx vite build` sukses.
- Respons endpoint diperiksa: delapan blok (`tahun`, `tahun_tersedia`, `ringkasan`, `konten`, `sla`, `analisa`, `kpi`, `tindakan`), 5 indikator KPI, tren 12 bulan.
- `/ppid/dashboard` dan `/ppid/analitik` sama-sama dilayani dev server panel (200); `navigation.ts` sudah tidak memuat entri analitik.
- Kamus i18n dirapikan: 4 kunci yang tidak lagi terpakai dilepas, jadi **349 kunci untuk 349 string UI** — sisa 3 (`FAQ`, `ID`, `IP`) memang sama dalam Bahasa Inggris.

**Catatan.** Sama seperti putaran sebelumnya, verifikasi panel masih statis (typecheck, build, uji controller, rute). Saya belum login ke panel lewat browser untuk melihat Dashboard gabungan ini ter-render.

---


## Status Pengerjaan (putaran 30 — langkah 44, 45, 46)

### Langkah 44 — ukuran ikon bendera

Penyebabnya sederhana: `<img>` bendera di `LanguageSwitcher` hanya diberi `min-w-5`, tanpa lebar maupun tinggi — jadi SVG-nya digambar sebesar ukuran bawaannya. Sekarang ukurannya dikunci `h-4 w-6` (plus sudut membulat dan garis tepi tipis supaya bendera putih tetap terlihat batasnya), dan atribut `width`/`height` pada `ID.svg` serta `GB.svg` dilepas agar tidak melawan kelas CSS-nya.

### Langkah 45 — halaman Analitik & SLA

Halaman baru **`/ppid/analitik`** di panel, sumber datanya endpoint baru `GET /v1/dashboard/analitik` (`AnalitikController`, hak akses `dashboard,view`). Ada penyaring tahun; tahun yang ditawarkan diambil dari tahun yang benar-benar punya data.

| Bagian | Isi |
|---|---|
| **Ringkasan** | permohonan, selesai, sedang berjalan, keberatan, rata-rata waktu tanggapan, kepuasan + jumlah responden |
| **SLA** | persentase kepatuhan, tepat waktu, telat dijawab, lewat batas & belum dijawab, mendekati batas (≤3 hari), plus kepatuhan tanggapan keberatan |
| **KPI** | 5 indikator: kepatuhan SLA ≥90%, rata-rata tanggapan ≤10 hari, permohonan tuntas ≥85%, kepuasan ≥80%, rasio keberatan ≤5%. Tiap indikator memuat dasar perhitungannya dan ditandai Tercapai / Belum tercapai |
| **Analisa** | tren 12 bulan (masuk vs ditanggapi), sebaran status, kategori paling diminta, cara pengiriman |
| **Tindakan** | 10 permohonan paling mendesak: kode, pemohon, status, batas waktu, dan berapa hari telat atau tersisa |

Acuan SLA-nya UU No. 14 Tahun 2008: tanggapan 10 hari kerja (+7 perpanjangan), keberatan 30 hari kerja. Target KPI dikumpulkan dalam satu konstanta `TARGET_KPI` supaya gampang disesuaikan bila manajemen menetapkan angka lain.

Dua keputusan yang perlu diketahui:

- **Yang dinilai hanya baris yang punya `batas_waktu_tanggapan`.** Baris lama tanpa batas waktu tidak ikut dihitung, supaya persentase kepatuhannya tidak menyesatkan.
- **Permohonan yang belum jatuh tempo tidak dihitung sebagai patuh maupun langgar** — ia baru masuk hitungan setelah dijawab atau setelah batas waktunya lewat.

### Langkah 46 — modul pengelolaan vs manajemen sistem

**Modul baru: Modul Sistem.** Tabel `modul_sistem` adalah dasar seluruh matrix hak akses, tetapi selama ini tidak ada modul CMS-nya — barisnya hanya bisa diubah lewat database. Sekarang ada `ModulSistemController` di api-ppid (hak aksesnya ikut `pengguna`, sama seperti Role) dan modul **Modul Sistem** di panel. Field slug diberi peringatan tegas: slug adalah kunci pemeriksaan hak akses, mengubahnya membuat akses lama tidak lagi cocok.

**Grup menu dirapikan.** Grup `Administrasi Sistem` → **Manajemen Sistem**, isinya Pengguna, Role, Modul Sistem, Pengaturan, Audit.

**Dua modul dikeluarkan dari menu karena isinya tidak tayang di situs publik** — modul, API, dan datanya tidak dihapus, hanya dilepas dari menu:

| Modul | Alasan |
|---|---|
| **Galeri** | modul Galeri dihapus dari situs pada langkah 9, jadi isinya tidak punya halaman |
| **Navigasi** | menu situs publik masih ditulis di template (`layouts/header.blade.php`), belum dibaca dari tabel `menu_navigasi` |

Ini temuan audit, bukan permintaan: keduanya sebelumnya bisa disunting operator padahal hasilnya tidak pernah muncul di situs. Kalau memang ingin dipakai, Navigasi perlu dikerjakan lebih dulu di fe-ppid (header dibaca dari CMS) — bilang saja.

**Hasil audit modul konten lainnya — semuanya sudah tayang:**

| Modul | Tempat tayang di fe-ppid |
|---|---|
| Kategori Informasi | menu Informasi Publik + `/informasi/{slug}` |
| Informasi Publik | `/informasi` |
| Informasi Dikecualikan | `/informasi/dikecualikan` |
| Berita, Kategori Berita | `/berita`, kartu berita di beranda |
| Regulasi | `/regulasi` + halaman detail |
| Halaman | `/profile/{slug}`, `/standar-layanan/{slug}` |
| FAQ | `/faq` + ringkasan di beranda |
| Struktur | `/struktur-ppid` |
| Banner | hero beranda |
| Tautan | kolom tautan di footer |
| Laporan Statistik / Laporan Pelayanan | `/laporan/statistik-informasi`, `/laporan/pelayanan-informasi` |
| Permohonan, Keberatan, Pemohon, Survei | Portal Pengguna (`/akun/...`) |
| Pengaturan | dibaca `App\Support\Cms` untuk isian situs |

**Nama modul di database disamakan.** Nama pada `modul_sistem` juga tampil di matrix hak akses Role, jadi kalau tidak ikut dipendekkan satu modul terbaca dua nama berbeda. `PenamaanModulSeeder` (**sudah dijalankan**, 12 baris) menyesuaikannya — mis. `Regulasi & Dasar Hukum` → `Regulasi`, `Pengguna & Role` → `Pengguna, Role & Modul`. **Slug tidak disentuh** karena slug adalah kunci hak akses.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` bersih; `npx vite build` sukses. Kamus i18n bertambah jadi **354 entri**; pemeriksaan cakupan: 356 string UI, sisa 3 (`FAQ`, `ID`, `IP`) memang sama dalam Bahasa Inggris.
- **Perhitungan SLA diuji dengan empat baris permohonan sementara** (satu dijawab tepat waktu, satu dijawab telat, satu lewat batas belum dijawab, satu tinggal 2 hari): hasilnya `dinilai 3, tepat 1, telat dijawab 1, lewat batas 1, mendekati batas 1, kepatuhan 33,3%`, KPI kepatuhan otomatis bertanda "belum tercapai", dan daftar tindakan memuat dua baris dengan hitungan `telat 1 hari` serta `2 hari lagi`. **Keempat baris ujinya sudah dihapus** — jumlah permohonan kembali 5 seperti semula.
- Rute `GET /v1/dashboard/analitik` dan tujuh rute `modul-sistem` terdaftar; halaman `/ppid/analitik` dilayani dev server panel (200).
- 9 rute publik fe-ppid dicek pada dua locale, semuanya 200.

**Catatan.** Verifikasi panel masih statis (typecheck, build, uji controller lewat tinker, rute). Saya belum membuka panel di browser dan login untuk melihat halaman Analitik ter-render.

---


## Status Pengerjaan (putaran 29 — langkah 41 lanjutan, 42, 43)

### Langkah 41 (lanjutan) — modul unggah di be-ppid

Sisi situs publiknya sudah selesai pada putaran 28. Yang ditambahkan sekarang: **modul khusus untuk mengunggah berkasnya**.

Modul gabungan **Laporan Layanan** dipecah jadi dua, karena isinya beda jenis — yang satu rekap angka, yang satu berkas per tahun:

| Modul | Isi formulir |
|---|---|
| **Laporan Statistik** | judul, tahun, periode, status, enam angka rekap + tombol Hitung otomatis, ringkasan, berkas (opsional) |
| **Laporan Pelayanan** | judul, tahun, periode, status, ringkasan, **berkas laporan** — tanpa satu pun field angka |

Keduanya tetap memakai satu tabel (`laporan_layanan`) dan satu hak akses (`laporan-layanan`); pembedanya kolom `tipe_laporan`. Supaya operator tidak perlu memilih tipe — dan tidak bisa salah pilih — ditambahkan kemampuan baru `nilaiTetap` pada `ResourceConfig`: nilainya ikut sebagai **filter** saat memuat daftar dan ikut **dikirim** saat menyimpan. Dipasang di `ResourceListPage` dan `ResourceFormDialog`, jadi modul lain bisa memakai pola yang sama nanti.

Modul Laporan Pelayanan menampilkan kolom **Diunggah oleh** dan **Tanggal publikasi**, dan berkasnya dibatasi PDF/gambar dengan keterangan bahwa halaman pertamanya jadi sampul di situs publik.

### Langkah 42 — nama modul dipendekkan

14 judul modul dipangkas supaya tidak terpotong di menu samping:

`Daftar Informasi Publik` → **Informasi Publik** · `Daftar Informasi Dikecualikan` → **Informasi Dikecualikan** · `Permohonan Informasi Publik` → **Permohonan** · `Pengajuan Keberatan Informasi Publik` → **Keberatan** · `Data Pemohon` → **Pemohon** · `Survei Kepuasan` → **Survei** · `Banner Slider` → **Banner** · `Struktur Organisasi` → **Struktur** · `Halaman Statis` → **Halaman** · `Tautan Terkait` → **Tautan** · `Menu Navigasi` → **Navigasi** · `Role & Hak Akses` → **Role** · `Pengaturan Situs` → **Pengaturan** · `Audit Log` → **Audit**

Yang diubah hanya labelnya. Slug URL, slug modul, dan hak aksesnya tidak disentuh, jadi tautan lama dan matrix hak akses tetap berlaku. Nama panjangnya tetap terbaca dari keterangan di bawah judul halaman.

### Langkah 43 — pengalih bahasa panel

Penyebabnya: i18n di be-ppid masih bawaan template — hanya berisi satu kunci contoh (`Welcome to React`), bahasanya English/Turki/Arab, dan Bahasa Indonesia tidak ada sama sekali. Jadi tombolnya berpindah tetapi tidak ada yang berubah, dan bendera `ID`/`GB` pun tidak ada berkasnya.

Yang dikerjakan:

- **Kamus** `src/@i18n/kamusPpid.ts` — **310 entri**, kuncinya teks Bahasa Indonesia apa adanya (pola yang sama dengan `lang/en.json` di fe-ppid). Akibatnya mode Indonesia tidak butuh kamus: label baru yang belum diterjemahkan tetap tampil dalam Bahasa Indonesia, bukan hilang atau berubah jadi kode.
- **`i18n.ts`** — bahasa `id` (bawaan) dan `en`, `fallbackLng: 'id'`, serta `keySeparator`/`nsSeparator` dimatikan; tanpa itu kunci yang memuat titik atau titik dua akan terpotong i18next.
- **Pilihan bahasa bertahan** setelah halaman dimuat ulang (`localStorage`).
- **`I18nProvider`** — daftar bahasa jadi Indonesia + English. Turki dan Arab dilepas karena tidak ada terjemahannya.
- **Bendera** `public/assets/images/flags/ID.svg` dan `GB.svg` ditambahkan (sebelumnya hanya US/TR/SA, sehingga ikonnya kosong).
- **Penerjemahan dipasang** di menu samping (`navigation.ts` + `PpidNavigationSync`, menu ditulis ulang saat bahasa berganti), halaman daftar, dialog formulir, komponen field, komponen unggah, dan Dashboard: judul & keterangan modul, label kolom, label & bantuan field, pilihan dropdown, badge status, tombol, dan seluruh pesan konfirmasi/notifikasi.

### Verifikasi

- `npx tsc --noEmit` bersih; `npx vite build` sukses.
- Pemeriksaan cakupan kamus: **313 string UI, 310 sudah ada padanannya**. Tiga sisanya `FAQ`, `ID`, `IP` — memang sama dalam Bahasa Inggris.
- Berkas bendera dilayani dev server: `/assets/images/flags/ID.svg` dan `GB.svg` sama-sama 200.
- Registry modul terbaca 24 modul (dari 23) setelah Laporan Layanan dipecah dua; menu grup Layanan memuat keduanya.

**Catatan.** `npm run build` di be-ppid gagal di Windows — skripnya memakai sintaks env Unix (`NODE_OPTIONS=… tsc && vite build`), sehingga muncul `'NODE_OPTIONS' is not recognized as an internal or external command`. Ini sudah ada sebelum perubahan ini. Saya membangun lewat `npx tsc --noEmit` + `npx vite build`. Kalau mau, skripnya bisa dibuat lintas-platform dengan `cross-env`.

---


## Status Pengerjaan (putaran 28 — langkah 41)

`/laporan/pelayanan-informasi` tidak lagi memakai tampilan tabel angka. Isinya sekarang **berkas laporan per tahun** yang diunggah petugas, disajikan persis seperti modul Regulasi.

**Laporan Statistik Informasi Publik (`/laporan/statistik-informasi`) tidak diubah** — halaman itu memang berisi rekap angka, jadi tetap memakai view lamanya (`ppid/report.blade.php`). Percabangannya ada di `PpidController@showReportPage`.

### Daftar (`/laporan/pelayanan-informasi`)

View baru `ppid/service_report.blade.php`, isi kartunya sejajar dengan kartu Regulasi:

- **Sampul halaman pertama dokumen** — PDF digambar ke `<canvas>` lewat `resources/js/sampul-pdf.js` (pdf.js), gambar ditampilkan apa adanya; selama belum tergambar atau bila berkasnya belum ada, dipakai `partials/regulasi_sampul_cadangan`.
- Badge **Tahun** dan **Periode**, tanggal + jam publikasi, judul, ringkasan, lalu baris **"Diunggah oleh <nama petugas>"** berikon logo perusahaan.
- Seluruh kartu dapat diklik menuju halaman detail (`after:inset-0`), tombol **Lihat** menuju halaman yang sama. Ada pencarian judul/tahun dan penghitung jumlah dokumen.
- Urutan: tahun terbaru lebih dulu.

### Halaman detail (`/laporan/pelayanan-informasi/{id}`)

Rute baru `ppid.report.show`, view `ppid/service_report_show.blade.php`.

- Dokumen **dibaca di halaman itu juga** (pdf.js, `data-pdf-dokumen`), tidak membuka tab baru dan tidak memaksa unduh.
- Bagian atas memuat tahun, periode, tanggal + jam publikasi, judul, dan pengunggah; di bawahnya ringkasan lalu dokumennya.
- Section **"Laporan Lainnya"** berisi hingga 6 laporan pelayanan lain lengkap dengan sampulnya.
- Rutenya didaftarkan **sebelum** `/laporan/{slug}` supaya tidak tertelan rute daftar; id yang bukan laporan pelayanan (mis. baris statistik) menghasilkan 404.

### Pengunggah

Tidak ada kolom baru. Namanya diambil dari `laporan_layanan.published_by` yang **sudah diisi server dari token** di `LaporanLayananController@beforeSave` saat laporan diterbitkan. Di fe-ppid ditambahkan relasi `LaporanLayanan::penerbit()`; bila kosong (baris lama), yang tampil "Petugas PPID".

### Sisi be-ppid

- Berkas laporan kini **hanya PDF atau gambar** — jenis unggahan `dokumen` → `dokumen_gambar` (validasi ekstensi dan MIME-nya sudah ada di `UploadController` sejak langkah 34), plus keterangan bahwa halaman pertamanya dipakai sebagai sampul.
- Tabel modulnya dapat kolom **Diunggah oleh** dan **Tanggal publikasi**.
- Keterangan field Status diperbarui: kedua halaman laporan di situs publik hanya menampilkan yang berstatus Terbit.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` di be-ppid tanpa error; `npm run build` sukses. `lang/en.json` +9 kunci (705).
- Diuji dengan **dua baris laporan sementara** yang ditautkan ke PDF asli: daftar menampilkan 3 kartu, 2 di antaranya memasang `data-pdf-cover` ke berkasnya, 3 baris "Diunggah oleh", dan tautan detail per kartu. Halaman detail memuat `data-pdf-dokumen` serta 2 kartu "Laporan Lainnya". **Baris ujinya sudah dihapus** — tersisa 1 laporan pelayanan asli seperti semula.
- `/laporan/pelayanan-informasi/99999` → 404; `/laporan/pelayanan-informasi/1` (baris statistik) → 404; `/laporan/statistik-informasi` tetap 200 dengan tampilan lamanya.
- 8 rute dicek pada dua locale, semuanya 200.

**Catatan.** Satu-satunya laporan pelayanan yang ada sekarang (`Laporan Pelayanan Informasi Publik Tahun 2025`) **belum punya berkas**, jadi kartunya memakai sampul cadangan dan halaman detailnya menampilkan "Berkas dokumen belum tersedia." Unggah PDF-nya lewat be-ppid → modul Laporan Layanan, dan sampul serta isinya langsung muncul tanpa perubahan kode.

---


## Status Pengerjaan (putaran 27 — langkah 40)

Sub modul **Register Permohonan Informasi** pada menu Layanan diganti jadi pintu **Registrasi Akun**, menuju halaman daftar akun pengunjung yang sudah ada (`/akun/daftar`).

- Diubah di tiga tempat: dropdown Layanan pada header desktop, menu Layanan pada tampilan ponsel, dan kolom Layanan di footer.
- Entrinya **hanya tampil untuk pengunjung yang belum masuk** (`@guest('pemohon')`) — yang sudah punya akun tidak diberi tautan mendaftar lagi; menu Akun Saya/Keluar yang tampil.
- Penanda menu aktif (`$isRegister`) ikut pindah dari `ppid.register` ke `akun.register`, jadi menu Layanan tetap ter-highlight saat halaman pendaftaran dibuka.
- Kunci terjemahan baru `Registrasi Akun` → `Account Registration` (`lang/en.json` jadi 696 kunci).

**Halaman register publik tidak dihapus.** Rute `/register-permohonan` beserta controller dan view-nya dibiarkan hidup dan tetap bisa diakses langsung — yang dilakukan hanya melepasnya dari menu. Alasannya register permohonan termasuk yang diminta Perki 1/2021 sebagai bukti pelayanan; kalau memang ingin dihapus total (rute + controller + view + kunci terjemahannya), bilang saja.

### Verifikasi

- `npm run build` sukses; 9 rute dicek pada dua locale, semuanya 200.
- Sebagai tamu: **3 tautan "Registrasi Akun"** (header, ponsel, footer) menuju `/akun/daftar`; **nol** sisa "Register Permohonan Informasi" di menu. Mode EN menampilkan "Account Registration" di ketiga tempat.
- Sebagai pengguna yang sudah masuk: **nol** entri "Registrasi Akun", dan menu akun (Akun Saya/Keluar) muncul. Diuji dengan akun sementara yang **sudah dihapus** setelah pengujian.
- `/register-permohonan` tetap 200 walau tidak lagi ada di menu.

**Dua temuan sampingan** (bukan bagian langkah ini, belum saya ubah):

1. Akun contoh `pemohon.demo@foodstation.co.id` **tidak bisa dipakai lagi dengan password yang tercatat di langkah 18** — login menolak dengan "Email atau password tidak cocok.". Kemungkinan passwordnya sudah diganti. Bilang kalau mau saya setel ulang.
2. api-ppid dan fe-ppid memakai **driver hash yang berbeda**. Akun yang password-nya dibuat dari sisi api-ppid gagal dipakai login di fe-ppid dengan galat `This password does not use the Bcrypt algorithm.` Selama pendaftaran hanya lewat fe-ppid ini tidak terasa, tetapi akan menggigit kalau nanti ada akun pemohon yang dibuat atau di-reset dari panel/API. Perlu disamakan (`config/hashing.php` di kedua sisi) — bilang kalau mau saya kerjakan.

---


## Status Pengerjaan (putaran 26 — langkah 39)

### Penyajian disamakan dengan Daftar Informasi Publik

`/informasi/dikecualikan` sebelumnya berupa tumpukan kartu besar satu per entri. Sekarang bentuknya sama persis dengan `/informasi`:

- **Kartu ringkasan di atas daftar yang sekaligus menyaring** — sekali klik, daftar di bawah ikut tersaring tanpa memuat ulang halaman. Daftar ini tidak punya klasifikasi seperti Informasi Publik, jadi yang dikelompokkan adalah ketersediaan surat penetapan: Semua Entri / Ada Surat Penetapan / Belum Ada Surat Penetapan.
- **Tabel pada layar sedang ke atas** (No., Informasi + ringkasan, Surat Penetapan) dan **kartu di ponsel**, jadi tidak ada tabel yang harus digeser ke samping.
- **Pencarian judul** dan penghitung jumlah entri tetap ada, posisinya disamakan dengan halaman Informasi Publik.
- Tombol aksinya dipisah jadi `partials/surat_penetapan_aksi.blade.php`, sejajar dengan `partials/informasi_aksi` milik Informasi Publik: ada berkasnya → tombol **Surat Penetapan**; belum ada → keterangan **Belum tersedia**, bukan tombol mati.

Hitungan kartunya disiapkan di `PpidController@showExcludedInformation` (`$data['kelompok']`), bukan dihitung di view.

### Dua label dihapus

- **"Entri yang dokumennya belum tersedia untuk diunduh tetap dapat dimohonkan melalui menu Permohonan Informasi Publik."** — blok catatan di bawah daftar `ppid/information_index.blade.php` dihapus seluruhnya.
- **"Catatan Penting — Pengecualian informasi ditetapkan melalui uji konsekuensi sesuai Pasal 17 UU No. 14 Tahun 2008 …"** — blok catatan hijau di bawah daftar `ppid/excluded_information.blade.php` dihapus seluruhnya.

Ketiga kunci terjemahannya ikut dilepas dari `lang/en.json` karena tidak dipakai lagi; tiga kunci baru (`Semua Entri`, `Ada Surat Penetapan`, `Belum Ada Surat Penetapan`) menggantikannya — total tetap 695.

### Verifikasi

- `php -l` bersih; `npm run build` sukses.
- `/informasi/dikecualikan` 200 dengan **22 baris tabel**; HTML-nya **nol kemunculan** "Catatan Penting" maupun "uji konsekuensi sesuai Pasal 17".
- `/informasi` 200; **nol kemunculan** "Entri yang dokumennya belum".
- Kartu ringkasan terbaca 22 / 0 / 22; mode EN memakai label Inggris (`All Entries`, `Determination Letter Available`, `No Determination Letter Yet`).
- 11 rute publik dicek pada kedua locale, semuanya 200; pemindaian sisa teks Indonesia pada mode EN tetap nol.

**Catatan.** Kartu "Ada Surat Penetapan" saat ini bernilai **0** karena belum satu pun entri dikecualikan yang berkas surat penetapannya diunggah lewat be-ppid. Penyaringnya sudah jalan dan angkanya akan ikut naik begitu berkasnya diunggah.

---


## Status Pengerjaan (putaran 23 — langkah 37)

### Situs publik

Keempat keterangan penetapan — **Alasan Pengecualian, Dasar Hukum, Jangka Waktu, Tanggal Penetapan** — dihapus dari halaman `/informasi/dikecualikan`. Kartunya kini hanya memuat nomor urut, judul, ringkasan (bila ada), dan tombol Surat Penetapan bila berkasnya diunggah. Datanya juga **tidak lagi dikirim dari controller ke view**, bukan sekadar disembunyikan dengan CSS.

Teks pengantar halaman ikut disesuaikan karena kalimat lamanya menjanjikan "lengkap dengan alasan pengecualian, dasar hukum, dan jangka waktu".

### Sisi back end

- Migrasi `2026_08_11_000002_make_alasan_pengecualian_nullable.php` (**sudah dijalankan**) melepas `NOT NULL` pada `informasi_dikecualikan.alasan_pengecualian`. Kolomnya tetap ada — isian lama tidak hilang dan masih bisa dipakai untuk arsip internal.
- Validasi API: `alasan_pengecualian` dari `required` menjadi `nullable`. Tiga field lainnya memang sudah opsional sejak awal.
- Modul be-ppid: tanda wajib pada Alasan pengecualian dilepas, ditambah keterangan bahwa keterangan penetapan tidak tampil di situs publik dan hanya diisi bila diperlukan.
- `DaftarInformasiDikecualikanSeeder` tidak lagi mengisi alasan/dasar hukum dengan teks pengganti, dan **teks pengganti yang sempat terisi di 22 baris hasil seeding sudah dikosongkan**. Dua entri contoh lama (`Dokumen Kontrak Pengadaan Beras…`, `Data Pribadi Pemohon…`) sengaja dibiarkan apa adanya karena isinya bukan buatan seeder.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` di be-ppid tanpa error.
- `/informasi/dikecualikan` 200 dan HTML-nya **nol kemunculan** "Alasan Pengecualian", "Dasar Hukum", "Jangka Waktu", maupun "Tanggal Penetapan"; 22 entri tetap tampil.
- Aturan validasi dicek langsung: `alasan_pengecualian => nullable,string`, dan penyimpanan baris tanpa keterangan penetapan berhasil (baris ujinya sudah dihapus).
- `/informasi`, `/regulasi`, dan beranda tetap 200.

---

## Status Pengerjaan (putaran 22 — langkah 36)

### Isi kedua daftar diambil dari dokumen resmi

Teks kedua PDF acuan diekstrak dengan `pdftotext -layout` lalu dituang jadi dua seeder idempoten di api-ppid (**keduanya sudah dijalankan**):

| Seeder | Isi |
|---|---|
| `DaftarInformasiPublikSeeder` | 17 entri Daftar Informasi Publik 2026 — 7 Berkala, 5 Serta Merta, 5 Tersedia Setiap Saat, lengkap dengan nomor urut dokumen (`nomor_klasifikasi`) dan ringkasan singkat |
| `DaftarInformasiDikecualikanSeeder` | 22 entri Daftar Informasi Dikecualikan 2026 |

Keduanya dicocokkan lewat `judul`. **Data contoh lama tidak dihapus**, hanya diturunkan jadi `draft` (7 entri informasi publik, 2 entri dikecualikan) sehingga hilang dari situs publik tetapi bisa dikembalikan kapan saja dari be-ppid.

Dokumen acuan Informasi Dikecualikan hanya memuat judul, sedangkan tabelnya mewajibkan alasan pengecualian. Karena itu **alasan dan dasar hukum diisi rumusan umum Pasal 17 UU No. 14 Tahun 2008 sebagai titik awal** — bukan hasil uji konsekuensi per dokumen. Silakan lengkapi per entri lewat be-ppid; seeder tidak menimpa isian yang sudah disunting.

### Tampilan situs publik

**Daftar Informasi Publik (`/informasi`)**

- Empat kartu ringkasan di atas daftar: total entri + jumlah per klasifikasi. Kartunya sekaligus **penyaring** — sekali klik daftar di bawah ikut tersaring, tanpa memuat ulang halaman.
- Daftar tampil **tabel pada layar sedang ke atas** (No., Informasi + ringkasan, Klasifikasi, Dokumen) dan berubah jadi **kartu di ponsel**, jadi tidak ada tabel yang harus digeser ke samping.
- Urutannya mengikuti dokumen resmi: per klasifikasi, lalu nomor urut dokumen (`nomor_klasifikasi` dibandingkan sebagai angka lewat `regexp_replace`).
- Badge klasifikasi menautkan ke halaman kategorinya. Entri yang berkasnya sudah ada memberi tombol **Lihat/Selengkapnya**; yang belum ada berkasnya mengarahkan ke **Permohonan Informasi Publik** (tombol "Mohon Dokumen") — jadi baris tanpa dokumen tetap berguna, bukan jalan buntu.

**Daftar Informasi Dikecualikan (`/informasi/dikecualikan`)**

- Kartu per entri (nomor, judul, ringkasan) dengan empat keterangan: alasan pengecualian, dasar hukum, jangka waktu, tanggal penetapan — plus tombol surat penetapan bila berkasnya diunggah.
- Ditambah **pencarian judul** dan penghitung jumlah entri di atas daftar.

### Sisi be-ppid

Kedua modul sudah ada dan lengkap (kategori, nomor klasifikasi, ringkasan, tautan, lampiran, status; serta alasan/dasar hukum/jangka waktu/surat penetapan). Yang ditambahkan: kolom **No.** pada tabel Daftar Informasi Publik supaya urutan resmi terlihat langsung dari panel.

### Verifikasi

- `php -l` bersih; `npx tsc --noEmit` di be-ppid tanpa error; `npm run build` sukses.
- Basis data setelah seeding: **17 entri informasi publik terbit** (7/5/5 per klasifikasi) dan **22 entri dikecualikan terbit**.
- `/informasi`, `/informasi/dikecualikan`, `/informasi/berkala`, `/informasi/serta-merta`, `/informasi/setiap-saat`, `/regulasi`, dan beranda semuanya 200.
- Tangkapan layar 1700px diperiksa: kartu ringkasan 17/7/5/5, tabel 17 baris berurutan sesuai dokumen; halaman dikecualikan menampilkan 22 entri. Tangkapan layar 430px memastikan daftar berubah jadi kartu di ponsel.
- `lang/en.json`: +11 kunci (total 630).

**Catatan** — pada lebar 430px, tangkapan layar memperlihatkan isi terpotong sedikit di sisi kanan. Halaman lain yang tidak saya sentuh (mis. `/berita`) memperlihatkan hal yang sama, jadi ini gejala lama pada layout ponsel, bukan akibat perubahan ini. Bilang saja kalau mau saya telusuri terpisah.

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
