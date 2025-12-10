<header x-data="{ open_mobile: false }" class="sticky top-0 z-50 bg-white shadow-md">

    {{-- MAIN BAR (Logo & Navigasi Desktop) --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">

            {{-- Logo PPID (Kiri) --}}
            <a href="/" class="flex flex-col items-start leading-none">
                <span class="text-4xl font-extrabold fs-logo-text tracking-tight">PPID</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest mt-1">FOOD STATION</span>
            </a>

            {{-- Navigasi & Mobile Menu Toggle (Kanan) --}}
            <div class="flex items-center">

                {{-- Navigasi Utama (Desktop) - MEMILIKI DROPDOWN --}}
                <nav class="hidden lg:flex items-center space-x-8 mr-6">

                    <a href="/" class="fs-nav-active">Beranda</a>

                    {{-- Dropdown: Profil --}}
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="fs-nav-link flex items-center">
                            Profil
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="{{ route('ppid.profile', 'singkat') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Profil Singkat</a>
                                <a href="{{ route('ppid.profile', 'struktur') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Struktur PPID</a>
                                <a href="{{ route('ppid.profile', 'visi-misi') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Visi & Misi</a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown: Informasi Publik --}}
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="fs-nav-link flex items-center">
                            Informasi Publik
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open"
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="transform opacity-0 scale-95"
                            x-transition:enter-end="transform opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-75"
                            x-transition:leave-start="transform opacity-100 scale-100"
                            x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5">
                             <div class="py-1">
                                <a href="{{ route('ppid.information', 'berkala') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Informasi Berkala</a>
                                <a href="{{ route('ppid.information', 'serta-merta') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Informasi Serta Merta</a>
                                <a href="{{ route('ppid.information', 'setiap-saat') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:fs-logo-text">Informasi Setiap Saat</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('ppid.regulation') }}" class="fs-nav-link">Regulasi</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="fs-nav-link flex items-center">
                            Standar Pelayanan
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5">
                            <div class="py-1">
                                <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#008060]">Maklumat Pelayanan</a>
                                <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#008060]">Prosedur Permohonan</a>
                                <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-[#008060]">Jalur & Waktu Layanan</a>
                            </div>
                        </div>
                    </div>

                </nav>

                {{-- Tombol Menu Mobile (Hamburger) - Menggunakan Alpine.js untuk toggle --}}
                <button @click="open_mobile = !open_mobile" class="lg:hidden text-gray-500 hover:text-[var(--color-fs-primary)] p-2 rounded-md transition duration-200">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU CONTENT (Sekarang di-toggle oleh 'open_mobile') --}}
    <div x-show="open_mobile" x-collapse.duration.300ms class="lg:hidden bg-gray-50 border-t border-gray-100 pb-4">
        <nav class="flex flex-col space-y-2 px-4 py-2">
            <a href="/" class="fs-nav-link py-2 border-b border-gray-100 hover:pl-2">Beranda</a>
            <a href="/profil" class="fs-nav-link py-2 border-b border-gray-100 hover:pl-2">Profil</a>
            <a href="/informasi-publik" class="fs-nav-link py-2 border-b border-gray-100 hover:pl-2">Informasi Publik</a>
            <a href="/regulasi" class="fs-nav-link py-2 border-b border-gray-100 hover:pl-2">Regulasi</a>
            <a href="/standar-pelayanan" class="fs-nav-link py-2 hover:pl-2">Standar Pelayanan</a>
        </nav>
    </div>
</header>

{{-- FITUR SEARCHING DIBAWAH NAVBAR (Tetap di sini) --}}
