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
10. pada Modul Informasi Publik, Sub Modul Informasi Berkala, tolong dibuat seperti pada url disini https://ppid.jiep.co.id/page/informasi-berkala?key_id=informasi-berkala&taxonomy_id=9c991cb6-ed5d-4e81-baab-cdd50619caf5&content_layout_page=page-standar&content_key_id=sub-menu-informasi-berkala
yang mana konten pada halaman tersebut bersifat dinamis yang nantinya akan dapat disesuaikan pada backend be-ppid. lalu nantinya karena ada tombol Selengkapnya yang berhubungan jika kita lihat pada link ke halaman yang lain, coba anda buatkan konsep seperti tersebut. Saat ini sudah saya coba input lewat backend tetapi tidak muncul di halaman FE modul informasi berkala. coba cek sekiranya seperti ini D:\Project\Ppid\INFORMASI BERKALA.xlsx

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
