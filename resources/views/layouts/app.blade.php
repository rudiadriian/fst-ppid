{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PPID - Layanan Informasi Publik')</title>

    {{-- Memuat Tailwind CSS melalui Vite --}}
    @vite('resources/css/app.css')

    {{-- Font Eksternal: Mengganti Inter dengan Poppins (Bobot 400, 500, 600, 700, 800) --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
{{-- Mengubah kelas font-[] menjadi font-sans (dan definisikan Poppins di app.css) --}}
<body class="bg-gray-50 font-sans antialiased min-h-screen flex flex-col">

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
