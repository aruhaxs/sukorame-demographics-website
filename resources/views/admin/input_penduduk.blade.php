@extends('layouts.admin')

@section('title', 'Tambah Data Penduduk')

@section('content')

<style>
    /* Styling Form Umum untuk Konsistensi */
    .form-card {
        background-color: var(--color-bg-card);
        padding: 2.5rem;
        border-radius: 12px;
        max-width: 800px;
        margin: 2rem auto;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4);
    }
    .form-card h2 {
        color: var(--color-primary-light);
        text-align: center;
        margin-top: 0;
        margin-bottom: 2rem;
        font-size: 2rem;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .form-control, .form-select {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #334e6f;
        background-color: #0b1a2e;
        color: #f0f4f8;
        box-sizing: border-box;
        font-size: 1rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--color-primary-light);
        outline: none;
        box-shadow: 0 0 0 3px rgba(75, 192, 192, 0.3);
    }
    .btn-submit {
        background-color: var(--color-primary-light);
        color: var(--color-primary-dark);
        padding: 12px 25px;
        border: none;
        border-radius: 8px;
        font-weight: 700;
        font-size: 1rem;
        cursor: pointer;
        transition: background-color 0.3s;
        width: 100%;
    }
    .btn-submit:hover { background-color: #3aa6a6; }
    .alert-error {
        color: #ffdddd;
        background-color: #dc3545;
        padding: 8px 12px;
        border-radius: 4px;
        margin-top: 5px;
        font-size: 0.85rem;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
</style>

<div class="form-card">
    <h2>Tambah Data Penduduk Baru</h2>

    <form action="{{ route('admin.penduduk.store') }}" method="POST">
        @csrf

        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
            @error('nama_lengkap') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="nik">NIK (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-control" value="{{ old('nik') }}" required maxlength="16" minlength="16">
                @error('nik') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nomor_kk">Nomor KK (16 Digit)</label>
                <input type="text" name="nomor_kk" id="nomor_kk" class="form-control" value="{{ old('nomor_kk') }}">
                @error('nomor_kk') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control" value="{{ old('tanggal_lahir') }}" required>
                @error('tanggal_lahir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="jenis_kelamin">Jenis Kelamin</label>
                <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                    <option value="">-- Pilih --</option>
                    <option value="Laki-laki" {{ old('jenis_kelamin') == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                    <option value="Perempuan" {{ old('jenis_kelamin') == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                </select>
                @error('jenis_kelamin') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="rt">RT</label>
                <input type="text" name="rt" id="rt" class="form-control" value="{{ old('rt') }}" required maxlength="3">
                @error('rt') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="rw">RW</label>
                <input type="text" name="rw" id="rw" class="form-control" value="{{ old('rw') }}" required maxlength="3">
                @error('rw') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="form-group">
            <label for="pekerjaan">Pekerjaan</label>
            <input type="text" name="pekerjaan" id="pekerjaan" class="form-control" value="{{ old('pekerjaan') }}">
            @error('pekerjaan') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
            <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control" value="{{ old('pendidikan_terakhir') }}">
            @error('pendidikan_terakhir') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <div class="form-group">
            <label for="agama">Agama</label>
            <select name="agama" id="agama" class="form-select">
                <option value="">-- Pilih Agama --</option>
                <option value="Islam" {{ old('agama') == 'Islam' ? 'selected' : '' }}>Islam</option>
                <option value="Kristen" {{ old('agama') == 'Kristen' ? 'selected' : '' }}>Kristen</option>
                <option value="Katolik" {{ old('agama') == 'Katolik' ? 'selected' : '' }}>Katolik</option>
                <option value="Hindu" {{ old('agama') == 'Hindu' ? 'selected' : '' }}>Hindu</option>
                <option value="Buddha" {{ old('agama') == 'Buddha' ? 'selected' : '' }}>Buddha</option>
                <option value="Konghucu" {{ old('agama') == 'Konghucu' ? 'selected' : '' }}>Konghucu</option>
            </select>
            @error('agama') <div class="alert-error">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection
