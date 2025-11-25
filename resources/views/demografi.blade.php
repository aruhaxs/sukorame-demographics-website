@extends('layouts.app')

@section('title', 'Demografi')

@section('content')

{{-- --- CSS KHUSUS HALAMAN INI --- --}}
<style>
    :root {
        --primary-navy: #001f3f;
        --accent-gold: #f7a731;
        --text-grey: #64748b;
        --bg-light: #f1f5f9;
        --card-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        background-color: var(--bg-light); /* Background halaman sedikit abu-abu agar card pop-up */
    }

    .page-header {
        margin-bottom: 2rem;
        padding-top: 1rem;
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--primary-navy);
        border-left: 5px solid var(--accent-gold);
        padding-left: 15px;
    }

    /* --- STATS CARDS --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid rgba(255,255,255,0.5);
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; width: 4px; height: 100%;
        background: var(--primary-navy);
        opacity: 0.5;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--primary-navy);
        margin: 0;
        line-height: 1.2;
    }

    .stat-content p {
        margin: 0;
        color: var(--text-grey);
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* Variasi Warna Icon */
    .icon-blue { background: #e0f2fe; color: #0284c7; }
    .icon-gold { background: #fef3c7; color: #d97706; }
    .icon-pink { background: #fce7f3; color: #db2777; }
    .icon-cyan { background: #ecfeff; color: #0891b2; }

    /* --- CHART SECTION --- */
    .chart-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--card-shadow);
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 1rem;
    }

    .chart-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-navy);
        margin: 0;
    }

    .custom-select {
        padding: 0.5rem 2rem 0.5rem 1rem;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background-color: #fff;
        font-weight: 500;
        color: var(--primary-navy);
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s;
    }
    .custom-select:focus {
        border-color: var(--accent-gold);
        box-shadow: 0 0 0 3px rgba(247, 167, 49, 0.2);
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    /* Tinggi Chart Pie */
    .pie-container {
        height: 400px; 
        width: 100%;
        max-width: 500px;
    }

    /* Tinggi Chart Bar (untuk masa depan) */
    .bar-container {
        height: 400px;
        width: 100%;
    }
</style>

<div class="container py-4">
    
    {{-- Header Halaman --}}
    <div class="page-header">
        <h1 class="page-title">Dashboard Demografi</h1>
        <p class="text-muted ms-3 mt-1">Statistik Kependudukan Kelurahan Sukorame</p>
    </div>

    {{-- 1. STATS CARDS GRID --}}
    <div class="stats-grid">
        {{-- Card Total --}}
        <div class="stat-card">
            <div class="stat-content">
                <p>Total Penduduk</p>
                <h3>{{ $totalPenduduk ?? '0' }}</h3>
            </div>
            <div class="stat-icon icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        {{-- Card KK --}}
        <div class="stat-card">
            <div class="stat-content">
                <p>Kepala Keluarga</p>
                <h3>{{ $jumlahKK ?? '0' }}</h3>
            </div>
            <div class="stat-icon icon-gold">
                <i class="bi bi-house-door-fill"></i>
            </div>
        </div>

        {{-- Card Laki-laki --}}
        <div class="stat-card">
            <div class="stat-content">
                <p>Laki-Laki</p>
                <h3>{{ $jumlahLakiLaki ?? '0' }}</h3>
            </div>
            <div class="stat-icon icon-cyan">
                <i class="bi bi-gender-male"></i>
            </div>
        </div>

        {{-- Card Perempuan --}}
        <div class="stat-card">
            <div class="stat-content">
                <p>Perempuan</p>
                <h3>{{ $jumlahPerempuan ?? '0' }}</h3>
            </div>
            <div class="stat-icon icon-pink">
                <i class="bi bi-gender-female"></i>
            </div>
        </div>
    </div>

    {{-- 2. CHART DINAMIS (PIE) --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h2 class="chart-title" id="chartTitle">DISTRIBUSI BERDASARKAN AGAMA</h2>
                <small class="text-muted">Visualisasi data persentase penduduk</small>
            </div>
            
            {{-- Filter Dropdown Modern --}}
            <select id="data_dimension" class="custom-select" onchange="loadDemografiData(this.value)">
                <option value="agama">Agama</option>
                <option value="gender">Gender</option>
                {{-- <option value="pendidikan">Pendidikan</option> --}}
                <option value="pekerjaan">Pekerjaan</option>
            </select>
        </div>

        <div class="chart-wrapper">
            <div class="pie-container">
                <canvas id="mainPieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. CHART USIA (BAR) - HIDDEN --}}
    {{-- 
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h2 class="chart-title">DISTRIBUSI KELOMPOK UMUR</h2>
                <small class="text-muted">Rentang usia penduduk per 5 tahun</small>
            </div>
        </div>
        <div class="chart-wrapper">
            <div class="bar-container">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
    </div>
    --}}

</div>

@endsection

@push('scripts')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Ambil data dari Controller (Sama seperti referensi Anda)
    let allDemografiData = @json($allDemografiData);
    let rawAgeChartData = @json($chartData);

    // Warna Chart yang lebih soft & professional
    const chartColors = [
        '#001f3f', // Navy Utama
        '#f7a731', // Emas Aksen
        '#3b82f6', // Biru Terang
        '#ef4444', // Merah
        '#10b981', // Hijau
        '#8b5cf6', // Ungu
        '#64748b', // Abu-abu
        '#f472b6', // Pink
    ];

    let mainChartInstance;
    let barChartInstance;

    // Fungsi Update Chart Pie
    function loadDemografiData(dimension) {
        if (!allDemografiData[dimension] || !mainChartInstance) return;

        const newData = allDemografiData[dimension];
        
        // Update Judul agar terlihat dinamis
        const titleText = 'DISTRIBUSI BERDASARKAN ' + dimension.toUpperCase();
        document.getElementById('chartTitle').innerText = titleText;

        mainChartInstance.data.labels = newData.labels;
        mainChartInstance.data.datasets[0].data = newData.counts;
        // Gunakan warna yang cukup untuk jumlah label
        mainChartInstance.data.datasets[0].backgroundColor = chartColors.slice(0, newData.labels.length);
        mainChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. PIE CHART ---
        const ctxPie = document.getElementById('mainPieChart');
        if (ctxPie) {
            mainChartInstance = new Chart(ctxPie.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: allDemografiData.agama.labels,
                    datasets: [{
                        data: allDemografiData.agama.counts,
                        backgroundColor: chartColors.slice(0, allDemografiData.agama.labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff', // Garis pemisah putih agar bersih
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: {
                        padding: 20
                    },
                    plugins: {
                        legend: { 
                            position: 'right', // Legend di kanan agar lebih rapi
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: {
                                    family: "'Helvetica Neue', 'Helvetica', 'Arial', sans-serif",
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 31, 63, 0.9)',
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false,
                        }
                    },
                    cutout: '65%', // Lubang tengah doughnut chart
                }
            });
        }

        /*
        // --- 2. BAR CHART (HIDDEN) ---
        const ctxBar = document.getElementById('ageChart');
        if (ctxBar && rawAgeChartData && rawAgeChartData.labels.length > 0) {
             // Logic Bar Chart disimpan disini (Sama seperti referensi)
             // ...
        }
        */
    });
</script>
@endpush