{{-- CTA strip --}}
<section class="bg-white dark:bg-[#0A0E0D]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mb-16 relative z-20">
        <div class="bg-[#004d3a] rounded-3xl px-8 py-10 lg:px-14 lg:py-12 shadow-2xl shadow-emerald-950/40 flex flex-col lg:flex-row items-center justify-between gap-6 overflow-hidden relative">
            <div class="absolute inset-0 fs-dot-pattern opacity-50"></div>
            <div class="relative z-10 text-center lg:text-left">
                <h3 class="text-2xl lg:text-3xl font-extrabold text-white">{{ __('Butuh Informasi Publik?') }}</h3>
                <p class="text-white/85 mt-1.5">{{ __('Ajukan permohonan Anda secara resmi, transparan, dan gratis.') }}</p>
            </div>
            <div class="relative z-10 flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
                <a href="{{ route('ppid.request') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-white text-[#00664e] text-sm font-bold rounded-xl hover:bg-emerald-50 transition-colors duration-200">{{ __('Ajukan Permohonan') }}</a>
                <a href="{{ route('ppid.status') }}" class="inline-flex items-center justify-center px-6 py-3.5 bg-white/10 border border-white/30 text-white text-sm font-bold rounded-xl hover:bg-white/20 transition-colors duration-200">{{ __('Cek Status Tiket') }}</a>
            </div>
        </div>
    </div>
</section>

