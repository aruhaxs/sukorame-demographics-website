@extends('layouts.admin')

@section('title', 'Tambah Data Komoditas')

@section('content')

<style>
    /* Menggunakan kembali style form yang sudah ada */
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-top: 0; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334e6f; background-color: #0b1a2e; color: #f0f4f8; box-sizing: border-box; font-size: 1rem; }
    .btn-submit { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px 12px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
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
                    <option value="">-- Pilih Kategori --</option>
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
                <input type="text" name="periode" id="periode" class="form-control" value="{{ old('periode') }}" placeholder="Contoh: Q4 2024">
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
                <input type="text" name="harga" id="harga" class="form-control" value="{{ old('harga') }}" placeholder="Contoh: 6.500.000/ton">
            </div>
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection
