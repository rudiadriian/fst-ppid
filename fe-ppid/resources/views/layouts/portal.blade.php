{{-- Kerangka Portal Pengguna.
     Header dan footer sama persis dengan halaman publik (layouts.app);
     yang berbeda hanya isi body: menu modul di kiri, konten modul di kanan. --}}
@extends('layouts.app')

@section('content')

    @php
        $akun = auth('pemohon')->user();

        $menu = [
            ['label' => __('Dashboard'), 'route' => 'akun.dashboard', 'aktif' => ['akun.dashboard'],
             'ikon' => 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6'],
            ['label' => __('Permohonan Informasi'), 'route' => 'akun.permohonan.index', 'aktif' => ['akun.permohonan.*'],
             'ikon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
            ['label' => __('Permohonan Keberatan'), 'route' => 'akun.keberatan.index', 'aktif' => ['akun.keberatan.*'],
             'ikon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.398 16c-.77 1.333.192 3 1.732 3z'],
            ['label' => __('Histori Permohonan'), 'route' => 'akun.histori', 'aktif' => ['akun.histori'],
             'ikon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];

        $menuPengaturan = [
            ['label' => __('Profil'), 'route' => 'akun.profil', 'aktif' => ['akun.profil']],
            ['label' => __('Data Pemohon & Berkas'), 'route' => 'akun.data-pemohon', 'aktif' => ['akun.data-pemohon']],
            ['label' => __('Ubah Password'), 'route' => 'akun.password.form', 'aktif' => ['akun.password.form']],
        ];

        $itemClass = 'flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold transition-colors';
        $itemOff = $itemClass.' text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] dark:text-gray-300 dark:hover:bg-white/5 dark:hover:text-[#3E9C6C]';
        $itemOn = $itemClass.' fs-gradient-accent text-white shadow-lg shadow-emerald-900/20';

        $subClass = 'block px-4 py-2.5 rounded-lg text-sm transition-colors';
        $subOff = $subClass.' text-gray-600 hover:bg-emerald-50 hover:text-[#10462F] dark:text-gray-300 dark:hover:bg-white/5';
        $subOn = $subClass.' font-bold text-[#10462F] bg-emerald-50 dark:bg-white/5 dark:text-[#3E9C6C]';
    @endphp

    {{-- Sapaan --}}
    <section class="relative fs-gradient overflow-hidden">
        <div class="absolute inset-0 fs-dot-pattern opacity-40"></div>
        <div class="relative z-10 max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-12 flex flex-wrap items-center gap-4 justify-between">
            <div>
                <p class="text-sm font-semibold tracking-widest uppercase text-white/70 mb-2">{{ __('Portal Pengguna') }}</p>
                <h1 class="text-2xl lg:text-3xl font-bold text-white leading-tight">@yield('portal-judul', __('Dashboard'))</h1>
            </div>
            <div class="flex items-center gap-3">
                @if ($akun?->foto)
                    <img src="{{ route('media.show', ['path' => $akun->foto]) }}" alt="" class="w-12 h-12 rounded-full object-cover ring-2 ring-white/40">
                @else
                    <span class="w-12 h-12 rounded-full bg-white/15 text-white text-lg font-bold flex items-center justify-center ring-2 ring-white/30">
                        {{ \Illuminate\Support\Str::upper(\Illuminate\Support\Str::substr($akun?->nama ?? '?', 0, 1)) }}
                    </span>
                @endif
                <div class="text-white">
                    <p class="font-semibold leading-tight">{{ $akun?->nama }}</p>
                    <p class="text-xs text-white/70">{{ $akun?->email }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-8 lg:py-12 bg-[#F3ECDD] dark:bg-[#082217]">
        <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-[260px_minmax(0,1fr)] gap-6">

            {{-- Menu modul --}}
            <aside class="lg:sticky lg:top-24 h-max bg-white dark:bg-[#0B2A1D] rounded-2xl border border-gray-100 dark:border-white/10 p-3">
                <nav class="space-y-1.5">
                    @foreach ($menu as $item)
                        <a href="{{ route($item['route']) }}" class="{{ request()->routeIs(...$item['aktif']) ? $itemOn : $itemOff }}">
                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="{{ $item['ikon'] }}"/></svg>
                            {{ $item['label'] }}
                        </a>
                    @endforeach

                    <div class="pt-3 mt-3 border-t border-gray-100 dark:border-white/10">
                        <p class="px-4 pb-2 text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500">{{ __('Pengaturan') }}</p>
                        @foreach ($menuPengaturan as $item)
                            <a href="{{ route($item['route']) }}" class="{{ request()->routeIs(...$item['aktif']) ? $subOn : $subOff }}">{{ $item['label'] }}</a>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('akun.logout') }}" class="pt-3 mt-3 border-t border-gray-100 dark:border-white/10">
                        @csrf
                        <button type="submit" class="{{ $subOff }} w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 dark:hover:bg-white/5">
                            {{ __('Logout') }}
                        </button>
                    </form>
                </nav>
            </aside>

            {{-- Konten modul --}}
            <div class="space-y-6 min-w-0">
                @if (session('status'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-100 text-sm text-[#10462F] dark:bg-white/5 dark:border-white/10 dark:text-[#3E9C6C]" role="status">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 rounded-xl bg-red-50 border border-red-100 text-sm text-red-700 dark:bg-red-500/10 dark:border-red-500/20 dark:text-red-300" role="alert">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $pesan)
                                <li>{{ $pesan }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('portal')
            </div>
        </div>
    </section>

@endsection
