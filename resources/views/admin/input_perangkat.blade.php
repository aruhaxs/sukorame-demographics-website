@extends('layouts.admin')

@section('title', 'Tambah Data Perangkat')

@section('content')

<style>
    /* Menggunakan kembali styling form dari input_penduduk */
    .form-section { background-color: #122841; color: #f0f4f8; padding: 4rem 5%; min-height: 80vh; }
    .form-card { background-color: #1c3d64; padding: 2.5rem; border-radius: 10px; max-width: 600px; margin: 0 auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-section h2 { color: var(--color-primary-light); text-align: center; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #334e6f;
        background-color: #0b1a2e;
        color: #f0f4f8;
        box-sizing: border-box;
    }
    .btn-submit {
        background-color: var(--color-primary-light);
        color: #0b1a2e;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }
    .btn-submit:hover { background-color: #3aa6a6; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
</style>

<section class="form-section">
    <div class="form-card">
        <h2>TAMBAH DATA PERANGKAT / RT/RW</h2>

        <form action="{{ route('admin.perangkat.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="nama">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" class="form-control" value="{{ old('nama') }}" required>
                @error('nama') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="jabatan">Jabatan (Contoh: Ketua RT 01/RW 02)</label>
                <input type="text" name="jabatan" id="jabatan" class="form-control" value="{{ old('jabatan') }}" required>
                @error('jabatan') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="nomor_telepon">Nomor Telepon</label>
                <input type="text" name="nomor_telepon" id="nomor_telepon" class="form-control" value="{{ old('nomor_telepon') }}" required maxlength="15">
                @error('nomor_telepon') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn-submit">SIMPAN DATA PERANGKAT</button>
        </form>
    </div>
</section>
@endsection
