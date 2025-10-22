<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | Kelurahan Sukorame</title>

    {{-- CSS Utama Anda --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    {{-- Chart.js (jika perlu) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- ====================================================== --}}
    {{--    BARU: Tambahkan @stack('styles') DI SINI        --}}
    {{-- ====================================================== --}}
    @stack('styles')
    {{-- Ini akan memuat CSS dari halaman anak (seperti CSS Leaflet) --}}

</head>
<body>

    @include('partials.navbar')

    {{-- Konten halaman akan masuk di sini --}}
    @yield('content')

    @include('partials.footer')

    {{-- Skrip dari halaman anak akan dimuat di sini --}}
    @stack('scripts')

    {{-- Skrip utama Anda (jika ada) --}}
    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html>
