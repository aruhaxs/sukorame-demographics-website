<nav class="navbar">
    <div class="navbar-logo">
        <a href="{{ route('admin.dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="Logo Admin">
        </a>
    </div>
    <ul class="navbar-links">
        <li><a href="{{ route('admin.dashboard') }}">DASHBOARD</a></li>
        <li><a href="{{ route('admin.perangkat.index') }}">RT/RW</a></li>
        <li><a href="{{ route('admin.penduduk.index') }}">PENDUDUK</a></li>
        <li><a href="{{ route('admin.komoditas.index') }}">KOMODITAS</a></li>
        <li><a href="{{ route('admin.bangunan.index') }}">BANGUNAN</a></li>
        <li><a href="{{ route('login') }}" style="color: #F88">LOGOUT</a></li>
    </ul>
</nav>
