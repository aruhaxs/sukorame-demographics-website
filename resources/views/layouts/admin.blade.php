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
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
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
            /* UBAH DISINI: Warna Biru Dongker Gelap */
            background-color: #031529; 
            padding: 0 3rem;
            height: 70px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .navbar-logo img {
            height: 45px;
            width: auto;
            display: block;
        }

        /* --- Menu Horizontal (Desktop) --- */
        .top-menu-links {
            display: flex;
            list-style: none;
            gap: 2rem;
            align-items: center;
            height: 100%;
            margin: 0;
        }
        
        /* Gaya Link Menu Desktop */
        .top-menu-links a {
            text-decoration: none;
            /* UBAH DISINI: Teks Putih, Tebal, Kapital */
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            transition: color 0.2s;
            padding: 5px 0;
        }
        
        .top-menu-links a:hover,
        .top-menu-links a.active {
            color: #f7a731; /* Warna emas saat di-hover */
        }

        /* --- Gaya Tombol Logout (Reset agar seperti link biasa) --- */
        .logout-form {
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .top-menu-links .logout-button {
            background: transparent; /* Hapus background merah */
            border: none;
            font-family: inherit;
            /* Samakan font dengan link lain */
            color: #ffffff;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
            cursor: pointer;
            padding: 0;
        }

        .top-menu-links .logout-button:hover {
            color: #dc3545; /* Merah hanya saat disentuh kursor */
        }

        /* --- Tombol Hamburger (Mobile) --- */
        .navbar-toggler {
            display: none;
            width: 40px;
            height: 40px;
            padding: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 8px;
            background-color: transparent;
            cursor: pointer;
            z-index: 101;
        }
        .navbar-toggler .bar {
            width: 100%;
            height: 3px;
            margin: 4px 0;
            background-color: #ffffff; /* Garis Putih */
            border-radius: 2px;
            transition: transform 0.3s ease-in-out;
        }
        .navbar-toggler.open .bar:nth-child(1) { transform: translateY(7px) rotate(45deg); }
        .navbar-toggler.open .bar:nth-child(2) { opacity: 0; }
        .navbar-toggler.open .bar:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

        /* --- Menu Samping (Mobile) --- */
        .side-menu {
            height: 100%;
            width: 280px;
            position: fixed;
            z-index: 102;
            top: 0;
            right: -280px;
            /* Samakan background dengan navbar utama */
            background-color: #031529;
            padding-top: 20px;
            transition: right 0.3s ease;
            box-shadow: -2px 0 5px rgba(0,0,0,0.5);
        }
        .side-menu.active { right: 0; }

        .close-menu-btn {
            position: absolute; top: 10px; right: 15px; background: none; border: none;
            color: #ffffff; font-size: 2.5rem; font-weight: 300; cursor: pointer;
        }
        
        .side-menu-links a {
            padding: 15px 25px; text-decoration: none; font-size: 1rem;
            color: #E9ECEF; display: block; border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .side-menu-links a:hover,
        .side-menu-links a.active {
            background-color: rgba(255, 255, 255, 0.1);
            color: #f7a731;
        }
        
        .side-menu .logout-button {
            background: none; border: none; padding: 15px 25px; width: 100%;
            text-align: left; cursor: pointer; font-size: 1rem;
            font-family: inherit; color: #E9ECEF; font-weight: bold;
        }
        .side-menu .logout-link {
            position: absolute; bottom: 0; width: 100%; border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        /* Konten Utama */
        .main-content { padding: 2rem; margin-top: 20px; }

        /* --- ATURAN RESPONSIVE --- */
        @media (max-width: 991.98px) {
            .top-menu-links { display: none; }
            .navbar-toggler { display: block; }
            .main-content { padding: 1rem; }
            .navbar { padding: 0 1rem; height: 60px; }
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
            {{-- Gunakan route('logout') jika tersedia, jika tidak gunakan yang ada --}}
            <form action="{{ route('logout') }}" method="POST">
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
            <li><a href="{{ route('admin.bangunan.index') }}" class="{{ request()->routeIs('admin.bangunan.*') ? 'active' : '' }}">BANGUNAN</a></li>
            
            <li class="logout-item">
                <form action="{{ route('logout') }}" method="POST" class="logout-form">
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
            
            if (toggler) toggler.addEventListener('click', toggleMenu);
            if (overlay) overlay.addEventListener('click', toggleMenu);
            if (closeBtn) closeBtn.addEventListener('click', toggleMenu);

            // SKRIP TOASTR
            if (typeof $ !== 'undefined' && typeof toastr !== 'undefined') {
                toastr.options = {
                    "closeButton": true,
                    "progressBar": true,
                    "positionClass": "toast-top-right",
                    "timeOut": "5000"
                }
                
                @if (Session::has('success'))
                    toastr.success("{{ Session::get('success') }}");
                @endif
                @if (Session::has('error'))
                    toastr.error("{{ Session::get('error') }}");
                @endif
                @if (Session::has('warning'))
                    toastr.warning("{{ Session::get('warning') }}");
                @endif
                @if ($errors->any())
                    @foreach ($errors->all() as $error)
                        toastr.error("{{ $error }}");
                        @break 
                    @endforeach
                @endif
            }
        });
    </script>
    @stack('scripts')
</body>
</html>