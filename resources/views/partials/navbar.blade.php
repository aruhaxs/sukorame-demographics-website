<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Sukorame Fixed</title>
    <style>
        /* --- RESET DASAR --- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Arial, sans-serif; background-color: #f0f0f0; overflow-x: hidden; }

        /* --- LAYAR GELAP (OVERLAY) --- */
        .overlay {
            position: fixed; top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.6);
            z-index: 1000; 
            display: none;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .overlay.active { display: block; opacity: 1; }

        /* --- NAVBAR BACKGROUND (FULL WIDTH) --- */
        .navbar {
            background-color: #fff;
            height: 70px;
            width: 100%;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
            position: relative; 
            z-index: 1001;
            display: flex;
            justify-content: center; 
        }

        /* --- NAV CONTAINER --- */
        .nav-container {
            width: 100%;
            max-width: 1200px;
            padding: 0 1.5rem;
            margin: 0 auto;
            height: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        /* --- LOGO --- */
        .navbar-logo img {
            height: 45px; width: auto; display: block;
        }

        /* --- LINKS CONTAINER (DESKTOP) --- */
        .navbar-links {
            display: flex; list-style: none; gap: 2rem; align-items: center; height: 100%;
        }

        .navbar-links a {
            color: #041c36; text-decoration: none; font-weight: 700;
            font-size: 14px; text-transform: uppercase; letter-spacing: 0.5px;
            transition: color 0.3s ease; cursor: pointer;
        }

        .navbar-links a:hover { color: #f7a731; }

        /* --- DROPDOWN (DESKTOP) --- */
        .dropdown { position: relative; height: 100%; display: flex; align-items: center; }
        .dropdown .arrow-down { font-size: 10px; margin-left: 5px; }
        
        .dropdown-content {
            display: none; position: absolute;
            top: 70px; left: 0;
            background-color: #041c36; min-width: 180px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.3);
            border-top: 3px solid #f7a731; z-index: 100;
        }
        
        .dropdown-content a { color: white; padding: 12px 16px; display: block; font-weight: 500; }
        .dropdown-content a:hover { background-color: #062547; color: #f7a731; }
        .dropdown:hover .dropdown-content { display: block; }

        /* --- HAMBURGER MENU --- */
        .hamburger {
            display: none; cursor: pointer;
            z-index: 2000;
        }
        .bar {
            display: block; width: 25px; height: 3px; margin: 5px auto;
            transition: all 0.3s ease-in-out; background-color: #041c36;
        }

        /* --- MEDIA QUERY: MOBILE DRAWER (Max width 768px) --- */
        @media (max-width: 768px) {
            .hamburger { display: block; }

            .hamburger.active .bar:nth-child(2) { opacity: 0; }
            .hamburger.active .bar:nth-child(1) { transform: translateY(8px) rotate(45deg); }
            .hamburger.active .bar:nth-child(3) { transform: translateY(-8px) rotate(-45deg); }

            /* --- DRAWER STYLE --- */
            .navbar-links {
                position: fixed;
                top: 0;
                right: -100%;
                width: 260px;
                height: 100vh;
                background-color: #fff;
                flex-direction: column;
                align-items: flex-start;
                justify-content: flex-start;
                padding-top: 80px;
                transition: right 0.4s cubic-bezier(0.77, 0.2, 0.05, 1.0);
                z-index: 1002; 
                box-shadow: -5px 0 15px rgba(0,0,0,0.3);
            }

            .navbar-links.active { right: 0; }

            /* --- PERBAIKAN: GARIS PEMISAH ITEM MENU --- */
            .navbar-links li {
                width: 100%;
                /* Garis Bawah Halus */
                border-bottom: 1px solid rgba(0,0,0,0.1); 
            }
            
            /* Garis Atas hanya untuk item pertama agar rapi
            .navbar-links li:first-child {
                border-top: 1px solid rgba(0,0,0,0.1);
            } */

            .navbar-links a {
                padding: 15px 25px; display: block; font-size: 16px; width: 100%;
                color: #333; /* Warna teks gelap agar kontras di background putih */
            }
            
            /* Efek hover di menu mobile */
            .navbar-links a:hover {
                background-color: #f9f9f9;
                color: #041c36;
            }

            /* Dropdown Mobile */
            .dropdown { display: block; height: auto; width: 100%; }
            .dropdown-content {
                position: relative; top: 0; width: 100%;
                border-top: none; 
                background-color: #f4f4f4; /* Background abu muda untuk sub-menu */
                display: none;
                box-shadow: inset 0 2px 5px rgba(0,0,0,0.05); /* Bayangan ke dalam */
            }
            .dropdown:hover .dropdown-content { display: block; }
            .dropdown-content a { 
                padding-left: 40px; 
                font-size: 14px; 
                color: #555;
                border-bottom: 1px solid rgba(0,0,0,0.05); /* Garis tipis antar sub-menu */
            }
        }
    </style>
</head>
<body>

    <div class="overlay"></div>

    <nav class="navbar">
        <div class="nav-container">
            
            <div class="navbar-logo">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo Kelurahan">
                </a>
            </div>

            <div class="hamburger">
                <span class="bar"></span>
                <span class="bar"></span>
                <span class="bar"></span>
            </div>

            <ul class="navbar-links">
                <li><a href="{{ route('home') }}">BERANDA</a></li>
                
                <li class="dropdown">
                    <a href="#" class="dropbtn">
                        DEMOGRAFI <span class="arrow-down">▼</span>
                    </a>
                    <div class="dropdown-content">
                        <a href="{{ route('demografi.index') }}">PENDUDUK</a>
                        <a href="{{ route('peta.index') }}">WILAYAH</a>
                    </div>
                </li>

                <li><a href="#profile-section">PROFIL</a></li>
                <li><a href="{{ route('login') }}">LOGIN</a></li>
            </ul>

        </div> 
    </nav>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const hamburger = document.querySelector(".hamburger");
            const navMenu = document.querySelector(".navbar-links");
            const overlay = document.querySelector(".overlay");
            const body = document.body;

            function toggleMenu() {
                hamburger.classList.toggle("active");
                navMenu.classList.toggle("active");
                overlay.classList.toggle("active");
                
                if (navMenu.classList.contains("active")) {
                    body.style.overflow = "hidden";
                } else {
                    body.style.overflow = "auto";
                }
            }

            hamburger.addEventListener("click", toggleMenu);
            overlay.addEventListener("click", toggleMenu);

            document.querySelectorAll(".navbar-links a:not(.dropbtn)").forEach(link => {
                link.addEventListener("click", () => {
                    hamburger.classList.remove("active");
                    navMenu.classList.remove("active");
                    overlay.classList.remove("active");
                    body.style.overflow = "auto";
                });
            });
        });
    </script>

</body>
</html>