Berikut dibawah ini adalah poin-poin dari hasil testing dari user yang mana perlu adanya perbaikan atau penyempurnaan dari fitur-fitur, Ui/Ux, Error/ Bugs dan sebagainnya, dan jika poin-poin testing dibawah ini telah selesai tolong ada checklist sebagai penanada :
1. [x] https://ppid.foodstation.co.id/akun/pengaturan/data-pemohon (pada fe-ppid) ketika pemohon telah mengisi data verifikasi oleh pemohon ada yang perlu disesuaikan :
    - [x] Field NIK / Nomor KTP *, dibuat maksimal 16 karakater dan hanya bisa angka/ number.
      Temuan lanjutan: NIK `123123123123` (12 digit) masih bisa Submit. Diperbaiki — NIK sekarang wajib tepat 16 digit angka (`digits:16` di server, `pattern="[0-9]{16}"` + `minlength`/`maxlength` 16 di formulir).
    - [x] Jenis Pemohon *, dropdownnya dibuat otomatis "Pilih Jenis Pemohon", agar pemohon dapat memilih sendiri tidak mengikuti default sistem.
    - [x] Ketika tombol verifikasi diklik / submit, harusnya redirect ke halaman Dashboard. Dan user tidak dapat merubah datanya sebelum petugas PPID merespon. (kalau saat ini pemohon bisa submit terus-menerus, yang mana ada memicu notifikasi spam ke be-ppid)
2. [x] pada bagian fe-ppid dan be-ppid perlu adanya modifikasi dan penyesuaian sebagai berikut "
    - [x] https://ppid.foodstation.co.id/ (dihalaman beranda) tepat diatas section Berita & Publikasi, Buatkan section baru yang menampilkan Laporan Tahunan PT Food Station Tjipinang Jaya (Perseroda).
        Dengan konsep, tampilan UI seperti gambar di path ini "C:\Users\Rudi Adrian\Desktop\Screenshot 2026-09-08 110049.png" dan
        UXnya jika :
        - [x] di klik akan muncul pop up modal dialog "apakah anda ingin melihat atau mengunduh?"
        - [x] Jika (tombol) Melihat diklik arahkan ke url https://foodstation.id/laporan-tahunan-fstj/
        - [x] Jika (tombol) Mengunduh diklik arahkan ke untuk Membuat Permohonan, jika belum login, arahkan ke halaman https://ppid.foodstation.co.id/akun/masuk
        Isinya diambil dari modul CMS baru **Laporan Tahunan** (be-ppid → Konten Situs). Satu baris = satu tahun buku: tahun, judul, gambar sampul, dan Tautan halaman (Melihat). Section-nya tidak dirender selama modulnya masih kosong.
    - [x] https://ppid.foodstation.co.id/informasi dan https://ppid.foodstation.co.id/informasi/dikecualikan (di modul daftar informasi publik), bagian Annual Report, ini kan dibuat pada backend yang mana dibackend (be-ppid) sudah buatkan field Tautan Halaman, untuk mengisi jika redirect ke url tertentu, tapi belum ada opsi untuk yang tombol Unduh
      Informasi Publik dapat kolom baru `tautan_unduh` — tombol Unduh kini muncul walau tidak ada berkas yang diunggah. Alamatnya tidak pernah dicetak di halaman daftar; pengunjung baru diantar ke sana setelah permohonannya atas dokumen itu disetujui petugas. Bila lampiran berkas terisi, berkas itu yang dikirim.
    - [x] https://adm-ppid.foodstation.co.id/ppid/informasi-publik dan https://adm-ppid.foodstation.co.id/ppid/informasi-dikecualikan pada bagian ini, tolong dibuatkan juga 2 mekanismenya.. Jika ada 2 opsi Melihat dan Mengunduh diarahkan ke URL mana
      Informasi Publik: dua isian — "Tautan halaman (Melihat)" dan "Tautan salinan (Mengunduh)".
      Informasi Dikecualikan: hanya "Tautan halaman (Melihat)" yang berupa URL. Tombol Mengunduh di sana mengantar pengunjung mengajukan Permohonan Informasi, bukan ke sebuah URL — `permohonan_informasi` hanya mengenal `informasi_publik_id`, jadi belum ada cara menautkan persetujuan petugas ke baris informasi yang dikecualikan. Memasang URL salinan di sana berarti memasang alamat yang tidak punya penjaga. Bila memang diperlukan, penautannya bisa ditambahkan menyusul.
      Catatan uji "ketika saya klik, tidak pop up pilihan": ubahan ini belum di-deploy, jadi adm-ppid dan ppid.foodstation.co.id masih menjalankan kode lama. Diperiksa pada data lokal: `/informasi` memunculkan dialog pada 21 baris yang punya isi (termasuk entri **Annual Report**), `/informasi/dikecualikan` pada seluruh barisnya. Baris yang belum punya Tautan halaman maupun salinan memang tidak berdialog — tombolnya tetap "Mohon Dokumen", sesuai perilaku sebelumnya.
    - [x] Pastikan 2 Opsi ini
      Keduanya dijaga aturan yang sama di seluruh halaman: **Melihat** terbuka untuk siapa saja tanpa masuk; **Mengunduh** selalu melewati Permohonan Informasi yang disetujui petugas PPID.
    - [x] Section labelnya seperti ini :
        Laporan Tahunan
        PT Food Station Tjipinang Jaya (Perseroda)
