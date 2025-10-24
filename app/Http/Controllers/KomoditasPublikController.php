<?php

namespace App\Http\Controllers;

use App\Models\Komoditas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class KomoditasPublikController extends Controller
{
    /**
     * Menampilkan halaman komoditas publik.
     */
    public function index(): View
    {
        // Ambil semua komoditas, urutkan berdasarkan kategori lalu nama
        $komoditas = Komoditas::orderBy('kategori')->orderBy('nama_komoditas')->get();

        // Kelompokkan berdasarkan kategori
        $komoditasGrouped = $komoditas->groupBy('kategori');

        // Ambil daftar kategori unik untuk filter
        $kategoriList = $komoditas->pluck('kategori')->unique()->sort();

        // Kirim data ke view
        return view('komoditas_publik', compact('komoditasGrouped', 'kategoriList'));
    }
}
