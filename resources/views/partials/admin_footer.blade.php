<style>
    /* == ADMIN FOOTER STYLES (Pine Green Theme) == */
    .admin-footer-wrapper {
        background-color: var(--color-bg-card); /* Menggunakan warna kartu admin */
        border-top: 1px solid var(--color-border);
        margin-top: auto; /* Mendorong footer ke bawah */
        padding-top: 3rem;
        color: var(--color-text-muted);
        font-size: 0.9rem;
    }

    .footer-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 0 2rem;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 2.5rem;
    }

    /* -- Kolom Identitas -- */
    .footer-brand {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .footer-logo {
        height: 50px;
        width: auto;
    }

    .brand-text h4 {
        margin: 0;
        color: var(--color-text-light);
        font-size: 1.1rem;
        font-weight: 700;
        letter-spacing: 0.5px;
    }

    .brand-text span {
        font-size: 0.8rem;
        color: var(--color-accent); /* Aksen Hijau Muda */
    }

    .footer-desc {
        line-height: 1.6;
        margin-bottom: 1.5rem;
        font-size: 0.85rem;
    }

    .social-links {
        display: flex;
        gap: 1rem;
    }

    .social-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        background-color: rgba(122, 186, 120, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-accent);
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }

    .social-btn:hover {
        background-color: var(--color-primary);
        color: #fff;
        transform: translateY(-3px);
    }

    /* -- Headings & Links -- */
    .footer-heading {
        color: var(--color-text-light);
        font-size: 1.1rem;
        margin-bottom: 1.2rem;
        font-weight: 600;
        position: relative;
        padding-bottom: 0.5rem;
    }

    .footer-heading::after {
        content: '';
        position: absolute;
        left: 0;
        bottom: 0;
        width: 40px;
        height: 3px;
        background-color: var(--color-primary);
        border-radius: 2px;
    }

    .footer-links-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .footer-links-list li {
        margin-bottom: 0.8rem;
    }

    .footer-links-list a {
        color: var(--color-text-muted);
        text-decoration: none;
        transition: color 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .footer-links-list a:hover {
        color: var(--color-accent);
        transform: translateX(5px);
    }

    /* -- Contact & Hours List -- */
    .info-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-list li {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        align-items: flex-start;
    }

    .info-icon {
        color: var(--color-accent);
        margin-top: 3px;
        flex-shrink: 0;
    }

    .hours-badge {
        background: rgba(10, 104, 71, 0.3);
        color: var(--color-text-light);
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.8rem;
        margin-left: auto;
    }

    .hours-badge.closed {
        background: rgba(220, 53, 69, 0.2);
        color: #fca5a5;
    }

    /* -- Copyright Bar -- */
    .footer-bottom {
        border-top: 1px solid var(--color-border);
        margin-top: 3rem;
        padding: 1.5rem 2rem;
        text-align: center;
        background-color: rgba(13, 27, 42, 0.5); /* Sedikit lebih gelap */
    }

    .footer-bottom p {
        margin: 0.2rem 0;
        font-size: 0.85rem;
    }

    .developed-by a {
        color: var(--color-accent);
        text-decoration: none;
        font-weight: 600;
    }

    .developed-by a:hover {
        text-decoration: underline;
    }
</style>

<footer class="admin-footer-wrapper">
    <div class="footer-container">
        
        {{-- Kolom 1: Identitas --}}
        <div class="footer-col">
            <div class="footer-brand">
                {{-- Gunakan Logo Anda atau Placeholder --}}
                <img src="{{ asset('images/logo2.png') }}" alt="Logo" class="footer-logo" onerror="this.style.display='none'">
                <div class="brand-text">
                    <h4>KELURAHAN SUKORAME</h4>
                    <span>Admin Dashboard System</span>
                </div>
            </div>
            <p class="footer-desc">
                Sistem Informasi Manajemen Data Kelurahan Sukorame. Mengelola data kependudukan, bangunan, dan potensi wilayah secara terpadu.
            </p>
            <div class="social-links">
                <a href="#" class="social-btn" aria-label="Facebook">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
                <a href="https://www.instagram.com/kelurahansukorame_kota_kediri/" target="_blank" class="social-btn" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
                <a href="#" class="social-btn" aria-label="YouTube">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.33 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>
                </a>
            </div>
        </div>

        {{-- Kolom 2: Navigasi Admin --}}
        <div class="footer-col">
            <h5 class="footer-heading">Navigasi Admin</h5>
            <ul class="footer-links-list">
                <li><a href="{{ route('admin.dashboard') }}">Dashboard Utama</a></li>
                <li><a href="#">Manajemen Data Penduduk</a></li>
                <li><a href="#">Data Bangunan & Wilayah</a></li>
                <li><a href="{{ url('/') }}" target="_blank">Lihat Website Utama <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg></a></li>
            </ul>
        </div>

        {{-- Kolom 3: Info Kantor --}}
        <div class="footer-col">
            <h5 class="footer-heading">Kantor Kelurahan</h5>
            <ul class="info-list">
                <li>
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    </div>
                    <span>Jl. Veteran IV No.25, Sukorame, Kec. Mojoroto, Kota Kediri, Jawa Timur 64114</span>
                </li>
                <li>
                    <div class="info-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    </div>
                    <span>(0354) 77xxxx</span>
                </li>
            </ul>
        </div>

        {{-- Kolom 4: Jam Operasional --}}
        <div class="footer-col">
            <h5 class="footer-heading">Jam Pelayanan</h5>
            <ul class="info-list" style="font-size: 0.9rem;">
                <li style="display: block; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <div style="display:flex; justify-content: space-between;">
                        <span>Senin - Kamis</span>
                        <span class="hours-badge">07.30 - 15.30</span>
                    </div>
                </li>
                <li style="display: block; border-bottom: 1px solid rgba(255,255,255,0.05); padding-bottom: 8px;">
                    <div style="display:flex; justify-content: space-between;">
                        <span>Jumat</span>
                        <span class="hours-badge">07.30 - 14.30</span>
                    </div>
                </li>
                <li style="display: block;">
                    <div style="display:flex; justify-content: space-between;">
                        <span>Sabtu - Minggu</span>
                        <span class="hours-badge closed">Tutup</span>
                    </div>
                </li>
            </ul>
        </div>

    </div>

    {{-- Copyright Bar --}}
    <div class="footer-bottom">
        <p>&copy; {{ date('Y') }} <strong>Pemerintah Kelurahan Sukorame</strong>. All Rights Reserved.</p>
        <p class="developed-by" style="color: var(--color-text-muted);">
            Sistem Administrasi Terpadu | Versi 1.0
        </p>
    </div>
</footer>