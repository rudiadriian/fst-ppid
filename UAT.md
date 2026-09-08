Berikut dibawah ini adalah poin-poin dari hasil testing dari user yang mana perlu adanya perbaikan atau penyempurnaan dari fitur-fitur, Ui/Ux, Error/ Bugs dan sebagainnya, dan jika poin-poin testing dibawah ini telah selesai tolong ada checklist sebagai penanada :
1. https://ppid.foodstation.co.id/akun/pengaturan/data-pemohon (pada fe-ppid) ketika pemohon telah mengisi data verifikasi oleh pemohon ada yang perlu disesuaikan :
    - [x] Field NIK / Nomor KTP *, dibuat maksimal 16 karakater dan hanya bisa angka/ number.
      Temuan lanjutan: NIK `123123123123` (12 digit) masih bisa Submit. Diperbaiki — NIK sekarang wajib tepat 16 digit angka (`digits:16` di server, `pattern="[0-9]{16}"` + `minlength`/`maxlength` 16 di formulir).
    - [x] Jenis Pemohon *, dropdownnya dibuat otomatis "Pilih Jenis Pemohon", agar pemohon dapat memilih sendiri tidak mengikuti default sistem.
    - [x] Ketika tombol verifikasi diklik / submit, harusnya redirect ke halaman Dashboard. Dan user tidak dapat merubah datanya sebelum petugas PPID merespon. (kalau saat ini pemohon bisa submit terus-menerus, yang mana ada memicu notifikasi spam ke be-ppid)
