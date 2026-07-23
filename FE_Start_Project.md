Saya ingin membuat aplikasi PPID untuk perusahaan BUMD Jakarta PT Food Station Tjipinang Jaya (Perseroda), yang mana website ini dibutuhkan oleh perusahaan untuk menampung arus keluar dokumen resmi berdasarkan permintaan pengunjung website.
adapun langkah-langkah yang perlu dijalankan, jika sudah selesai dijalankan tolong beri tanda checklist. berikut langkahnya dibawah ini :
1. [x] ✅ **SELESAI** — Tolong modifikasi dengan KONSEP Desain UI/UX yang berbeda dari kode yang seakrang pada halaman website Front End untuk semua pengunjung website yang mana project ini dapat diakses public adapun pathnya resources\views\layouts\ dan resources\views\ppid\  dibutuhkan konsep desain UI/UX (Warna thema korportnya Hijau Gradasi, Hitam, dan Putih https://foodstation.id/) halaman website Public yang menarik (Modern, Korporat, Responsive jika diakses pada resolusi layar device apapun, dan Informatif). ada beberapa referensi website yaitu https://ppid.transjakarta.co.id/ dan https://ppid.pamjaya.co.id/ 
2. [x] ✅ **SELESAI** — Ubah warna background Header resources\views\layouts\header.blade.php menjadi warna putih
3. modifikasi resources\views\ppid\home.blade.php :
    a. [x] ✅ **SELESAI** — Tambahkan Slider gambar agar fitur lebih menarik, pada section Portal Keterbukaan Informasi Publik dan Arsip Resmi Laporan
    b. [x] ✅ **SELESAI** — Pada bagian Kategori Informasi tambahkan background gambar agar lebih menarik
    c. [x] ✅ **SELESAI** — pada bagian Bantuan Pertanyaan Umum dan Hubungi Kami Kontak PPID Food Station dibuat 1 section saja col-6 
    d. [x] ✅ **SELESAI** — Pada bagian Prosedur Alur Permohonan Informasi, tambahkan line yang saling terhubung antar Card-card dan saat ini icon panahnya tidak tertutup.
    e. [x] ✅ **SELESAI** — Pada Card Permohonan Informasi, Pengajuan Keberatan, Cek Status Ticket  dan Unduh Dokumen dibuat menggantung dengan section Portal Keterbukaan Informasi Publik
4. [x] ✅ **SELESAI** — modifikasi background warna resources\views\layouts\footer.blade.php gunakan warna thema hijau gradasi dengan tambahkan nots-nots/paterns warna putih agar terkesan tidak polos banget.
5. [x] ✅ **SELESAI** — Aktifkan mode Dark dan Light, Translate Bahasa Indonesia dan Bahasa Inggris pada semua halaman website ini.
   - [x] FIX: Dark/Light CTA strip footer → `<section class="bg-white dark:bg-[#0A0E0D]">` (dulu tanpa dark).
   - [x] FIX: Translate ID/EN kini aktif di SEMUA modul ppid/ (profil, informasi, regulasi, standar, form permohonan/keberatan/cek-status) — output $data + string hardcoded dibungkus __(), kamus lengkap di lang/en.json. Verified via server.
   - ~~Dark/Light pada section ini tidak berhasil aktif.~~ (fixed)
        <section class="bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mb-16 relative z-20">
                <div class="bg-[#004d3a] rounded-3xl px-8 py-10 lg:px-14 lg:py-12 shadow-2xl shadow-emerald-950/40 flex flex-col lg:flex-row items-center justify-between gap-6 overflow-hidden relative">
                    <div class="absolute inset-0 fs-dot-pattern opacity-50"></div>
                    <div class="relative z-10 text-center lg:text-left">
                        <h3 class="text-2xl lg:text-3xl font-extrabold text-white">Butuh Informasi Publik?</h3>
                        <p class="text-white/85 mt-1.5">Ajukan permohonan Anda secara resmi, transparan, dan gratis.</p>
                    </div>
                    <div class="relative z-10 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                        <a href="http://localhost:8000/permohonan" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-[#00664e] text-sm font-bold rounded-xl hover:bg-emerald-50 transition-colors duration-200">Ajukan Permohonan</a>
                        <a href="http://localhost:8000/cek-status" class="inline-flex items-center justify-center px-6 py-3.5 bg-white/10 border border-white/30 text-white text-sm font-bold rounded-xl hover:bg-white/20 transition-colors duration-200">Cek Status Tiket</a>
                    </div>
                </div>
            </div>
        </section>
6. [x] ✅ **SELESAI** — cek detail fitur Translate ID/EN, deskripsi, alert konten belum berubah.
   - Deskripsi hero + notice 3 form di-wrap `__()`.
   - Konten DINAMIS form kini ikut translate (blade `__()` di-render server-side sebelum JS): `alert()`, `placeholder`, `<option>` (value ID dipertahankan, display translate), hint, teks sukses/notFound, teks "Jika tidak puas...".
   - Kamus lengkap di lang/en.json. Cache view/config/route/compiled dibersihkan.
   - Verified server EN: opsi (Individual, Information Request Denied), alert (Failed to submit...), placeholder (Describe specifically...), notFound (was not found...). Tanpa parse error.
   - CATATAN: bila browser masih ID → hard refresh Ctrl+Shift+R. Self-host: `php artisan view:clear && php artisan config:clear && php artisan route:clear`.
7. [x] ✅ **SELESAI** — beberapa halaman konten belum berubah bahasa — semua di-wrap `__()` + kunci en.json, verified server EN:
    - [x] profile/struktur: label diagram (Atasan/Utama/Pembantu) → "PPID Superior"/"Main PPID"/"Assisting PPID 1-2" + "[ Organizational Structure Diagram ]" dan Tombol Unduh Dokumen Struktur (PDF)
    - [x] FIX BUG: `information.blade.php` heading rusak `{{ __('Dokumen >Dokumen &amp; Arsip<amp; Arsip') }}` → `{{ __('Dokumen & Arsip') }}` = "Documents & Archives".
    - [x] home: slider badge "PDF · Dokumen Resmi", judul+subjudul slide (`__($slide['title'])`, `__($slide['subtitle'])`), modal Formulir Unduh Dokumen (label Nama Lengkap/Surat Elektronik/Telepon, tombol Kirim Tautan Unduhan, Memproses..., Permintaan Berhasil, teks email, Tutup).
    - [x] profile: pesan fallback slug tak dikenali.
    - [x] layouts/app: `<title>` default → `__('PPID - Layanan Informasi Publik')`.
    - [x] +20 kunci baru di `lang/en.json` (total 307). Cache view/config/route dibersihkan.
    - Verified server EN: `/profile/struktur` → PPID Superior, Main PPID, Assisting PPID 1, Organizational Structure Diagram, Download Structure Document (PDF). `/informasi/berkala` → Documents & Archives. `/` → 2025 Annual Report, Official Document, Document Download Form.
8. [x] ✅ **SELESAI** — Pasang icon perusahaan untuk di tab browser pakai file \ppid\public\assets\images\logo\favicon.ico
    - `layouts/app.blade.php` head: `<link rel="icon" ... sizes="any">` + `<link rel="shortcut icon" type="image/x-icon">` → `asset('assets/images/logo/favicon.ico')`, plus `apple-touch-icon` → `logo_fs.png`.
    - Verified: tag ter-render, aset HTTP 200.
9. [x] ✅ **SELESAI** — Pasang logo perusahaan untuk di header resources\views\layouts\header.blade.php pakai file public\assets\images\logo\logo_fs.png
    - Kotak gradasi "FS" diganti `<img src="{{ asset('assets/images/logo/logo_fs.png') }}">`, tinggi `h-9 sm:h-10 w-auto`, hover scale halus.
    - Dark mode: wordmark logo berwarna hitam → diberi `dark:bg-white dark:rounded-lg dark:px-2 dark:py-1` supaya tetap terbaca.
    - Ditambah divider vertikal; subteks "Food Station" (duplikat wordmark logo) diganti `{{ __('Informasi Publik') }}` (kunci sudah ada di en.json).
    - Verified: `<img>` ter-render, `logo_fs.png` HTTP 200.
    - [x] BONUS: footer (`layouts/footer.blade.php`) disamakan — badge "FS" diganti logo dalam pill putih (`bg-white rounded-xl px-2.5 py-1.5`) karena background footer hijau gradasi, + divider, teks jadi "PPID" / "Transparansi Informasi".
10. [x] ✅ **SELESAI** — label 'Public Information' tolong dihapus, biarkan hanya label PPID saja pada header resources\views\layouts\header.blade.php, buat ukuran textnya yang besar namun terlihat proporsional.
    - Subteks `{{ __('Informasi Publik') }}` / "Public Information" dihapus, wrapper `flex flex-col` dibuang → tinggal 1 `<span>` PPID.
    - Ukuran naik `text-xl` → `text-2xl sm:text-3xl` + `leading-none` supaya sejajar tinggi logo (`h-9 sm:h-10`) tanpa lebihi bar `h-[72px]`.
    - Verified server: span ter-render, 0 sisa "Informasi Publik" di header (ID maupun EN).
11. [x] ✅ **SELESAI** — tampilan Public Information Service Hours pada halaman /profile/singkat tolong dibuat lebih rapi dan pastikan tampilan responsive.
    - Data `service_hours` di `PpidController.php` diubah dari 1 string panjang per baris → array terstruktur `['days','time','break']`, supaya bisa dirender rapi & tiap bagian bisa ditranslate terpisah.
    - `profile.blade.php`: dua kartu jadwal (`grid-cols-1 sm:grid-cols-2 gap-4`) — hari (uppercase hijau), jam besar `tabular-nums`, istirahat + ikon. Catatan hari kerja pindah ke bawah full-width + ikon info (dulu kolom kanan berdempet garis pemisah, pecah di layar sempit).
    - Responsive: padding `p-6 sm:p-8`, judul `text-xl sm:text-[22px]` + `min-w-0` (cegah overflow), jam `text-lg sm:text-xl`, ikon `flex-shrink-0`. 1 kolom di mobile → 2 kolom mulai `sm`.
    - `lang/en.json` +1 kunci ("Istirahat" → "Break"; "Senin s.d. Kamis"/"Jum’at" sudah ada), total 308.
    - Verified server ID: Senin s.d. Kamis / Jum’at, 08.00 – 15.00 WIB, Istirahat 12.00 – 13.00 & 11.30 – 13.30. EN: Monday to Thursday / Friday / Break / Public Information Service Hours.
12. [x] ✅ **SELESAI** — tampilan Detail Information Service Channels & Hours pada halaman /standar-layanan/jalur-waktu-layanan tolong dibuat lebih rapi dan pastikan tampilan responsive. tampilan yang terlalu menumpuk dan terlihat tidak rapi.
    - FIX BUG: blade dulu pakai `explode(':', $schedule, 2)` untuk pecah jam layanan — rapuh & sel tabel sempit. Tabel dibuang.
    - `PpidController.php`: `channels` → `['label','desc','recommended']`, `hours` → `['days','time','break']`.
    - AKAR "menumpuk": container `max-w-4xl` (896px) dibagi 2 kolom, lalu kolom kanan dibagi 2 lagi → tiap kartu ±200px. Fix: container jadi `max-w-6xl` khusus slug ini, dan 2 kolom sejajar diganti **stack vertikal** (`space-y-6`) full-width.
    - Jalur Pelayanan: 3 kartu `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3`, tiap kartu ada ikon sendiri (globe/orang/amplop), label uppercase hijau + badge "Direkomendasikan" (Online) + deskripsi, `h-full flex flex-col` biar rata tinggi.
    - Waktu Layanan: kartu jadwal `grid-cols-1 sm:grid-cols-2` identik halaman /profile/singkat.
    - Responsive: `p-6 sm:p-8`, ikon `w-12 h-12 sm:w-14 sm:h-14 flex-shrink-0`, judul `text-xl sm:text-[22px] min-w-0`, badge `flex-wrap`, jam `tabular-nums break-words`.
    - `lang/en.json` +7 kunci (Online/In Person/Mail/Recommended + 3 deskripsi), total 315.
    - Verified server: HTTP 200, `max-w-6xl` + `sm:grid-cols-2 lg:grid-cols-3` ter-render. ID: Direkomendasikan, Senin s.d. Kamis, Jum’at. EN: In Person, Mail, Recommended, Monday to Thursday, Break.
13. [x] ✅ **SELESAI** — ketika mode Light, Angka pada CARD-CARD pada halaman home (resources\views\ppid\home.blade.php) di section Public Information dan Information Request Flow menjadi tidak terlihat jelas. Jika mode Light, ubah warna menjadi hijau nyata. Dan jika mode Dark, ubah warnanya menjadi putih.
    - Penyebab: angka pakai warna nyaris putih di atas kartu putih — Informasi Publik `text-gray-100` (#f3f4f6), watermark Alur `text-gray-50` (#f9fafb). Varian dark juga tak ada.
    - Angka jumlah dokumen (Public Information): `text-[#008060] group-hover:text-[#00664e] dark:text-white dark:group-hover:text-white` — hijau solid di Light, putih solid di Dark.
    - Watermark nomor langkah (Information Request Flow): `text-[#008060]/25 group-hover:text-[#008060]/40 dark:text-white/25 dark:group-hover:text-white/40`. Hijau di Light, putih di Dark; pakai opacity 25% karena angkanya `text-[6rem]` dan berada TEPAT di belakang judul+deskripsi kartu — warna solid membuat teks kartu tak terbaca. Kalau tetap mau solid: hapus `/25` dan `/40`.
    - Verified server: 3 angka Public Information + 6 watermark langkah pakai kelas baru; 0 sisa `text-gray-50`/`text-gray-100`.
    - CATATAN: kelas Tailwind baru → jalankan `npm run build` (atau `npm run dev`) supaya CSS ter-compile.
14. [x] ✅ **SELESAI** — Pada halaman resources\views\ppid\home.blade.php dibagian Card-card Permohonan Informasi, Pengajuan Keberatan, Cek Status Ticket dan Unduh Dokumen dibuat menggantung dengan section Portal Keterbukaan Informasi Publik, posisinya dibuat lebih porporsional saja, lalu tambahkan label Hero agar lebih rapi.
    - Kartu berada DI DALAM section hero lalu digantung keluar (`-mb-...`), konsep sama dgn CTA "Butuh Informasi Publik?". Section pembungkus terpisah + `-mt-24 lg:-mt-28` lama dihapus.
    - FIX BUG: hero punya `overflow-hidden` → kartu menggantung akan terpotong. `overflow-hidden` dipindah ke layer dekorasi (`<div class="absolute inset-0 overflow-hidden">` pembungkus dot-pattern + 2 lingkaran blur); section hero jadi `relative fs-gradient`.
    - LABEL HERO baru di atas grup kartu (masih di area hijau hero): eyebrow "Layanan Cepat" + garis kecil, judul "Mulai Layanan dari Sini", dan keterangan kanan "Empat layanan utama PPID dalam satu klik." — layout `flex-col sm:flex-row sm:items-end sm:justify-between`.
    - Proporsi digantung disetel ulang: hero `pb-20 lg:pb-24` → `pb-12 lg:pb-16` (ruang dipakai label), gantung `-mb-16 lg:-mb-20` → `-mb-20 lg:-mb-24`, section Statistik `pt-32 lg:pt-40` → `pt-36 lg:pt-44`.
    - `lang/en.json` +3 kunci (Quick Services / Start Your Service Here / Four core PPID services in one click.), total 318.
    - Verified server HTTP 200: label ID + EN ter-render, kelas `-mb-20 lg:-mb-24`, `pb-12 lg:pb-16`, `pt-36 lg:pt-44` aktif.
15. [x] ✅ **SELESAI** — Tolong buat menu-menu Active ketika modul tersebut di akses
    - `header.blade.php`: dulu "Beranda" di-hardcode selalu aktif (desktop + mobile), menu lain tak pernah aktif. Sekarang dihitung dari route.
    - Flag: `$isHome = request()->is('/')`, `$isProfile/$isInfo/$isRegulation/$isService/$isRequest/$isStatus = request()->routeIs('ppid.*')`, `$activeSlug = request()->route('slug')`.
    - Helper `$navItem()/$dropItem()` (desktop) + `$mobileItem()/$mobileSub()` (mobile) pilih kelas normal vs aktif.
    - Gaya aktif: parent = `text-[#008060] bg-emerald-50 dark:bg-white/5 dark:text-[#00A66C]`; item dropdown/submenu = tambah `font-bold` + `border-l-2 border-[#008060]`; tombol CTA Permohonan = `ring-2 ring-[#008060] ring-offset-2`; tombol Cek Status mobile = border+bg hijau.
    - Parent ikut aktif saat salah satu anaknya dibuka (mis. buka /profile/struktur → "Profil" aktif + "Struktur PPID" aktif).
    - Mobile: akordeon modul aktif otomatis TERBUKA (`x-data="{ open: true }"` + `style="display:none"` dilepas kondisional).
    - A11y: `aria-current="page"` pada link tunggal yang aktif (Beranda, Regulasi).
    - Verified server semua rute HTTP 200: `/` & `/regulasi` → aria-current + parent aktif; `/profile/struktur`, `/informasi/berkala`, `/standar-layanan/jalur-waktu-layanan` → parent aktif + 2 submenu aktif (desktop & mobile); `/permohonan` → ring CTA; `/cek-status` → tombol status aktif.
16. Buatkan section HERO baru Pada halaman resources\views\ppid\home.blade.php dibagian Card-card (Permohonan Informasi, Pengajuan Keberatan, Cek Status Ticket dan Unduh Dokumen dibuat menggantung dengan section Portal Keterbukaan Informasi Publik) dengan tepat di atas Kategori Informasi dengan BACKGROUND PUTIH, PISAHKAN DENGAN SECTION Kategori Informasi.
17.  Pada halaman resources\views\ppid\home.blade.php dibagian summary angka Pemohon, Dokumen, Regulasi, dan Presentase Kepuasan PIDAHKAN ke section Alur Permohonan Informasi tepat diatas label Prosedur
