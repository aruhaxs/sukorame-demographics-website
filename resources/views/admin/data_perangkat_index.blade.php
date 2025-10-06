@extends('layouts.admin')

@section('title', 'Data RT/RW')

@section('content')

<style>
    /* Style khusus untuk halaman ini */
    .header-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    /* PENTING: Container untuk Search dan Tombol Tambah */
    .header-actions {
        display: flex;
        gap: 1rem;
        align-items: center;
    }

    /* PENTING: Style untuk Search Input */
    .search-input {
        background-color: var(--color-bg-card);
        border: 1px solid #334e6f;
        color: #E0E7FF;
        padding: 8px 15px;
        border-radius: 5px;
        width: 250px;
        font-size: 0.9rem;
    }
    .search-input:focus {
        outline: none;
        border-color: var(--color-primary-light);
    }

    /* PENTING: Memastikan tombol terlihat modern */
    .btn-tambah-data {
        background-color: var(--color-primary-light);
        color: var(--color-primary-dark);
        padding: 8px 15px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap; /* Mencegah teks turun baris */
    }

    /* Style untuk kartu statistik dan list data */
    .stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; }
    .stat-card-small { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 10px; text-align: center; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .stat-card-small .value { font-size: 2rem; font-weight: 700; color: var(--color-primary-light); }
    .card-list-wrapper { display: grid; gap: 1.5rem; }
    .data-card-item { background-color: var(--color-bg-card); border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.3); position: relative; }
    .data-card-item h3 { margin: 0; font-size: 1.25rem; color: #fff; }
</style>

<div class="header-bar">
    <h1 class="admin-title">Data RT / RW</h1>

    {{-- Container untuk Search dan Tombol Tambah --}}
    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari RT/RW atau ketua...">
        <a href="{{ route('admin.perangkat.create') }}" class="btn-tambah-data">Tambah Baru</a>
    </div>
</div>

{{-- Statistik Card --}}
<div class="stat-cards-grid">
    <div class="stat-card-small">
        <p>Total RT</p>
        <span class="value">{{ $totalRT }}</span>
    </div>
    <div class="stat-card-small">
        <p>Total RW</p>
        <span class="value">{{ $totalRW }}</span>
    </div>
</div>

{{-- Daftar Perangkat dalam Card View --}}
<div class="card-list-wrapper">
    @forelse($perangkats as $p)
    <div class="data-card-item">
        <div class="card-header">
            <h3>{{ $p->jabatan }}</h3>
            @if(str_contains(strtoupper($p->jabatan), 'RT'))
                <span class="rt-rw-tag rt-tag">RT</span>
            @elseif(str_contains(strtoupper($p->jabatan), 'RW'))
                <span class="rt-rw-tag rw-tag">RW</span>
            @endif
        </div>
        <div class="card-body">
            <p><strong>Nama:</strong> {{ $p->nama }}</p>
            <p><strong>No. HP:</strong> {{ $p->nomor_telepon }}</p>
        </div>
        <div class="card-actions">
            <a href="{{ route('admin.perangkat.edit', $p) }}" class="btn-icon" title="Edit">✏️</a>
            <form action="{{ route('admin.perangkat.destroy', $p) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">🗑️</button>
            </form>
        </div>
    </div>
    @empty
    <div class="data-card-item" style="text-align: center;">
        <p>Belum ada data perangkat.</p>
    </div>
    @endforelse
</div>

@endsection
