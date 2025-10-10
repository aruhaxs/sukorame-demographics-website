@extends('layouts.app')

@section('title', 'Demografi')

@section('content')

{{-- Bagian DATA PENDUDUK (Kartu Atas) --}}
<section class="demografi-section data-penduduk-section">
    <h2 class="section-title">DATA PENDUDUK</h2>
    <div class="data-cards-grid">
        <div class="data-card-demografi">
            <p class="card-label">TOTAL PENDUDUK</p>
            <p class="card-value">{{ $totalPenduduk ?? '0' }}</p>
        </div>
        <div class="data-card-demografi">
            <p class="card-label">JUMLAH KK</p>
            <p class="card-value">{{ $jumlahKK ?? '0' }}</p>
        </div>
        <div class="data-card-demografi">
            <p class="card-label">PEREMPUAN</p>
            <p class="card-value">{{ $jumlahPerempuan ?? '0' }}</p>
        </div>
        <div class="data-card-demografi">
            <p class="card-label">LAKI - LAKI</p>
            <p class="card-value">{{ $jumlahLakiLaki ?? '0' }}</p>
        </div>
    </div>
</section>

{{-- BAGIAN DEMOGRAFI DINAMIS (CHART UTAMA) --}}
<section class="demografi-section chart-container-section">
    <h2 class="section-title">DATA DEMOGRAFI</h2>

    {{-- FILTER UTAMA UNTUK MEMILIH DIMENSI DATA --}}
    <div class="filter-controls">
        <select id="data_dimension" class="select-modern" onchange="loadDemografiData(this.value)">
            <option value="agama">BERDASARKAN AGAMA</option>
            <option value="gender">BERDASARKAN GENDER</option>
            <option value="pendidikan">BERDASARKAN PENDIDIKAN TERAKHIR</option>
            <option value="pekerjaan">BERDASARKAN PEKERJAAN</option>
        </select>
    </div>

    {{-- Judul akan diperbarui oleh JavaScript --}}
    <h3 id="chartTitle" class="chart-title-sub">BERDASARKAN AGAMA</h3>

    <div class="chart-area-card">
        <div class="chart-container-inner agama-chart">
            {{-- Container Chart Pie --}}
            <div style="margin: 0 auto; max-width: 300px;">
                <canvas id="mainPieChart"></canvas>
            </div>
        </div>
    </div>
</section>

{{-- BAGIAN KELOMPOK UMUR (BAR CHART) --}}
<section class="demografi-section chart-container-section">
    <h3 class="section-title chart-title-sub">BERDASARKAN KELOMPOK UMUR</h3>
    <div class="chart-area-card">
        <div class="chart-container-inner">
            <canvas id="ageChart"></canvas>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
    // Data utama dari Controller
    let allDemografiData = @json($allDemografiData);
    let rawAgeChartData = @json($chartData);
    const chartColors = [
        '#f7a731', '#8c81ff', '#4b6cb7', '#a44bcf', '#55A08F', '#dc3545',
        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF'
    ];

    let mainChartInstance;

    // FUNGSI UTAMA: Memuat data dan merender chart (dinamis)
    function loadDemografiData(dimension) {
        const newData = allDemografiData[dimension];
        const chartTitle = document.getElementById('chartTitle');

        const titleMap = {
            'agama': 'BERDASARKAN AGAMA',
            'gender': 'BERDASARKAN GENDER',
            'pendidikan': 'BERDASARKAN PENDIDIKAN TERAKHIR',
            'pekerjaan': 'BERDASARKAN PEKERJAAN'
        };
        chartTitle.innerText = titleMap[dimension];

        mainChartInstance.data.labels = newData.labels;
        mainChartInstance.data.datasets[0].data = newData.counts;
        mainChartInstance.data.datasets[0].backgroundColor = chartColors.slice(0, newData.labels.length);
        mainChartInstance.update();
    }

    // Konfigurasi Legend Dinamis
    const customLegendOptions = {
        padding: 10,
        usePointStyle: true,
        generateLabels: function(chart) {
            const data = chart.data;
            if (data.labels.length && data.datasets.length) {
                return data.labels.map((label, i) => {
                    const value = data.datasets[0].data[i];
                    return {
                        text: label + ': ' + value + ' penduduk',
                        fillStyle: data.datasets[0].backgroundColor[i],
                        hidden: chart.getDatasetMeta(0).data[i].hidden,
                        index: i
                    };
                });
            }
            return [];
        }
    };

    // INISIALISASI PIE CHART UTAMA (Hanya berjalan sekali saat load)
    document.addEventListener('DOMContentLoaded', function() {
        const initialData = allDemografiData.agama;
        const ctx = document.getElementById('mainPieChart').getContext('2d');

        mainChartInstance = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: initialData.labels,
                datasets: [{
                    data: initialData.counts,
                    backgroundColor: chartColors.slice(0, initialData.labels.length),
                    borderColor: '#0b1a2e',
                    borderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true, position: 'bottom', labels: customLegendOptions.labels },
                    layout: { padding: 0 }
                }
            }
        });


        // INISIALISASI BAR CHART KELOMPOK UMUR
        const ageParsedData = rawAgeChartData;
        const barCtx = document.getElementById('ageChart').getContext('2d');

        const ageData = {
            labels: ageParsedData.labels,
            datasets: [{
                label: 'Total Penduduk',
                data: ageParsedData.total,
                backgroundColor: '#80B3A8',
                borderColor: '#55A08F',
                borderWidth: 1,
                barPercentage: 0.8,
                categoryPercentage: 0.8,
            }]
        };

        const ageOptions = {
            responsive: true,
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Jumlah Penduduk' } },
                x: {
                    title: { display: true, text: 'Kelompok Usia' },
                    ticks: { maxRotation: 45, minRotation: 45, autoSkip: false }
                }
            },
            plugins: { legend: { display: false } }
        };

        new Chart(barCtx, {
            type: 'bar',
            data: ageData,
            options: ageOptions
        });
    });
</script>
@endpush
