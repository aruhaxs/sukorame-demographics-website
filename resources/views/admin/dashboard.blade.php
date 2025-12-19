@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    /* == PINE GREEN THEME VARIABLES == */
    :root {
        --color-primary: #0a6847;       /* Hijau Pine Utama */
        --color-primary-hover: #085239; /* Hijau Pine Gelap (Hover) */
        --color-accent: #7aba78;        /* Hijau Muda (Aksen) */
        --color-bg-dark: #0d1b2a;       /* Background Utama Gelap */
        --color-bg-card: #1b263b;       /* Background Card */
        --color-text-light: #f0f8ff;    /* Teks Terang (AliceBlue) */
        --color-text-muted: #a0aec0;    /* Teks Muted */
        --color-border: #2d3748;        /* Border Halus */
    }

    /* == Global Styles Override for Dashboard == */
    .dashboard-container {
        font-family: 'Inter', sans-serif;
        color: var(--color-text-light);
        /* Setup Flexbox untuk Footer Sticky */
        display: flex;
        flex-direction: column;
        min-height: 85vh; /* Memastikan tinggi minimal agar footer di bawah */
    }

    /* Wrapper konten utama agar footer terdorong ke bawah */
    .dashboard-content {
        flex: 1;
        /* Menambahkan jarak antara konten paling bawah dengan footer */
        margin-bottom: 4rem; 
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid var(--color-border);
    }

    .admin-title {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0;
        color: var(--color-text-light);
        letter-spacing: -0.5px;
    }

    /* == Alert Styles == */
    .alert-success {
        background: rgba(10, 104, 71, 0.2);
        border: 1px solid var(--color-primary);
        color: var(--color-accent);
        padding: 1rem;
        border-radius: 8px;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* == Summary Cards Grid == */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2.5rem;
    }

    .stat-card {
        background-color: var(--color-bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid var(--color-border);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.5);
        border-color: var(--color-primary);
    }

    /* Dekorasi aksen di sebelah kiri card */
    .stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 4px;
        background: linear-gradient(to bottom, var(--color-primary), var(--color-accent));
        border-top-left-radius: 12px;
        border-bottom-left-radius: 12px;
    }

    .stat-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
    }

    .stat-title {
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--color-text-muted);
        font-weight: 600;
        margin: 0;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background-color: rgba(122, 186, 120, 0.1); /* Transparan hijau */
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--color-accent);
    }

    .stat-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--color-text-light);
        line-height: 1;
    }

    /* == Charts Layout == */
    .charts-wrapper {
        display: grid;
        grid-template-columns: 1fr 2fr; /* Rasio 1:2 untuk Doughnut:Bar */
        gap: 1.5rem;
    }

    .chart-card {
        background-color: var(--color-bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        border: 1px solid var(--color-border);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .chart-header {
        margin-bottom: 1.5rem;
        text-align: center;
    }

    .chart-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--color-text-light);
    }

    @media (max-width: 1024px) {
        .charts-wrapper {
            grid-template-columns: 1fr; /* Stack charts on smaller screens */
        }
    }
</style>

