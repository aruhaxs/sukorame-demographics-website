@extends('layouts.admin')

@section('title', 'Tambah Data Bangunan')

@section('admin_content')

<style>
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
    .form-group { margin-bottom: 1.5rem; }
    .form-group label {
        display: block;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }
    .form-control, .form-select, textarea.form-control {
        width: 100%;
        padding: 12px;
        border-radius: 8px;
        border: 1px solid #334e6f;
        background-color: #0b1a2e;
        color: #f0f4f8;
        box-sizing: border-box;
        font-size: 1rem;
        font-family: 'Poppins', sans-serif;
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
        width: 100%;
    }
    .grid-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }
</style>

<div class="form-card">
    <h2>Tambah Data Bangunan Baru</h2>

    {{-- ✅ Form Tambah Data Bangunan --}}
    <form action="{{ route('admin.bangunan.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="grid-2">
            <div class="form-group">
                <label for="nama_bangunan">Nama Bangunan *</label>
                <input type="text" name="nama_bangunan" id="nama_bangunan" class="form-control"
                    value="{{ old('nama_bangunan') }}" required>
            </div>
            <div class="form-group">
                <label for="kategori">Kategori *</label>
                <select name="kategori" id="kategori" class="form-select" required>
                    <option value="">-- Pilih Kategori --</option>
                    <option value="Pendidikan" {{ old('kategori') == 'Pendidikan' ? 'selected' : '' }}>Pendidikan</option>
                    <option value="Kesehatan" {{ old('kategori') == 'Kesehatan' ? 'selected' : '' }}>Kesehatan</option>
                    <option value="Tempat Ibadah" {{ old('kategori') == 'Tempat Ibadah' ? 'selected' : '' }}>Tempat Ibadah</option>
                    <option value="UMKM" {{ old('kategori') == 'UMKM' ? 'selected' : '' }}>UMKM</option>
                    <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                </select>
            </div>
        </div>

        {{-- ✅ Dropdown RW dan RT --}}
        <div class="grid-2">
            <div class="form-group">
                <label for="rw_id">Nomor RW *</label>
                <select name="rw_id" id="rw_id" class="form-select" required>
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}" {{ old('rw_id') == $rw->id ? 'selected' : '' }}>
                            RW {{ $rw->nomor_rw }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="rt_id">Nomor RT *</label>
                <select name="rt_id" id="rt_id" class="form-select" required>
                    <option value="">-- Pilih RT --</option>
                    @foreach($rts as $rt)
                        <option value="{{ $rt->id }}" {{ old('rt_id') == $rt->id ? 'selected' : '' }}>
                            RT {{ $rt->nomor_rt }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="grid-2">
            <div class="form-group">
                <label for="latitude">Latitude *</label>
                <input type="text" name="latitude" id="latitude" class="form-control"
                    value="{{ old('latitude') }}" placeholder="Contoh: -7.812345" required>
            </div>
            <div class="form-group">
                <label for="longitude">Longitude *</label>
                <input type="text" name="longitude" id="longitude" class="form-control"
                    value="{{ old('longitude') }}" placeholder="Contoh: 112.012345" required>
            </div>
        </div>

        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea name="deskripsi" id="deskripsi" class="form-control" rows="4">{{ old('deskripsi') }}</textarea>
        </div>

        <div class="form-group">
            <label for="foto">Foto Lokasi</label>
            <input type="file" name="foto" id="foto" class="form-control">
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA BANGUNAN</button>
    </form>
</div>
@endsection
