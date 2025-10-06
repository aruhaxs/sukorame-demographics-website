<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Admin - Kelurahan Sukorame</title>

    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body style="background-color: #122841;">

    {{-- Navbar Admin --}}
    @include('partials.admin_navbar')

    {{-- Konten halaman --}}
    <div class="admin-page-section">
        @yield('content')
    </div>

    @stack('scripts')

</body>
</html>
