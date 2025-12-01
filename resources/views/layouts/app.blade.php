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
    
    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Stack Styles (Untuk CSS Leaflet/Peta) --}}
    @stack('styles')

    <style>
        /* =========================================
           1. KONFIGURASI NAVBAR UTAMA
           ========================================= */
        nav, .navbar, header {
            position: sticky;           /* Tetap di atas saat scroll */
            position: -webkit-sticky;   /* Support Safari */
            top: 0;
            z-index: 9999;              /* Layer paling atas */
            background-color: var(--color-primary-dark, #001f3f); 
            padding: 0;                 /* Reset padding agar hitungan akurat */
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        /* Pastikan item navigasi relatif terhadap parent */
        .navbar-nav .nav-item {
            position: relative; 
            height: 100%;
            display: flex;
            align-items: center;
        }

        /* --- SOLUSI JEMBATAN (THE BRIDGE FIX) --- */
        /* Kita perbesar area teks navbar ke bawah agar menyentuh dropdown */
        .navbar-nav .nav-link {
            padding-top: 25px;    /* Sesuaikan tinggi navbar */
            padding-bottom: 25px; /* PENTING: Perluas area hover ke bawah */
            display: flex;
            align-items: center;
            transition: color 0.3s;
        }

        /* =========================================
           2. KONFIGURASI DROPDOWN (ANTI-TUTUP)
           ========================================= */
        .dropdown-menu {
            display: none;      /* Default sembunyi */
            position: absolute;
            
            /* TRICK: Tarik dropdown ke atas (90%) supaya menumpuk sedikit dengan navbar */
            /* Ini menghilangkan celah kosong di antara navbar dan menu */
            top: 90% !important; 
            left: 0;
            margin-top: 0 !important;
            
            /* Styling Visual */
            min-width: 200px;
            background-color: #ffffff;
            border: none;
            border-radius: 8px;
            border-top: 4px solid #f7a731; /* Aksen Emas di atas */
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            z-index: 10000;
            padding: 0.5rem 0;
        }

        /* JEMBATAN CADANGAN (INVISIBLE BRIDGE) */
        /* Membuat area transparan di atas dropdown sebagai pengaman ganda */
        .dropdown-menu::before {
            content: "";
            display: block;
            position: absolute;
            top: -30px; /* Area aman di atas */
            left: 0;
            width: 100%;
            height: 30px;
            background: transparent;
        }

        /* =========================================
           3. LOGIKA HOVER (MUNCULKAN MENU)
           ========================================= */
        
        /* Menu muncul saat Mouse ada di Nav Item ATAU sedang di dalam Dropdown */
        .nav-item.dropdown:hover > .dropdown-menu,
        .dropdown-menu:hover {
            display: block;
            animation: slideUp 0.2s ease forwards;
        }

        /* Animasi Slide Naik Halus */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* =========================================
           4. STYLING ITEM MENU
           ========================================= */
        .dropdown-item {
            padding: 12px 20px;
            font-weight: 500;
            color: #333;
            transition: all 0.2s;
        }

        .dropdown-item:hover {
            background-color: #f7a731; /* Warna Emas saat hover */
            color: #fff;               /* Teks Putih */
            padding-left: 25px;        /* Efek geser kanan */
        }

        /* =========================================
           5. STYLING LEAFLET POPUP (BAWAAN ANDA)
           ========================================= */
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