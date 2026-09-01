@extends('layouts.app')

@section('title', __($data['title']) . ' | ' . __('Standar Pelayanan PPID'))

{{-- Maklumat berupa PDF digambar lewat pdf.js, sama seperti halaman detail
     Regulasi; skripnya hanya dimuat bila memang ada PDF yang dibaca. --}}
@if (!empty($data['dokumen']) && $data['dokumen']['ext'] === 'pdf')
    @push('scripts')
        @vite('resources/js/sampul-pdf.js')
    @endpush
@endif

@section('content')

    {{-- HERO --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 28px 28px;"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-6 lg:px-8 py-16 lg:py-20 text-center">
            <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-4">{{ __('Standar Pelayanan') }}</p>
            <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight">{!! $judulDua(__($data['title']), 1, 'fs-title-accent-soft') !!}</h1>
            <p class="mt-4 text-lg font-normal text-white/80 max-w-2xl mx-auto leading-relaxed">
                {{ __('Komitmen pelayanan informasi publik PPID Food Station yang cepat, mudah, dan transparan.') }}
            </p>
        </div>
    </section>

    {{-- KONTEN --}}
    <section class="py-16 lg:py-20 bg-[#FAF6EC] dark:bg-[#082217]">
        <div class="{{ $slug === 'jalur-waktu-layanan' ? 'max-w-screen-2xl' : 'max-w-6xl' }} mx-auto px-6 lg:px-8">
            <div class="bg-white dark:bg-[#0B2A1D] p-6 sm:p-10 rounded-2xl shadow-sm border border-gray-100 dark:border-white/10">

                @php
                    /*
                     * Judul "Detail <nama halaman>" dilewati pada halaman yang
                     * tayang sebagai dokumen atau gambar (langkah 86 & 88):
                     * hero di atasnya sudah menyebut nama halaman, dan
                     * mengulanginya di baris pertama isi hanya mendorong
                     * gambarnya turun satu layar.
                     *
                     * Yang menentukan bukan nama halamannya melainkan ada
                     * tidaknya gambar — halaman yang gambarnya belum diunggah
                     * tetap memakai judul itu sebagai pembuka isinya.
                     */
                    $tayangBergambar = !empty($data['gambar_alur']) || !empty($data['dokumen']);
                @endphp

                @if (!$tayangBergambar)
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Detail').' '.__($data['title'])) !!}</h2>
                @endif

                @if ($slug === 'maklumat-pelayanan')
                    @include('partials.db_notice')

                    @if (!empty($data['dokumen']))
                        @php
                            $dokumen = $data['dokumen'];
                        @endphp

                        {{-- Maklumat resmi dibaca utuh di halaman ini; berkasnya
                             diunggah petugas lewat modul Maklumat di CMS.

                             Halamannya sengaja tinggal dokumennya saja (langkah
                             88). Pengantar, tombol "Buka di tab baru", dan tombol
                             "Unduh Maklumat" dilepas: ketiganya mengelilingi satu
                             gambar yang isinya sudah lengkap dan sudah bisa dibuka
                             penuh dengan mengklik gambarnya sendiri. --}}
                        {{-- Lebar lembarnya dicari di antara dua kutub yang
                             keduanya sudah dicoba dan ditolak: 768 px membuat
                             tulisannya harus dizoom dulu, sedangkan selebar isi
                             halaman (±1.180 px) membuat lembar A4 ini menjulang
                             lebih tinggi dari layar dan menuntut gulir panjang
                             sebelum sampai tanda tangannya. 896 px berada di
                             tengah keduanya; sisanya diserahkan ke klik-untuk-
                             ukuran-penuh. --}}
                        <figure class="mx-auto w-full max-w-4xl">
                            @if ($dokumen['ext'] === 'pdf')
                                <div class="space-y-4 rounded-2xl border border-gray-100 dark:border-white/10 bg-[#FAF6EC] dark:bg-[#082217] p-3 sm:p-5"
                                     data-pdf-dokumen="{{ $dokumen['url'] }}"
                                     aria-label="{{ __('Isi dokumen') }}: {{ $dokumen['judul'] }}">
                                    <div data-pdf-dokumen-status class="rounded-xl border border-gray-100 dark:border-white/10 bg-white dark:bg-[#0B2A1D] py-16 text-center">
                                        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">{{ __('Memuat dokumen…') }}</p>
                                    </div>
                                </div>
                            @elseif (in_array($dokumen['ext'], ['jpg', 'jpeg', 'png', 'webp'], true))
                                {{-- Gambarnya sendiri yang jadi tautannya: satu
                                     sasaran besar, sama mudahnya disentuh di ponsel
                                     maupun diklik di layar lebar, dan zoom-nya
                                     memakai peramban — bukan overlay yang justru
                                     mengecilkan gambar lagi. --}}
                                <a href="{{ $dokumen['url'] }}" target="_blank" rel="noopener"
                                   class="group block overflow-hidden rounded-2xl border border-gray-100 dark:border-white/10 bg-white shadow-sm transition-shadow duration-300 hover:shadow-lg focus:outline-none focus-visible:ring-2 focus-visible:ring-[#10462F] focus-visible:ring-offset-2"
                                   title="{{ __('Buka gambar ukuran penuh') }}">
                                    <img src="{{ $dokumen['url'] }}" alt="{{ $dokumen['judul'] }}"
                                         loading="lazy" decoding="async"
                                         class="block w-full h-auto">
                                </a>
                            @else
                                <div class="rounded-2xl border border-gray-100 dark:border-white/10 bg-[#FAF6EC] dark:bg-[#082217] py-16 text-center">
                                    <svg class="w-10 h-10 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                    {{-- Bentuk berkas ini tidak bisa digambar di
                                         halaman. Tautan teks biasa, bukan tombol:
                                         tanpa ini halamannya buntu. --}}
                                    <a href="{{ $dokumen['url'] }}" target="_blank" rel="noopener"
                                       class="text-base font-semibold text-[#10462F] dark:text-emerald-300 underline underline-offset-4">
                                        {{ __('Buka berkas maklumat') }}
                                    </a>
                                </div>
                            @endif

                            {{-- Keterangan penerbitan ditaruh di bawah dokumennya,
                                 kecil dan satu baris: ia menerangkan gambarnya,
                                 bukan bersaing dengannya. Di layar sempit kedua
                                 keterangan turun sendiri jadi dua baris. --}}
                            <figcaption class="mt-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-center text-sm font-normal text-gray-500 dark:text-gray-400">
                                @if (!empty($dokumen['tanggal']))
                                    <span>{{ __('Terbit') }} {{ $dokumen['tanggal'] }}</span>
                                @endif
                                <span>{{ __('Diunggah oleh') }} <span class="font-semibold text-gray-700 dark:text-gray-200">{{ $dokumen['pengunggah'] ?: __('Petugas PPID') }}</span></span>
                            </figcaption>
                        </figure>
                    @else
                    <div class="p-8 rounded-2xl fs-gradient text-white relative overflow-hidden">
                        <div class="absolute top-0 right-0 w-40 h-40 bg-white/10 rounded-full -mr-16 -mt-16"></div>
                        <p class="text-xl font-semibold leading-relaxed mb-8 relative z-10">"{{ __($data['intro']) }}"</p>
                        <ul class="space-y-4 border-t border-white/20 pt-6 relative z-10">
                            @foreach ($data['content_list'] as $item)
                                <li class="flex items-start gap-3">
                                    <svg class="w-6 h-6 text-white flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                    <span class="text-base font-normal text-white/90 leading-relaxed">{{ __($item) }}</span>
                                </li>
                            @endforeach
                        </ul>
                        <p class="mt-8 text-sm font-normal text-white/70 border-t border-white/20 pt-4 relative z-10">{{ __($data['footer']) }}</p>
                    </div>
                    @endif

                @elseif (in_array($slug, ['prosedur-permohonan', 'prosedur-keberatan'], true))
                    @include('partials.db_notice')

                    {{-- Intro ikut dilewati saat ada gambar: kalimatnya
                         merangkum alur yang gambarnya justru ada tepat di
                         bawahnya. --}}
                    @if (empty($data['gambar_alur']))
                        <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-12">{{ __($data['intro']) }}</p>
                    @endif

                    {{-- Alur bergambar: infografis resmi yang diunggah petugas
                         lewat modul Alur Prosedur di CMS. Gambar inilah isi
                         halamannya — ia memperlihatkan tampilan layar yang
                         akan ditemui pemohon, bukan sekadar menyebut namanya.

                         Begitu gambarnya ada, Ringkasan Tahapan dan Rincian
                         Tahapan tidak ikut tayang (langkah 86): keduanya
                         mengulang isi gambar dengan kalimat, dan pengulangan
                         itu membuat pemohon membaca prosedur yang sama tiga
                         kali. Teksnya tidak dihapus dari kode — halaman yang
                         belum punya gambar tetap memakainya, lihat blok
                         `@else` di bawah. --}}
                    @if (!empty($data['gambar_alur']))
                        <div class="mb-16">
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">{!! $judulDua(__('Panduan Bergambar'), 1) !!}</h3>
                            <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-8">
                                {{ __('Ikuti gambar berikut secara berurutan. Klik gambar untuk membukanya dalam ukuran penuh.') }}
                            </p>

                            <div class="space-y-10">
                                @foreach ($data['gambar_alur'] as $i => $gambarAlur)
                                    <figure class="rounded-2xl border border-gray-100 dark:border-white/10 bg-[#FAF6EC] dark:bg-[#082217] overflow-hidden">
                                        <figcaption class="flex items-start gap-4 px-5 sm:px-6 py-4 border-b border-gray-100 dark:border-white/10 bg-white dark:bg-[#0B2A1D]">
                                            <span class="w-10 h-10 flex-shrink-0 flex items-center justify-center bg-[#10462F] text-white text-base font-semibold rounded-full">{{ $i + 1 }}</span>
                                            <div class="min-w-0">
                                                <h4 class="text-lg font-bold text-gray-900 dark:text-white leading-snug">{{ $gambarAlur['judul'] }}</h4>
                                                @if (!empty($gambarAlur['keterangan']))
                                                    <p class="mt-1 text-sm font-normal text-gray-600 dark:text-gray-300 leading-relaxed">{{ $gambarAlur['keterangan'] }}</p>
                                                @endif
                                            </div>
                                        </figcaption>

                                        {{-- Tautan biasa, bukan lightbox: tulisan di
                                             infografis ini kecil, dan tab baru memberi
                                             pemohon zoom bawaan peramban — termasuk di
                                             ponsel, tempat overlay justru mengecilkan
                                             gambarnya lagi. --}}
                                        <a href="{{ $gambarAlur['url'] }}" target="_blank" rel="noopener"
                                           class="block group"
                                           title="{{ __('Buka gambar ukuran penuh') }}">
                                            <img src="{{ $gambarAlur['url'] }}" alt="{{ $gambarAlur['judul'] }}"
                                                 loading="lazy" decoding="async"
                                                 class="w-full h-auto group-hover:opacity-95 transition-opacity duration-300">
                                        </a>
                                    </figure>
                                @endforeach
                            </div>
                        </div>

                    @else

                    {{-- Alur ringkas berupa kartu bernomor. Untuk Prosedur
                         Permohonan, blok ini dipindahkan dari beranda. Kini
                         hanya tayang pada halaman prosedur yang belum punya
                         gambar alur — hari ini Prosedur Keberatan. --}}
                    @if (!empty($data['flow']))
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-x-5 lg:gap-x-6 mb-14">
                            @foreach ($data['flow'] as $i => $langkah)
                                {{-- Wrapper relatif (tanpa overflow) agar konektor tidak terpotong --}}
                                <div class="group relative h-full">
                                    <div class="relative h-full {{ $cardTier($i) }} rounded-3xl p-7 shadow-xl shadow-accent-200/60 dark:shadow-black/30 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 overflow-hidden">
                                        {{-- Nomor watermark --}}
                                        <span class="absolute -top-3 -right-1 text-[6rem] leading-none font-extrabold text-white/25 group-hover:text-white/40 transition-colors select-none">{{ $i + 1 }}</span>

                                        <div class="relative z-10">
                                            <div class="flex items-center gap-3 mb-5">
                                                <div class="w-14 h-14 bg-white text-[#E87317] rounded-full flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $langkah['icon'] }}"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-white uppercase tracking-widest">{{ __('Langkah') }} {{ $i + 1 }}</span>
                                            </div>
                                            <h3 class="text-lg font-bold text-white mb-1.5">{{ __($langkah['title']) }}</h3>
                                            <p class="text-sm text-white/90 leading-relaxed">{{ __($langkah['desc']) }}</p>
                                        </div>
                                    </div>

                                    {{-- Line penghubung + panah antar kartu dalam 1 baris (desktop) --}}
                                    @if (($i + 1) % 3 !== 0 && $i + 1 < count($data['flow']))
                                        <div class="hidden lg:block absolute top-1/2 left-full -translate-y-1/2 w-6 z-20">
                                            <div class="h-[3px] w-full fs-gradient-accent rounded-full opacity-70"></div>
                                            <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white dark:bg-[#0B2A1D] border border-accent-200 shadow-md flex items-center justify-center text-[#E87317]">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>

                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-8">{!! $judulDua(__('Rincian Tahapan'), 1) !!}</h3>
                    @endif

                    <div class="relative pl-6">
                        <div class="absolute top-2 bottom-2 left-6 w-0.5 bg-emerald-100"></div>
                        <div class="space-y-8">
                            @foreach ($data['steps'] as $index => $step)
                                <div class="relative flex items-start gap-5">
                                    <span class="w-12 h-12 flex-shrink-0 flex items-center justify-center bg-[#10462F] text-white text-base font-semibold rounded-full relative z-10 -ml-6 ring-4 ring-white">{{ $index + 1 }}</span>
                                    <div class="flex-1 p-6 bg-[#FAF6EC] dark:bg-[#082217] rounded-2xl border border-gray-100 dark:border-white/10">
                                        <p class="text-base font-normal text-gray-700 dark:text-gray-300 leading-relaxed">{{ __($step) }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    @endif

                    <div class="mt-12 text-center">
                        @if ($slug === 'prosedur-keberatan')
                            <a href="{{ route('ppid.objection') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#10462F] hover:bg-[#0B3524] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                                {{ __('Ajukan Keberatan') }}
                            </a>
                        @else
                            <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#10462F] hover:bg-[#0B3524] text-white text-base font-semibold rounded-xl transition-colors duration-300">
                                {{ __('Mulai Ajukan Permohonan') }}
                            </a>
                        @endif
                    </div>

                @elseif ($slug === 'jalur-waktu-layanan')
                    <p class="text-base font-normal text-gray-600 dark:text-gray-300 leading-relaxed mb-10">{{ __($data['intro']) }}</p>
                    {{-- Panel Waktu Layanan tertutup saat halaman dibuka dan
                         terbuka lewat kartu jalur "Langsung" — jam operasional
                         hanya berarti bagi yang memang datang ke kantor. --}}
                    <div class="space-y-6" x-data="{ waktuTerbuka: false }">
                        {{-- Jalur Pelayanan --}}
                        <div class="p-6 sm:p-8 rounded-2xl bg-[#FAF6EC] dark:bg-[#082217] border border-gray-100 dark:border-white/10">
                            <div class="flex items-center gap-4 mb-6">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#10462F] rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                                </span>
                                <h3 class="text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white min-w-0">{{ __('Jalur Pelayanan') }}</h3>
                            </div>
                            @php
                                $channelIcons = [
                                    'Online' => 'M21 12a9 9 0 11-18 0 9 9 0 0118 0zM3.6 9h16.8M3.6 15h16.8M12 3c2.5 3 2.5 15 0 18-2.5-3-2.5-15 0-18z',
                                    'Langsung' => 'M17 20h5v-2a3 3 0 00-5.36-1.86M17 20H7m10 0v-2c0-.66-.13-1.3-.36-1.86M7 20H2v-2a3 3 0 015.36-1.86M7 20v-2c0-.66.13-1.3.36-1.86m0 0A5 5 0 0112 13a5 5 0 014.64 3.14M15 7a3 3 0 11-6 0 3 3 0 016 0z',
                                ];

                                // Kelas kartunya sama, hanya elemen pembungkusnya
                                // yang berbeda: satu tautan, satu tombol.
                                $kelasKartu = 'h-full w-full text-left flex flex-col rounded-xl p-5 shadow-lg shadow-accent-200/60 dark:shadow-black/30 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#175A3C]';
                            @endphp
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach ($data['channels'] as $i => $channel)
                                    @php
                                        $aksi = $channel['aksi'] ?? null;
                                        $petunjuk = $aksi === 'masuk'
                                            ? __('Masuk ke Portal Pemohon')
                                            : ($aksi === 'waktu' ? __('Lihat waktu layanan & lokasi') : null);
                                    @endphp

                                    {{-- Isi kartunya identik untuk kedua jalur;
                                         hanya pembungkusnya yang berbeda. --}}
                                    @php
                                        $isiKartu = view('partials.jalur_layanan_isi', [
                                            'channel' => $channel,
                                            'ikon' => $channelIcons[$channel['label']] ?? $channelIcons['Langsung'],
                                            'petunjuk' => $petunjuk,
                                        ])->render();
                                    @endphp

                                    @if ($aksi === 'masuk')
                                        <a href="{{ route('akun.login') }}" class="{{ $kelasKartu }} {{ $cardTier($i) }}">
                                            {!! $isiKartu !!}
                                        </a>
                                    @elseif ($aksi === 'waktu')
                                        <button type="button"
                                                @click="waktuTerbuka = !waktuTerbuka; if (waktuTerbuka) $nextTick(() => $refs.waktuLayanan.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))"
                                                :aria-expanded="waktuTerbuka ? 'true' : 'false'"
                                                aria-controls="panel-waktu-layanan"
                                                class="{{ $kelasKartu }} {{ $cardTier($i) }}">
                                            {!! $isiKartu !!}
                                        </button>
                                    @else
                                        <div class="{{ $kelasKartu }} {{ $cardTier($i) }}">
                                            {!! $isiKartu !!}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        {{-- Waktu Layanan --}}
                        <div x-ref="waktuLayanan" class="p-6 sm:p-8 rounded-2xl bg-[#FAF6EC] dark:bg-[#082217] border border-gray-100 dark:border-white/10">
                            <button type="button"
                                    @click="waktuTerbuka = !waktuTerbuka"
                                    :aria-expanded="waktuTerbuka ? 'true' : 'false'"
                                    aria-controls="panel-waktu-layanan"
                                    class="w-full flex items-center gap-4 text-left focus:outline-none focus-visible:ring-2 focus-visible:ring-[#175A3C] rounded-xl"
                                    :class="waktuTerbuka ? 'mb-6' : ''">
                                <span class="w-12 h-12 sm:w-14 sm:h-14 bg-emerald-50 text-[#10462F] rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6 sm:w-7 sm:h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </span>
                                <span class="min-w-0 flex-1">
                                    <span class="block text-xl sm:text-[22px] font-semibold text-gray-900 dark:text-white">{{ __('Waktu Layanan') }}</span>
                                    <span class="block text-sm font-normal text-gray-500 dark:text-gray-400" x-show="!waktuTerbuka">{{ __('Jam operasional & lokasi kantor PPID') }}</span>
                                </span>
                                <svg class="w-5 h-5 flex-shrink-0 text-gray-500 dark:text-gray-400 transition-transform" :class="waktuTerbuka ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <div id="panel-waktu-layanan" x-show="waktuTerbuka" x-collapse x-cloak>
                            {{-- Satu kartu per baris jam; jam istirahat hanya
                                 tampil bila memang diumumkan. --}}
                            <div class="grid grid-cols-1 {{ count($data['hours']) > 1 ? 'sm:grid-cols-2' : '' }} gap-4">
                                @foreach ($data['hours'] as $schedule)
                                    <div class="rounded-xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10 p-5 shadow-sm">
                                        <p class="text-[11px] font-bold uppercase tracking-[0.14em] text-[#10462F] dark:text-[#3E9C6C]">{{ __($schedule['days']) }}</p>
                                        <p class="mt-2 text-lg sm:text-xl font-bold text-gray-900 dark:text-white tabular-nums break-words">{{ $schedule['time'] }}</p>
                                        @if (!empty($schedule['break']))
                                            <p class="mt-2 flex items-start gap-1.5 text-xs font-normal text-gray-500 dark:text-gray-400">
                                                <svg class="w-4 h-4 mt-px flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8h2a2 2 0 012 2v2a2 2 0 01-2 2h-2M5 8h12v7a4 4 0 01-4 4H9a4 4 0 01-4-4V8zM3 21h18"></path></svg>
                                                <span class="tabular-nums">{{ __('Istirahat') }} {{ $schedule['break'] }}</span>
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <p class="mt-5 flex items-start gap-2.5 text-sm font-normal text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-white/10 pt-4 leading-relaxed">
                                <svg class="w-4 h-4 mt-0.5 flex-shrink-0 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ __($data['note']) }}</span>
                            </p>

                            {{-- Jam layanan hanya berguna kalau pemohon tahu
                                 harus datang ke mana. --}}
                            <div class="mt-6 border-t border-gray-200 dark:border-white/10 pt-6">
                                <h4 class="text-sm font-bold uppercase tracking-[0.14em] text-[#10462F] dark:text-[#3E9C6C] mb-4">{{ __('Lokasi Kantor PPID') }}</h4>
                                @include('partials.peta_lokasi', ['tinggi' => 'h-[320px]'])
                            </div>
                            </div>
                        </div>
                    </div>

                @else
                    <div class="text-center py-12 bg-red-50 border border-red-100 rounded-2xl">
                        <p class="text-red-600 font-semibold text-lg">{{ __('Konten Standar Pelayanan tidak ditemukan.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>

@endsection