<div class="dashboard-container">
    
    {{-- Wrapper Konten Utama (agar footer terdorong ke bawah) --}}
    <div class="dashboard-content">
        {{-- Header --}}
        <div class="admin-header">
            <h1 class="admin-title">Dashboard Overview</h1>
            <span style="color: var(--color-text-muted); font-size: 0.9rem;">
                Update Terakhir: {{ \Carbon\Carbon::now()->format('d M Y') }}
            </span>
        </div>

        {{-- Alert Messages --}}
        @if(session('success'))
            <div class="alert-success">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                {{ session('success') }}
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="stats-grid">
            {{-- Card 1: Total Penduduk --}}
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Total Penduduk</p>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($data['total_penduduk']) }}</div>
            </div>

            {{-- Card 2: Total RT --}}
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Total RT</p>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>
                    </div>
                </div>
                <div class="stat-value">{{ $data['total_rt'] ?? 0 }}</div>
            </div>

            {{-- Card 3: Total RW --}}
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Total RW</p>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon><line x1="8" y1="2" x2="8" y2="18"></line><line x1="16" y1="6" x2="16" y2="22"></line></svg>
                    </div>
                </div>
                <div class="stat-value">{{ $data['total_rw'] ?? 0 }}</div>
            </div>

            {{-- Card 4: Total Bangunan --}}
            <div class="stat-card">
                <div class="stat-header">
                    <p class="stat-title">Total Bangunan</p>
                    <div class="stat-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><line x1="9" y1="22" x2="9" y2="22.01"></line><line x1="15" y1="22" x2="15" y2="22.01"></line><line x1="9" y1="18" x2="9" y2="18.01"></line><line x1="15" y1="18" x2="15" y2="18.01"></line><line x1="9" y1="14" x2="9" y2="14.01"></line><line x1="15" y1="14" x2="15" y2="14.01"></line><line x1="9" y1="10" x2="9" y2="10.01"></line><line x1="15" y1="10" x2="15" y2="10.01"></line><line x1="9" y1="6" x2="9" y2="6.01"></line><line x1="15" y1="6" x2="15" y2="6.01"></line></svg>
                    </div>
                </div>
                <div class="stat-value">{{ number_format($data['total_bangunan']) }}</div>
            </div>
        </div>

        {{-- Charts Section --}}
        <div class="charts-wrapper">
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Jenis Kelamin</h3>
                </div>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="genderChart"></canvas>
                </div>
            </div>
            <div class="chart-card">
                <div class="chart-header">
                    <h3 class="chart-title">Kelompok Usia</h3>
                </div>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="ageChart"></canvas>
                </div>
            </div>
        </div>
    </div> {{-- End .dashboard-content --}}

    {{-- INCLUDE FOOTER ADMIN
    @include('partials.admin_footer') --}}

</div> {{-- End .dashboard-container --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Data dari Controller
        const genderChartData = @json($genderChartData);
        const ageChartData = @json($ageChartData);

        // Styling Variables
        Chart.defaults.color = '#a0aec0';
        Chart.defaults.font.family = "'Inter', sans-serif";
        const gridColor = 'rgba(255, 255, 255, 0.05)';

        // 1. Chart Gender (Doughnut)
        const ctxGender = document.getElementById('genderChart').getContext('2d');
        new Chart(ctxGender, {
            type: 'doughnut',
            data: {
                labels: genderChartData.labels,
                datasets: [{
                    data: genderChartData.data,
                    backgroundColor: [
                        '#7aba78', // Laki-laki (Hijau Muda)
                        '#0a6847'  // Perempuan (Pine Green)
                    ],
                    borderColor: '#1b263b',
                    borderWidth: 5,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '75%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    }
                }
            }
        });

        // 2. Chart Usia (Bar)
        const ctxAge = document.getElementById('ageChart').getContext('2d');
        
        // Membuat Gradient untuk Bar Chart
        let gradientFill = ctxAge.createLinearGradient(0, 0, 0, 400);
        gradientFill.addColorStop(0, '#7aba78');
        gradientFill.addColorStop(1, 'rgba(122, 186, 120, 0.1)');

        new Chart(ctxAge, {
            type: 'bar',
            data: {
                labels: ageChartData.labels,
                datasets: [{
                    label: 'Jumlah Penduduk',
                    data: ageChartData.data,
                    backgroundColor: gradientFill,
                    borderColor: '#7aba78',
                    borderWidth: 1,
                    borderRadius: 4,
                    barPercentage: 0.6,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: gridColor,
                            borderDash: [5, 5]
                        },
                        ticks: {
                            padding: 10
                        },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#0d1b2a',
                        titleColor: '#f0f8ff',
                        bodyColor: '#a0aec0',
                        borderColor: '#2d3748',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        });
    });
</script>
@endpush