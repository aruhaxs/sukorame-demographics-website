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
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Stack Styles (Untuk CSS Leaflet/Peta) --}}
    @stack('styles')

    <style>
        /* --- PERBAIKAN NAVBAR (SANGAT PENTING) --- */
        nav, .navbar, header {
            position: sticky;              /* Tetap sticky */
            position: -webkit-sticky;      /* Support Safari */
            top: 0;
            z-index: 9999;                 /* Layer tinggi */
            background-color: var(--color-primary-dark, #001f3f); 
            
            /* KUNCI PERBAIKAN: */
            overflow: visible !important;  /* Izinkan dropdown "tumpah" keluar */
            height: auto !important;       /* Cegah navbar gepeng */
            min-height: 70px;              /* Jaga tinggi minimal */
        }

        /* Pastikan Dropdown Menu punya prioritas lebih tinggi dari Navbar */
        .dropdown-menu {
            z-index: 10000 !important;
            margin-top: 0; /* Rapikan jarak */
        }

        /* --- STYLING POPUP PETA (SINTAKS BARU ANDA) --- */
        .leaflet-popup-content img.popup-img-fixed {
            width: 100%;
            height: auto;
            max-height: 200px;
            object-fit: cover;
            display: block;
            margin-top: 10px;
            border-radius: 4px;
        }

        .leaflet-popup-content p {
            max-height: 100px;
            overflow-y: auto;
        }
    </style>

</head>
<body>

    @include('partials.navbar')

    {{-- Konten halaman --}}
    @yield('content')

    @include('partials.footer')

    {{-- Skrip halaman anak --}}
    @stack('scripts')

    {{-- Skrip utama --}}
    <script src="{{ asset('js/carousel.js') }}"></script>
</body>
</html>