@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    /* == PINE GREEN THEME == */
    :root {
        --color-primary: #0a6847;
        --color-primary-light: #7aba78;
        --color-bg-dark: #0d1b2a;
        --color-bg-card: #1b263b;
        --color-text-light: #f0f8ff;
        --color-text-subtle: #a0aec0;
        --color-border: #4a5568;
    }

    /* == Tata Letak Utama == */
    .admin-title { font-size: 1.8rem; font-weight: 600; color: var(--color-text-light); margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;}
    .alert-success { background-color: var(--color-primary); color: var(--color-text-light); padding: 15px; border-radius: 8px; margin-bottom: 2rem; font-weight: 500; }
    
    /* == Kartu Ringkasan == */
    .summary-card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem; }
    .summary-card { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--color-primary); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .summary-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.4); }
    .summary-card p { margin: 0 0 0.5rem 0; color: var(--color-text-subtle); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    .summary-card .value { font-size: 2.5rem; font-weight: 700; color: var(--color-primary-light); }
    
    /* == Visualisasi Data / Grafik == */
    .chart-grid { display: grid; grid-template-columns: 1fr 1.5fr; gap: 1.5rem; }
    .chart-container { background-color: var(--color-bg-card); padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); }
    .chart-title { font-size: 1.2rem; font-weight: 600; color: var(--color-primary-light); margin-bottom: 1.5rem; text-align: center; }

    @media (max-width: 992px) {
        .chart-grid { grid-template-columns: 1fr; }
    }
</style>

{{-- JUDUL HALAMAN --}}
<h1 class="admin-title">Dashboard</h1>

{{-- NOTIFIKASI (jika ada) --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- KARTU RINGKASAN DATA --}}
<div class="summary-card-grid">
    <div class="summary-card">
        <p>Total Penduduk</p>
        <p class="value">{{ $data['total_penduduk'] }}</p>
    </div>
    <div class="summary-card">
        <p>Total RT & RW</p>
        <p class="value">{{ $data['total_rt_rw'] }}</p>
    </div>
    <div class="summary-card">
        <p>Total Komoditas</p>
        <p class="value">{{ $data['total_komoditas'] }}</p>
    </div>
    <div class="summary-card">
        <p>Total Bangunan</p>
        <p class="value">{{ $data['total_bangunan'] }}</p>
    </div>
</div>

{{-- AREA VISUALISASI DATA --}}
<div class="chart-grid">
    <div class="chart-container">
        <h3 class="chart-title">Penduduk Berdasarkan Jenis Kelamin</h3>
        <canvas id="genderChart"></canvas>
    </div>
    <div class="chart-container">
        <h3 class="chart-title">Penduduk Berdasarkan Kelompok Usia</h3>
        <canvas id="ageChart"></canvas>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const genderChartData = @json($genderChartData);
        const ageChartData = @json($ageChartData);

        const textColor = 'rgba(240, 248, 255, 0.8)'; // Warna teks (Alice Blue transparan)
        const gridColor = 'rgba(255, 255, 255, 0.1)'; // Warna garis grid

        // --- Render Grafik Jenis Kelamin (Doughnut Chart) ---
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: genderChartData.labels,
                datasets: [{
                    label: 'Total',
                    data: genderChartData.data,
                    // ✅ PERBAIKAN: Warna disesuaikan dengan data
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)', // Biru untuk Laki-laki
                        'rgba(255, 99, 132, 0.8)',  // Pink untuk Perempuan
                        'rgba(201, 203, 207, 0.8)'  // Abu-abu untuk data lain
                    ],
                    borderColor: 'var(--color-bg-card)',
                    borderWidth: 4,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { color: textColor }
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
                    // ✅ PERBAIKAN: Menggunakan array warna berbeda untuk setiap bar
                    backgroundColor: [
                        'rgba(122, 186, 120, 0.8)', // Hijau muda (Anak-anak)
                        'rgba(10, 104, 71, 0.8)',   // Hijau tua (Remaja)
                        'rgba(54, 162, 235, 0.8)',  // Biru (Dewasa)
                        'rgba(75, 85, 99, 0.8)'     // Abu-abu tua (Lansia)
                    ],
                    borderColor: [ // Border disesuaikan dengan warna bar
                        'rgb(122, 186, 120)',
                        'rgb(10, 104, 71)',
                        'rgb(54, 162, 235)',
                        'rgb(75, 85, 99)'
                    ],
                    borderWidth: 1,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { color: textColor },
                        grid: { color: gridColor }
                    },
                    x: {
                        ticks: { color: textColor },
                        grid: { color: 'transparent' }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(0,0,0,0.8)',
                        titleFont: { size: 14 },
                        bodyFont: { size: 12 },
                        padding: 10
                    }
                }
            }
        });
    });
</script>
@endpush