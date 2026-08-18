@php
    /* Tinggi bingkai peta; halaman Standar Layanan memakai yang lebih tinggi. */
    $tinggi ??= 'h-[260px]';
@endphp

@php
    /*
     * Peta kantor PPID PT Food Station Tjipinang Jaya.
     *
     * Dipakai dua tempat — kartu Kontak di Beranda dan panel Waktu Layanan di
     * Standar Layanan — jadi koordinat serta tautannya hanya ditulis di sini.
     *
     * Bentuk `?q=<lat>,<lng>&output=embed` sengaja dipilih ketimbang URL
     * `maps/embed?pb=…`: yang terakhir memuat token panjang hasil salinan dari
     * peramban yang tidak bisa dibaca maupun disesuaikan kalau titiknya
     * bergeser.
     */
    $lat = '-6.213053';
    $lng = '106.881272';
    $zoom = 17;

    $embed = "https://www.google.com/maps?q={$lat},{$lng}&z={$zoom}&hl=id&output=embed";
    $tautan = "https://www.google.com/maps/search/pt+food+station+tjipinang+jaya/@{$lat},{$lng},{$zoom}z";
@endphp

<div>
    <div class="rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 {{ $tinggi }}">
        <iframe
            src="{{ $embed }}"
            title="{{ __('Peta lokasi kantor PPID PT Food Station Tjipinang Jaya') }}"
            class="w-full h-full"
            style="border:0;"
            allowfullscreen=""
            loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
        </iframe>
    </div>

    {{-- Peta yang tersemat tidak bisa dipakai menyusun rute, jadi tautan ke
         Google Maps aslinya tetap disediakan. --}}
    <a href="{{ $tautan }}" target="_blank" rel="noopener"
       class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-[#175A3C] hover:text-[#10462F] dark:text-[#3E9C6C] dark:hover:text-[#7DB395] transition">
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        {{ __('Buka di Google Maps') }}
    </a>
</div>
