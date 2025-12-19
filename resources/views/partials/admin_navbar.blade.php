<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Responsif Biru</title>
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
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            position: relative;
            z-index: 100;
        }

        .navbar-logo img {
            height: 40px; 
            width: auto;
            display: block;
            /* Opsional: Filter agar logo terlihat jika logo asli berwarna gelap */
            /* filter: brightness(0) invert(1); */ 
        }

        /* --- Link Navigasi (Tampilan Desktop) --- */
        .navbar-links {
            display: flex;
            list-style: none;
            gap: 2rem; 
        }

        .navbar-links a {
            color: #ffffff; /* UBAH: Teks jadi Putih */
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
            font-size: 0.95rem;
            letter-spacing: 0.5px;
        }

        .navbar-links a:hover {
            color: #f7a731; /* UBAH: Hover jadi Oranye/Emas */
        }

        .navbar-links .logout-link {
            color: #ff6b6b; /* UBAH: Merah terang agar terbaca di background gelap */
        }
        
        .navbar-links .logout-link:hover {
            color: #ff4c4c;
        }

        /* --- Tombol Hamburger --- */
        .navbar-toggler {
            display: none; 
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
            background-color: #ffffff; /* UBAH: Garis hamburger jadi putih */
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
        @media (max-width: 900px) {
            .navbar-toggler {
                display: flex; 
            }

            .navbar-links {
                position: fixed;
                top: 0;
                right: -300px; 
                width: 300px;
                height: 100vh;
                background-color: #ffffff;
                box-shadow: -2px 0 5px rgba(0,0,0,0.2);

                flex-direction: column;
                justify-content: flex-start;
                align-items: flex-start;
                padding-top: 5rem;
                gap: 0;
                transition: right 0.3s ease-in-out;
                border-left: 1px solid rgba(255,255,255,0.1);
            }

            /* Saat menu aktif */
            .navbar-links.active {
                right: 0; 
            }

            .navbar-links li {
                width: 100%;
                border-bottom: 1px solid rgba(255,255,255,0.05); /* Garis pemisah tipis */
            }

            .navbar-links a {
                display: block;
                padding: 1rem 2rem;
                width: 100%;
            }

            .navbar-links a:hover {
                background-color: rgba(255,255,255,0.1); /* Efek hover background transparan */
                color: #f7a731;
            }
        }

        /* Penyesuaian untuk layar sangat kecil */
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
                <img src="{{ asset('images/logo.png') }}" alt="Logo Admin">
            </a>
        </div>

        <ul class="navbar-links" id="navbar-links">
            <li><a href="{{ route('admin.dashboard') }}">DASHBOARD</a></li>
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
                navbarToggler.classList.toggle('open');
                navbarLinks.classList.toggle('active');
                overlay.classList.toggle('active');
            };

            navbarToggler.addEventListener('click', toggleMenu);
            overlay.addEventListener('click', toggleMenu);
        });
    </script>

</body>
</html>