3. [x] pada fe-ppid modul Permohonan Infromasi ( https://ppid.foodstation.co.id/akun/permohonan) dan Permohonan Keberatan Informasi (https://ppid.foodstation.co.id/akun/keberatan/baru), ada masalah :
    - [x] ketika permohonan di submit error dengan message : 500 Server Error
    dan payloadnya ini 
        _token
        1mDCCHbHWzs51o1lUJIgsYpZrDBZiJaIyQ9Rl5QV
        rincian_informasi
        Natus nostrum tempor ea velit doloribus commodi eos voluptas quos unde et
        tujuan_penggunaan
        Aperiam voluptas nisi impedit exercitationem ad dolores
        cara_memperoleh
        membaca
        format_informasi
        softcopy
        cara_pengiriman
        email
        pernyataan_benar
        1
      Penyebabnya `NotifikasiAdmin::permohonanBaru()` di fe-ppid: muatan notifikasinya menyebut `$keberatan->kode_keberatan`, variabel yang tidak ada di method itu (tersalin dari `keberatanBaru()`). Galatnya lahir saat argumen disusun — di luar jangkauan try/catch di dalam `kirim()` — sehingga sampai ke pemohon sebagai 500 padahal permohonannya sudah tersimpan dan bernomor. Kolom itu dibuang.
      Pagar tambahan: di PermohonanController dan KeberatanController, pemberitahuan setelah simpan (lonceng panel + surel tanda terima) dibungkus try/catch — pengiriman yang sudah tersimpan tidak boleh lagi berubah jadi 500 hanya karena pekerjaan ikutannya gagal.
      Uji baru: `PortalPermohonanKirimTest` (kirim permohonan, dua kiriman berturut-turut, pemohon belum terverifikasi, kirim keberatan) dan `NotifikasiAdminTest::test_notifikasi_permohonan_baru_tersusun_utuh`.

4. [x] ada error di fe-ppid :
    - [x] Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open_mobile" x-collapse.duration.300ms class=​"lg:​hidden bg-white border-t border-gray-100 pb-5 dark:​bg-[#071A12]​ dark:​border-white/​10" style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    cdn.min.js:5 Alpine Warning: You can't use [x-collapse] without first installing the "Collapse" plugin here: https://alpinejs.dev/plugins/collapse <div x-show=​"open" x-collapse style=​"display:​none">​…​</div>​
    A @ cdn.min.js:5
    accessibility.js:1 An iframe which has both allow-scripts and allow-same-origin for its sandbox attribute can escape its sandboxing.
    GetDefaultProp @ accessibility.js:1
    accessibility.js:1 [IND] You are running  Windows  Operating system,  Chrome  browser, version:  152
    accessibility.js:1 [IND] Version 5.3.1
    accessibility.js:1 Failed to execute 'postMessage' on 'DOMWindow': The target origin provided ('https://www.google.com') does not match the recipient window's origin ('https://ppid.foodstation.co.id').
    postFrames @ accessibility.js:1
    accessibility.js:1 Failed to execute 'postMessage' on 'DOMWindow': The target origin provided ('https://www.google.com') does not match the recipient window's origin ('https://ppid.foodstation.co.id').

      Penyebabnya `resources/views/layouts/app.blade.php`: Alpine dimuat dari `cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js`, dan berkas CDN itu tidak membawa plugin apa pun. Padahal `x-collapse` dipakai di header (menu ponsel + 4 submenu), FAQ beranda, halaman FAQ, dan Standar Layanan.
      Bundel sendiri sebenarnya sudah benar — `resources/js/app.js` memanggil `Alpine.plugin(collapse)` — tetapi layout hanya memuat `@vite('resources/css/app.css')`, jadi berkas JS-nya tidak pernah ikut dimuat halaman mana pun.
      Diperbaiki: baris CDN diganti `@vite('resources/js/app.js')`, ditaruh setelah `<script>` pendaftar store tema supaya pendengar `alpine:init` sudah siap sebelum Alpine jalan. Situs publik sekaligus lepas dari CDN pihak ketiga. CI sudah membangun asetnya (`build:fe-assets`), jadi tidak ada langkah deploy baru.
      Diperiksa pada render beranda: `cdn.jsdelivr` 0, bundel `build/assets/app-*.js` termuat, `x-collapse` 10 tempat. Uji baru `AsetAlpineTest`.

      Sisa pesan di console berasal dari widget aksesibilitas EqualWeb (`accessibility.js`, pihak ketiga, versi 5.3.1 terkunci SRI): peringatan sandbox iframe dan `postMessage` ke `https://www.google.com`. Keduanya lahir di dalam skrip mereka, bukan kode kita — tidak ada yang bisa diperbaiki dari sisi ini selain melepas widgetnya.
5. [x] pada fe-ppid (https://ppid.foodstation.co.id/informasi) bagian ini buatkan juga fitur untuk Melihat Saja dan Mengunduh dengan modal dialog. dan juga disesuaikan backendnya be-ppid (https://adm-ppid.foodstation.co.id/ppid/informasi-publik ), karena ketika saya input Tautan salinan (Mengunduh) muncul alert error : 500 Server Error
6. [x] pada fe-ppid (https://ppid.foodstation.co.id/informasi/dikecualikan) bagian ini hanya ada fitur Melihat Saja, tidak ada tombol Mengunduh (tapi untuk membuat dinamis, jika dibackendnya di isi tampilkan saja tombolnya) dengan modal dialog. Dan juga disesuaikan backendnya be-ppid (https://adm-ppid.foodstation.co.id/ppid/informasi-dikecualikan).
      **Poin 5.** Dialog dua pilihannya sebenarnya sudah ada di /informasi sejak langkah 83; label tombolnya sekarang disamakan dengan permintaan — **Hanya Lihat** dan **Mengunduh** (sebelumnya "Di Lihat Saja" / "Unduh Dokumen"), ikut berubah di halaman akses dokumen.
      500 saat menyimpan **Tautan salinan (Mengunduh)** tidak bisa direproduksi di lokal. Diuji lewat API apa adanya, termasuk muatan penuh seperti yang dikirim formulir panel (`files`, `unduhan_terbatas`, status, dsb.) — semuanya 200 dan nilainya tersimpan. Uji: `api-ppid/tests/Feature/TautanUnduhInformasiTest.php` (5 kasus).
      Dugaan terkuat: kolom `informasi_publik.tautan_unduh` belum ada di basis data server yang dipakai panel, sementara kode api-nya sudah menyebut kolom itu — bentuknya galat SQL "column does not exist", yang memang keluar sebagai 500. Urutan deploy karena itu penting: **`deploy:api` dulu** (job itu yang menjalankan `artisan migrate --force`), baru `deploy:adm`. Kalau setelah deploy masih 500, kirim satu baris galat terakhir dari `api-ppid/storage/logs/laravel.log` — di situ sebab aslinya tercatat.

      **Poin 6.** `informasi_dikecualikan` sekarang punya `tautan` dan `tautan_unduh`; keduanya tampil di panel sebagai "Tautan halaman (Melihat)" dan "Tautan salinan (Mengunduh)". Di situs, tiap tombol hanya muncul bila alamatnya diisi — kosong berarti dialognya menyebutkan apa yang belum tersedia, bukan memasang tombol yang tidak menuju ke mana-mana.
      Catatan penting soal aturannya: di halaman ini tombol Mengunduh membuka alamatnya **langsung**, tanpa gerbang permohonan. Alasannya, pada daftar Dikecualikan penerbitan berkas memang sudah begitu — berkas Surat Penetapan pun dilayani apa adanya di sana — jadi mengisi alamat salinan adalah keputusan petugas untuk menerbitkannya. Berbeda dengan `informasi_publik.tautan_unduh`, yang tetap dijaga aturan unduhan terbatas. Bilang saja kalau yang diinginkan justru bergerbang; itu menuntut kolom penaut permohonan ke baris Dikecualikan.
      Uji baru di fe-ppid: dialog dikecualikan membawa kedua alamat, baris tanpa alamat tidak memasang tombol, dan baris yang hanya punya salinan tetap benar.
