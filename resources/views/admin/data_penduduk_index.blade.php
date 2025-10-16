@extends('layouts.admin')

@section('title', 'Data Penduduk')

@section('content')

@push('styles')
<style>
    /* == PINE GREEN THEME (Konsisten) == */
    :root {
        --color-primary: #0a6847;
        --color-primary-light: #7aba78;
        --color-bg-dark: #0d1b2a;
        --color-bg-card: #1b263b;
        --color-text-light: #f0f8ff;
        --color-text-subtle: #a0aec0;
        --color-border: #4a5568;
        --color-danger: #e53e3e;
    }

    /* == HEADER, SEARCH & STATS == */
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; }
    .admin-title { font-size: 1.8rem; font-weight: 600; color: var(--color-text-light); }
    .header-actions { display: flex; gap: 1rem; align-items: center; }
    .search-input { background-color: var(--color-bg-card); border: 1px solid var(--color-border); color: var(--color-text-light); padding: 10px 15px; border-radius: 8px; width: 250px; font-size: 0.9rem; }
    .btn-tambah-data { background-color: var(--color-primary); color: var(--color-text-light); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; }
    .stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card-small { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--color-primary); }
    .stat-card-small p { margin: 0 0 0.5rem 0; color: var(--color-text-subtle); text-transform: uppercase; font-size: 0.85rem; }
    .stat-card-small .value { font-size: 2.25rem; font-weight: 700; color: var(--color-primary-light); }

    /* == DESAIN CARD PENDUDUK == */
    .card-list-wrapper { display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 1.5rem; }
    .data-card-item { background-color: var(--color-bg-card); border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .data-card-item:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); }
    .card-content { padding: 1.5rem; flex-grow: 1; }
    .card-header { margin-bottom: 1rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; }
    .data-card-item h3 { margin: 0; font-size: 1.3rem; color: #fff; line-height: 1.4; }
    .gender-tag { font-size: 0.8rem; font-weight: 600; padding: 3px 8px; border-radius: 5px; color: var(--color-bg-dark); }
    .gender-tag.laki-laki { background-color: #87CEEB; } /* Biru Langit */
    .gender-tag.perempuan { background-color: #F08080; } /* Pink Terang */

    .card-body p { margin: 0.75rem 0; color: var(--color-text-light); display: flex; font-size: 0.95rem; }
    .card-body strong { color: var(--color-text-subtle); font-weight: 600; width: 90px; flex-shrink: 0; }
    .card-actions { background-color: rgba(0,0,0,0.2); padding: 1rem 1.5rem; text-align: right; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; display: flex; gap: 0.75rem; justify-content: flex-end; }
    .btn-aksi { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.2s ease; }
    .btn-aksi.edit { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.edit:hover { background-color: var(--color-primary); color: #fff; }
    .btn-aksi.hapus { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.hapus:hover { background-color: var(--color-danger); color: #fff; }
    .empty-card { text-align: center; padding: 2rem; color: var(--color-text-subtle); }

    /* == STYLING PAGINATION == */
    .pagination-wrapper { margin-top: 2rem; }
    .pagination { display: flex; justify-content: center; list-style: none; padding: 0; }
    .page-item .page-link { color: var(--color-text-subtle); background-color: #2d3748; border: 1px solid var(--color-border); padding: 8px 14px; margin: 0 3px; border-radius: 6px; text-decoration: none; transition: all 0.2s ease; }
    .page-item.active .page-link { background-color: var(--color-primary); color: #fff; border-color: var(--color-primary); }
    .page-item:not(.disabled) .page-link:hover { background-color: var(--color-primary-light); color: var(--color-bg-dark); border-color: var(--color-primary-light); }
    .page-item.disabled .page-link { color: #6b7280; background-color: var(--color-bg-card); border-color: var(--color-border); cursor: not-allowed; }
</style>
@endpush

@section('content')

<div class="header-bar">
    <h1 class="admin-title">Data Penduduk</h1>
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
        <p>KK Terdaftar</p>
        <span class="value">{{ $totalKK }}</span>
    </div>
</div>

{{-- Daftar Penduduk dalam Card View --}}
<div class="card-list-wrapper">
    @forelse($penduduks as $p)
    <div class="data-card-item">
        <div class="card-content">
            <div class="card-header">
                <h3>{{ $p->nama_lengkap }}</h3>
                <span class="gender-tag {{ $p->jenis_kelamin == 'Laki-laki' ? 'laki-laki' : 'perempuan' }}">
                    {{ $p->jenis_kelamin }}
                </span>
            </div>
            <div class="card-body">
                <p><strong>NIK:</strong> {{ $p->nik }}</p>
                <p><strong>No. KK:</strong> {{ $p->nomor_kk ?? '-' }}</p>
                <p><strong>Alamat:</strong>
                    {{-- ✅ PERBAIKAN: Menggunakan relasi untuk menampilkan nomor RT/RW --}}
                    RT {{ optional($p->rt)->nomor_rt ?? 'N/A' }} /
                    RW {{ optional($p->rw)->nomor_rw ?? 'N/A' }}
                </p>
            </div>
        </div>
        <div class="card-actions">
            <a href="{{ route('admin.penduduk.edit', $p) }}" class="btn-aksi edit">Edit</a>
            <form action="{{ route('admin.penduduk.destroy', $p) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-aksi hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">Hapus</button>
            </form>
        </div>
    </div>
    @empty
    <div class="data-card-item empty-card">
        <p>Belum ada data penduduk.</p>
    </div>
    @endforelse
</div>

{{-- Tautan Pagination --}}
<div class="pagination-wrapper">
    {{ $penduduks->links() }}
</div>

@endsection