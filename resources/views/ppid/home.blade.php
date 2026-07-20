{{--
    =============================================================================
    CATATAN INSTALASI (hapus komentar ini setelah dibaca):

    1) Tambahkan Google Fonts di <head> layout utama Anda (resources/views/layouts/app.blade.php):

       <link rel="preconnect" href="https://fonts.googleapis.com">
       <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;600;700;800;900&family=Inter:wght@400;500;600&family=IBM+Plex+Mono:wght@500;600&display=swap" rel="stylesheet">

    2) Tambahkan font-family & warna custom di tailwind.config.js:

       theme: {
         extend: {
           fontFamily: {
             display: ['Public Sans', 'sans-serif'],
             mono: ['IBM Plex Mono', 'monospace'],
           },
           colors: {
             navy: { DEFAULT: '#0B2545', 2: '#12335e' },
             teal: { DEFAULT: '#0F6F5C', 2: '#0c5a4a' },
             sky: '#2D82B7',
             gold: '#C9A227',
             mist: '#F4F8F9',
           }
         }
       }

       Setelah config ini aktif, kelas seperti bg-[#0B2545] di bawah bisa Anda
       sederhanakan jadi bg-navy, bg-teal, dsb — tapi kode di bawah sudah
       berfungsi langsung tanpa config tambahan karena pakai arbitrary values.

    3) Jalankan: npm run build (atau npm run dev)
    =============================================================================
--}}
@extends('layouts.app')

@section('title', 'PPID FSTJ - Transparansi Informasi Publik')

