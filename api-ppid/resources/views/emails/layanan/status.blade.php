@php
    /*
     * Pemberitahuan status pengajuan layanan (permohonan informasi & keberatan).
     *
     * Dipakai untuk tiga peristiwa saja — terkirim, diterima petugas, dan
     * selesai — supaya kotak masuk pemohon tidak dibanjiri setiap pergeseran
     * status internal.
     *
     * Variabel: judul, preheader, nama, paragraf[], baris[], catatan[], url,
     * labelTombol. Nilai bawaan di bawah menjaga view tetap bisa dirender
     * meski pemanggilnya tidak mengisi bagian yang opsional.
     */
    $preheader ??= '';
    $paragraf ??= [];
    $baris ??= [];
    $catatan ??= [];
    $url ??= null;
    $labelTombol ??= null;
@endphp

<x-email.layout :judul="$judul" :preheader="$preheader">

    <p style="margin:0 0 14px 0;">{{ __('Yth. :nama,', ['nama' => $nama]) }}</p>

    @foreach ($paragraf as $isi)
        <p style="margin:0 0 14px 0;">{{ $isi }}</p>
    @endforeach

    <x-email.rincian :baris="$baris" />

    @if ($url && $labelTombol)
        <x-email.tombol :url="$url">{{ $labelTombol }}</x-email.tombol>
        <x-email.tautan-cadangan :url="$url" />
    @endif

    @foreach ($catatan as $isi)
        <p style="margin:18px 0 0 0;font-size:14px;color:#5B6660;">{{ $isi }}</p>
    @endforeach

    {{-- Nama instansi lengkapnya sudah ada di kaki email, jadi tanda tangan
         di sini memakai bentuk pendek supaya tidak terbaca dua kali. --}}
    <p style="margin:22px 0 0 0;">
        {{ __('Salam,') }}<br>
        <strong>{{ __('PPID Food Station') }}</strong>
    </p>
</x-email.layout>
