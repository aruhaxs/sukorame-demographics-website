<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - Nama Desa</title>
    
        @vite('resources/css/app.css')

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
        <style>
        /* --- Reset & Gaya Dasar --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #F8F9FA;
            color: #343A40;
        }

        /* --- Lapisan Overlay (Untuk Mobile) --- */
        .overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
            transition: opacity 0.3s ease;
        }
        .overlay.active { display: block; }

        /* --- Navbar Atas (Desktop & Mobile) --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 0.8rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .navbar-logo img {
            height: 40px;
            display: block;
        }

        /* --- Menu Horizontal (Hanya untuk Desktop) --- */
        .top-menu-links {
            display: flex;
            list-style: none;
            gap: 1.5rem;
        }
        .top-menu-links a {
            text-decoration: none;
            color: #343A40;
            font-weight: 500;
            padding-bottom: 5px;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-bottom-color 0.2s;
        }
        .top-menu-links a:hover,
        .top-menu-links a.active {
            color: #1E5631;
            border-bottom-color: #4C9A2A;
        }
        .top-menu-links .logout-button {
            background: none; border: none; font-family: inherit;
            font-size: inherit; color: #dc3545; cursor: pointer;
            font-weight: 500;
        }

        /* --- Tombol Hamburger --- */
        .navbar-toggler {
            display: none;
            width: 40px;
            height: 40px;
            padding: 8px;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            background-color: #ffffff;
            cursor: pointer;
            z-index: 101;
        }
        .navbar-toggler .bar {
            width: 100%;
            height: 3px;
            margin: 4px 0;
            background-color: #1E5631;
            border-radius: 2px;
            transition: transform 0.3s ease-in-out;
        }
        .navbar-toggler.open .bar:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .navbar-toggler.open .bar:nth-child(2) { opacity: 0; }
        .navbar-toggler.open .bar:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* --- Menu Samping dari Kanan --- */
        .side-menu {
            height: 100%;
            width: 280px;
            position: fixed;
            z-index: 102;
            top: 0;
            right: -280px;
            background-color: #1E5631;
            padding-top: 20px;
            transition: right 0.3s ease;
            box-shadow: -2px 0 5px rgba(0,0,0,0.2);
        }
        .side-menu.active {
            right: 0;
        }

        .close-menu-btn {
            position: absolute; top: 10px; right: 15px; background: none; border: none;
            color: #b0c4de; font-size: 2.5rem; font-weight: 300; cursor: pointer;
        }
        .side-menu-links a {
            padding: 15px 25px; text-decoration: none; font-size: 1rem;
            color: #E9ECEF; display: block;
        }
        .side-menu-links a:hover,
        .side-menu-links a.active {
            background-color: rgba(76, 154, 42, 0.2);
            color: #ffffff;
        }
        .side-menu .logout-button {
            background: none; border: none; padding: 15px 25px; width: 100%;
            text-align: left; cursor: pointer; font-size: 1rem;
            font-family: inherit; color: #E9ECEF;
        }
        .side-menu .logout-link {
            position: absolute; bottom: 0; width: 100%; border-top: 1px solid #2a528a;
        }
        
        /* Konten Utama */
        .main-content { padding: 2rem; }

        /* --- ATURAN RESPONSIVE --- */
        @media (max-width: 991.98px) {
            .top-menu-links { display: none; }
            .navbar-toggler { display: block; }
            .main-content { padding: 1rem; }
            .navbar { padding: 0.8rem 1rem; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div class="overlay" id="overlay"></div>

    <aside class="side-menu" id="side-menu">
        <button class="close-menu-btn" id="close-menu-btn" aria-label="Close menu">&times;</button>
        <div class="side-menu-links" style="padding-top: 40px;">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">DASHBOARD</a>
            <a href="{{ route('admin.rt-rw.index') }}" class="{{ request()->routeIs('admin.rt-rw.*') ? 'active' : '' }}">RT/RW</a>
            <a href="{{ route('admin.penduduk.index') }}" class="{{ request()->routeIs('admin.penduduk.*') ? 'active' : '' }}">PENDUDUK</a>
            <a href="{{ route('admin.komoditas.index') }}" class="{{ request()->routeIs('admin.komoditas.*') ? 'active' : '' }}">KOMODITAS</a>
            <a href="{{ route('admin.bangunan.index') }}" class="{{ request()->routeIs('admin.bangunan.*') ? 'active' : '' }}">BANGUNAN</a>
        </div>
        <div class="logout-link">
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <button type="submit" class="logout-button">LOGOUT</button>
            </form>
        </div>
    </aside>

    <header class="navbar">
        <div class="navbar-logo">
            <a href="{{ route('admin.dashboard') }}">
                <img src="{{ asset('images/logo.png') }}" alt="Logo Admin">
            </a>
        </div>

        <ul class="top-menu-links">
            <li><a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">DASHBOARD</a></li>
            <li><a href="{{ route('admin.rt-rw.index') }}" class="{{ request()->routeIs('admin.rt-rw.*') ? 'active' : '' }}">RT/RW</a></li>
            <li><a href="{{ route('admin.penduduk.index') }}" class="{{ request()->routeIs('admin.penduduk.*') ? 'active' : '' }}">PENDUDUK</a></li>
            <li><a href="{{ route('admin.komoditas.index') }}" class="{{ request()->routeIs('admin.komoditas.*') ? 'active' : '' }}">KOMODITAS</a></li>
            <li><a href="{{ route('admin.bangunan.index') }}" class="{{ request()->routeIs('admin.bangunan.*') ? 'active' : '' }}">BANGUNAN</a></li>
            <li>
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <button type="submit" class="logout-button">LOGOUT</button>
                </form>
            </li>
        </ul>

        <button class="navbar-toggler" id="navbar-toggler" aria-label="Toggle navigation">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </header>

    <main class="main-content">
        @yield('content')
    </main>

        @vite('resources/js/app.js')

        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const toggler = document.getElementById('navbar-toggler');
            const sideMenu = document.getElementById('side-menu');
            const overlay = document.getElementById('overlay');
            const closeBtn = document.getElementById('close-menu-btn');

            const toggleMenu = () => {
                sideMenu.classList.toggle('active');
                overlay.classList.toggle('active');
                toggler.classList.toggle('open');
            };
            
            // Periksa keberadaan elemen sebelum menambahkan event listener
            if (toggler) toggler.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);
            if (closeBtn) closeBtn.addEventListener('click', toggleMenu);

            // SKRIP INISIALISASI TOASTR
            // Pastikan jQuery sudah dimuat sebelum ini
            if (typeof $ !== 'undefined' && typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000" // Notifikasi hilang setelah 5 detik
                }
                
                // Cek Flash Message dan tampilkan Toastr
                @if (Session::has('success'))
                    toastr.success("{{ Session::get('success') }}");
                @endif
                @if (Session::has('error'))
                    toastr.error("{{ Session::get('error') }}");
                @endif
                @if (Session::has('warning'))
                    toastr.warning("{{ Session::get('warning') }}");
                @endif
                // Tampilkan error validasi Laravel pertama sebagai notifikasi Toastr
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        toastr.error("{{ $error }}");
                        @break 
                    @endforeach
                @endif
            } else {
                console.error("jQuery or Toastr not loaded correctly.");
            }
        });
    </script>

    @stack('scripts')
</body>
</html>