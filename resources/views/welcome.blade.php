@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

<style>
    .chart-container {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .data-card.chart-card {
        padding: 1.5rem;
    }
</style>

<header class="hero-section">
    <div class="overlay"></div>
    <div class="header-content">
        <h1 class="title">KELURAHAN SUKORAME</h1>
        <p class="subtitle">Official Website</p>
    </div>
</header>

<section class="data-section">
    <h2 class="section-title">DATA PENDUDUK</h2>
    <div class="data-carousel-wrapper">
        <button class="carousel-nav prev">←</button>

        <div class="data-cards-container">
            <div class="data-card large">
                <div class="card-content">
                    <p class="data-label">Jumlah Penduduk</p>
                    <p class="data-value">{{ $totalPenduduk }}</p>
                </div>
                <div class="card-image-placeholder"></div>
            </div>

            <div class="data-card small chart-card">
                <div class="chart-container">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>

            <div class="data-card large">
                <div class="card-content">
                    <p class="data-label">Total Kartu Keluarga (KK)</p>
                    <p class="data-value">{{ $totalKK }}</p>
                </div>
                <div class="card-image-placeholder"></div>
            </div>
            
            <div class="data-card small chart-card">
                 <div class="chart-container">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>

        <button class="carousel-nav next">→</button>
    </div>
</section>

<section class="info-section">
    <h2 class="section-title">BERITA dan INFORMASI</h2>
    <div class="info-card-container">
        <div class="info-card">
            <div class="info-text">
                <h3 class="info-title">Program Pemberdayaan Masyarakat</h3>
                <p class="info-description">
                    Kelurahan Sukorame aktif mengadakan berbagai program untuk meningkatkan keterampilan dan kesejahteraan warga.
                </p>
                <a href="#" class="info-button">Selengkapnya</a>
            </div>
            <div class="info-image">
                <img src="{{ asset('images/berita.png') }}" alt="Informasi">
            </div>
        </div>
        <div class="info-navigation">
            <span class="nav-arrow left">←</span>
            <span class="nav-arrow right">→</span>
        </div>
    </div>
</section>

<section class="profile-section">
    <div class="profile-content">
        <div class="profile-image-container">
            <img src="{{ asset('images/lurah.jpeg') }}" alt="Vita Sari" class="profile-photo">
        </div>
        <div class="profile-text">
            <h2 class="profile-name">Vita Sari, SE. MM.</h2>
            <p class="profile-description">
                Vita Sari, S.E., M.M., memegang jabatan sebagai Lurah Kelurahan Sukorame, yang menempatkannya sebagai pimpinan eksekutif tertinggi yang bertanggung jawab atas seluruh aspek pemerintahan, pembangunan, dan kemasyarakatan di wilayahnya.
            </p>
        </div>
    </div>
</section>

<section class="location-section">
    <h2 class="section-title">LOKASI</h2>
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d15810.19831623955!2d112.00898863955077!3d-7.836906900000002!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e78570957c5a4ad%3A0x2358055361304132!2sSukorame%2C%20Kec.%20Mojoroto%2C%20Kota%20Kediri%2C%20Jawa%20Timur!5e0!3m2!1sid!2sid!4v1734327774213!5m2!1sid!2sid" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const jumlahLakiLaki = {{ $jumlahLakiLaki }};
        const jumlahPerempuan = {{ $jumlahPerempuan }};
        const usiaData = @json($usiaData);

        const ctxGender = document.getElementById('genderChart');
        if (ctxGender) {
            new Chart(ctxGender, {
                type: 'doughnut',
                data: {
                    labels: ['Laki-laki', 'Perempuan'],
                    datasets: [{
                        label: 'Jumlah',
                        data: [jumlahLakiLaki, jumlahPerempuan],
                        backgroundColor: ['#4b6cb7', '#f7a731'],
                        borderColor: '#ffffff',
                        borderWidth: 2,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                color: '#333',
                                font: { size: 14 }
                            }
                        },
                        title: {
                            display: true,
                            text: 'Komposisi Jenis Kelamin',
                            color: '#1E5631',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
        }

        const ctxAge = document.getElementById('ageChart');
        if (ctxAge) {
            new Chart(ctxAge, {
                type: 'bar',
                data: {
                    labels: usiaData.labels,
                    datasets: [{
                        label: 'Jumlah Penduduk',
                        data: usiaData.data,
                        backgroundColor: [
                            '#f7a731', // Oranye
                            '#8c81ff', // Ungu
                            '#4b6cb7', // Biru
                            '#55A08F', // Hijau Tosca
                            '#dc3545'  // Merah
                        ],
                        borderRadius: 5
                    }]
                },
                options: {
                    indexAxis: 'y',
                    responsive: true,
                    scales: {
                        x: {
                            ticks: { color: '#555' },
                            grid: { display: true, color: '#eee' }
                        },
                        y: {
                            ticks: { color: '#333' },
                            grid: { display: false }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        title: {
                            display: true,
                            text: 'Komposisi Kelompok Usia',
                            color: '#1E5631',
                            font: { size: 16, weight: 'bold' }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush