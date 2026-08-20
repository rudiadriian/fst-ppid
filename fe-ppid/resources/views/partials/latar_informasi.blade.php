{{--
    Latar bergambar untuk area Informasi Publik.

    Menggantikan blok hijau polos: gambar maskot PPID ("di rumah, di kantor,
    di perjalanan, di mana saja") dipasang sebagai latar, lalu ditumpangi
    gradasi hijau korporat supaya tetap satu keluarga dengan bagian situs yang
    lain.

    Tiga lapis, urutannya menentukan keterbacaan:

      1. gambar, diredupkan lewat `filter: brightness()` — ilustrasinya punya
         bidang putih terang yang, tanpa diredupkan, membuat teks putih di
         atasnya tidak terbaca;
      2. gradasi hijau `fs-gradient` dengan opasitas yang bisa diatur pemanggil;
      3. pola titik tipis, sama seperti hero lain.

    Pemanggil yang teksnya duduk langsung di atas latar (hero) memakai opasitas
    lebih tinggi; yang isinya kartu berlatar sendiri boleh lebih rendah supaya
    gambarnya lebih terlihat.

    Berkasnya WebP 1536 px (±200 KB) hasil kecilan dari PNG aslinya yang 2,2 MB —
    latar halaman tidak boleh seberat itu. `aria-hidden` dan `alt` kosong karena
    ini hiasan: pembaca layar tidak punya urusan dengannya.

    Angkanya bukan selera: kombinasi yang dipakai di sini diuji dengan menyusun
    ulang lapisannya di luar peramban lalu mengukur kontras teks putih pada pita
    tempat judul benar-benar duduk. Yang terburuk 5,5:1 — di atas ambang WCAG AA
    (4,5:1). Menaikkan `$terang` atau menurunkan `$opasitas` lebih jauh membuat
    gambarnya lebih terlihat tetapi menembus ambang itu.

    Parameter:
      $opasitas — opasitas gradasi hijau (0–1), bawaan 0.66
      $terang   — kecerahan gambar (0–1), bawaan 0.68
      $muat     — 'eager' untuk hero di atas lipatan, bawaan 'lazy'
      $scrim    — true: tambah gelap di sisi atas, untuk judul yang duduk di sana
--}}
@php
    $opasitas = $opasitas ?? 0.66;
    $terang = $terang ?? 0.68;
    $muat = $muat ?? 'lazy';
    $scrim = $scrim ?? false;
@endphp

<div class="absolute inset-0" aria-hidden="true">
    <img src="{{ asset('assets/images/ppid/ppid-di-mana-saja.webp') }}"
         alt=""
         loading="{{ $muat }}"
         decoding="async"
         class="w-full h-full object-cover object-center"
         style="filter: brightness({{ $terang }});">

    {{-- Gelap di pita tengah, bukan di sisi atas: isi section beranda dipusatkan
         vertikal, jadi judulnya duduk di sekitar sepertiga sampai setengah
         tinggi — bukan menempel di atas. --}}
    @if ($scrim)
        <div class="absolute inset-0 bg-gradient-to-b from-transparent via-black/45 to-transparent"></div>
    @endif

    <div class="absolute inset-0 fs-gradient" style="opacity: {{ $opasitas }};"></div>
    <div class="absolute inset-0 fs-dot-pattern opacity-25"></div>
</div>
