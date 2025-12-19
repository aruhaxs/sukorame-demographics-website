@extends('layouts.app')

@section('title', 'Data Demografi')

@section('content')

<style>
    /* --- KONFIGURASI WARNA TEMA (SAMA DENGAN BERANDA) --- */
    :root {
        --primary-navy: #001f3f; 
        --accent-gold: #f7a731;
        --bg-page: #f3f4f6; 
        --card-bg: #ffffff;
        --text-dark: #1f2937;
        --text-grey: #6b7280;
        --soft-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    body {
        background-color: var(--bg-page);
        font-family: 'Poppins', sans-serif;
    }

    /* --- CONTAINER PENYAMA LEBAR (SAMA DENGAN NAVBAR) --- */
    .content-container {
        width: 100%;
        max-width: 1200px; /* Lebar maksimum sama dengan Navbar */
        margin: 0 auto;    /* Posisi Tengah */
        padding: 0 1.5rem; /* Padding Kiri-Kanan sama dengan Navbar */
        position: relative; 
    }

    /* --- PAGE HEADER --- */
    .page-header {
        margin-bottom: 2rem;
        padding-top: 2rem;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
    }

    .section-title {
        font-size: 2rem; 
        font-weight: 700; 
        color: var(--primary-navy); 
        margin-bottom: 5px;
        position: relative; 
        display: inline-block;
        text-transform: uppercase;
    }
    
    .section-title::after {
        content: ''; display: block; width: 60px; height: 4px; background: var(--accent-gold);
        margin-top: 5px; border-radius: 2px;
    }

    .page-subtitle {
        color: var(--text-grey);
        font-size: 1rem;
        margin-top: 0.5rem;
    }

    /* --- STATS CARDS GRID --- */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .demo-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--soft-shadow);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border-left: 5px solid var(--primary-navy); /* Aksen Navy */
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .demo-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .demo-card-content h3 {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--primary-navy);
        line-height: 1;
        margin: 0 0 5px 0;
    }

    .demo-card-content p {
        margin: 0;
        color: var(--text-grey);
        font-size: 0.9rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .demo-icon {
        width: 50px; height: 50px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.8rem;
        background-color: #f0fdf4; /* Default Hijau muda */
        color: var(--primary-navy);
    }
    
    /* Warna icon spesifik */
    .icon-blue { background-color: #eff6ff; color: #1e40af; }
    .icon-gold { background-color: #fffbeb; color: #d97706; }
    .icon-teal { background-color: #ccfbf1; color: #0f766e; }
    .icon-rose { background-color: #ffe4e6; color: #be123c; }

    /* --- CHART SECTION --- */
    .chart-card {
        background: var(--card-bg);
        border-radius: 16px;
        box-shadow: var(--soft-shadow);
        padding: 2rem;
        margin-bottom: 2rem;
        position: relative;
    }

    .chart-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
        border-bottom: 1px solid #f3f4f6;
        padding-bottom: 1rem;
    }

    .chart-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--primary-navy);
        margin-bottom: 0.25rem;
    }

    /* Custom Select Modern */
    .custom-select {
        padding: 0.6rem 2.5rem 0.6rem 1rem;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        background-color: #f9fafb;
        font-weight: 600;
        color: var(--primary-navy);
        cursor: pointer;
        outline: none;
        transition: all 0.2s;
        min-width: 150px;
    }
    .custom-select:focus {
        border-color: var(--primary-navy);
        box-shadow: 0 0 0 3px rgba(0, 31, 63, 0.1);
    }

    .chart-wrapper {
        position: relative;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 400px;
    }

    .pie-container {
        width: 100%;
        max-width: 500px; /* Batasi lebar pie chart agar tidak raksasa */
    }

    .bar-container {
        width: 100%;
        height: 400px;
    }

</style>

{{-- WRAPPER UTAMA DENGAN PADDING YANG SAMA DENGAN NAVBAR --}}
<div class="content-container">

    {{-- HEADER HALAMAN --}}
    <div class="page-header">
        <div>
            <h1 class="section-title">Data Demografi</h1>
            <p class="page-subtitle">Ringkasan statistik kependudukan Kelurahan Sukorame</p>
        </div>
        <div class="d-none d-md-block">
            {{-- PERBAIKAN: Menggunakan bg-white, shadow, padding lebih besar, dan warna teks Navy --}}
            <span class="badge bg-white border rounded-pill shadow-sm" 
                  style="font-size: 1rem; padding: 10px 20px; color: var(--primary-navy);">
                <i class="bi bi-calendar-event me-2" style="color: var(--accent-gold); font-size: 1.1rem;"></i> 
                {{ date('d F Y') }}
             </span>
        </div>
    </div>

    {{-- 1. GRID KARTU STATISTIK --}}
    <div class="stats-grid">

        <div class="demo-card" style="border-left-color: var(--accent-gold);">
            <div class="demo-card-content">
                <p>Kepala Keluarga</p>
                <h3>{{ $jumlahKK ?? '0' }}</h3>
            </div>
            <div class="demo-icon icon-gold">
                <i class="bi bi-house-door-fill"></i>
            </div>
        </div>
        
        {{-- Card 1: Total Penduduk --}}
        <div class="demo-card">
            <div class="demo-card-content">
                <p>Total Penduduk</p>
                <h3>{{ $totalPenduduk ?? '0' }}</h3>
            </div>
            <div class="demo-icon icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
        </div>

        {{-- Card 3: Laki-laki --}}
        <div class="demo-card" style="border-left-color: #0f766e;">
            <div class="demo-card-content">
                <p>Laki-Laki</p>
                <h3>{{ $jumlahLakiLaki ?? '0' }}</h3>
            </div>
            <div class="demo-icon icon-teal">
                <i class="bi bi-gender-male"></i>
            </div>
        </div>

        {{-- Card 4: Perempuan --}}
        <div class="demo-card" style="border-left-color: #be123c;">
            <div class="demo-card-content">
                <p>Perempuan</p>
                <h3>{{ $jumlahPerempuan ?? '0' }}</h3>
            </div>
            <div class="demo-icon icon-rose">
                <i class="bi bi-gender-female"></i>
            </div>
        </div>

    </div>

    {{-- 2. CHART DINAMIS (PIE CHART) --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h2 id="chartTitle">Distribusi Berdasarkan Agama</h2>
                <small class="text-muted">Klik pada legenda untuk menyembunyikan/menampilkan data</small>
            </div>
            <div>
                <select id="data_dimension" class="custom-select form-select" onchange="loadDemografiData(this.value)">
                    <option value="agama">Agama</option>
                    <option value="gender">Gender</option>
                    <option value="pekerjaan">Pekerjaan</option>
                </select>
            </div>
        </div>

        <div class="chart-wrapper">
            <div class="pie-container">
                <canvas id="mainPieChart"></canvas>
            </div>
        </div>
    </div>

    {{-- 3. CHART KELOMPOK UMUR (BAR CHART) --}}
    <div class="chart-card">
        <div class="chart-header">
            <div>
                <h2>Distribusi Kelompok Umur</h2>
                <small class="text-muted">Jumlah penduduk berdasarkan rentang usia (Interval 5 Tahun)</small>
            </div>
        </div>

        <div class="chart-wrapper">
            <div class="bar-container">
                <canvas id="ageChart"></canvas>
            </div>
        </div>
    </div>

</div> {{-- END CONTENT CONTAINER --}}

@endsection

@push('scripts')
{{-- Load Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // 1. Ambil data dari Controller (PHP ke JS)
    let allDemografiData = @json($allDemografiData);
    let rawAgeChartData = @json($chartData); // Data Usia

    // 2. Konfigurasi Warna
    const chartColors = [
        '#001f3f', // Navy Utama
        '#f7a731', // Gold Utama
        '#3b82f6', // Biru Terang
        '#ef4444', // Merah
        '#10b981', // Hijau
        '#8b5cf6', // Ungu
        '#ec4899', // Pink
        '#64748b'  // Abu-abu
    ];

    let mainChartInstance;
    let barChartInstance;

    // 3. Fungsi Ganti Data Pie Chart (Dropdown)
    function loadDemografiData(dimension) {
        if (!allDemografiData[dimension] || !mainChartInstance) return;

        const newData = allDemografiData[dimension];
        const titleText = 'Distribusi Berdasarkan ' + dimension.charAt(0).toUpperCase() + dimension.slice(1);
        document.getElementById('chartTitle').innerText = titleText;

        mainChartInstance.data.labels = newData.labels;
        mainChartInstance.data.datasets[0].data = newData.counts;
        // Reset warna agar urut lagi
        mainChartInstance.data.datasets[0].backgroundColor = chartColors.slice(0, newData.labels.length);
        mainChartInstance.update();
    }

    document.addEventListener('DOMContentLoaded', function() {
        
        // --- A. INISIALISASI PIE CHART ---
        const ctxPie = document.getElementById('mainPieChart');
        if (ctxPie) {
            mainChartInstance = new Chart(ctxPie.getContext('2d'), {
                type: 'doughnut', // Tipe Donat agar modern
                data: {
                    labels: allDemografiData.agama.labels,
                    datasets: [{
                        data: allDemografiData.agama.counts,
                        backgroundColor: chartColors.slice(0, allDemografiData.agama.labels.length),
                        borderWidth: 2,
                        borderColor: '#ffffff',
                        hoverOffset: 15
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    layout: { padding: 20 },
                    plugins: {
                        legend: { 
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 12, family: "'Poppins', sans-serif" }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 31, 63, 0.9)', // Tooltip Navy
                            padding: 12,
                            cornerRadius: 8,
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.parsed;
                                    let total = context.chart._metasets[context.datasetIndex].total;
                                    let percentage = ((value / total) * 100).toFixed(1) + "%";
                                    return ` ${label}: ${value} orang (${percentage})`;
                                }
                            }
                        }
                    },
                    cutout: '55%', // Lubang tengah donat
                }
            });
        }

        // --- B. INISIALISASI BAR CHART (USIA) ---
        const ctxBar = document.getElementById('ageChart');
        if (ctxBar && rawAgeChartData && rawAgeChartData.labels.length > 0) {
            barChartInstance = new Chart(ctxBar.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: rawAgeChartData.labels,
                    datasets: [{
                        label: 'Jumlah Penduduk',
                        data: rawAgeChartData.total,
                        backgroundColor: '#001f3f', // Batang Warna Navy
                        borderRadius: 4,            // Sudut tumpul
                        barPercentage: 0.7,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: '#f3f4f6', borderDash: [5, 5] },
                            ticks: { stepSize: 1 }
                        },
                        x: {
                            grid: { display: false },
                            title: { display: true, text: 'Rentang Usia (Tahun)', font: { weight: 'bold' } }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: 'rgba(0, 31, 63, 0.9)',
                            callbacks: {
                                label: function(context) { return ` ${context.raw} Jiwa`; }
                            }
                        }
                    }
                }
            });
        }
    });
</script>
@endpush