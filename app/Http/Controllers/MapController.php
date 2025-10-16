<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bangunan; // Pastikan Model Bangunan sudah dibuat

class MapController extends Controller
{
    // Menampilkan halaman peta
    public function index()
    {
        return view('peta_wilayah');
    }

    // Mengirim data lokasi sebagai JSON
    public function getLocationsApi()
    {
        // Ambil semua data dari tabel bangunans
        $locations = Bangunan::all();
        return response()->json($locations);
    }
}
