<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View; // <-- Pastikan ini ada

class MapController extends Controller
{
    /**
     * Menampilkan halaman peta publik.
     * Ini akan memuat file view: 'resources/views/peta_publik.blade.php'
     */
    public function index(): View
    {
        return view('peta_publik');
    }

    /**
     * API lama Anda untuk locations.
     * Biarkan saja jika masih ada, tidak akan mengganggu.
     */
    public function getLocationsApi()
    {
        // ... (kode lama Anda biarkan di sini)
    }
}
