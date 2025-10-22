@extends('layouts.admin')

@section('title', 'Data Penduduk')

@section('content')

<style>
    /* Style khusus untuk halaman index data */
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
    .data-card-item { background-color: var(--color-bg-card); border-radius: 10px; padding: 1.5rem; box-shadow: 0 4px 10px rgba(0,0,0,0.3); }
    .data-card-item h3 { margin: 0 0 1rem 0; font-size: 1.25rem; color: #fff; }
</style>

<div class="header-bar">
    <h1 class="admin-title">Data Penduduk</h1>

    {{-- Container untuk Search dan Tombol Tambah --}}
    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari nama atau NIK...">
        <a href="{{ route('admin.penduduk.create') }}" class="btn-tambah-data">Tambah Warga Baru</a>
    </div>
</div>

{{-- Statistik Card --}}
<div class="stat-cards-grid">
    <div class="stat-card-small">
        <p>Total Penduduk</p>
        <span class="value">{{ $totalPenduduk }}</span>
    </div>
    <div class="stat-card-small">
        <p>KK Aktif</p>
        <span class="value">{{ $totalKK }}</span>
    </div>
</div>

{{-- Daftar Penduduk dalam Card View --}}
<div class="card-list-wrapper">
    @forelse($penduduks as $p)
    <div class="data-card-item">
        {{-- Konten kartu data penduduk --}}
        <h3>{{ $p->nama_lengkap }}</h3>
        <p><strong>NIK:</strong> {{ $p->nik }}</p>
        <p>
            Alamat:
            RT {{ optional($p->rt)->nomor_rt ?? 'N/A' }} /
            RW {{ optional($p->rw)->nomor_rw ?? 'N/A' }}
        </p>
        <div class="card-actions">
            <a href="{{ route('admin.penduduk.edit', $p) }}" class="btn-card edit">Edit</a>
            <form action="{{ route('admin.penduduk.destroy', $p) }}" method="POST" style="flex-grow: 1;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-card hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="data-card-item" style="text-align: center;">
        <p>Belum ada data penduduk.</p>
    </div>
    @endforelse
</div>

{{-- Tautan Pagination --}}
<div class="pagination-links" style="margin-top: 2rem;">
    {{ $penduduks->links() }}
</div>

@endsection
