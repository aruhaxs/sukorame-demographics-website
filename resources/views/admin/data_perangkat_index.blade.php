@extends('layouts.admin')

@section('title', 'Data RT & RW')

@push('styles')
<style>
    /* == PINE GREEN THEME == */
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

    /* == HEADER & STATS (Konsisten) == */
    .header-bar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 1rem; margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem;}
    .admin-title { font-size: 1.8rem; font-weight: 600; color: var(--color-text-light); }
    .btn-tambah-data { background-color: var(--color-primary); color: var(--color-text-light); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: background-color 0.2s; }
    .btn-tambah-data:hover { background-color: #0d8259; }
    .stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card-small { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--color-primary); }
    .stat-card-small p { margin: 0 0 0.5rem 0; color: var(--color-text-subtle); text-transform: uppercase; font-size: 0.85rem; }
    .stat-card-small .value { font-size: 2.25rem; font-weight: 700; color: var(--color-primary-light); }

    /* == STRUKTUR KATEGORI BARU == */
    .category-section { margin-bottom: 3rem; }
    .category-section-title { font-size: 1.5rem; font-weight: 600; color: var(--color-primary-light); margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 2px solid var(--color-border); }

    /* == DESAIN CARD BARU == */
    .card-list-wrapper { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; }
    .data-card-item { background-color: var(--color-bg-card); border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; border-left: 4px solid var(--color-primary); transition: transform 0.2s ease, box-shadow 0.2s ease; }
    .data-card-item:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.4); }
    .card-content { padding: 1.5rem; flex-grow: 1; }
    .card-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1rem; }
    .data-card-item h3 { margin: 0; font-size: 1.25rem; color: #fff; line-height: 1.4; }
    .rt-rw-tag { font-size: 0.75rem; font-weight: 700; padding: 4px 10px; border-radius: 15px; color: #fff; flex-shrink: 0; margin-left: 10px; }
    .rt-tag { background-color: var(--color-primary-light); color: var(--color-bg-dark); }
    .rw-tag { background-color: var(--color-primary); }
    .card-body p { margin: 0.5rem 0; color: var(--color-text-subtle); display: flex; font-size: 0.95rem; }
    .card-body strong { color: var(--color-text-light); font-weight: 600; width: 60px; flex-shrink: 0; }
    .card-actions { background-color: rgba(0,0,0,0.2); padding: 1rem 1.5rem; text-align: right; border-bottom-left-radius: 12px; border-bottom-right-radius: 12px; display: flex; gap: 0.75rem; justify-content: flex-end; }
    .btn-aksi { padding: 6px 14px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.85rem; border: none; cursor: pointer; transition: all 0.2s ease; }
    .btn-aksi.edit { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.edit:hover { background-color: var(--color-primary); color: #fff; }
    .btn-aksi.hapus { background-color: #2d3748; color: var(--color-text-subtle); }
    .btn-aksi.hapus:hover { background-color: var(--color-danger); color: #fff; }
    .empty-card { grid-column: 1 / -1; text-align: center; padding: 2rem; color: var(--color-text-subtle); }
</style>
@endpush

@section('content')
<div class="header-bar">
    <h1 class="admin-title">Data RT / RW</h1>
    <div class="header-actions">
        <a href="{{ route('admin.rt-rw.create') }}" class="btn-tambah-data">Tambah Baru</a>
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

{{-- KATEGORI DATA RW --}}
<div class="category-section">
    <h2 class="category-section-title">Data RW</h2>
    <div class="card-list-wrapper">
        {{-- ✅ PERBAIKAN: Looping langsung dari variabel $rws --}}
        @forelse($rws as $item)
            <div class="data-card-item">
                <div class="card-content">
                    <div class="card-header">
                        <h3>Ketua RW {{ $item->nomor_rw }}</h3>
                        <span class="rt-rw-tag rw-tag">RW</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $item->ketua_rw }}</p>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.rt-rw.edit', ['type' => 'rw', 'id' => $item->id]) }}" class="btn-aksi edit">Edit</a>
                    <form action="{{ route('admin.rt-rw.destroy', ['type' => 'rw', 'id' => $item->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-aksi hapus" onclick="return confirm('Yakin ingin menghapus data RW {{ $item->nomor_rw }}? Menghapus RW akan gagal jika masih memiliki data RT.')">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="data-card-item empty-card">
                <p>Belum ada data RW yang ditambahkan.</p>
            </div>
        @endforelse
    </div>
</div>

{{-- KATEGORI DATA RT --}}
<div class="category-section">
    <h2 class="category-section-title">Data RT</h2>
    <div class="card-list-wrapper">
        {{-- ✅ PERBAIKAN: Looping langsung dari variabel $rts --}}
        @forelse($rts as $item)
            <div class="data-card-item">
                 <div class="card-content">
                    <div class="card-header">
                        <h3>Ketua RT {{ $item->nomor_rt }} / RW {{ $item->rw->nomor_rw ?? 'N/A' }}</h3>
                        <span class="rt-rw-tag rt-tag">RT</span>
                    </div>
                    <div class="card-body">
                        <p><strong>Nama:</strong> {{ $item->ketua_rt }}</p>
                    </div>
                </div>
                <div class="card-actions">
                    <a href="{{ route('admin.rt-rw.edit', ['type' => 'rt', 'id' => $item->id]) }}" class="btn-aksi edit">Edit</a>
                    <form action="{{ route('admin.rt-rw.destroy', ['type' => 'rt', 'id' => $item->id]) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-aksi hapus" onclick="return confirm('Yakin ingin menghapus data RT {{ $item->nomor_rt }}?')">Hapus</button>
                    </form>
                </div>
            </div>
        @empty
            <div class="data-card-item empty-card">
                <p>Belum ada data RT yang ditambahkan.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection