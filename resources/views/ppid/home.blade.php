@extends('layouts.app')

@section('title', 'PPID FSTJ - Transparansi Informasi Publik')

@section('content')

    @php
        $reports = [
            ['year' => 2024, 'detail' => 'Diumumkan 2025', 'link' => '#'],
            ['year' => 2023, 'detail' => 'Audit Tahun 2023', 'link' => '#'],
            ['year' => 2022, 'detail' => 'Audit Tahun 2022', 'link' => '#'],
            ['year' => 2021, 'detail' => 'Audit Tahun 2021', 'link' => '#'],
            ['year' => 2020, 'detail' => 'Audit Tahun 2020', 'link' => '#'],
            ['year' => 2019, 'detail' => 'Audit Tahun 2019', 'link' => '#'],
        ];
        $totalReports = count($reports);
        $slidesPerView = 3;
        $maxOffset = $totalReports - $slidesPerView;
    @endphp

    {{-- 1. HERO SECTION (Menggabungkan Banner & Pencarian) --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-10 pb-16 lg:pt-16 lg:pb-24">
            <div class="flex flex-col lg:flex-row items-center justify-between">

                {{-- Konten Kiri (Teks & CTA) --}}
                <div class="lg:w-1/2 lg:pr-12 text-center lg:text-left mb-10 lg:mb-0">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 mb-4 tracking-tight">
                        Wujudkan Transparansi di
                        <span class="fs-logo-text">Food Station</span>
                    </h1>
                    <p class="text-xl text-gray-600 mb-8 max-w-lg mx-auto lg:mx-0">
                        Akses Informasi Publik Perumda Pasar Jaya Food Station dengan mudah dan cepat sesuai UU KIP.
                    </p>

                    {{-- === SEARCH BAR DENGAN ALPINE.JS LIVE SEARCH (SUDAH DIPERBAIKI) === --}}
                    <div x-data="{
                            query: '',
                            suggestions: [],
                            loading: false,
                            isOpen: false,
                            timeoutId: null,

                            // Fungsi pencarian menggunakan manual debounce
                            fetchSuggestions: function() {
                                clearTimeout(this.timeoutId);

                                if (this.query.length < 3) {
                                    this.suggestions = [];
                                    this.isOpen = false;
                                    console.log('Query kurang dari 3. Suggestions disembunyikan.');
                                    return;
                                }

                                this.loading = true;
                                this.isOpen = true;
                                console.log('Fungsi dipanggil. Mencari untuk:', this.query);

                                // Lakukan Debounce 300ms
                                this.timeoutId = setTimeout(() => {
                                    // GANTI INI DENGAN FETCH/AXIOS ASLI KE LARAVEL API
                                    if (this.query.toLowerCase().includes('laporan')) {
                                        this.suggestions = [
                                            { title: 'Laporan Keuangan 2023 (Simulasi)', url: '#' },
                                            { title: 'Laporan Tahunan 2022 (Simulasi)', url: '#' },
                                            { title: 'Lihat Semua Laporan', url: '#' },
                                        ];
                                    } else if (this.query.toLowerCase().includes('regulasi')) {
                                        this.suggestions = [
                                            { title: 'Regulasi KIP Terbaru (Simulasi)', url: '#' },
                                            { title: 'Peraturan Direksi No. 5 (Simulasi)', url: '#' },
                                        ];
                                    } else {
                                        this.suggestions = [
                                            { title: 'Hasil tidak ditemukan (Simulasi)', url: '#' }
                                        ];
                                    }
                                    this.loading = false;
                                    console.log('Simulasi selesai. Jumlah saran:', this.suggestions.length);
                                }, 300);
                            }
                         }"
                         @click.away="isOpen = false"
                         class="relative w-full max-w-md mx-auto lg:mx-0">

                        {{-- Input Pencarian Utama --}}
                        <form action="{{ route('search') }}" method="GET" @submit="isOpen = false" class="flex items-center border border-gray-300 rounded-lg overflow-hidden focus-within:ring-2 focus-within:ring-[#008060] transition shadow-md">
                            <input x-model="query" // TANPA .debounce.300ms
                                   @input="fetchSuggestions()"
                                   @focus="isOpen = (query.length >= 3 && suggestions.length > 0)"
                                   name="query"
                                   placeholder="Cari dokumen, regulasi, atau laporan..."
                                   class="w-full p-3 border-none focus:ring-0 text-base text-gray-700 bg-white"
                                   autocomplete="off">
                            <button type="submit" class="bg-[#008060] text-white p-3 hover:bg-[#00664e] transition duration-200 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </form>

                        {{-- Kotak Saran (Suggestions) --}}
                        <div x-show="isOpen && query.length >= 3"
                             x-transition:enter.duration.300ms
                             class="absolute z-30 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-60 overflow-y-auto">

                            {{-- Loading State --}}
                            <div x-show="loading" class="p-3 text-center text-sm text-gray-500">
                                Mencari...
                            </div>

                            {{-- Suggestions List --}}
                            <template x-for="item in suggestions" :key="item.title">
                                <a :href="item.url" class="flex items-center p-3 text-left hover:bg-gray-100 transition border-b border-gray-100 last:border-b-0">
                                    <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                    <span x-text="item.title" class="text-sm font-medium text-gray-800"></span>
                                </a>
                            </template>

                        </div>
                    </div>
                    {{-- === AKHIR SEARCH BAR === --}}

                </div>

                {{-- Konten Kanan (Simulasi Gambar/Ilustrasi) --}}
                <div class="lg:w-1/2 flex justify-center">
                    <div class="w-full max-w-lg h-64 sm:h-80 bg-gray-200 rounded-xl shadow-2xl flex items-center justify-center text-gray-500 italic">

                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="bg-gray-50 border-b border-gray-100 py-4 shadow-inner"></div>

    {{-- 2. LAYANAN KATEGORI --}}
    <section id="kategori" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Layanan Kami</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8">
                <a href="#" class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-[#008060] hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                    <div class="flex items-center space-x-4 mb-3">
                        <svg class="w-8 h-8 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v2m-7 11v-5a2 2 0 012-2h2a2 2 0 012 2v5m-11 0h10"></path></svg>
                        <h3 class="text-xl font-semibold text-gray-900 group-hover:text-[#008060]">Informasi Berkala</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Dokumen yang diumumkan secara rutin dan wajib diperbaharui. Contoh: Laporan Tahunan, Keuangan.</p>
                </a>
                <a href="#" class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-yellow-500 hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                    <div class="flex items-center space-x-4 mb-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        <h3 class="text-xl font-semibold text-gray-900 group-hover:text-yellow-600">Serta Merta</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Informasi yang harus diumumkan segera demi keselamatan publik. Contoh: Bencana, Kejadian Darurat.</p>
                </a>
                <a href="#" class="bg-white p-6 rounded-xl shadow-lg border-b-4 border-gray-400 hover:shadow-xl transition duration-300 transform hover:-translate-y-1 group">
                    <div class="flex items-center space-x-4 mb-3">
                        <svg class="w-8 h-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9h-3M12 21a9 9 0 01-9-9m9 9v-3M3 12h3M12 3v3"></path></svg>
                        <h3 class="text-xl font-semibold text-gray-900 group-hover:text-gray-600">Setiap Saat</h3>
                    </div>
                    <p class="text-gray-600 text-sm">Informasi yang dapat diakses kapan pun oleh pemohon. Contoh: Regulasi, Prosedur Layanan.</p>
                </a>
            </div>
        </div>
    </section>

    {{-- 3. PENGAJUAN PERMOHONAN --}}
    <section id="ajukan" class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Ajukan Permohonan Informasi Kapan Saja</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-white p-6 rounded-xl shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-[#e0f2f1] rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Permohonan Informasi Publik</h3>
                    <p class="text-gray-600 text-sm mb-6">Ajukan permohonan informasi publik Anda secara resmi.</p>
                    <a href="{{ route('ppid.request') }}" class="inline-block bg-[#008060] text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-[#00664e] transition duration-300 shadow-md">
                        Ajukan Permohonan
                    </a>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Pengajuan Keberatan</h3>
                    <p class="text-gray-600 text-sm mb-6">Mengajukan keberatan jika permintaan informasi ditolak.</p>
                    <a href="{{ route('ppid.objection') }}" class="inline-block bg-red-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition duration-300 shadow-md">
                        Ajukan Keberatan
                    </a>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-lg text-center">
                    <div class="flex justify-center mb-4">
                        <div class="w-20 h-20 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Status Permohonan Informasi</h3>
                    <p class="text-gray-600 text-sm mb-6">Lacak status permohonan informasi publik Anda di sini.</p>
                    <a href="{{ route('ppid.status') }}" class="inline-block bg-blue-600 text-white px-6 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition duration-300 shadow-md">
                        Lihat Status
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- 4. LAPORAN & DOKUMEN PENTING (Tombol Diperbarui) --}}
    <section id="laporan" class="py-16 lg:py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Laporan & Dokumen Penting</h2>

            <div x-data="{
                        currentIndex: 0,
                        reportsCount: {{ $totalReports }},
                        maxSlides: {{ $maxOffset }},

                        next() {
                            if (this.currentIndex === this.maxSlides) {
                                this.currentIndex = 0; // Loop ke awal
                            } else {
                                this.currentIndex++;
                            }
                        },

                        prev() {
                            if (this.currentIndex === 0) {
                                this.currentIndex = this.maxOffset; // Loop ke akhir
                            } else {
                                this.currentIndex--;
                            }
                        }
                     }"
                 class="relative">
                <div class="overflow-hidden">
                    <div class="flex transition-transform duration-500"
                          :style="`transform: translateX(-${currentIndex * (100 / {{ $slidesPerView }})}%)`">
                        @foreach ($reports as $index => $report)
                            <div class="flex-shrink-0 w-full sm:w-1/2 lg:w-1/3 p-4">
                                <div class="bg-gray-50 p-6 rounded-xl shadow-lg border-t-4 border-[#008060] text-center h-full flex flex-col items-center justify-between transition duration-300 hover:shadow-xl">
                                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Laporan Keuangan {{ $report['year'] }}</h3>
                                    <div class="w-32 h-40 bg-gray-200 border border-gray-300 rounded-sm flex items-center justify-center text-xs italic text-gray-500 my-4">
                                        [Cover Report {{ $report['year'] }}]
                                    </div>
                                    <p class="text-xs text-gray-500 mb-3">{{ $report['detail'] }}</p>

                                    {{-- TOMBOL UNDUH BARU MEMICU MODAL --}}
                                    <button @click="$dispatch('open-download-modal', { title: 'Laporan Keuangan {{ $report['year'] }}' })"
                                        type="button"
                                        class="text-[#008060] font-semibold hover:text-[#00664e] transition text-sm flex items-center">
                                        Download Dokumen (PDF)
                                    </button>
                                    {{-- AKHIR TOMBOL BARU --}}

                                </div>
                            </div>
                        @endforeach

                    </div>
                </div>
                <button @click="prev()"
                        class="absolute left-0 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-70 p-3 rounded-full hover:bg-[#008060] transition duration-300 z-10 ml-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <button @click="next()"
                        class="absolute right-0 top-1/2 transform -translate-y-1/2 bg-gray-800 bg-opacity-70 p-3 rounded-full hover:bg-[#008060] transition duration-300 z-10 mr-2">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
                <div class="flex justify-center space-x-2 mt-8">
                    <template x-for="i in maxSlides + 1" :key="i">
                        <button @click="currentIndex = i - 1"
                                class="w-3 h-3 rounded-full transition duration-300"
                                :class="{ 'bg-[#008060]': currentIndex === i - 1, 'bg-gray-300': currentIndex !== i - 1 }"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    {{-- 5. KONTAK PPID --}}
    <section id="kontak" class="py-16 lg:py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-800 text-center mb-10">Kontak PPID</h2>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                <div class="bg-white p-8 rounded-xl shadow-lg">
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">PPID PT. FOOD STATION TJIPINANG JAYA</h3>
                    <div class="space-y-4 text-gray-700">
                        <p class="text-lg font-semibold">KANTOR PUSAT FOOD STATION</p>
                        <div class="flex items-start space-x-3">
                            <svg class="w-6 h-6 text-[#008060] flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p>Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1 Jakarta Timur 13230</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-[#008060] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            <p>Telp. 021-471 8011, 471 7990, 471 7993 | Fax. 021-4786 5611</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-[#008060] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"></path></svg>
                            <p>Whatsapp. 082137001200</p>
                        </div>
                        <div class="flex items-center space-x-3">
                            <svg class="w-6 h-6 text-[#008060] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            <p>Email: ppid@foodstation.co.id</p>
                        </div>
                        <hr class="border-gray-300 pt-4">
                        <div class="pt-2">
                             <p class="font-semibold text-gray-900 text-lg mb-2">Jadwal Pelayanan Informasi Publik</p>
                             <p>Senin s.d. Jumat: Pukul 08:00 s.d. 17:00 WIB</p>
                             <p class="text-sm italic text-gray-500">(Istirahat Pukul 12:00 s.d. 13:00)</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden h-96">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3966.331206122421!2d106.881272!3d-6.213053!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e69f41757805903%3A0x6b447432cc13b1a!2sPT.%20Food%20Station%20Tjipinang%20Jaya!5e0!3m2!1sid!2sid!4v1700000000000!5m2!1sid!2sid"
                        width="100%"
                        height="100%"
                        style="border:0;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>


    {{-- MODAL DOWNLOAD LAPORAN DENGAN ALPINE.JS --}}
    <div x-data="{
        showModal: false,
        isSubmitting: false,
        formSuccess: false,
        reportTitle: '',
        user: { name: '', phone: '', email: '', purpose: 'Pribadi', institution: '' },
        successEmail: '',

        // FUNGSI SIMULASI SUBMIT FORM
        submitDownloadForm: async function() {
            this.isSubmitting = true;

            const formData = {
                _token: '{{ csrf_token() }}', // Wajib untuk Laravel security
                name: this.user.name,
                phone: this.user.phone,
                email: this.user.email,
                purpose: this.user.purpose,
                institution: this.user.institution,
                reportTitle: this.reportTitle // Mengirim judul laporan juga
            };

            try {
                const response = await fetch('{{ route('report.download') }}', { // Menggunakan route yang sudah dibuat
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });

                const data = await response.json();

                if (data.success) {
                    this.formSuccess = true;
                    this.successEmail = data.email;
                } else {
                    // Handle error response dari server
                    alert('Gagal mengirim tautan: ' + data.message);
                    // Anda bisa tambahkan logika untuk menampilkan error di modal
                }
            } catch (error) {
                console.error('Error saat fetch:', error);
                alert('Terjadi kesalahan koneksi.');
            } finally {
                this.isSubmitting = false;
            }
        }

    }"
    @open-download-modal.window="
        showModal = true;
        reportTitle = $event.detail.title;
        formSuccess = false; // Reset status
        user = { name: '', phone: '', email: '', purpose: 'Pribadi', institution: '' }; // Reset form
    "
    x-show="showModal"
    style="display: none"
    class="fixed inset-0 z-50 overflow-y-auto"
    aria-labelledby="modal-title"
    role="dialog"
    aria-modal="true">

        <div class="flex items-center justify-center min-h-screen p-4 text-center sm:p-0">
            {{-- Background Overlay --}}
            <div x-show="showModal" x-transition.opacity @click="showModal = false"
                class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            {{-- Modal Panel --}}
            <div x-show="showModal" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                {{-- Konten Modal --}}
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">

                    {{-- 1. Konten Form (x-show: !formSuccess) --}}
                    <div x-show="!formSuccess">
                        <div class="sm:flex sm:items-start">
                            <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-[#e0f2f1] sm:mx-0 sm:h-10 sm:w-10">
                                <svg class="h-6 w-6 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            </div>
                            <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    Unduh Dokumen
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        Isi formulir ini untuk menerima tautan unduh <span class="font-semibold text-gray-800" x-text="reportTitle"></span> melalui email.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Form Download --}}
                        <form @submit.prevent="submitDownloadForm()" class="mt-5 space-y-4">

                            {{-- Nama --}}
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                                <input x-model="user.name" type="text" id="name" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            </div>

                            {{-- Nomor Telepon --}}
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input x-model="user.phone" type="tel" id="phone" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                                <input x-model="user.email" type="email" id="email" required class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            </div>

                            {{-- Keperluan --}}
                            <div>
                                <label for="purpose" class="block text-sm font-medium text-gray-700">Keperluan</label>
                                <select x-model="user.purpose" id="purpose" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                                    <option>Pribadi</option>
                                    <option>Instansi</option>
                                </select>
                            </div>

                            {{-- Nama Instansi (Muncul jika Keperluan = Instansi) --}}
                            <div x-show="user.purpose === 'Instansi'" x-transition>
                                <label for="institution" class="block text-sm font-medium text-gray-700">Nama Instansi</label>
                                <input x-model="user.institution" type="text" id="institution" :required="user.purpose === 'Instansi'" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm p-2">
                            </div>

                            {{-- Tombol Submit --}}
                            <div class="sm:flex sm:flex-row-reverse pt-4">
                                <button type="submit" :disabled="isSubmitting"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#008060] text-base font-medium text-white hover:bg-[#00664e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#008060] sm:ml-3 sm:w-auto sm:text-sm">
                                    <span x-show="!isSubmitting">Kirim Tautan Unduh</span>
                                    <span x-show="isSubmitting">Mengirim...</span>
                                </button>
                                <button type="button" @click="showModal = false"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- 2. Konten Sukses (x-show: formSuccess) --}}
                    <div x-show="formSuccess" class="py-10 text-center">
                        <svg class="mx-auto h-16 w-16 text-[#008060]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <h3 class="mt-4 text-xl font-semibold text-gray-900">Berhasil!</h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Tautan untuk unduh laporan <span class="font-medium" x-text="reportTitle"></span> sudah terkirim.
                        </p>
                        <p class="text-sm font-bold text-[#008060] mt-1" x-text="successEmail"></p>
                    </div>

                </div>

                <div x-show="formSuccess" class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                     <button type="button" @click="showModal = false"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-[#008060] text-base font-medium text-white hover:bg-[#00664e] focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#008060] sm:w-auto sm:text-sm">
                        Tutup
                    </button>
                </div>

            </div>
        </div>
    </div>
    {{-- AKHIR MODAL --}}

@endsection
