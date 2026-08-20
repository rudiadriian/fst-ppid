@php
    $locale = app()->getLocale();

    // Beranda memakai hero setinggi layar. Di sana header melayang di atas
    // banner (transparan) dan baru berlatar putih setelah halaman digulir.
    // Halaman lain tetap memakai header putih yang menempel seperti biasa.
    $headerMelayang = request()->is('/');
@endphp

{{-- Top utility bar dihapus supaya header lebih full & clean (mengikuti desain acuan).
     Alamat, telepon, dan email tetap tampil di footer (dari $cmsKontak). --}}

<header x-data="{ open_mobile: false, scrolled: false }"
        x-init="scrolled = (window.pageYOffset > 10)"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        @if ($headerMelayang)
            class="fixed inset-x-0 top-0 z-50 transition-all duration-300"
            {{-- Menu mobile yang terbuka ikut memaksa latar putih supaya
                 daftar menunya tidak mengambang di atas gambar. --}}
            :class="(scrolled || open_mobile)
                ? 'bg-white/95 backdrop-blur border-b border-gray-100 shadow-md shadow-gray-200/60 dark:bg-[#071A12]/95 dark:border-white/10 dark:shadow-black/40'
                : 'hdr-top'"
        @else
            class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 transition-all duration-300 dark:bg-[#071A12]/95 dark:border-white/10"
            :class="scrolled ? 'shadow-md shadow-gray-200/60 dark:shadow-black/40' : ''"
        @endif>
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-[72px]">
            {{-- Logo --}}
            <a href="/" class="flex items-center gap-3 group">
                <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="PT Food Station Tjipinang Jaya (Perseroda)"
                     class="hdr-logo h-9 sm:h-10 w-auto transition-transform duration-200 group-hover:scale-[1.03] dark:bg-white dark:rounded-lg dark:px-2 dark:py-1">
                <span class="hdr-divider w-px h-9 bg-gray-200 dark:bg-white/15"></span>
                <span class="hdr-brand text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight leading-none dark:text-white">PPID</span>
            </a>

            {{-- Desktop nav --}}
            <div class="flex items-center gap-2">
                <nav class="hidden lg:flex items-center gap-1 mr-1">
                    @php
                        // Status modul aktif (dipakai desktop + mobile nav)
                        $isHome       = request()->is('/');
                        $isProfile    = request()->routeIs('ppid.profile');
                        $isInfo       = request()->routeIs('ppid.information');
                        $isInfoIndex  = request()->routeIs('ppid.information.index');
                        $isNews       = request()->routeIs('ppid.news.*');
                        $isRegulation = request()->routeIs('ppid.regulation');
                        $isService    = request()->routeIs('ppid.service');
                        $isRequest    = request()->routeIs('ppid.request');
                        $isStatus     = request()->routeIs('ppid.status');
                        $isExcluded   = request()->routeIs('ppid.excluded');
                        $isReport     = request()->routeIs('ppid.report');
                        // Sub modul "Register Permohonan Informasi" diganti pintu Registrasi Akun.
                        $isRegister   = request()->routeIs('akun.register');
                        $activeSlug   = request()->route('slug');

                        // Kelas penanda `hdr-nav-link` / `hdr-nav-active` dipakai
                        // CSS `.hdr-top` untuk memutihkan teks saat header masih
                        // transparan di atas banner beranda.
                        $navLinkClass       = 'hdr-nav-link text-sm font-semibold text-gray-600 hover:text-[#10462F] transition-colors duration-200 px-3.5 py-2 rounded-lg hover:bg-emerald-50 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5';
                        $navLinkActiveClass = 'hdr-nav-link hdr-nav-active text-sm font-semibold text-[#10462F] px-3.5 py-2 rounded-lg bg-emerald-50 dark:bg-white/5 dark:text-[#3E9C6C]';
                        $dropdownItemClass       = 'block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] transition-colors dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
                        $dropdownItemActiveClass = 'block px-4 py-2.5 text-sm font-bold text-[#10462F] bg-emerald-50 border-l-2 border-[#10462F] dark:bg-white/5 dark:text-[#3E9C6C] dark:border-[#3E9C6C]';

                        $navItem  = fn ($active) => $active ? $navLinkActiveClass : $navLinkClass;
                        $dropItem = fn ($active) => $active ? $dropdownItemActiveClass : $dropdownItemClass;
                    @endphp
                    <a href="/" class="{{ $navItem($isHome) }}" @if ($isHome) aria-current="page" @endif>{{ __('Beranda') }}</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isProfile) }} flex items-center gap-1">
                            {{ __('Profil') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $dropItem($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                                <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $dropItem($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur Organisasi') }}</a>
                                <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $dropItem($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                                <a href="{{ route('ppid.profile', 'tugas-fungsi-wewenang') }}" class="{{ $dropItem($isProfile && $activeSlug === 'tugas-fungsi-wewenang') }}">{{ __('Tugas, Fungsi dan Wewenang') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isInfo || $isExcluded || $isInfoIndex || $isNews) }} flex items-center gap-1">
                            {{ __('Informasi Publik') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.information.index') }}" class="{{ $dropItem($isInfoIndex) }}">{{ __('Daftar Informasi Publik') }}</a>
                                <a href="{{ route('ppid.excluded') }}" class="{{ $dropItem($isExcluded) }}">{{ __('Daftar Informasi Dikecualikan') }}</a>

                                {{-- Daftar kategori mengikuti modul Kategori Informasi di CMS. --}}
                                @foreach ($cmsKategoriInformasi as $kat)
                                    <a href="{{ route('ppid.information', $kat['slug']) }}" class="{{ $dropItem($isInfo && $activeSlug === $kat['slug']) }}">{{ __($kat['nama']) }}</a>
                                @endforeach

                                <a href="{{ route('ppid.news.index') }}" class="{{ $dropItem($isNews) }}">{{ __('Berita') }}</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('ppid.regulation') }}" class="{{ $navItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isService) }} flex items-center gap-1">
                            {{ __('Standar Layanan') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $dropItem($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                                <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $dropItem($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan Informasi') }}</a>
                                <a href="{{ route('ppid.service', 'prosedur-keberatan') }}" class="{{ $dropItem($isService && $activeSlug === 'prosedur-keberatan') }}">{{ __('Prosedur Permohonan Keberatan') }}</a>
                                <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $dropItem($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur dan Waktu Layanan') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isReport || $isRegister || $isRequest) }} flex items-center gap-1">
                            {{ __('Layanan') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-80 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#0A2619] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.request') }}" class="{{ $dropItem(false) }}">{{ __('Permohonan Informasi Publik') }}</a>
                                <a href="{{ route('ppid.objection') }}" class="{{ $dropItem(false) }}">{{ __('Pengajuan Keberatan Informasi Publik') }}</a>
                                {{-- Pengunjung harus punya akun sebelum bisa mengajukan permohonan,
                                     jadi pintu masuknya ditaruh di menu Layanan. Disembunyikan
                                     untuk yang sudah masuk — akunnya sudah ada. --}}
                                @guest('pemohon')
                                    <a href="{{ route('akun.register') }}" class="{{ $dropItem($isRegister) }}">{{ __('Registrasi Akun') }}</a>
                                @endguest
                                <a href="{{ route('ppid.report') }}" class="{{ $dropItem($isReport) }}">{{ __('Laporan Pelayanan Informasi') }}</a>
                            </div>
                        </div>
                    </div>
                </nav>

                {{-- Theme toggle --}}
                <button @click="$store.theme.toggle()" class="hdr-ctl p-2.5 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5" :title="$store.theme.dark ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                {{-- Lang (server-side switch) --}}
                <div class="relative hidden sm:block" x-data="{ open_lang: false }" @click.outside="open_lang = false">
                    <button @click="open_lang = !open_lang" type="button"
                            aria-haspopup="true" :aria-expanded="open_lang ? 'true' : 'false'"
                            aria-label="{{ __('Ganti bahasa') }}"
                            class="hdr-ctl flex items-center gap-1.5 px-2.5 py-2 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5">
                        @include('partials.bendera', ['kode' => $locale, 'kelas' => 'w-5'])
                        <span class="uppercase font-semibold text-sm">{{ $locale }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open_lang" x-transition class="absolute z-50 mt-1 w-44 rounded-xl shadow-xl bg-white ring-1 ring-black/5 right-0 overflow-hidden origin-top-right dark:bg-[#0A2619] dark:ring-white/10" style="display:none">
                        <div class="py-1.5">
                            <a href="{{ route('locale.set', 'id') }}" class="{{ $dropdownItemClass }} flex items-center gap-2.5 {{ $locale === 'id' ? 'font-bold' : '' }}">
                                @include('partials.bendera', ['kode' => 'id', 'kelas' => 'w-5'])
                                {{ __('Indonesia') }}
                            </a>
                            <a href="{{ route('locale.set', 'en') }}" class="{{ $dropdownItemClass }} flex items-center gap-2.5 {{ $locale === 'en' ? 'font-bold' : '' }}">
                                @include('partials.bendera', ['kode' => 'en', 'kelas' => 'w-5'])
                                {{ __('English') }}
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Akun pengunjung (guard `pemohon`) — terpisah dari akun petugas. --}}
                @php $akun = auth('pemohon')->user(); @endphp
                @if ($akun)
                    {{-- Lonceng umpan balik petugas; ikut tampil di halaman
                         publik supaya pemohon tidak perlu membuka portal dulu
                         untuk tahu pengajuannya sudah ditanggapi. --}}
                    @include('akun.partials.lonceng')

                    <div class="relative hidden sm:block" x-data="{ open_akun: false }" @click.outside="open_akun = false">
                        <button @click="open_akun = !open_akun" class="hdr-ctl flex items-center gap-2 px-3 py-2 rounded-lg text-gray-600 hover:text-[#10462F] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#3E9C6C] dark:hover:bg-white/5">
                            @include('akun.partials.avatar', ['pemohon' => $akun, 'ukuran' => 'w-7 h-7'])
                            <span class="text-sm font-semibold max-w-[110px] truncate">{{ $akun->nama }}</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open_akun" x-transition class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 right-0 overflow-hidden origin-top-right dark:bg-[#0A2619] dark:ring-white/10" style="display:none">
                            <div class="py-1.5">
                                <a href="{{ route('akun.dashboard') }}" class="{{ $dropdownItemClass }}">{{ __('Akun Saya') }}</a>
                                <a href="{{ route('akun.profil') }}" class="{{ $dropdownItemClass }}">{{ __('Data Diri Saya') }}</a>
                                <form method="POST" action="{{ route('akun.logout') }}">
                                    @csrf
                                    <button type="submit" class="{{ $dropdownItemClass }} w-full text-left">{{ __('Keluar') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('akun.login') }}" class="hdr-outline hidden sm:inline-flex items-center gap-1.5 px-3.5 py-2.5 rounded-xl text-sm font-semibold border border-gray-200 text-gray-700 hover:bg-gray-50 transition dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5">
                        {{ __('Masuk') }}
                    </a>
                @endif

                {{-- CTA — hanya untuk pengunjung yang belum masuk.
                     Tautannya mengalihkan ke /akun/permohonan/baru, dan itu
                     sudah jadi modul tersendiri di Portal Pemohon; bagi yang
                     sudah masuk tombol ini cuma pintu kedua ke halaman yang
                     sama. --}}
                @if (!$akun)
                    <a href="{{ route('ppid.request') }}" class="hidden sm:inline-flex items-center gap-1.5 fs-gradient-accent text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-900/20 hover:shadow-emerald-900/40 hover:-translate-y-0.5 transition-all duration-200 {{ $isRequest ? 'ring-2 ring-[#10462F] ring-offset-2 ring-offset-white dark:ring-[#3E9C6C] dark:ring-offset-[#071A12]' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        {{ __('Permohonan') }}
                    </a>
                @endif

                {{-- Mobile toggle --}}
                <button @click="open_mobile = !open_mobile" class="hdr-ctl lg:hidden text-gray-700 p-2 rounded-lg hover:bg-gray-100 transition duration-200 dark:text-gray-200 dark:hover:bg-white/5">
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
                $openProfile = $isProfile;
                $openInfo    = $isInfo || $isExcluded || $isInfoIndex || $isNews;
                $openStandar = $isService;
                $openLayanan = $isReport || $isRegister;
            @endphp

            <div x-data="{ open: {{ $openProfile ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openProfile) }} w-full flex justify-between items-center">
                    {{ __('Profil') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openProfile) style="display:none" @endif>
                    <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                    <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur Organisasi') }}</a>
                    <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                    <a href="{{ route('ppid.profile', 'tugas-fungsi-wewenang') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'tugas-fungsi-wewenang') }}">{{ __('Tugas, Fungsi dan Wewenang') }}</a>
                </div>
            </div>

            <div x-data="{ open: {{ $openInfo ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openInfo) }} w-full flex justify-between items-center">
                    {{ __('Informasi Publik') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openInfo) style="display:none" @endif>
                    <a href="{{ route('ppid.information.index') }}" class="{{ $mobileSub($isInfoIndex) }}">{{ __('Daftar Informasi Publik') }}</a>
                    <a href="{{ route('ppid.excluded') }}" class="{{ $mobileSub($isExcluded) }}">{{ __('Daftar Informasi Dikecualikan') }}</a>
                    @foreach ($cmsKategoriInformasi as $kat)
                        <a href="{{ route('ppid.information', $kat['slug']) }}" class="{{ $mobileSub($isInfo && $activeSlug === $kat['slug']) }}">{{ __($kat['nama']) }}</a>
                    @endforeach
                    <a href="{{ route('ppid.news.index') }}" class="{{ $mobileSub($isNews) }}">{{ __('Berita') }}</a>
                </div>
            </div>

            <a href="{{ route('ppid.regulation') }}" class="{{ $mobileItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>

            <div x-data="{ open: {{ $openStandar ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openStandar) }} w-full flex justify-between items-center">
                    {{ __('Standar Layanan') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openStandar) style="display:none" @endif>
                    <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                    <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $mobileSub($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan Informasi') }}</a>
                    <a href="{{ route('ppid.service', 'prosedur-keberatan') }}" class="{{ $mobileSub($isService && $activeSlug === 'prosedur-keberatan') }}">{{ __('Prosedur Permohonan Keberatan') }}</a>
                    <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur dan Waktu Layanan') }}</a>
                </div>
            </div>

            <div x-data="{ open: {{ $openLayanan ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($openLayanan) }} w-full flex justify-between items-center">
                    {{ __('Layanan') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$openLayanan) style="display:none" @endif>
                    <a href="{{ route('ppid.request') }}" class="{{ $mobileSub(false) }}">{{ __('Permohonan Informasi Publik') }}</a>
                    <a href="{{ route('ppid.objection') }}" class="{{ $mobileSub(false) }}">{{ __('Pengajuan Keberatan Informasi Publik') }}</a>
                    @guest('pemohon')
                        <a href="{{ route('akun.register') }}" class="{{ $mobileSub($isRegister) }}">{{ __('Registrasi Akun') }}</a>
                    @endguest
                    <a href="{{ route('ppid.report') }}" class="{{ $mobileSub($isReport) }}">{{ __('Laporan Pelayanan Informasi') }}</a>
                </div>
            </div>

            {{-- Lang switch mobile --}}
            <div class="flex gap-2 pt-3">
                <a href="{{ route('locale.set', 'id') }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'id' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">
                    @include('partials.bendera', ['kode' => 'id', 'kelas' => 'w-5'])
                    ID
                </a>
                <a href="{{ route('locale.set', 'en') }}" class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'en' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">
                    @include('partials.bendera', ['kode' => 'en', 'kelas' => 'w-5'])
                    EN
                </a>
            </div>

            <div class="pt-3 space-y-2">
                {{-- Sama seperti CTA di header desktop: disembunyikan setelah
                     masuk karena sudah ada modulnya di Portal Pemohon. --}}
                @if (!$akun)
                    <a href="{{ route('ppid.request') }}" class="block w-full text-center py-3 fs-gradient-accent text-white font-semibold rounded-xl shadow-lg {{ $isRequest ? 'ring-2 ring-[#10462F] ring-offset-2 ring-offset-white dark:ring-[#3E9C6C] dark:ring-offset-[#071A12]' : '' }}">{{ __('Permohonan Informasi') }}</a>
                @endif
                <a href="{{ route('ppid.status') }}" class="block w-full text-center py-3 font-semibold rounded-xl transition {{ $isStatus ? 'bg-emerald-50 text-[#10462F] border border-[#10462F] dark:bg-white/5 dark:text-[#3E9C6C] dark:border-[#3E9C6C]' : 'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5' }}">{{ __('Cek Status Tiket') }}</a>

                @if ($akun)
                    {{-- Avatar ikut tampil di menu mobile supaya foto profil
                         terlihat sama di kedua tampilan header. --}}
                    <a href="{{ route('akun.dashboard') }}" class="flex items-center justify-center gap-2.5 w-full py-3 font-semibold rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5">
                        @include('akun.partials.avatar', ['pemohon' => $akun, 'ukuran' => 'w-7 h-7'])
                        {{ __('Akun Saya') }}
                    </a>
                    <a href="{{ route('akun.notifikasi') }}" class="block w-full text-center py-3 font-semibold rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5">
                        {{ __('Notifikasi') }}
                    </a>
                    <form method="POST" action="{{ route('akun.logout') }}">
                        @csrf
                        <button type="submit" class="block w-full text-center py-3 font-semibold rounded-xl text-red-600 hover:bg-red-50 dark:hover:bg-white/5">{{ __('Keluar') }}</button>
                    </form>
                @else
                    <a href="{{ route('akun.login') }}" class="block w-full text-center py-3 font-semibold rounded-xl border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5">{{ __('Masuk / Daftar Akun') }}</a>
                @endif
            </div>
        </nav>
    </div>
</header>