<footer id="kontak" class="fs-gradient pt-32 relative overflow-hidden">
    {{-- Pola titik putih agar tidak polos --}}
    <div class="absolute inset-0 fs-dot-pattern opacity-50"></div>
    <div class="absolute -top-20 -right-16 w-80 h-80 bg-white/5 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-24 -left-20 w-96 h-96 bg-black/10 rounded-full blur-3xl"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        {{-- Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-center pb-12 border-b border-white/15">
            <div class="p-4">
                <p class="text-4xl font-extrabold text-white mb-1" x-data="{ count: 0 }" x-init="setInterval(() => { count = Math.floor(Math.random() * 500) + 1200 }, 2000)"><span x-text="count"></span>+</p>
                <p class="text-sm font-semibold text-white/70 uppercase tracking-wide">{{ __('Permohonan Diproses') }}</p>
            </div>
            <div class="p-4">
                <p class="text-4xl font-extrabold text-white mb-1" x-data="{ count: 0 }" x-init="setInterval(() => { count = Math.floor(Math.random() * 100) + 200 }, 2000)"><span x-text="count"></span></p>
                <p class="text-sm font-semibold text-white/70 uppercase tracking-wide">{{ __('Pengunjung Hari Ini') }}</p>
            </div>
            <div class="p-4">
                <p class="text-4xl font-extrabold text-white mb-1">98.5%</p>
                <p class="text-sm font-semibold text-white/70 uppercase tracking-wide">{{ __('Kepuasan Pemohon') }}</p>
            </div>
        </div>

        {{-- Main --}}
        <div class="grid grid-cols-1 md:grid-cols-12 gap-10 lg:gap-16 pt-14">
            <div class="md:col-span-4 space-y-7">
                <a href="/" class="flex items-center gap-3 group">
                    <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="PT Food Station Tjipinang Jaya (Perseroda)"
                         class="h-10 w-auto bg-white rounded-xl px-2.5 py-1.5 shadow-lg shadow-emerald-950/20 transition-transform duration-200 group-hover:scale-[1.03]">
                    <span class="w-px h-10 bg-white/25"></span>
                    <span class="flex flex-col leading-none">
                        <span class="text-xl font-extrabold text-white tracking-tight">PPID</span>
                        <span class="text-[11px] text-white/70 uppercase tracking-widest font-semibold mt-1">{{ __('Transparansi Informasi') }}</span>
                    </span>
                </a>
                <p class="text-sm text-white/75 leading-relaxed">{{ __('PT Food Station Tjipinang Jaya (Perseroda) — BUMD DKI Jakarta yang berkomitmen pada keterbukaan informasi publik sesuai UU No. 14 Tahun 2008.') }}</p>
                <div class="space-y-3 text-sm text-white/80">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-white mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.828 0l-4.243-4.243m-4.243 0a8 8 0 1111.314 0z"></path></svg>
                        <p>Komplek Pasar Induk Beras Cipinang, Jl. Pisangan Lama Selatan No. 1, Jakarta Timur 13230</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <p>(021) 4718011 (Ext. PPID)</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5 text-white flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8m-2 4v7a2 2 0 01-2 2H7a2 2 0 01-2-2v-7"></path></svg>
                        <p>ppid@foodstation.co.id</p>
                    </div>
                </div>
                <div class="flex gap-3 pt-1">
                    @foreach (['Facebook','Twitter','Instagram','Youtube'] as $sos)
                        <a href="#" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/25 text-white flex items-center justify-center transition-colors duration-200" title="{{ $sos }}">
                            @if ($sos === 'Facebook')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M22 12c0-5.52-4.48-10-10-10S2 6.48 2 12c0 4.84 3.44 8.87 8 9.8V15h-2.25V12H10V9.75c0-2.23 1.36-3.45 3.33-3.45 1.05 0 2.11.19 2.11.19V8.5h-1.11c-1.1 0-1.44.68-1.44 1.38V12h2.56l-.41 3H13v6.78c4.56-.93 8-4.97 8-9.8z"></path></svg>
                            @elseif ($sos === 'Twitter')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm6.6 4.75a7.9 7.9 0 01-2.2.6 4.02 4.02 0 001.75-2.1c-.8.5-1.7.9-2.65 1.1a4.03 4.03 0 00-6.85 3.6c-3.37-.17-6.36-1.78-8.48-4.26a4.02 4.02 0 001.25 5.3 4.03 4.03 0 01-1.8-.5v.05c0 1.95 1.38 3.58 3.2 3.96a4.03 4.03 0 01-1.8.07c.5 1.6 2 2.76 3.77 2.8c-1.38 1.08-3.13 1.7-5.02 1.7-.33 0-.65-.02-.97-.06A10 10 0 0012 20c7.47 0 11.5-6.57 11.5-12.44v-.5C20.67 7.3 19.4 6.78 18.6 6.55z"/></svg>
                            @elseif ($sos === 'Instagram')
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M7.8 2h8.4C17.6 2 18 2.4 18 3.6v8.4c0 1.2-.4 1.6-1.6 1.6H7.8C6.6 14 6.2 13.6 6.2 12.4V3.6C6.2 2.4 6.6 2 7.8 2zm8.4 1.6H7.8c-.8 0-1.4.6-1.4 1.4v7.6c0 .8.6 1.4 1.4 1.4h8.4c.8 0 1.4-.6 1.4-1.4V5c0-.8-.6-1.4-1.4-1.4zM12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zm0 8.2c-1.76 0-3.2-1.44-3.2-3.2s1.44-3.2 3.2-3.2 3.2 1.44 3.2 3.2-1.44 3.2-3.2 3.2zM17.4 5.6c-.34 0-.6.26-.6.6s.26.6.6.6.6-.26.6-.6-.26-.6-.6-.6z"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M19.6 4.4a.97.97 0 00-.67-.67C18.15 3.5 12 3.5 12 3.5S5.85 3.5 4.97 3.73a.97.97 0 00-.67.67C4 5.37 4 9.5 4 9.5s0 4.13.29 5.07a.97.97 0 00.67.67C5.85 15.5 12 15.5 12 15.5s6.15 0 7.03-.29a.97.97 0 00.67-.67C20 13.63 20 9.5 20 9.5s0-4.13-.29-5.07zM10 12.4V7.6l5 2.4-5 2.4z"/></svg>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>

            <div class="md:col-span-8 grid grid-cols-2 sm:grid-cols-4 gap-8">
                <div class="space-y-4">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Profil') }}</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('ppid.profile', 'profil-singkat') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Profil Singkat') }}</a></li>
                        <li><a href="{{ route('ppid.profile', 'tugas-dan-wewenang') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Tugas & Wewenang') }}</a></li>
                        <li><a href="{{ route('ppid.profile', 'struktur-organisasi') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Struktur PPID') }}</a></li>
                        <li><a href="{{ route('ppid.profile', 'visi-misi') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Visi & Misi') }}</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Informasi Publik') }}</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('ppid.information', 'berkala') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Informasi Berkala') }}</a></li>
                        <li><a href="{{ route('ppid.information', 'serta-merta') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Informasi Serta Merta') }}</a></li>
                        <li><a href="{{ route('ppid.information', 'setiap-saat') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Informasi Setiap Saat') }}</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Layanan') }}</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('ppid.regulation') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Regulasi & Pedoman') }}</a></li>
                        <li><a href="{{ route('ppid.request') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Permohonan Informasi') }}</a></li>
                        <li><a href="{{ route('ppid.objection') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Pengajuan Keberatan') }}</a></li>
                        <li><a href="{{ route('ppid.status') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Cek Status') }}</a></li>
                    </ul>
                </div>
                <div class="space-y-4">
                    <h5 class="text-sm font-bold text-white uppercase tracking-wide">{{ __('Standar Pelayanan') }}</h5>
                    <ul class="space-y-2.5">
                        <li><a href="{{ route('ppid.service', 'maklumat-pelayanan') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Maklumat Pelayanan') }}</a></li>
                        <li><a href="{{ route('ppid.service', 'prosedur-permohonan') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Prosedur Permohonan') }}</a></li>
                        <li><a href="{{ route('ppid.service', 'jalur-waktu-layanan') }}" class="text-sm text-white/70 hover:text-white transition">{{ __('Jalur & Waktu Layanan') }}</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="mt-14 py-6 border-t border-white/15 text-center md:flex md:justify-between md:text-left">
            <p class="text-xs text-white/70">&copy; {{ date('Y') }} PT FOOD STATION TJIPINANG JAYA (PERSERODA). {{ __('Hak cipta dilindungi.') }}</p>
            <p class="text-xs text-white/70 mt-2 md:mt-0">{{ __('Dikembangkan oleh PPID Food Station') }}</p>
        </div>
    </div>
</footer>
