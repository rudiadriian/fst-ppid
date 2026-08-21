{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('PPID - Layanan Informasi Publik'))</title>

    {{-- Favicon perusahaan --}}
    <link rel="icon" href="{{ asset('assets/images/logo/favicon.ico') }}" sizes="any">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo/favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/images/logo/logo_fs.png') }}">

    {{-- Pre-paint theme (hindari FOUC dark/light) --}}
    <script>
        (function () {
            try {
                var t = localStorage.getItem('theme');
                if (t === 'dark' || (!t && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                }
            } catch (e) {}
        })();
    </script>

    {{-- Memuat Tailwind CSS melalui Vite --}}
    @vite('resources/css/app.css')

    {{-- Font Eksternal: Plus Jakarta Sans (korporat modern) + Poppins fallback --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    {{-- Store tema (dark/light) --}}
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('theme', {
                dark: document.documentElement.classList.contains('dark'),
                toggle() {
                    this.dark = !this.dark;
                    document.documentElement.classList.toggle('dark', this.dark);
                    try { localStorage.setItem('theme', this.dark ? 'dark' : 'light'); } catch (e) {}
                }
            });
        });
    </script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        /* Palet mengikuti referensi desain: hijau hutan + oranye + krem. */
        :root {
            --fs-green-light: #3E9C6C;
            --fs-green: #10462F;
            --fs-green-dark: #0B3524;
            --fs-orange: #E87317;
            --fs-orange-dark: #C85C10;
            --fs-orange-light: #F5A94C;
            --fs-cream: #FAF6EC;
            --fs-cream-dark: #F3ECDD;
            --fs-ink: #071A12;
        }
        body { font-family: 'Plus Jakarta Sans', 'Poppins', system-ui, sans-serif; }

        /* Blok/banner struktural: hijau hutan pekat. */
        .fs-gradient { background-image: linear-gradient(135deg, #175A3C 0%, #10462F 45%, #08281B 100%); }

        /* Tombol aksi utama: oranye (aksen desain). */
        .fs-gradient-accent { background-image: linear-gradient(135deg, #F08C2A 0%, #E87317 55%, #C85C10 100%); }

        .fs-gradient-text {
            background-image: linear-gradient(135deg, #E87317, #10462F);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .dark .fs-gradient-text {
            background-image: linear-gradient(135deg, #F5A94C, #3E9C6C);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .fs-dot-pattern { background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px); background-size: 26px 26px; }

        /* Header melayang (beranda): transparan di atas banner sampai halaman
           digulir, lalu berubah putih. Warna isinya dibalik lewat satu kelas
           `.hdr-top` di elemen <header>, jadi tiap tautan tidak perlu punya
           aturan warna sendiri. */
        .hdr-top { background-color: transparent; border-color: transparent; box-shadow: none; }
        .hdr-top .hdr-brand { color: #fff; }
        .hdr-top .hdr-divider { background-color: rgba(255,255,255,.4); }
        .hdr-top .hdr-logo { background-color: #fff; border-radius: .5rem; padding: .15rem .4rem; }
        .hdr-top .hdr-nav-link,
        .hdr-top .hdr-ctl,
        .hdr-top .hdr-outline { color: #fff; }
        .hdr-top .hdr-nav-link:hover,
        .hdr-top .hdr-ctl:hover,
        .hdr-top .hdr-outline:hover { color: #fff; background-color: rgba(255,255,255,.16); }
        .hdr-top .hdr-nav-active { background-color: rgba(255,255,255,.18); }
        .hdr-top .hdr-outline { border-color: rgba(255,255,255,.55); }

        /* Ken Burns: gambar slider yang sedang tayang merayap membesar pelan
           supaya perpindahan slide tidak terasa seperti gambar diam. */
        @keyframes fs-kenburns { from { transform: scale(1); } to { transform: scale(1.08); } }
        .fs-kenburns { animation: fs-kenburns 8s ease-out forwards; }

        @media (prefers-reduced-motion: reduce) {
            .fs-kenburns { animation: none; }
        }

        /* Judul section dua warna (acuan: theme-color.jpeg) — sebagian kata
           memakai warna aksen oranye. Dipasang lewat helper $judulDua. */
        .fs-title-accent { color: #E87317; }
        .dark .fs-title-accent { color: #F5A94C; }
        .fs-title-accent-soft { color: #F5A94C; }

        /* Tiga tingkat oranye untuk latar kartu (acuan: theme-color-card.png).
           Dipakai bergantian lewat helper $cardTier di Blade agar kartu
           berurutan tidak memakai nada yang sama. */
        .fs-card-1 { background-color: #FE6B17; }
        .fs-card-2 { background-color: #FD8B02; }
        .fs-card-3 { background-color: #FFA849; }
        .dark .fs-card-1 { background-color: #E85F0E; }
        .dark .fs-card-2 { background-color: #E67D02; }
        .dark .fs-card-3 { background-color: #E8963C; }
    </style>
</head>
<body class="bg-cream-100 text-gray-800 antialiased min-h-screen flex flex-col transition-colors duration-300 dark:bg-[#071A12] dark:text-gray-200">

    {{-- Lewati navigasi: bantu pengguna keyboard & screen reader --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:absolute focus:z-[9999] focus:top-3 focus:left-3 focus:rounded-lg focus:bg-[#E87317] focus:px-4 focus:py-2 focus:text-white focus:shadow-lg">
        {{ __('Lewati ke konten utama') }}
    </a>

    {{-- Memuat Header (Navigasi) --}}
    @include('layouts.header')

    {{-- Konten Spesifik Halaman --}}
    <main id="main-content" tabindex="-1" class="flex-grow">
        @yield('content')
    </main>

    {{-- Memuat Footer --}}
    @include('layouts.footer')

    {{-- Penghalang Verifikasi Data Diri Pemohon (hanya di portal pengguna) --}}
    @auth('pemohon')
        @include('akun.partials.popup-verifikasi')
    @endauth

    {{-- Backsound jingle Food Station (langkah 84). Bisa dimatikan lewat
         tombol di pojok; pilihannya diingat antar halaman. --}}
    @include('partials.backsound')

    {{-- Widget aksesibilitas (EqualWeb) --}}
    @include('partials.accessibility')

    {{-- Skrip khusus halaman (mis. penggambar sampul PDF di halaman Regulasi). --}}
    @stack('scripts')

</body>
</html>
