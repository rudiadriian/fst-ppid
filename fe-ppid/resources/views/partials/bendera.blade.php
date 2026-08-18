{{-- Ikon bendera untuk pengalih bahasa.

     Emoji bendera (🇮🇩 / 🇬🇧) tidak punya glif di font bawaan Windows — yang tampil
     di sana hanya dua huruf kecil "ID"/"GB" atau kotak kosong. Karena itu benderanya
     digambar sebagai SVG inline supaya hasilnya sama di semua sistem operasi.

     Butuh: $kode ('id' | 'en'). Opsional: $kelas (kelas Tailwind untuk ukuran). --}}
@php
    $kelasBendera = ($kelas ?? 'w-5') . ' h-auto shrink-0 rounded-[2px] ring-1 ring-black/10 dark:ring-white/20';
    // clipPath butuh id unik supaya beberapa bendera dalam satu halaman tidak saling menimpa.
    $uid = 'bendera-' . \Illuminate\Support\Str::random(6);
@endphp

@if (($kode ?? 'id') === 'id')
    <svg viewBox="0 0 60 40" class="{{ $kelasBendera }}" role="img" aria-hidden="true" focusable="false">
        <rect width="60" height="20" fill="#E70011"></rect>
        <rect y="20" width="60" height="20" fill="#FFFFFF"></rect>
    </svg>
@else
    <svg viewBox="0 0 60 30" class="{{ $kelasBendera }}" role="img" aria-hidden="true" focusable="false">
        <clipPath id="{{ $uid }}">
            <path d="M30,15 h30 v15 z v15 h-30 z h-30 v-15 z v-15 h30 z"></path>
        </clipPath>
        <rect width="60" height="30" fill="#012169"></rect>
        <path d="M0,0 L60,30 M60,0 L0,30" stroke="#FFFFFF" stroke-width="6"></path>
        <path d="M0,0 L60,30 M60,0 L0,30" clip-path="url(#{{ $uid }})" stroke="#C8102E" stroke-width="4"></path>
        <path d="M30,0 v30 M0,15 h60" stroke="#FFFFFF" stroke-width="10"></path>
        <path d="M30,0 v30 M0,15 h60" stroke="#C8102E" stroke-width="6"></path>
    </svg>
@endif
