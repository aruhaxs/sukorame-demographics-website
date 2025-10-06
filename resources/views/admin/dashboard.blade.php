@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<style>
    .summary-card-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 2rem; }
    .summary-card { background-color: #1c3d64; padding: 20px; border-radius: 10px; text-align: center; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.3); }
    .summary-card .value { font-size: 2.5rem; font-weight: 700; color: #4BC0C0; }
</style>

{{-- AREA NOTIFIKASI --}}
@if(session('success'))
    <div class="alert-success">{{ session('success') }}</div>
@endif

{{-- KARTU RINGKASAN DATA --}}
<h1>SELAMAT DATANG ADMIN</h1>
<div class="summary-card-grid">
    <div class="summary-card">
        <p>Total Penduduk</p>
        <p class="value">{{ $data['total_penduduk'] }}</p>
    </div>
    <div class="summary-card">
        <p>Total Perangkat (RT/RW)</p>
        <p class="value">{{ $data['total_perangkat'] }}</p>
    </div>
    <div class="summary-card">
        <p>Total Komoditas</p>
        <p class="value">2</p> {{-- Dummy --}}
    </div>
    <div class="summary-card">
        <p>Total Bangunan</p>
        <p class="value">2</p> {{-- Dummy --}}
    </div>
</div>
@endsection
