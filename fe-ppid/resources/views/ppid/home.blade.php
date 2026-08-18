@extends('layouts.app')

@section('title', __('PPID FSTJ - Transparansi Informasi Publik'))
@section('content')

    {{-- Data beranda ($infoPublik, $news, $faqs, $heroSlides, $contacts)
         dikirim HomeController dari CMS. Yang tersisa di bawah ini murni elemen
         tampilan: ikon dan tautan menu layanan. --}}
    @php
        $quickServices = [
            ['title' => 'Permohonan Informasi', 'desc' => 'Ajukan permohonan resmi', 'route' => route('ppid.request'), 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            ['title' => 'Pengajuan Keberatan', 'desc' => 'Sampaikan keberatan Anda', 'route' => route('ppid.objection'), 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z'],
            ['title' => 'Cek Status Tiket', 'desc' => 'Lacak permohonan Anda', 'route' => route('ppid.status'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
        ];

        // $cardTier (rotasi tiga nada oranye untuk latar kartu) di-share global
        // dari AppServiceProvider.

    @endphp

    {{-- =====================================================================
         1. HERO — slider full-bleed: gambar mengisi seluruh lebar & tinggi
            hero, teks + CTA menumpuk di atasnya (mengikuti theme-slider-beranda.jpeg)

            Di layar besar hero mengisi satu layar penuh. Header beranda
            melayang transparan di atasnya, jadi tingginya satu viewport utuh
            (bukan dikurangi tinggi header) dan isinya diberi jarak atas supaya
            tidak tertimpa header. Ukuran unggahan yang dianjurkan:
            1920 × 1080 px (16:9).

            Tiap banner boleh membawa judul dan ringkasannya sendiri (modul
            Banner di CMS). Banner tanpa judul memakai teks bawaan di bawah ini.
         ===================================================================== --}}
    @php
        // Slider baru memunculkan blok teks per slide bila ada minimal satu
        // banner yang punya judul; kalau semua banner hanya gambar, teks
        // bawaan beranda yang dipakai (satu blok, tidak ikut berganti).
        $adaTeksSlide = collect($heroSlides)->contains(fn ($s) => filled($s['caption'] ?? null));
        $judulBawaan = __('Portal Keterbukaan');
        $judulBawaanAksen = __('Informasi Publik');
        $ringkasanBawaan = __('Wujud tata kelola perusahaan yang baik dan akuntabel — melaksanakan keterbukaan informasi publik sesuai Undang-Undang No. 14 Tahun 2008.');
    @endphp

    <section class="relative overflow-hidden fs-gradient"
             x-data="{ active: 0, total: {{ max(count($heroSlides), 1) }}, timer: null,
                       start() { if (this.total > 1) this.timer = setInterval(() => this.next(), 7000) },
                       next() { this.active = (this.active + 1) % this.total },
                       prev() { this.active = (this.active - 1 + this.total) % this.total },
                       go(i) { this.active = i; clearInterval(this.timer); this.start() } }"
             x-init="start()"
             @mouseenter="clearInterval(timer)" @mouseleave="start()">

        {{-- Lapisan gambar slider (di belakang teks) --}}
        <div class="absolute inset-0">
            @foreach ($heroSlides as $i => $slide)
                <div x-show="active === {{ $i }}"
                     x-transition:enter="transition ease-out duration-1000"
                     x-transition:enter-start="opacity-0 scale-105"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-700"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="absolute inset-0"
                     @if ($i !== 0) style="display:none" @endif>
                    {{-- Gambar yang sedang tayang merayap membesar (Ken Burns)
                         supaya slider terasa bergerak, bukan gambar diam. --}}
                    <img src="{{ $slide['image'] }}" alt="{{ $slide['caption'] }}"
                         class="w-full h-full object-cover object-center"
                         :class="active === {{ $i }} ? 'fs-kenburns' : ''"
                         @if ($i === 0) fetchpriority="high" @else loading="lazy" @endif>
                </div>
            @endforeach

            {{-- Scrim: gelap di kiri supaya teks tetap terbaca, makin ke kanan
                 makin bening supaya gambar bannernya sendiri tetap terlihat
                 jelas — bukan tertutup lapisan gelap satu halaman penuh. --}}
            <div class="absolute inset-0 bg-gradient-to-r from-[#041710]/90 via-[#041710]/55 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-[#041710]/55 to-transparent"></div>
        </div>

        {{-- Konten hero sekaligus penentu tinggi hero: satu layar penuh
             (dibatasi 1100 px supaya di layar yang sangat jangkung banner tidak
             jadi terlalu panjang). Jarak atas menghindari header melayang. --}}
        <div class="relative z-10 flex items-center min-h-[560px] lg:min-h-screen lg:max-h-[1100px] pt-[104px] pb-20 lg:pb-24">
            <div class="w-full max-w-7xl mx-auto px-6 lg:px-8">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/25 text-white/90 text-xs font-semibold tracking-wide uppercase mb-6 backdrop-blur-sm">
                        <span class="w-2 h-2 rounded-full bg-[#F5A94C] animate-pulse"></span>
                        {{ __('PPID PT Food Station Tjipinang Jaya') }}
                    </span>

                    @if ($adaTeksSlide)
                        {{-- Judul + ringkasan milik slide yang sedang tayang.
                             Semua slide ditumpuk pada satu sel grid supaya tinggi
                             bloknya mengikuti teks terpanjang — tombol di bawahnya
                             tidak melompat saat slide berganti. --}}
                        <div class="grid mb-9">
                            @foreach ($heroSlides as $i => $slide)
                                <div class="col-start-1 row-start-1"
                                     x-show="active === {{ $i }}"
                                     x-transition:enter="transition ease-out duration-700 delay-150"
                                     x-transition:enter-start="opacity-0 translate-y-6"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-300"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     @if ($i !== 0) style="display:none" @endif>
                                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] drop-shadow-lg">
                                        @if (filled($slide['caption']))
                                            {{ $slide['caption'] }}
                                        @else
                                            {{ $judulBawaan }}<br><span class="fs-title-accent-soft">{{ $judulBawaanAksen }}</span>
                                        @endif
                                    </h1>
                                    <p class="text-lg text-white/90 max-w-xl mt-6 leading-relaxed drop-shadow">
                                        {{ filled($slide['ringkasan'] ?? null) ? $slide['ringkasan'] : $ringkasanBawaan }}
                                    </p>

                                    @if (!empty($slide['link']))
                                        <a href="{{ $slide['link'] }}" class="inline-flex items-center gap-2 mt-6 text-base font-bold text-[#F5A94C] hover:text-white transition-colors">
                                            {{ __('Selengkapnya') }}
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                        </a>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Baris kedua memakai warna aksen (konsep judul dua warna). --}}
                        <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-6 drop-shadow-lg">
                            {{ $judulBawaan }}<br><span class="fs-title-accent-soft">{{ $judulBawaanAksen }}</span>
                        </h1>
                        <p class="text-lg text-white/90 max-w-xl mb-9 leading-relaxed drop-shadow">
                            {{ $ringkasanBawaan }}
                        </p>
                    @endif

                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 fs-gradient-accent text-white text-base font-bold rounded-xl hover:brightness-110 hover:-translate-y-0.5 transition-all duration-200 shadow-xl">
                            {{ __('Ajukan Permohonan') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="{{ route('ppid.status') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-white/10 border border-white/40 text-white text-base font-bold rounded-xl backdrop-blur-sm hover:bg-white/20 transition-colors duration-200">
                            {{ __('Cek Status Permohonan') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if (count($heroSlides) > 1)
            {{-- Judul slide sudah tampil pada blok teks utama, jadi caption
                 terpisah di pojok kanan bawah dilepas — isinya kembar. Penanda
                 nomor slide juga dilepas; posisi slide sudah terbaca dari
                 titik-titik di bawah banner. --}}

            {{-- Prev / Next --}}
            <button @click="prev()" aria-label="{{ __('Slide sebelumnya') }}"
                    class="absolute z-20 top-1/2 -translate-y-1/2 left-3 lg:left-6 w-11 h-11 rounded-full bg-white/15 border border-white/30 text-white backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
            </button>
            <button @click="next()" aria-label="{{ __('Slide berikutnya') }}"
                    class="absolute z-20 top-1/2 -translate-y-1/2 right-3 lg:right-6 w-11 h-11 rounded-full bg-white/15 border border-white/30 text-white backdrop-blur-sm flex items-center justify-center hover:bg-white/30 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
            </button>

            {{-- Dots --}}
            <div class="absolute z-20 bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                @foreach ($heroSlides as $i => $slide)
                    <button @click="go({{ $i }})" aria-label="{{ __('Slide') }} {{ $i + 1 }}"
                            class="h-2 rounded-full transition-all duration-300"
                            :class="active === {{ $i }} ? 'w-8 bg-[#E87317]' : 'w-2 bg-white/60 hover:bg-white'"></button>
                @endforeach
            </div>
        @endif
    </section>

    {{-- 3. STATISTIK — dipindah ke section 5 (Alur Permohonan), tepat di atas label "Prosedur" --}}

    {{-- =====================================================================
         2b. SECTION LAYANAN CEPAT — background putih, section terpisah penuh
             (tidak menggantung / tidak menimpa section Kategori Informasi)
         ===================================================================== --}}
    <section class="py-16 lg:py-20 bg-white dark:bg-[#0B2A1D]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                <div>
                    <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#10462F] dark:text-[#3E9C6C]">
                        <span class="w-6 h-px bg-[#10462F]/40 dark:bg-[#3E9C6C]/40"></span>
                        {{ __('Layanan Cepat') }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5">{!! $judulDua(__('Mulai Layanan dari Sini'), 2) !!}</h2>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 sm:text-right">{{ __('Tiga layanan utama PPID dalam satu klik.') }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach ($quickServices as $i => $qs)
                    <a href="{{ $qs['route'] }}" class="group {{ $cardTier($i) }} rounded-3xl shadow-xl shadow-accent-200/60 dark:shadow-black/30 p-8 text-center hover:-translate-y-1.5 hover:shadow-2xl transition-all duration-300">
                        <div class="w-16 h-16 mx-auto mb-5 bg-white text-[#E87317] rounded-full flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $qs['icon'] }}"></path></svg>
                        </div>
                        <p class="text-lg font-bold text-white mb-1.5">{{ __($qs['title']) }}</p>
                        <p class="text-sm text-white/90 mb-4">{{ __($qs['desc']) }}</p>
                        <span class="inline-flex items-center gap-1.5 text-white font-semibold text-sm">
                            {{ __('Selengkapnya') }}
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         4. INFORMASI PUBLIK
         ===================================================================== --}}
    <section class="relative py-16 lg:py-24 overflow-hidden">
        {{-- Background gambar --}}
        <div class="absolute inset-0">
            <img src="https://images.unsplash.com/photo-1607113256158-56a934936ef1?q=80&w=1600&auto=format&fit=crop" alt="" class="w-full h-full object-cover">
            <div class="absolute inset-0 fs-gradient opacity-[0.96]"></div>
            <div class="absolute inset-0 fs-dot-pattern opacity-25"></div>
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-sm font-bold text-white/70 uppercase tracking-widest">{{ __('Kategori Informasi') }}</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-white mt-3">{!! $judulDua(__('Informasi Publik'), 1, 'fs-title-accent-soft') !!}</h2>
                <p class="text-white/80 mt-4 leading-relaxed">{{ __('Tiga klasifikasi informasi publik yang dapat diakses masyarakat sesuai peraturan keterbukaan informasi.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($infoPublik as $i => $ip)
                    <div class="group {{ $cardTier($i) }} rounded-3xl p-8 shadow-xl shadow-black/20 hover:-translate-y-1 hover:shadow-2xl transition-all duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 bg-white text-[#E87317] rounded-full flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-3xl font-extrabold text-white">{{ $ip['count'] }}</span>
                        </div>
                        <p class="text-xl font-bold text-white mb-2">{{ __($ip['title']) }}</p>
                        <p class="text-sm text-white/90 leading-relaxed mb-6">{{ __($ip['desc']) }}</p>
                        <a href="{{ route('ppid.information', $ip['slug']) }}" class="inline-flex items-center gap-1.5 text-white font-semibold text-sm underline-offset-4 hover:underline">
                            {{ __('Lihat') }} {{ $ip['count'] }} {{ __('Dokumen') }}
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         6. BERITA
         ===================================================================== --}}
    <section id="berita" class="py-16 lg:py-24 bg-white dark:bg-[#0B2A1D]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="text-sm font-bold text-[#10462F] uppercase tracking-widest">{{ __('Kabar Terbaru') }}</span>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{!! $judulDua(__('Berita & Publikasi'), 2) !!}</h2>
                </div>
                <a href="{{ route('ppid.news.index') }}" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#10462F] hover:text-[#0B3524]">
                    {{ __('Lihat Semua') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                @forelse ($news as $item)
                    <a href="{{ $item['url'] }}" class="group h-full flex flex-col bg-white dark:bg-[#0B2A1D] rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-1 transition-all duration-300">
                        <div class="relative h-48 flex-shrink-0 overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-4 left-4 fs-gradient text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">{{ $item['category'] }}</span>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">{{ $item['date'] }}</p>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#10462F] transition-colors">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 leading-relaxed line-clamp-3 mb-4">{{ $item['excerpt'] }}</p>
                            <span class="mt-auto inline-flex items-center gap-1.5 text-[#10462F] font-semibold text-sm">
                                {{ __('Baca Selengkapnya') }}
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                        </div>
                    </a>
                @empty
                    <p class="md:col-span-3 rounded-2xl border border-gray-100 bg-white p-8 text-center text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">
                        {{ __('Belum ada berita yang diterbitkan.') }}
                    </p>
                @endforelse
            </div>
        </div>
    </section>

    {{-- =====================================================================
         7. BANTUAN + KONTAK (1 section, 2 kolom 6/6)
         ===================================================================== --}}
    <section id="kontak-section" class="py-16 lg:py-24 bg-[#F3ECDD] dark:bg-[#082217]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">

                {{-- Kolom kiri: FAQ --}}
                <div>
                    <div class="mb-8">
                        <span class="text-sm font-bold text-[#10462F] uppercase tracking-widest">{{ __('Bantuan') }}</span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{!! $judulDua(__('Pertanyaan Umum')) !!}</h2>
                    </div>
                    <div class="space-y-3">
                        @forelse ($faqs as $faq)
                            <div x-data="{ open: false }" class="bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden" :class="open ? 'shadow-lg shadow-gray-200/60' : ''">
                                <button @click="open = !open" class="w-full flex items-center justify-between gap-4 px-6 py-5 text-left">
                                    <span class="text-base font-semibold text-gray-900 dark:text-white">{{ __($faq['q']) }}</span>
                                    <span class="w-8 h-8 flex-shrink-0 rounded-lg flex items-center justify-center transition-colors duration-300" :class="open ? 'fs-gradient text-white' : 'bg-gray-100 text-gray-400'">
                                        <svg class="w-5 h-5 transition-transform duration-300" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </span>
                                </button>
                                <div x-show="open" x-collapse style="display:none">
                                    <p class="px-6 pb-5 text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __($faq['a']) }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="rounded-2xl border border-gray-100 bg-white p-6 text-base text-gray-500 dark:border-white/10 dark:bg-[#0B2A1D] dark:text-gray-400">{{ __('Belum ada pertanyaan umum yang dipublikasikan.') }}</p>
                @endforelse
                    </div>
                </div>

                {{-- Kolom kanan: Kontak --}}
                <div>
                    <div class="mb-8">
                        <span class="text-sm font-bold text-[#10462F] uppercase tracking-widest">{{ __('Hubungi Kami') }}</span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{!! $judulDua(__('Kontak PPID Food Station'), 2) !!}</h2>
                    </div>
                    {{-- $contacts berasal dari modul Pengaturan Situs di CMS. --}}
                    <div class="space-y-3 mb-6">
                        @foreach ($contacts as $c)
                            <div class="flex gap-4 p-5 rounded-2xl bg-white dark:bg-[#0B2A1D] border border-gray-100 dark:border-white/10">
                                <div class="w-11 h-11 flex-shrink-0 fs-gradient text-white rounded-xl flex items-center justify-center">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $c['icon'] }}"></path></svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-semibold text-gray-900 dark:text-white mb-1">{{ __($c['label']) }}</dt>
                                    <dd class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ $c['value'] }}</dd>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    {{-- Peta dipisah ke partial supaya titik lokasinya sama
                         dengan yang tampil di halaman Standar Layanan. --}}
                    @include('partials.peta_lokasi', ['tinggi' => 'h-[260px]'])
                </div>

            </div>
        </div>
    </section>

    {{-- MODAL DOWNLOAD — wajib masuk sebagai pengunjung; identitas dari akun. --}}
    @php $akunUnduh = auth('pemohon')->user(); @endphp
    <div x-data="{
        showModal: false,
        isSubmitting: false,
        formSuccess: false,
        reportTitle: '',
        reportId: null,
        sudahMasuk: {{ $akunUnduh ? 'true' : 'false' }},
        user: @js([
            'name' => $akunUnduh?->nama,
            'phone' => $akunUnduh?->no_hp,
            'email' => $akunUnduh?->email,
        ]),
        successEmail: '',
        submitDownloadForm: async function() {
            this.isSubmitting = true;

            try {
                const res = await fetch('{{ route('report.download') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ laporan_id: this.reportId })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    this.successEmail = data.email;
                    this.formSuccess = true;
                } else if (res.status === 401) {
                    window.location = data.login_url || '{{ route('akun.login') }}';
                } else {
                    alert(data.message || '{{ __('Tautan gagal dikirim. Coba lagi beberapa saat lagi.') }}');
                }
            } catch (e) {
                alert('{{ __('Terjadi kesalahan jaringan atau server.') }}');
            } finally {
                this.isSubmitting = false;
            }
        }
    }"
    @open-download-modal.window="
        showModal = true;
        reportTitle = $event.detail.title;
        reportId = $event.detail.id;
        formSuccess = false;
    "
    x-show="showModal"
    style="display: none"
    class="fixed inset-0 z-[100] overflow-y-auto"
    role="dialog"
    aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity @click="showModal = false" class="fixed inset-0 bg-[#071A12]/60 backdrop-blur-sm transition-opacity"></div>

            <div x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                class="relative inline-block align-bottom bg-white dark:bg-[#0B2A1D] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full w-full">

                <div class="fs-gradient px-8 py-6 relative overflow-hidden">
                    <div class="absolute inset-0 fs-dot-pattern opacity-30"></div>
                    <div class="flex justify-between items-center relative z-10">
                        <h3 class="text-xl font-bold text-white">{{ __('Formulir Unduh Dokumen') }}</h3>
                        <button @click="showModal = false" class="w-8 h-8 flex items-center justify-center rounded-full bg-white/15 text-white hover:bg-white/25 transition-colors duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                </div>

                <div class="px-8 pt-7 pb-8">
                    {{-- Belum masuk: tidak ada formulir, langsung diarahkan ke login. --}}
                    <div x-show="!sudahMasuk" class="py-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                            {{ __('Tautan unduh dikirim ke email akun Anda. Masuk dulu untuk meminta') }}
                            <span class="font-semibold text-gray-900 dark:text-white" x-text="reportTitle"></span>.
                        </p>
                        <a href="{{ route('akun.login') }}" class="mt-6 inline-flex items-center justify-center px-6 py-3.5 fs-gradient-accent text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-300">
                            {{ __('Masuk / Daftar Akun') }}
                        </a>
                    </div>

                    <div x-show="sudahMasuk && !formSuccess">
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-6">{{ __('Tautan unduh') }} <span class="font-semibold text-gray-900 dark:text-white" x-text="reportTitle"></span> {{ __('akan dikirim ke email akun Anda.') }}</p>
                        <form @submit.prevent="submitDownloadForm()" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Nama Lengkap') }}</label>
                                <input x-model="user.name" type="text" disabled class="w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl dark:bg-[#0B2A1D] outline-none text-base opacity-70 cursor-not-allowed">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Surat Elektronik (Email)') }}</label>
                                <input x-model="user.email" type="email" disabled class="w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl dark:bg-[#0B2A1D] outline-none text-base opacity-70 cursor-not-allowed">
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                {{ __('Data belum lengkap?') }}
                                <a href="{{ route('akun.profil') }}" class="font-semibold text-[#E87317] hover:underline">{{ __('Perbarui di Data Diri Saya') }}</a>.
                            </p>
                            <div class="pt-2">
                                <button type="submit" :disabled="isSubmitting" class="w-full flex items-center justify-center px-6 py-3.5 fs-gradient-accent text-white text-base font-semibold rounded-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed shadow-lg shadow-emerald-900/20">
                                    <span x-show="!isSubmitting">{{ __('Kirim Tautan Unduhan') }}</span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        {{ __('Memproses...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="sudahMasuk && formSuccess" class="py-6 text-center">
                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-8 h-8 text-[#10462F]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __('Permintaan Berhasil') }}</h3>
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 leading-relaxed">
                            {{ __('Tautan unduhan telah dikirim ke alamat email:') }}<br>
                            <span class="font-semibold text-gray-900 dark:text-white block mt-2" x-text="successEmail"></span>
                        </p>
                        <button @click="showModal = false" class="mt-7 px-6 py-2.5 bg-gray-100 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-200 transition-colors duration-300">
                            {{ __('Tutup') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
