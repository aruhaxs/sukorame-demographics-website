<footer class="main-footer">
    <div class="footer-container">
        
        {{-- Kolom 1: Identitas --}}
        <div class="footer-col">
            <div class="footer-brand">
                <img src="{{ asset('images/logo2.png') }}" alt="Logo Kota Kediri" class="footer-logo">
                <div class="brand-text">
                    <h4>KELURAHAN SUKORAME</h4>
                    <span>Kecamatan Mojoroto, Kota Kediri</span>
                </div>
            </div>
            <p class="footer-desc">
                Website resmi Pemerintah Kelurahan Sukorame. Memberikan pelayanan publik yang transparan, akuntabel, dan prima menuju Sukorame yang lebih maju.
            </p>
            <div class="social-links">
                <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                <a href="https://www.instagram.com/kelurahansukorame_kota_kediri/" target="_blank" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" aria-label="YouTube"><i class="bi bi-youtube"></i></a>
            </div>
        </div>

        {{-- Kolom 2: Tautan Cepat --}}
        <div class="footer-col">
            <h5 class="footer-heading">Jelajahi</h5>
            <ul class="footer-links">
                <li><a href="{{ url('/') }}">Beranda</a></li>
                <li><a href="#">Profil Wilayah</a></li>
                <li><a href="#">Data Penduduk</a></li>
                <li><a href="#">Layanan Surat</a></li>
                <li><a href="#">Berita & Kegiatan</a></li>
            </ul>
        </div>

        {{-- Kolom 3: Kontak --}}
        <div class="footer-col">
            <h5 class="footer-heading">Hubungi Kami</h5>
            <ul class="contact-list">
                <li>
                    <i class="bi bi-geo-alt-fill"></i>
                    <span>Jl. Veteran IV No.25, Sukorame, Kec. Mojoroto, Kota Kediri, Jawa Timur 64114</span>
                </li>
                <li>
                    <i class="bi bi-telephone-fill"></i>
                    <span>(0354) 77xxxx</span>
                </li>
                <li>
                    <i class="bi bi-envelope-fill"></i>
                    <span>kelurahansukorame@kedirikota.go.id</span>
                </li>
            </ul>
        </div>

        {{-- Kolom 4: Jam Pelayanan --}}
        <div class="footer-col">
            <h5 class="footer-heading">Jam Pelayanan</h5>
            <ul class="service-hours">
                <li>
                    <span>Senin - Kamis</span>
                    <span class="hours">07.30 - 15.30 WIB</span>
                </li>
                <li>
                    <span>Jumat</span>
                    <span class="hours">07.30 - 14.30 WIB</span>
                </li>
                <li>
                    <span>Sabtu - Minggu</span>
                    <span class="hours closed">Tutup</span>
                </li>
            </ul>
        </div>

    </div>

    {{-- Copyright Bar --}}
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} Pemerintah Kelurahan Sukorame. All Rights Reserved.</p>
        <p class="developed-by">Supported by <a href="https://kedirikota.go.id" target="_blank">Pemkot Kediri</a></p>
    </div>
</footer>