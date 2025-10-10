<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- Judul akan diisi dari child view --}}
    <title>@yield('title') | Kelurahan Sukorame - Admin</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @stack('head_scripts')
</head>
<body>

    {{-- Yields untuk Navbar, Content, dan Footer --}}
    @yield('navbar')

    <main>
        @yield('content')
    </main>

    @yield('footer')

    @stack('scripts')

    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html>
