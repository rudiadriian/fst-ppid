@extends('layouts.app')

@section('title', 'PPID FSTJ - Transparansi Informasi Publik')

@section('content')

    @php
        $stats = [
            ['value' => '2.154', 'label' => 'Permohonan'],
            ['value' => '125', 'label' => 'Dokumen'],
            ['value' => '12', 'label' => 'Regulasi'],
            ['value' => '98%', 'label' => 'Kepuasan'],
        ];

        $quickServices = [
            ['title' => 'Permohonan Informasi', 'desc' => 'Ajukan permohonan resmi', 'route' => route('ppid.request'), 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            ['title' => 'Pengajuan Keberatan', 'desc' => 'Sampaikan keberatan Anda', 'route' => route('ppid.objection'), 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z'],
            ['title' => 'Cek Status Tiket', 'desc' => 'Lacak permohonan Anda', 'route' => route('ppid.status'), 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['title' => 'Unduh Dokumen', 'desc' => 'Laporan & arsip resmi', 'route' => '#dokumen', 'icon' => 'M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4'],
        ];

        $infoPublik = [
            ['title' => 'Informasi Berkala', 'count' => 112, 'slug' => 'berkala', 'desc' => 'Informasi yang wajib disediakan dan diumumkan secara berkala.'],
            ['title' => 'Informasi Setiap Saat', 'count' => 85, 'slug' => 'setiap-saat', 'desc' => 'Informasi yang wajib tersedia setiap saat bagi publik.'],
            ['title' => 'Informasi Serta Merta', 'count' => 18, 'slug' => 'serta-merta', 'desc' => 'Informasi yang wajib diumumkan tanpa penundaan.'],
        ];

        $timelineSteps = [
            'Ajukan Permohonan',
            'Verifikasi',
            'Diproses',
            'Persetujuan PPID',
            'Informasi Dikirim',
            'Selesai',
        ];

        $news = [
            [
                'title' => 'Food Station Jaga Stabilitas Pasokan Beras Jelang Hari Raya',
                'date' => '10 Desember 2025',
                'category' => 'Operasional',
                'excerpt' => 'PT Food Station Tjipinang Jaya memastikan ketersediaan stok beras di Pasar Induk Beras Cipinang tetap aman menjelang periode permintaan tinggi.',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=700&auto=format&fit=crop',
            ],
            [
                'title' => 'Raih Penghargaan Top BUMD 2025 Kategori Transparansi Publik',
                'date' => '05 Desember 2025',
                'category' => 'Prestasi',
                'excerpt' => 'Penghargaan diberikan atas komitmen perusahaan dalam menjalankan keterbukaan informasi publik sesuai regulasi yang berlaku.',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=700&auto=format&fit=crop',
            ],
            [
                'title' => 'Program Pangan Murah Bersubsidi Kembali Digelar di 5 Wilayah',
                'date' => '01 Desember 2025',
                'category' => 'CSR',
                'excerpt' => 'Program ini menyasar wilayah dengan kebutuhan pangan bersubsidi tinggi, bekerja sama dengan pemerintah daerah setempat.',
                'image' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?q=80&w=700&auto=format&fit=crop',
            ],
        ];

        $reports = [
            ['title' => 'Laporan Tahunan', 'year' => 2025, 'size' => '2.1 MB'],
            ['title' => 'Laporan Keuangan & Kinerja', 'year' => 2024, 'size' => '2.4 MB'],
            ['title' => 'Laporan Keberlanjutan (Sustainability)', 'year' => 2023, 'size' => '2.8 MB'],
        ];

        $faqs = [
            ['q' => 'Bagaimana cara mengajukan permohonan informasi publik?', 'a' => 'Permohonan dapat diajukan secara daring melalui menu Permohonan Informasi pada portal ini dengan mengisi formulir yang tersedia, atau secara langsung ke kantor PPID pada jam layanan.'],
            ['q' => 'Berapa lama waktu tanggapan atas permohonan?', 'a' => 'Sesuai UU No. 14 Tahun 2008, PPID wajib menanggapi permohonan paling lambat 10 hari kerja sejak permohonan diterima, dan dapat diperpanjang 7 hari kerja apabila diperlukan.'],
            ['q' => 'Apakah layanan informasi publik dikenakan biaya?', 'a' => 'Layanan informasi publik tidak dipungut biaya, kecuali biaya penggandaan atau pengiriman dokumen yang besarannya wajar dan diinformasikan di awal.'],
            ['q' => 'Apa yang bisa dilakukan jika permohonan ditolak?', 'a' => 'Pemohon dapat mengajukan keberatan secara tertulis kepada atasan PPID melalui menu Pengajuan Keberatan pada portal ini, paling lambat 30 hari kerja sejak diterimanya penolakan.'],
            ['q' => 'Bagaimana cara melacak status permohonan yang sudah diajukan?', 'a' => 'Gunakan menu Cek Status Layanan dan masukkan nomor tiket yang diterima saat pengajuan untuk melihat perkembangan proses secara real-time.'],
        ];

        // Slider gambar HERO (Portal Keterbukaan Informasi Publik)
        $heroSlides = [
            ['image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1200&auto=format&fit=crop', 'caption' => 'Gudang & Distribusi Pangan'],
            ['image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=1200&auto=format&fit=crop', 'caption' => 'Ketahanan Stok Beras'],
            ['image' => 'https://images.unsplash.com/photo-1454165804606-c3d57bc86b40?q=80&w=1200&auto=format&fit=crop', 'caption' => 'Tata Kelola Transparan'],
            ['image' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?q=80&w=1200&auto=format&fit=crop', 'caption' => 'Pelayanan Informasi Publik'],
        ];

        // Slider gambar ARSIP RESMI / LAPORAN
        $arsipSlides = [
            ['image' => 'https://images.unsplash.com/photo-1450101499163-c8848c66ca85?q=80&w=1400&auto=format&fit=crop', 'title' => 'Laporan Tahunan 2025', 'subtitle' => 'Kinerja & pencapaian perusahaan sepanjang tahun'],
            ['image' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?q=80&w=1400&auto=format&fit=crop', 'title' => 'Laporan Keuangan & Kinerja', 'subtitle' => 'Transparansi keuangan yang telah diaudit'],
            ['image' => 'https://images.unsplash.com/photo-1507925921958-8a62f3d1a50d?q=80&w=1400&auto=format&fit=crop', 'title' => 'Laporan Keberlanjutan', 'subtitle' => 'Komitmen tata kelola & keberlanjutan (ESG)'],
        ];
    @endphp

    {{-- =====================================================================
         1. HERO — gradasi hijau + pola titik, teks putih
         ===================================================================== --}}
    {{-- overflow-hidden dipindah ke layer dekorasi supaya kartu quick service
         yang menggantung (-mb) tidak terpotong --}}
    <section class="relative fs-gradient">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-32 -left-20 w-96 h-96 bg-black/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative z-10 max-w-7xl mx-auto px-6 lg:px-8 pt-16 lg:pt-24 pb-20 lg:pb-28">
            <div class="grid lg:grid-cols-2 gap-12 lg:gap-10 items-center">
                {{-- Teks --}}
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 border border-white/20 text-white/90 text-xs font-semibold tracking-wide uppercase mb-6">
                        <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                        {{ __('PPID PT Food Station Tjipinang Jaya') }}
                    </span>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.1] mb-6">
                        {!! __('Portal Keterbukaan<br>Informasi Publik') !!}
                    </h1>
                    <p class="text-lg text-white/85 max-w-2xl mb-9 leading-relaxed">
                        {{ __('Wujud tata kelola perusahaan yang baik dan akuntabel — melaksanakan keterbukaan informasi publik sesuai Undang-Undang No. 14 Tahun 2008.') }}
                    </p>
                    <div class="flex flex-col sm:flex-row gap-4">
                        <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-[#00664e] text-base font-bold rounded-xl hover:bg-emerald-50 hover:-translate-y-0.5 transition-all duration-200 shadow-xl">
                            {{ __('Ajukan Permohonan') }}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                        <a href="{{ route('ppid.status') }}" class="inline-flex items-center justify-center px-7 py-3.5 bg-white/10 border border-white/30 text-white text-base font-bold rounded-xl hover:bg-white/20 transition-colors duration-200">
                            {{ __('Cek Status Permohonan') }}
                        </a>
                    </div>
                </div>

                {{-- Slider gambar --}}
                <div class="relative hidden lg:block"
                     x-data="{ active: 0, total: {{ count($heroSlides) }}, timer: null,
                               start() { this.timer = setInterval(() => this.next(), 4500) },
                               next() { this.active = (this.active + 1) % this.total },
                               go(i) { this.active = i; clearInterval(this.timer); this.start() } }"
                     x-init="start()">
                    <div class="relative rounded-3xl overflow-hidden shadow-2xl shadow-emerald-950/40 ring-1 ring-white/20 aspect-[4/3]">
                        @foreach ($heroSlides as $i => $slide)
                            <div x-show="active === {{ $i }}"
                                 x-transition:enter="transition ease-out duration-700"
                                 x-transition:enter-start="opacity-0 scale-105"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-500 absolute inset-0"
                                 x-transition:leave-start="opacity-100"
                                 x-transition:leave-end="opacity-0"
                                 class="absolute inset-0">
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['caption'] }}" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-black/10 to-transparent"></div>
                                <p class="absolute bottom-5 left-6 right-6 text-white font-semibold text-lg drop-shadow">{{ $slide['caption'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Prev / Next --}}
                    <button @click="go((active - 1 + total) % total)" class="absolute top-1/2 -translate-y-1/2 -left-4 w-11 h-11 rounded-full bg-white dark:bg-[#121a17] text-[#008060] shadow-lg flex items-center justify-center hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="go((active + 1) % total)" class="absolute top-1/2 -translate-y-1/2 -right-4 w-11 h-11 rounded-full bg-white dark:bg-[#121a17] text-[#008060] shadow-lg flex items-center justify-center hover:scale-110 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    {{-- Dots --}}
                    <div class="flex justify-center gap-2 mt-5">
                        @foreach ($heroSlides as $i => $slide)
                            <button @click="go({{ $i }})" class="h-2 rounded-full transition-all duration-300"
                                    :class="active === {{ $i }} ? 'w-7 bg-white' : 'w-2 bg-white/40 hover:bg-white/70'"></button>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </section>

    {{-- 3. STATISTIK — dipindah ke section 5 (Alur Permohonan), tepat di atas label "Prosedur" --}}

    {{-- =====================================================================
         2b. SECTION LAYANAN CEPAT — background putih, section terpisah penuh
             (tidak menggantung / tidak menimpa section Kategori Informasi)
         ===================================================================== --}}
    <section class="py-16 lg:py-20 bg-white dark:bg-[#121a17]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-2">
                <div>
                    <span class="inline-flex items-center gap-2 text-[11px] font-bold uppercase tracking-[0.18em] text-[#008060] dark:text-[#00A66C]">
                        <span class="w-6 h-px bg-[#008060]/40 dark:bg-[#00A66C]/40"></span>
                        {{ __('Layanan Cepat') }}
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 dark:text-white mt-1.5">{{ __('Mulai Layanan dari Sini') }}</h2>
                </div>
                <p class="text-sm text-gray-500 dark:text-gray-400 sm:text-right">{{ __('Empat layanan utama PPID dalam satu klik.') }}</p>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-6 lg:px-8 mt-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($quickServices as $qs)
                    <a href="{{ $qs['route'] }}" class="group bg-white dark:bg-[#121a17] rounded-2xl border border-gray-100 dark:border-white/10 shadow-xl shadow-gray-200/60 dark:shadow-black/30 p-6 hover:-translate-y-1.5 hover:shadow-2xl hover:border-emerald-100 transition-all duration-300">
                        <div class="w-14 h-14 mb-5 fs-gradient text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-900/20 group-hover:scale-110 transition-transform duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $qs['icon'] }}"></path></svg>
                        </div>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mb-1">{{ __($qs['title']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">{{ __($qs['desc']) }}</p>
                        <span class="inline-flex items-center gap-1.5 text-[#008060] font-semibold text-sm">
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
                <h2 class="text-3xl lg:text-4xl font-extrabold text-white mt-3">{{ __('Informasi Publik') }}</h2>
                <p class="text-white/80 mt-4 leading-relaxed">{{ __('Tiga klasifikasi informasi publik yang dapat diakses masyarakat sesuai peraturan keterbukaan informasi.') }}</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($infoPublik as $ip)
                    <div class="group bg-white dark:bg-[#121a17] rounded-2xl border border-gray-100 dark:border-white/10 p-8 hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-14 h-14 bg-emerald-50 text-[#008060] rounded-2xl flex items-center justify-center group-hover:fs-gradient group-hover:text-white transition-all duration-300">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <span class="text-3xl font-extrabold text-[#008060] group-hover:text-[#00664e] dark:text-white dark:group-hover:text-white transition-colors">{{ $ip['count'] }}</span>
                        </div>
                        <p class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ __($ip['title']) }}</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed mb-6">{{ __($ip['desc']) }}</p>
                        <a href="{{ route('ppid.information', $ip['slug']) }}" class="inline-flex items-center gap-1.5 text-[#008060] font-semibold text-sm">
                            {{ __('Lihat') }} {{ $ip['count'] }} {{ __('Dokumen') }}
                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         5. ALUR PERMOHONAN
         ===================================================================== --}}
    <section class="py-16 lg:py-24 bg-white dark:bg-[#121a17]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            {{-- Ringkasan angka (dipindah dari section STATISTIK) --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-16">
                @foreach ($stats as $s)
                    <div class="text-center py-8 rounded-2xl bg-[#F7F9F8] dark:bg-[#0d1310] border border-gray-100 dark:border-white/10">
                        <p class="text-4xl lg:text-5xl font-extrabold fs-gradient-text mb-1">{{ $s['value'] }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ __($s['label']) }}</p>
                    </div>
                @endforeach
            </div>

            <div class="text-center max-w-2xl mx-auto mb-16">
                <span class="text-sm font-bold text-[#008060] uppercase tracking-widest">{{ __('Prosedur') }}</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{{ __('Alur Permohonan Informasi') }}</h2>
                <p class="text-gray-500 dark:text-gray-400 mt-4 leading-relaxed">{{ __('Enam tahapan sederhana dari pengajuan hingga informasi Anda terima.') }}</p>
            </div>

            @php
                $flowSteps = [
                    ['title' => 'Ajukan Permohonan', 'desc' => 'Isi formulir permohonan informasi secara daring.', 'icon' => 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
                    ['title' => 'Verifikasi', 'desc' => 'Berkas & identitas pemohon diperiksa petugas PPID.', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ['title' => 'Diproses', 'desc' => 'Permohonan ditelaah dan informasi dihimpun.', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.525.321 1.157.498 1.724 1.065z'],
                    ['title' => 'Persetujuan PPID', 'desc' => 'Keputusan pemberian informasi disetujui pejabat PPID.', 'icon' => 'M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z'],
                    ['title' => 'Informasi Dikirim', 'desc' => 'Dokumen dikirim via email atau diambil di kantor.', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['title' => 'Selesai', 'desc' => 'Permohonan tuntas & tercatat dalam arsip layanan.', 'icon' => 'M5 13l4 4L19 7'],
                ];
            @endphp

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-8 gap-x-5 lg:gap-x-6">
                @foreach ($flowSteps as $i => $step)
                    {{-- Wrapper relatif (tanpa overflow) agar konektor tidak terpotong --}}
                    <div class="group relative h-full">
                        <div class="relative h-full bg-white dark:bg-[#121a17] rounded-2xl border border-gray-100 dark:border-white/10 p-7 hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-1 transition-all duration-300 overflow-hidden">
                            {{-- Nomor watermark --}}
                            <span class="absolute -top-3 -right-1 text-[6rem] leading-none font-extrabold text-[#008060]/25 group-hover:text-[#008060]/40 dark:text-white/25 dark:group-hover:text-white/40 transition-colors select-none">{{ $i + 1 }}</span>

                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-5">
                                    <div class="w-14 h-14 fs-gradient text-white rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-900/20 group-hover:scale-110 transition-transform duration-300">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $step['icon'] }}"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-[#008060] uppercase tracking-widest">{{ __('Langkah') }} {{ $i + 1 }}</span>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1.5">{{ __($step['title']) }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">{{ __($step['desc']) }}</p>
                            </div>
                        </div>

                        {{-- Line penghubung + panah antar kartu dalam 1 baris (desktop) --}}
                        @if (!in_array($i, [2, 5]))
                            <div class="hidden lg:block absolute top-1/2 left-full -translate-y-1/2 w-6 z-20">
                                <div class="h-[3px] w-full fs-gradient rounded-full opacity-60"></div>
                                <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-7 h-7 rounded-full bg-white dark:bg-[#121a17] border border-emerald-100 shadow-md flex items-center justify-center text-[#008060]">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M9 5l7 7-7 7"></path></svg>
                                </div>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('ppid.request') }}" class="inline-flex items-center gap-2 px-8 py-3.5 fs-gradient text-white text-base font-semibold rounded-xl shadow-lg shadow-emerald-900/20 hover:-translate-y-0.5 transition-all duration-200">
                    {{ __('Mulai Ajukan Permohonan') }}
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- =====================================================================
         6. BERITA
         ===================================================================== --}}
    <section id="berita" class="py-16 lg:py-24 bg-[#F7F9F8] dark:bg-[#0d1310]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 mb-12">
                <div>
                    <span class="text-sm font-bold text-[#008060] uppercase tracking-widest">{{ __('Kabar Terbaru') }}</span>
                    <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{{ __('Berita & Publikasi') }}</h2>
                </div>
                <a href="#" class="inline-flex items-center gap-1.5 text-sm font-semibold text-[#008060] hover:text-[#00664e]">
                    {{ __('Lihat Semua') }}
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                @foreach ($news as $item)
                    <a href="#" class="group h-full flex flex-col bg-white dark:bg-[#121a17] rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 hover:shadow-xl hover:shadow-gray-200/60 hover:-translate-y-1 transition-all duration-300">
                        <div class="relative h-48 flex-shrink-0 overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-4 left-4 fs-gradient text-white text-xs font-semibold px-3 py-1 rounded-full shadow-lg">{{ $item['category'] }}</span>
                        </div>
                        <div class="p-6 flex flex-col flex-1">
                            <p class="text-xs font-medium text-gray-400 dark:text-gray-500 mb-2 uppercase tracking-wide">{{ $item['date'] }}</p>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-[#008060] transition-colors">{{ $item['title'] }}</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 leading-relaxed line-clamp-3 mb-4">{{ $item['excerpt'] }}</p>
                            <span class="mt-auto inline-flex items-center gap-1.5 text-[#008060] font-semibold text-sm">
                                {{ __('Baca Selengkapnya') }}
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         7. DOKUMEN / LAPORAN
         ===================================================================== --}}
    <section id="dokumen" class="py-16 lg:py-24 bg-white dark:bg-[#121a17]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-14">
                <span class="text-sm font-bold text-[#008060] uppercase tracking-widest">{{ __('Arsip Resmi') }}</span>
                <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{{ __('Laporan & Dokumen') }}</h2>
            </div>

            {{-- Slider gambar arsip --}}
            <div class="relative mb-12"
                 x-data="{ active: 0, total: {{ count($arsipSlides) }}, timer: null,
                           start() { this.timer = setInterval(() => this.next(), 5000) },
                           next() { this.active = (this.active + 1) % this.total },
                           go(i) { this.active = i; clearInterval(this.timer); this.start() } }"
                 x-init="start()">
                <div class="relative rounded-3xl overflow-hidden shadow-xl shadow-gray-300/50 aspect-[16/7] sm:aspect-[16/6]">
                    @foreach ($arsipSlides as $i => $slide)
                        <div x-show="active === {{ $i }}"
                             x-transition:enter="transition ease-out duration-700"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100"
                             x-transition:leave="transition ease-in duration-500 absolute inset-0"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0"
                             class="absolute inset-0">
                            <img src="{{ $slide['image'] }}" alt="{{ $slide['title'] }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#00664e]/90 via-[#008060]/60 to-transparent"></div>
                            <div class="absolute inset-0 flex flex-col justify-center px-8 sm:px-14 max-w-xl">
                                <span class="inline-flex w-fit items-center gap-1.5 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-semibold uppercase tracking-wide mb-3">{{ __('PDF · Dokumen Resmi') }}</span>
                                <h3 class="text-2xl sm:text-4xl font-extrabold text-white leading-tight mb-2">{{ __($slide['title']) }}</h3>
                                <p class="text-white/85 text-sm sm:text-base mb-5">{{ __($slide['subtitle']) }}</p>
                                <a href="#dokumen-list" class="inline-flex w-fit items-center gap-2 px-6 py-3 bg-white text-[#00664e] text-sm font-bold rounded-xl hover:bg-emerald-50 transition-colors duration-200">
                                    {{ __('Lihat Dokumen') }}
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    @endforeach

                    {{-- Prev / Next --}}
                    <button @click="go((active - 1 + total) % total)" class="absolute top-1/2 -translate-y-1/2 left-4 w-10 h-10 rounded-full bg-white/90 text-[#008060] shadow-lg flex items-center justify-center hover:bg-white dark:bg-[#121a17] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                    </button>
                    <button @click="go((active + 1) % total)" class="absolute top-1/2 -translate-y-1/2 right-4 w-10 h-10 rounded-full bg-white/90 text-[#008060] shadow-lg flex items-center justify-center hover:bg-white dark:bg-[#121a17] transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                    </button>

                    {{-- Dots --}}
                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
                        @foreach ($arsipSlides as $i => $slide)
                            <button @click="go({{ $i }})" class="h-2 rounded-full transition-all duration-300"
                                    :class="active === {{ $i }} ? 'w-7 bg-white' : 'w-2 bg-white/50 hover:bg-white/80'"></button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div id="dokumen-list" class="grid grid-cols-1 md:grid-cols-3 gap-6 items-stretch">
                @foreach ($reports as $report)
                    <div class="group h-full flex flex-col bg-[#F7F9F8] dark:bg-[#0d1310] rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 hover:shadow-xl hover:shadow-gray-200/60 transition-all duration-300">
                        <div class="relative h-40 flex-shrink-0 fs-gradient flex items-center justify-center overflow-hidden">
                            <div class="absolute inset-0 fs-dot-pattern opacity-30"></div>
                            <span class="text-white text-5xl font-extrabold tracking-tight relative z-10">{{ $report['year'] }}</span>
                            <span class="absolute top-4 right-4 text-xs font-semibold uppercase tracking-wide px-3 py-1 rounded-full bg-white/20 text-white z-10">PDF</span>
                        </div>
                        <div class="p-7 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug">{{ $report['title'] }}</h3>
                            <p class="text-xs text-gray-400 dark:text-gray-500 mb-6 uppercase tracking-wide">{{ $report['year'] }} · PDF · {{ $report['size'] }}</p>
                            <button @click="$dispatch('open-download-modal', { title: '{{ $report['title'] }} {{ $report['year'] }}' })"
                                    class="mt-auto w-full inline-flex items-center justify-center gap-2 px-6 py-3.5 fs-gradient text-white text-base font-semibold rounded-xl hover:-translate-y-0.5 transition-all duration-200 shadow-lg shadow-emerald-900/20">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                {{ __('Unduh Dokumen') }}
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- =====================================================================
         8. BANTUAN + KONTAK (1 section, 2 kolom 6/6)
         ===================================================================== --}}
    <section id="kontak-section" class="py-16 lg:py-24 bg-[#F7F9F8] dark:bg-[#0d1310]">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">

                {{-- Kolom kiri: FAQ --}}
                <div>
                    <div class="mb-8">
                        <span class="text-sm font-bold text-[#008060] uppercase tracking-widest">{{ __('Bantuan') }}</span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{{ __('Pertanyaan Umum') }}</h2>
                    </div>
                    <div class="space-y-3">
                        @foreach ($faqs as $faq)
                            <div x-data="{ open: false }" class="bg-white dark:bg-[#121a17] rounded-2xl border border-gray-100 dark:border-white/10 overflow-hidden" :class="open ? 'shadow-lg shadow-gray-200/60' : ''">
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
                        @endforeach
                    </div>
                </div>

                {{-- Kolom kanan: Kontak --}}
                <div>
                    <div class="mb-8">
                        <span class="text-sm font-bold text-[#008060] uppercase tracking-widest">{{ __('Hubungi Kami') }}</span>
                        <h2 class="text-3xl lg:text-4xl font-extrabold text-gray-900 dark:text-white mt-3">{{ __('Kontak PPID Food Station') }}</h2>
                    </div>
                    @php
                        $contacts = [
                            ['label' => 'Alamat', 'value' => 'Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z'],
                            ['label' => 'Email & Telepon', 'value' => 'ppid@foodstation.co.id · 021-471 8011', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                            ['label' => 'Jam Layanan', 'value' => 'Senin–Jumat, 08.00–17.00 WIB', 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                        ];
                    @endphp
                    <div class="space-y-3 mb-6">
                        @foreach ($contacts as $c)
                            <div class="flex gap-4 p-5 rounded-2xl bg-white dark:bg-[#121a17] border border-gray-100 dark:border-white/10">
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
                    <div class="rounded-2xl overflow-hidden border border-gray-100 dark:border-white/10 h-[260px]">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.349182907665!2d106.89023207499014!3d-6.217601393770331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4b2635315a3%3A0x923242750674630!2sPT.%20Food%20Station%20Tjipinang%20Jaya!5e0!3m2!1sid!2sid!4v1709123456789!5m2!1sid!2sid"
                            class="w-full h-full"
                            style="border:0;"
                            allowfullscreen=""
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade">
                        </iframe>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- MODAL DOWNLOAD (logika Alpine tetap sama) --}}
    <div x-data="{
        showModal: false,
        isSubmitting: false,
        formSuccess: false,
        reportTitle: '',
        user: { name: '', phone: '', email: '', purpose: 'Pribadi', institution: '' },
        successEmail: '',
        submitDownloadForm: async function() {
            this.isSubmitting = true;
            setTimeout(() => {
                this.formSuccess = true;
                this.successEmail = this.user.email;
                this.isSubmitting = false;
            }, 1000);
        }
    }"
    @open-download-modal.window="
        showModal = true;
        reportTitle = $event.detail.title;
        formSuccess = false;
        user = { name: '', phone: '', email: '', purpose: 'Pribadi', institution: '' };
    "
    x-show="showModal"
    style="display: none"
    class="fixed inset-0 z-[100] overflow-y-auto"
    role="dialog"
    aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity @click="showModal = false" class="fixed inset-0 bg-[#0A0E0D]/60 backdrop-blur-sm transition-opacity"></div>

            <div x-show="showModal"
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:scale-95"
                class="relative inline-block align-bottom bg-white dark:bg-[#121a17] rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full w-full">

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
                    <div x-show="!formSuccess">
                        <p class="text-sm text-gray-500 dark:text-gray-400 dark:text-gray-500 mb-6">{{ __('Lengkapi data diri untuk mengunduh') }} <span class="font-semibold text-gray-900 dark:text-white" x-text="reportTitle"></span>.</p>
                        <form @submit.prevent="submitDownloadForm()" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Nama Lengkap') }}</label>
                                <input x-model="user.name" type="text" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Surat Elektronik (Email)') }}</label>
                                <input x-model="user.email" type="email" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('Telepon') }}</label>
                                <input x-model="user.phone" type="tel" required class="w-full px-4 py-3 bg-gray-50 border border-gray-200 dark:border-white/10 rounded-xl focus:bg-white dark:bg-[#121a17] focus:border-[#008060] focus:ring-2 focus:ring-[#008060]/15 outline-none transition-all text-base">
                            </div>
                            <div class="pt-2">
                                <button type="submit" :disabled="isSubmitting" class="w-full flex items-center justify-center px-6 py-3.5 fs-gradient text-white text-base font-semibold rounded-xl hover:-translate-y-0.5 transition-all duration-300 disabled:opacity-70 disabled:cursor-not-allowed shadow-lg shadow-emerald-900/20">
                                    <span x-show="!isSubmitting">{{ __('Kirim Tautan Unduhan') }}</span>
                                    <span x-show="isSubmitting" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        {{ __('Memproses...') }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <div x-show="formSuccess" class="py-6 text-center">
                        <div class="w-16 h-16 bg-emerald-50 rounded-full flex items-center justify-center mx-auto mb-5">
                            <svg class="w-8 h-8 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
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
