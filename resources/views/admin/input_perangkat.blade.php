@extends('layouts.admin')

@section('title', 'Tambah Data RT/RW')

@section('content')
<style>
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 700px; margin: 2rem auto; box-shadow: 0 8px 20px rgba(0, 0, 0, 0.4); }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-bottom: 2rem; font-size: 2rem; }
    .form-group { margin-bottom: 1.5rem; }
    .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; }
    .form-control, .form-select { width: 100%; padding: 12px; border-radius: 8px; border: 1px solid #334e6f; background-color: #0b1a2e; color: #f0f4f8; box-sizing: border-box; font-size: 1rem; }
    .btn-submit { background-color: var(--color-primary-light); color: var(--color-primary-dark); padding: 12px 25px; border: none; border-radius: 8px; font-weight: 700; font-size: 1rem; cursor: pointer; width: 100%; margin-top: 1rem; }
    .alert-error { color: #ffdddd; background-color: #dc3545; padding: 8px; border-radius: 4px; margin-top: 5px; font-size: 0.85rem; }
</style>

<div class="form-card">
    <h2>Tambah Data Baru</h2>
    <form action="{{ route('admin.rt-rw.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="type">Pilih Tipe Data</label>
            <select name="type" id="type" class="form-select" required>
                <option value="">-- Pilih Tipe --</option>
                <option value="rw">Tambah RW</option>
                <option value="rt">Tambah RT</option>
            </select>
        </div>

        <div id="rw-form" style="display: none;">
            <div class="form-group">
                <label for="nomor_rw">Nomor RW</label>
                <input type="number" name="nomor_rw" class="form-control" placeholder="Contoh: 1">
                @error('nomor_rw') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="ketua_rw">Nama Ketua RW</label>
                <input type="text" name="ketua_rw" class="form-control" placeholder="Masukkan nama lengkap">
                @error('ketua_rw') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div id="rt-form" style="display: none;">
            <div class="form-group">
                <label for="rw_id">Pilih RW Induk</label>
                <select name="rw_id" class="form-select">
                    <option value="">-- Pilih RW --</option>
                    @foreach($rws as $rw)
                        <option value="{{ $rw->id }}">RW {{ $rw->nomor_rw }}</option>
                    @endforeach
                </select>
                @error('rw_id') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="nomor_rt">Nomor RT</label>
                <input type="number" name="nomor_rt" class="form-control" placeholder="Contoh: 1">
                @error('nomor_rt') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
            <div class="form-group">
                <label for="ketua_rt">Nama Ketua RT</label>
                <input type="text" name="ketua_rt" class="form-control" placeholder="Masukkan nama lengkap">
                @error('ketua_rt') <div class="alert-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <button type="submit" class="btn-submit">SIMPAN DATA</button>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.getElementById('type').addEventListener('change', function () {
        const rwForm = document.getElementById('rw-form');
        const rtForm = document.getElementById('rt-form');
        if (this.value === 'rw') {
            rwForm.style.display = 'block';
            rtForm.style.display = 'none';
        } else if (this.value === 'rt') {
            rwForm.style.display = 'none';
            rtForm.style.display = 'block';
        } else {
            rwForm.style.display = 'none';
            rtForm.style.display = 'none';
        }
    });
</script>
@endpush
