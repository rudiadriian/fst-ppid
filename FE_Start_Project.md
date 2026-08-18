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
63. tolong jalankan langkah registrasi akun pengguna/ pengunjung website ppdi alur prosesnya :
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


pada portal pemohon atau http://localhost:8000/akun tolong buatkan lonceng notifikasi jika ada upadate dari feedback yang diberikan oleh admin





---


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
