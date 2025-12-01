@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<style>
    /* --- UTILITY: CONTAINER PENYAMA LEBAR (SAMA DENGAN NAVBAR & FOOTER) --- */
    .content-container {
        width: 100%;
        max-width: 1200px; /* Lebar maksimum sama dengan Navbar */
        margin: 0 auto;    /* Posisi Tengah */
        padding: 0 1.5rem; /* Padding Kiri-Kanan sama dengan Navbar */
        position: relative; 
        z-index: 2;
    }

    /* --- HERO SECTION --- */
    .hero-section {
        position: relative; z-index: 1; height: 60vh; min-height: 400px;
        background: url('{{ asset("images/klotok.webp") }}') center/cover no-repeat fixed;
        display: flex; align-items: center; justify-content: center; text-align: center; color: white;
    }
    .hero-section::before {
        content: ''; position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 1;
        background: linear-gradient(to bottom, rgba(0, 31, 63, 0.7), rgba(0, 31, 63, 0.9));
    }
    .hero-content { 
        position: relative; z-index: 2; padding: 20px 0; 
    }
    .hero-title { font-size: 3rem; font-weight: 800; text-shadow: 0 2px 10px rgba(0,0,0,0.3); margin-bottom: 0.5rem; }
    .hero-subtitle { font-size: 1.2rem; font-weight: 300; letter-spacing: 1px; opacity: 0.9; }
    
    /* --- DATA SECTION --- */
    .data-section {
        background-color: #001f3f;
        padding: 3rem 0; /* Padding vertical saja, horizontal diatur container */
        position: relative;
        z-index: 1;
    }

    .section-header { text-align: center; margin-bottom: 2rem; }
    .section-title {
        font-size: 2rem; font-weight: 700; color: #ffffff; margin-bottom: 10px;
        position: relative; display: inline-block;
        text-transform: uppercase;
    }
    .section-title::after {
        content: ''; display: block; width: 60px; height: 4px; background: #f7a731;
        margin: 10px auto 0; border-radius: 2px;
    }

    /* Grid Layout - 1 Baris (5 Kolom) */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr); 
        gap: 1rem;
        width: 100%;
    }

    /* Card Styling - Compact */
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 1.5rem 0.5rem;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        position: relative;
        overflow: hidden;
        backdrop-filter: blur(5px);
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.3);
        background: rgba(255, 255, 255, 0.1);
    }

    .stat-card.primary {
        display: flex; flex-direction: column; justify-content: center; align-items: center;
        text-align: center;
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
    }

    .stat-icon { font-size: 2rem; color: #f7a731; margin-bottom: 0.5rem; }
    .stat-value { font-size: 2.5rem; font-weight: 800; color: #ffffff; line-height: 1; margin-bottom: 0.25rem; }
    .stat-label { font-size: 0.9rem; color: #a0aec0; font-weight: 500; white-space: nowrap; }

    /* RESPONSIVE */
    @media (max-width: 992px) {
        .dashboard-grid { grid-template-columns: repeat(3, 1fr); }
    }
    @media (max-width: 576px) {
        .dashboard-grid { grid-template-columns: repeat(2, 1fr); }
        .stat-value { font-size: 2rem; }
    }

    /* --- INFO, PROFILE, LOCATION --- */
    .info-section, .profile-section, .location-section { 
        padding: 4rem 0; /* Padding vertical saja */
        background: #fff; 
    }
    .profile-section { background: #f8f9fa; }
    
    .map-container iframe { border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
</style>

{{-- HERO --}}
<header class="hero-section">
    <div class="content-container">
        <div class="hero-content">
            <h1 class="hero-title">KELURAHAN SUKORAME</h1>
            <p class="hero-subtitle">Official Website & Layanan Digital</p>
        </div>
    </div>
</header>

{{-- DATA DASHBOARD --}}
<section class="data-section">
    <div class="content-container">
        
        <div class="section-header">
            <h2 class="section-title">INFORMASI & DATA TERPADU</h2>
            <p style="color: #a0aec0; margin-top: 10px;">Ringkasan data demografi & infrastruktur terkini</p>
        </div>

        <div class="dashboard-grid">
            
            {{-- Card 1: Kepala Keluarga --}}
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-house-door-fill"></i></div>
                <div class="stat-value">{{ $totalKK }}</div>
                <div class="stat-label">Kepala Keluarga</div>
            </div>

            {{-- Card 2: Total Penduduk --}}
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-value">{{ $totalPenduduk }}</div>
                <div class="stat-label">Total Penduduk</div>
            </div>

            {{-- Card 3: Laki-laki --}}
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-gender-male"></i></div>
                <div class="stat-value">{{ $totalLaki ?? 0 }}</div>
                <div class="stat-label">Laki-laki</div>
            </div>

            {{-- Card 4: Perempuan --}}
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-gender-female"></i></div>
                <div class="stat-value">{{ $totalPerempuan ?? 0 }}</div>
                <div class="stat-label">Perempuan</div>
            </div>

            {{-- Card 5: Total Bangunan --}}
            <div class="stat-card primary">
                <div class="stat-icon"><i class="bi bi-building"></i></div>
                <div class="stat-value">{{ $totalBangunan ?? 0 }}</div>
                <div class="stat-label">Total Bangunan</div>
            </div>
            
        </div>
    </div>
</section>

{{-- BERITA / INFO --}}
<section class="info-section">
    <div class="content-container">
        
        <div style="display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
            <div class="info-text" style="flex: 1; min-width: 300px;">
                <h2 style="font-size: 2rem; font-weight: 700; color: #001f3f; margin-bottom: 1rem;">Program Unggulan</h2>
                <h3 style="font-size: 1.2rem; color: #f7a731; margin-bottom: 1rem;">Pemberdayaan Masyarakat</h3>
                <p style="color: #555; line-height: 1.6; margin-bottom: 1.5rem;">
                    Kelurahan Sukorame aktif mengadakan berbagai program untuk meningkatkan keterampilan dan kesejahteraan warga.
                </p>
                <a href="https://www.instagram.com/prokopimkotakediri?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" style="display: inline-block; padding: 10px 20px; background: #001f3f; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">Baca Selengkapnya</a>
            </div>
            <div class="info-image" style="flex: 1; min-width: 300px;">
                <img src="{{ asset('images/umkm.webp') }}" alt="Informasi" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
            </div>
        </div>

    </div>
</section>

{{-- PROFIL LURAH --}}
<section class="profile-section">
    <div class="content-container">
        
        <div style="text-align: center; margin-bottom: 2rem;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #001f3f;">PIMPINAN KAMI</h2>
        </div>
        
        <div style="max-width: 900px; margin: 0 auto; background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; text-align: left;">
            <img src="{{ asset('images/lurah.jpeg') }}" alt="Vita Sari" style="width: 150px; height: 150px; object-fit: cover; border-radius: 50%; border: 4px solid #f7a731;">
            <div style="flex: 1;">
                <h3 style="font-size: 1.5rem; font-weight: 700; color: #001f3f; margin-bottom: 0.5rem;">Vita Sari, SE. MM.</h3>
                <p style="color: #888; margin-bottom: 1rem; font-weight: 500;">Lurah Kelurahan Sukorame</p>
                <p style="color: #555; line-height: 1.6;">
                    "Berkomitmen untuk memberikan pelayanan publik yang transparan, akuntabel, dan mengutamakan kesejahteraan seluruh warga Sukorame."
                </p>
            </div>
        </div>

    </div>
</section>

{{-- LOKASI --}}
<section class="location-section">
    <div class="content-container">
        
        <div class="section-header">
            <h2 class="section-title" style="color: #001f3f;">LOKASI KANTOR</h2>
        </div>
        <div class="map-container">
            {{-- Gunakan link embed khusus ini --}}
            <iframe 
                src="https://maps.google.com/maps?q=Kantor+Kelurahan+Sukorame+Kediri&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                width="100%" 
                height="450" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>

    </div>
</section>

@endsection

@push('scripts')
{{-- Scripts tambahan jika diperlukan --}}
@endpush