@php $locale = app()->getLocale(); @endphp

{{-- Top utility bar --}}
<div class="hidden lg:block bg-[#F7F9F8] text-gray-500 text-xs border-b border-gray-100 dark:bg-[#0A0E0D] dark:text-white/50 dark:border-white/5">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-9">
        <div class="flex items-center gap-5">
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#008060] dark:text-[#00A66C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                ppid@foodstation.co.id
            </span>
            <span class="flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5 text-[#008060] dark:text-[#00A66C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                (021) 4718011
            </span>
        </div>
        <div class="flex items-center gap-1.5">
            <svg class="w-3.5 h-3.5 text-[#008060] dark:text-[#00A66C]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ __('Senin–Jumat, 08.00–17.00 WIB') }}
        </div>
    </div>
</div>

<header x-data="{ open_mobile: false, scrolled: false }"
        @scroll.window="scrolled = (window.pageYOffset > 10)"
        class="sticky top-0 z-50 bg-white/95 backdrop-blur border-b border-gray-100 transition-all duration-300 dark:bg-[#0A0E0D]/95 dark:border-white/10"
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
                        $activeSlug   = request()->route('slug');

                        $navLinkClass       = 'text-sm font-semibold text-gray-600 hover:text-[#008060] transition-colors duration-200 px-3.5 py-2 rounded-lg hover:bg-emerald-50 dark:text-gray-300 dark:hover:text-[#00A66C] dark:hover:bg-white/5';
                        $navLinkActiveClass = 'text-sm font-semibold text-[#008060] px-3.5 py-2 rounded-lg bg-emerald-50 dark:bg-white/5 dark:text-[#00A66C]';
                        $dropdownItemClass       = 'block px-4 py-2.5 text-sm text-gray-600 hover:bg-emerald-50 hover:text-[#008060] transition-colors dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#00A66C]';
                        $dropdownItemActiveClass = 'block px-4 py-2.5 text-sm font-bold text-[#008060] bg-emerald-50 border-l-2 border-[#008060] dark:bg-white/5 dark:text-[#00A66C] dark:border-[#00A66C]';

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
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#111917] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $dropItem($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                                <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $dropItem($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur PPID') }}</a>
                                <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $dropItem($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                            </div>
                        </div>
                    </div>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isInfo) }} flex items-center gap-1">
                            {{ __('Informasi Publik') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#111917] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.information', 'berkala') }}" class="{{ $dropItem($isInfo && $activeSlug === 'berkala') }}">{{ __('Informasi Berkala') }}</a>
                                <a href="{{ route('ppid.information', 'serta-merta') }}" class="{{ $dropItem($isInfo && $activeSlug === 'serta-merta') }}">{{ __('Informasi Serta Merta') }}</a>
                                <a href="{{ route('ppid.information', 'setiap-saat') }}" class="{{ $dropItem($isInfo && $activeSlug === 'setiap-saat') }}">{{ __('Informasi Setiap Saat') }}</a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('ppid.regulation') }}" class="{{ $navItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>

                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button class="{{ $navItem($isService) }} flex items-center gap-1">
                            {{ __('Pelayanan') }}
                            <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                            class="absolute z-50 mt-1 w-56 rounded-xl shadow-xl bg-white ring-1 ring-black/5 overflow-hidden origin-top-left dark:bg-[#111917] dark:ring-white/10">
                            <div class="py-1.5">
                                <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $dropItem($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                                <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $dropItem($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan') }}</a>
                                <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $dropItem($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur & Waktu Layanan') }}</a>
                            </div>
                        </div>
                    </div>
                </nav>

                {{-- Theme toggle --}}
                <button @click="$store.theme.toggle()" class="p-2.5 rounded-lg text-gray-600 hover:text-[#008060] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#00A66C] dark:hover:bg-white/5" :title="$store.theme.dark ? '{{ __('Mode Terang') }}' : '{{ __('Mode Gelap') }}'">
                    <svg x-show="!$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
                    <svg x-show="$store.theme.dark" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                </button>

                {{-- Lang (server-side switch) --}}
                <div class="relative hidden sm:block" x-data="{ open_lang: false }" @click.outside="open_lang = false">
                    <button @click="open_lang = !open_lang" class="flex items-center gap-1 px-2.5 py-2 rounded-lg text-gray-600 hover:text-[#008060] hover:bg-emerald-50 transition duration-200 dark:text-gray-300 dark:hover:text-[#00A66C] dark:hover:bg-white/5">
                        <span class="uppercase font-semibold text-sm">{{ $locale }}</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                    <div x-show="open_lang" x-transition class="absolute z-50 mt-1 w-40 rounded-xl shadow-xl bg-white ring-1 ring-black/5 right-0 overflow-hidden origin-top-right dark:bg-[#111917] dark:ring-white/10" style="display:none">
                        <div class="py-1.5">
                            <a href="/set-locale/id" class="{{ $dropdownItemClass }} {{ $locale === 'id' ? 'font-bold' : '' }}">🇮🇩 {{ __('Indonesia') }}</a>
                            <a href="/set-locale/en" class="{{ $dropdownItemClass }} {{ $locale === 'en' ? 'font-bold' : '' }}">🇬🇧 {{ __('English') }}</a>
                        </div>
                    </div>
                </div>

                {{-- CTA --}}
                <a href="{{ route('ppid.request') }}" class="hidden sm:inline-flex items-center gap-1.5 fs-gradient text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-lg shadow-emerald-900/20 hover:shadow-emerald-900/40 hover:-translate-y-0.5 transition-all duration-200 {{ $isRequest ? 'ring-2 ring-[#008060] ring-offset-2 ring-offset-white dark:ring-[#00A66C] dark:ring-offset-[#0A0E0D]' : '' }}">
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
    <div x-show="open_mobile" x-collapse.duration.300ms class="lg:hidden bg-white border-t border-gray-100 pb-5 dark:bg-[#0A0E0D] dark:border-white/10" style="display:none">
        <nav class="flex flex-col gap-1 px-4 pt-3">
            @php
                $mobileLinkClass = 'block px-4 py-3 text-base font-semibold text-gray-700 hover:bg-emerald-50 hover:text-[#008060] rounded-xl transition duration-150 dark:text-gray-200 dark:hover:bg-white/5 dark:hover:text-[#00A66C]';
                $mobileSubClass = 'block pl-8 pr-4 py-2.5 text-sm text-gray-500 hover:bg-emerald-50 hover:text-[#008060] rounded-lg transition dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-[#00A66C]';
                $mobileLinkActiveClass = 'block px-4 py-3 text-base font-bold text-[#008060] bg-emerald-50 rounded-xl dark:bg-white/5 dark:text-[#00A66C]';
                $mobileSubActiveClass  = 'block pl-8 pr-4 py-2.5 text-sm font-bold text-[#008060] bg-emerald-50 rounded-lg border-l-2 border-[#008060] dark:bg-white/5 dark:text-[#00A66C] dark:border-[#00A66C]';

                $mobileItem = fn ($active) => $active ? $mobileLinkActiveClass : $mobileLinkClass;
                $mobileSub  = fn ($active) => $active ? $mobileSubActiveClass : $mobileSubClass;
            @endphp

            <a href="/" class="{{ $mobileItem($isHome) }}" @if ($isHome) aria-current="page" @endif>{{ __('Beranda') }}</a>

            <div x-data="{ open: {{ $isProfile ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($isProfile) }} w-full flex justify-between items-center">
                    {{ __('Profil') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$isProfile) style="display:none" @endif>
                    <a href="{{ route('ppid.profile', 'singkat') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'singkat') }}">{{ __('Profil Singkat') }}</a>
                    <a href="{{ route('ppid.profile', 'struktur') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'struktur') }}">{{ __('Struktur PPID') }}</a>
                    <a href="{{ route('ppid.profile', 'visi-misi') }}" class="{{ $mobileSub($isProfile && $activeSlug === 'visi-misi') }}">{{ __('Visi & Misi') }}</a>
                </div>
            </div>

            <div x-data="{ open: {{ $isInfo ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($isInfo) }} w-full flex justify-between items-center">
                    {{ __('Informasi Publik') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$isInfo) style="display:none" @endif>
                    <a href="{{ route('ppid.information', 'berkala') }}" class="{{ $mobileSub($isInfo && $activeSlug === 'berkala') }}">{{ __('Informasi Berkala') }}</a>
                    <a href="{{ route('ppid.information', 'serta-merta') }}" class="{{ $mobileSub($isInfo && $activeSlug === 'serta-merta') }}">{{ __('Informasi Serta Merta') }}</a>
                    <a href="{{ route('ppid.information', 'setiap-saat') }}" class="{{ $mobileSub($isInfo && $activeSlug === 'setiap-saat') }}">{{ __('Informasi Setiap Saat') }}</a>
                </div>
            </div>

            <a href="{{ route('ppid.regulation') }}" class="{{ $mobileItem($isRegulation) }}" @if ($isRegulation) aria-current="page" @endif>{{ __('Regulasi') }}</a>

            <div x-data="{ open: {{ $isService ? 'true' : 'false' }} }">
                <button @click="open = !open" class="{{ $mobileItem($isService) }} w-full flex justify-between items-center">
                    {{ __('Standar Pelayanan') }}
                    <svg class="w-4 h-4 transition-transform" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                <div x-show="open" x-collapse @if (!$isService) style="display:none" @endif>
                    <a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'maklumat-pelayanan') }}">{{ __('Maklumat Pelayanan') }}</a>
                    <a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="{{ $mobileSub($isService && $activeSlug === 'prosedur-permohonan') }}">{{ __('Prosedur Permohonan') }}</a>
                    <a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="{{ $mobileSub($isService && $activeSlug === 'jalur-waktu-layanan') }}">{{ __('Jalur & Waktu Layanan') }}</a>
                </div>
            </div>

            {{-- Lang switch mobile --}}
            <div class="flex gap-2 pt-3">
                <a href="/set-locale/id" class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'id' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">🇮🇩 ID</a>
                <a href="/set-locale/en" class="flex-1 text-center py-2.5 rounded-xl text-sm font-semibold border {{ $locale === 'en' ? 'fs-gradient text-white border-transparent' : 'border-gray-200 text-gray-600 dark:border-white/10 dark:text-gray-300' }}">🇬🇧 EN</a>
            </div>

            <div class="pt-3 space-y-2">
                <a href="{{ route('ppid.request') }}" class="block w-full text-center py-3 fs-gradient text-white font-semibold rounded-xl shadow-lg {{ $isRequest ? 'ring-2 ring-[#008060] ring-offset-2 ring-offset-white dark:ring-[#00A66C] dark:ring-offset-[#0A0E0D]' : '' }}">{{ __('Permohonan Informasi') }}</a>
                <a href="{{ route('ppid.status') }}" class="block w-full text-center py-3 font-semibold rounded-xl transition {{ $isStatus ? 'bg-emerald-50 text-[#008060] border border-[#008060] dark:bg-white/5 dark:text-[#00A66C] dark:border-[#00A66C]' : 'border border-gray-200 text-gray-700 hover:bg-gray-50 dark:border-white/10 dark:text-gray-200 dark:hover:bg-white/5' }}">{{ __('Cek Status Tiket') }}</a>
            </div>
        </nav>
    </div>
</header>
