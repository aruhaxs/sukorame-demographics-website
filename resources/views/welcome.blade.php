@extends('layouts.app')

@section('title', 'Beranda')

@section('content')

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
            {{-- Kartu 1: Total Penduduk --}}
            <div class="data-card large">
                <div class="card-content">
                    <p class="data-label">Jumlah Penduduk</p>
                    <p class="data-value">{{ $totalPenduduk ?? '0' }}</p>
                </div>
                <div class="card-image-placeholder"></div>
            </div>

            {{-- Kartu 2: Laki-laki & Perempuan --}}
            <div class="data-card small chart-card">
                <div class="chart-label">
                    <p>Laki-laki: {{ $jumlahLakiLaki ?? '0' }}</p>
                    <p>Perempuan: {{ $jumlahPerempuan ?? '0' }}</p>
                </div>
                <div class="bar-chart">
                    @php
                        $totalGender = ($jumlahLakiLaki ?? 0) + ($jumlahPerempuan ?? 0);
                        $lakiPercent = $totalGender > 0 ? ($jumlahLakiLaki / $totalGender) * 100 : 0;
                        $perempuanPercent = $totalGender > 0 ? ($jumlahPerempuan / $totalGender) * 100 : 0;
                    @endphp
                    <div class="bar-fill laki" style="width: {{ $lakiPercent }}%;"></div>
                    <div class="bar-fill perempuan" style="width: {{ $perempuanPercent }}%;"></div>
                </div>
            </div>

            {{-- Kartu 3: Jumlah Rumah Tangga (Statis) --}}
            <div class="data-card large">
                <div class="card-content">
                    <p class="data-label">Jumlah Rumah Tangga</p>
                    <p class="data-value">75</p>
                </div>
                <div class="card-image-placeholder"></div>
            </div>

            <div class="data-card small chart-card">
                <div class="chart-label">
                    {{-- DIBENAHI: Tampilkan persentase per kategori --}}
                    <p>Bayi: {{ $usiaData['counts']['Bayi'] ?? '0' }} ({{ round($usiaData['percentages']['Bayi'] ?? 0) }}%)</p>
                    <p>Anak-anak: {{ $usiaData['counts']['Anak-anak'] ?? '0' }} ({{ round($usiaData['percentages']['Anak-anak'] ?? 0) }}%)</p>
                    <p>Remaja: {{ $usiaData['counts']['Remaja'] ?? '0' }} ({{ round($usiaData['percentages']['Remaja'] ?? 0) }}%)</p>
                    <p>Dewasa: {{ $usiaData['counts']['Dewasa'] ?? '0' }} ({{ round($usiaData['percentages']['Dewasa'] ?? 0) }}%)</p>
                    <p>Lansia: {{ $usiaData['counts']['Lansia'] ?? '0' }} ({{ round($usiaData['percentages']['Lansia'] ?? 0) }}%)</p>
                </div>

                <div class="bar-chart-multi">
                    {{-- Total Usia Muda dan Usia Dewasa dihitung dari Controller, TAPI KITA BUTUH PERSENTASE DI PHP --}}
                    @php
                        // Hitungan persentase total dua kelompok besar sudah di Controller.
                        // Ambil dari array percentages:
                        $mudaPercent = $usiaData['percentages']['Usia Muda'] ?? 0;
                        $dewasaPercent = $usiaData['percentages']['Usia Dewasa'] ?? 0;
                    @endphp

                    {{-- Menampilkan 5 bar fill di dalam satu bar chart multi --}}
                    {{-- Kita akan menggunakan persentase masing-masing kategori usia di sini --}}

                    <div class="bar-fill-multi" style="width: {{ $usiaData['percentages']['Bayi'] ?? 0 }}%; background-color: #f7a731;" title="Bayi"></div>
                    <div class="bar-fill-multi" style="width: {{ $usiaData['percentages']['Anak-anak'] ?? 0 }}%; background-color: #8c81ff;" title="Anak-anak"></div>
                    <div class="bar-fill-multi" style="width: {{ $usiaData['percentages']['Remaja'] ?? 0 }}%; background-color: #4b6cb7;" title="Remaja"></div>
                    <div class="bar-fill-multi" style="width: {{ $usiaData['percentages']['Dewasa'] ?? 0 }}%; background-color: #55A08F;" title="Dewasa"></div>
                    <div class="bar-fill-multi" style="width: {{ $usiaData['percentages']['Lansia'] ?? 0 }}%; background-color: #dc3545;" title="Lansia"></div>
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
                <h3 class="info-title">Jumlah Penduduk</h3>
                <p class="info-description">
                    Lagi-lagi aku bingung mau nulis apa. Coba gini dulu sementara ya.
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
                Vita Sari, S.E., M.M., memegang jabatan sebagai Lurah Kelurahan Sukorame, yang menempatkannya sebagai pimpinan eksekutif tertinggi yang bertanggung jawab atas seluruh aspek pemerintahan, pembangunan, dan kemasyarakatan di wilayahnya. Perannya bukan sekadar administratif, melainkan sebagai seorang manajer publik strategis yang mengoordinasikan dan mengawasi kinerja tiga seksi utama yang mencakup bidang pelayanan umum, pembangunan ekonomi dan pemberdayaan masyarakat, serta penjaminan ketentraman dan ketertiban sosial.
            </p>
        </div>
    </div>
</section>

<section class="location-section">
    <h2 class="section-title">LOKASI</h2>
    <div class="map-container">
        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.535359781846!2d111.993131!3d-7.733535!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7856d35272a5a5%3A0x600b33b9134a66a!2sKelurahan%20Sukorame!5e0!3m2!1sen!2sid!4v1699999999999!5m2!1sen!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
    </div>
</section>

@endsection
