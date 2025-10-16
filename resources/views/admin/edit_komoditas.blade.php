@extends('layouts.admin')

@section('title', 'Data Komoditas')

@section('content')

<style>
    /* PINE GREEN THEME */
    :root {
        --color-primary: #0a6847; /* Deep Pine Green */
        --color-primary-light: #7aba78; /* Light Accent Green */
        --color-bg-dark: #0d1b2a;  /* Very Dark Blue-Gray */
        --color-bg-card: #1b263b;  /* Dark Card Background */
        --color-text-light: #f0f8ff; /* Alice Blue (off-white) */
        --color-text-subtle: #a0aec0; /* Lighter gray for labels */
        --color-border: #4a5568;
        --color-danger: #e53e3e;
    }

    /* === GENERAL LAYOUT & CARD STYLES === */
    .header-bar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; border-bottom: 1px solid var(--color-border); padding-bottom: 1rem; }
    .header-actions { display: flex; gap: 1rem; align-items: center; }
    .search-input { background-color: var(--color-bg-card); border: 1px solid var(--color-border); color: var(--color-text-light); padding: 10px 15px; border-radius: 8px; width: 250px; font-size: 0.9rem; transition: all 0.2s ease; }
    .search-input:focus { border-color: var(--color-primary-light); box-shadow: 0 0 0 3px rgba(122, 186, 120, 0.3); }
    .btn-tambah-data { background-color: var(--color-primary); color: var(--color-text-light); padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; white-space: nowrap; transition: background-color 0.2s ease; }
    .btn-tambah-data:hover { background-color: #0d8259; }

    .stat-cards-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2.5rem; }
    .stat-card-small { background-color: var(--color-bg-card); padding: 1.5rem; border-radius: 12px; text-align: center; border-left: 5px solid var(--color-primary); }
    .stat-card-small p { margin: 0 0 0.5rem 0; color: var(--color-text-subtle); text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    .stat-card-small .value { font-size: 2.25rem; font-weight: 700; color: var(--color-primary-light); }

    .card-list-wrapper { display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem; }
    .data-card-item {
        background-color: var(--color-bg-card);
        border-radius: 12px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        position: relative; /* Penting untuk positioning kategori dan aksi */
        overflow: hidden; /* Memastikan tidak ada yang keluar dari card */
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .data-card-item:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
    
    .card-header { margin-bottom: 1rem; display: flex; align-items: center; }
    .card-header h3 { margin: 0; font-size: 1.3rem; color: var(--color-text-light); }
    
    /* Perubahan pada kategori tag */
    .category-tag {
        position: absolute; /* Posisikan secara absolut */
        top: 0; /* Di pojok kanan atas */
        right: 0;
        background-color: var(--color-primary);
        color: var(--color-text-light);
        padding: 8px 15px; /* Lebih besar */
        border-bottom-left-radius: 12px; /* Melengkung di kiri bawah */
        font-size: 0.9rem; /* Lebih besar */
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    .card-body { flex-grow: 1; margin-top: 1rem; } /* Menambahkan margin top karena kategori di atas */
    .card-body p { margin: 0 0 0.75rem 0; font-size: 0.95rem; color: var(--color-text-light); display: flex; }
    .card-body p strong { color: var(--color-text-subtle); width: 90px; display: inline-block; flex-shrink: 0; }
    
    /* Perubahan pada card actions */
    .card-actions {
        margin-top: 1.5rem;
        text-align: right;
        border-top: 1px solid var(--color-border); /* Garis pemisah */
        padding-top: 1rem;
        display: flex;
        justify-content: flex-end; /* Pindahkan ke kanan */
        gap: 0.75rem; /* Jarak antar tombol */
    }
    .btn-icon {
        background: #2d3748;
        border: none;
        cursor: pointer;
        padding: 8px 12px; /* Sesuaikan padding agar terlihat seperti tombol */
        font-size: 0.9rem; /* Ukuran font ikon/teks */
        color: #a0aec0;
        border-radius: 6px; /* Bentuk sedikit lebih persegi */
        text-decoration: none;
        display: inline-flex; /* Untuk menengahkan ikon/teks */
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .btn-icon:hover { color: var(--color-text-light); background-color: var(--color-primary); }
    .btn-icon-delete:hover { background-color: var(--color-danger); }
    /* Gaya ikon dari font awesome atau sejenisnya */
    .fas, .far { margin-right: 5px; } /* Jika Anda menggunakan Font Awesome */


    /* === FORM STYLES === */
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-top: 0; margin-bottom: 2.5rem; font-size: 1.8rem; font-weight: 600; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.6rem; font-weight: 600; color: var(--color-text-subtle); font-size: 0.9rem; }
    .form-control, .form-select { width: 100%; padding: 12px 15px; border-radius: 8px; border: 1px solid var(--color-border); background-color: var(--color-bg-dark); color: var(--color-text-light); box-sizing: border-box; font-size: 1rem; transition: all 0.2s ease; }
    .form-control:focus, .form-select:focus { border-color: var(--color-primary-light); box-shadow: 0 0 0 3px rgba(122, 186, 120, 0.3); outline: none; }
    .form-group small { display: block; margin-top: 0.5rem; font-size: 0.8rem; color: var(--color-text-subtle); }
    
    .btn-submit { background-color: var(--color-primary-light); color: #0d1b2a; padding: 14px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; transition: background-color 0.2s ease; }
    .btn-submit:hover { background-color: #96c997; }
    .alert-error { color: #fcc; background-color: rgba(229, 62, 62, 0.5); padding: 8px 12px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

</style>

<div class="header-bar">
    <h1 class="admin-title">Data Komoditas</h1>
    <div class="header-actions">
        <input type="text" class="search-input" placeholder="Cari komoditas...">
        <a href="{{ route('admin.komoditas.create') }}" class="btn-tambah-data">Tambah Komoditas</a>
    </div>
</div>

{{-- Statistik Card --}}
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

{{-- Daftar Komoditas --}}
<div class="card-list-wrapper">
    @forelse($komoditas as $k)
    <div class="data-card-item">
        <div class="category-tag">{{ $k->kategori }}</div> {{-- Kategori di pojok kanan atas --}}
        
        <div class="card-header">
            <h3>{{ $k->nama_komoditas }}</h3>
        </div>
        <div class="card-body">
            <p><strong>Produksi:</strong> {{ $k->produksi ?? '-' }}</p>
            <p><strong>Periode:</strong> {{ $k->periode ?? '-' }}</p>
            <p><strong>Produsen:</strong> {{ $k->produsen ?? '-' }}</p>
            <p><strong>Lokasi:</strong> {{ $k->lokasi ?? '-' }}</p>
            <p><strong>Harga:</strong>
                @if(is_numeric($k->harga)) {{-- Menggunakan is_numeric untuk keamanan --}}
                    Rp {{ number_format($k->harga, 0, ',', '.') }}
                @else
                    -
                @endif
            </p>
        </div>
        <div class="card-actions">
            <a href="{{ route('admin.komoditas.edit', $k) }}" class="btn-icon" title="Edit">
                {{-- Menggunakan ikon umum (misal: dari Font Awesome jika terpasang, atau HTML entity) --}}
                <i class="fas fa-edit"></i> Edit {{-- Ganti dengan <span class="material-icons">edit</span> jika pakai Material Icons --}}
            </a>
            <form action="{{ route('admin.komoditas.destroy', $k) }}" method="POST" style="display:inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-icon btn-icon-delete" title="Hapus" onclick="return confirm('Yakin ingin menghapus data ini?')">
                    <i class="fas fa-trash-alt"></i> Hapus {{-- Ganti dengan <span class="material-icons">delete</span> jika pakai Material Icons --}}
                </button>
            </form>
        </div>
    </div>
    @empty
    <div class="data-card-item" style="text-align: center; grid-column: 1 / -1; padding-top: 3rem;">
        <p style="color: var(--color-text-subtle);">Belum ada data komoditas.</p>
    </div>
    @endforelse
</div>
@endsection