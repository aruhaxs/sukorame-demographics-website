@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    /* ... (semua style yang sudah ada tetap di sini) ... */
    .page-container { padding: 2rem; max-width: 1400px; margin: 0 auto; }
    .page-title { font-size: 2rem; margin-bottom: 1rem; font-weight: 600; }
    .summary-card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 1.5rem; }
    .summary-card { background-color: #1c3d64; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); color: #f0f0f0; transition: transform 0.2s ease-in-out; }
    .summary-card:hover { transform: translateY(-5px); }
    .summary-card p { margin: 0; font-size: 1rem; }
    .summary-card .value { font-size: 2.5rem; font-weight: 700; color: #4BC0C0; margin-top: 8px; }
    .alert-success { background-color: #4BC0C0; color: #1c3d64; padding: 15px; border-radius: 5px; margin-bottom: 1.5rem; font-weight: 500; }
    
    /* --- STYLE BARU UNTUK GRAFIK --- */
    .chart-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-top: 2.5rem;
    }
    .chart-container {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);

    }
    .chart-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #1c3d64;
        margin-bottom: 1rem;
        text-align: center;
    }

    @media (max-width: 768px) {
        .page-container { padding: 1rem; }
        .page-title { font-size: 1.5rem; }
        .summary-card .value { font-size: 2rem; }
    }
    #genderChart {
        max-width: 200px;
        max-height: 200px;
    }
</style>

<div class="page-container">

    {{-- AREA NOTIFIKASI --}}
    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    {{-- JUDUL HALAMAN --}}
    <h1 class="page-title">SELAMAT DATANG ADMIN</h1>

    {{-- KARTU RINGKASAN DATA --}}
    <div class="summary-card-grid">
        {{-- ... (kartu ringkasan Anda tetap di sini) ... --}}
        <div class="summary-card">
            <p>Total Penduduk</p>
            <p class="value">{{ $data['total_penduduk'] }}</p>
        </div>
        <div class="summary-card">
            <p>Total Perangkat (RT/RW)</p>
            <p class="value">{{ $data['total_perangkat'] }}</p>
        </div>
        <div class="summary-card">
            <p>Total Komoditas</p>
            <p class="value">2</p> {{-- Dummy --}}
        </div>
        <div class="summary-card">
            <p>Total Bangunan</p>
            <p class="value">2</p> {{-- Dummy --}}
        </div>
    </div>

    {{-- AREA VISUALISASI DATA BARU --}}
    <div class="chart-grid">
        <div class="chart-container">
            <h3 class="chart-title">Penduduk Berdasarkan Jenis Kelamin</h3>
            <canvas id="genderChart" width="200" height="200"></canvas>
        </div>
        <div class="chart-container">
            <h3 class="chart-title">Penduduk Berdasarkan Usia</h3>
            <canvas id="ageChart"></canvas>
        </div>
    </div>
</div>

{{-- Tambahkan library Chart.js dan skrip untuk render grafik --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari Controller (diubah dari PHP ke JavaScript oleh Blade)
        const genderChartData = @json($genderChartData);
        const ageChartData = @json($ageChartData);

        // --- Render Grafik Jenis Kelamin (Pie Chart) ---
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'pie',
            data: {
                labels: genderChartData.labels,
                datasets: [{
                    label: 'Total',
                    data: genderChartData.data,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)', // Biru
                        'rgba(255, 99, 132, 0.8)',  // Merah muda
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                }
            }
        });

        // --- Render Grafik Kelompok Usia (Bar Chart) ---
        const ctxAge = document.getElementById('ageChart').getContext('2d');
        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: ageChartData.labels,
                datasets: [{
                    label: 'Jumlah Penduduk',
                    data: ageChartData.data,
                    backgroundColor: 'rgba(75, 192, 192, 0.8)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                radius: '70%',
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    legend: {
                        display: false,
                        position: 'top',
                    }
                }
            }
        });
    });
</script>
@endsection
