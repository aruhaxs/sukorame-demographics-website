@extends('layouts.app')

@section('title', 'Edit Data Penduduk')

@section('content')

<style>
    /* Styling Form Umum untuk Konsistensi */
    .form-section { background-color: #122841; color: #f0f4f8; padding: 4rem 5%; min-height: 80vh; }
    .form-card { background-color: #1c3d64; padding: 2.5rem; border-radius: 10px; max-width: 800px; margin: 0 auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-section h2 { color: #ffffff; text-align: center; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #334e6f;
        background-color: #0b1a2e;
        color: #f0f4f8;
        box-sizing: border-box;
    }
    .form-control:focus, .form-select:focus { border-color: #4BC0C0; outline: none; box-shadow: 0 0 0 2px rgba(75, 192, 192, 0.5); }
    .btn-submit {
        background-color: #4BC0C0; /* Warna edit */
        color: #0b1a2e;
        padding: 12px 25px;
        border: none;
        border-radius: 5px;
        font-weight: 700;
        cursor: pointer;
        transition: background-color 0.3s;
    }
    .btn-submit:hover { background-color: #4BC0C0; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }
</style>

<section class="form-section">
    <div class="form-card">
        <h2>EDIT DATA PENDUDUK</h2>

        {{-- Perhatikan penggunaan method PUT dan Route Model Binding --}}
        <form action="{{ route('admin.penduduk.update', $penduduk) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control"
                    value="{{ old('nama_lengkap', $penduduk->nama_lengkap) }}" required>
                @error('nama_lengkap') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="nik">NIK (16 Digit)</label>
                <input type="text" name="nik" id="nik" class="form-control"
                    value="{{ old('nik', $penduduk->nik) }}" required maxlength="16" minlength="16">
                @error('nik') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="tanggal_lahir">Tanggal Lahir</label>
                    <input type="date" name="tanggal_lahir" id="tanggal_lahir" class="form-control"
                        value="{{ old('tanggal_lahir', $penduduk->tanggal_lahir?->format('Y-m-d')) }}" required>
                    @error('tanggal_lahir') <div class="alert-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="jenis_kelamin">Jenis Kelamin</label>
                    <select name="jenis_kelamin" id="jenis_kelamin" class="form-select" required>
                        <option value="">-- Pilih Jenis Kelamin --</option>
                        @php $jk = old('jenis_kelamin', $penduduk->jenis_kelamin); @endphp
                        <option value="Laki-laki" {{ $jk == 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                        <option value="Perempuan" {{ $jk == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                    </select>
                    @error('jenis_kelamin') <div class="alert-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="grid-2">
                <div class="form-group">
                    <label for="rt">RT</label>
                    <input type="text" name="rt" id="rt" class="form-control"
                        value="{{ old('rt', $penduduk->rt) }}" required maxlength="3">
                    @error('rt') <div class="alert-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label for="rw">RW</label>
                    <input type="text" name="rw" id="rw" class="form-control"
                        value="{{ old('rw', $penduduk->rw) }}" required maxlength="3">
                    @error('rw') <div class="alert-error">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="form-group">
                <label for="pekerjaan">Pekerjaan</label>
                <input type="text" name="pekerjaan" id="pekerjaan" class="form-control"
                    value="{{ old('pekerjaan', $penduduk->pekerjaan) }}">
                @error('pekerjaan') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="pendidikan_terakhir">Pendidikan Terakhir</label>
                <input type="text" name="pendidikan_terakhir" id="pendidikan_terakhir" class="form-control"
                    value="{{ old('pendidikan_terakhir', $penduduk->pendidikan_terakhir) }}">
                @error('pendidikan_terakhir') <div class="alert-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label for="agama">Agama</label>
                <select name="agama" id="agama" class="form-control">
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

            <button type="submit" class="btn-submit">PERBARUI DATA PENDUDUK</button>
        </form>
    </div>
</section>
@endsection
