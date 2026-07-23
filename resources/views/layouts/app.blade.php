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
        :root {
            --fs-green-light: #00A66C;
            --fs-green: #008060;
            --fs-green-dark: #00664e;
            --fs-ink: #0A0E0D;
        }
        body { font-family: 'Plus Jakarta Sans', 'Poppins', system-ui, sans-serif; }
        .fs-gradient { background-image: linear-gradient(135deg, #00A66C 0%, #008060 45%, #00664e 100%); }
        .fs-gradient-text {
            background-image: linear-gradient(135deg, #00A66C, #00664e);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .dark .fs-gradient-text {
            background-image: linear-gradient(135deg, #34d399, #00A66C);
            -webkit-background-clip: text; background-clip: text; color: transparent;
        }
        .fs-dot-pattern { background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px); background-size: 26px 26px; }
    </style>
</head>
<body class="bg-white text-gray-800 antialiased min-h-screen flex flex-col transition-colors duration-300 dark:bg-[#0A0E0D] dark:text-gray-200">

    {{-- Memuat Header (Navigasi) --}}
    @include('layouts.header')

    {{-- Konten Spesifik Halaman --}}
    <main class="flex-grow">
        @yield('content')
    </main>

    {{-- Memuat Footer --}}
    @include('layouts.footer')

</body>
</html>
