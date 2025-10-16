@extends('layouts.admin')

@section('title', 'Data Bangunan')

@section('content')

<style>
    /* Style tambahan khusus untuk halaman ini */
    .map-placeholder {
        background-color: #1c3d64;
        height: 400px;
        margin-bottom: 2rem;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        color: #aaa;
        font-style: italic;
    }
</style>

<div class="data-table-section">
    <div class="data-table-header">
        <h2>DATA BANGUNAN & PETA WILAYAH</h2>
        <a href="{{ route('admin.bangunan.create') }}" class="btn-tambah-data">Tambah Data Bangunan</a>
    </div>

    {{-- Placeholder untuk Peta Interaktif --}}
    <div class="map-placeholder">
        [ Area Peta Interaktif Akan Ditampilkan di Sini ]
    </div>

    <div class="data-table-wrapper">
        <table>
            <thead>
                <tr>
                    <th>NAMA BANGUNAN</th>
                    <th>JENIS</th>
                    <th>KOORDINAT</th>
                    <th>AKSI</th>
                </tr>
            </thead>
            <tbody>
                {{-- Data ini masih dummy dari Controller --}}
                @foreach($bangunans as $b)
                <tr>
                    <td>{{ $b->nama }}</td>
                    <td>{{ $b->jenis }}</td>
                    <td>{{ $b->koordinat }}</td>
                    <td>
                        <a href="#" class="btn-aksi edit">Edit</a>
                        <a href="#" class="btn-aksi hapus">Hapus</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
