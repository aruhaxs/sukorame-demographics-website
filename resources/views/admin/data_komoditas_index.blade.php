@extends('layouts.admin')

@section('title', 'Data Komoditas')

@section('content')

<style>
    /* Menggunakan kembali style dari halaman data lainnya */
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
    .header-actions { display: flex; gap: 1rem; align-items: center; }
    .search-input { background-color: var(--color-bg-card); border: 1px solid #334e6f; color: #E0E7FF; padding: 8px 15px; border-radius: 5px; width: 250px; }
    .btn-tambah-data { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 8px 15px; border-radius: 5px; text-decoration: none; font-weight: 600; white-space: nowrap; }
    .stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card-small { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .stat-card-small .value { font-size: 2rem; font-weight: 700; color: var(--color-primary-light); }
    .card-list-wrapper { display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 1.5rem; }
    .data-card-item { background-color: var(--color-bg-card); border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.3); position: relative; }
    .data-card-item h3 { margin: 0; font-size: 1.25rem; color: #fff; }
    .category-tag { font-size: 0.75rem; font-weight: 700; padding: 4px 8px; border-radius: 5px; background-color: var(--color-primary-light); color: var(--color-primary-dark); }
    .data-card-item p { margin: 0 0 0.5rem 0; font-size: 0.95rem; }
    .data-card-item p strong { color: var(--color-text-subtle); width: 100px; display: inline-block; }
    .card-actions { position: absolute; top: 1.5rem; right: 1.5rem; }
    .btn-icon { background: none; border: none; cursor: pointer; padding: 5px; font-size: 1rem; color: #AAB7C4; }
</style>

<div class="header-bar">
    <h1 class="admin-title">Data Komoditas</h1>
    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari komoditas...">
        <a href="{{ route('admin.komoditas.create') }}" class="btn-tambah-data">Tambah Komoditas</a>
    </div>
</div>

{{-- Statistik Card (DINAMIS) --}}
<div class="stat-cards-grid">
    <div class="stat-card-small">
        <p>Total Komoditas</p>
        <span class="value">{{ $totalKomoditas }}</span>
    </div>
    <div class="stat-card-small">
        <p>Total Kategori</p>
        <span class="value">{{ $totalKategori }}</span>
    </div>
</div>

{{-- Daftar Komoditas dalam Card View --}}
<div class="card-list-wrapper">
    @forelse($komoditas as $k)
    <div class="data-card-item">
        <div class="card-header">
            {{-- FIX: Menampilkan nama komoditas --}}
            <h3>{{ $k->nama_komoditas }}</h3>
            <span class="category-tag">{{ $k->kategori }}</span>
        </div>
        <div class="card-body">
            <p><strong>Produksi:</strong> {{ $k->produksi ?? '-' }}</p>
            <p><strong>Periode:</strong> {{ $k->periode ?? '-' }}</p>
            <p><strong>Produsen:</strong> {{ $k->produsen ?? '-' }}</p>
            <p><strong>Lokasi:</strong> {{ $k->lokasi ?? '-' }}</p>
            <p><strong>Harga:</strong> {{ $k->harga ?? '-' }}</p>
        </div>
        <div class="card-actions">
            {{-- FIX: Menambahkan link Edit dan Hapus --}}
            <a href="{{ route('admin.komoditas.edit', $k) }}" class="btn-icon" title="Edit">✏️</a>
            <form action="{{ route('admin.komoditas.destroy', $k) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️</button>
            </form>
        </div>
    </div>
    @empty
    <div class="data-card-item" style="text-align: center;">
        <p>Belum ada data komoditas.</p>
    </div>
    @endforelse
</div>
@endsection
