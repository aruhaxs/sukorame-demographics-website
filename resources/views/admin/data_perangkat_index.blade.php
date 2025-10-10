@extends('layouts.admin')

@section('title', 'Data RT/RW')

@push('styles')
<style>
    /* Gaya Header: Judul, Search, dan Tombol */
    .header-bar {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .admin-title {
        font-size: 1.8rem;
        font-weight: 600;
        color: #343A40;
    }

    .header-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        align-items: center;
    }

    .search-input {
        padding: 10px 15px;
        border-radius: 5px;
        border: 1px solid #ced4da;
        background-color: #ffffff;
        color: #495057;
        font-size: 0.9rem;
        width: 250px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }

    .search-input:focus {
        border-color: #4C9A2A;
        box-shadow: 0 0 0 3px rgba(76, 154, 42, 0.2);
        outline: none;
    }

    .btn-tambah-data {
        /* Gradien Hijau Profesional */
        background: linear-gradient(90deg, #1E5631 0%, #4C9A2A 100%);
        color: #ffffff;
        padding: 10px 18px;
        border-radius: 5px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.9rem;
        white-space: nowrap;
        border: none;
        transition: transform 0.2s, box-shadow 0.3s;
    }

    .btn-tambah-data:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(30, 86, 49, 0.3);
    }

    /* Kartu Statistik */
    .stat-cards-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .stat-card-small {
        background-color: #ffffff;
        padding: 1.5rem;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }
    
    .stat-card-small p {
        margin: 0 0 0.5rem 0;
        color: #555;
    }

    .stat-card-small .value {
        font-size: 2rem;
        font-weight: 700;
        color: #1E5631;
    }

    /* Daftar Data Perangkat */
    .card-list-wrapper {
        display: grid;
        gap: 1.5rem;
    }

    .data-card-item {
        background-color: #ffffff;
        border-radius: 10px;
        padding: 1.5rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        border: 1px solid #e9ecef;
    }
    
    .card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .data-card-item h3 {
        margin: 0;
        font-size: 1.25rem;
        color: #343A40;
    }

    .rt-rw-tag {
        font-size: 0.75rem;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 15px;
        color: #fff;
    }

    .rt-tag { background-color: #4C9A2A; } /* Hijau Terang */
    .rw-tag { background-color: #1E5631; } /* Hijau Tua */

    .card-body p {
        margin: 0.5rem 0;
        color: #333;
    }

    .card-actions {
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f1f1;
        text-align: right;
    }

    .btn-icon {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.2rem;
        color: #6c757d;
        text-decoration: none;
        margin-left: 0.5rem;
        transition: color 0.2s;
    }

    .btn-icon:hover { color: #4C9A2A; }
    .btn-icon[title="Hapus"]:hover { color: #dc3545; }

    /* --- Aturan Responsif --- */
    @media (max-width: 768px) {
        .header-bar {
            flex-direction: column;
            align-items: stretch;
        }
        .header-actions {
            flex-direction: column;
            width: 100%;
        }
        .search-input, .btn-tambah-data {
            width: 100%;
            text-align: center;
        }
        .stat-cards-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="header-bar">
    <h1 class="admin-title">Data RT / RW</h1>

    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari nama atau jabatan...">
        <a href="{{ route('admin.perangkat.create') }}" class="btn-tambah-data">Tambah Baru</a>
    </div>
</div>

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

<div class="card-list-wrapper">
    @forelse($perangkats as $p)
    <div class="data-card-item">
        <div class="card-header">
            <h3>{{ $p->jabatan }}</h3>
            <div>
                @if(str_contains(strtoupper($p->jabatan), 'RT'))
                    <span class="rt-rw-tag rt-tag">RT</span>
                @elseif(str_contains(strtoupper($p->jabatan), 'RW'))
                    <span class="rt-rw-tag rw-tag">RW</span>
                @endif
            </div>
        </div>
        <div class="card-body">
            <p><strong>Ketua:</strong> {{ $p->nama }}</p>
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
        <p>Belum ada data perangkat. Silakan tambahkan data baru.</p>
    </div>
    @endforelse
</div>

@endsection