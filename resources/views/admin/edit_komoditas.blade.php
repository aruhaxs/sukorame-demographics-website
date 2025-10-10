@extends('layouts.admin')

@section('title', 'Edit Data Komoditas')

@section('content')
<style>
    /* Menggunakan kembali style form yang sudah ada */
    .form-card { background-color: var(--color-bg-card); padding: 2.5rem; border-radius: 12px; max-width: 800px; margin: 2rem auto; }
    .form-card h2 { color: var(--color-primary-light); text-align: center; margin-bottom: 2rem; }
</style>
<div class="form-card">
    <h2>Edit Data Komoditas</h2>
    <form action="{{ route('admin.komoditas.update', $komoditas) }}" method="POST">
        @csrf
        @method('PUT')
        {{-- Salin semua field dari input_komoditas.blade.php dan tambahkan value dari $komoditas --}}
        {{-- Contoh: --}}
        <div class="form-group">
            <label for="nama_komoditas">Nama Komoditas *</label>
            <input type="text" name="nama_komoditas" class="form-control" value="{{ old('nama_komoditas', $komoditas->nama_komoditas) }}" required>
        </div>
        <button type="submit" class="btn-submit">SIMPAN PERUBAHAN</button>
    </form>
</div>
@endsection
