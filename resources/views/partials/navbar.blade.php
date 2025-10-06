<nav class="navbar">
    <div class="navbar-logo">
        <a href="{{ route('home') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Kelurahan">
        </a>
    </div>
    <ul class="navbar-links">
        <li><a href="{{ route('home') }}">BERANDA</a></li>
        <li class="dropdown">
            <a href="{{ route('demografi.index') }}" class="dropbtn">DEMOGRAFI <span class="arrow-down">▼</span></a>
            <div class="dropdown-content">
                <a href="{{ route('demografi.index') }}">PENDUDUK</a>
                <a href="#">WILAYAH</a>
                <a href="#">KOMODITAS</a>
            </div>
        </li>
        <li><a href="#profile-section">PROFIL</a></li>
        <li><a href="{{ route('login') }}">LOGIN</a></li>
    </ul>
</nav>
