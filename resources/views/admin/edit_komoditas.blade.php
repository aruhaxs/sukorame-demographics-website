@extends('layouts.admin')

@section('title', 'Edit Data Komoditas')

@push('styles')
<style>
    /* Menggunakan style CSS yang sama dari halaman index Anda */
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
@endpush

@section('content')

<div class="form-card">
    <h2>Edit Data Komoditas</h2>

    @if ($errors->any())
        <div class="alert alert-danger" style="background-color: var(--color-danger); color: white; padding: 1rem; border-radius: 8px; margin-bottom: 1.5rem;">
            <strong>Data tidak valid:</strong>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- =========================================== --}}
    {{-- PERBAIKAN 4: Ganti $komoditas -> $komodita --}}
    {{-- =========================================== --}}
    <form action="{{ route('admin.komoditas.update', ['komodita' => $komodita->id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nama_komoditas">Nama Komoditas</label>
            <input type="text" name="nama_komoditas" id="nama_komoditas" class="form-control"
                   value="{{ old('nama_komoditas', $komodita->nama_komoditas) }}" required>
            @error('nama_komoditas') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="kategori">Kategori</label>
            <input type="text" name="kategori" id="kategori" class="form-control"
                   value="{{ old('kategori', $komodita->kategori) }}" required>
            @error('kategori') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="produksi">Produksi</label>
                <input type="text" name="produksi" id="produksi" class="form-control"
                       value="{{ old('produksi', $komodita->produksi) }}">
                @error('produksi') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="periode">Periode</label>
                <input type="text" name="periode" id="periode" class="form-control"
                       value="{{ old('periode', $komodita->periode) }}">
                @error('periode') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="produsen">Produsen</label>
            <input type="text" name="produsen" id="produsen" class="form-control"
                   value="{{ old('produsen', $komodita->produsen) }}">
            @error('produsen') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="lokasi">Lokasi</label>
            <input type="text" name="lokasi" id="lokasi" class="form-control"
                   value="{{ old('lokasi', $komodita->lokasi) }}">
            @error('lokasi') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="text" name="harga" id="harga" class="form-control"
                   value="{{ old('harga', $komodita->harga) }}">
            <small>Masukkan angka saja, contoh: 15000</small>
            @error('harga') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn-submit">UPDATE DATA</button>
    </form>
</div>
@endsection