<style>
    .fs-primary-text { color: #008060; }
    .fs-hover-text:hover { color: #008060; }
</style>

<header x-data="{ open_mobile: false, lang: 'ID' }" class="sticky top-0 z-50 bg-white/95 backdrop-blur-sm shadow-lg transition duration-300">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            <a href="/" class="flex flex-col items-start leading-none group">
                <span class="text-4xl font-extrabold text-[#008060] tracking-tight transition duration-200">PPID</span>
                <span class="text-xs text-gray-500 uppercase tracking-widest mt-1 group-hover:text-gray-700 transition duration-200">FOOD STATION</span>
            </a>

            <div class="flex items-center space-x-4">
                <nav class="hidden lg:flex items-center space-x-7 mr-4">

                    @php
                        $navLinkClass = 'text-sm font-semibold text-gray-700 hover:text-[#008060] transition-colors duration-200 py-2';

                        $dropdownItemClass = 'block px-4 py-2 text-sm text-gray-700 hover:bg-emerald-50 hover:text-[#008060] transition-colors';
                    @endphp

                    <a href="/" class="{{ $navLinkClass }} border-b-2 border-[#008060]">Beranda</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navLinkClass }} flex items-center">
                            Profil
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 origin-top-left">
                            <div class="py-1">
                                <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $dropdownItemClass }}">Profil Singkat</a>
                                <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $dropdownItemClass }}">Struktur PPID</a>
                                <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $dropdownItemClass }}">Visi & Misi</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navLinkClass }} flex items-center">
                            Informasi Publik
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 origin-top-left">
                           <div class="py-1">
                                <a href="{{ route('ppid.information', 'berkala') }}" class="{{ $dropdownItemClass }}">Informasi Berkala</a>
                                <a href="{{ route('ppid.information', 'serta-merta') }}" class="{{ $dropdownItemClass }}">Informasi Serta Merta</a>
                                <a href="{{ route('ppid.information', 'setiap-saat') }}" class="{{ $dropdownItemClass }}">Informasi Setiap Saat</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('ppid.regulation') }}" class="{{ $navLinkClass }}">Regulasi</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navLinkClass }} flex items-center">
                            Pelayanan
                            <svg class="w-4 h-4 ml-1 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                            class="absolute z-50 mt-2 w-56 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 origin-top-left">
                            <div class="py-1">
                                <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $dropdownItemClass }}">Maklumat Pelayanan</a>
                                <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $dropdownItemClass }}">Prosedur Permohonan</a>
                                <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $dropdownItemClass }}">Jalur & Waktu Layanan</a>
                            </div>
                        </div>
                    </div>
                </nav>

                <div class="relative" x-data="{ open_lang: false }" @click.outside="open_lang = false">
                    <button @click="open_lang = !open_lang" class="flex items-center space-x-1 p-2 rounded-lg text-gray-600 hover:text-[#008060] bg-gray-100 hover:bg-emerald-50 transition duration-200">
                        <span class="uppercase font-semibold text-sm" x-text="lang"></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 0115 10c0-1.574-.533-3.04-1.517-4.249L12 3m-2.293 2.293L11 6m-1 1l-1 1"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20.25l-2.25-2.25M12 20.25L14.25 18M12 20.25v-2.25m-1.5-1.5v-1.5m3 0v1.5"></path></svg>
                    </button>
                    <div x-show="open_lang" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95"
                        class="absolute z-50 mt-2 w-32 rounded-lg shadow-xl bg-white ring-1 ring-black ring-opacity-5 right-0 origin-top-right">
                        <div class="py-1">
                            <button @click="lang = 'ID'; open_lang = false" class="{{ $dropdownItemClass }} w-full text-left">🇮🇩 Bahasa Indonesia</button>
                            <button @click="lang = 'EN'; open_lang = false" class="{{ $dropdownItemClass }} w-full text-left">🇬🇧 English</button>
                        </div>
                    </div>
                </div>
                <button @click="open_mobile = !open_mobile" class="lg:hidden text-gray-500 hover:text-[#008060] p-2 rounded-md transition duration-200">
                    <svg x-show="!open_mobile" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="open_mobile" class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    <div x-show="open_mobile" x-collapse.duration.300ms class="lg:hidden bg-gray-50 border-t border-emerald-200 pb-4">
        <nav class="flex flex-col space-y-1 px-4 pt-2">
            @php
                $mobileLinkClass = 'block px-3 py-2 text-base font-medium text-gray-700 hover:bg-emerald-100 hover:text-[#008060] rounded-lg transition duration-150';
            @endphp

            <a href="/" class="{{ $mobileLinkClass }}">Beranda</a>

            <div x-data="{ open: false }" class="border-b border-gray-200">
                <button @click="open = !open" class="{{ $mobileLinkClass }} w-full flex justify-between items-center">
                    Profil
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('ppid.profile', 'singkat') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Profil Singkat</a>
                    <a href="{{ route('ppid.profile', 'struktur') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Struktur PPID</a>
                    <a href="{{ route('ppid.profile', 'visi-misi') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Visi & Misi</a>
                </div>
            </div>

            <div x-data="{ open: false }" class="border-b border-gray-200">
                <button @click="open = !open" class="{{ $mobileLinkClass }} w-full flex justify-between items-center">
                    Informasi Publik
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('ppid.information', 'berkala') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Informasi Berkala</a>
                    <a href="{{ route('ppid.information', 'serta-merta') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Informasi Serta Merta</a>
                    <a href="{{ route('ppid.information', 'setiap-saat') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Informasi Setiap Saat</a>
                </div>
            </div>

            <a href="{{ route('ppid.regulation') }}" class="{{ $mobileLinkClass }} border-b border-gray-200">Regulasi</a>

            <div x-data="{ open: false }" class="border-b border-gray-200">
                <button @click="open = !open" class="{{ $mobileLinkClass }} w-full flex justify-between items-center">
                    Standar Pelayanan
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse>
                    <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Maklumat Pelayanan</a>
                    <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Prosedur Permohonan</a>
                    <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="block pl-8 pr-3 py-2 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] rounded-lg">Jalur & Waktu Layanan</a>
                </div>
            </div>
            <div class="pt-4 space-y-2">
                 <a href="{{ route('ppid.request') }}" class="block w-full text-center py-2.5 bg-[#008060] text-white font-bold rounded-lg shadow-md hover:bg-[#00664e] transition">
                    Permohonan Informasi
                </a>
                <a href="{{ route('ppid.status') }}" class="block w-full text-center py-2.5 border border-gray-300 text-gray-700 font-bold rounded-lg hover:bg-gray-100 transition">
                    Cek Status Tiket
                </a>
            </div>

        </nav>
    </div>
</header>
