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
