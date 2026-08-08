@php $locale = app()->getLocale(); @endphp

{{-- Top utility bar dihapus supaya header lebih full & clean (mengikuti desain acuan).
     Alamat, telepon, dan email tetap tampil di footer (dari $cmsKontak). --}}

<header x-data="{ open_mobile: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 transition-all duration-300 dark:bg-[#071A12]/95 dark:border-white/10"
        :class="scrolled ? 'shadow-md shadow-gray-200/60 dark:shadow-black/40' : ''">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-[72px]">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="PT Food Station Tjipinang Jaya (Perseroda)"
                     class="h-9 sm:h-10 w-auto transition-transform duration-200 group-hover:scale-[1.03] dark:bg-white dark:rounded-lg dark:px-2 dark:py-1">
                <span class="w-px h-9 bg-gray-200 dark:bg-white/15"></span>
                <span class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-none dark:text-white">PPID</span>
            </a>

            {{-- Desktop nav --}}
            <div class="flex items-center gap-2">
                <nav class="hidden lg:flex items-center gap-1 mr-1">
                    @php
                        // Status modul aktif (dipakai desktop + mobile nav)
                        $isHome       = request()->is('/');
                        $isProfile    = request()->routeIs('ppid.profile');
                        $isInfo       = request()->routeIs('ppid.information');
                        $isRegulation = request()->routeIs('ppid.regulation');
                        $isService    = request()->routeIs('ppid.service');
                        $isRequest    = request()->routeIs('ppid.request');
                        $isStatus     = request()->routeIs('ppid.status');
                        $isLegalBasis = request()->routeIs('ppid.legal_basis');
                        $isExcluded   = request()->routeIs('ppid.excluded');
                        $isReport     = request()->routeIs('ppid.report');
                        $isRegister   = request()->routeIs('ppid.register');
                        $activeSlug   = request()->route('slug');

                        $navLinkClass       = 'text-sm font-semibold text-gray-600 hover:text-[#10462F] transition-colors duration-200 px-3.5 py-2 rounded-lg hover:bg-emerald-50 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5';
                        $navLinkActiveClass = 'text-sm font-semibold text-[#10462F] px-3.5 py-2 rounded-lg bg-emerald-50 dark:bg-white/5 dark:text-[#3E9C6C]';
                        $dropdownItemClass       = 'block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] transition-colors dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
                        $dropdownItemActiveClass = 'block px-4 py-2.5 text-sm font-bold text-[#10462F] bg-emerald-50 border-l-2 border-[#10462F] dark:bg-white/5 dark:text-[#3E9C6C] dark:border-[#3E9C6C]';

                        $navItem  = fn ($active) => $active ? $navLinkActiveClass : $navLinkClass;
                        $dropItem = fn ($active) => $active ? $dropdownItemActiveClass : $dropdownItemClass;
                    @endphp
                    <a href="/" class="{{ $navItem($isHome) }}" @if ($isHome) aria-current="page" @endif>{{ __('Beranda') }}</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isProfile || $isLegalBasis) }} flex items-center gap-1">
                            {{ __('Profil') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $dropItem($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                                <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $dropItem($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur PPID') }}</a>
                                <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $dropItem($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                                <a href="{{ route('ppid.legal_basis') }}" class="{{ $dropItem($isLegalBasis) }}">{{ __('Dasar Hukum') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isInfo || $isExcluded) }} flex items-center gap-1">
                            {{ __('Informasi Publik') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                {{-- Daftar kategori mengikuti modul Kategori Informasi di CMS. --}}
                                @foreach ($cmsKategoriInformasi as $kat)
                                    <a href="{{ route('ppid.information', $kat['slug']) }}" class="{{ $dropItem($isInfo && $activeSlug === $kat['slug']) }}">{{ $kat['nama'] }}</a>
                                @endforeach
                                <a href="{{ route('ppid.excluded') }}" class="{{ $dropItem($isExcluded) }}">{{ __('Informasi Dikecualikan') }}</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('ppid.regulation') }}" class="{{ $navItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>

                    {{-- Kanal konten yang isinya dikelola penuh lewat CMS. --}}
                    <a href="{{ route('ppid.news.index') }}" class="{{ $navItem(request()->routeIs('ppid.news.*')) }}">{{ __('Berita') }}</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isService || $isReport || $isRegister) }} flex items-center gap-1">
                            {{ __('Pelayanan') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-72 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $dropItem($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                                <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $dropItem($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan') }}</a>
                                <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $dropItem($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur & Waktu Layanan') }}</a>
                                <a href="{{ route('ppid.register') }}" class="{{ $dropItem($isRegister) }}">{{ __('Register Permohonan Informasi') }}</a>
                                <a href="{{ route('ppid.report', 'statistik-informasi') }}" class="{{ $dropItem($isReport && $activeSlug === 'statistik-informasi') }}">{{ __('Laporan Statistik Informasi Publik') }}</a>
                                <a href="{{ route('ppid.report', 'pelayanan-informasi') }}" class="{{ $dropItem($isReport && $activeSlug === 'pelayanan-informasi') }}">{{ __('Laporan Pelayanan Informasi') }}</a>
                            </div>
                        </div>
                    </div>
                </nav>

                {{-- Theme toggle --}}
                <button @click="$store.theme.toggle()" class="p-2.5 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5" :title="$store.theme.dark ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                {{-- Lang (server-side switch) --}}
                <div class="relative hidden sm:block" x-data="{ open_lang: false }" @click.outside="open_lang = false">
                    <button @click="open_lang = !open_lang" class="flex items-center gap-1 px-2.5 py-2 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5">
                        <span class="uppercase font-semibold text-sm">{{ $locale }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open_lang" x-transition class="absolute z-50 mt-1 w-40 rounded-xl shadow-xl bg-white ring-1 ring-black/5 right-0 overflow-hidden origin-top-right dark:bg-[#0A2619] dark:ring-white/10" style="display:none">
                        <div class="py-1.5">
                            <a href="/set-locale/id" class="{{ $dropdownItemClass }} {{ $locale === 'id' ? 'font-bold' : '' }}">🇮🇩 {{ __('Indonesia') }}</a>
                            <a href="/set-locale/en" class="{{ $dropdownItemClass }} {{ $locale === 'en' ? 'font-bold' : '' }}">🇬🇧 {{ __('English') }}</a>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('ppid.request') }}" class="hidden sm:inline-flex items-center gap-1.5 fs-gradient-accent text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-900/20 hover:shadow-emerald-900/40 hover:-translate-y-0.5 transition-all duration-200 {{ $isRequest ? 'ring-2 ring-[#10462F] ring-offset-2 ring-offset-white dark:ring-[#3E9C6C] dark:ring-offset-[#071A12]' : '' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    {{ __('Permohonan') }}
                </a>

                {{-- Mobile toggle --}}
                <button @click="open_mobile = !open_mobile" class="lg:hidden text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition duration-200 dark:text-gray-200 dark:hover:bg-white/5">
                    <svg x-show="!open_mobile" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    <svg x-show="open_mobile" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        </div>
    </div>

    {{-- Mobile menu --}}
    <div x-show="open_mobile" x-collapse.duration.300ms class="lg:hidden bg-white border-t border-gray-100 pb-5 dark:bg-[#071A12] dark:border-white/10" style="display:none">
        <nav class="flex flex-col gap-1 px-4 pt-3">
            @php
                $mobileLinkClass = 'block px-4 py-3 text-base font-semibold text-gray-700 hover:bg-emerald-50 hover:text-[#10462F] rounded-xl transition duration-150 dark:text-gray-200 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
                $mobileSubClass = 'block pl-8 pr-4 py-2.5 text-sm text-gray-500 hover:bg-emerald-50 hover:text-[#10462F] rounded-lg transition dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
                $mobileLinkActiveClass = 'block px-4 py-3 text-base font-bold text-[#10462F] bg-emerald-50 rounded-xl dark:bg-white/5 dark:text-[#3E9C6C]';
                $mobileSubActiveClass  = 'block pl-8 pr-4 py-2.5 text-sm font-bold text-[#10462F] bg-emerald-50 rounded-lg border-l-2 border-[#10462F] dark:bg-white/5 dark:text-[#3E9C6C] dark:border-[#3E9C6C]';

                $mobileItem = fn ($active) => $active ? $mobileLinkActiveClass : $mobileLinkClass;
                $mobileSub  = fn ($active) => $active ? $mobileSubActiveClass : $mobileSubClass;
            @endphp

            <a href="/" class="{{ $mobileItem($isHome) }}" @if ($isHome) aria-current="page" @endif>{{ __('Beranda') }}</a>

            @php
                $openProfile = $isProfile || $isLegalBasis;
                $openInfo    = $isInfo || $isExcluded;
                $openService = $isService || $isReport || $isRegister;
            @endphp

            <div x-data="{ open: {{ $openProfile ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openProfile) }} w-full flex justify-between items-center">
                    {{ __('Profil') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openProfile) style="display:none" @endif>
                    <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                    <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur PPID') }}</a>
                    <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                    <a href="{{ route('ppid.legal_basis') }}" class="{{ $mobileSub($isLegalBasis) }}">{{ __('Dasar Hukum') }}</a>
                </div>
            </div>

            <div x-data="{ open: {{ $openInfo ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openInfo) }} w-full flex justify-between items-center">
                    {{ __('Informasi Publik') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openInfo) style="display:none" @endif>
                    @foreach ($cmsKategoriInformasi as $kat)
                        <a href="{{ route('ppid.information', $kat['slug']) }}" class="{{ $mobileSub($isInfo && $activeSlug === $kat['slug']) }}">{{ $kat['nama'] }}</a>
                    @endforeach
                    <a href="{{ route('ppid.excluded') }}" class="{{ $mobileSub($isExcluded) }}">{{ __('Informasi Dikecualikan') }}</a>
                </div>
            </div>

            <a href="{{ route('ppid.regulation') }}" class="{{ $mobileItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>
            <a href="{{ route('ppid.news.index') }}" class="{{ $mobileItem(request()->routeIs('ppid.news.*')) }}">{{ __('Berita') }}</a>

            <div x-data="{ open: {{ $openService ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openService) }} w-full flex justify-between items-center">
                    {{ __('Standar Pelayanan') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openService) style="display:none" @endif>
                    <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                    <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $mobileSub($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan') }}</a>
                    <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur & Waktu Layanan') }}</a>
                    <a href="{{ route('ppid.register') }}" class="{{ $mobileSub($isRegister) }}">{{ __('Register Permohonan Informasi') }}</a>
                    <a href="{{ route('ppid.report', 'statistik-informasi') }}" class="{{ $mobileSub($isReport && $activeSlug === 'statistik-informasi') }}">{{ __('Laporan Statistik Informasi Publik') }}</a>
                    <a href="{{ route('ppid.report', 'pelayanan-informasi') }}" class="{{ $mobileSub($isReport && $activeSlug === 'pelayanan-informasi') }}">{{ __('Laporan Pelayanan Informasi') }}</a>
                </div>
            </div>

            {{-- Lang switch mobile --}}
            <div class="flex gap-2 pt-3">
                <a href="/set-locale/id" class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'id' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">🇮🇩 ID</a>
                <a href="/set-locale/en" class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'en' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">🇬🇧 EN</a>
            </div>

            <div class="pt-3 space-y-2">
                <a href="{{ route('ppid.request') }}" class="block w-full text-center py-3 fs-gradient-accent text-white font-semibold rounded-xl shadow-lg {{ $isRequest ? 'ring-2 ring-[#10462F] ring-offset-2 ring-offset-white dark:ring-[#3E9C6C] dark:ring-offset-[#071A12]' : '' }}">{{ __('Permohonan Informasi') }}</a>
                <a href="{{ route('ppid.status') }}" class="block w-full text-center py-3 font-semibold rounded-xl transition {{ $isStatus ? 'bg-emerald-50 text-[#10462F] border border-[#10462F] dark:bg-white/5 dark:text-[#3E9C6C] dark:border-[#3E9C6C]' : 'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5' }}">{{ __('Cek Status Tiket') }}</a>
            </div>
        </nav>
    </div>
</header>
