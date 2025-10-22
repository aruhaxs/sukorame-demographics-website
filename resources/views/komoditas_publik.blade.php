{{-- Ganti 'layouts.app' dengan nama layout publik Anda --}}
@extends('layouts.app')

@section('title', 'Potensi & Komoditas Unggulan')

@push('styles')
<style>
    /* Sesuaikan variabel warna dengan tema publik Anda */
    :root {
        --komoditas-bg-card: #ffffff;
        --komoditas-text-primary: #333333;
        --komoditas-text-subtle: #666666;
        --komoditas-border-color: #dddddd;
        --komoditas-primary-color: #0a6847; /* Hijau */
        --komoditas-tag-bg: #e2f5ea;
        --komoditas-tag-text: #0a6847;
    }

    .komoditas-container {
        max-width: 1200px;
        margin: 2rem auto;
        padding: 0 1rem;
    }

    .komoditas-header {
        text-align: center;
        margin-bottom: 2.5rem;
    }
    .komoditas-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--komoditas-text-primary);
        margin-bottom: 0.5rem;
    }
    .komoditas-header p {
        color: var(--komoditas-text-subtle);
        font-size: 1.1rem;
    }

    /* Filter & Search */
    .komoditas-filters {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2.5rem;
        background-color: var(--komoditas-bg-card, #f8f9fa);
        padding: 1rem;
        border-radius: 8px;
        border: 1px solid var(--komoditas-border-color, #dee2e6);
    }
    .komoditas-filters .form-group {
        flex: 1 1 250px;
    }
    .komoditas-filters label { display: none; } /* Sembunyikan label, gunakan placeholder */
    .komoditas-filters .form-control,
    .komoditas-filters .form-select {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid var(--komoditas-border-color, #ced4da);
        background-color: #ffffff;
        color: var(--komoditas-text-primary, #495057);
        box-sizing: border-box;
        font-size: 0.95rem;
    }

    /* Kategori Section */
    .kategori-section {
        margin-bottom: 3rem;
    }
    .kategori-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: var(--komoditas-primary-color);
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--komoditas-primary-color);
        padding-bottom: 0.5rem;
        display: inline-block;
    }

    /* Card Grid */
    .komoditas-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1.5rem;
    }

    /* Card Item */
    .komoditas-card {
        background-color: var(--komoditas-bg-card);
        border-radius: 8px;
        border: 1px solid var(--komoditas-border-color);
        box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        padding: 1.5rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: flex; /* Untuk flexbox layout di dalam card */
        flex-direction: column;
    }
    .komoditas-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 15px rgba(0,0,0,0.1);
    }
    .komoditas-card .card-header {
        margin-bottom: 1rem;
    }
    .komoditas-card h3 { /* Nama Komoditas */
        font-size: 1.2rem;
        font-weight: 600;
        color: var(--komoditas-text-primary);
        margin: 0 0 0.5rem 0;
    }
    .komoditas-card .kategori-tag {
        background-color: var(--komoditas-tag-bg);
        color: var(--komoditas-tag-text);
        font-size: 0.75rem;
        font-weight: 600;
        padding: 3px 10px;
        border-radius: 12px;
        display: inline-block;
    }
    .komoditas-card .card-details {
        font-size: 0.9rem;
        color: var(--komoditas-text-subtle);
        line-height: 1.6;
        flex-grow: 1; /* Agar detail mengisi ruang */
        margin-bottom: 1rem;
    }
    .komoditas-card .card-details p {
        margin: 0.5rem 0;
    }
    .komoditas-card .card-details strong {
         color: var(--komoditas-text-primary);
         margin-right: 5px;
    }
    .komoditas-card .card-footer { /* Untuk Harga */
        margin-top: auto; /* Dorong ke bawah */
        padding-top: 1rem;
        border-top: 1px solid var(--komoditas-border-color);
    }
    .komoditas-card .harga {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--komoditas-primary-color);
    }
    .no-data {
        text-align: center;
        color: var(--komoditas-text-subtle);
        padding: 2rem;
    }
    /* Hide card if filtered out */
    .komoditas-card.hidden {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="komoditas-container">

    <header class="komoditas-header">
        <h1>Potensi & Komoditas Unggulan</h1>
        <p>Jelajahi berbagai produk dan potensi ekonomi dari Kelurahan Sukorame.</p>
    </header>

    {{-- Filter & Search --}}
    <div class="komoditas-filters">
        <div class="form-group">
            {{-- <label for="search-komoditas">Cari Komoditas</label> --}}
            <input type="text" id="search-komoditas" class="form-control" placeholder="🔍 Cari nama komoditas...">
        </div>
        <div class="form-group">
            {{-- <label for="filter-kategori">Filter Kategori</label> --}}
            <select id="filter-kategori" class="form-select">
                <option value="">Semua Kategori</option>
                @foreach($kategoriList as $kategori)
                    <option value="{{ $kategori }}">{{ $kategori }}</option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- Daftar Komoditas --}}
    @forelse($komoditasGrouped as $kategori => $items)
    <section class="kategori-section" data-kategori="{{ $kategori }}">
        <h2 class="kategori-title">{{ $kategori }}</h2>
        <div class="komoditas-grid">
            @foreach($items as $item)
            <div class="komoditas-card" data-nama="{{ strtolower($item->nama_komoditas) }}">
                <div class="card-header">
                    <h3>{{ $item->nama_komoditas }}</h3>
                    <span class="kategori-tag">{{ $item->kategori }}</span>
                </div>
                <div class="card-details">
                    @if($item->produsen)
                        <p><strong>Produsen:</strong> {{ $item->produsen }}</p>
                    @endif
                    @if($item->lokasi)
                        <p><strong>Lokasi:</strong> {{ $item->lokasi }}</p>
                    @endif
                    @if($item->periode)
                        <p><strong>Periode:</strong> {{ $item->periode }}</p>
                    @endif
                     @if($item->produksi)
                        <p><strong>Produksi:</strong> {{ $item->produksi }}</p>
                    @endif
                </div>
                @if($item->harga)
                <div class="card-footer">
                    {{-- Format harga jika perlu --}}
                    <span class="harga">Harga: Rp {{ number_format($item->harga, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </section>
    @empty
        <p class="no-data">Belum ada data komoditas yang tersedia.</p>
    @endforelse

</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-komoditas');
    const categoryFilter = document.getElementById('filter-kategori');
    const komoditasCards = document.querySelectorAll('.komoditas-card');
    const kategoriSections = document.querySelectorAll('.kategori-section');

    function filterKomoditas() {
        const searchTerm = searchInput.value.toLowerCase();
        const selectedCategory = categoryFilter.value;
        let visibleSections = 0;

        kategoriSections.forEach(section => {
            let sectionHasVisibleCard = false;
            const sectionCategory = section.getAttribute('data-kategori');
            const cardsInSection = section.querySelectorAll('.komoditas-card');

            cardsInSection.forEach(card => {
                const namaKomoditas = card.getAttribute('data-nama');
                const nameMatch = namaKomoditas.includes(searchTerm);
                const categoryMatch = (selectedCategory === "" || sectionCategory === selectedCategory);

                if (nameMatch && categoryMatch) {
                    card.classList.remove('hidden');
                    sectionHasVisibleCard = true;
                } else {
                    card.classList.add('hidden');
                }
            });

            // Sembunyikan/tampilkan judul kategori
            if (sectionHasVisibleCard) {
                section.style.display = 'block';
                visibleSections++;
            } else {
                section.style.display = 'none';
            }
        });

        // Tampilkan pesan jika tidak ada hasil
        // (Anda bisa membuat elemen <p> khusus untuk ini jika mau)
        // const noResultMessage = document.getElementById('no-result-message');
        // if (visibleSections === 0) {
        //     noResultMessage.style.display = 'block';
        // } else {
        //     noResultMessage.style.display = 'none';
        // }
    }

    searchInput.addEventListener('input', filterKomoditas);
    categoryFilter.addEventListener('change', filterKomoditas);
});
</script>
@endpush
