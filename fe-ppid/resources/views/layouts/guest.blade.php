{{-- resources/views/layouts/guest.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('Login - PPID Food Station'))</title>

    <link rel="icon" href="{{ asset('assets/images/logo/favicon.ico') }}" sizes="any">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('assets/images/logo/favicon.ico') }}">

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

    {{-- Tailwind via Vite --}}
    @vite('resources/css/app.css')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="font-sans antialiased text-gray-900 dark:text-gray-100">
    <div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-gray-100 dark:bg-gray-900">
        <div class="mb-6">
            <a href="/">
                <img src="{{ asset('assets/images/logo/logo_fs.png') }}" alt="Food Station"
                     class="h-16 w-auto" onerror="this.style.display='none'">
            </a>
        </div>

        <div class="w-full sm:max-w-md px-6 py-8 bg-white dark:bg-gray-800 shadow-md rounded-lg">
            {{ $slot }}
        </div>

        <p class="mt-6 text-xs text-gray-500 dark:text-gray-400">
            &copy; {{ date('Y') }} PT Food Station Tjipinang Jaya (Perseroda) &middot; PPID
        </p>
    </div>
</body>
</html>
