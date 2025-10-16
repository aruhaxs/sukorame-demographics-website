@extends('layouts.admin')

@section('title', 'Tambah Data Komoditas')

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

<div class="form-card">
    <h2>Tambah Data Komoditas Baru</h2>

    <form action="{{ route('admin.komoditas.store') }}" method="POST">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label for="nama_komoditas">Nama Komoditas *</label>
                <input type="text" name="nama_komoditas" id="nama_komoditas" class="form-control" value="{{ old('nama_komoditas') }}" required>
                @error('nama_komoditas') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="kategori">Kategori *</label>
                <select name="kategori" id="kategori" class="form-select" required>
                    <option value="" disabled selected>-- Pilih Kategori --</option>
                    <option value="Pertanian" {{ old('kategori') == 'Pertanian' ? 'selected' : '' }}>Pertanian</option>
                    <option value="Perkebunan" {{ old('kategori') == 'Perkebunan' ? 'selected' : '' }}>Perkebunan</option>
                    <option value="Peternakan" {{ old('kategori') == 'Peternakan' ? 'selected' : '' }}>Peternakan</option>
                    <option value="Perikanan" {{ old('kategori') == 'Perikanan' ? 'selected' : '' }}>Perikanan</option>
                </select>
                @error('kategori') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="produksi">Produksi</label>
                <input type="text" name="produksi" id="produksi" class="form-control" value="{{ old('produksi') }}" placeholder="Contoh: 250 ton">
            </div>
            <div class="form-group">
                <label for="periode">Periode</label>
                <input type="text" name="periode" id="periode" class="form-control" value="{{ old('periode') }}" placeholder="Contoh: Q4 2025">
            </div>
        </div>

        <div class="form-group">
            <label for="produsen">Produsen</label>
            <input type="text" name="produsen" id="produsen" class="form-control" value="{{ old('produsen') }}" placeholder="Contoh: Kelompok Tani Makmur">
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="lokasi">Lokasi</label>
                <input type="text" name="lokasi" id="lokasi" class="form-control" value="{{ old('lokasi') }}" placeholder="Contoh: RW 01">
            </div>
            <div class="form-group">
                <label for="harga">Harga</label>
                <input type="number" name="harga" id="harga" class="form-control" value="{{ old('harga') }}" placeholder="Contoh: 6500000" min="0">
                <small>Masukkan angka saja tanpa titik atau "Rp". Contoh: 10000</small>
            </div>
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection