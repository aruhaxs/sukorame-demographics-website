@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<style>
    .hero-section {
        position: relative;
        z-index: 1;
        height: 60vh;
        min-height: 400px;
        background: url('{{ asset("images/klotok.webp") }}') center/cover no-repeat fixed;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: white;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 1;
        background: linear-gradient(to bottom, rgba(0, 31, 63, 0.7), rgba(0, 31, 63, 0.9));
    }

    .hero-content {
        position: relative;
        z-index: 2;
        padding: 20px;
    }
    
    /* --- DATA SECTION --- */
    .data-section {
        background-color: #001f3f;
        padding: 4rem 1rem;
        position: relative;
        z-index: 1;
    }

    .section-header {
        text-align: center;
        margin-bottom: 3rem;
    }
    .section-title {
        font-size: 2rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 10px;
        position: relative;
        display: inline-block;
    }
    .section-title::after {
        content: '';
        display: block;
        width: 60px;
        height: 4px;
        background: #f7a731;
        margin: 10px auto 0;
        border-radius: 2px;
    }

    /* Grid Layout */
    .dashboard-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 2rem;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* Card Styling */
    .stat-card {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 2rem;
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

    /* Tipe Card: Angka Besar */
    .stat-card.primary {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        min-height: 250px;
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.02) 100%);
    }
    .stat-icon {
        font-size: 3rem;
        color: #f7a731;
        margin-bottom: 1rem;
    }
    .stat-value {
        font-size: 4rem;
        font-weight: 800;
        color: #ffffff;
        line-height: 1;
        margin-bottom: 0.5rem;
    }
    .stat-label {
        font-size: 1.1rem;
        color: #a0aec0;
        font-weight: 500;
    }

    /* Tipe Card: Grafik */
    .stat-card.chart-wrapper {
        min-height: 300px;
        display: flex;
        flex-direction: column;
    }
    .chart-container {
        position: relative;
        height: 220px;
        width: 100%;
        margin-top: auto;
        margin-bottom: auto;
    }
    .card-title {
        color: #fff;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        padding-bottom: 0.5rem;
    }

    /* --- INFO & PROFILE SECTIONS --- */
    .info-section, .profile-section, .location-section {
        padding: 4rem 1rem;
        background: #fff;
    }
    .profile-section {
        background: #f8f9fa;
    }
    .map-container iframe {
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
</style>

{{-- HERO --}}
<header class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">KELURAHAN SUKORAME</h1>
        <p class="hero-subtitle">Official Website & Layanan Digital</p>
    </div>
</header>

{{-- DATA DASHBOARD --}}
<section class="data-section">
    <div class="section-header">
        <h2 class="section-title">STATISTIK KEPENDUDUKAN</h2>
        <p style="color: #a0aec0; margin-top: 10px;">Data terkini demografi warga Kelurahan Sukorame</p>
    </div>

    <div class="dashboard-grid">
        {{-- Card Total Penduduk --}}
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-value">{{ $totalPenduduk }}</div>
            <div class="stat-label">Total Penduduk</div>
        </div>

        {{-- Card Chart Gender --}}
        <div class="stat-card chart-wrapper">
            <h3 class="card-title">Jenis Kelamin</h3>
            <div class="chart-container">
                <canvas id="genderChart"></canvas>
            </div>
        </div>

        {{-- Card Kepala Keluarga --}}
        <div class="stat-card primary">
            <div class="stat-icon"><i class="bi bi-house-door-fill"></i></div>
            <div class="stat-value">{{ $totalKK }}</div>
            <div class="stat-label">Kepala Keluarga</div>
        </div>
        
        {{-- 
            DIKOMENTARI SEMENTARA (HIDDEN)
            Bagian Chart Kelompok Usia
        --}}
        {{-- 
        <div class="stat-card chart-wrapper">
            <h3 class="card-title">Kelompok Usia</h3>
            <div class="chart-container">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
        --}}
    </div>
</section>

{{-- BERITA / INFO --}}
<section class="info-section">
    <div class="container" style="max-width: 1000px; margin: 0 auto; display: flex; gap: 2rem; align-items: center; flex-wrap: wrap;">
        <div class="info-text" style="flex: 1; min-width: 300px;">
            <h2 style="font-size: 2rem; font-weight: 700; color: #001f3f; margin-bottom: 1rem;">Program Unggulan</h2>
            <h3 style="font-size: 1.2rem; color: #f7a731; margin-bottom: 1rem;">Pemberdayaan Masyarakat</h3>
            <p style="color: #555; line-height: 1.6; margin-bottom: 1.5rem;">
                Kelurahan Sukorame aktif mengadakan berbagai program untuk meningkatkan keterampilan dan kesejahteraan warga, mulai dari pelatihan UMKM hingga kegiatan sosial kemasyarakatan.
            </p>
            <a href="https://www.instagram.com/prokopimkotakediri?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" style="display: inline-block; padding: 10px 20px; background: #001f3f; color: white; text-decoration: none; border-radius: 5px; font-weight: 600;">Baca Selengkapnya</a>
        </div>
        <div class="info-image" style="flex: 1; min-width: 300px;">
            <img src="{{ asset('images/umkm.webp') }}" alt="Informasi" style="width: 100%; border-radius: 12px; box-shadow: 0 10px 20px rgba(0,0,0,0.1);">
        </div>
    </div>
</section>

{{-- PROFIL LURAH --}}
<section class="profile-section">
    <div class="container" style="max-width: 900px; margin: 0 auto; text-align: center;">
        <h2 style="font-size: 2rem; font-weight: 700; color: #001f3f; margin-bottom: 2rem;">PIMPINAN KAMI</h2>
        <div style="background: white; padding: 2rem; border-radius: 16px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); display: flex; align-items: center; gap: 2rem; flex-wrap: wrap; text-align: left;">
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
    <div class="section-header">
        <h2 class="section-title" style="color: #001f3f;">LOKASI KANTOR</h2>
    </div>
    <div class="map-container" style="max-width: 1200px; margin: 0 auto;">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.088333276634!2d112.01639897588352!3d-7.780447377196231!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e785731057e6211%3A0xc47414732168395e!2sKantor%20Kelurahan%20Sukorame!5e0!3m2!1sid!2sid!4v1716300000000!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari Controller
        const jumlahLakiLaki = {{ $jumlahLakiLaki ?? 0 }};
        const jumlahPerempuan = {{ $jumlahPerempuan ?? 0 }};
        const usiaData = @json($usiaData ?? ['labels' => [], 'data' => []]);

        // Pengaturan Font Global Chart agar terlihat di background gelap
        Chart.defaults.color = '#e0e0e0';
        Chart.defaults.font.family = "'Poppins', sans-serif";

        // 1. Chart Gender (Doughnut)
        const ctxGender = document.getElementById('genderChart');
        if (ctxGender) {
            new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        data: [jumlahLakiLaki, jumlahPerempuan],
                        backgroundColor: ['#4b6cb7', '#f7a731'], // Biru & Oranye
                        borderColor: 'transparent',
                        hoverOffset: 10
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: { color: '#fff', boxWidth: 12 }
                        }
                    },
                    cutout: '70%',
                }
            });
        }

        /*
        // 2. Chart Usia (Bar) - SEMENTARA DINONAKTIFKAN
        const ctxAge = document.getElementById('ageChart');
        if (ctxAge) {
            new Chart(ctxAge, {
                type: 'bar',
                data: {
                    labels: usiaData.labels,
                    datasets: [{
                        label: 'Jiwa',
                        data: usiaData.data,
                        backgroundColor: '#f7a731',
                        borderRadius: 4,
                        barThickness: 20
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal Bar
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            grid: { color: 'rgba(255,255,255,0.1)' },
                            ticks: { color: '#a0aec0' }
                        },
                        y: {
                            grid: { display: false },
                            ticks: { color: '#fff' }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
        */
    });
</script>
@endpush