@section('content')

    @php
        // Data Laporan
        $reports = [
            ['year' => 2024, 'title' => 'Laporan Keuangan & Kinerja', 'label' => 'Laporan Tahunan', 'cover_color' => 'bg-[#0B2545]', 'link' => '#'],
            ['year' => 2023, 'title' => 'Laporan Tahunan Perusahaan', 'label' => 'Laporan Tahunan', 'cover_color' => 'bg-[#0F6F5C]', 'link' => '#'],
            ['year' => 2021, 'title' => 'Laporan Keberlanjutan (Sustainability)', 'label' => 'Sustainability', 'cover_color' => 'bg-[#2D82B7]', 'link' => '#'],
        ];

        // Data Berita
        $news = [
            [
                'title' => 'Food Station Jaga Stabilitas Pasokan Beras Jelang Hari Raya',
                'date' => '10 Desember 2025',
                'category' => 'Operasional',
                'category_color' => 'text-[#0F6F5C]',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'title' => 'Raih Penghargaan Top BUMD 2025 Kategori Transparansi Publik',
                'date' => '05 Desember 2025',
                'category' => 'Prestasi',
                'category_color' => 'text-[#C9A227]',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'title' => 'Program Pangan Murah Bersubsidi Kembali Digelar di 5 Wilayah',
                'date' => '01 Desember 2025',
                'category' => 'CSR',
                'category_color' => 'text-[#2D82B7]',
                'image' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?q=80&w=800&auto=format&fit=crop'
            ],
        ];

        // Data Klasifikasi Informasi (Pasal UU KIP)
        $classifications = [
            ['pasal' => 'Pasal 9 UU KIP', 'title' => 'Informasi Berkala', 'desc' => 'Diumumkan secara rutin — laporan tahunan, kinerja, dan keuangan perusahaan.'],
            ['pasal' => 'Pasal 10 UU KIP', 'title' => 'Informasi Serta Merta', 'desc' => 'Menyangkut hajat hidup orang banyak, keselamatan, dan ketertiban umum.'],
            ['pasal' => 'Pasal 11 UU KIP', 'title' => 'Informasi Setiap Saat', 'desc' => 'Tersedia kapan pun diminta — regulasi, prosedur, dan profil layanan.'],
        ];

        // Data Statistik Dashboard Transparansi
        $stats = [
            ['value' => '318', 'label' => 'Permohonan Diterima', 'color' => 'text-[#0B2545]'],
            ['value' => '95%', 'label' => 'Selesai Tepat Waktu', 'color' => 'text-[#0F6F5C]'],
            ['value' => '2.4 Hari', 'label' => 'Rata-rata Respon', 'color' => 'text-[#2D82B7]'],
            ['value' => '4.8/5', 'label' => 'Skor Kepuasan Pemohon', 'color' => 'text-[#C9A227]'],
        ];

        // Status tiket berjalan (ticker) - idealnya diambil dari DB (misal: Ticket::latest()->take(6)->get())
        $ticketTicker = [
            'Permohonan #FSTJ-2412 — Diproses',
            'Permohonan #FSTJ-2411 — Selesai',
            'Keberatan #KBR-0087 — Menunggu Tanggapan Atasan PPID',
            'Permohonan #FSTJ-2410 — Selesai',
        ];
    @endphp

    {{-- ===================================================================
         TOP UTILITY BAR
         =================================================================== --}}
    <div class="bg-[#0B2545] text-white/80 text-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between h-9">
            <span class="hidden sm:inline">Situs Resmi PPID PT Food Station Tjipinang Jaya (Perseroda)</span>
            <span class="sm:hidden">Situs Resmi PPID FSTJ</span>
            <div class="flex items-center gap-4">
                <a href="#" class="hover:text-white transition">Peta Situs</a>
                <span class="w-px h-3 bg-white/20"></span>
                <a href="#" class="hover:text-white transition">Bahasa Indonesia</a>
            </div>
        </div>
    </div>
    <div class="h-[3px]" style="background-image: repeating-linear-gradient(90deg, rgba(201,162,39,.5) 0 2px, transparent 2px 14px);"></div>

    {{-- ===================================================================
         1. HERO
         =================================================================== --}}
    <section class="relative overflow-hidden bg-[#0B2545]">
        <div class="absolute inset-0 opacity-[0.07]" style="background-image: repeating-linear-gradient(45deg, #fff 0 1px, transparent 1px 26px);"></div>
        <div class="absolute -right-24 -top-24 w-[420px] h-[420px] rounded-full bg-[#0F6F5C]/30 blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-14 lg:pt-20 pb-16">
            <div class="grid lg:grid-cols-12 gap-10 items-center">
                <div class="lg:col-span-7">
                    <span class="inline-flex items-center gap-2 font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">
                        UU No. 14 Tahun 2008 tentang Keterbukaan Informasi Publik
                    </span>
                    <h1 class="font-display font-extrabold text-white text-4xl sm:text-5xl lg:text-[3.4rem] leading-[1.08] mt-6 mb-6">
                        Informasi Publik,<br>Hak Anda yang <span class="text-[#C9A227]">Kami Jamin</span>
                    </h1>
                    <p class="text-slate-300 text-lg leading-relaxed max-w-xl mb-8">
                        PPID PT Food Station Tjipinang Jaya (Perseroda) melayani permohonan, keberatan, dan akses data publik secara terbuka, terukur, dan tepat waktu.
                    </p>

                    <form action="{{ route('search') }}" method="GET" class="flex items-stretch bg-white rounded-xl shadow-lg overflow-hidden max-w-xl mb-8">
                        <div class="flex items-center pl-4 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input name="query" placeholder="Cari dokumen, regulasi, atau laporan..." class="w-full px-3 py-4 text-sm text-slate-700 border-0 focus:ring-0" autocomplete="off">
                        <button type="submit" class="bg-[#0F6F5C] hover:bg-[#0c5a4a] text-white font-display font-bold text-sm px-6 transition">Cari</button>
                    </form>

                    <div class="flex flex-wrap gap-3">
                        <a href="{{ route('ppid.request') }}" class="inline-flex items-center gap-2 bg-white text-[#0B2545] font-display font-bold text-sm px-5 py-3 rounded-lg hover:bg-slate-100 transition">
                            Ajukan Permohonan
                        </a>
                        <a href="#laporan" class="inline-flex items-center gap-2 border border-white/30 text-white font-display font-semibold text-sm px-5 py-3 rounded-lg hover:bg-white/10 transition">
                            Lihat Laporan Publik
                        </a>
                    </div>
                </div>

                <div class="lg:col-span-5">
                    <div class="bg-white rounded-2xl p-6 shadow-2xl">
                        <p class="font-display font-bold text-[#0B2545] text-sm mb-5">Indeks Keterbukaan Informasi</p>
                        <div class="flex items-end gap-2 mb-1">
                            <span class="font-mono font-semibold text-4xl text-[#0B2545]">92.4</span>
                            <span class="text-slate-400 text-sm mb-1">/ 100 — "Menuju Informatif"</span>
                        </div>
                        <div class="w-full h-2 rounded-full bg-slate-100 mb-6">
                            <div class="h-2 rounded-full bg-gradient-to-r from-[#0F6F5C] to-[#2D82B7]" style="width:92.4%"></div>
                        </div>
                        <div class="grid grid-cols-3 gap-3 text-center">
                            <div class="bg-[#F4F8F9] rounded-lg py-3">
                                <p class="font-mono font-semibold text-xl text-[#0B2545]">95%</p>
                                <p class="text-[11px] text-slate-500 mt-1">Tepat Waktu</p>
                            </div>
                            <div class="bg-[#F4F8F9] rounded-lg py-3">
                                <p class="font-mono font-semibold text-xl text-[#0B2545]">2.4</p>
                                <p class="text-[11px] text-slate-500 mt-1">Hari Rata-rata</p>
                            </div>
                            <div class="bg-[#F4F8F9] rounded-lg py-3">
                                <p class="font-mono font-semibold text-xl text-[#0B2545]">4.8</p>
                                <p class="text-[11px] text-slate-500 mt-1">Kepuasan</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Ticker status SLA --}}
        <div class="relative bg-[#12335e] border-t border-white/10 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-11 flex items-center gap-3">
                <span class="flex items-center gap-2 text-[#C9A227] text-xs font-display font-bold shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C9A227] animate-pulse"></span> STATUS LAYANAN
                </span>
                <div class="overflow-hidden flex-1">
                    <div class="flex gap-10 whitespace-nowrap text-xs text-white/70" style="animation: ticker 22s linear infinite;">
                        @foreach (array_merge($ticketTicker, $ticketTicker) as $item)
                            <span>{{ $item }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         2. KLASIFIKASI INFORMASI
         =================================================================== --}}
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Klasifikasi Informasi Publik</span>
                <h2 class="font-display font-extrabold text-3xl text-[#0B2545] mt-4">Tiga Jenis Informasi yang Kami Sediakan</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($classifications as $item)
                    <div class="bg-[#F4F8F9] rounded-2xl p-8 border border-slate-100 hover:-translate-y-1 hover:shadow-lg transition duration-300">
                        <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">{{ $item['pasal'] }}</span>
                        <h3 class="font-display font-bold text-xl text-[#0B2545] mt-4 mb-2">{{ $item['title'] }}</h3>
                        <p class="text-sm text-slate-500 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================================================================
         3. LAYANAN DIGITAL
         =================================================================== --}}
    <section id="layanan" class="py-20 bg-[#0B2545] relative overflow-hidden">
        <div class="absolute inset-0 opacity-[0.06]" style="background-image: repeating-linear-gradient(45deg, #fff 0 1px, transparent 1px 26px);"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Layanan Digital PPID</span>
                <h2 class="font-display font-extrabold text-3xl text-white mt-4">Ajukan &amp; Pantau Permohonan Anda</h2>
                <p class="text-slate-300 mt-3">Tiga kanal layanan utama sesuai standar operasional PPID, tersedia daring 24 jam.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-white/[0.06] border border-white/10 rounded-2xl p-8 hover:-translate-y-1 hover:shadow-2xl transition duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#0F6F5C] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-white mb-2">Permohonan Informasi</h3>
                    <p class="text-sm text-slate-300 mb-6 leading-relaxed">Ajukan permintaan data publik secara resmi, dilacak dengan nomor tiket.</p>
                    <a href="{{ route('ppid.request') }}" class="inline-flex items-center text-sm font-display font-bold text-[#C9A227] hover:underline">
                        Ajukan Sekarang →
                    </a>
                </div>
                <div class="bg-white/[0.06] border border-white/10 rounded-2xl p-8 hover:-translate-y-1 hover:shadow-2xl transition duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#2D82B7] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-white mb-2">Pengajuan Keberatan</h3>
                    <p class="text-sm text-slate-300 mb-6 leading-relaxed">Sampaikan banding bila permohonan ditolak atau tidak sesuai ketentuan.</p>
                    <a href="{{ route('ppid.objection') }}" class="inline-flex items-center text-sm font-display font-bold text-[#C9A227] hover:underline">
                        Ajukan Keberatan →
                    </a>
                </div>
                <div class="bg-white/[0.06] border border-white/10 rounded-2xl p-8 hover:-translate-y-1 hover:shadow-2xl transition duration-300">
                    <div class="w-12 h-12 rounded-xl bg-[#C9A227] flex items-center justify-center mb-6">
                        <svg class="w-6 h-6 text-[#0B2545]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h3 class="font-display font-bold text-lg text-white mb-2">Cek Status Tiket</h3>
                    <p class="text-sm text-slate-300 mb-6 leading-relaxed">Pantau progres permohonan menggunakan nomor tiket yang diberikan.</p>
                    <a href="{{ route('ppid.status') }}" class="inline-flex items-center text-sm font-display font-bold text-[#C9A227] hover:underline">
                        Lacak Status →
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         4. DASHBOARD TRANSPARANSI
         =================================================================== --}}
    <section class="py-20 bg-[#F4F8F9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Dashboard Transparansi</span>
                    <h2 class="font-display font-extrabold text-3xl text-[#0B2545] mt-4">Kinerja Pelayanan Informasi {{ date('Y') }}</h2>
                </div>
                <a href="#" class="text-sm font-display font-bold text-[#0F6F5C] hover:underline whitespace-nowrap">Unduh Data Lengkap (CSV) →</a>
            </div>

            <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
                @foreach ($stats as $stat)
                    <div class="bg-white rounded-2xl p-6 border border-slate-100">
                        <p class="font-mono font-semibold text-3xl {{ $stat['color'] }}">{{ $stat['value'] }}</p>
                        <p class="text-xs text-slate-500 mt-2">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================================================================
         5. LAPORAN
         =================================================================== --}}
    <section id="laporan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-2xl mb-12">
                <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Publikasi Resmi</span>
                <h2 class="font-display font-extrabold text-3xl text-[#0B2545] mt-4">Laporan Terkini Perusahaan</h2>
                <p class="text-slate-500 mt-3">Unduh dokumen laporan tahunan, keuangan, dan keberlanjutan.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($reports as $report)
                    <div class="rounded-2xl overflow-hidden border border-slate-100 hover:-translate-y-1 hover:shadow-xl transition duration-300">
                        <div class="h-56 {{ $report['cover_color'] }} p-6 flex flex-col justify-between">
                            <span class="inline-block w-fit font-mono text-[11px] tracking-wide text-white/80 bg-white/10 border border-white/20 px-3 py-1 rounded">{{ $report['label'] }}</span>
                            <div>
                                <p class="font-mono text-white/60 text-sm mb-1">{{ $report['year'] }}</p>
                                <h3 class="font-display font-bold text-white text-xl leading-snug">{{ $report['title'] }}</h3>
                            </div>
                        </div>
                        <div class="p-5 flex items-center justify-between bg-[#F4F8F9]">
                            <p class="text-xs text-slate-500">Format PDF · Siap diunduh</p>
                            <button @click="$dispatch('open-download-modal', { title: '{{ $report['title'] }} {{ $report['year'] }}' })"
                                    class="inline-flex items-center gap-1.5 text-xs font-display font-bold text-[#0F6F5C]">
                                Unduh
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================================================================
         6. BERITA
         =================================================================== --}}
    <section id="berita" class="py-20 bg-[#F4F8F9]">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Seputar Korporasi</span>
                    <h2 class="font-display font-extrabold text-3xl text-[#0B2545] mt-4">Berita Terbaru</h2>
                </div>
                <a href="#" class="text-sm font-display font-bold text-[#0F6F5C] hover:underline whitespace-nowrap">Lihat Semua Berita →</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($news as $item)
                    <article class="bg-white rounded-2xl overflow-hidden border border-slate-100 hover:-translate-y-1 hover:shadow-xl transition duration-300">
                        <div class="relative h-44 overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover">
                            <span class="absolute top-3 left-3 bg-white/95 px-3 py-1 rounded-md text-[11px] font-display font-bold {{ $item['category_color'] }}">{{ strtoupper($item['category']) }}</span>
                        </div>
                        <div class="p-6">
                            <p class="text-xs font-mono text-slate-400 mb-3">{{ strtoupper($item['date']) }}</p>
                            <h3 class="font-display font-bold text-[#0B2545] mb-2 leading-snug">
                                <a href="#">{{ $item['title'] }}</a>
                            </h3>
                            <a href="#" class="text-xs font-display font-bold text-[#0F6F5C]">Baca Selengkapnya →</a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ===================================================================
         7. KONTAK
         =================================================================== --}}
    <section id="kontak" class="py-20 bg-white border-t border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#F4F8F9] rounded-2xl overflow-hidden border border-slate-100 flex flex-col lg:flex-row">
                <div class="lg:w-2/5 p-8 lg:p-10">
                    <span class="inline-block font-mono text-[11px] tracking-wide text-[#0F6F5C] bg-[#0F6F5C]/10 border border-[#0F6F5C]/25 px-3 py-1 rounded">Hubungi Kami</span>
                    <h2 class="font-display font-extrabold text-2xl text-[#0B2545] mt-4 mb-8">Kantor PPID Food Station</h2>
                    <div class="space-y-6 text-sm">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center border border-slate-200 shrink-0">
                                <svg class="w-5 h-5 text-[#0F6F5C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-[#0B2545]">Alamat</p>
                                <p class="text-slate-500 mt-1 leading-relaxed">Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center border border-slate-200 shrink-0">
                                <svg class="w-5 h-5 text-[#0F6F5C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="font-display font-bold text-[#0B2545]">Telepon &amp; Email</p>
                                <p class="text-slate-500 mt-1">021-471 8011 (Ext. PPID)<br><a href="mailto:ppid@foodstation.co.id" class="text-[#0F6F5C] font-medium">ppid@foodstation.co.id</a></p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8 pt-6 border-t border-slate-200">
                        <p class="text-xs font-display font-bold text-[#0B2545] uppercase tracking-wide mb-2">Jam Operasional</p>
                        <p class="inline-block bg-white px-3 py-1.5 rounded-lg text-sm font-medium border border-slate-200">Senin – Jumat, 08.00–17.00 WIB</p>
                    </div>
                </div>
                <div class="lg:w-3/5 h-96 lg:h-auto bg-gray-200 relative">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.349182907665!2d106.89023207499014!3d-6.217601393770331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4b2635315a3%3A0x923242750674630!2sPT.%20Food%20Station%20Tjipinang%20Jaya!5e0!3m2!1sid!2sid!4v1709123456789!5m2!1sid!2sid"
                        class="absolute inset-0 w-full h-full grayscale hover:grayscale-0 transition duration-500"
                        style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    {{-- ===================================================================
         MODAL UNDUH (Alpine.js — sama seperti kode asli Anda)
         =================================================================== --}}
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
    class="fixed inset-0 z-[60] overflow-y-auto"
    role="dialog"
    aria-modal="true">
        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            <div x-show="showModal" x-transition.opacity @click="showModal = false" class="fixed inset-0 bg-[#0B2545] bg-opacity-60 transition-opacity backdrop-blur-sm"></div>
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-display font-bold text-[#0B2545]">Unduh Dokumen</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div x-show="!formSuccess">
                        <p class="text-sm text-gray-500 mb-4">Lengkapi data diri untuk mengunduh <span class="font-bold text-gray-800" x-text="reportTitle"></span>.</p>
                        <form @submit.prevent="submitDownloadForm()" class="space-y-4">
                            <div><label class="block text-xs font-display font-bold text-[#0B2545] uppercase tracking-wide mb-1">Nama Lengkap</label><input x-model="user.name" type="text" required class="w-full rounded-lg border-gray-300 focus:border-[#0F6F5C] focus:ring-[#0F6F5C]"></div>
                            <div><label class="block text-xs font-display font-bold text-[#0B2545] uppercase tracking-wide mb-1">Email</label><input x-model="user.email" type="email" required class="w-full rounded-lg border-gray-300 focus:border-[#0F6F5C] focus:ring-[#0F6F5C]"></div>
                            <div><label class="block text-xs font-display font-bold text-[#0B2545] uppercase tracking-wide mb-1">No. Telepon</label><input x-model="user.phone" type="tel" required class="w-full rounded-lg border-gray-300 focus:border-[#0F6F5C] focus:ring-[#0F6F5C]"></div>
                            <button type="submit" :disabled="isSubmitting" class="w-full bg-[#0F6F5C] text-white font-display font-bold py-3 rounded-xl hover:bg-[#0c5a4a] transition shadow-md disabled:opacity-50"><span x-show="!isSubmitting">Kirim Permintaan</span><span x-show="isSubmitting">Memproses...</span></button>
                        </form>
                    </div>
                    <div x-show="formSuccess" class="py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                        <h3 class="text-xl font-display font-bold text-[#0B2545]">Berhasil!</h3>
                        <p class="text-gray-500 mt-2 text-sm">Link download telah dikirim ke email Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes ticker {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
        @media (prefers-reduced-motion: reduce) {
            [style*="ticker"] { animation: none !important; }
        }
    </style>

@endsection
