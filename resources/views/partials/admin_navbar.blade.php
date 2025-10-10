<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Responsif</title>
    <style>
        /* --- Reset & Gaya Dasar --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f4f7f6;
        }

        /* --- Lapisan Overlay (Latar Belakang Redup) --- */
        .overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 99;
            transition: opacity 0.3s ease;
        }

        .overlay.active {
            display: block;
        }

        /* --- Style Navbar Utama --- */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #ffffff;
            padding: 1rem 2rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            position: relative;
            z-index: 100;
        }

        .navbar-logo img {
            height: 40px; /* Atur tinggi logo */
            width: auto;
            display: block;
        }

        /* --- Link Navigasi (Tampilan Desktop) --- */
        .navbar-links {
            display: flex;
            list-style: none;
            gap: 2rem; /* Jarak antar menu */
        }

        .navbar-links a {
            color: #333;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .navbar-links a:hover {
            color: #007bff;
        }
        
        .navbar-links .logout-link {
            color: #dc3545; /* Warna khusus logout */
        }

        /* --- Tombol Hamburger --- */
        .navbar-toggler {
            display: none; /* Sembunyi di desktop */
            flex-direction: column;
            justify-content: space-around;
            width: 30px;
            height: 21px;
            background: transparent;
            border: none;
            cursor: pointer;
            z-index: 101;
        }

        .navbar-toggler .bar {
            width: 100%;
            height: 3px;
            background-color: #333;
            border-radius: 2px;
            transition: all 0.3s ease-in-out;
        }

        /* Animasi Hamburger menjadi 'X' */
        .navbar-toggler.open .bar:nth-child(1) {
            transform: translateY(9px) rotate(45deg);
        }
        .navbar-toggler.open .bar:nth-child(2) {
            opacity: 0;
        }
        .navbar-toggler.open .bar:nth-child(3) {
            transform: translateY(-9px) rotate(-45deg);
        }

        /* --- Logika Responsif --- */
        /* Aktif untuk layar dengan lebar 900px atau kurang */
        @media (max-width: 900px) {
            .navbar-toggler {
                display: flex; /* Tampilkan tombol hamburger */
            }

            .navbar-links {
                /* Ubah menjadi menu geser dari kanan */
                position: fixed;
                top: 0;
                right: -300px; /* Sembunyi di luar layar kanan */
                width: 300px;
                height: 100vh;
                background-color: #ffffff;
                box-shadow: -2px 0 5px rgba(0,0,0,0.1);
                
                /* Atur item menu menjadi vertikal */
                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                padding-top: 5rem;
                gap: 0;
                transition: right 0.3s ease-in-out;
            }

            /* Saat menu aktif (dikontrol JS) */
            .navbar-links.active {
                right: 0; /* Tampilkan menu dengan menggesernya ke dalam layar */
            }

            .navbar-links li {
                width: 100%;
            }

            .navbar-links a {
                display: block;
                padding: 1rem 2rem;
                width: 100%;
            }

            .navbar-links a:hover {
                background-color: #f1f1f1;
            }
        }

        /* Penyesuaian untuk layar yang sangat kecil */
        @media (max-width: 400px) {
            .navbar {
                padding: 1rem;
            }

            .navbar-links {
                width: 250px;
                right: -250px;
            }
        }
    </style>
</head>
<body>

    <div class="overlay" id="overlay"></div>

    <nav class="navbar">
        <div class="navbar-logo">
            <a href="#">
                {{-- Ganti '#' dengan route yang sesuai --}}
                {{-- Pastikan Anda memiliki gambar di public/images/logo.png --}}
                <img src="{{ asset('images/logo.png') }}" alt="Logo Admin">
            </a>
        </div>
        
        <ul class="navbar-links" id="navbar-links">
            <li><a href="{{ route('admin.dashboard') }}">DASHBOARD</a></li>
            <li><a href="{{ route('admin.perangkat.index') }}">RT/RW</a></li>
            <li><a href="{{ route('admin.penduduk.index') }}">PENDUDUK</a></li>
            <li><a href="{{ route('admin.komoditas.index') }}">KOMODITAS</a></li>
            <li><a href="{{ route('admin.bangunan.index') }}">BANGUNAN</a></li>
            <li><a href="{{ route('login') }}" class="logout-link">LOGOUT</a></li>
        </ul>
        
        <button class="navbar-toggler" id="navbar-toggler" aria-label="Toggle navigation">
            <span class="bar"></span>
            <span class="bar"></span>
            <span class="bar"></span>
        </button>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbarToggler = document.getElementById('navbar-toggler');
            const navbarLinks = document.getElementById('navbar-links');
            const overlay = document.getElementById('overlay');

            const toggleMenu = () => {
                // Toggle kelas 'open' untuk animasi tombol hamburger
                navbarToggler.classList.toggle('open');
                // Toggle kelas 'active' untuk menampilkan/menyembunyikan menu
                navbarLinks.classList.toggle('active');
                // Toggle kelas 'active' untuk menampilkan/menyembunyikan overlay
                overlay.classList.toggle('active');
            };

            // Tambahkan event listener untuk tombol hamburger
            navbarToggler.addEventListener('click', toggleMenu);

            // Tambahkan event listener untuk overlay (menutup menu saat diklik)
            overlay.addEventListener('click', toggleMenu);
        });
    </script>

</body>
</html>