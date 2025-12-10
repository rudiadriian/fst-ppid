@extends('layouts.app')
@section('title', 'PPID FSTJ - Transparansi Informasi Publik')
@section('content')

    @php
        $reports = [
            ['year' => 2024, 'title' => 'Laporan Keuangan & Kinerja', 'cover_color' => 'bg-blue-600', 'link' => '#'],
            ['year' => 2023, 'title' => 'Laporan Tahunan Perusahaan', 'cover_color' => 'bg-emerald-600', 'link' => '#'],
            ['year' => 2022, 'title' => 'Laporan Keberlanjutan (Sustainability)', 'cover_color' => 'bg-purple-600', 'link' => '#'],
        ];
        $news = [
            [
                'title' => 'Food Station Jaga Stabilitas Pasokan Beras Jelang Hari Raya',
                'date' => '10 Desember 2025',
                'category' => 'Operasional',
                'image' => 'https://images.unsplash.com/photo-1586201375761-83865001e31c?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'title' => 'Raih Penghargaan Top BUMD 2025 Kategori Transparansi Publik',
                'date' => '05 Desember 2025',
                'category' => 'Prestasi',
                'image' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?q=80&w=800&auto=format&fit=crop'
            ],
            [
                'title' => 'Program Pangan Murah Bersubsidi Kembali Digelar di 5 Wilayah',
                'date' => '01 Desember 2025',
                'category' => 'CSR',
                'image' => 'https://images.unsplash.com/photo-1488459716781-31db52582fe9?q=80&w=800&auto=format&fit=crop'
            ],
        ];
    @endphp
    <section class="relative overflow-hidden pb-28">
        <div class="absolute inset-0">
            <img class="w-full h-full object-cover"
                 src="https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?q=80&w=1920&auto=format&fit=crop"
                 alt="Food Station Warehouse Background">
            <div class="absolute inset-0 bg-gradient-to-r from-emerald-950/95 via-emerald-900/90 to-gray-900/80 mix-blend-multiply"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 lg:pt-24 z-10">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <div class="inline-block px-4 py-1.5 mb-6 rounded-full bg-white/10 text-white text-sm font-semibold tracking-wide shadow-sm backdrop-blur-sm border border-white/20">
                    Selamat Datang di PPID Online
                </div>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white mb-6 leading-tight drop-shadow-sm">
                    Wujudkan Transparansi <br class="hidden lg:block">di
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-300 to-white">Food Station</span>
                </h1>
                <p class="text-lg sm:text-xl text-emerald-100 mb-10 leading-relaxed max-w-2xl mx-auto">
                    Akses Informasi Publik PT Food Station Tjipinang Jaya (Perseroda) dengan mudah, cepat, dan transparan.
                </p>
                 <div class="max-w-lg mx-auto">
                    <form action="{{ route('search') }}" method="GET" class="relative flex items-center bg-white rounded-full shadow-xl hover:shadow-2xl transition-shadow duration-300 p-1">
                        <div class="pl-4 text-gray-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                        <input name="query"
                               placeholder="Cari dokumen, regulasi, atau laporan..."
                               class="w-full p-3 pl-3 border-none focus:ring-0 text-gray-700 bg-transparent rounded-full placeholder-gray-400"
                               autocomplete="off">
                        <button type="submit" class="bg-[#008060] text-white px-6 py-2.5 rounded-full hover:bg-[#00664e] transition duration-200 font-medium text-sm shadow-md">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 relative z-10 -mb-16">
                <a href="#" class="group relative bg-white p-8 rounded-3xl shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-emerald-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition duration-300"></div>
                    <div class="relative w-14 h-14 bg-emerald-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#008060] transition duration-300">
                        <svg class="w-7 h-7 text-[#008060] group-hover:text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v2m-7 11v-5a2 2 0 012-2h2a2 2 0 012 2v5m-11 0h10"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#008060] transition">Informasi Berkala</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Dokumen yang diumumkan secara rutin seperti Laporan Tahunan & Keuangan.</p>
                </a>
                <a href="#" class="group relative bg-white p-8 rounded-3xl shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-yellow-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition duration-300"></div>
                    <div class="relative w-14 h-14 bg-yellow-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-yellow-500 transition duration-300">
                        <svg class="w-7 h-7 text-yellow-600 group-hover:text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-yellow-600 transition">Serta Merta</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Informasi menyangkut hajat hidup orang banyak dan ketertiban umum.</p>
                </a>
                <a href="#" class="group relative bg-white p-8 rounded-3xl shadow-xl border border-gray-100 hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">
                    <div class="absolute top-0 right-0 w-24 h-24 bg-blue-50 rounded-bl-full -mr-4 -mt-4 opacity-50 group-hover:scale-110 transition duration-300"></div>
                    <div class="relative w-14 h-14 bg-blue-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition duration-300">
                        <svg class="w-7 h-7 text-blue-600 group-hover:text-white transition duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9h-3M12 21a9 9 0 01-9-9m9 9v-3M3 12h3M12 3v3"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-blue-600 transition">Setiap Saat</h3>
                    <p class="text-gray-500 leading-relaxed text-sm">Informasi yang tersedia setiap saat meliputi regulasi dan prosedur layanan.</p>
                </a>
            </div>
        </div>
    </section>
    <section id="berita" class="pt-32 pb-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12">
                <div>
                    <span class="text-[#008060] font-semibold tracking-wider uppercase text-sm">Seputar Korporasi</span>
                    <h2 class="mt-2 text-3xl font-extrabold text-gray-900">Berita Terbaru</h2>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="#" class="inline-flex items-center text-[#008060] font-bold hover:underline transition">
                        Lihat Semua Berita
                        <svg class="w-5 h-5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($news as $item)
                    <article class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 group">
                        <div class="relative h-48 overflow-hidden">
                            <img src="{{ $item['image'] }}" alt="{{ $item['title'] }}" class="w-full h-full object-cover transform group-hover:scale-110 transition duration-700">
                            <div class="absolute top-4 left-4 bg-white/90 backdrop-blur-sm px-3 py-1 rounded-full text-xs font-bold text-[#008060] uppercase tracking-wide">
                                {{ $item['category'] }}
                            </div>
                        </div>
                        <div class="p-6">
                            <div class="flex items-center text-xs text-gray-400 mb-3">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                {{ $item['date'] }}
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 mb-3 leading-snug group-hover:text-[#008060] transition-colors">
                                <a href="#">{{ $item['title'] }}</a>
                            </h3>
                            <p class="text-gray-500 text-sm mb-4 line-clamp-2">
                                PT Food Station Tjipinang Jaya terus berkomitmen dalam menjaga ketahanan pangan dan transparansi publik...
                            </p>
                            <a href="#" class="inline-flex items-center text-sm font-semibold text-[#008060] hover:text-[#00664e]">
                                Baca Selengkapnya
                                <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
    <section id="ajukan" class="py-24 bg-[#0d2e26] relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(#ffffff 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-emerald-300 font-semibold tracking-wider uppercase text-sm">Layanan Digital</span>
                <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-white mb-4">Ajukan Permohonan Online</h2>
                <p class="text-emerald-200 max-w-2xl mx-auto">Gunakan kanal digital kami untuk kemudahan akses informasi publik, kapan saja dan di mana saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-[#134035] border border-[#1f5c4d] p-8 rounded-3xl hover:bg-[#1a5244] transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Permohonan Informasi</h3>
                    <p class="text-emerald-200/70 text-sm mb-8">Formulir pengajuan permintaan data publik secara resmi.</p>
                    <a href="{{ route('ppid.request') }}" class="inline-block w-full py-3 px-6 bg-[#008060] hover:bg-[#00b386] text-white rounded-xl font-semibold transition shadow-lg">
                        Ajukan Sekarang
                    </a>
                </div>
                <div class="bg-[#134035] border border-[#1f5c4d] p-8 rounded-3xl hover:bg-[#1a5244] transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-red-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Pengajuan Keberatan</h3>
                    <p class="text-emerald-200/70 text-sm mb-8">Ajukan banding jika permohonan informasi tidak sesuai.</p>
                    <a href="{{ route('ppid.objection') }}" class="inline-block w-full py-3 px-6 bg-transparent border border-red-400 text-red-400 hover:bg-red-500 hover:text-white rounded-xl font-semibold transition">
                        Ajukan Keberatan
                    </a>
                </div>
                <div class="bg-[#134035] border border-[#1f5c4d] p-8 rounded-3xl hover:bg-[#1a5244] transition duration-300 text-center group">
                    <div class="w-16 h-16 bg-blue-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 group-hover:scale-110 transition">
                        <svg class="w-8 h-8 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Cek Status Tiket</h3>
                    <p class="text-emerald-200/70 text-sm mb-8">Pantau progress permohonan yang telah Anda ajukan.</p>
                    <a href="{{ route('ppid.status') }}" class="inline-block w-full py-3 px-6 bg-transparent border border-blue-400 text-blue-400 hover:bg-blue-500 hover:text-white rounded-xl font-semibold transition">
                        Lacak Status
                    </a>
                </div>
            </div>
        </div>
    </section>
    <section id="laporan" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-[#008060] font-semibold tracking-wider uppercase text-sm">Publikasi Resmi</span>
                <h2 class="mt-2 text-3xl md:text-4xl font-extrabold text-gray-900">Laporan Terkini Perusahaan</h2>
                <p class="text-gray-500 mt-4 max-w-2xl mx-auto">Unduh dokumen laporan tahunan, keuangan, dan kinerja terbaru PT Food Station Tjipinang Jaya.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12">
                @foreach ($reports as $report)
                    <div class="group relative flex flex-col h-full bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden border border-gray-100">
                        <div class="relative h-96 w-full {{ $report['cover_color'] }} bg-opacity-90 flex flex-col justify-end p-8 overflow-hidden">
                             <div class="absolute inset-0 opacity-20 bg-cover bg-center mix-blend-overlay" style="background-image: url('https://images.unsplash.com/photo-1550684848-fac1c5b4e853?q=80&w=800&auto=format&fit=crop');"></div>
                            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                            <div class="relative z-10">
                                <span class="inline-block px-3 py-1 bg-white/20 text-white text-xs font-bold tracking-wider mb-4 rounded-full uppercase">Food Station</span>
                                <h3 class="text-3xl font-extrabold text-white mb-2 leading-tight">{{ $report['title'] }}</h3>
                                <span class="text-5xl font-black text-white/90 tracking-tight">{{ $report['year'] }}</span>
                            </div>
                        </div>
                        <div class="p-6 bg-white flex-grow flex items-center justify-between border-t border-gray-100">
                            <div>
                                <p class="text-sm font-medium text-gray-900">Format PDF</p>
                                <p class="text-xs text-gray-500">Siap diunduh</p>
                            </div>
                            <button @click="$dispatch('open-download-modal', { title: '{{ $report['title'] }} {{ $report['year'] }}' })"
                                    class="inline-flex items-center px-5 py-2.5 bg-gray-900 text-white text-sm font-bold rounded-full hover:bg-[#008060] transition-colors duration-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#008060]">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="text-center mt-12">
                <a href="#" class="inline-flex items-center text-[#008060] font-bold hover:underline text-lg group">
                    Lihat Arsip Laporan Lengkap
                    <svg class="w-5 h-5 ml-2 transform group-hover:translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>
    <section class="py-20 bg-[#f8fcfb] border-t border-b border-gray-100 relative overflow-hidden">
        <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(#008060 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
             <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900">Kinerja Pelayanan Informasi</h2>
                <p class="text-gray-500 mt-4">Komitmen kami dalam memberikan pelayanan informasi publik yang prima.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center">
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-emerald-50/50">
                    <div class="w-16 h-16 bg-emerald-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="block text-4xl font-extrabold text-gray-900 mb-2">95%</span>
                    <h3 class="text-lg font-bold text-gray-700 mb-1">Tingkat Penyelesaian</h3>
                    <p class="text-sm text-gray-500">Permohonan informasi diselesaikan tepat waktu.</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-blue-50/50">
                     <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="block text-4xl font-extrabold text-gray-900 mb-2">< 3 Hari</span>
                    <h3 class="text-lg font-bold text-gray-700 mb-1">Rata-rata Respon</h3>
                    <p class="text-sm text-gray-500">Waktu tanggap awal untuk setiap permohonan.</p>
                </div>
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-yellow-50/50">
                     <div class="w-16 h-16 bg-yellow-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <span class="block text-4xl font-extrabold text-gray-900 mb-2">4.8/5</span>
                    <h3 class="text-lg font-bold text-gray-700 mb-1">Kepuasan Pemohon</h3>
                    <p class="text-sm text-gray-500">Indeks kepuasan masyarakat terhadap layanan PPID.</p>
                </div>
            </div>
        </div>
    </section>
    <section id="kontak" class="py-24 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-[#f8fcfb] rounded-3xl overflow-hidden shadow-lg border border-emerald-50 flex flex-col lg:flex-row">
                <div class="lg:w-2/5 p-8 lg:p-12">
                    <span class="text-[#008060] font-bold tracking-wider uppercase text-xs mb-2 block">Hubungi Kami</span>
                    <h2 class="text-3xl font-extrabold text-gray-900 mb-8">Kantor PPID Food Station</h2>
                    <div class="space-y-8">
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-[#008060] border border-emerald-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-lg font-bold text-gray-900">Alamat</p>
                                <p class="text-gray-600 mt-2 leading-relaxed">Komplek Pasar Induk Beras Cipinang,<br>Jl. Pisangan Lama Selatan No. 1<br>Jakarta Timur 13230</p>
                            </div>
                        </div>
                        <div class="flex items-start">
                            <div class="flex-shrink-0 w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-sm text-[#008060] border border-emerald-100">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div class="ml-5">
                                <p class="text-lg font-bold text-gray-900">Telepon & Email</p>
                                <p class="text-gray-600 mt-2">
                                    Telp: <span class="font-medium text-gray-900">021-471 8011 (Ext. PPID)</span><br>
                                    Email: <a href="mailto:ppid@foodstation.co.id" class="font-medium text-[#008060] hover:underline">ppid@foodstation.co.id</a>
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-10 pt-8 border-t border-gray-200">
                         <p class="text-sm font-bold text-gray-900 uppercase tracking-wide mb-2">Jam Operasional Pelayanan</p>
                         <p class="text-gray-700 bg-emerald-50 inline-block px-4 py-2 rounded-lg font-medium">Senin - Jumat: 08:00 - 17:00 WIB</p>
                    </div>
                </div>
                <div class="lg:w-3/5 h-96 lg:h-auto bg-gray-200 relative">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.349182907665!2d106.89023207499014!3d-6.217601393770331!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f4b2635315a3%3A0x923242750674630!2sPT.%20Food%20Station%20Tjipinang%20Jaya!5e0!3m2!1sid!2sid!4v1709123456789!5m2!1sid!2sid"
                        class="absolute inset-0 w-full h-full grayscale hover:grayscale-0 transition duration-500"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>
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
            <div x-show="showModal" x-transition.opacity @click="showModal = false" class="fixed inset-0 bg-gray-900 bg-opacity-60 transition-opacity backdrop-blur-sm"></div>
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-6 pt-6 pb-4">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-lg font-bold text-gray-900">Unduh Dokumen</h3>
                        <button @click="showModal = false" class="text-gray-400 hover:text-gray-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>
                    <div x-show="!formSuccess">
                        <p class="text-sm text-gray-500 mb-4">Lengkapi data diri untuk mengunduh <span class="font-bold text-gray-800" x-text="reportTitle"></span>.</p>
                        <form @submit.prevent="submitDownloadForm()" class="space-y-4">
                            <div><label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Nama Lengkap</label><input x-model="user.name" type="text" required class="w-full rounded-lg border-gray-300 focus:border-[#008060] focus:ring-[#008060]"></div>
                            <div><label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">Email</label><input x-model="user.email" type="email" required class="w-full rounded-lg border-gray-300 focus:border-[#008060] focus:ring-[#008060]"></div>
                            <div><label class="block text-xs font-semibold text-gray-700 uppercase tracking-wide mb-1">No. Telepon</label><input x-model="user.phone" type="tel" required class="w-full rounded-lg border-gray-300 focus:border-[#008060] focus:ring-[#008060]"></div>
                            <button type="submit" :disabled="isSubmitting" class="w-full bg-[#008060] text-white font-bold py-3 rounded-xl hover:bg-[#00664e] transition shadow-md disabled:opacity-50"><span x-show="!isSubmitting">Kirim Permintaan</span><span x-show="isSubmitting">Memproses...</span></button>
                        </form>
                    </div>
                    <div x-show="formSuccess" class="py-8 text-center">
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4"><svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg></div>
                        <h3 class="text-xl font-bold text-gray-900">Berhasil!</h3>
                        <p class="text-gray-500 mt-2 text-sm">Link download telah dikirim ke email Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
