@php
    /*
     * Avatar akun pemohon.
     *
     * Dipisah ke partial supaya foto yang diunggah di Profil tampil sama di
     * semua tempat — header situs, sapaan Portal Pemohon, dan halaman Profil
     * sendiri. Sebelumnya header selalu menggambar inisial, sehingga foto baru
     * seolah tidak tersimpan.
     *
     * Variabel: $pemohon, $ukuran (kelas lebar/tinggi), $teks (kelas ukuran
     * huruf inisial), $cincin (kelas ring, boleh kosong), $latar (kelas latar
     * inisial).
     */
    $ukuran ??= 'w-7 h-7';
    $teks ??= 'text-xs';
    $cincin ??= '';
    $latar ??= 'fs-gradient-accent text-white';

    $nama = $pemohon?->nama ?? '?';
@endphp

@if ($pemohon?->foto)
    <img src="{{ route('media.show', ['path' => $pemohon->foto]) }}"
         alt="{{ __('Foto profil :nama', ['nama' => $nama]) }}"
         class="{{ $ukuran }} {{ $cincin }} rounded-full object-cover flex-shrink-0">
@else
    <span class="{{ $ukuran }} {{ $teks }} {{ $cincin }} {{ $latar }} rounded-full font-bold flex items-center justify-center flex-shrink-0"
          aria-hidden="true">
        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($nama, 0, 1)) }}
    </span>
@endif
